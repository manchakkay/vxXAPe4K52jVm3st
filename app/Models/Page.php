<?php

namespace App\Models;

use App\Models\_Templates\InstanceModel;
use Illuminate\Support\Facades\Storage;

class Page extends InstanceModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'slug',
        'is_visible',
        'is_processed',
        'is_structured',
        'content_title',
        'content_thumbnail',
        'content_description',
        'content_json',
        'content_template',
        'content_body',
        'content_data',
        'meta_title',
        'meta_thumbnail',
        'meta_description',
        'published_at',
        'created_at',
        'updated_at',
        'sort',
        'parent_id',
        'content_imported',
        'is_imported',
        'render_hash',
        'old_slug',
    ];

    public static function boot()
    {
        parent::boot();

        // При мягком удалении
        Page::deleted(function ($page_instance) {
            $page_instance->update([
                'published_at' => null,
            ]);
            $page_instance->files()->delete();
        });

        // При полном удалении
        Page::forceDeleted(function ($page_instance) {
            $page_instance->files()->forceDelete();
        });

        Page::restored(function ($page_instance) {
            $page_instance->files()->restore();
        });
    }

    public function children()
    {
        return $this->hasMany(Page::class, "parent_id", "id")->with('children');
    }

    public function thumbnail()
    {
        return $this->hasOne(File::class, 'thumbnail_id', 'id');

    }
    public function parent()
    {
        return $this->belongsTo(Page::class, "parent_id", "id")->with('parent');
    }

    public function ascendings()
    {
        $ascendings = collect();

        $temp = $this;
        $slug = $this->slug;

        while ($temp->parent) {
            $ascendings->push($temp->parent);
            $slug = $temp->parent->slug . '/' . $slug;

            if ($temp->parent) {
                $temp = $temp->parent;
            }
        }

        return ['slug' => $slug, 'parents' => $ascendings];
    }

    public function files()
    {
        return $this->hasMany(File::class, 'news_id', 'id');
    }

}
