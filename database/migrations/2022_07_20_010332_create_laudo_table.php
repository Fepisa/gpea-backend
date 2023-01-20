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
            $table->string('local_geral');
            $table->string('local_especifico');
            $table->date('data');
            $table->date('validade');
            $table->string('relatorio');
            $table->string('responsavel_tecnico');
            $table->string('contratante');
            $table->string('contratada');
            $table->string('observacao')->nullable();
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
