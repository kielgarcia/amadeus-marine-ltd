<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use App\Zdatalog;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetPasswordLink(Request $request){
        $request->validate([
            'email' => 'bail|required|email|max:191',
        ]);
        
        $user = User::whereEmail($request->email)->where('status', '=', 'Active')->first();

        if(is_null($user)){
            return redirect()->back()->with('failed', 'Email does not exist in our records!');
        }
        else{
            $this->createPasswordResetToken($request->email);
            
            // Send Mail Functions
            $recipient = User::where('id','=',$user->id)->first();
            $recipientEmail = $recipient->email;
            $recipientName = $recipient->name;
            $tokenData = $this->getCreatedPasswordResetToken($request->email);
            $host = $request->getSchemeAndHttpHost();
            $link = $host.'/password/reset/'.$tokenData->token.'?email='.urlencode($user->email);
            $note = 'You are receiving this email because we received a password reset request for your account. Token will expire in 60 minutes. If you did not request a password reset, no further action is required.';


            $data = array(
                'name' => $recipientName,
                'email' => $recipientEmail,
                // 'password' => $randomString,
                'note' => $note,
                'link' => $link
            );

            Mail::send('Admin.ResetPasswordEmail', $data, function($message) use ($recipientEmail,$recipientName) {
                $message->to($recipientEmail,$recipientName)
                        ->subject('Reset Password - Amadeus Marine Ltd. Drawing Database');
                $message->from('kyelgarcia124@gmail.com','Kiel Garcia');
            });

            $action = 'Request a reset password link.';
            $primary_id = $user->id;
            $this->saveToDataLog($action, $primary_id);

            return redirect()->back()->with('success', 'Password reset link was sent to your email.');
        }
    }

    public function createPasswordResetToken($email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
    }

    public function getCreatedPasswordResetToken($email){
        return $tokenData = DB::table('password_resets')
        ->where('email', $email)->latest()->first();
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