<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('laudo', function (Blueprint $table) {
            $table->foreignId('criador_id')->nullable()->after('observacao')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('responsavel_id')->nullable()->after('criador_id')
                ->constrained('users')->nullOnDelete();
        });

        // Laudos existentes: vincula ao primeiro usuário Admin (menor id)
        $adminId = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'Admin')
            ->min('users.id');

        if ($adminId) {
            DB::table('laudo')->whereNull('criador_id')->update(['criador_id' => $adminId]);
            DB::table('laudo')->whereNull('responsavel_id')->update(['responsavel_id' => $adminId]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laudo', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsavel_id');
            $table->dropConstrainedForeignId('criador_id');
        });
    }
};
