<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDesaData extends Model
{
    use HasFactory;
    
    protected $table = 'assetdesa_data';
    
    protected $fillable = [
        'assetdesa_id',
        'value',
        'is_multiple_answer'
    ];
    
    protected $casts = [
        'is_multiple_answer' => 'boolean',
    ];
    
    /**
     * Menentukan nama foreign key yang benar untuk relasi
     */
    public function assetDesa()
    {
        return $this->belongsTo(AssetDesa::class, 'assetdesa_id');
    }
}