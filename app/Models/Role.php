<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'Cod_Rol';
    public $timestamps = false;

    // Un Rol tiene muchos registros de Acceso
    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'Cod_Rol', 'Cod_Rol');
    }
}
