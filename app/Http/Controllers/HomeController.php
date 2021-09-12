<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Zdatalog;
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $current_datetime = Carbon::now();
        $my_activity_count = Zdatalog::where('user_id','=',Auth::id())->count();
        $my_activity = Zdatalog::leftJoin('users','users.id','=','zdatalog.user_id')
                ->select('zdatalog.*','users.name as action_by')    
                ->where('zdatalog.user_id','=',Auth::id())
                ->orderBy('zdatalog.id','desc')
                ->take(3)
                ->get();

        return view('home',compact('my_activity','my_activity_count','current_datetime'));
    }
}
