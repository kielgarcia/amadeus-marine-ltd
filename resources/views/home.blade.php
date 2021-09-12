@extends('layouts.app')

@section('content')

@section('title', 'Home - ')
@include('layouts/loader')

<style>

    .home-bg-img {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: auto;
        min-height: 100%;
        min-width: 1024px;
        background-size: cover;
        background-position: 50% 50%;
        background-repeat: none;
        z-index:-1;     
        opacity:0.5;
    }

    .btn-transparent:hover {
        background-color: Transparent; 
        font-weight:bold; 
        color:#1a6061; 
        border:2px solid #1a6061;
        box-shadow:none;
    }
    .btn-transparent {
        background-color: #1a6061; 
        font-weight:bold; 
        color:#fff;
        box-shadow:2px 2px #fff; 
    }
</style>


<div id="contents" class="container-fluid" style="display:none;">

<img class="home-bg-img" src="/images/BG004.jpg" alt="Home - Background Image" />

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

    <div class="justify-content-left">
    
        <h1 style="color:#fff; text-shadow:2px 2px #1a6061;"> Hi, {{Auth::user()->name }}</h1>
        <b style="color:#1a6061; font-size:16px; text-shadow:1px 1px #fff;"><i class="far fa-calendar-alt"></i> {{ $current_datetime->format('M-d-Y h:i A') }} - You are logged in!</b>
        
        <h3 style="color:#fff; text-shadow:2px 2px #1a6061;">Welcome to <img src="/images/amadeus-icon2.png" style="width:30px;"/>Amadeus Marine Ltd. - Drawing Database</h3>
        
        <div class="col-sm-10">
            <b style="color:#1a6061; font-size:16px; text-shadow:1px 1px #fff;"> - This project aims to develop a web-based system <br>that will provide a solution to the risk of lost or misplaced <br>drawing files and having a facility to manage certificates, <br>completed and work in progress drawings.</b>
            <br>
            <br>
                <form method="GET" action="{{ route('viewDrawings') }}">
                    <button class="btn btn-transparent" type="submit">Start Now</button>
                </form>
            <br>

            @if( $my_activity_count != 0 )

                <b style="color:#1a6061; font-size:16px; text-shadow:1px 1px #fff;">Your recent activity:</b><br>

                @foreach( $my_activity as $la )
                    <b style="color:#1a6061; font-size:16px; text-shadow:1px 1px #fff;"><i class="fas fa-check"></i> {{ \Carbon\Carbon::parse($la->created_at)->diffForHumans() }} {{$la->action}}</b><br>
                @endforeach
                

                @if( $my_activity_count > 5 )
                <br>
                <form method="GET" action="{{ route('viewActivityLog') }}">
                    <button class="btn btn-transparent" type="submit" style="border-radius:10px; padding:0px 30px 0px 30px;">View More</button>
                </form>
                @endif

            @endif
            
        </div>
        
        
    </div>
</div>
@endsection
