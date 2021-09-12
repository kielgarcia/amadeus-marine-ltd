<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevisionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('drawing_id');
            $table->integer('revision_no');
            $table->integer('uploaded_by');
            $table->datetime('date_published');
            $table->string('pdf');
            $table->string('dwf');
            $table->string('dwg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revision_histories');
    }
}
