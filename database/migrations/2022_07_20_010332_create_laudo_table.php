<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laudo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('descricao');
            $table->string('capacidade');
            $table->string('massa');
            $table->string('quantidade');
            $table->string('tipo');
            $table->string('empresa');
            $table->string('local_geral');
            $table->string('local_especifico');
            $table->date('data');
            $table->string('contrato');
            $table->string('crea');
            $table->string('art');
            $table->string('capacidade_2')->nullable();
            $table->string('massa_2')->nullable();
            $table->string('quantidade_2')->nullable();
            $table->string('tipo_2')->nullable();
            $table->string('empresa_2')->nullable();
            $table->string('local_geral_2')->nullable();
            $table->string('local_especifico_2')->nullable();
            $table->date('data_2')->nullable();
            $table->string('contrato_2')->nullable();
            $table->string('crea_2')->nullable();
            $table->string('art_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laudo');
    }
};
