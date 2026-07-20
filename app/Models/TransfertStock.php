<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransfertStock extends Model
{
    protected $table = 'transferts_stock';

    protected $fillable = [
        'produit_id', 'user_id', 'emplacement_source',
        'emplacement_destination', 'quantite', 'date_transfert',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
