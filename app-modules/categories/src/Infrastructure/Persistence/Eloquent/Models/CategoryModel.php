<?php

namespace Modules\Categories\Infrastructure\Persistence\Eloquents\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Category\Domain\Enums\CategoryStatus;

class CategoryModel extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => CategoryStatus::class,
    ];

    /**
     * Auto generate slug
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ],
        ];
    }

    public function children()
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}