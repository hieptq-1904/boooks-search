<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('summary');
            $table->timestamps();
            $table->unsignedInteger('publisher_id');
        });

        DB::statement("ALTER TABLE books ADD COLUMN ts_title tsvector GENERATED ALWAYS AS (to_tsvector('english', title)) STORED;");
        DB::statement("ALTER TABLE books ADD COLUMN ts_summary tsvector GENERATED ALWAYS AS (to_tsvector('english', summary)) STORED;");
        DB::statement("CREATE INDEX books_ts_title_idx ON books USING gin(ts_title)");
        DB::statement("CREATE INDEX books_ts_summary_idx ON books USING gin(ts_summary)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
