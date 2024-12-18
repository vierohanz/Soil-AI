<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AverageDaily extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'avg_temperature',
        'avg_air_humidity',
        'avg_soil_humidity',
        'avg_light',

    ];
    protected $table = 'average_daily';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
}
