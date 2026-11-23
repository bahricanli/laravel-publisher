<?php

namespace BahriCanli\Publisher\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image_url',
        'status',
        'categories',
        'tags',
        'language',
        'translation_key',
    ];

    protected $casts = [
        'categories' => 'array',
        'tags'       => 'array',
    ];
}
