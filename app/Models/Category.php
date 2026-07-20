<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nom', 'code', 'description'];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
