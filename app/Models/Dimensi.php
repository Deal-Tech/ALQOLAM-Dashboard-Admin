<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimensi extends Model
{
    use HasFactory;

    protected $table = 'dimensi'; 

    protected $fillable = ['nama', 'deskripsi', 'bobot'];

    public function variabels()
    {
        return $this->hasMany(Variabel::class);
    }
}