<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\ModelHasRole;
use App\Zdatalog;
use App\Drawing;
use App\Hull;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:administrator',[ 'only' => 'index']);
        
    }

    public function index()
    {
        $users = User::join('model_has_roles','users.id','=','model_has_roles.model_id')
                    ->join('roles','roles.id','=','model_has_roles.role_id')
                    ->select('model_has_roles.*','users.*','users.id as user_id','users.name as user_name','users.email as user_email','roles.*','roles.id as role_id','roles.name as role_name')
                    ->get();
        $roles = Role::all()->sortBy('name');


        return view('Admin.index', compact('users','roles'));
    }

    public function viewProfile()
    {
        $drawings = Drawing::where('deleted_at','=',NULL)->count();
        $user = User::findOrFail( Auth::id() );

        $total_sr = Drawing::join('hulls','hulls.id','=','drawings.hull_id')
                            ->where('hulls.type','=','Ship Repair')
                            ->where('drawings.draft','=','0')
                            ->where('drawings.deleted_at','=',NULL)
                            ->where('drawings.uploaded_by','=', Auth::id() )->count();

        $total_sr_draft = Drawing::join('hulls','hulls.id','=','drawings.hull_id')
                            ->where('hulls.type','=','Ship Repair')
                            ->where('drawings.draft','=','1')
                            ->where('drawings.deleted_at','=',NULL)
                            ->where('drawings.uploaded_by','=', Auth::id() )->count();

        $total_sb = Drawing::join('hulls','hulls.id','=','drawings.hull_id')
                            ->where('hulls.type','=','Ship Building')
                            ->where('drawings.draft','=','0')
                            ->where('drawings.deleted_at','=',NULL)
                            ->where('drawings.uploaded_by','=', Auth::id() )->count();

        $total_sb_draft = Drawing::join('hulls','hulls.id','=','drawings.hull_id')
                            ->where('hulls.type','=','Ship Building')
                            ->where('drawings.draft','=','1')
                            ->where('drawings.deleted_at','=',NULL)
                            ->where('drawings.uploaded_by','=', Auth::id() )->count();

        $total_sp = Drawing::where('hull_id','=','28')
                            ->where('draft','=','0')
                            ->where('deleted_at','=',NULL)
                            ->where('uploaded_by','=', Auth::id() )->count();
        
        $total_sp_draft = Drawing::where('hull_id','=','28')
                            ->where('draft','=','1')
                            ->where('deleted_at','=',NULL)
                            ->where('uploaded_by','=', Auth::id() )->count();
                        
        return view('Admin.profile', compact('user','total_sr','total_sr_draft','total_sb','total_sb_draft','total_sp','total_sp_draft','drawings'));
    }

    public function store(Request $request)
    {
        $request->validate(['new_user_name' => 'required',
                            'new_user_email' => 'required',
                            'new_user_role' => 'required',
                            'new_user_password' => 'required|min:8',
                            'new_user_confirm_password' => 'required|min:8']);

        $duplicate = User::where('email','=', $request->get('new_user_email'))->count();
        if( $duplicate > 0 ){

            return redirect()->back()->with('failed','Email '.$request->get('new_user_email').' already exists.');

        }else{

            if( $request->get('new_user_password') != $request->get('new_user_confirm_password') ){

                return redirect()->back()->with('failed','Password confirmation does not match.');

            }else{

                $user = new User();
                $user->name = $request->get('new_user_name');
                $user->email = $request->get('new_user_email');
                $user->password = Hash::make($request->get('new_user_password'));
                $user->save();
                $this->saveToZdatalog($user, $request);


                $model_has_role = new ModelHasRole();
                $model_has_role->role_id = $request->get('new_user_role');
                $model_has_role->model_type = 'App\User';
                $model_has_role->model_id = $user->id;
                $model_has_role->save();
                $this->saveToZdatalog($model_has_role, $request);


                return redirect()->back()->with('success',$user->name.' added successfully.');

            }
        }

    }

    public function update(Request $request)
    {
        $request->validate(['edit_user_name' => 'required',
                            'edit_user_email' => 'required|email',
                            'edit_user_role' => 'required']);

        $id = $request->get('edit_user_id');
        $user = User::findOrFail($id);
        $role = ModelHasRole::where('model_id','=', $id)->first();
        
        $duplicate = User::where('email','=', $request->get('edit_user_email'))->where('id','!=', $id)->count();
        if( $duplicate > 0 ){

            return redirect()->back()->with('failed', $request->get('edit_user_email').' already exists.');

        }else{

            if( $user->name != $request->get('edit_user_name') ||
                $user->email != $request->get('edit_user_email')||
                $role->role_id != $request->get('edit_user_role') ) {

                $user->name = $request->get('edit_user_name');
                $user->email = $request->get('edit_user_email');
                $user->update();
                $this->saveToZdatalog($user, $request);
                

                $remove_role = ModelHasRole::where('model_id','=', $id)->delete();
                $update_role = new ModelHasRole();
                $update_role->role_id = $request->get('edit_user_role');
                $update_role->model_type = 'App\User';
                $update_role->model_id = $id;
                $update_role->save();
                $this->saveToZdatalog($update_role, $request);

                return redirect()->back()->with('success', $user->name.' updated successfully.');
                
            }else{
                return redirect()->back();
            }
        }
    }

    public function updateMyDetails(Request $request)
    {
        $request->validate(['edit_my_name' => 'required','edit_my_email' => 'required']);

        $id = $request->get('my_id');
        $my_profile = User::findOrFail($id);
        
        $duplicate = User::where('email','=', $request->get('edit_my_email'))->where('id','!=', $id)->count();

            if( $duplicate > 0 ){
                return redirect()->back()->with('failed',$request->get('edit_my_email').' already exists.');
            }else{

                if( $my_profile->name != $request->get('edit_my_name') || $my_profile->email != $request->get('edit_my_email'))
                {
                    $my_profile->name = $request->get('edit_my_name');
                    $my_profile->email = $request->get('edit_my_email');
                    $my_profile->update();
                    return redirect()->back()->with('success', $my_profile->name.' updated successfully.');
                }else{
                    return redirect()->back();
                }
            }
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['edit_current_password' => 'required|min:8',
                            'edit_new_password' => 'required|min:8',
                            'edit_confirm_password' => 'required|min:8']);
        
        $id = $request->get('edit_password_user_id');
        $current_password = $request->get('edit_current_password');
        $user = User::findOrFail($id);

        if( !Hash::check( $current_password,$user->password) ) {

            return redirect()->back()->with('failed','The specified password of '.$user->name.' does not match in our records.');

        }else{

            if( $request->get('edit_new_password') != $request->get('edit_confirm_password')) {

                return redirect()->back()->with('failed','Confirmation of new password does not match.');

            }else{

                $user->password = Hash::make($request->get('edit_new_password'));
                $user->update();
                $this->saveToZdatalog($user, $request);
                return redirect()->back()->with('success','Password of '.$user->name.' updated successfully.');
            }
        }
    }

    public function saveToZdatalog($model, $request)
    {
        $dataDecode = json_decode($model);
        $modelAttributes = $model->getattributes();
        $action = $request->path();
        $method = $request->method();
 
        $zdatalog = new Zdatalog();
        $zdatalog->table_name = $model->getTable();
        $zdatalog->primary_id = $dataDecode->id;
 
        $data = "";
        $data .="action => $action\n";
        $data .="method => $method\n";
        foreach ($modelAttributes as $key => $value){
            $data .= "$key => $value\n";
        }
 
        $zdatalog->data = $data;
        $zdatalog->user = Auth::user()->name;
        $zdatalog->entered_date_time = Carbon::now();
        $zdatalog->save();
    }
}
