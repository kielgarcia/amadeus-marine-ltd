<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('hull_id');
            $table->integer('draft')->default('0');
            $table->string('drawing_no',191);
            $table->string('drawing_title',191);
            $table->string('pdf',191);
            $table->string('dwf',191);
            $table->string('dwg',191);
            $table->integer('revision_no');
            $table->datetime('date_published');
            $table->integer('uploaded_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('drawings');
    }
}
