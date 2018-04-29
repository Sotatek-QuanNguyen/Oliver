<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Quiz;
use App\Question;
class RoomService
{
    public function getAllQuizzesData() {
    	$quizzes = Quiz::all()->toArray();
    	return $quizzes;
    }

    public function getAllQuestionsData() {
    	$questions = Question::all()->toArray();
    	return $questions;
    }
}
