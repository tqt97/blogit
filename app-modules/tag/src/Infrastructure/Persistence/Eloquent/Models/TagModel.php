<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Tag\Database\Factories\TagModelFactory;

class TagModel extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory()
    {
        return TagModelFactory::new();
    }
}
