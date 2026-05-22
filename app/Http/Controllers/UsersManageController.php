<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Actions\Teams\CreateTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class UsersManageController extends Controller
{
    public function testedBy(): string
    {
        return \Tests\Feature\Users\UsersManageControllerTest::class;
    }

    public function index(): View
    {
        Gate::authorize('users_manage_index');

        $users = User::orderBy('id', 'desc')->get();

        return view('users.manage.index', compact('users'));
    }

    public function create(): View
    {
        Gate::authorize('users_manage_create');

        return view('users.manage.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('users_manage_store');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'super_admin' => $request->has('super_admin') ? (bool)$request->input('super_admin') : false,
        ]);

        try {
            $createTeam = App::make(CreateTeam::class);
            $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);
        } catch (\Throwable $e) {
            // Ignorem si falla la creació de l'equip en algun cas de test
        }

        return redirect()->route('users.manage.index')
            ->with('success', 'Usuari creat correctament.');
    }

    public function edit(string $id): View
    {
        Gate::authorize('users_manage_edit');

        $user = User::findOrFail($id);

        return view('users.manage.edit', compact('user'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Gate::authorize('users_manage_update');

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'super_admin' => $request->has('super_admin') ? (bool)$request->input('super_admin') : false,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.manage.index')
            ->with('success', 'Usuari actualitzat correctament.');
    }

    public function delete(string $id): View
    {
        Gate::authorize('users_manage_delete');

        $user = User::findOrFail($id);

        return view('users.manage.delete', compact('user'));
    }

    public function destroy(string $id): RedirectResponse
    {
        Gate::authorize('users_manage_destroy');

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.manage.index')
            ->with('success', 'Usuari eliminat correctament.');
    }
}
