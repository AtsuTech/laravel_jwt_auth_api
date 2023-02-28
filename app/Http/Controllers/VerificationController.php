<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    //
    public function verify($user_id, Request $request){

        //送信されてきたリクエストが有効な著名を持っているかを検査
        if(!$request->hasValidSignature()){
            return $this->respondUnAuthorizedRequest();
        }

        $user = User::findOrFail($user_id);

        if(!$user->hasVerifiedEmail()){
            //markEmailAsVerified()でUserテーブルの"email_verifiyed_at"に日付を保存してる？
            $user->markEmailAsVerified();
        }

        return redirect()->to('/');
    }


    public function resend(){
        if(auth()->user()->hasVerifiedEmail()){
            return $this->respondBadRequest();
        }

        auth()->user()->sendEmailVerificationNotification();
        return $this->respondWithMessage('メール承認のリンクを再送しました');
    }
}
