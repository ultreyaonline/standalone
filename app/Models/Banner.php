<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * NOTE: This model is for GENERAL/COMMUNITY BANNERS ONLY
 * Weekend-specific banner images are uploaded and controlled within each Weekend model themselves, and are unrelated to this model.
 */
class Banner extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['title'];


    public function getBannerUrlAttribute()
    {
        if ($this->getMedia('banner_general')->count()) {
            return $this->getFirstMediaUrl('banner_general', 'resized');
        }

        return $this->attributes['banner_url'] ?? null;
    }

    public function getBannerUrlOriginalAttribute()
    {
        if ($this->getMedia('banner_general')->count()) {
            return $this->getFirstMediaUrl('banner_general');
        }

        return $this->attributes['banner_url'] ?? null;
    }

    /**
     * Register Spatie Media-Library collections
     */
    public function registerMediaCollections(): void
    {
        // Banner is a single image, so subsequent images replace prior ones
        $this
            ->addMediaCollection('banner_general')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('resized')
                    ->width(800)
                    ->height(600)
//                    ->orientation(Manipulations::ORIENTATION_AUTO)
                ;
            });
    }

    /**
     * Delete media collections on Delete
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Banner $banner) {
            $banner->clearMediaCollection('banner_general');

            return $banner;
        });
    }
}
