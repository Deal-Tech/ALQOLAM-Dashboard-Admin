<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'judul',
        'id_kategori_kegiatan',
        'imgfuture',
        'lampiran',
        'konten',
        'created_at',
        'status',
    ];

    public function kategoriKegiatan()
    {
        return $this->belongsTo(KategoriKegiatan::class, 'id_kategori_kegiatan');
    }

    public function setLampiranAttribute($value)
    {
        $this->attributes['lampiran'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getLampiranAttribute($value)
    {
        return json_decode($value, true);
    }
}