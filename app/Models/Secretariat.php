<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Secretariat extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'secretariat';
    protected static $logAttributes = ['*'];

    protected $table = 'secretariat';
}
