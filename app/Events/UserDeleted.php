<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserDeleted
{
    use Dispatchable, SerializesModels;

    public $who;
    public $by;

    public function __construct(string $who, string $by)
    {
        $this->who  = $who;
        $this->by   = $by;
    }
}
