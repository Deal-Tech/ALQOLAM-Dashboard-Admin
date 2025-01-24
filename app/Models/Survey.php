<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'survey';

    protected $fillable = [
        'dimensi_id',
        'variabel_id',
        'desa_id',
        'pertanyaan',
        'opsi_jawaban_1',
        'opsi_jawaban_2',
        'opsi_jawaban_3',
        'opsi_jawaban_4',
        'opsi_jawaban_5',
    ];

    public function dimensi()
    {
        return $this->belongsTo(Dimensi::class);
    }

    public function variabel()
    {
        return $this->belongsTo(Variabel::class);
    }

    public function subvariabel()
    {
        return $this->belongsTo(SubVariabel::class);
    }
}