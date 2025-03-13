<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondAssetDesaDetail extends Model
{
    use HasFactory;
    
    protected $table = 'respond_assetdesa_detail';
    
    protected $fillable = [
        'respond_assetdesa_id',
        'assetdesa_id',
        'assetdesa_data_id',
        'assetdesa_sub_jenis_id',
        'assetdesa_sub_jenis_data_id',
        'nilai'
    ];
    
    /**
     * Get the respond asset desa record this detail belongs to
     */
    public function respondAssetDesa()
    {
        return $this->belongsTo(RespondAssetDesa::class, 'respond_assetdesa_id');
    }
    
    /**
     * Get the related asset desa
     */
    public function assetDesa()
    {
        return $this->belongsTo(AssetDesa::class, 'assetdesa_id');
    }
    
    /**
     * Get the related asset desa data (if applicable)
     */
    public function assetDesaData()
    {
        return $this->belongsTo(AssetDesaData::class, 'assetdesa_data_id');
    }
    
    /**
     * Get the related asset desa sub jenis (if applicable)
     */
    public function assetDesaSubJenis()
    {
        return $this->belongsTo(AssetDesaSubJenis::class, 'assetdesa_sub_jenis_id');
    }
    
    /**
     * Get the related asset desa sub jenis data (if applicable)
     */
    public function assetDesaSubJenisData()
    {
        return $this->belongsTo(AssetDesaSubJenisData::class, 'assetdesa_sub_jenis_data_id');
    }
    
    /**
     * Get the related response data
     */
    public function data()
    {
        return $this->hasMany(RespondAssetDesaData::class, 'respond_assetdesa_detail_id');
    }
    
    /**
     * Get the related response sub jenis data
     */
    public function subJenisData()
    {
        return $this->hasMany(RespondAssetDesaSubJenisData::class, 'respond_assetdesa_detail_id');
    }
    
    /**
     * Get the related response jenis kelamin
     */
    public function jenisKelamin()
    {
        return $this->hasMany(RespondAssetDesaJenisKelamin::class, 'respond_assetdesa_detail_id');
    }
}