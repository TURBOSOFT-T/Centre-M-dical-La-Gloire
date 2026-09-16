<?php

use App\Models\config;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable()->default(null);
            $table->string('logoHeader')->nullable()->default(null);
            $table->string('logofooter')->nullable()->default(null);
            $table->string('telephone')->nullable()->default(null);
            $table->string('addresse')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('description')->nullable()->default(null);
            $table->integer('frais')->nullable();
            //  $table->decimal("frais", 10,3)->nullable();
            $table->string('icon')->nullable()->default(null);
            $table->text('slogan')->nullable();
            $table->text('titre_annee')->nullable();
            $table->text('titre_prix')->nullable();
            $table->text('titre_satisfaction')->nullable();

            $table->integer("satisfaction")->nullable();
            $table->string('icone_satisfaction')->nullable();
            $table->string("des_satisfaction")->nullable();

            $table->integer("annee")->nullable();
            $table->string('icone_annee')->nullable();
            $table->string('des_annee')->nullable();

            $table->integer("prix")->nullable();
            $table->string('icone_prix')->nullable();
            $table->string('des_prix')->nullable();

            $table->text('titre_apropos')->nullable();
            $table->text('des_apropos')->nullable();
            $table->string('image_apropos')->nullable();

            $table->text('titre_apropos1')->nullable();
            $table->text('des_apropos1')->nullable();
            $table->string('image_apropos1')->nullable();

            $table->text('titre_apropos2')->nullable();
            $table->text('des_apropos2')->nullable();
            $table->string('image_apropos2')->nullable();

            $table->string('image_contact')->nullable();
            $table->string('image_shop')->nullable();
            $table->string('image_about')->nullable();
            $table->string('image_login')->nullable();
            $table->string('image_register')->nullable();
            $table->timestamps();
        });


        // $config = new config();
        // $config->logo=null;
        // $config->save();


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
