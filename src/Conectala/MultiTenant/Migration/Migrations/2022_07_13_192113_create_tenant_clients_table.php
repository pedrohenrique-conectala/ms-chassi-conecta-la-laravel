<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome da conexão. Qualquer nome.');
            $table->string('database')->unique()->comment('Nome do banco de dados');
            $table->string('tenant')->unique()->comment('Nome do cliente. Ex: conectala, decathlon, mesbla.');
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
        Schema::dropIfExists('tenant_clients');
    }
}
