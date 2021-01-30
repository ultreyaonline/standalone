<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TeamFeePayments extends Model
{
    use LogsActivity;
    use HasFactory;

    protected static $logName = 'teamfees';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $table = 'team_fees';

    protected $casts = [
        'complete' => 'boolean',
        'date'     => 'date',
    ];

    protected $fillable = ['weekendID', 'memberID', 'total_paid', 'date_paid', 'complete', 'comments', 'recorded_by'];

    /**
     * $table->unsignedInteger('weekendID')->references('id')->on('weekends')->index('feesforweekendid');
     * $table->unsignedInteger('memberID')->references('id')->on('users')->index('feesformember');
     * $table->double('total_paid', 4, 2)->nullable();
     * $table->date('date_paid')->nullable();
     * $table->integer('complete')->nullable()->index('bycomplete');
     * $table->string('recorded_by', 60);
     * $table->text('comments')->nullable();
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'memberID')->withDefault();
    }

    public function weekend()
    {
        return $this->belongsTo(Weekend::class, 'weekendID')->withDefault();
    }
}
