<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Institution;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        return view('verification.index');
    }

    public function verify(Request $request, $code = null)
    {
        $verificationCode = $code ?? $request->input('verification_code');
        
        if (!$verificationCode) {
            return redirect()->route('credential.verification.index')
                ->with('error', 'Please enter a verification code.');
        }

        $credential = Credential::where('verification_code', $verificationCode)
            ->with(['institution'])
            ->first();

        if (!$credential) {
            return view('verification.result', [
                'found' => false,
                'message' => 'Credential not found. Please check the verification code and try again.'
            ]);
        }

        return view('verification.result', [
            'found' => true,
            'credential' => $credential,
            'qrPath' => $this->getQrCodePath($credential),
        ]);
    }

    public function institutionVerify(Institution $institution)
    {
        return view('verification.institution', compact('institution'));
    }

    public function institutionVerifySubmit(Request $request, Institution $institution)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $credential = Credential::where('verification_code', $request->verification_code)
            ->where('institution_id', $institution->id)
            ->with(['institution'])
            ->first();

        if (!$credential) {
            return view('verification.result', [
                'found' => false,
                'message' => 'Credential not found for this institution. Please check the verification code and try again.',
                'institution' => $institution,
            ]);
        }

        return view('verification.result', [
            'found' => true,
            'credential' => $credential,
            'institution' => $institution,
            'qrPath' => $this->getQrCodePath($credential),
        ]);
    }

    public function downloadJson(Credential $credential)
    {
        if (!$credential->json_path || !file_exists(storage_path('app/public/' . $credential->json_path))) {
            abort(404, 'Blockcerts JSON file not found.');
        }

        return response()->download(
            storage_path('app/public/' . $credential->json_path),
            'credential_' . $credential->verification_code . '.json'
        );
    }

    private function getQrCodePath(Credential $credential)
    {
        $qrPath = 'credentials/qr/qr_' . $credential->verification_code . '.png';
        
        if (file_exists(storage_path('app/public/' . $qrPath))) {
            return asset('storage/' . $qrPath);
        }
        
        return null;
    }
}
