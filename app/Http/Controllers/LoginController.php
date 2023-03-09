<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JWTAuth;//JWTAuth::attempt()を使用するため

class LoginController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * ログイン処理
     * メール承認が完了(Userテーブルのemail_verified_at != null)していないと
     * ログインができない仕様にしてある
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        // メール承認のカラムのデータ取得
        $user = Authenticatable::where('email','=',request(['email']))->first();
        $verify_check = $user->email_verified_at;

        // メール承認のカラムがnullどうかチェック。nullならログインを却下する
        if(is_null($verify_check)){
            return response()->json(['error' => 'まだメールが承認されていません'], 401);
        }

        //JWTAuth::attempt()にしないとトークンが得られなかった(auth()->attemptだとtrueと帰ってくる)
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token,$verify_check);
    }

    /**
     * マイページ？？
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * ログアウト処理
     */
    public function logout()
    {
        auth('')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Refresh a token.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * ログイン成功時にトークンなどの値を返す
     */
    protected function respondWithToken($token,$verify_check)
    {
        return response()->json([
            'access_token' => $token,
            'email_verify' => $verify_check,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }


}


