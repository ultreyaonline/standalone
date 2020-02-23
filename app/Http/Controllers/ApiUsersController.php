<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Resources\Member as MemberResource;
use Illuminate\Http\Request;

class ApiUsersController extends Controller
{
    public function show(User $user): MemberResource
    {
        return new MemberResource($user);
    }
}
