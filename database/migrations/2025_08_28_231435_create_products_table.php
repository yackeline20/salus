<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoTable extends Migration
{
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->integer('Cod_Producto', true);
            $table->string('Nombre_Producto', 30);
            $table->text('Descripcion')->nullable();
            $table->decimal('Precio_Venta', 10, 2);
            $table->decimal('Costo_Compra', 10, 2);
            $table->integer('Cantidad_En_Stock');
            $table->date('Fecha_Vencimiento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto');
    }
}