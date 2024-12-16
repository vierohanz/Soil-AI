<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectData extends Model
{
    use HasFactory;
    protected $fillable = [
        'temperature',
        'air_humidity',
        'soil_humidity',
        'light',
    ];
    protected $table = 'collect_data';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function averageDaily(): HasMany
    {
        return $this->HasMany(AverageDaily::class, 'collect_id', 'id');
    }
}
