<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Show extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }

    public function nominees(): HasManyThrough
    {
        return $this->hasManyThrough(Nominee::class, Award::class);
    }
}
