<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondSurvey extends Model
{
    use HasFactory;

    protected $table = 'respondsurvey';

    protected $fillable = [
        'user_id',
        'kabupaten_id',
        'kecamatan_id',
        'desa_id',
        'survey_id',
        'nama_ketua',
        'is_compled',
        'is_published',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'user_id');
    }

    public function respondetail()
    {
        return $this->hasMany(ResponDetail::class, 'respondsurvey_id');
    }
}