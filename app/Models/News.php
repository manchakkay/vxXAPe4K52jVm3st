<?php

namespace App\Models;

use App\Models\_Templates\InstanceModel;

class News extends InstanceModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'slug',
        'news_category_id',
        'is_visible',
        'is_processed',
        'content_title',
        'content_description',
        'content_json',
        'content_html',
        'meta_title',
        'meta_thumbnail',
        'meta_description',
        'published_at',
        'created_at',
        'updated_at',
        'event_date',
        'is_imported',
        'content_imported',
        'render_hash',
        'old_slug',
    ];

    public static function boot()
    {
        parent::boot();

        // При мягком удалении
        News::deleted(function ($news_instance) {
            $news_instance->update([
                'published_at' => null,
            ]);
            $news_instance->files()->delete();
        });

        // При полном удалении
        News::forceDeleted(function ($news_instance) {
            $news_instance->files()->forceDelete();
        });

        News::restored(function ($news_instance) {
            $news_instance->files()->restore();
        });
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function favorite()
    {
        return $this->hasOne(NewsFavorite::class);
    }

    public function thumbnail()
    {
        return $this->hasOne(File::class, 'thumbnail_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'news_id', 'id');
    }

    public function get_thumbnail($extension = null)
    {
        if ($this->thumbnail !== null) {
            // Возврат существующей обложки
            return $this->thumbnail->direct_url();
        } else if (count($this->files()->getResults()) != 0 && $this->files()->getResults()[0]->content_type == 'image') {
            // Возврат первой картинки связанной с новостью
            return $this->files()->getResults()[0]->direct_url();
        }

        // Возврат пустышки
        return route('files_direct') . '/blank';
    }

}
