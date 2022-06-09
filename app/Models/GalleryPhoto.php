<?php

namespace App\Models;

use App\Models\_Templates\InstanceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryPhoto extends InstanceModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'slug',
        'is_visible',
        'content_title',
        'content_description',
        'pulished_at',
    ];

    public static function boot()
    {
        parent::boot();

        // При мягком удалении
        GalleryPhoto::deleted(function ($instance) {
            $instance->update([
                'published_at' => null,
            ]);
            $instance->files()->delete();
        });

        // При полном удалении
        GalleryPhoto::forceDeleted(function ($instance) {
            $instance->files()->forceDelete();
        });

        GalleryPhoto::restored(function ($instance) {
            $instance->files()->restore();
        });
    }

    public function files()
    {
        return $this->hasMany(File::class, 'gallery_photo_id', 'id')->orderBy('content_sort', 'ASC');
    }
}
