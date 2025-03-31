<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('producto_ordenes_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compras');
            $table->string('codigo_producto');
            $table->integer('cantidad_cajas');
            $table->integer('valor_unitario');
            $table->enum('estado', ['Pendiente', 'Ingresado', 'Modificado', 'Nuevo'])->default('Pendiente');
            $table->timestamps();

            $table->unique(['orden_compra_id', 'codigo_producto']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_orden_compras');
    }
};
