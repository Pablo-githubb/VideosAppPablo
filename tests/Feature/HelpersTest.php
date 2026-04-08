<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        require_once base_path('app/helpers/helpers.php');
    }

    /**
     * Verifica que l'usuari per defecte es crea correctament amb el seu equip.
     */
    public function test_default_user_is_created_correctly()
    {
        // Arrange
        Config::set('default_users.user.name', 'Pablo Masó');
        Config::set('default_users.user.email', 'pablomaso@iesebre.com');
        Config::set('default_users.user.password', 'ablso330');

        // Act
        $user = createDefaultUser();

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Pablo Masó', $user->name);
        $this->assertEquals('pablomaso@iesebre.com', $user->email);
        //Encriptem la contrasenya amb un HASH per comparar-la amb la contrasenya de la base de dades
        $this->assertTrue(Hash::check('ablso330', $user->password));

        //Associem l'usuari amb un team
        $this->assertNotNull($user->current_team_id);
        $this->assertEquals(1, $user->ownedTeams()->count());

        $team = $user->ownedTeams()->first();
        $this->assertEquals("Pablo's Team", $team->name);
        $this->assertTrue($team->is_personal);
    }

    /**
     * Verifica que el professor per defecte es crea correctament amb el seu equip.
     */
    public function test_default_professor_is_created_correctly()
    {
        // Arrange
        Config::set('default_users.professor.name', 'Jan Almudeve');
        Config::set('default_users.professor.email', 'janalmudeve@iesebre.com');
        Config::set('default_users.professor.password', 'admin123');

        // Act
        $user = createDefaultProfessor();

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Jan Almudeve', $user->name);
        $this->assertEquals('janalmudeve@iesebre.com', $user->email);
        //Encriptem la contrasenya amb un HASH per comparar-la amb la contrasenya de la base de dades
        $this->assertTrue(Hash::check('admin123', $user->password));

        //Associem al professor en un team
        $this->assertNotNull($user->current_team_id);
        $this->assertEquals(1, $user->ownedTeams()->count());

        $team = $user->ownedTeams()->first();
        $this->assertEquals("Jan's Team", $team->name);
        $this->assertTrue($team->is_personal);
    }
}
