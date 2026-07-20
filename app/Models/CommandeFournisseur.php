<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeFournisseur extends Model
{
    protected $table = 'commandes_fournisseurs';

    protected $fillable = [
        'fournisseur_id', 'produit_id', 'user_id',
        'quantite_commandee', 'quantite_recue', 'statut',
        'prix_unitaire', 'date_commande', 'date_reception', 'notes',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quantiteRestante(): int
    {
        return $this->quantite_commandee - $this->quantite_recue;
    }
}
