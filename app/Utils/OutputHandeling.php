<?php

namespace App\Utils;
use App\Utils\ErrorCodes;

class OutputHandeling extends ErrorCodes{
    Public function renderSucessfullResponse($message){
        return response()->json([ 'message' => $message, 'status'=>200]);
    }

    Public  function renderDataResponse($data, $message=''){
        return response()->json(['data' => $data, 'message' => $message, 'status'=>200]);
    }

    Public function renderErroresponse($errorCode, $message, $systemErrorCode){
        if(config('app.debug') == false)
            return response()->json([ 'message' => $message, 'status'=>$errorCode]);
        else{
            $user = auth()->user();
            return response()->json([ 'message' => $message, 'status'=>$errorCode , 'systemErrorCode' => $systemErrorCode, 'user' => $user]);
        }

    }
}
