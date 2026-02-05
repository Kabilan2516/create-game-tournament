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
        'url',
        'disk',
        'size',
    ];
    public function getUrlAttribute()
{
    if ($this->disk === 'cloud' && $this->attributes['url']) {
        return $this->attributes['url'];
    }

    return asset('storage/' . $this->file_path);
}

}
