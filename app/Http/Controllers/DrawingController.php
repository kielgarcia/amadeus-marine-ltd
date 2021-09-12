<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Drawing;
use App\Hull;
use App\User;
use App\RevisionHistory;
use App\Zdatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use File;
use DataTables;

class DrawingController extends Controller
{
    public function __construct()
    {
       $this->middleware('permission:viewDrawings',[ 'only' => 'index']);
       $this->middleware('permission:saveDrawing',[ 'only' => 'saveSbDrawing']);
       $this->middleware('permission:updateDrawing',[ 'only' => 'updateSbDrawing']);
       $this->middleware('permission:uploadDrawingRevision',[ 'only' => 'uploadSbDrawingRevision']);
       $this->middleware('permission:deleteDrawingCertificate',[ 'only' => 'deleteDrawing']);
       $this->middleware('permission:saveWipDrawing',[ 'only' => 'saveWipDrawing']);
       $this->middleware('permission:updateWipDrawing',[ 'only' => 'updateWipDrawing']);
       $this->middleware('permission:finalizeWipDrawing',[ 'only' => 'finalizeWipDrawing']);

       $this->middleware('permission:viewCertificates',[ 'only' => 'viewCertificate']);
       $this->middleware('permission:saveCertificate',[ 'only' => 'saveCertificate']);
       $this->middleware('permission:updateCertificate',[ 'only' => 'updateCertificate']);
    }

    // View drawing main index function 
    public function index()
    {
        $sb_hulls = Hull::leftJoin('users','users.id','=','hulls.created_by')
            ->leftJoin('drawings',function($join) {
            $join->on('drawings.hull_id','=','hulls.id');
            $join->where('drawings.deleted_at','=',NULL);
            })->select('hulls.id',
                'hulls.hull_no', 
                'hulls.hull_description', 
                'hulls.updated_at', 
                'users.name as created_by',
                DB::raw('sum(drawings.type = "Drawing") as total_drawings'),
                DB::raw('sum(drawings.type = "WIP") as total_wip'))
            ->where('hulls.deleted_at','=',null)
            ->groupBy('hulls.id', 'hulls.hull_no', 'hulls.hull_description','hulls.updated_at','users.name')
            ->get();

        $wip_drawings = Drawing::leftJoin('hulls','hulls.id','=','drawings.hull_id')
            ->leftJoin('users','users.id','=','drawings.uploaded_by')
            ->select('drawings.*',
                    'hulls.hull_no as hull_no',
                    'users.name as uploader')
            ->where('drawings.type','=','WIP')
            ->where('drawings.deleted_at','=',null)
            ->get();

        return view('Drawing.index',compact('sb_hulls','wip_drawings'));
    }

    public function viewCertificate()
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
                DB::raw('sum(drawings.type = "Certificate") as total_certificates'))
            ->where('hulls.deleted_at','=',null)
            ->groupBy('hulls.id', 'hulls.hull_no', 'hulls.hull_description','hulls.updated_at','users.name')
            ->get();

            return view('Certificate.index',compact('hulls'));
    }

    public function getHullCertificates($selectedCertHullId, Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Drawing::select('count(*) as allcount')->where('hull_id','=',$selectedCertHullId)->where('drawings.type','=','Certificate')->where('drawings.deleted_at','=',null)->count();
        $totalRecordswithFilter = Drawing::select('count(*) as allcount')->where('drawing_no', 'like', '%' .$searchValue . '%')->where('hull_id','=',$selectedCertHullId)->where('drawings.type','=','Certificate')->where('drawings.deleted_at','=',null)->count();

        // Fetch records
        $records = Drawing::leftJoin('users','users.id','=','drawings.uploaded_by')
            ->orderBy($columnName,$columnSortOrder)
            ->where('drawings.drawing_no', 'like', '%' .$searchValue . '%')
            ->select('drawings.*','users.name as uploaded_by')
            ->where('drawings.hull_id','=',$selectedCertHullId)
            ->where('drawings.type','=','Certificate')
            ->where('drawings.deleted_at','=',null)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        
        foreach($records as $record){
           $id = $record->id;
           $hull_id = $record->hull_id;
           $drawing_no = $record->drawing_no;
           $drawing_title = $record->drawing_title;
           $revision_no = $record->revision_no;
           $pdf = $record->pdf;
           $dwf = $record->dwf;
           $dwg = $record->dwg;
           $date_published = $record->date_published;
           $uploaded_by = $record->uploaded_by;

           $data_arr[] = array(
             "id" => $id,
             "hull_id" => $hull_id,
             "drawing_no" => $drawing_no,
             "drawing_title" => $drawing_title,
             "revision_no" => $revision_no,
             "pdf" => $pdf,
             "dwf" => $dwf,
             "dwg" => $dwg,
             "date_published" => $date_published,
             "uploaded_by" => $uploaded_by
           );
        }

        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function getSbHullDrawings($selectedSbHullId, Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Drawing::select('count(*) as allcount')->where('hull_id','=',$selectedSbHullId)->where('drawings.type','=','Drawing')->where('drawings.deleted_at','=',null)->count();
        $totalRecordswithFilter = Drawing::select('count(*) as allcount')->where('drawing_no', 'like', '%' .$searchValue . '%')->where('hull_id','=',$selectedSbHullId)->where('drawings.type','=','Drawing')->where('drawings.deleted_at','=',null)->count();

        // Fetch records
        $records = Drawing::leftJoin('users','users.id','=','drawings.uploaded_by')
            ->orderBy($columnName,$columnSortOrder)
            ->where('drawings.drawing_no', 'like', '%' .$searchValue . '%')
            ->select('drawings.*','users.name as uploaded_by')
            ->where('drawings.hull_id','=',$selectedSbHullId)
            ->where('drawings.type','=','Drawing')
            ->where('drawings.deleted_at','=',null)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        
        foreach($records as $record){
           $id = $record->id;
           $hull_id = $record->hull_id;
           $drawing_no = $record->drawing_no;
           $drawing_title = $record->drawing_title;
           $revision_no = $record->revision_no;
           $pdf = $record->pdf;
           $dwf = $record->dwf;
           $dwg = $record->dwg;
           $date_published = $record->date_published;
           $uploaded_by = $record->uploaded_by;

           $data_arr[] = array(
             "id" => $id,
             "hull_id" => $hull_id,
             "drawing_no" => $drawing_no,
             "drawing_title" => $drawing_title,
             "revision_no" => $revision_no,
             "pdf" => $pdf,
             "dwf" => $dwf,
             "dwg" => $dwg,
             "date_published" => $date_published,
             "uploaded_by" => $uploaded_by
           );
        }

        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function downloadDrawings($filename)
    {
        $drawing = Drawing::where('pdf', $filename)->orWhere('dwf', $filename)->orWhere('dwg',$filename)->first();
        if( $drawing->type == "WIP" ){
            return response()->download(storage_path('/drawings/drafts/'.$filename));
        }elseif( $drawing->type == "Drawing" ){
            return response()->download(storage_path('/drawings/projects/'.$filename));
        }elseif( $drawing->type == "Certificate" ){
            return response()->download(storage_path('/drawings/certificates/'.$filename));
        }
    }

    public function downloadRevised($filename)
    {
        return response()->download(storage_path('/drawings/revised/'.$filename));
    }

    public function getRevisionHistory($drawingId)
    {
        $revision_history = RevisionHistory::join('users','users.id','=','revision_histories.uploaded_by')
                            ->select('users.*','revision_histories.*','revision_histories.pdf as pdf_file')
                            ->where('revision_histories.drawing_id',$drawingId)->get();

        return \Response::json(['success'=>true, 'revisionHistory'=>$revision_history]);
    }


    #region Ship Building Functions

        #region drawing functions
        public function saveSbDrawing(Request $request)
        {
            $request->validate([
                'new_sb_drawing_hull_id' => 'bail|required|numeric',
                'new_sb_drawing_no' => 'bail|required|string',
                'new_sb_drawing_title' => 'bail|required|string',
                'new_sb_drawing_pdf' => 'bail|required|file',
                'new_sb_drawing_dwf' => 'bail|required|file',
                'new_sb_drawing_dwg' => 'bail|required|file'
            ]);

            try{
                DB::beginTransaction();
                $sb_drawing = new Drawing;
                $sb_drawing->hull_id = $request->get('new_sb_drawing_hull_id');
                $sb_drawing->drawing_no = $request->get('new_sb_drawing_no');
                $sb_drawing->drawing_title = $request->get('new_sb_drawing_title');
                $sb_drawing->type = 'Drawing';
                $sb_drawing->revision_no = '0';
                $sb_drawing->date_published = Carbon::now();
                $sb_drawing->uploaded_by = Auth::id();

                // PDF
                $drawing_pdf = $request->file('new_sb_drawing_pdf');
                $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                $if_pdf = $drawing_pdf->getClientOriginalExtension();

                // DWF
                $drawing_dwf = $request->file('new_sb_drawing_dwf');
                $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                $if_dwf = $drawing_dwf->getClientOriginalExtension();

                // DWG
                $drawing_dwg = $request->file('new_sb_drawing_dwg');
                $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                $if_dwg = $drawing_dwg->getClientOriginalExtension();

                $sb_hull = Hull::where('id','=',$sb_drawing->hull_id)->first();

                $duplicate = Drawing::where('hull_id','=',$sb_drawing->hull_id)->where('drawing_no','=',$sb_drawing->drawing_no)->where('deleted_at','=',null)->first();

                if(is_null($duplicate))
                {
                    if( $if_pdf == 'pdf' && $if_dwf == 'dwf' && $if_dwg == 'dwg' ) 
                    {
                        $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$pdf_original_filename;
                        $drawing_pdf->move(storage_path().'/drawings/projects/', $pdf_dd_filename);
                        $sb_drawing->pdf = $pdf_dd_filename;
                    
                        $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwf_original_filename;
                        $drawing_dwf->move(storage_path().'/drawings/projects/', $dwf_dd_filename);
                        $sb_drawing->dwf = $dwf_dd_filename;
        
                        
                        $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwg_original_filename;
                        $drawing_dwg->move(storage_path().'/drawings/projects/', $dwg_dd_filename);
                        $sb_drawing->dwg = $dwg_dd_filename;
        
                        $sb_drawing->save();
                        DB::commit();

                        $action = 'Added new drawing '.$sb_drawing->drawing_no.' in hull - '.$sb_hull->hull_no;
                        $primary_id = $sb_drawing->id;
                        $this->saveToDataLog($action, $primary_id);

                        return redirect()->back()->with('success','Drawing '.$sb_drawing->drawing_no.' added successfully in '.$sb_hull->hull_no.'.');
        
                    }else{
                        return redirect()->back()->with('failed','File to upload must be a PDF, DWF and DWG file.');
                    }
                }else{
                    return redirect()->back()->with('failed','Drawing no.'.$sb_drawing->drawing_no.' are already exists in '.$sb_hull->hull_no.'.');
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function updateSbDrawing(Request $request)
        {
            $request->validate([
                'edit_sb_drawing_id' => 'bail|required|numeric',
                'edit_sb_drawing_no' => 'bail|required|string',
                'edit_sb_drawing_title' => 'bail|required|string',
                'edit_sb_drawing_pdf' => 'bail|nullable|file',
                'edit_sb_drawing_dwf' => 'bail|nullable|file',
                'edit_sb_drawing_dwg' => 'bail|nullable|file'
            ]);

            $id = $request->get('edit_sb_drawing_id');
            $hasFileToUpdate = $request->get('view_edit_sb_drawing_file_cb');

            try{
                DB::beginTransaction();
                $sb_drawing = Drawing::findOrFail($id);
                $sb_drawing->drawing_no = $request->get('edit_sb_drawing_no');
                $sb_drawing->drawing_title = $request->get('edit_sb_drawing_title');
                $sb_drawing->updated_by = Auth::id();

                $duplicate = Drawing::where('id','!=',$id)->where('hull_id','=',$sb_drawing->hull_id)->where('drawing_no','=',$sb_drawing->drawing_no)->where('deleted_at','=',null)->first();
                
                

                if(is_null($duplicate)){
                    $sb_hull = Hull::where('id','=',$sb_drawing->hull_id)->first();

                    if( $hasFileToUpdate == 1 ){
                        // Save PDF
                        $drawing_pdf = $request->file('edit_sb_drawing_pdf');
                        $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                        $if_pdf = $drawing_pdf->getClientOriginalExtension();

                        // Save DWF
                        $drawing_dwf = $request->file('edit_sb_drawing_dwf');
                        $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                        $if_dwf = $drawing_dwf->getClientOriginalExtension();

                        // Save DWG
                        $drawing_dwg = $request->file('edit_sb_drawing_dwg');
                        $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                        $if_dwg = $drawing_dwg->getClientOriginalExtension();

                        if( $if_pdf != 'pdf' || $if_dwf != 'dwf' || $if_dwg != 'dwg' ) {

                            return redirect()->back()->with('failed','File to upload must be a PDF, DWF and DWG file.');

                        }else{

                            // Delete existing file
                            $pdf_file = storage_path().'/drawings/projects/'. $sb_drawing->pdf;
                            $dwf_file = storage_path().'/drawings/projects/'. $sb_drawing->dwf;
                            $dwg_file = storage_path().'/drawings/projects/'. $sb_drawing->dwg;
                            $delete = File::delete($pdf_file, $dwf_file, $dwg_file);
                            // End delete

                            $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$pdf_original_filename;
                            $drawing_pdf->move(storage_path().'/drawings/projects/', $pdf_dd_filename);
                            $sb_drawing->pdf = $pdf_dd_filename;

                            $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwf_original_filename;
                            $drawing_dwf->move(storage_path().'/drawings/projects/', $dwf_dd_filename);
                            $sb_drawing->dwf = $dwf_dd_filename;
                            
                            $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwg_original_filename;
                            $drawing_dwg->move(storage_path().'/drawings/projects/', $dwg_dd_filename);
                            $sb_drawing->dwg = $dwg_dd_filename;

                            $sb_drawing->date_published = Carbon::now();

                            $action = 'Updated a drawing files in '.$sb_drawing->drawing_no.' in hull - '.$sb_hull->hull_no;
                            $primary_id = $sb_drawing->id;
                            $this->saveToDataLog($action, $primary_id);
                        }
                    }
                    
                    $sb_drawing->update();
                    DB::commit();

                    $action = 'Updated a drawing '.$sb_drawing->drawing_no.' in hull - '.$sb_hull->hull_no;
                    $primary_id = $sb_drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Drawing '.$sb_drawing->drawing_no.' in hull '.$sb_hull->hull_no.' updated successfully.');
                }else{
                    $sb_hull = Hull::where('id','=',$duplicate->hull_id)->first();

                    return redirect()->back()->with('failed','Drawing no. '.$sb_drawing->drawing_no.' already exists in hull '.$sb_hull->hull_no);
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function uploadSbDrawingRevision(Request $request)
        {
            $request->validate([
                'new_revision_sb_drawing_id' => 'bail|required|numeric',
                'new_revision_sb_drawing_pdf' => 'bail|required|file',
                'new_revision_sb_drawing_dwf' => 'bail|required|file',
                'new_revision_sb_drawing_dwg' => 'bail|required|file'
            ]);

            $id = $request->get('new_revision_sb_drawing_id');

            try{
                DB::beginTransaction();
                $sb_drawing = Drawing::findOrFail($id);

                // Save PDF
                $drawing_pdf = $request->file('new_revision_sb_drawing_pdf');
                $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                $if_pdf = $drawing_pdf->getClientOriginalExtension();

                // Save DWF
                $drawing_dwf = $request->file('new_revision_sb_drawing_dwf');
                $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                $if_dwf = $drawing_dwf->getClientOriginalExtension();

                // Save DWG
                $drawing_dwg = $request->file('new_revision_sb_drawing_dwg');
                $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                $if_dwg = $drawing_dwg->getClientOriginalExtension();

                if( $if_pdf != 'pdf' || $if_dwf != 'dwf' || $if_dwg != 'dwg' ) {
                    return redirect()->back()->with('failed','File to upload must be a PDF, DWF and DWG file.');
                }else{

                    $old_path1 = storage_path().'/drawings/projects/'. $sb_drawing->pdf;
                    $old_path2 = storage_path().'/drawings/projects/'. $sb_drawing->dwf;
                    $old_path3 = storage_path().'/drawings/projects/'. $sb_drawing->dwg;

                    $new_path1 = storage_path().'/drawings/revised/'. $sb_drawing->pdf;
                    $new_path2 = storage_path().'/drawings/revised/'. $sb_drawing->dwf;
                    $new_path3 = storage_path().'/drawings/revised/'. $sb_drawing->dwg;

                    $move = File::move($old_path1, $new_path1);
                    $move = File::move($old_path2, $new_path2);
                    $move = File::move($old_path3, $new_path3);
                    $delete = File::delete($old_path1, $old_path2, $old_path3);

                    $revision_history = new RevisionHistory();
                    $revision_history->drawing_id = $id;
                    $revision_history->revision_no = $sb_drawing->revision_no;
                    $revision_history->uploaded_by = $sb_drawing->uploaded_by;
                    $revision_history->date_published = $sb_drawing->date_published;
                    $revision_history->pdf = $sb_drawing->pdf;
                    $revision_history->dwf = $sb_drawing->dwf;
                    $revision_history->dwg = $sb_drawing->dwg;
                    $revision_history->save();

                    $sb_drawing->uploaded_by = Auth::id();
                    $sb_drawing->date_published = Carbon::now();
                    $sb_drawing->revision_no = $sb_drawing->revision_no + 1;

                    $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$pdf_original_filename;
                    $drawing_pdf->move(storage_path().'/drawings/projects/', $pdf_dd_filename);
                    $sb_drawing->pdf = $pdf_dd_filename;
                
                    $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwf_original_filename;
                    $drawing_dwf->move(storage_path().'/drawings/projects/', $dwf_dd_filename);
                    $sb_drawing->dwf = $dwf_dd_filename;
                    
                    $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$sb_drawing->revision_no."-".$dwg_original_filename;
                    $drawing_dwg->move(storage_path().'/drawings/projects/', $dwg_dd_filename);
                    $sb_drawing->dwg = $dwg_dd_filename;

                    
                    $sb_drawing->update();
                    
                    DB::commit();

                    $sb_hull = Hull::where('id','=',$sb_drawing->hull_id)->first();
                    
                    $action = 'Uploaded a revision in drawing '.$sb_drawing->drawing_no.' - hull - '.$sb_hull->hull_no;
                    $primary_id = $sb_drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success',$sb_hull->hull_no.' - '.$sb_drawing->drawing_no.' revision uploaded successfully.');
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function deleteDrawing(Request $request)
        {
            $request->validate(['delete_drawing_id' => 'bail|required|numeric']);

            $id = $request->get('delete_drawing_id');

            try{
                DB::beginTransaction();
                $drawing = Drawing::findOrFail($id);
                $drawing->deleted_by = Auth::id();
                $hull = Hull::where('id','=',$drawing->hull_id)->first();

                if($drawing->type == 'WIP') {
                    $dwf_file = storage_path().'/drawings/drafts/'. $drawing->dwf;
                    $dwg_file = storage_path().'/drawings/drafts/'. $drawing->dwg;
        
                    $delete = File::delete($dwf_file, $dwg_file);
        
                    $drawing->update();
                    $drawing->delete();
                    DB::commit();

                    $action = 'Deleted a work in progress drawing '.$drawing->drawing_no.' - hull - '.$hull->hull_no;
                    $primary_id = $drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Work in progress drawing '. $drawing->drawing_no.'  deleted successfully from hull '.$hull->hull_no);

                }elseif($drawing->type == 'Drawing'){

                    $pdf_file = storage_path().'/drawings/projects/'. $drawing->pdf;
                    $dwf_file = storage_path().'/drawings/projects/'. $drawing->dwf;
                    $dwg_file = storage_path().'/drawings/projects/'. $drawing->dwg;
        
                    $delete = File::delete($pdf_file, $dwf_file, $dwg_file);
        
                    $drawing->update();
                    $drawing->delete();
                    DB::commit();

                    $action = 'Deleted a drawing '.$drawing->drawing_no.' - hull - '.$hull->hull_no;
                    $primary_id = $drawing->id;
                    $this->saveToDataLog($action, $primary_id);
                    
                    return redirect()->back()->with('success','Drawing '. $drawing->drawing_no.'  deleted successfully from hull'.$hull->hull_no);

                }elseif($drawing->type == 'Certificate'){

                    $pdf_file = storage_path().'/drawings/certificates/'. $drawing->pdf;
        
                    $delete = File::delete($pdf_file);
        
                    $drawing->update();
                    $drawing->delete();
                    DB::commit();

                    $action = 'Deleted a certificate '.$drawing->drawing_no.' - hull - '.$hull->hull_no;
                    $primary_id = $drawing->id;
                    $this->saveToDataLog($action, $primary_id);
                    
                    return redirect()->back()->with('success','Certificate '. $drawing->drawing_no.'  deleted successfully from hull'.$hull->hull_no);
                }
                
            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }
        #endregion

        #region Work In Progress Functions 
        public function saveWipDrawing(Request $request)
        {
            $request->validate([
                'new_wip_drawing_hull_no' => 'bail|required|numeric',
                'new_wip_drawing_no' => 'bail|required|string',
                'new_wip_drawing_title' => 'bail|required|string',
                'new_wip_drawing_dwf' => 'bail|nullable|file',
                'new_wip_drawing_dwg' => 'bail|nullable|file',

            ]);

            try{
                DB::beginTransaction();
                $wip_drawing = new Drawing;
                $wip_drawing->hull_id = $request->get('new_wip_drawing_hull_no');
                $wip_drawing->drawing_no = $request->get('new_wip_drawing_no');
                $wip_drawing->drawing_title = $request->get('new_wip_drawing_title');
                $wip_drawing->type = 'WIP';
                $wip_drawing->revision_no = '0';
                $wip_drawing->date_published = Carbon::now();
                $wip_drawing->uploaded_by = Auth::id();

                $duplicate = Drawing::where('hull_id','=',$wip_drawing->hull_id)->where('drawing_no','=',$wip_drawing->drawing_no)->where('deleted_at','=',null)->first();
                $wip_hull = Hull::where('id','=',$wip_drawing->hull_id)->first();
                
                if(is_null($duplicate))
                {
                    // DWF
                    if($request->hasFile('new_wip_drawing_dwf')){
                        $drawing_dwf = $request->file('new_wip_drawing_dwf');
                        $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                        $if_dwf = $drawing_dwf->getClientOriginalExtension();

                        if( $if_dwf == 'dwf' ){
                            $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-WIP-".$dwf_original_filename;
                            $drawing_dwf->move(storage_path().'/drawings/drafts/', $dwf_dd_filename);
                            $wip_drawing->dwf = $dwf_dd_filename;
                        }else{
                            return redirect()->back()->with('failed','File to upload must be a DWF or DWG file.');
                        }
                    }
                    

                    // DWG
                    if($request->hasFile('new_wip_drawing_dwg')){
                        $drawing_dwg = $request->file('new_wip_drawing_dwg');
                        $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                        $if_dwg = $drawing_dwg->getClientOriginalExtension();

                        if( $if_dwg == 'dwg' ){
                            $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-WIP-".$dwg_original_filename;
                            $drawing_dwg->move(storage_path().'/drawings/drafts/', $dwg_dd_filename);
                            $wip_drawing->dwg = $dwg_dd_filename;
                        }else{
                            return redirect()->back()->with('failed','File to upload must be a DWF or DWG file.');
                        }
                    }
        
                    $wip_drawing->save();
                    DB::commit();

                    $action = 'Added a new work in work in progress drawing '.$wip_drawing->drawing_no.' in hull - '.$wip_hull->hull_no;
                    $primary_id = $wip_drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Drawing '.$wip_drawing->drawing_no.' added successfully in hull '.$wip_hull->hull_no.'.');
           
                }else{
                    return redirect()->back()->with('failed','Drawing no.'.$wip_drawing->drawing_no.' are already exists in hull '.$wip_hull->hull_no.'.');
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function updateWipDrawing(Request $request)
        {
            $request->validate([
                'edit_wip_drawing_id' => 'bail|required|numeric',
                'edit_wip_drawing_hull_no' => 'bail|required|numeric',
                'edit_wip_drawing_no' => 'bail|required|string',
                'edit_wip_drawing_title' => 'bail|required|string',
                'edit_wip_drawing_pdf' => 'bail|nullable|file',
                'edit_wip_drawing_dwf' => 'bail|nullable|file',
                'edit_wip_drawing_dwg' => 'bail|nullable|file'
            ]);

            $id = $request->get('edit_wip_drawing_id');
            $hasFileToUpdate = $request->get('view_edit_wip_drawing_file_cb');

            try{
                DB::beginTransaction();
                $wip_drawing = Drawing::findOrFail($id);
                $wip_drawing->hull_id = $request->get('edit_wip_drawing_hull_no');
                $wip_drawing->drawing_no = $request->get('edit_wip_drawing_no');
                $wip_drawing->drawing_title = $request->get('edit_wip_drawing_title');
                $wip_drawing->updated_by = Auth::id();

                $duplicate = Drawing::where('id','!=',$id)->where('hull_id','=',$wip_drawing->hull_id)->where('drawing_no','=',$wip_drawing->drawing_no)->where('deleted_at','=',null)->first();
                
                if(is_null($duplicate)){
                    $sb_hull = Hull::where('id','=',$wip_drawing->hull_id)->first();

                    if( $hasFileToUpdate == 1 ){

                        $forDeleteDwf = $wip_drawing->dwf;
                        $forDeleteDwg = $wip_drawing->dwg;

                        $wip_drawing->dwf = null;
                        $wip_drawing->dwg = null;


                        if(($request->hasFile('edit_wip_drawing_dwf')) && ($request->hasFile('edit_wip_drawing_dwg'))){ //DWF and DWG
                            $drawing_dwf = $request->file('edit_wip_drawing_dwf');
                            $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                            $if_dwf = $drawing_dwf->getClientOriginalExtension();

                            $drawing_dwg = $request->file('edit_wip_drawing_dwg');
                            $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                            $if_dwg = $drawing_dwg->getClientOriginalExtension();
                        
                            if(($if_dwf == 'dwf') && ($if_dwg == 'dwg')){
                                $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwf_original_filename;
                                $drawing_dwf->move(storage_path().'/drawings/drafts/', $dwf_dd_filename);

                                $wip_drawing->dwf = $dwf_dd_filename;

                                $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwg_original_filename;
                                $drawing_dwg->move(storage_path().'/drawings/drafts/', $dwg_dd_filename);

                                $wip_drawing->dwg = $dwg_dd_filename;
                                
                            }else{
                                return redirect()->back()->with('failed','File to upload must be a DWF or DWG file.');
                            }

                        }elseif($request->hasFile('edit_wip_drawing_dwf')){ // DWF Only
                            $drawing_dwf = $request->file('edit_wip_drawing_dwf');
                            $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                            $if_dwf = $drawing_dwf->getClientOriginalExtension();

                            if( $if_dwf == 'dwf' ){
                                $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwf_original_filename;
                                $drawing_dwf->move(storage_path().'/drawings/drafts/', $dwf_dd_filename);

                                $wip_drawing->dwf = $dwf_dd_filename;

                            }else{
                                return redirect()->back()->with('failed','File to upload must be a DWF or DWG file.');
                            }

                        }elseif($request->hasFile('edit_wip_drawing_dwg')){ // DWG Only
                            $drawing_dwg = $request->file('edit_wip_drawing_dwg');
                            $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                            $if_dwg = $drawing_dwg->getClientOriginalExtension();

                            if( $if_dwg == 'dwg' ){

                                $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwg_original_filename;
                                $drawing_dwg->move(storage_path().'/drawings/drafts/', $dwg_dd_filename);


                                $wip_drawing->dwg = $dwg_dd_filename;

                            }else{
                                return redirect()->back()->with('failed','File to upload must be a DWF or DWG file.');
                            }  
                        }

                        if(!is_null($forDeleteDwf) && !is_null($forDeleteDwg)){

                            $dwf_file = storage_path().'/drawings/drafts/'.$forDeleteDwf;
                            $dwg_file = storage_path().'/drawings/drafts/'.$forDeleteDwg;
                            $delete = File::delete($dwf_file,$dwg_file);

                        }elseif(!is_null($forDeleteDwf) && is_null($forDeleteDwg)){
                            $dwf_file = storage_path().'/drawings/drafts/'.$forDeleteDwf;
                            $delete = File::delete($dwf_file);

                        }elseif(is_null($forDeleteDwf) && !is_null($forDeleteDwg)){
                            $dwg_file = storage_path().'/drawings/drafts/'.$forDeleteDwg;
                            $delete = File::delete($dwg_file);
                        }

                        $wip_drawing->uploaded_by = Auth::id();
                        $wip_drawing->date_published = Carbon::now();

                        $action = 'Updated a work in progess drawing files '.$wip_drawing->drawing_no.' in hull - '.$sb_hull->hull_no;
                        $primary_id = $wip_drawing->id;
                        $this->saveToDataLog($action, $primary_id);
                    }

                    $wip_drawing->update();
                    DB::commit();



                    $action = 'Updated a work in progess drawing '.$wip_drawing->drawing_no.' in hull - '.$sb_hull->hull_no;
                    $primary_id = $wip_drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Work in progress drawing '.$wip_drawing->drawing_no.' in hull '.$sb_hull->hull_no.' updated successfully.');
                }else{
                    $sb_hull = Hull::where('id','=',$duplicate->hull_id)->first();

                    return redirect()->back()->with('failed','Drawing no. '.$wip_drawing->drawing_no.' already exists in hull '.$sb_hull->hull_no);
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function finalizeWipDrawing(Request $request)
        {
            $request->validate(['finalize_wip_drawing_id' => 'bail|required|numeric']);

            $id = $request->get('finalize_wip_drawing_id');

            try{
                DB::beginTransaction();
                $wip_drawing = Drawing::findOrFail($id);

                // Save PDF
                $drawing_pdf = $request->file('finalize_wip_drawing_pdf');
                $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                $if_pdf = $drawing_pdf->getClientOriginalExtension();

                // Save DWF
                $drawing_dwf = $request->file('finalize_wip_drawing_dwf');
                $dwf_original_filename = $drawing_dwf->getClientOriginalName();
                $if_dwf = $drawing_dwf->getClientOriginalExtension();

                // Save DWG
                $drawing_dwg = $request->file('finalize_wip_drawing_dwg');
                $dwg_original_filename = $drawing_dwg->getClientOriginalName();
                $if_dwg = $drawing_dwg->getClientOriginalExtension();

                if( $if_pdf != 'pdf' || $if_dwf != 'dwf' || $if_dwg != 'dwg' ) {
                    return redirect()->back()->with('failed','File to upload must be a PDF, DWF and DWG file.');
                }else{

                    if(!is_null($wip_drawing->dwf) && !is_null($wip_drawing->dwg)){

                        $wipDwf = storage_path().'/drawings/drafts/'. $wip_drawing->dwf;
                        $wipDwg = storage_path().'/drawings/drafts/'. $wip_drawing->dwg;
                        $delete = File::delete($wipDwf, $wipDwg);

                    }elseif(!is_null($wip_drawing->dwf) && is_null($wip_drawing->dwg)){

                        $wipDwf = storage_path().'/drawings/drafts/'. $wip_drawing->dwf;
                        $delete = File::delete($wipDwf);

                    }elseif(is_null($wip_drawing->dwf) && !is_null($wip_drawing->dwg)){

                        $wipDwg = storage_path().'/drawings/drafts/'. $wip_drawing->dwg;
                        $delete = File::delete($wipDwg);
                    }
                    
                    $wip_drawing->uploaded_by = Auth::id();
                    $wip_drawing->date_published = Carbon::now();

                    $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$pdf_original_filename;
                    $drawing_pdf->move(storage_path().'/drawings/projects/', $pdf_dd_filename);
                    $wip_drawing->pdf = $pdf_dd_filename;
                
                    $dwf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwf_original_filename;
                    $drawing_dwf->move(storage_path().'/drawings/projects/', $dwf_dd_filename);
                    $wip_drawing->dwf = $dwf_dd_filename;
                    
                    $dwg_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$wip_drawing->revision_no."-".$dwg_original_filename;
                    $drawing_dwg->move(storage_path().'/drawings/projects/', $dwg_dd_filename);
                    $wip_drawing->dwg = $dwg_dd_filename;

                    $wip_drawing->type = "Drawing";
                    $wip_drawing->update();
                    DB::commit();

                    $hull = Hull::where('id','=',$wip_drawing->hull_id)->first();
                    
                    $action = 'Finalized drawing '.$wip_drawing->drawing_no.' - hull - '.$hull->hull_no;
                    $primary_id = $wip_drawing->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Drawing '.$wip_drawing->drawing_no.' finalized successfully. Record moved to hull '.$hull->hull_no);
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }

        }
        #endregion
    #endregion

    #regiion Certificate Functions
        // Save Certificate Function
        public function saveCertificate(Request $request)
        {
            $request->validate([
                'new_certificate_hull_id' => 'bail|required|numeric',
                'new_certificate_no' => 'bail|required|string',
                'new_certificate_title' => 'bail|required|string',
                'new_certificate_pdf' => 'bail|required|file'
            ]);

            try{
                DB::beginTransaction();
                $certificate = new Drawing;
                $certificate->hull_id = $request->get('new_certificate_hull_id');
                $certificate->drawing_no = $request->get('new_certificate_no');
                $certificate->drawing_title = $request->get('new_certificate_title');
                $certificate->type = 'Certificate';
                $certificate->revision_no = '0';
                $certificate->date_published = Carbon::now();
                $certificate->uploaded_by = Auth::id();

                // PDF
                $drawing_pdf = $request->file('new_certificate_pdf');
                $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                $if_pdf = $drawing_pdf->getClientOriginalExtension();

                $hull = Hull::where('id','=',$certificate->hull_id)->first();

                $duplicate = Drawing::where('hull_id','=',$certificate->hull_id)->where('drawing_no','=',$certificate->drawing_no)->where('deleted_at','=',null)->first();

                if(is_null($duplicate))
                {
                    if($if_pdf == 'pdf') 
                    {
                        $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-".$certificate->revision_no."-".$pdf_original_filename;
                        $drawing_pdf->move(storage_path().'/drawings/certificates/', $pdf_dd_filename);
                        $certificate->pdf = $pdf_dd_filename;
                    
                        $certificate->save();
                        DB::commit();

                        $action = 'Added new certificates '.$certificate->drawing_no.' in hull - '.$hull->hull_no;
                        $primary_id = $certificate->id;
                        $this->saveToDataLog($action, $primary_id);

                        return redirect()->back()->with('success','Certificate '.$certificate->drawing_no.' added successfully in '.$hull->hull_no.'.');
        
                    }else{
                        return redirect()->back()->with('failed','File to upload must be a PDF file.');
                    }
                }else{
                    return redirect()->back()->with('failed','Certificate no.'.$certificate->drawing_no.' are already exists in '.$certificate->hull_no.'.');
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }

        public function updateCertificate(Request $request)
        {
            $request->validate([
                'edit_certificate_id' => 'bail|required|numeric',
                'edit_certificate_no' => 'bail|required|string',
                'edit_certificate_title' => 'bail|required|string',
                'edit_certificate_pdf' => 'bail|nullable|file'
            ]);

            $id = $request->get('edit_certificate_id');
            $hasFileToUpdate = $request->get('view_edit_certificate_file_cb');

            try{
                DB::beginTransaction();
                $certificate = Drawing::findOrFail($id);
                $certificate->drawing_no = $request->get('edit_certificate_no');
                $certificate->drawing_title = $request->get('edit_certificate_title');
                $certificate->updated_by = Auth::id();

                $duplicate = Drawing::where('id','!=',$id)->where('hull_id','=',$certificate->hull_id)->where('drawing_no','=',$certificate->drawing_no)->where('deleted_at','=',null)->first();
                
                $hull = Hull::where('id','=',$certificate->hull_id)->first();

                if(is_null($duplicate)){

                    if( $hasFileToUpdate == 1 ){
                        // Save PDF
                        $drawing_pdf = $request->file('edit_certificate_pdf');
                        $pdf_original_filename = $drawing_pdf->getClientOriginalName();
                        $if_pdf = $drawing_pdf->getClientOriginalExtension();

                        if( $if_pdf != 'pdf' ) {

                            return redirect()->back()->with('failed','File to upload must be a PDF file.');

                        }else{

                            // Delete existing file
                            $pdf_file = storage_path().'/drawings/certificates/'.$certificate->pdf;
                            $delete = File::delete($pdf_file);
                            // End delete

                            $pdf_dd_filename = Auth::id()."-".Carbon::now()->format('YmdHi')."-CERT-".$pdf_original_filename;
                            $drawing_pdf->move(storage_path().'/drawings/certificates/', $pdf_dd_filename);
                            $certificate->pdf = $pdf_dd_filename;

                            $certificate->date_published = Carbon::now();

                            $action = 'Updated a certificate files in '.$certificate->drawing_no.' in hull - '.$hull->hull_no;
                            $primary_id = $certificate->id;
                            $this->saveToDataLog($action, $primary_id);
                        }
                    }
                    
                    $certificate->update();
                    DB::commit();

                    $action = 'Updated a certificate '.$certificate->drawing_no.' in hull - '.$hull->hull_no;
                    $primary_id = $certificate->id;
                    $this->saveToDataLog($action, $primary_id);

                    return redirect()->back()->with('success','Certificate '.$certificate->drawing_no.' in hull '.$hull->hull_no.' updated successfully.');
                }else{
                    return redirect()->back()->with('failed','Certificate no. '.$certificate->drawing_no.' already exists in hull '.$hull->hull_no);
                }

            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('failed', "Something went wrong! ".$e->getMessage());
            }
        }
    #endregion

    // Save to Activity Log Function
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
