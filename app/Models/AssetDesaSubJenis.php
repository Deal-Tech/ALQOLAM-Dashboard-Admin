<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDesaSubJenis extends Model
{
    use HasFactory;

    protected $table = 'assetdesa_sub_jenis';
    
    protected $fillable = [
        'assetdesa_id',
        'subjenis' 
    ];
    
    /**
     * Relasi ke model AssetDesa (parent)
     */
    public function assetDesa()
    {
        return $this->belongsTo(AssetDesa::class, 'assetdesa_id');
    }
    
    /**
     * Relasi ke model AssetDesaSubJenisData (child)
     */
    public function data()
    {
        return $this->hasMany(AssetDesaSubJenisData::class, 'subjenis_id');
    }
}