<?php

use App\Models\User;
use App\Actions\Teams\CreateTeam;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

/**
 * Crea un usuari per defecte i el seu equip personal associat.
 */
if (!function_exists('createDefaultUser')) {
    function createDefaultUser()
    {
        $user = User::create([
            'name' => config('default_users.user.name', 'Pablo Masó'),
            'email' => config('default_users.user.email', 'pablomaso@iesebre.com'),
            'password' => Hash::make(config('default_users.user.password', 'ablso330')),
        ]);

        $createTeam = App::make(CreateTeam::class);
        $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);

        return $user->fresh();
    }
}

/**
 * Crea un professor per defecte i el seu equip personal associat.
 */
if (!function_exists('createDefaultProfessor')) {
    function createDefaultProfessor()
    {
        $user = User::create([
            'name' => config('default_users.professor.name', 'Jan Almudeve'),
            'email' => config('default_users.professor.email', 'janalmudeve@iesebre.com'),
            'password' => Hash::make(config('default_users.professor.password', 'admin123')),
        ]);

        $createTeam = App::make(CreateTeam::class);
        $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);

        return $user->fresh();
    }
}
