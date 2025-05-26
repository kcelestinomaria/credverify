<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\EmployerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->isEmployer()) {
            abort(403, 'Unauthorized');
        }

        $recentVerifications = EmployerVerification::where('employer_user_id', $user->id)
            ->with(['credential.institution'])
            ->latest()
            ->take(10)
            ->get();

        $totalVerifications = EmployerVerification::where('employer_user_id', $user->id)->count();

        return view('employer.dashboard', compact('recentVerifications', 'totalVerifications'));
    }

    public function verify(Request $request, $code = null)
    {
        $user = Auth::user();
        
        if (!$user->isEmployer()) {
            abort(403, 'Unauthorized');
        }

        $verificationCode = $code ?? $request->input('verification_code');
        
        if (!$verificationCode) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please enter a verification code.');
        }

        $credential = Credential::where('verification_code', $verificationCode)
            ->with(['institution'])
            ->first();

        // Log the verification attempt
        if ($credential) {
            EmployerVerification::create([
                'employer_user_id' => $user->id,
                'credential_id' => $credential->id,
                'searched_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        }

        if (!$credential) {
            return view('employer.verify-result', [
                'found' => false,
                'message' => 'Credential not found. Please check the verification code and try again.',
                'verificationCode' => $verificationCode,
            ]);
        }

        return view('employer.verify-result', [
            'found' => true,
            'credential' => $credential,
            'verificationCode' => $verificationCode,
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        
        if (!$user->isEmployer()) {
            abort(403, 'Unauthorized');
        }

        $verifications = EmployerVerification::where('employer_user_id', $user->id)
            ->with(['credential.institution'])
            ->latest()
            ->paginate(20);

        return view('employer.history', compact('verifications'));
    }

    public function bulkVerify()
    {
        $user = Auth::user();
        
        if (!$user->isEmployer()) {
            abort(403, 'Unauthorized');
        }

        return view('employer.bulk-verify');
    }

    public function processBulkVerify(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployer()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'verification_codes' => 'required|string',
        ]);

        $codes = array_filter(array_map('trim', explode("\n", $request->verification_codes)));
        $results = [];

        foreach ($codes as $code) {
            $credential = Credential::where('verification_code', $code)
                ->with(['institution'])
                ->first();

            if ($credential) {
                EmployerVerification::create([
                    'employer_user_id' => $user->id,
                    'credential_id' => $credential->id,
                    'searched_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            }

            $results[] = [
                'code' => $code,
                'found' => $credential ? true : false,
                'credential' => $credential,
            ];
        }

        return view('employer.bulk-verify-results', compact('results'));
    }
}
