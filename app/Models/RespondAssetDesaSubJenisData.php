<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondAssetDesaSubJenisData extends Model
{
    use HasFactory;
    
    protected $table = 'respond_assetdesa_sub_jenis_data';
    
    protected $fillable = [
        'assetdesa_sub_jenis_data_id',
        'respond_assetdesa_detail_id',
        'jawaban'
    ];
    
    /**
     * Get the asset sub jenis data that this response is for
     */
    public function assetDesaSubJenisData()
    {
        return $this->belongsTo(AssetDesaSubJenisData::class, 'assetdesa_sub_jenis_data_id');
    }
    
    /**
     * Get the detail that this response belongs to
     */
    public function respondDetail()
    {
        return $this->belongsTo(RespondAssetDesaDetail::class, 'respond_assetdesa_detail_id');
    }
}