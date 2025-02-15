<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DosenPendamping extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $table = 'dosenpendamping';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_handphone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@alqolam.ac.id') && $this->hasVerifiedEmail();
        }
 
        return true;
    }

    public function getFilamentName(): string 
    {
        return $this->nama_lengkap ?? $this->email ?? 'Dosen';
    }

    public function getName(): string
    {
        return $this->getFilamentName();
    }
}