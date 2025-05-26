<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::latest()->paginate(10);
        return view('institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('institutions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'logo_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Institution::create([
            'name' => $request->name,
            'contact_email' => $request->contact_email,
            'slug' => Str::slug($request->name),
            'logo_url' => $request->logo_url,
            'description' => $request->description,
        ]);

        return redirect()->route('institutions.index')
            ->with('success', 'Institution created successfully!');
    }

    public function show(Institution $institution)
    {
        $credentialsCount = $institution->credentials()->count();
        $verifiedCount = $institution->credentials()->where('status', 'verified')->count();
        $revokedCount = $institution->credentials()->where('status', 'revoked')->count();

        return view('institutions.show', compact('institution', 'credentialsCount', 'verifiedCount', 'revokedCount'));
    }

    public function edit(Institution $institution)
    {
        return view('institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'logo_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $institution->update([
            'name' => $request->name,
            'contact_email' => $request->contact_email,
            'slug' => Str::slug($request->name),
            'logo_url' => $request->logo_url,
            'description' => $request->description,
        ]);

        return redirect()->route('institutions.index')
            ->with('success', 'Institution updated successfully!');
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();
        
        return redirect()->route('institutions.index')
            ->with('success', 'Institution deleted successfully!');
    }
}
