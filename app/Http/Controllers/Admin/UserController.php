<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Validate input and sanitize data
        $validated = $request->validate([
            'query' => 'nullable|string', // Define explicit field and type
        ]);

        $searchQuery = $validated['query'] ?? null; // Extract search term if present

        // Build secure query using Eloquent query builder methods
        $query = User::query();

        if (!empty($searchQuery)) {
            $query->where('name', 'like', "%{$searchQuery}%"); // Placeholder, use parameterized query
        }

        $users = $query->latest()->paginate(10);

        return response()->json($users);
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

    public function changeRole(User $user, Request $request)
    {
        $user->update([
            'role'=>$request->role,
        ]);

        return response()->json(['success'=>true]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }

    public function bulkDelete()
    {
        User::whereIn('id',request('ids'))->delete();
        return response()->json(['message'=>'Users deleted successfully!']);
    }

}
