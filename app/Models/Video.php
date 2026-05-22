<?php

/**
 * Model Video — Representació d'un Vídeo de la Plataforma
 *
 * Conté tots els atributs i relacions del recurs Video. Sprint 5 va afegir
 * el camp user_id per vincular cada vídeo al seu creador. Sprint 6 va afegir
 * el camp series_id per permetre agrupar vídeos dins d'una sèrie temàtica.
 *
 * Camps de la base de dades (taula: videos):
 *   - id           → Clau primària auto-incremental.
 *   - title        → Títol del vídeo.
 *   - description  → Descripció del contingut del vídeo.
 *   - url          → URL del vídeo (YouTube o similar).
 *   - published_at → Data/hora de publicació (nullable, convertit automàticament a Carbon).
 *   - user_id      → [Sprint 5] Clau forana cap a users.id. Identifica el creador.
 *   - series_id    → [Sprint 6] Clau forana cap a series.id. Agrupa el vídeo en una sèrie.
 *   - created_at   → Timestamp de creació (gestionat per Eloquent).
 *   - updated_at   → Timestamp d'última modificació (gestionat per Eloquent).
 *
 * Relacions:
 *   - Video belongsTo User   (via user_id)
 *   - Video belongsTo Serie  (via series_id) [Sprint 6]
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    /**
     * Permetre tots els camps per a assignació massiva.
     */
    protected $guarded = [];

    /**
     * Conversions automàtiques de tipus (casts).
     *
     * 'published_at' es converteix a instància Carbon en llegir-lo,
     * habilitant mètodes de data com ->format(), ->isoFormat(), ->diffForHumans().
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Accessor: Data de publicació en format humà llarg.
     *
     * Exemple: "22 de maig de 2026"
     * Disponible com a $video->formatted_published_at a les vistes Blade.
     */
    public function getFormattedPublishedAtAttribute(): string
    {
        if (!$this->published_at) {
            return '';
        }
        return Carbon::parse($this->published_at)->isoFormat('D [de] MMMM [de] YYYY');
    }

    /**
     * Accessor: Data de publicació en format relatiu.
     *
     * Exemple: "fa 2 dies", "fa 5 minuts"
     * Disponible com a $video->formatted_for_humans_published_at.
     */
    public function getFormattedForHumansPublishedAtAttribute(): string
    {
        if (!$this->published_at) {
            return '';
        }
        return Carbon::parse($this->published_at)->diffForHumans();
    }

    /**
     * Accessor: Timestamp Unix de la data de publicació.
     *
     * Retorna el nombre de segons des de l'Epoch. Útil per a ordenació
     * al costat del client o per a APIs que necessitin valors numèrics.
     */
    public function getPublishedAtTimestampAttribute(): ?int
    {
        if (!$this->published_at) {
            return null;
        }
        return Carbon::parse($this->published_at)->timestamp;
    }

    /**
     * Relació N:1 amb User (Sprint 5).
     *
     * Cada vídeo pertany a un usuari creador. La clau forana 'user_id'
     * apunta a users.id. Permet consultar $video->user->name, etc.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relació N:1 amb Serie (Sprint 6).
     *
     * Cada vídeo pot estar assignat opcionalment a una sèrie temàtica.
     * La clau forana 'series_id' apunta a series.id. Si series_id és null,
     * el vídeo no pertany a cap sèrie.
     *
     * Ús: $video->serie → Instància Serie o null.
     *     $video->serie->title → Nom de la sèrie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serie()
    {
        // Especifiquem explícitament la clau forana 'series_id' perquè
        // Eloquent no la pot inferir automàticament del nom 'serie' (esperaria 'serie_id').
        return $this->belongsTo(Serie::class, 'series_id');
    }
}
