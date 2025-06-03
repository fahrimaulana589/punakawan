<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('success', 'profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        try {
            $user->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the company profile data form.
     */
    public function editProfile(Request $request): View
    {
        $perusahaan = Profile::firstOrNew();
        return view('profile.profile', [
            'profile' => $perusahaan,
        ]);
    }

    /**
     * Update the company profile data.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'alamat'         => 'required|string|max:500',
            'handphone'      => 'required|string|max:20',
            'logo'           => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email_server'   => 'nullable|string|max:255',
            'email_port'     => 'nullable|integer',
            'email_password' => 'nullable|string|max:255',
            'email_username' => 'nullable|email|max:255',
        ]);

        $data = $request->only([
            'nama',
            'alamat',
            'handphone',
            'email_server',
            'email_port',
            'email_password',
            'email_username',
        ]);

        $profile = Profile::createOrFirst([
            'id' => 1, // Assuming the profile is unique and has an ID of 1
        ], $data);

        // Handle logo upload if exists
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }

        $profile->fill($data);
        $profile->save();

        return back()->with('success', 'Company profile updated successfully.');
    }
}
