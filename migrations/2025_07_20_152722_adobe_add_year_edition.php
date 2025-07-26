<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AdobeAddYearEdition extends Migration
{
    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table('adobe', function (Blueprint $table) {
            $table->string('year_edition')->nullable()->after('base_version');
            $table->index('year_edition');
        });
    }
    
    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table('adobe', function (Blueprint $table) {
            $table->dropIndex(['year_edition']);
            $table->dropColumn('year_edition');
        });
    }
} 