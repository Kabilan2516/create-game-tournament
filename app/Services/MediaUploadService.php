<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaUploadService
{
    public static function upload(
        UploadedFile $file,
        $model,
        string $collection = 'default',
        string $folder = 'uploads'
    ): Media {

        // Generate unique name
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs($folder, $fileName, 'public');

        // Save in DB
        return Media::create([
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'collection' => $collection,
            'file_name'  => $fileName,
            'file_path'  => $path,
            'mime_type'  => $file->getClientMimeType(),
            'size'       => $file->getSize(),
        ]);
    }
}
