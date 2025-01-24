<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variabel extends Model
{
    use HasFactory;

    protected $table = 'variabel';

    protected $fillable = ['dimensi_id', 'nama', 'deskripsi'];

    public function dimensi()
    {
        return $this->belongsTo(Dimensi::class);
    }

    public function subVariabels()
    {
        return $this->hasMany(SubVariabel::class);
    }
}