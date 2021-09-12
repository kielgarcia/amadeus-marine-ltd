@extends('layouts.app')
@auth
@section('content')
@section('title', 'Hulls - ')
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
            <h6>Hulls
            
            @can('saveHull')
            <button id="new_hull_btn" class="btn btn-primary" title="New Hull" style="float:right;">New Hull</button>
            @endcan
            </h6>
        </div>

        <div class="card-body">
            <table id="hulls_tbl" class="table-hover table responsive" style="width:100%; font-size:12px;">
                <thead>
                    <tr>
                        <th class="all">Hull No.</th>
                        <th>Description</th>
                        <th style="text-align:center;">WIP/Total<br>Drawing(s)</th>
                        <th style="text-align:center;">Total<br>Certificate(s)</th>
                        <th>Created by</th>
                        <th>Last Updated</th>
                        <th class="all"></th>
                    </tr>
                </thead>

                <tbody>
                @foreach($hulls as $hull)
                    <tr>
                        <td>{{$hull->hull_no}}</td>
                        <td>{{$hull->hull_description}}</td>
                        <td style="text-align:center;">
                            @if( $hull->total_wip == null && $hull->total_drawings == null )
                                0/0
                            @else
                                {{$hull->total_wip}}/{{$hull->total_drawings}}
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if( $hull->total_certificates == null )
                                0
                            @else
                                {{$hull->total_certificates}}
                            @endif
                        </td>
                        <td>{{$hull->created_by}}</td>
                        <td>{{$hull->updated_at}}</td>

                        <td style="text-align:right">
                            <div class="btn-group">
                                @can('updateHull')
                                <button class="edit_hull_btn btn" title="Edit Hull"
                                    data-edit_hull_id="{{$hull->id}}"
                                    data-edit_hull_no="{{$hull->hull_no}}"
                                    data-edit_hull_description="{{$hull->hull_description}}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                @endcan

                                @can('deleteHull')
                                @if( $hull->total_drawings == null && $hull->total_wip == null)
                                    <button class="delete_hull_btn btn" title="Delete Hull"
                                        data-delete_hull_id="{{$hull->id}}"
                                        data-delete_hull_no="{{$hull->hull_no}}"
                                        data-delete_hull_description="{{$hull->hull_description}}"
                                        data-delete_hull_total_drawing="{{$hull->total_drawings}}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @else
                                    <button class="btn" disabled>
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @endif
                                @endcan

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Start New SB Hull Modal -->
    <div class="modal fade" id="new_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>New Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="new_hull_form" action="{{ route('saveSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_hull_no" name="new_hull_no" placeholder="Enter hull no." />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="new_hull_description">Description</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="new_hull_description" name="new_hull_description" placeholder="Enter hull description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="save_hull_btn" class="btn btn-primary" type="button" style="width:100%;">Save</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End New SB Hull Modal -->
 
    <!-- Start Delete SB Hull Modal  -->
    <div class="modal fade" id="delete_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Delete Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="delete_hull_form" action="{{ route('deleteSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" id="delete_hull_id" name="delete_hull_id" readonly />
                        <label class="col-md-12 col-form-label text-md-left">Are you sure you want to delete hull <b id="delete_hull_description" style="color:#ff0000;"></b>?</label>                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="confirm_delete_hull_btn" class="btn btn-danger" type="button" style="width:100%;">Delete</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Delete SB Hull Modal  -->

    <!-- Start Edit SB Hull Modal -->
    <div class="modal fade" id="edit_hull_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>Edit Hull</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit_hull_form" action="{{ route('updateSbHull') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                {{ csrf_field() }}

                <input type="hidden" class="form-control" id="edit_hull_id" name="edit_hull_id" readonly />

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_hull_no">Hull No.</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_hull_no" name="edit_hull_no" placeholder="Enter hull no." />
                        </div>
                    </div>
                    

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right" for="edit_hull_description">Description</label>

                        <div class="col-md-8">
                            <input type="text" class="form-control" id="edit_hull_description" name="edit_hull_description" placeholder="Enter hull description" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="update_hull_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                    <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </div>
    <!-- End Edit SB Hull Modal -->

</div>

<script src="{{ asset('js/Drawing/hull.js') }}"></script>
@endsection
@endauth