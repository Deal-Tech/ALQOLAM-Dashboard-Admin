<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponDetail extends Model
{
    use HasFactory;
    protected $table = 'respondetail';
    protected $fillable = [
        'respondsurvey_id',
        'jawaban',
        'survey_id',
    ];

    public function respondsurvey()
    {
        return $this->belongsTo(RespondSurvey::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    
}
