<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEmplacement extends Model
{
    protected $table = 'stock_emplacements';

    protected $fillable = ['produit_id', 'emplacement', 'quantite'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
