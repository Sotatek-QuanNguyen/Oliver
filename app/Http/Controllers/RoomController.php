<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\RoomService;
class RoomController extends AppBaseController
{
    protected $roomService;

    public function __construct(RoomService $roomService) {
    	$this->roomService = $roomService;
    }

    public function showRoomView() {
    	return view('app.roomView', [
    		'quizzes'	=> $this->roomService->getAllQuizzesData()
    	]);
    }
}
