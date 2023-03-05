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


    public function resend(Request $request){

        /**
         * GTEリクエストでメールアドレスをパラメータとして送り、
         * そのメールアドレスに承認メールを送信するという仕様にしている。
         * なので、ビュー(Reactなど)ではinput hiddenなどでemailのパラメータを送るようにする。
         * 参考コードではログイン状態でないとメールを再送信できない仕様になっていたが、
         * メール承認が済んでいないのにログインができるというのはセキュリティ上問題があると思い
         * このような仕様にした。
         */
        $user = User::where('email','=',$request->email)->first();
        $user->sendEmailVerificationNotification();
        return response()->json(['message' => 'メール承認のリンクを再送しました']);
    }
}
