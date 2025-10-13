<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    use HasFactory;

    protected $table = 'objetos';
    protected $primaryKey = 'Cod_Objeto';
    public $timestamps = false;

    // Un Objeto puede tener muchos registros de Acceso
    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'Cod_Objeto', 'Cod_Objeto');
    }
}
