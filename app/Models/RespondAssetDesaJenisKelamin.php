<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondAssetDesaJenisKelamin extends Model
{
    use HasFactory;
    
    protected $table = 'respond_assetdesa_jenis_kelamin';
    
    protected $fillable = [
        'assetdesa_data_id',
        'respond_assetdesa_detail_id',
        'jawaban_laki_laki',
        'jawaban_perempuan'
    ];
    
    /**
     * Get the asset data that this response is for
     */
    public function assetDesaJenisKelamin()
    {
        return $this->belongsTo(AssetDesaJenisKelamin::class, 'assetdesa_data_id');
    }
    
    /**
     * Get the detail that this response belongs to
     */
    public function respondDetail()
    {
        return $this->belongsTo(RespondAssetDesaDetail::class, 'respond_assetdesa_detail_id');
    }
}