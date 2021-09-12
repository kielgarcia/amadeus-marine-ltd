<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') Amadeus Drawing Database</title>
    <link rel="icon" type="image/png" href="{{url('/images/amadeus-icon.png')}}" style=" mix-blend-mode: multiply;">

    <!-- Scripts -->

   <!-- Fonts -->
   <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.7/css/responsive.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    
    
    @yield('css links')

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
</head>

<style>
    @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

    body {        
        /* font-family: 'Raleway', sans-serif; */
        font-family: 'DaxPro','Roboto',Arial,sans-serif;
        background: #f5f8fa;
    }
    
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #ffffff;
        border-radius: 10px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;

    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
    }

    #sidebar a, a:hover, a:focus {
        color: inherit;
        text-decoration: none;
        transition: all 0.3s;
    }

    #sidebar {
        min-width: 200px;
        max-width: 200px;
        height: 100vh;
        background: #1a6061;
        color: #fff;
        transition: all 0.3s;
        position: fixed;
    }

    #sidebar.active {
        margin-left: -200px;
    }

    #sidebar .sidebar-header {
        padding: 20px;
        background: #6d7fcc;
    }

    #sidebar ul.components {
        padding: 20px 0;
        border-bottom: 1px solid #47748b;
    }

    #sidebar ul p {
        color: #fff;
        padding: 10px;
    }

    #sidebar ul li a {
        padding: 10px;
        font-size: 1.1em;
        display: block;
    }

    #sidebar ul li a:hover {
        font-weight: bold;
        color: #1a6061;
        background: #fff;
    }

    ul ul a {
        font-size: 0.9em !important;
        padding-left: 30px !important;
        background: #6d7fcc;
    }

    #sidebarCollapse {
        width: 40px;
        height: 40px;
    }
    
    #sidebarCollapse span {
        width: 100%;
        height: 4px;
        margin: 0 auto;
        display: block;
        background: #1a6061;
        border-radius: 2px;
        transition: all 0.8s cubic-bezier(0.810, -0.330, 0.345, 1.375);
    }
    
    #sidebarCollapse span:first-of-type {
        /* rotate first one */
        transform: rotate(45deg) translate(4px, 4px);
    }
    #sidebarCollapse span:nth-of-type(2) {
        /* second one is not visible */
        opacity: 0;
    }
    #sidebarCollapse span:last-of-type {
        /* rotate third one */
        transform: rotate(-45deg) translate(1px, -1px);
    }
    
    #sidebarCollapse.active span {
        /* no rotation */
        transform: none;
        /* all bars are visible */
        opacity: 1;
        margin: 5px auto;
    }

    .form-label, .col-form-label {
        font-size:13px;
        font-weight:bold;
    }

    .emptyField {
        border-color: #ff0000;
    }

    .btn-primary, .btn-primary:disabled, .btn-outline-primary:hover, .a-btn-primary {
        color: #ffffff;
        background-color: #1a6061;
        border-color: #1a6061;
    }

    .btn-outline-primary, .btn-primary:hover:enabled, .a-btn-primary:hover {
        background-color: #ffffff;
        border-color: #1a6061;
        color: #1a6061;
    }

    .active-menu {
        color: #fff;
        background: #30b3b5;
        font-weight: bold;
    }

    .nav-link {
        text-decoration: none;
        color: #1a6061;
    }

    .card-header, .card-footer {
        background-color: #fff;
    }

    h1, h2, h3, h4, h5, h6 {
        font-weight: bold;
        color: #1a6061;
    }

    .dropdown-item {
        color: #1a6061;
    }

    .dropdown-item:hover {
        background-color: #1a6061;
        color: #fff;
    }
</style>
    
<body>
    <div id="app">
        <!-- Start Nav Bar Menu -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="position:fixed; z-index:1; width:100%;">

            <div class="container-fluid" style="padding:0px 10px 0px 10px;">
                    @auth
                    <a type="button" id="sidebarCollapse" class="float-left" style="margin-top:20px;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                    @endauth
                    <a class="navbar-brand" href="/home">
                        <img src="{{url('/images/amadeus-logo.png')}}" alt="Logo" style="width:300px; mix-blend-mode: multiply;">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent" style="float:right;">
                    <!-- Start Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @auth
                            <!-- Start User Menu -->
                            <li class="nav-item dropdown">
                                <a id="user" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="font-weight:bold; color:#1a6061;">
                                    <span class='fa fa-user'></span> {{Str::words(Auth::user()->name, 1, '')}} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user">
                                    <a class="dropdown-item" href="#" id="change_password_btn">Change Password</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            <!-- End User Menu -->
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endauth
                    </ul>
                    <!-- End Right Side Of Navbar -->
                </div>
            </div>
        </nav>
        <!-- End Nav Bar Menu -->

        <!-- Start Side Nav Bar Menu -->
        <div class="wrapper">
            <!-- Sidebar -->
            @auth
            <nav id="sidebar" style="margin-top:0px;">
                <ul class="list-unstyled components" style="margin-top:60px;">
                    <li class="{{ (request()->is('home')) ? 'active-menu' : '' }}">
                        <a href="/home"><i class="fas fa-home"></i> Home</a>
                    </li>

                    @hasanyrole('Administrator|Moderator')
                    <li class="{{ (request()->is('administration')) ? 'active-menu' : '' }}">
                        <a href="{{ route('viewAdministration') }}" style="font-size:16px;"><i class="fas fa-users-cog"></i>    Users Management</a>
                    </li>
                    @endhasanyrole

                    <li class="{{ (request()->is('hulls')) ? 'active-menu' : '' }}">
                        <a href="{{ route('viewHulls') }}"><i class="fas fa-ship"></i> Hulls</a>
                    </li>

                    <li class="{{ (request()->is('drawings')) ? 'active-menu' : '' }}">
                        <a href="{{ route('viewDrawings') }}"><i class="fas fa-pencil-ruler"></i>   Drawings</a>
                    </li>

                    <li class="{{ (request()->is('certificates')) ? 'active-menu' : '' }}">
                        <a href="{{ route('viewCertificates') }}"><i class="fas fa-certificate"></i>  Certificates</a>
                    </li>

                    <li class="{{ (request()->is('activity-logs')) ? 'active-menu' : '' }}">
                        <a href="{{ route('viewActivityLog') }}"><i class="fas fa-clipboard-list"></i>  Activity Logs</a>
                    </li>
                </ul>
            </nav>
            @endauth
            <!-- End Side Nav Bar Menu -->

            <!-- Start Web Content -->
            <div id="main-app" class="container-fluid" style="margin-top:100px; margin-left:200px; margin-bottom:50px; display: block;">
                @yield('content')

                <!-- Start My Profile Modal  -->
                <div class="modal fade" id="change_my_password_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6>Change Password</h6>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <form id="change_my_password_form" action="{{ route('changeMyPassword') }}" method="POST" enctype="multipart/form-data" onkeydown="return preventFormSubmitOnEnterKey(event)">
                            {{ csrf_field() }}


                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right" for="my_current_password">Current</label>

                                    <div class="col-md-8">
                                        <input class="form-control" autocomplete="off" type="password" id="my_current_password" name="my_current_password" placeholder="Enter current password" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right" for="my_new_password">New</label>

                                    <div class="col-md-8">
                                        <input class="form-control" autocomplete="off" type="password" id="my_new_password" name="my_new_password" placeholder="Enter new password" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right" for="confirm_new_password">Re-type New</label>

                                    <div class="col-md-8">
                                        <input class="form-control" autocomplete="off" type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Re-type new password" />

                                        <div id="min_password_lbl">
                                            <p class="col-md-10 col-form-label text-md-left" style="color:#ff0000;">At least 8 characters password</p>
                                        </div>

                                        <div id="not_match_password_lbl">
                                            <p class="col-md-10 col-form-label text-md-left" style="color:#ff0000;">Confirm new password does not match.</p>
                                        </div>
                                        
                                        <br>

                                        <div class="col-md-10 form-check text-md-left">
                                            <input class="form-check-input" type="checkbox" id="show_password_cb">
                                            <label class="form-check-label" for="show_password_cb">Show Password</label>
                                        </div>
                                    </div>

                                    
                                </div>

                                
                            </div>

                            <div class="modal-footer">
                                <button id="update_my_password_btn" class="btn btn-primary" type="button" style="width:100%;">Update</button>
                                <button class="btn btn-outline-secondary" style="width:100%;" data-dismiss="modal">Cancel</button>
                            </div>

                            </form>

                        </div>
                    </div>
                </div>
                <!-- End My Profile Modal  -->
            </div>
            <!-- End Web Content -->
        </div>

        <!-- Start User Profile Modal  -->
        <!-- End User Profile Modal  -->

    </div>
</body>

@yield('js links')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    $(document).ready(function(){
        $(document).bind("contextmenu",function(e){
              return false;
       });
       
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            $(this).toggleClass('active');

            if( $(this).hasClass('active') ){
                document.getElementById("main-app").style.marginLeft = "0px";
                document.getElementById("main-app").style.transitionDuration = "0.3s";
            }else{
                document.getElementById("main-app").style.marginLeft = "200px";
                document.getElementById("main-app").style.transitionDuration = "0.3s";
            }
        });
    });

    $('#change_password_btn').on('click',function(){
        $('#min_password_lbl').prop('hidden',true);
        $('#not_match_password_lbl').prop('hidden',true);
        $('#change_my_password_modal').modal('show');
    });

    $('#update_my_password_btn').on('click',function(){
        var current_password = $('#my_current_password').val();
        var new_password = $('#my_new_password').val();
        var confirm_new_password = $('#confirm_new_password').val();
        
        if(
            (current_password != null && current_password.trim() != "" && current_password.length >= 8) &&
            (new_password != null && new_password.trim() != "" && new_password.length >= 8) &&
            (confirm_new_password != null && confirm_new_password.trim() != "" && confirm_new_password.length >= 8) &&
            (new_password == confirm_new_password)
        ){
            $('#change_my_password_form').submit();
        }else{
            setEmptyFieldInUpdatePasswordForm(current_password,new_password,confirm_new_password);
        }
    });

    function setEmptyFieldInUpdatePasswordForm(current_password,new_password,confirm_new_password){
        if(current_password == null || current_password.trim() == ""){
            $('#my_current_password').addClass('emptyField');

            if(current_password.length <= 7){
                $('#min_password_lbl').prop('hidden',false);
            }else{
                $('#min_password_lbl').prop('hidden',true);
            }
        }else{
            $('#my_current_password').removeClass('emptyField');
        }

        if(new_password == null || new_password.trim() == ""){
            $('#my_new_password').addClass('emptyField');

            if(new_password.length <= 7){
                $('#min_password_lbl').prop('hidden',false);
            }else{
                $('#min_password_lbl').prop('hidden',true);
            }
        }else{
            $('#my_new_password').removeClass('emptyField');
        }

        if(confirm_new_password == null || confirm_new_password.trim() == ""){
            $('#confirm_new_password').addClass('emptyField');

            if(confirm_new_password.length <= 7){
                $('#min_password_lbl').prop('hidden',false);
            }else{
                $('#min_password_lbl').prop('hidden',true);
            }
        }else{
            $('#confirm_new_password').removeClass('emptyField');
        }

        if(new_password != confirm_new_password){
            $('#not_match_password_lbl').prop('hidden',false);
        }else{
            $('#not_match_password_lbl').prop('hidden',true);
        }
    };

    $('#change_my_password_modal').on('click','#show_password_cb',function(){
        if ($('#show_password_cb').is(':checked')){
            $('#my_current_password').attr('type', 'text');
            $('#my_new_password').attr('type', 'text');
            $('#confirm_new_password').attr('type', 'text');
        }else{
            $('#my_current_password').attr('type', 'password');
            $('#my_new_password').attr('type', 'password');
            $('#confirm_new_password').attr('type', 'password');
            
        }
    });

    $('#change_my_password_modal').on('hide.bs.modal', function(){
        $('#change_my_password_form').trigger('reset');
        $('#not_match_password_lbl').prop('hidden',true);
        $('#min_password_lbl').prop('hidden',true);
        $('#my_current_password').removeClass('emptyField');
        $('#my_new_password').removeClass('emptyField');
        $('#confirm_new_password').removeClass('emptyField');
    });

    $('#change_my_password_form').on('submit', function(){
        $('#update_my_password_btn').prop('disabled',true).text('...');
    });
</script>
</html>