<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
class AppBaseController extends Controller
{
    public function sendResponse($result, $message) {
    	$res = [
    		'success'			=> true,
    		'message'			=> $message,
    		'data'				=> $result,
    	];

    	return Response::json($res);
    }

    public function sendError($error, $code = 404)
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];

        return Response::json($res, $code);
    }
}
