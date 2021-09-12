<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="row">
                <h5 class="col-sm-6">Hulls</h5>
                
                <div class="col-sm-6">
                    <form action="{{ route('viewHulls') }}" method="GET">
                        {{ csrf_field() }} 
                        <button  type="submit" class="btn btn-primary" title="Manage Hull" style="float:right;">Manage Hull</button>
                    </form>
                </div>
            
            </div>
        </div>

        <div class="card-body">
            <table id="ship_bldg_hulls_tbl" class="table-hover table responsive" style="width:100%; font-size:12px;">
                <thead style="display: table; width: 100%; table-layout:fixed;">
                    <tr>
                        <th class="all">Hull No.</th>
                        <th>Description</th>
                        <th style="text-align:center;">WIP/Total<br>Drawing(s)</th>
                        <th>Created by</th>
                        <th>Last Updated</th>
                        <!-- <th class="all"></th> -->
                    </tr>
                </thead>

                <tbody  id="ship_bldg_hulls_tbl_body" class="table-body" style="display: block; height: 300px; overflow: auto;">
                @foreach($sb_hulls as $hull)
                    <tr style="display: table; width: 100%; table-layout: fixed;">
                        <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}">
                            {{$hull->hull_no}}
                        </td>
                        <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}">
                            {{$hull->hull_description}}
                        </td>

                        <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}" style="text-align:center;">
                        @if( $hull->total_wip == null && $hull->total_drawings == null )
                            0/0
                         @else
                            {{$hull->total_wip}}/{{$hull->total_drawings}}
                        @endif
                        </td>

                        <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}">
                            {{$hull->created_by}}
                        </td>

                        <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}">
                            {{$hull->updated_at}}
                        </td>

                        <!-- <td class="view_drawings" data-selected_sb_hull_id="{{$hull->id}}" data-selected_sb_hull_no="{{$hull->hull_no}}" style="text-align:right">
                            <div class="btn-group">
                                <button class="edit_sb_hull_btn btn" title="Edit Hull"
                                    data-edit_sb_hull_id="{{$hull->id}}"
                                    data-edit_sb_hull_no="{{$hull->hull_no}}"
                                    data-edit_sb_hull_description="{{$hull->hull_description}}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                @if( $hull->total_drawings == null && $hull->total_wip == null)
                                    <button class="delete_sb_hull_btn btn" title="Delete Hull"
                                        data-delete_sb_hull_id="{{$hull->id}}"
                                        data-delete_sb_hull_no="{{$hull->hull_no}}"
                                        data-delete_sb_hull_description="{{$hull->hull_description}}"
                                        data-delete_sb_hull_total_drawing="{{$hull->total_drawings}}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @else
                                    <button class="btn" disabled>
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td> -->
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <br>

    <div class="card shadow-md" id="sb_drawings_div">
        <div class="card-header">
            <div class="row">
                <h5 class="col-sm-6" id="selected_sb_hull_lbl"></h5>

                <div class="col-sm-6">
                    @can('saveDrawing')
                    <button id="new_sb_drawing_btn" class="btn btn-primary" style="float:right;" title="New Drawing">New Drawing</button>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="ship_bldg_drawings_tbl" class="table table-hover responsive" style="width:100%; font-size:12px;">
                <thead>
                    <tr>
                        <th class="all">Drawing No.</th>
                        <th>Drawing Title</th>
                        <th>Revision No.</th>
                        <th>Date Published</th>
                        <th>Uploaded by</th>
                        <th class="all">Download as</th>
                        <th class="all"></th>
                    </tr>
                </thead>

                <tbody id="ship_bldg_drawings_tbl_body">
               
                </tbody>
            </table>
        </div>
    </div>

    <!-- Start SB Hull Modals -->

    <!-- Start New SB Hull Modal -->
    <div class="modal fade" id="new_sb_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_sb_hull_form" action="{{ route('saveSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_sb_hull_no" name="new_sb_hull_no" placeholder="Enter hull no." />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_hull_description">Description</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_sb_hull_description" name="new_sb_hull_description" placeholder="Enter hull description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_sb_hull_btn" class="btn btn-primary" type="button" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End New SB Hull Modal -->
 
    <!-- Start Delete SB Hull Modal  -->
    <div class="modal fade" id="delete_sb_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Delete Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="delete_sb_hull_form" action="{{ route('deleteSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" id="delete_sb_hull_id" name="delete_sb_hull_id" readonly />
                        <label class="col-md-12 col-form-label text-md-left">Are you sure you want to delete hull <b id="delete_sb_hull_description" style="color:#ff0000;"></b>?</label>                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="confirm_delete_sb_hull_btn" class="btn btn-danger" type="button" style="width:100%;">Delete</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Delete SB Hull Modal  -->

    <!-- Start Edit SB Hull Modal -->
    <div class="modal fade" id="edit_sb_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_sb_hull_form" action="{{ route('updateSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="edit_sb_hull_id" name="edit_sb_hull_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_hull_no" name="edit_sb_hull_no" placeholder="Enter hull no." />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_hull_description">Description</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_hull_description" name="edit_sb_hull_description" placeholder="Enter hull description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_sb_hull_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Edit SB Hull Modal -->

    <!-- End SB Hull Modals -->

    <!-- Start SB Drawing Modals -->
    <!-- Start SB New Drawing Modal  -->
    <div class="modal fade" id="new_sb_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Drawing</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_sb_drawing_form" action="{{ route('saveSbDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="new_sb_drawing_hull_id" name="new_sb_drawing_hull_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_sb_drawing_hull_no" name="new_sb_drawing_hull_no" placeholder="Enter hull no." disabled/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_no">Drawing No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_sb_drawing_no" name="new_sb_drawing_no" placeholder="Enter drawing no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_title">Drawing Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_sb_drawing_title" name="new_sb_drawing_title" placeholder="Enter drawing title" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_pdf">PDF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_sb_drawing_pdf" name="new_sb_drawing_pdf" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_dwf">DWF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_sb_drawing_dwf" name="new_sb_drawing_dwf" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_sb_drawing_dwg">DWG File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_sb_drawing_dwg" name="new_sb_drawing_dwg" required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_sb_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End SB New Drawing Modal  -->

    <!-- Start SB Edit Drawing Modal  -->
    <div class="modal fade" id="edit_sb_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Drawing Details</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_sb_drawing_form" action="{{ route('updateSbDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="edit_sb_drawing_id" name="edit_sb_drawing_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_drawing_hull_no" name="edit_sb_drawing_hull_no" placeholder="Enter hull no." disabled/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_no">Drawing No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_drawing_no" name="edit_sb_drawing_no" placeholder="Enter drawing no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_title">Drawing Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_drawing_title" name="edit_sb_drawing_title" placeholder="Enter drawing title" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_revision_no">Revision No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_sb_drawing_revision_no" name="edit_sb_drawing_revision_no" placeholder="Enter drawing title" disabled />
                        </div>
                    </div>


                    <div class="form-group row justify-content-center">
                        <label class="form-check-label text-md-right" for="view_edit_sb_drawing_file_cb">
                            <input class="form-check-input" type="checkbox" name="view_edit_sb_drawing_file_cb" id="view_edit_sb_drawing_file_cb">
                            <b>Update Drawing Files?</b>
                            <i style="font-size:12px;"> (Check if you need to update drawing files)</i>
                        </label>
                    </div>

                    <div id="edit_sb_drawing_file_div">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_pdf">PDF File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_sb_drawing_pdf" name="edit_sb_drawing_pdf" required />
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_dwf">DWF File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_sb_drawing_dwf" name="edit_sb_drawing_dwf" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_sb_drawing_dwg">DWG File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_sb_drawing_dwg" name="edit_sb_drawing_dwg" required />
                            </div>
                        </div>
                    </div>

                    <div id="sb_drawing_revision_history" class="row justify-content-center">   
                        <table  id="sb_drawing_revision_history_tbl" class="table table-bordered table-hover" style="width:80%; font-size:12px;">
                            <thead>
                                <tr>
                                    <th colspan="4">Revision History</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">No.</th>
                                    <th>Date Published</th>
                                    <th>Uploaded by</th>
                                    <th>Download as</th>
                                </tr>
                            </thead>

                            <tbody id="sb_drawing_revision_history_tbl_body">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_sb_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                    <button id="new_revision_sb_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Upload Revision</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End SB Edit Drawing Modal  -->

    <!-- Start SB New Revision Drawing Modal  -->
    <div class="modal fade" id="new_revision_sb_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 id="new_revision_sb_drawing_title"></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_revision_sb_drawing_form" action="{{ route('uploadSbDrawingRevision') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="new_revision_sb_drawing_id" name="new_revision_sb_drawing_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_revision_sb_drawing_pdf">PDF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_revision_sb_drawing_pdf" name="new_revision_sb_drawing_pdf" required />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_revision_sb_drawing_dwf">DWF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_revision_sb_drawing_dwf" name="new_revision_sb_drawing_dwf" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_revision_sb_drawing_dwg">DWG File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_revision_sb_drawing_dwg" name="new_revision_sb_drawing_dwg" required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="upload_revision_sb_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Upload</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End SB New Revision Drawing Modal  -->
    <!-- Start Delete Sb Drawing Modal  -->
    <div class="modal fade" id="delete_sb_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Delete Drawing</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="delete_sb_drawing_form" action="{{ route('deleteDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" id="delete_drawing_id" name="delete_drawing_id" readonly />
                        <label class="col-md-12 col-form-label text-md-left" for="delete_sb_drawing_description">Are you sure you want to delete drawing <b id="delete_sb_drawing_description" style="color:#ff0000;"></b> from hull <b  id="delete_sb_drawing_hull_no" style="color:#ff0000;"></b>?</label>                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="confirm_delete_sb_drawing_btn" class="btn btn-danger" type="button" style="width:100%;">Delete</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Delete Sb Drawing Modal  -->

    <!-- End SB Drawing Modals -->
</div>

<script src="{{ asset('js/Drawing/shipBuilding.js') }}"></script>
