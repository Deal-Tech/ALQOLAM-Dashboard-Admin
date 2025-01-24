<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubVariabel extends Model
{
    use HasFactory;

    protected $table = 'subvariabel';

    protected $fillable = ['variabel_id', 'nama', 'deskripsi'];

    public function variabel()
    {
        return $this->belongsTo(Variabel::class);
    }
}