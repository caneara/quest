<?php declare(strict_types = 1);

// Using directives
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// User migration
class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     **/
    public function up() : void
    {
        Schema::create('users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('country');
        });
    }



    /**
     * Reverse the migrations.
     *
     **/
    public function down() : void
    {
        Schema::dropIfExists('users');
    }

}