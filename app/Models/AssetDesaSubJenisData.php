<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDesaSubJenisData extends Model
{
    use HasFactory;
    
    protected $table = 'assetdesa_sub_jenis_data';
    
    protected $fillable = [
        'subjenis_id',
        'nama'  
    ];
    
    /**
     * Relasi ke model AssetDesaSubJenis (parent)
     */
    public function subJenis()
    {
        return $this->belongsTo(AssetDesaSubJenis::class, 'subjenis_id');
    }
}