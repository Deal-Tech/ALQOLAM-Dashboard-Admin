<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DosenPendamping extends Model
{
    use HasFactory;

    protected $table = 'dosenpendamping';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_handphone',
    ];

    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
