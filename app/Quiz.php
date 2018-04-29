<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    //

	public $table = 'quizzes';

	public $fillable = ['question','answers','right_answer','description_answer','type'];

    protected $casts = [
    	'answers' => 'array'
    ];
}
