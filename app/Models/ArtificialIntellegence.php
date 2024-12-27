<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtificialIntellegence extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'prob_siram',
        'prob_tidak_siram',
        'average_id'
    ];
    protected $table = 'artificial_intellegence';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function averageDaily()
    {
        return $this->belongsTo(CollectData::class, 'month_id');
    }
}
