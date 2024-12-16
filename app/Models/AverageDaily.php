<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AverageDaily extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function dataHistory(): BelongsTo
    {
        return $this->belongsTo(CollectData::class, "collect_id", "id");
    }
}
