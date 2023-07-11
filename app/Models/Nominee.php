<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Nominee extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('voting-image')->singleFile();
    }

    public function votingImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', '=', 'voting-image');
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
