<div class="container-fluid">
    <div class="panel-heading">
        <h5>Work In Progress Drawings
            <button id="new_wip_drawing_btn" class="btn btn-primary" title="New WIP Drawing" style="float:right;">New WIP Drawing</button>
        </h5>
    </div>

    <br>    

    <table id="wip_drawings_tbl" class="table-hover table responsive" style="width:100%; font-size:12px;">
        <thead>
            <tr>
                <th class="all">Hull No.</th>
                <th>Drawing No.</th>
                <th>Drawing Title</th>
                <th>Created at</th>
                <th>Last Updated</th>
                <th>Uploaded by</th>
                <th class="all"></th>
            </tr>
        </thead>

        <tbody  id="wip_drawings_tbl_body">
            @foreach( $wip_drawings as $wip )
            <tr>
                <td>{{ $wip->hull_no }}</td>
                <td>{{ $wip->drawing_no }}</td>
                <td>{{ $wip->drawing_title }}</td>
                <td>{{ $wip->created_at }}</td>
                <td>{{$wip->updated_at->diffForHumans()}}</td>
                <td>{{ $wip->uploader }}</td>
                <td style="text-align:right;">
                    @if( $wip->dwf != null && $wip->dwg != null )
                        <div class="btn-group">
                            <a href="{{ route('download', ['filename' => $wip->dwf ])}}" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-left-radius:10px; border-bottom-left-radius:10px; padding:0px 10px 0px 10px;">DWF</a>
                            <a href="{{ route('download', ['filename' => $wip->dwg ])}}" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-top-right-radius:10px;  border-bottom-right-radius:10px; padding:0px 10px 0px 10px;">DWG</a> 
                        </div>
                    @elseif( $wip->dwf != null && $wip->dwg == null ) <!-- If DWF only available -->
                        <a href="{{ route('download', ['filename' => $wip->dwf ])}}" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-radius:10px; padding:0px 20px 0px 20px;">DWF</a>
                    @elseif( $wip->dwf == null && $wip->dwg != null ) <!-- If DWG only available -->
                        <a href="{{ route('download', ['filename' => $wip->dwg ])}}" class="download-btn btn btn-outline-primary" style="text-decoration:none; border-radius:10px; padding:0px 20px 0px 20px;">DWG</a> 
                    @endif
                    

                    <div class="btn-group">
                        <button class="edit_wip_drawing_btn btn" title="Edit WIP Drawing"
                            data-edit_wip_drawing_id="{{ $wip->id }}"
                            data-edit_wip_drawing_hull_id="{{ $wip->hull_id }}"
                            data-edit_wip_drawing_no="{{ $wip->drawing_no }}"
                            data-edit_wip_drawing_title="{{ $wip->drawing_title }}">
                            <i class="fas fa-pen"></i>
                        </button>
                        
                        <button class="delete_wip_drawing_btn btn" title="Delete WIP Drawing"
                            data-delete_wip_drawing_id="{{ $wip->id }}"
                            data-delete_wip_drawing_hull_no="{{ $wip->hull_no }}"
                            data-delete_wip_drawing_no="{{ $wip->drawing_no }}"
                            data-delete_wip_drawing_title="{{ $wip->drawing_title }}">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
        
    

    <!-- Start WIP Drawing Modals  -->
    <!-- Start NEW WIP Drawing Modal  -->
    <div class="modal fade" id="new_wip_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New WIP Drawing</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_wip_drawing_form" action="{{ route('saveWipDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_wip_drawing_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <select class="form-control" id="new_wip_drawing_hull_no" name="new_wip_drawing_hull_no" required>
                                <option selected value="" style="display:none;">Select Hull</option>
                                @foreach($sb_hulls as $hull)
                                    <option value="{{ $hull->id }}">{{ $hull->hull_no }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_wip_drawing_no">Drawing No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_wip_drawing_no" name="new_wip_drawing_no" placeholder="Enter drawing no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_wip_drawing_title">Drawing Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_wip_drawing_title" name="new_wip_drawing_title" placeholder="Enter drawing title" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_wip_drawing_dwf">DWF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_wip_drawing_dwf" name="new_wip_drawing_dwf" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_wip_drawing_dwg">DWG File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_wip_drawing_dwg" name="new_wip_drawing_dwg" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <p class="col-md-10 col-form-label text-md-center" id="new_wip_drawing_file_validation_lbl" style="color:#ff0000;">At least 1 file required to upload.</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_wip_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End NEW WIP Drawing Modal  -->

    <!-- Start Edit WIP Drawing Modal  -->
    <div class="modal fade" id="edit_wip_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit WIP Drawing</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_wip_drawing_form" action="{{ route('updateWipDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" id="edit_wip_drawing_id" name="edit_wip_drawing_id" readonly required />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_wip_drawing_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <select class="form-control" id="edit_wip_drawing_hull_no" name="edit_wip_drawing_hull_no" required>
                                <option selected value="" style="display:none;">Select Hull</option>
                                @foreach($sb_hulls as $hull)
                                    <option value="{{ $hull->id }}">{{ $hull->hull_no }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_wip_drawing_no">Drawing No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_wip_drawing_no" name="edit_wip_drawing_no" placeholder="Enter drawing no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_wip_drawing_title">Drawing Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_wip_drawing_title" name="edit_wip_drawing_title" placeholder="Enter drawing title" required />
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="form-check-label text-md-right" for="view_edit_wip_drawing_file_cb">
                            <input class="form-check-input" type="checkbox" name="view_edit_wip_drawing_file_cb" id="view_edit_wip_drawing_file_cb">
                            <b>Update Drawing Files?</b>
                            <i style="font-size:12px;"> (Check if you need to update drawing files)</i>
                        </label>
                    </div>

                    <div id="edit_wip_drawing_file_div">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_wip_drawing_dwf">DWF File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_wip_drawing_dwf" name="edit_wip_drawing_dwf" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_wip_drawing_dwg">DWG File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_wip_drawing_dwg" name="edit_wip_drawing_dwg" />
                            </div>
                        </div>

                        <div class="form-group">
                            <p class="col-md-10 col-form-label text-md-center" id="edit_wip_drawing_file_validation_lbl" style="color:#ff0000;">At least 1 file required to upload.</p>
                        </div>
                    </div>

                    
                </div>

                <div class="modal-footer">
                    <button id="update_wip_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                    <button id="finalize_wip_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Finalize</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Edit WIP Drawing Modal  -->

    <!-- Start Finalize WIP Drawing Modal  -->
    <div class="modal fade" id="finalize_wip_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 id="finalize_wip_drawing_title"></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="finalize_wip_drawing_form" action="{{ route('finalizeWipDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="finalize_wip_drawing_id" name="finalize_wip_drawing_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="finalize_wip_drawing_pdf">PDF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="finalize_wip_drawing_pdf" name="finalize_wip_drawing_pdf" required />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="finalize_wip_drawing_dwf">DWF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="finalize_wip_drawing_dwf" name="finalize_wip_drawing_dwf" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="finalize_wip_drawing_dwg">DWG File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="finalize_wip_drawing_dwg" name="finalize_wip_drawing_dwg" required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_finalize_wip_drawing_btn" class="btn btn-primary" type="button" style="width:100%;">Finalize</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Finalize WIP Drawing Modal  -->

    <!-- Start Delete Sb Drawing Modal  -->
    <div class="modal fade" id="delete_wip_drawing_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Delete WIP Drawing</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="delete_wip_drawing_form" action="{{ route('deleteDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" id="delete_wip_drawing_id" name="delete_drawing_id" readonly />
                        <label class="col-md-12 col-form-label text-md-left" for="delete_wip_drawing_description">Are you sure you want to delete work in progress drawing <b id="delete_wip_drawing_description" style="color:#ff0000;"></b> from hull <b  id="delete_wip_drawing_hull_no" style="color:#ff0000;"></b>?</label>                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="confirm_delete_wip_drawing_btn" class="btn btn-danger" type="submit" style="width:100%;">Delete</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Delete Sb Drawing Modal  -->
    <!-- End WIP Drawing Modals  -->
</div>

<script src="{{ asset('js/Drawing/wip.js') }}"></script>
