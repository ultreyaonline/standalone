<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class CandidateAdded
{
    use Dispatchable, SerializesModels;

    public $who;
    public $by;

    public function __construct($who, User $by)
    {
        $this->who = $who;
        $this->by   = $by;
    }
}
