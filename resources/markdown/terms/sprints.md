# Guia del Projecte - VideosAppPablo

Aquest fitxer reflecteix els progressos elaborats durant els dos sprints per a la Videos App.

## De què tracta el projecte?
Videos App és una plataforma orientada al maneig i reproducció de contingut en vídeo de manera estructurada, integrant llistes de reproducció amb navegació (anterior/posterior) i un gestor enfocat en perfils amb rols (usuaris i professors per defecte connectats mitjançant Jetstream/bancs de dades d'equips o Teams).

## Resum dels Sprints

### Sprint 1: Autenticació, Equips i HelpersBase
En aquest sprint ens vam enfocar en la lògica base del maneig d'usuaris. Les característiques han estat:
1. Configuració de l'entorn de testos amb base de dades local ràpida (SQLite en memòria).
2. Establiment d'usuaris base globals a través de `.env`.
3. Creació de les primeres proves TDD (HelpersTest) sota convenció AAA (Arrange, Act, Assert).
4. Creació d'usuaris personalitzats prelligats als seus «Personal Teams» mitjançant accions programades a `helpers.php` (CreateDefaultUser i CreateDefaultProfessor).

### Sprint 2: Core del Model de Vídeos i Code Quality
Durant el segon sprint, hem aixecat la pedra angular de l'aplicatiu, la taula de vídeos i totes les interfícies rellevants:
1. Generació de les dependències de dades (`migration` de videos) de YouTube per allotjament URL, dades públiques de publicació indexada (`published_at`) i relacions recursives entre anteriors i següents (`previous`, `next`).
2. Funcions d'accessoria d'informació sobre la data amb `Carbon`, llançant l'hora en format universal (timestamp Unix) i estilitzat per als humans segons llengua i temps ("13 de gener de 2025" i "fa 2 hores").
3. Integració de components de Front-End amb TailwindCSS sobre les plantilles en construcció pròpies de Livewire `VideosAppLayout` i la primera vista `show` de vídeos i ruta.
4. Extensió directa i consolidació de l'arbre de tests amb noves asseveracions unitàries (Dates) i Feature Tests (Visualització i resposta correcta d'URL's 404 de Videos).
5. Instal·lació en paral·lel de Larastan per garantir robustesa de la capa PHP estàtica.

### Sprint 3: CRUD de Vídeos, Permisos i Navegació
En aquest sprint s'ha implementat el sistema complet de gestió de vídeos amb control d'accés:

1. **Sistema de permisos**: Creació d'un sistema de permisos propi amb la taula `user_permissions` i el camp `super_admin` a la taula `users`. Definició de Gates de Laravel per als permisos de CRUD de vídeos (`videos_manage_index`, `videos_manage_create`, `videos_manage_store`, `videos_manage_edit`, `videos_manage_update`, `videos_manage_delete`, `videos_manage_destroy`).

2. **VideosManageController**: Controlador complet amb les funcions `testedBy`, `index`, `create`, `store`, `show`, `edit`, `update`, `delete` i `destroy`. Cada acció està protegida amb `Gate::authorize()`.

3. **VideosController**: Afegida la funció `index` per mostrar tots els vídeos en una vista pública estil YouTube.

4. **Helpers actualitzats**:
   - `createDefaultVideos()`: Crea 3 vídeos per defecte (Introducció a Laravel, Eloquent ORM, Testing amb PHPUnit).
   - `createVideoManagerUser()`: Crea un usuari amb tots els permisos de gestió de vídeos.
   - `createSuperAdminUser()`: Crea un superadministrador amb accés total.
   - `defineVideoPermissionGates()`: Registra les Gates de permisos.

5. **Vistes CRUD** (`resources/views/videos/manage/`):
   - `index.blade.php`: Taula amb tots els vídeos i accions d'editar/eliminar.
   - `create.blade.php`: Formulari de creació amb atributs `data-qa` per testing.
   - `edit.blade.php`: Formulari d'edició pre-emplenat.
   - `delete.blade.php`: Pàgina de confirmació d'eliminació.

6. **Vista pública** (`resources/views/videos/index.blade.php`): Grid de vídeos estil YouTube amb thumbnails, títols i descripcions. Accessible per tothom (amb o sense login).

7. **Rutes**: Rutes públiques per `/videos` i `/videos/{id}`. Rutes protegides amb middleware `auth` per `/videos/manage/*`.

8. **Layout amb navbar i footer**: Plantilla `videos-app-layout.blade.php` actualitzada amb navbar de navegació (Vídeos, Gestió Vídeos condicional), botons d'autenticació i footer.

9. **Tests**:
   - `VideosTest`: Afegides `user_without_permissions_can_see_default_videos_page`, `user_with_permissions_can_see_default_videos_page`, `not_logged_users_can_see_default_videos_page`.
   - `VideosManageControllerTest`: Tests complets de CRUD amb `loginAsVideoManager`, `loginAsSuperAdmin`, `loginAsRegularUser` i verificació de permisos per a cada acció.

10. **DatabaseSeeder**: Actualitzat per crear superadmin, video manager i 3 vídeos per defecte.

11. **Larastan**: Tots els fitxers creats han estat verificats amb Larastan nivell 5.
