<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
    use HasFactory;

    protected $table = 'correo';
    protected $primaryKey = 'Cod_Correo';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Correo',
        'Tipo_correo'
    ];

    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }

    // --- Métodos AUXILIARES ---

    public function getNombreCompleto()
    {
        return $this->persona ? $this->persona->getNombreCompleto() : 'Desconocido';
    }

    public function esPersonal()
    {
        return $this->Tipo_correo === 'Personal';
    }

    public function scopePersonales($query)
    {
        return $query->where('Tipo_correo', 'Personal');
    }
}
