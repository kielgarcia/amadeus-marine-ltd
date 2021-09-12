@extends('layouts.app')
@auth
@section('content')
@section('title', 'Drawings - ')
@include('layouts/loader')
<style>
   tr.selected {
       background-color: #a6a6a6;
    }
 </style>

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
            <a class="nav-link active" id="pills-sb-tab" data-toggle="tab" href="#pills-sb" role="tab" aria-controls="pills-sb" aria-selected="true" style="font-weight:bold;">PROJECTS</a>
        </li>

        <!-- <li class="nav-item">
            <SHIP class="nav-link" id="pills-cert-tab" data-toggle="tab" href="#pills-cert" role="tab" aria-controls="pills-cert" aria-selected="false"  style="font-weight:bold;">CERTIFICATES</a>
        </li> -->

        <li class="nav-item">
            <a class="nav-link" id="pills-wip-tab" data-toggle="tab" href="#pills-wip" role="tab" aria-controls="pills-wip" aria-selected="false"  style="font-weight:bold;">WORK IN PROGRESS</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active border border-top-0 bg-white shadow-sm" id="pills-sb" role="tabpanel" aria-labelledby="pills-sb-tab">
            <br>
                @include('Drawing.shipBuilding')
            <br>
        </div>

        <!-- <div class="tab-pane fade border border-top-0 bg-white shadow-sm" id="pills-cert" role="tabpanel" aria-labelledby="pills-cert-tab">
            <br>
                
            <br>
        </div> -->

        <div class="tab-pane fade border border-top-0 bg-white shadow-sm" id="pills-wip" role="tabpanel" aria-labelledby="pills-wip-tab">
            <br>
                @include('Drawing.wip')
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