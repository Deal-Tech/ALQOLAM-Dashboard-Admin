<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDesa extends Model
{
    protected $table = 'assetdesa';
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array'
    ];

    public function getJenisAttribute()
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);
        return $data['jenis'] ?? '-';
    }

    public function getDataArrayAttribute()
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);
        $items = $data['data'] ?? [];
        return array_map(fn($item) => ['item' => $item], $items);
    }

    public function getTipeArrayAttribute()
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);
        $items = $data['tipe'] ?? [];
        return array_map(fn($item) => ['item' => $item], $items);
    }

    public function getFormattedDataAttribute()
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);
        return isset($data['data']) ? implode(', ', $data['data']) : '-';
    }

    public function getFormattedTipeAttribute()
    {
        $data = is_array($this->data) ? $this->data : json_decode($this->data, true);
        return isset($data['tipe']) ? implode(', ', $data['tipe']) : '-';
    }
}