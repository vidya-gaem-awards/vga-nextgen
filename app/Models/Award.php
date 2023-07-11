<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Award extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('winner-image')->singleFile();
    }

    public function winnerImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', '=', 'winner-image');
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }
}
