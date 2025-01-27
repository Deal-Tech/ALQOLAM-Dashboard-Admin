<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class SpesialUser extends Model
{
    use HasFactory;
    protected $table = 'spesialuser';

    protected $fillable = [
        'email',
        'password',
        'no_handphone',
        'nama_lengkap',
    ];

    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
