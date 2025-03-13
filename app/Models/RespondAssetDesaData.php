<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondAssetDesaData extends Model
{
    use HasFactory;

    protected $table = 'respond_assetdesa_data';
    
    protected $fillable = [
        'assetdesa_data_id',
        'respond_assetdesa_detail_id',
        'jawaban'
    ];
    
    /**
     * Get the asset data that this response is for
     */
    public function assetDesaData()
    {
        return $this->belongsTo(AssetDesaData::class, 'assetdesa_data_id');
    }
    
    /**
     * Get the detail that this response belongs to
     */
    public function respondDetail()
    {
        return $this->belongsTo(RespondAssetDesaDetail::class, 'respond_assetdesa_detail_id');
    }
}