<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
        protected $fillable = [
        'model_type',
        'model_id',
        'collection',
        'file_name',
        'file_path',
        'mime_type',
        'size',
    ];
}
