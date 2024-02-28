<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('publishers')->count()) {
            $this->insert100000Publishers();
        }

        if (!DB::table('authors')->count()) {
            $this->insert200000Authors();
        }

        if (!DB::table('books')->count()) {
            $this->insert1000000Books();
        }

        if (!DB::table('author_book')->count()) {
            $this->insert1000000AuthorBook();
        }
    }

    protected function insert100000Publishers(): void
    {
        $startId = 1;
        foreach (range(1, 1000) as $i) {
            DB::table('publishers')->insert($this->fake100PublishersData($startId));
            $startId += 100;
        }
    }
    protected function fake100PublishersData($startId): array
    {
        $output = [];
        foreach (range($startId, $startId + 99) as $i) {
            $output[] = [
                'id' => $i,
                'name' => fake()->company
            ];
        }
        return $output;
    }

    protected function insert200000Authors(): void
    {
        $startId = 1;
        foreach (range(1, 2000) as $i) {
            DB::table('authors')->insert($this->fake100AuthorsData($startId));
            $startId += 100;
        }
    }

    protected function fake100AuthorsData($startId): array
    {
        $output = [];
        foreach (range($startId, $startId + 99) as $i) {
            $output[] = [
                'id' => $i,
                'name' => fake()->name
            ];
        }
        return $output;
    }

    protected function insert1000000Books(): void
    {
        $startId = 1;
        foreach (range(1, 10000) as $i) {
            DB::table('books')->insert($this->fake100Books($startId));
            $startId += 100;
        }
    }

    protected function fake100Books($startId): array
    {
        $output = [];
        foreach (range($startId, $startId + 99) as $i) {
            $output[] = [
                'id' => $i,
                'title' => fake()->text(50),
                'summary' => fake()->text(100),
                'publisher_id' => rand(1, 100000)
            ];
        }
        return $output;
    }

    protected function insert1000000AuthorBook(): void
    {
        $startId = 1;
        foreach (range(1, 1000) as $i) {
            DB::table('author_book')->insert($this->fake1000AuthorBook($startId));
            $startId += 1000;
        }
    }
    protected function fake1000AuthorBook($startId): array
    {
        $output = [];
        foreach (range($startId, $startId + 999) as $i) {
            $output[] = [
                'id' => $i,
                'book_id' => $i,
                'author_id' => rand(1, 200000)
            ];
        }
        return $output;
    }
}
