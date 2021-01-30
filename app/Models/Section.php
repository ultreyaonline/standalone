<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Section extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'config-sections';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    /* Table structure
        $table->increments('id');
        $table->string('name');
        $table->integer('sort_order')->nullable();
        $table->boolean('enabled')->default(1);
    */

    protected $casts = [
        'enabled' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('enabledSectionsBySortOrder', function (Builder $query) {
            $query->where('enabled', 1);
            $query->orderBy('sort_order', 'asc');
        });
    }
}
