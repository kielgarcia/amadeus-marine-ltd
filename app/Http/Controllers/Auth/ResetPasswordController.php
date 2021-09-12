<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Zdatalog;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'bail|required|email|max:191',
            'password' => 'bail|required|string|min:8|max:191',
            'password_confirmation' => 'bail|required|string|same:password',
        ]);

        $tokenData = DB::table('password_resets')->where('token', $request->token)->where('created_at', '>=', Carbon::now()->subHours(1))->first();
        if(is_null($tokenData)){
            return redirect()->back()->with('failed', 'Invalid token.');
        }

        $user = User::where('email', $tokenData->email)->first();
        if(is_null($user) || ($request->get('email') != $tokenData->email)){
            return redirect()->back()->with('failed', 'Invalid email.');
        }

        $user->password = Hash::make($request->get('password'));
        $user->updated_at = Carbon::now();
        $user->update();

        $action = 'Updated password';
        $primary_id = $user->id;
        $this->saveToDataLog($action, $primary_id);

        DB::table('password_resets')->where('email', $user->email)->delete();
        Auth::login($user);

        return redirect('/home')->with('success', 'Your password has been updated.');
    }

    public function saveToDataLog( $action, $primary_id )
    {
        $log = new Zdatalog;
        $log->action = $action;
        $log->primary_id = $primary_id;
        $log->user_id = $primary_id;
        $log->created_at = Carbon::now();
        $log->save();
    }
}
