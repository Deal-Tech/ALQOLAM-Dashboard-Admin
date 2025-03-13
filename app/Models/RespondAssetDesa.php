<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondAssetDesa extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'respond_assetdesa';

    // Define fillable fields
    protected $fillable = [
        'desa_id',
        'user_id',
        'is_completed',
        'is_published'
    ];

    // Define casts for boolean fields
    protected $casts = [
        'is_completed' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Get the desa that this response belongs to
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    /**
     * Get the user that created this response
     */
    public function user()
    {
        return $this->belongsTo(Mahasiswa::class, 'user_id');
    }

    /**
     * Get the response details for this asset desa response
     */
    public function details()
    {
        return $this->hasMany(RespondAssetDesaDetail::class, 'respond_assetdesa_id');
    }
}