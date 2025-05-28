<?php

namespace Invento\Blog\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Tags\HasTags;

class Blog extends Model
{
    use HasFactory, Sluggable, SoftDeletes,HasTags;

    protected $table = 'blogs';
    protected $fillable = [
        'title',
        'short_description',
        'content',
        'status',
        'display_order',
        'is_featured',
        'blog_category_id',
        'category_name',
        'thumbnail',
        'slug',
        'banner',
        'meta_title',
        'meta_description'
    ];
    public const Image = 'blog_thumbnails';
    public const Banner = 'blog_banners';

    public const STATUS = [
        'Published' => 'Published',
        'Draft' => 'Draft'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->created_by == null) $model->created_by = auth()->user()->id;
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->update();
        });
    }

    public function getThumbnailAttribute(): string
    {
        return $this->attributes['thumbnail'] ? url('storage/' . $this->attributes['thumbnail']) : url('storage/blog_thumbnails/default.jpg');
    }

    public function getHasThumbnailAttribute(){
        return $this->attributes['thumbnail'] ? $this->attributes['thumbnail'] : false;
    }

    public function getBannerAttribute(): string
    {
        return $this->attributes['banner'] ? url('storage/' . $this->attributes['banner']) : url('storage/blog_banners/default.jpg');
    }

    public function getHasBannerAttribute(){
        return $this->attributes['banner'] ? $this->attributes['banner'] : false;
    }


    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('status', true);
    }

    /**
     * @param Builder $builder
     * @param $searchItem
     * @return Builder
     */
    public function scopeSearch(Builder $builder, $searchItem): Builder
    {
        return $builder->where('id', "{$searchItem}%")
            ->orWhere('title', 'LIKE', "{$searchItem}%")
            ->orWhere('short_description', 'LIKE', "{$searchItem}%");
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'blog_category_id');
    }
}
