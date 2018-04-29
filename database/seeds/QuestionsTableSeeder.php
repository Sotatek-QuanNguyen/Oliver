<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $QUIZ_COUNT = 300;
        $quizzes = [];

        for($i = 1; $i <= $QUIZ_COUNT; $i++) {
        	$quizzes[] = [
        		'id'			=> $i,
        		'question'		=> $faker->text(400),
        		'answer'		=> $faker->text($maxNbChars = 100),	
                'type'          => 'question'
        	];
        }


        foreach (array_chunk($quizzes, 500) as $data){
            DB::table("questions")->insert($data);
        }
    }
}
