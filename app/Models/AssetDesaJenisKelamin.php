<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDesaJenisKelamin extends Model
{
    use HasFactory;
    
    protected $table = 'assetdesa_jenis_kelamin';
    
    protected $fillable = [
        'assetdesa_id',
        'nama'
    ];
    
    public function assetDesa()
    {
        return $this->belongsTo(AssetDesa::class, 'assetdesa_id');
    }
}