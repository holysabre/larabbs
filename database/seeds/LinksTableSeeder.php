<?php

use Illuminate\Database\Seeder;
use App\Models\Link;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(\Faker\Generator::class);

        $links = factory(Link::class)->times(6)->make()->each(function ($link, $index)
            use($faker)
        {

        });

        Link::insert($links->toArray());
    }
}
