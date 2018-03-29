<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    //

	public $table = 'quizzes';

    protected $casts = [
    	'answers' => 'array'
    ];
}
