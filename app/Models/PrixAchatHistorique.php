<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrixAchatHistorique extends Model
{
    protected $table = 'prix_achat_historiques';

    protected $fillable = [
        'produit_id', 'prix_achat', 'date_changement',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
