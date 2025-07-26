<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AdobeAddIsUpToDate extends Migration
{
    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table('adobe', function (Blueprint $table) {
            $table->boolean('is_up_to_date')->nullable()->default(null);
            $table->index('is_up_to_date');
        });
    }
    
    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table('adobe', function (Blueprint $table) {
            $table->dropColumn('is_up_to_date');
        });
    }
} 