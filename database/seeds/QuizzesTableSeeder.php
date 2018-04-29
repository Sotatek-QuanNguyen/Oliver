<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class QuizzesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        $QUIZ_COUNT = 300;
        $quizzes = [];

        for($i = 1; $i <= $QUIZ_COUNT; $i++) {
        	$quizzes[] = [
        		'id'			=> $i,
        		'question'		=> $faker->text(400),
        		'answers'		=> json_encode(['answers' => $faker->sentences($nb = 4, $asText = false)]),
        		'right_answer' 	=> $faker->numberBetween($min = 1, $max = 4),
                'type'          => 'quizz'
        	];
        }


        foreach (array_chunk($quizzes, 500) as $data){
            DB::table("quizzes")->insert($data);
        }
    }
}
