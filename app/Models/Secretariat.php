<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Secretariat extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected $table = 'secretariat';
}
