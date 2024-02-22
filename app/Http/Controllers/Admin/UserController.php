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
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:users,email',
            'password'=>'required|min:8',
        ]);

        return User::create([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>bcrypt($request->password),
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:users,email,' . $user->id,
            'password'=>'sometimes|min:8',
        ]);

        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->passwprd ? bcrypt($request->password) : $user->password,
        ]);
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
