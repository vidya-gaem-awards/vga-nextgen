<?php

namespace App;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;

class UlidFileNamer extends DefaultFileNamer
{
    public function originalFileName(string $fileName): string
    {
        return Str::ulid();
    }
}
