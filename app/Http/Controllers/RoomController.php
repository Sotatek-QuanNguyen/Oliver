<?php

namespace App\Http\Controllers;
use Input;
use DB;
use Excel;
use Illuminate\Http\Request;
use App\Http\Services\RoomService;
class RoomController extends AppBaseController
{
    protected $roomService;

    public function __construct(RoomService $roomService) {
    	$this->roomService = $roomService;
    }


    public function showCreateView() {
        return view('app.create_room', []);
    }

    public function intoRoomView(Request $request) {

        $data = $request["info"];
        $size = $request["size"];
        //dd($this->roomService->getAllQuizzesData());
        return view('app.roomView', [
            'infos'      => implode(",",$data),
            'quizzes'   => $this->roomService->getAllQuizzesData(),
            'questions' => $this->roomService->getAllQuestionsData(),
            'size'      => $size
        ]);
    }

    public function importExport()
    {
        return view('app.importFile');
    }

    public function importExcel(Request $request)
    {
        if($request->hasFile('import_file')){
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data) && $data->count()){
                $this->importQuestions($data[0]);
                $this->importQuizzes($data[1]);
            }
        }
        return back();
    }

    function importQuizzes($data) {
        DB::table('quizzes')->delete();
        foreach ($data as $element) {
                        $answers = json_encode([$element->a, $element->b, $element->c, $element->d]); 

                        $insert[] = ['id' => $element->stt, 'question' => $element->cau_hoi, 'answers' => $answers , 'type' => 'quizz','right_answer' => $this->formatAnswer($element->dap_an), 'description_answer' => $element->giai_thich, 'level' => $element->thang_diem];
        }

        if(!empty($insert)){
            foreach ($insert as $data) {
                DB::table('quizzes')->insert($data);
            }
                
        }
    }

    function importQuestions($data) {
        DB::table('questions')->delete();
        foreach ($data as $element) {
                    $answer = '';
                    $question = '';
                    if (!is_null($element->dap_an)) {
                        $answer = $element->dap_an;
                    }
                    if (!is_null($element->cau_hoi)) {
                        $question = $element->cau_hoi;
                    }
                        $insert[] = ['id' => $element->stt, 'question' => $question, 'answer' => $answer, 'type' => 'question', 'level' => $element->thang_diem];
        }

        if(!empty($insert)){
            $this->console_log(count($insert));
            foreach ($insert as $data) {
                DB::table('questions')->insert($data);
            }
                
        }
    }

    function formatAnswer($answer_key) {
        switch ($answer_key) {
            case 'A':
                return '1';
                break;
            case 'B':
                return '2';
                break;
            case 'C':
                return '3';
                break;
            case 'D':
                return '4';
                break;
            default:
                return '1';
                break;
        }
    }

    function console_log( $data ){
      echo '<script>';
      echo 'console.log('. json_encode( $data ) .')';
      echo '</script>';
    }

}
