<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Quiz;
class RoomService
{
    public function getAllQuizzesData() {
    	$quizzes = Quiz::all();
    	return $quizzes;
    }
}
