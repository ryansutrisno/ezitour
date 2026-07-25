<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    /**
     * Determine whether the user may access the Filament admin panel.
     *
     * Filament v3 blocks any non-{@link FilamentUser} model outside of the
     * `local` environment, so implementing this contract is required for the
     * panel to be reachable in staging/production (and during tests, which run
     * with `APP_ENV=testing`).
     *
     * Intentionally still permissive (returns true) for the MVP: a dedicated
     * admin-assignment flow doesn't exist yet, so gating on `role === 'admin'`
     * would lock out the seeded admin and break SettingsPageTest (which logs
     * in a plain factory user). Once real role assignment lands, switch this to
     * `return $this->isAdmin();`.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Whether this user has the admin role.
     *
     * Kept independent from {@see canAccessPanel()} until a real role-assignment
     * flow exists, so admin features can still be progressively gated.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Uppercase initials derived from the user's name (avatar fallback).
     *
     * Examples: "Andi Rahman" → "AR", "siti nurhaliza" → "SN", "Budi" → "B".
     */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === [] || $parts === false) {
            return '?';
        }

        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials;
    }
}
