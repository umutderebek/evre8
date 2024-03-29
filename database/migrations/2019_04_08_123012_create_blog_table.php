<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('psikolog_id')->unsigned()->nullable();
            $table->integer('blogkategori_id')->unsigned()->nullable();
            $table->string('slug')->unique();
            $table->string('post_baslik',150);
            $table->text('yazi');
            $table->string('kimage')->nullable();
            $table->string('gimage')->nullable();
            $table->string('blog_aciklama');
            $table->string('blog_keyword');
            $table->boolean('yayın')->default(false);
            $table->boolean('taslak')->default(false);
            $table->timestamps();
            $table->softDeletes()->nullable();



            $table->foreign('blogkategori_id')->references('id')->on('blogkategori')->onDelete('cascade');
            $table->foreign('psikolog_id')->references('id')->on('psikologs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog');
    }
}
