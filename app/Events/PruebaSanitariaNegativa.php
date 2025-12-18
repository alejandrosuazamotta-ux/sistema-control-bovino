<?php

namespace App\Events;

use App\Models\PruebaSanitaria;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PruebaSanitariaNegativa
{
    use Dispatchable, SerializesModels;

    public PruebaSanitaria $pruebaSanitaria;

    /**
     * Create a new event instance.
     */
    public function __construct(PruebaSanitaria $pruebaSanitaria)
    {
        $this->pruebaSanitaria = $pruebaSanitaria;
    }
}
