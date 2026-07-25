<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form (account info, avatar, password).
     */
    public function edit(): View
    {
        return view('front.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the authenticated user's profile (name, email, phone, avatar).
     *
     * Avatar uploads are stored on the `public` disk; a previously stored file
     * is removed before the new one is persisted to avoid orphaned files.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $previous = $user->avatar_url;

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_url'] = $path;

            // Delete the old avatar file only if it lived on our own disk —
            // never strip an externally hosted URL the user might have set.
            if ($previous && ! str_starts_with($previous, 'http')) {
                Storage::disk('public')->delete($previous);
            }
        }

        $user->update($data);

        return redirect()
            ->route('front.profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Update the authenticated user's password after confirming the current one.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('front.profile.edit')
            ->with('status', 'password-updated');
    }
}
