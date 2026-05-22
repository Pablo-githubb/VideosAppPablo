<?php

/**
 * Model Serie — Representació de les Sèries de Vídeos (Sprint 6)
 *
 * Una Sèrie és una col·lecció de vídeos agrupats temàticament (ex: "Laravel des de zero",
 * "Conceptes de xarxes", etc.). Cada sèrie pot tenir molts vídeos associats (relació 1:N).
 *
 * Camps de la base de dades (taula: series):
 *   - id             → Clau primària auto-incremental.
 *   - title          → Títol descriptiu de la sèrie.
 *   - description    → Descripció completa del contingut de la sèrie.
 *   - image          → URL opcional d'imatge de portada.
 *   - user_name      → Nom de l'usuari que ha creat la sèrie (desnormalitzat per eficiència).
 *   - user_photo_url → URL de la foto de perfil del creador (opcional, per a la UI).
 *   - published_at   → Data/hora de publicació (nullable).
 *   - created_at     → Timestamp de creació (gestionat per Eloquent).
 *   - updated_at     → Timestamp d'última modificació (gestionat per Eloquent).
 *
 * Relacions:
 *   - Serie hasMany Video (via series_id a la taula videos)
 *
 * Tests associats: Tests\Unit\SerieTest
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    /**
     * Permetre tots els camps per a assignació massiva.
     * S'utilitza $guarded = [] en lloc de $fillable per comoditat
     * en projectes acadèmics; en producció és recomanable usar $fillable.
     */
    protected $guarded = [];

    /**
     * Conversions automàtiques de tipus (casts).
     *
     * Laravel converteix automàticament 'published_at' a un objecte Carbon
     * quan s'accedeix a la propietat, permetent usar mètodes com ->format(), ->diffForHumans(), etc.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Referència al test unitari associat a aquest model.
     * Permet descobrir fàcilment quin test cobreix aquest model.
     */
    public static function testedBy(): string
    {
        return \Tests\Unit\SerieTest::class;
    }

    /**
     * Relació 1:N amb Video.
     *
     * Una sèrie pot contenir molts vídeos. La clau forana 'series_id'
     * a la taula 'videos' apunta a l'id d'aquesta sèrie.
     *
     * Ús: $serie->videos → Col·lecció de vídeos d'aquesta sèrie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos()
    {
        return $this->hasMany(Video::class, 'series_id');
    }

    /**
     * Accessor: Data de creació en format humà llarg.
     *
     * Exemple de retorn: "22 de maig de 2026"
     * Retorna cadena buida si created_at no existeix.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }
        return Carbon::parse($this->created_at)->isoFormat('D [de] MMMM [de] YYYY');
    }

    /**
     * Accessor: Data de creació en format relatiu humà.
     *
     * Exemple de retorn: "fa 3 dies", "fa 2 hores"
     * Útil per a interfícies que mostren la "frescor" del contingut.
     */
    public function getFormattedForHumansCreatedAtAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * Accessor: Timestamp Unix de la data de creació.
     *
     * Retorna el nombre de segons des de l'Epoch Unix (1970-01-01 00:00:00 UTC).
     * Útil per a ordenació, comparació en JavaScript, o APIs que necessitin timestamp numèric.
     * Retorna null si created_at no existeix.
     */
    public function getCreatedAtTimestampAttribute(): ?int
    {
        if (!$this->created_at) {
            return null;
        }
        return Carbon::parse($this->created_at)->timestamp;
    }
}
