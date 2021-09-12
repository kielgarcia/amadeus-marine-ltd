@extends('layouts.app')
@auth
@section('content')
@section('title', 'Users Management - ')
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

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-users-tab" data-toggle="tab" href="#pills-users" role="tab" aria-controls="pills-users" aria-selected="true" style="font-weight:bold;">USERS</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="pills-roles-tab" data-toggle="tab" href="#pills-roles" role="tab" aria-controls="pills-roles" aria-selected="false"  style="font-weight:bold;">ROLES</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="pills-permissions-tab" data-toggle="tab" href="#pills-permissions" role="tab" aria-controls="pills-permissions" aria-selected="false"  style="font-weight:bold;">PERMISSIONS</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active border border-top-0 bg-white shadow-sm" id="pills-users" role="tabpanel" aria-labelledby="pills-users-tab">
            <br>
            @include('Admin/user')
            <br>
        </div>

        <div class="tab-pane fade border border-top-0 bg-white shadow-sm" id="pills-roles" role="tabpanel" aria-labelledby="pills-roles-tab">
            <br>
            @include('Admin/role')
            <br>
        </div>

        <div class="tab-pane fade border border-top-0 bg-white shadow-sm" id="pills-permissions" role="tabpanel" aria-labelledby="pills-permissions-tab">
            <br>
            @include('Admin/permission')
            <br>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#myTab a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endsection
@endauth