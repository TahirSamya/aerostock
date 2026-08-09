<?php

namespace App\Mail;

use App\Models\Produit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlerteRuptureCritique extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Produit $produit)
    {
    }

    public function build()
    {
        return $this->subject("⚠ Rupture de stock — article critique : {$this->produit->nom}")
            ->view('emails.alerte-rupture');
    }
}
