<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class CredentialController extends Controller
{
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

        // Generate Blockcerts JSON
        $this->generateBlockcertsJson($credential);

        // Generate QR Code
        $this->generateQrCode($credential);

        return redirect()->route('credentials.index')
            ->with('success', 'Credential uploaded successfully!');
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

        $credential->update(['status' => 'Revoked']);

        return redirect()->route('credentials.index')
            ->with('success', 'Credential revoked successfully!');
    }

    private function generateBlockcertsJson(Credential $credential)
    {
        $blockcertsData = [
            '@context' => [
                'https://www.w3.org/2018/credentials/v1',
                'https://www.blockcerts.org/schema/3.0-alpha/context.json'
            ],
            'type' => ['VerifiableCredential', 'BlockcertsCredential'],
            'issuer' => [
                'id' => url('/institution/' . $credential->institution->slug),
                'name' => $credential->institution->name,
                'email' => $credential->institution->contact_email,
            ],
            'issuanceDate' => $credential->issued_on->toISOString(),
            'credentialSubject' => [
                'id' => 'urn:uuid:' . Str::uuid(),
                'name' => $credential->full_name,
                'credentialType' => $credential->credential_type,
                'issuedBy' => $credential->issued_by,
            ],
            'proof' => [
                'type' => 'MerkleProof2019',
                'created' => now()->toISOString(),
                'verificationMethod' => url('/verification/' . $credential->verification_code),
                'proofValue' => $credential->hash,
            ],
            'verification' => [
                'type' => 'hosted',
                'url' => url('/verify/' . $credential->verification_code),
            ],
        ];

        $jsonContent = json_encode($blockcertsData, JSON_PRETTY_PRINT);
        $jsonFilename = 'blockcerts_' . $credential->verification_code . '.json';
        $jsonPath = 'credentials/json/' . $jsonFilename;
        
        Storage::disk('public')->put($jsonPath, $jsonContent);
        
        $credential->update(['json_path' => $jsonPath]);
    }

    private function generateQrCode(Credential $credential)
    {
        $verificationUrl = url('/verify/' . $credential->verification_code);
        
        $qrCode = new QrCode($verificationUrl);
        $qrCode->setSize(300);
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $qrFilename = 'qr_' . $credential->verification_code . '.png';
        $qrPath = 'credentials/qr/' . $qrFilename;
        
        Storage::disk('public')->put($qrPath, $result->getString());
        
        return $qrPath;
    }
}
