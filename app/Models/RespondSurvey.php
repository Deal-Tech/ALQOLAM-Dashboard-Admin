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
        'jawaban',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
}