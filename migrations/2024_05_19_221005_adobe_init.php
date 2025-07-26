<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AdobeInit extends Migration
{
    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->create('adobe', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number');
            $table->string('app_name')->nullable();
            $table->string('sapcode')->nullable();
            $table->string('base_version')->nullable();
            $table->string('installed_version')->nullable();
            $table->string('latest_version')->nullable();
            $table->string('description')->nullable();

            $table->index('serial_number');
            $table->index('app_name');
            $table->index('sapcode');
            $table->index('base_version');
            $table->index('installed_version');
            $table->index('latest_version');
            $table->index('description');

        });
    }
    
    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->dropIfExists('adobe');
    }
}
