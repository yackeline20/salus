<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acceso extends Model
{
    use HasFactory;

    protected $table = 'accesos';
    protected $primaryKey = 'Cod_Acceso';
    public $timestamps = false;

    // Relación inversa a Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'Cod_Rol', 'Cod_Rol');
    }

    // Relación inversa a Objeto
    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'Cod_Objeto', 'Cod_Objeto');
    }
}
