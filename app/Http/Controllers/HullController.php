<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Drawing;
use App\Hull;
use App\User;
use App\Repository;
use App\Zdatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HullController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:viewHulls',[ 'only' => 'index']);
        $this->middleware('permission:saveHull',[ 'only' => 'saveSbHull']);
        $this->middleware('permission:updateHull',[ 'only' => 'updateSbHull']);
        $this->middleware('permission:deleteHull',[ 'only' => 'deleteSbHull']);
    }

    #region hull functions
    public function index()
    {
        $hulls = Hull::leftJoin('users','users.id','=','hulls.created_by')
            ->leftJoin('drawings',function($join) {
            $join->on('drawings.hull_id','=','hulls.id');
            $join->where('drawings.deleted_at','=',NULL);
            })->select('hulls.id',
                'hulls.hull_no', 
                'hulls.hull_description', 
                'hulls.updated_at', 
                'users.name as created_by',
                DB::raw('sum(drawings.type = "Drawing") as total_drawings'),
                DB::raw('sum(drawings.type = "WIP") as total_wip'),
                DB::raw('sum(drawings.type = "Certificate") as total_certificates'))
            ->where('hulls.deleted_at','=',null)
            ->groupBy('hulls.id', 'hulls.hull_no', 'hulls.hull_description','hulls.updated_at','users.name')
            ->get();

        return view('Hull.index',compact('hulls'));
    }

    public function saveSbHull(Request $request) 
    {
        $request->validate([
            'new_hull_no' => 'bail|required|string',
            'new_hull_description' => 'bail|required|string'
        ]);

        try{
            DB::beginTransaction();
            $sb_hull = new Hull;
            $sb_hull->hull_no = $request->get('new_hull_no');
            $sb_hull->hull_description = $request->get('new_hull_description');
            $sb_hull->created_by = Auth::id();

            $duplicate = Hull::where('hull_no','=',$sb_hull->hull_no)->where('deleted_at','=',null)->first();

            if(is_null($duplicate))
            {
                $sb_hull->save();
                DB::commit();

                $action = 'Added a new hull - '.$sb_hull->hull_no;
                $primary_id = $sb_hull->id;
                $this->saveToDataLog($action, $primary_id);


                return redirect()->back()->with('success','New hull '.$sb_hull->hull_no.' added successfully.');
            }else{
                return redirect()->back()->with('failed','Hull '.$sb_hull->hull_no.' are already exists.');
            }
            
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function deleteSbHull(Request $request)
    {
        $request->validate(['delete_hull_id' => 'bail|required|numeric']);

        $id = $request->get('delete_hull_id');

        try{
            DB::beginTransaction();
            $sb_hull = Hull::findOrFail($id);
            $sb_hull->deleted_by = Auth::id();
            $sb_hull->update();
            $sb_hull->delete();
            DB::commit();

            $action = 'Deleted a hull - '.$sb_hull->hull_no;
            $primary_id = $sb_hull->id;
            $this->saveToDataLog($action, $primary_id);

            return redirect()->back()->with('success','Ship building hull '.$sb_hull->hull_no.' deleted successfully.');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }

    public function updateSbHull(Request $request)
    {
        $request->validate([
            'edit_hull_id' => 'bail|required|numeric',
            'edit_hull_no' => 'bail|required|string',
            'edit_hull_description' => 'bail|required|string'
        ]);

        $id = $request->get('edit_hull_id');

        try{ 
            DB::beginTransaction();
            $sb_hull = Hull::findOrFail($id);
            $sb_hull->hull_no = $request->get('edit_hull_no');
            $sb_hull->hull_description = $request->get('edit_hull_description');
            $sb_hull->updated_by = Auth::id();

            $duplicate = Hull::where('hull_no','=',$sb_hull->hull_no)->where('id','!=',$id)->where('deleted_at','=',null)->first();

            if(is_null($duplicate)){
                $sb_hull->update();
                DB::commit();

                $action = 'Updated a hull -'.$sb_hull->hull_no;
                $primary_id = $sb_hull->id;
                $this->saveToDataLog($action, $primary_id);

                return redirect()->back()->with('success','Ship building hull '.$sb_hull->hull_no.' updated successfully.');
            }else{
                return redirect()->back()->with('failed','Ship building hull '.$sb_hull->hull_no.' are already exists.');
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
        }
    }
    #endregion

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
