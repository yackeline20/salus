<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;
    protected $table = 'direccion';
    protected $primaryKey = 'Cod_Direccion';
    public $timestamps = false;

    protected $fillable = ['Cod_Persona','Direccion','Descripcion'];

    public function persona() { return $this->belongsTo(Persona::class, 'Cod_Persona'); }
}
