<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;
use App\Models\User;
use App\Models\Topic;

class ReplysTableSeeder extends Seeder
{
    public function run()
    {

        $faker = app(Faker\Generator::class);

        //所有的用户id
        $user_ids = User::all()->pluck('id')->toArray();

        //所有的话题id
        $topic_ids = Topic::all()->pluck('id')->toArray();

        $replys = factory(Reply::class)->times(1000)->make()->each(function ($reply, $index)
            use($faker, $user_ids, $topic_ids)
        {
            //抽取随机用户id
            $reply->user_id = $faker->randomElement($user_ids);

            //抽取随机话题id
            $reply->topic_id = $faker->randomElement($topic_ids);
        });

        Reply::insert($replys->toArray());
    }

}

