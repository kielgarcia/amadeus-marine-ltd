<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
Use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\User;
use App\Role;
use App\Permission;
use App\RoleHasPermission;
use App\ModelHasRole;
use Carbon\Carbon;
use App\Zdatalog;
use Mail;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:viewUsersManagement',[ 'only' => 'index']);
        $this->middleware('permission:saveUser',[ 'only' => 'saveUser']);
        $this->middleware('permission:updateUser',[ 'only' => 'updateUser']);
        $this->middleware('permission:resetUserPassword',[ 'only' => 'resetUserPassword']);
        $this->middleware('permission:saveRole',[ 'only' => 'saveRole']);
        $this->middleware('permission:updateRole',[ 'only' => 'updateRole']);
        $this->middleware('permission:savePermission',[ 'only' => 'savePermission']);
        $this->middleware('permission:updatePermission',[ 'only' => 'updatePermission']);
        
    }

    public function index()
    {
        $users = User::join('model_has_roles','users.id','=','model_has_roles.model_id')
                    ->join('roles','roles.id','=','model_has_roles.role_id')
                    ->select('model_has_roles.*','users.*','users.id as user_id','users.name as user_name','users.email as user_email','roles.*','roles.id as role_id','roles.name as role_name')
                    ->get();

        $roles = Role::all()->sortBy('name');

        $permissions = Permission::orderBy('updated_at','desc')->get();

            foreach($roles as $key => $role){
                $role_permissions[] = RoleHasPermission::leftJoin('permissions', 'role_has_permissions.permission_id', 'permissions.id')
                ->select('role_has_permissions.*','permissions.id AS permission_id', 'permissions.name AS permission', 'permissions.*')
                ->where('role_has_permissions.role_id', '=', $role->id)
                ->orderBy('permissions.name', 'asc')
                ->get();
            }

            $role_has_permissions = new Collection();

            foreach($role_permissions as $items){
                foreach($items as $item){
                    $role_has_permissions->push($item);
                }
            }

        return view('Admin.index', compact('users','roles','role_has_permissions','permissions'));
    }

    public function viewActivityLog()
    {
        $model_has_role = ModelHasRole::where('model_id','=',Auth::id())->first();

        if( $model_has_role->role_id != 1 )
        {
            $activity_logs = Zdatalog::leftJoin('users','users.id','=','zdatalog.user_id')
                ->select('zdatalog.*','users.name as action_by')    
                ->where('zdatalog.user_id','=',Auth::id())
                ->orderBy('zdatalog.id','desc')
                ->take(100)
                ->get();
        }else{
            $activity_logs = Zdatalog::leftJoin('users','users.id','=','zdatalog.user_id')
                ->select('zdatalog.*','users.name as action_by')
                ->orderBy('zdatalog.id','desc')
                ->take(100)
                ->get();
        }


        return view('Admin.activityLog', compact('activity_logs'));
    }


    #region Users Functions
    public function saveUser(Request $request)
    {
        $request->validate([
            'new_user_name' => 'bail|required|string',
            'new_user_email' => 'bail|required|email',
            'new_user_role' => 'bail|required|numeric'
        ]);
        
        try{
            $randomString = Str::random(8);

            DB::beginTransaction();
            $user = new User();
            $user->name = $request->get('new_user_name');
            $user->email = $request->get('new_user_email');
            $user->password = Hash::make($randomString);    
            $user->created_by = Auth::id();
        
            $duplicate = User::where('email','=',$user->email)->first();

            if(is_null($duplicate))
            {
                $user->save();
                DB::commit();

                $model_has_role = new ModelHasRole();
                $model_has_role->role_id = $request->get('new_user_role');
                $model_has_role->model_type = 'App\User';
                $model_has_role->model_id = $user->id;
                $model_has_role->save();

                // Send Mail Functions
                $recipient = User::where('id','=',$user->id)->first();
                
                $recipientEmail = $recipient->email;
                $recipientName = $recipient->name;
                $host = $request->getSchemeAndHttpHost();
                $link = $host.'/login';
                $note = 'Your account is now active.';

                $data = array(
                    'name' => $recipientName,
                    'email' => $recipientEmail,
                    'password' => $randomString,
                    'note' => $note,
                    'link' => $link
                );

                Mail::send('Admin.mail', $data, function($message) use ($recipientEmail,$recipientName) {
                    $message->to($recipientEmail,$recipientName)
                            ->subject('New Account - Amadeus Marine Ltd. Drawing Database');
                    $message->from('amadeus@motolite.com','Amadeus Drawing Database Support'); // Email should be change
                });
   
                $action = 'Added new user - '.$user->name;
                $primary_id = $user->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success',$recipient->name.' user added successfully. Account credentials sent to '.$recipient->email);
                
            }else{  
                return redirect()->back()->with('failed',$user->email.' already exists.');    
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'edit_user_id' => 'bail|required|numeric',
            'edit_user_name' => 'bail|required|string',
            'edit_user_email' => 'bail|required|email',
            'edit_user_role' => 'bail|required|numeric'
        ]);

        $id = $request->get('edit_user_id');

        try{
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->name = $request->get('edit_user_name');
            $user->email = $request->get('edit_user_email');
            $user->status = $request->get('edit_user_status');
            $user->updated_by = Auth::id();

            $remove_role = ModelHasRole::where('model_id','=', $id)->delete();
            $update_role = new ModelHasRole();
            $update_role->role_id = $request->get('edit_user_role');
            $update_role->model_type = 'App\User';
            $update_role->model_id = $id;
            

            $duplicate = User::where('email','=', $user->email)->where('id','!=', $id)->first();
            
            if(is_null($duplicate)){

                $user->update();
                $update_role->save();
                DB::commit();

                $action = 'Updated user - '.$user->name;
                $primary_id = $user->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success', $user->name.' updated successfully.');

            }else{
                return redirect()->back()->with('failed', $user->email.' already exists.');
            }

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function resetUserPassword(Request $request)
    {
        $request->validate([
            'reset_password_user_id' => 'bail|required|numeric'
        ]);
        
        $id = $request->get('reset_password_user_id');

        try{
            $randomString = Str::random(8);

            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->password = Hash::make($randomString); 
            $user->updated_by = Auth::id();
            $user->update();
            DB::commit();

            // Send Mail Functions
            $recipient = User::where('id','=',$user->id)->first();
                
            $recipientEmail = $recipient->email;
            $recipientName = $recipient->name;
            $host = $request->getSchemeAndHttpHost();
            $link = $host.'/login';
            $note = 'Your password is now reset.';

            $data = array(
                'name' => $recipientName,
                'email' => $recipientEmail,
                'password' => $randomString,
                'note' => $note,
                'link' => $link
            );

            Mail::send('Admin.mail', $data, function($message) use ($recipientEmail,$recipientName) {
                $message->to($recipientEmail,$recipientName)
                        ->subject('Reset Password - Amadeus Marine Ltd. Drawing Database');
                $message->from('amadeus@motolite.com','Amadeus Drawing Database Support'); // Email should be change
            });

            $action = 'Reset password - '.$user->name;
            $primary_id = $user->id;
            $this->saveToDataLog($action, $primary_id);
            
            return redirect()->back()->with('success','Password of '.$recipient->name.' reset successfully. Account credentials sent to '.$recipient->email);
            
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }

    }
    #endregion Users Functions

    #region Roles Functions
    public function getPermissions($role_id)
    {
        $role_permissions = RoleHasPermission::leftJoin('permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->select('role_has_permissions.*','permissions.id AS permission_id', 'permissions.name AS permission', 'permissions.*')
            ->where('role_has_permissions.role_id', '=', $role_id)
            ->orderBy('permissions.name', 'asc')
            ->get();
        return \Response::json([ 'success' => true, 'permissions' => $role_permissions ]);
    }

    public function saveRole(Request $request)
    {
        $request->validate(['new_role_name' => 'bail|required|min:2',
                            'new_role_permissions' => 'bail|required']);

        try{
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request->get('new_role_name');
            $role->guard_name = 'web';
            $rolePermissions = $request->get('new_role_permissions');

            $duplicate = Role::where('name','=', $role->name)->first();
        
            if(is_null($duplicate)){

                $role->save();
                DB::commit();

                foreach($rolePermissions as $rolePermission)
                {
                    $roleHasPermission = new RoleHasPermission();
                    $roleHasPermission->permission_id = $rolePermission;
                    $roleHasPermission->role_id = $role->id;
                    $roleHasPermission->save();
                }

                $action = 'Added new role - '.$role->name;
                $primary_id = $role->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success','Role '.$role->name.' added successfully.');
            }else{
                return redirect()->back()->with('failed','Role '.$role->name.' already exists.');
            }

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }  
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'edit_role_id' => 'bail|required|numeric',
            'edit_role_name' => 'bail|required|min:2',
            'edit_role_permissions' => 'bail|required'
        ]);

        try{
            $id = $request->get('edit_role_id');

            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $role->name = $request->get('edit_role_name');
            $rolePermissions = $request->get('edit_role_permissions');

            $duplicate = Role::where('name','=', $role->name)->where('id','!=', $id)->first();

            if(is_null($duplicate)){
                $role->update();
                DB::commit();

                RoleHasPermission::where('role_id','=', $id)->delete();

                foreach($rolePermissions as $rolePermission){
                    $roleHasPermission = new RoleHasPermission();
                    $roleHasPermission->permission_id = $rolePermission;
                    $roleHasPermission->role_id = $role->id;
                    $roleHasPermission->save();
                }

                $action = 'Updated role - '.$role->name;
                $primary_id = $role->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success','Role '.$role->name.' updated successfully.');

            }else{
                return redirect()->back()->with('failed','Role '.$role->name.' already exists.');
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }
    #endregion Role Functions
    
    #region Start Permission Functions
    public function savePermission(Request $request)
    {
        $request->validate([
            'new_permission_name' => 'bail|required|string',
            'new_permission_description' => 'bail|required|string'
        ]);
        
        try{
            DB::beginTransaction();
            $permission = new Permission();
            $permission->name = $request->get('new_permission_name');
            $permission->description = $request->get('new_permission_description');
            $permission->guard_name = 'web';

            $duplicate = Permission::where('name','=', $permission->name)->first();

            if(is_null($duplicate))
            {
                $permission->save();
                DB::commit();

                $action = 'Added new permission - '.$permission->name;
                $primary_id = $permission->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success','Permission '.$permission->name.' added successfully.');
            }else{
                return redirect()->back()->with('failed','Permission '.$permission->name.' already exists.');
            }

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'edit_permission_id' => 'bail|required|numeric',
            'edit_permission_name' => 'bail|required|string',
            'edit_permission_description' => 'bail|required|string'
        ]);

        try{
            $id = $request->get('edit_permission_id');

            DB::beginTransaction();
            $permission = Permission::findOrFail($id);
            $permission->name = $request->get('edit_permission_name');
            $permission->description = $request->get('edit_permission_description');

            $duplicate = Permission::where('name','=',$permission->name)->where('id','!=',$id)->first();

            if(is_null($duplicate))
            {
                $permission->update();
                DB::commit();

                $action = 'Updated permission - '.$permission->name;
                $primary_id = $permission->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success','Permission '.$permission->name.' updated successfully.');
            }else{
                return redirect()->back()->with('failed','Permission '.$permission->name.' already exists.');
            }

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }
    #endregion Permission Functions

    public function changeMyPassword(Request $request)
    {
        $request->validate([
            'my_current_password' => 'bail|required|string|min:8',
            'my_new_password' => 'bail|required|string|min:8',
            'confirm_new_password' => 'bail|required|string|min:8']);
        
        try{
            DB::beginTransaction();
            $user = User::findOrFail(Auth::id());
            $currentPassword = $request->get('my_current_password');

            if(!Hash::check($currentPassword,$user->password)){
                
                return redirect()->back()->with('failed','Your current password does not match in our records.');
            
            }else{

                if( $request->get('my_new_password') != $request->get('confirm_new_password')) {

                    return redirect()->back()->with('failed','Confirmation of new password does not match.');
    
                }else{
                    $user->password = Hash::make($request->get('my_new_password'));
                    $user->update();
                    DB::commit();

                    $action = 'Password changed.';
                    $primary_id = $user->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Your password updated successfully.');
                }
            }


        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function saveToDataLog( $action, $primary_id )
    {
        $log = new Zdatalog;
        $log->action = $action;
        $log->primary_id = $primary_id;
        $log->user_id = Auth::id();
        $log->created_at = Carbon::now();
        $log->save();
    }
}
