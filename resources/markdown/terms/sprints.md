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
