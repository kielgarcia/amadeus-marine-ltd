@extends('layouts.app')
@auth
@section('content')
@section('title', 'Certificates - ')
@include('layouts/loader')

<div id="contents" class="container-fluid" style="display:none;">
    @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('failed'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ $message }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="list-group" style="padding:10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
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
            <table id="certificate_hulls_tbl" class="table-hover table responsive" style="width:100%; font-size:12px;">
                <thead style="display: table; width: 100%; table-layout:fixed;">
                    <tr>
                        <th class="all">Hull No.</th>
                        <th>Description</th>
                        <th style="text-align:center;">Total<br>Certificate(s)</th>
                        <th>Created by</th>
                        <th>Last Updated</th>
                        <!-- <th class="all"></th> -->
                    </tr>
                </thead>

                <tbody  id="certificate_hulls_tbl_body" class="table-body" style="display: block; height: 300px; overflow: auto;">
                @foreach($hulls as $hull)
                    <tr style="display: table; width: 100%; table-layout: fixed;">
                        <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}">
                            {{$hull->hull_no}}
                        </td>
                        <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}">
                            {{$hull->hull_description}}
                        </td>

                        <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}" style="text-align:center;">
                        @if( $hull->total_certificates == null )
                            0
                         @else
                            {{$hull->total_certificates}}
                        @endif
                        </td>

                        <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}">
                            {{$hull->created_by}}
                        </td>

                        <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}">
                            {{$hull->updated_at}}
                        </td>

                        <!-- <td class="view_certificates" data-selected_certificate_hull_id="{{$hull->id}}" data-selected_certificate_hull_no="{{$hull->hull_no}}" style="text-align:right">
                            <div class="btn-group">
                                <button class="edit_certificate_hull_btn btn" title="Edit Hull"
                                    data-edit_certificate_hull_id="{{$hull->id}}"
                                    data-edit_certificate_hull_no="{{$hull->hull_no}}"
                                    data-edit_certificate_hull_description="{{$hull->hull_description}}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                @if( $hull->total_drawings == null && $hull->total_wip == null)
                                    <button class="delete_certificate_hull_btn btn" title="Delete Hull"
                                        data-delete_certificate_hull_id="{{$hull->id}}"
                                        data-delete_certificate_hull_no="{{$hull->hull_no}}"
                                        data-delete_certificate_hull_description="{{$hull->hull_description}}"
                                        data-delete_certificate_hull_total_drawing="{{$hull->total_drawings}}">
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

    <div class="card shadow-md" id="certificates_div">
        <div class="card-header">
            <div class="row">
                <h5 class="col-sm-6" id="selected_certificate_hull_lbl"></h5>

                <div class="col-sm-6">
                    <button id="new_certificate_btn" class="btn btn-primary" style="float:right;" title="New Certificate">New Certificate</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="certificates_tbl" class="table table-hover responsive" style="width:100%; font-size:12px;">
                <thead>
                    <tr>
                        <th class="all">Certificate No.</th>
                        <th>Title</th>
                        <th>Date Published</th>
                        <th>Uploaded by</th>
                        <th>Download As</th>
                        <th class="all"></th>
                    </tr>
                </thead>

                <tbody id="certificates_tbl_body">
               
                </tbody>
            </table>
        </div>
    </div>


    <!-- Start Certificate Modals -->
    <!-- Start New Certificate Modal  -->
    <div class="modal fade" id="new_certificate_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Certificate</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_certificate_form" action="{{ route('saveCertificate') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="new_certificate_hull_id" name="new_certificate_hull_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_certificate_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_certificate_hull_no" name="new_certificate_hull_no" placeholder="Enter hull no." disabled/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_certificate_no">Certificate No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_certificate_no" name="new_certificate_no" placeholder="Enter certificate no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_certificate_title">Certificate Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_certificate_title" name="new_certificate_title" placeholder="Enter certificate title" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_certificate_pdf">PDF File</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" id="new_certificate_pdf" name="new_certificate_pdf" required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_certificate_btn" class="btn btn-primary" type="button" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End New Certificate Modal  -->

    <!-- Start Edit Certificate Modal  -->
    <div class="modal fade" id="edit_certificate_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit Certificate</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_certificate_form" action="{{ route('updateCertificate') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="edit_certificate_id" name="edit_certificate_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_certificate_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_certificate_hull_no" name="edit_certificate_hull_no" placeholder="Enter hull no." disabled/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_certificate_no">Certificate No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_certificate_no" name="edit_certificate_no" placeholder="Enter certificate no." required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_certificate_title">Certificate Title</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_certificate_title" name="edit_certificate_title" placeholder="Enter certificate title" required />
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label class="form-check-label text-md-right" for="view_edit_certificate_file_cb">
                            <input class="form-check-input" type="checkbox" name="view_edit_certificate_file_cb" id="view_edit_certificate_file_cb">
                            <b>Update Certificate File?</b>
                            <i style="font-size:12px;"> (Check if you need to update certificate file)</i>
                        </label>
                    </div>

                    <div id="edit_certificate_file_div">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right" for="edit_certificate_pdf">PDF File</label>

                            <div class="col-md-8">
                                <input type="file" class="form-control" id="edit_certificate_pdf" name="edit_certificate_pdf" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_certificate_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Edit Certificate Modal  -->

    <!-- Start Delete Certificate Modal  -->
    <div class="modal fade" id="delete_certificate_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Delete Certificate</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="delete_certificate_form" action="{{ route('deleteDrawing') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" id="delete_certificate_id" name="delete_drawing_id" readonly />
                        <label class="col-md-12 col-form-label text-md-left" for="delete_certificate_description">Are you sure you want to delete certificate <b id="delete_certificate_description" style="color:#ff0000;"></b> from hull <b  id="delete_certificate_hull_no" style="color:#ff0000;"></b>?</label>                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="confirm_delete_certificate_btn" class="btn btn-danger" type="button" style="width:100%;">Delete</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Delete Certificate Modal  -->

    <!-- End Certificate Modals -->



</div>

<script src="{{ asset('js/Drawing/certificate.js') }}"></script>
@endsection
@endauth