<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15);

        return view('users.index', compact('users', 'search'));
    }

    public function show(string $id): View
    {
        $user = User::findOrFail($id);
        $videos = $user->videos()->orderBy('published_at', 'desc')->get();

        return view('users.show', compact('user', 'videos'));
    }
}
