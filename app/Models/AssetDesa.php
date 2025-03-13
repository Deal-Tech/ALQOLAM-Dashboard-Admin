<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDesa extends Model
{
    protected $table = 'assetdesa';
    
    protected $fillable = [
        'jenis', 
        'is_data',
        'is_sub_jenis',
        'is_jenis_kelamin'
    ];
    
    protected $casts = [
        'is_data' => 'boolean',
        'is_sub_jenis' => 'boolean',
        'is_jenis_kelamin' => 'boolean',
    ];
    
    public function data()
    {
        return $this->hasMany(AssetDesaData::class, 'assetdesa_id');
    }
    
    public function subJenis()
    {
        return $this->hasMany(AssetDesaSubJenis::class, 'assetdesa_id');
    }
    
    public function jenisKelamin()
    {
        return $this->hasMany(AssetDesaJenisKelamin::class, 'assetdesa_id');
    }
}