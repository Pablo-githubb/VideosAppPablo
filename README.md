# 🎬 VideosAppPablo

Aplicació web per gestionar i visualitzar vídeos, construïda amb **Laravel 13**, **Livewire 4** i **Flux UI**. Inclou sistema d'autenticació complet, gestió d'equips i reproducció de vídeos amb navegació entre ells.

---

## 📋 Taula de continguts

- [Què fa aquesta aplicació?](#-què-fa-aquesta-aplicació)
- [Requisits previs](#-requisits-previs)
- [Instal·lació](#-installació)
- [Ús de l'aplicació](#-ús-de-laplicació)
- [Estructura del projecte](#-estructura-del-projecte)
- [Tests](#-tests)
- [Tecnologies utilitzades](#-tecnologies-utilitzades)

---

## 🎯 Què fa aquesta aplicació?

**VideosAppPablo** és una plataforma de gestió de vídeos amb les funcionalitats següents:

### Gestió de vídeos
- **Visualització de vídeos**: Cada vídeo es mostra amb el seu títol, descripció, data de publicació i un reproductor integrat (iframe).
- **Navegació seqüencial**: Els vídeos poden estar enllaçats entre ells (vídeo anterior / vídeo següent), permetent navegar com en una sèrie.
- **Sèries**: Els vídeos es poden agrupar per sèries mitjançant un `series_id`.
- **Dates amigables**: La data de publicació es presenta en format llegible (ex: *"13 d'abril de 2026"*) i també en format relatiu (ex: *"fa 2 dies"*).

### Autenticació i seguretat
- **Registre i inici de sessió**: Formularis complets per a registrar-se i fer login.
- **Verificació d'email**: Opcional, per assegurar que l'adreça de correu és vàlida.
- **Recuperació de contrasenya**: Permet restablir la contrasenya mitjançant email.
- **Autenticació de dos factors (2FA)**: Seguretat addicional amb codis TOTP i codis de recuperació.

### Gestió d'equips
- **Equips personals**: Cada usuari té automàticament un equip personal creat.
- **Rols i permisos**: Tres nivells de rol dins d'un equip:
  - 🔑 **Owner** (Propietari) — Control total.
  - 🛡️ **Admin** (Administrador) — Pot gestionar l'equip, crear i cancel·lar invitacions.
  - 👤 **Member** (Membre) — Accés bàsic.
- **Invitacions**: Es poden convidar nous membres als equips.
- **Canvi d'equip**: Un usuari pot pertànyer a múltiples equips i canviar entre ells.

### Configuració de l'usuari
- **Perfil**: Modificar nom i email.
- **Aparença**: Canviar entre mode clar/fosc.
- **Seguretat**: Gestionar contrasenya i configurar la 2FA.
- **Eliminar compte**: Opció per eliminar el compte sencer.

---

## ⚙️ Requisits previs

Abans d'instal·lar l'aplicació, assegura't de tenir instal·lat:

| Programari | Versió mínima |
|------------|---------------|
| PHP        | 8.3           |
| Composer   | 2.x           |
| Node.js    | 18.x          |
| npm        | 9.x           |
| SQLite     | 3.x           |

---

## 🚀 Instal·lació

### 1. Clonar el repositori

```bash
git clone https://github.com/Pablo-githubb/VideosAppPablo.git
cd VideosAppPablo
```

### 2. Instal·lació ràpida (recomanat)

Executa la comanda `setup` que fa totes les passes automàticament:

```bash
composer setup
```

Això farà el següent:
1. Instal·lar dependències PHP (`composer install`)
2. Crear el fitxer `.env` si no existeix
3. Generar la clau de l'aplicació
4. Executar les migracions de la base de dades
5. Instal·lar dependències JavaScript (`npm install`)
6. Compilar els assets (`npm run build`)

### 3. Instal·lació manual (pas a pas)

Si prefereixes fer-ho manualment:

```bash
# Instal·lar dependències PHP
composer install

# Copiar fitxer d'entorn
cp .env.example .env

# Generar clau d'aplicació
php artisan key:generate

# Crear la base de dades SQLite
touch database/database.sqlite

# Executar migracions
php artisan migrate

# Instal·lar dependències JavaScript
npm install

# Compilar assets
npm run build
```

### 4. Configurar els usuaris per defecte (opcional)

Al fitxer `.env` pots personalitzar els usuaris que el seeder crearà:

```env
DEFAULT_USER_NAME="Pablo Masó"
DEFAULT_USER_EMAIL="pablomaso@iesebre.com"
DEFAULT_USER_PASSWORD="ablso330"

DEFAULT_PROFESSOR_NAME="Jan Almudeve"
DEFAULT_PROFESSOR_EMAIL="janalmudeve@iesebre.com"
DEFAULT_PROFESSOR_PASSWORD="admin123"
```

### 5. Arrencar el servidor de desenvolupament

```bash
composer run dev
```

Aquesta comanda arrenca simultàniament:
- 🌐 **Servidor Laravel** (`php artisan serve`)
- 📬 **Cua de treballs** (`php artisan queue:listen`)
- 📋 **Logs en temps real** (`php artisan pail`)
- ⚡ **Vite** per a la compilació d'assets en calent (`npm run dev`)

L'aplicació estarà disponible a **http://localhost:8000**.

---

## 📖 Ús de l'aplicació

### Pàgina d'inici

En obrir l'aplicació veuràs la pàgina de benvinguda amb opcions per:
- **Iniciar sessió** si ja tens un compte.
- **Registrar-se** si ets un usuari nou.

### Registrar-se

1. Fes clic a **Register**.
2. Omple el formulari amb el teu nom, email i contrasenya.
3. Verificaràs el teu email (si està habilitat).
4. Seràs redireccionat al **Dashboard**.

### Dashboard

El dashboard és la pàgina principal de l'usuari autenticat. Des d'aquí pots accedir a totes les funcionalitats.

### Veure un vídeo

Accedeix a un vídeo a través de la URL:

```
/videos/{id}
```

A la pàgina del vídeo veuràs:
- 🎥 El reproductor de vídeo integrat.
- 📝 El títol i la descripció.
- 📅 La data de publicació.
- ⬅️ ➡️ Botons de navegació cap al vídeo anterior o següent (si existeixen).

### Configuració del perfil

Accedeix a **Settings** des del menú d'usuari:

| Secció        | Descripció                                       |
|---------------|--------------------------------------------------|
| **Profile**   | Canviar nom i email                              |
| **Appearance**| Canviar el tema (clar/fosc)                      |
| **Security**  | Canviar contrasenya, configurar 2FA              |
| **Teams**     | Gestionar equips, invitacions i membres          |

### Gestió d'equips

1. Ves a **Settings → Teams**.
2. Pots crear un nou equip o editar els equips existents.
3. Per convidar un membre, fes clic a **Invite Member** dins de l'equip.
4. L'usuari convidat rebrà una invitació que pot acceptar.
5. Pots canviar el rol dels membres (Admin o Member).
6. Pots eliminar equips (excepte l'equip personal).
---

## 🏃 Historial de Sprints

Aquest projecte s'ha desenvolupat seguint una metodologia àgil en diferents fases (sprints):

### Sprint 1: Autenticació, Equips i HelpersBase
- Configuració de l'entorn de testos amb base de dades local ràpida (SQLite en memòria).
- Establiment d'usuaris base globals a través del fitxer `.env`.
- Creació de les primeres proves TDD (`HelpersTest`) sota convenció AAA (Arrange, Act, Assert).
- Creació d'usuaris personalitzats prelligats als seus «Personal Teams» mitjançant accions programades a `helpers.php` (`createDefaultUser` i `createDefaultProfessor`).

### Sprint 2: Core del Model de Vídeos i Code Quality
- Generació de les dependències de dades (migració de `videos`) de YouTube per allotjament URL, dades públiques de publicació indexada (`published_at`) i relacions recursives entre anteriors i següents (`previous`, `next`).
- Funcions d'accessoria d'informació sobre la data amb `Carbon`, proporcionant l'hora en format universal (timestamp Unix) i estilitzat per a humans segons l'idioma ("13 d'abril de 2026" i "fa 2 hores").
- Integració de components de Front-End amb Tailwind CSS sobre les plantilles en construcció pròpies de Livewire (`VideosAppLayout`) i la primera vista `show` de vídeos amb la seva ruta.
- Extensió i consolidació de l'arbre de tests amb noves asseveracions unitàries per les dates i Feature Tests (visualització i resposta correcta d'URL's 404 de vídeos).
- Instal·lació de Larastan per garantir la robustesa de l'anàlisi de codi PHP estàtica.

---

## 📁 Estructura del projecte

```
VideosAppPablo/
├── app/
│   ├── Actions/           # Accions de negoci (ex: crear equips)
│   ├── Concerns/          # Traits reutilitzables
│   ├── Enums/             # Enumeracions (TeamRole, TeamPermission)
│   ├── Http/
│   │   ├── Controllers/   # Controladors (VideosController)
│   │   ├── Middleware/     # Middleware personalitzat
│   │   └── Responses/     # Respostes d'autenticació personalitzades
│   ├── Livewire/          # Components Livewire
│   ├── Models/            # Models Eloquent
│   │   ├── User.php       # Model d'usuari
│   │   ├── Video.php      # Model de vídeo
│   │   ├── Team.php       # Model d'equip
│   │   ├── Membership.php # Relació usuari-equip
│   │   └── TeamInvitation.php # Invitacions d'equip
│   ├── Notifications/     # Notificacions
│   ├── Policies/          # Polítiques d'autorització
│   └── Providers/         # Proveïdors de serveis
├── config/                # Fitxers de configuració
├── database/
│   ├── factories/         # Factories per a tests
│   ├── migrations/        # Migracions de base de dades
│   └── seeders/           # Seeders per a dades inicials
├── resources/
│   └── views/             # Vistes Blade
│       ├── dashboard.blade.php
│       ├── welcome.blade.php
│       ├── videos/        # Vistes de vídeos
│       └── pages/         # Pàgines Livewire
│           ├── auth/      # Login, registre, 2FA...
│           ├── settings/  # Configuració d'usuari
│           └── teams/     # Gestió d'equips
├── routes/
│   ├── web.php            # Rutes principals
│   └── settings.php       # Rutes de configuració
└── tests/
    ├── Feature/           # Tests funcionals
    └── Unit/              # Tests unitaris
```

---

## 🧪 Tests

El projecte utilitza **PHPUnit** per als tests. Per executar-los:

```bash
# Executar tots els tests
php artisan test --compact

# Executar un fitxer de test concret
php artisan test --compact tests/Feature/Videos/VideosTest.php

# Executar un test específic per nom
php artisan test --compact --filter=testNomDelTest
```

### Cobertura de tests

| Àrea            | Tipus de test |
|-----------------|---------------|
| Vídeos          | Feature + Unit |
| Autenticació    | Feature       |
| Dashboard       | Feature       |
| Equips          | Feature       |
| Configuració    | Feature       |
| Helpers         | Unit          |

---

## 🛠️ Tecnologies utilitzades

| Tecnologia      | Versió   | Descripció                                    |
|-----------------|----------|-----------------------------------------------|
| **Laravel**     | 13.x     | Framework PHP principal                       |
| **Livewire**    | 4.x      | Components reactius sense JavaScript          |
| **Flux UI**     | 2.x      | Llibreria de components UI per Livewire       |
| **Fortify**     | 1.x      | Backend d'autenticació                        |
| **Tailwind CSS**| 4.x      | Framework CSS utilitari                       |
| **Vite**        | 8.x      | Bundler d'assets (JavaScript/CSS)             |
| **SQLite**      | 3.x      | Base de dades lleugera                        |
| **PHPUnit**     | 12.x     | Framework de tests                            |
| **Larastan**    | 3.x      | Anàlisi estàtica de codi PHP                  |
| **Pint**        | 1.x      | Formatejador de codi PHP                      |

---

## 📝 Comandes útils

| Comanda                        | Descripció                                    |
|--------------------------------|-----------------------------------------------|
| `composer run dev`             | Arrencar servidor de desenvolupament          |
| `composer setup`               | Instal·lació completa automàtica              |
| `php artisan migrate`          | Executar migracions pendents                  |
| `php artisan migrate:fresh`    | Recrear tota la base de dades                 |
| `php artisan test --compact`   | Executar tots els tests                       |
| `vendor/bin/pint --dirty`      | Formatejar fitxers PHP modificats             |
| `npm run build`                | Compilar assets per a producció               |
| `npm run dev`                  | Compilar assets en mode desenvolupament       |
