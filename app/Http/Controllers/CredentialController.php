<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\BlockchainAnchor;
use App\Models\RevocationRegistry;
use App\Services\BlockchainService;
use App\Services\BlockcertsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class CredentialController extends Controller
{
    private $blockchainService;
    private $blockcertsService;

    public function __construct(BlockchainService $blockchainService, BlockcertsService $blockcertsService)
    {
        $this->blockchainService = $blockchainService;
        $this->blockcertsService = $blockcertsService;
    }
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $credentials = Credential::where('institution_id', $user->institution_id)
            ->with(['institution'])
            ->latest()
            ->paginate(10);

        return view('credentials.index', compact('credentials'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('credentials.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'credential_type' => 'required|string|max:255',
            'issued_by' => 'required|string|max:255',
            'issued_on' => 'required|date',
            'credential_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Store the credential file
        $file = $request->file('credential_file');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('credentials', $filename, 'public');

        // Generate hash of the file
        $fileContent = file_get_contents($file->getRealPath());
        $hash = hash('sha256', $fileContent);

        // Generate verification code
        $verificationCode = Credential::generateVerificationCode();

        // Create credential record
        $credential = Credential::create([
            'user_id' => $user->id,
            'institution_id' => $user->institution_id,
            'full_name' => $request->full_name,
            'credential_type' => $request->credential_type,
            'issued_by' => $request->issued_by,
            'issued_on' => $request->issued_on,
            'credential_file_path' => $filePath,
            'hash' => $hash,
            'verification_code' => $verificationCode,
            'status' => 'Verified',
        ]);

        // Generate institution keys if they don't exist
        $this->ensureInstitutionKeys($user->institution_id);

        // Generate Blockcerts credential with digital signature
        $blockcertsResult = $this->blockcertsService->generateBlockcertsCredential($credential);
        
        if (!$blockcertsResult['success']) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to generate Blockcerts credential: ' . $blockcertsResult['error']]);
        }

        // Generate QR Code
        $this->generateQrCode($credential);

        // Check if we should process for blockchain anchoring
        $this->processBlockchainAnchoring($credential);

        return redirect()->route('credentials.index')
            ->with('success', 'Credential uploaded and digitally signed successfully! Blockchain anchoring in progress.');
    }

    public function show(Credential $credential)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() || $credential->institution_id !== $user->institution_id) {
            abort(403, 'Unauthorized');
        }

        return view('credentials.show', compact('credential'));
    }

    public function revoke(Credential $credential)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() || $credential->institution_id !== $user->institution_id) {
            abort(403, 'Unauthorized');
        }

        try {
            // Update credential status
            $credential->update(['status' => 'Revoked']);

            // Add to blockchain revocation registry
            if ($credential->isBlockchainAnchored()) {
                $revocationResult = $this->blockchainService->addToRevocationRegistry(
                    $credential->verification_code,
                    'Revoked by institution'
                );

                // Create revocation registry record
                RevocationRegistry::create([
                    'credential_id' => $credential->id,
                    'revocation_transaction_hash' => $revocationResult['transaction_hash'],
                    'reason' => 'Revoked by institution',
                    'revoked_at' => now(),
                    'revoked_by' => $user->name,
                    'blockchain' => 'ethereum',
                    'network' => config('blockchain.network', 'sepolia'),
                    'status' => 'pending'
                ]);

                return redirect()->route('credentials.index')
                    ->with('success', 'Credential revoked successfully! Blockchain revocation in progress.');
            }

            return redirect()->route('credentials.index')
                ->with('success', 'Credential revoked successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to revoke credential: ' . $e->getMessage()]);
        }
    }

    /**
     * Ensure institution has cryptographic keys
     */
    private function ensureInstitutionKeys($institutionId)
    {
        $privateKeyPath = storage_path("app/keys/institution_{$institutionId}_private.pem");
        $publicKeyPath = storage_path("app/keys/institution_{$institutionId}_public.pem");

        if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
            // Create keys directory if it doesn't exist
            if (!is_dir(storage_path('app/keys'))) {
                mkdir(storage_path('app/keys'), 0755, true);
            }

            $this->blockchainService->generateInstitutionKeys($institutionId);
        }
    }

    /**
     * Process credentials for blockchain anchoring
     */
    private function processBlockchainAnchoring(Credential $credential)
    {
        // Get unanchored credentials for this institution
        $unanchoredCredentials = Credential::where('institution_id', $credential->institution_id)
            ->where('blockchain_anchored', false)
            ->whereNotNull('digital_signature')
            ->get();

        // If we have enough credentials or it's been long enough, process batch
        $batchSize = config('blockchain.batch_size', 10);
        
        if ($unanchoredCredentials->count() >= $batchSize) {
            $this->processBatchForAnchoring($unanchoredCredentials->take($batchSize));
        }
    }

    /**
     * Process a batch of credentials for blockchain anchoring
     */
    private function processBatchForAnchoring($credentials)
    {
        try {
            $result = $this->blockcertsService->processCredentialsForAnchoring($credentials);
            
            if ($result['success']) {
                \Log::info('Batch anchored successfully', [
                    'batch_id' => $result['batch_id'],
                    'transaction_hash' => $result['transaction_hash'],
                    'credentials_count' => $credentials->count()
                ]);
            } else {
                \Log::error('Batch anchoring failed', ['error' => $result['error']]);
            }
        } catch (\Exception $e) {
            \Log::error('Batch anchoring exception', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Add method to manually trigger batch processing
     */
    public function processPendingBatch()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $unanchoredCredentials = Credential::where('institution_id', $user->institution_id)
            ->where('blockchain_anchored', false)
            ->whereNotNull('digital_signature')
            ->get();

        if ($unanchoredCredentials->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No credentials pending blockchain anchoring.');
        }

        $this->processBatchForAnchoring($unanchoredCredentials);

        return redirect()->back()
            ->with('success', 'Batch processing initiated for ' . $unanchoredCredentials->count() . ' credentials.');
    }

    private function generateQrCode(Credential $credential)
    {
        $verificationUrl = url('/verify/' . $credential->verification_code);
        
        $qrCode = new QrCode(
            data: $verificationUrl,
            size: 300,
            margin: 10
        );
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $qrFilename = 'qr_' . $credential->verification_code . '.png';
        $qrPath = 'credentials/qr/' . $qrFilename;
        
        Storage::disk('public')->put($qrPath, $result->getString());
        
        $credential->update(['qr_code_path' => $qrPath]);
        
        return $qrPath;
    }
}
