<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class UserController extends Controller
{
    public function index()
    {
        $users=User::latest()->get();

        return $users;
    }

    public function store(Request $request)
    {
        return User::create([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>bcrypt($request->password),
        ]);
    }

    public function update(User $user, Request $request)
    {
        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->passwprd ? bcrypt($request->password) : $user->password,
        ]);
        return $user;
    }
}
