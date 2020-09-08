<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserAdded
{
    use Dispatchable, SerializesModels;

    public $user;
    public $by;

    public function __construct(User $user, string $by)
    {
        $this->user = $user;
        $this->by   = $by;
    }
}
