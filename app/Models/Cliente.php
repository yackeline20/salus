<?php
// En: app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente'; // Nombre de tu tabla de clientes
    protected $primaryKey = 'Cod_Cliente';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Tipo_Cliente',
        'Nota_Preferencia'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }
}
