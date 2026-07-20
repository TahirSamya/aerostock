<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'nom', 'reference', 'category_id', 'fournisseur_id',
        'quantite', 'seuil_alerte', 'quantite_max', 'prix_achat', 'prix_vente',
        'emplacement', 'criticite',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function enAlerte(): bool
    {
        return $this->quantite <= $this->seuil_alerte;
    }

    /**
     * Capacité de référence utilisée pour la jauge (100% = ce niveau).
     * Si aucune capacité n'a été définie, on retombe sur une estimation
     * basée sur le seuil d'alerte, pour ne jamais afficher une jauge fausse.
     */
    public function capaciteReference(): int
    {
        return $this->quantite_max ?: max($this->seuil_alerte * 4, $this->quantite, 1);
    }

    /**
     * Taux de remplissage réel (0 à 1) par rapport à la capacité de référence.
     */
    public function tauxRemplissage(): float
    {
        return min($this->quantite / $this->capaciteReference(), 1);
    }

    /**
     * Niveau d'urgence de l'alerte stock, pour un affichage plus fin que "en alerte / pas en alerte".
     * rupture   : stock à 0
     * critique  : à la moitié du seuil ou moins (ou article marqué "critique" en alerte)
     * bas       : sous le seuil, sans être encore critique
     */
    public function niveauUrgence(): string
    {
        if ($this->quantite <= 0) {
            return 'rupture';
        }
        if (! $this->enAlerte()) {
            return 'ok';
        }
        if ($this->criticite === 'critique' || $this->quantite <= $this->seuil_alerte / 2) {
            return 'critique';
        }
        return 'bas';
    }
}
