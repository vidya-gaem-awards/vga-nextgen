<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nominee extends Model
{
    protected $guarded = ['id'];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
