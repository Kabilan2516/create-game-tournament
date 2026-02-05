<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Aws\S3\S3Client;

class MediaUploadService
{
    public static function upload(
        UploadedFile $file,
        $model,
        string $collection = 'default',
        string $folder = 'uploads'
    ): Media {

        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $disk = config('media.disk', env('MEDIA_DISK', 'local'));

        if ($disk === 'cloud') {
            return self::uploadToCloud($file, $fileName, $model, $collection, $folder);
        }

        // 🔹 LOCAL (EXISTING BEHAVIOR)
        $path = $file->storeAs($folder, $fileName, 'public');

        return Media::create([
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'collection' => $collection,
            'file_name'  => $fileName,
            'file_path'  => $path,
            'mime_type'  => $file->getClientMimeType(),
            'size'       => $file->getSize(),
            'disk'       => 'local',
        ]);
    }

    /* ============================================
       CLOUD (R2 / S3 COMPATIBLE)
    ============================================ */
    protected static function uploadToCloud(
        UploadedFile $file,
        string $fileName,
        $model,
        string $collection,
        string $folder
    ): Media {

        $key = trim($folder, '/') . '/' . $fileName;

        $s3 = new S3Client([
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'version' => 'latest',
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $s3->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => $key,
            'Body'   => fopen($file->getPathname(), 'rb'),
            'ACL'    => 'public-read',
            'ContentType' => $file->getClientMimeType(),
        ]);

        $cdnUrl = rtrim(env('MEDIA_CDN_URL'), '/') . '/' . $key;

        return Media::create([
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'collection' => $collection,
            'file_name'  => $fileName,
            'file_path'  => $key,
            'mime_type'  => $file->getClientMimeType(),
            'size'       => $file->getSize(),
            'disk'       => 'cloud',
            'url'        => $cdnUrl,
        ]);
    }
}
