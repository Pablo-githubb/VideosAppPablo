<?php

use App\Models\User;
use App\Actions\Teams\CreateTeam;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

/**
 * Crea un usuari per defecte i el seu equip personal associat.
 */
if (!function_exists('createDefaultUser')) {
    function createDefaultUser(): User
    {
        $user = User::create([
            'name' => config('default_users.user.name', 'Pablo Masó'),
            'email' => config('default_users.user.email', 'pablomaso@iesebre.com'),
            'password' => Hash::make((string) config('default_users.user.password', 'ablso330')),
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
    function createDefaultProfessor(): User
    {
        $user = User::create([
            'name' => config('default_users.professor.name', 'Jan Almudeve'),
            'email' => config('default_users.professor.email', 'janalmudeve@iesebre.com'),
            'password' => Hash::make((string) config('default_users.professor.password', 'admin123')),
        ]);

        $createTeam = App::make(CreateTeam::class);
        $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);

        return $user->fresh();
    }
}

/**
 * Crea un vídeo per defecte.
 */
if (!function_exists('createDefaultVideo')) {
    function createDefaultVideo(): \App\Models\Video
    {
        return \App\Models\Video::create([
            'title' => 'Vídeo per defecte',
            'description' => 'Aquest és un vídeo creat per defecte des del seeder/helper.',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
            'previous' => null,
            'next' => null,
            'series_id' => null,
        ]);
    }
}

/**
 * Crea 3 vídeos per defecte.
 */
if (!function_exists('createDefaultVideos')) {
    /**
     * @return array<int, \App\Models\Video>
     */
    function createDefaultVideos(): array
    {
        $video1 = \App\Models\Video::create([
            'title' => 'Introducció a Laravel',
            'description' => 'Un vídeo introductori sobre el framework Laravel.',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'published_at' => now(),
            'previous' => null,
            'next' => null,
            'series_id' => null,
        ]);

        $video2 = \App\Models\Video::create([
            'title' => 'Eloquent ORM',
            'description' => 'Aprèn a utilitzar Eloquent ORM per gestionar la base de dades.',
            'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
            'published_at' => now(),
            'previous' => null,
            'next' => null,
            'series_id' => null,
        ]);

        $video3 = \App\Models\Video::create([
            'title' => 'Testing amb PHPUnit',
            'description' => 'Com escriure tests automatitzats amb PHPUnit a Laravel.',
            'url' => 'https://www.youtube.com/watch?v=JGwWNGJdvx8',
            'published_at' => now(),
            'previous' => null,
            'next' => null,
            'series_id' => null,
        ]);

        return [$video1, $video2, $video3];
    }
}

/**
 * Crea un usuari amb permisos de gestió de vídeos.
 */
if (!function_exists('createVideoManagerUser')) {
    function createVideoManagerUser(): User
    {
        $user = User::create([
            'name' => 'Video Manager',
            'email' => 'videomanager@iesebre.com',
            'password' => Hash::make('manager123'),
        ]);

        $createTeam = App::make(CreateTeam::class);
        $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);

        $user = $user->fresh();
        $user->givePermission('videos_manage_index');
        $user->givePermission('videos_manage_create');
        $user->givePermission('videos_manage_store');
        $user->givePermission('videos_manage_edit');
        $user->givePermission('videos_manage_update');
        $user->givePermission('videos_manage_delete');
        $user->givePermission('videos_manage_destroy');

        return $user;
    }
}

/**
 * Crea un superadmin amb tots els permisos.
 */
if (!function_exists('createSuperAdminUser')) {
    function createSuperAdminUser(): User
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@iesebre.com',
            'password' => Hash::make('superadmin123'),
            'super_admin' => true,
        ]);

        $createTeam = App::make(CreateTeam::class);
        $createTeam->handle($user, explode(' ', $user->name, 2)[0] . "'s Team", isPersonal: true);

        return $user->fresh();
    }
}

/**
 * Defineix les Gates dels permisos de vídeos.
 */
if (!function_exists('defineVideoPermissionGates')) {
    function defineVideoPermissionGates(): void
    {
        $permissions = [
            'videos_manage_index',
            'videos_manage_create',
            'videos_manage_store',
            'videos_manage_edit',
            'videos_manage_update',
            'videos_manage_delete',
            'videos_manage_destroy',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                if ($user->super_admin) {
                    return true;
                }
                return $user->hasPermission($permission);
            });
        }
    }
}
