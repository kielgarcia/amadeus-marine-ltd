@extends('layouts.app')
@section('content')
@section('title', 'Login -')
@guest


<style>
.card {
    margin-top: 10%;
}
.cb-slideshow,
.cb-slideshow:after { 
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    z-index: 0; 
}
/* .cb-slideshow:after { 
    content: '';
    background: transparent url(../images/BG001.jpg); 
} */

.cb-slideshow li span { 
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0px;
    left: 0px;
    color: transparent;
    background-size: cover;
    background-position: 50% 50%;
    background-repeat: none;
    opacity: 0;
    z-index: 0;
    animation: imageAnimation 36s linear infinite 0s; 
}

.cb-slideshow li div { 
    z-index: 1000;
    position: absolute;
    bottom: 30px;
    left: 0px;
    width: 100%;
    text-align: center;
    opacity: 0;
    color: #fff;
    animation: titleAnimation 36s linear infinite 0s; 
}
.cb-slideshow li div h3 { 
    font-family: 'BebasNeueRegular', 'Arial Narrow', Arial, sans-serif;
    font-size: 240px;
    padding: 0;
    line-height: 200px; 
}

.cb-slideshow li:nth-child(1) span { 
    background-image: url(../images/BG001.jpg); 
}
.cb-slideshow li:nth-child(2) span { 
    background-image: url(../images/BG002.jpg);
    animation-delay: 6s; 
}
.cb-slideshow li:nth-child(3) span { 
    background-image: url(../images/BG003.jpg);
    animation-delay: 12s; 
}
.cb-slideshow li:nth-child(4) span { 
    background-image: url(../images/BG004.jpg);
    animation-delay: 18s; 
}
.cb-slideshow li:nth-child(5) span { 
    background-image: url(../images/BG005.jpg);
    animation-delay: 24s; 
}
/* .cb-slideshow li:nth-child(6) span { 
    background-image: url(../images/6.jpg);
    animation-delay: 30s; 
} */


.cb-slideshow li:nth-child(2) div { 
    animation-delay: 6s; 
}
.cb-slideshow li:nth-child(3) div { 
    animation-delay: 12s; 
}
.cb-slideshow li:nth-child(4) div { 
    animation-delay: 18s; 
}
.cb-slideshow li:nth-child(5) div { 
    animation-delay: 24s; 
}
/* .cb-slideshow li:nth-child(6) div { 
    animation-delay: 30s; 
} */

@keyframes imageAnimation { 
    0% { opacity: 0; animation-timing-function: ease-in; }
    8% { opacity: 1; animation-timing-function: ease-out; }
    17% { opacity: 1 }
    25% { opacity: 0 }
    100% { opacity: 0 }
}

@keyframes titleAnimation { 
    0% { opacity: 0 }
    8% { opacity: 1 }
    17% { opacity: 1 }
    19% { opacity: 0 }
    100% { opacity: 0 }
}

.no-cssanimations .cb-slideshow li span{
	opacity: 1;
}

@media screen and (max-width: 1140px) { 
    .cb-slideshow li div h3 { font-size: 140px }
}
@media screen and (max-width: 600px) { 
    .cb-slideshow li div h3 { font-size: 80px }
}

@keyframes imageAnimation { 
	0% {
	    opacity: 0;
	    /* animation-timing-function: ease-in; */
	}
	8% {
	    opacity: 1;
	    transform: scale(1.05);
	    /* animation-timing-function: ease-out; */
	}
	17% {
	    opacity: 1;
	    transform: scale(1.1);
	}
	25% {
	    opacity: 0;
	    transform: scale(1.1);
	}
	100% { opacity: 0 }
}

@keyframes titleAnimation { 
	0% {
	    opacity: 0;
	    transform: translateX(200px);
	}
	8% {
	    opacity: 1;
	    transform: translateX(0px);
	}
	17% {
	    opacity: 1;
	    transform: translateX(0px);
	}
	19% {
	    opacity: 0;
	    transform: translateX(-400px);
	}
	25% { opacity: 0 }
	100% { opacity: 0 }
}

.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color:#1a6061;
   color: white;
   text-align: center;
}

</style>

<ul class="cb-slideshow">
	<li style="list-style-type: none;">
		<span></span>
	</li>

    <li style="list-style-type: none;">
		<span></span>
	</li>

    <li style="list-style-type: none;">
		<span></span>
	</li>

    <li style="list-style-type: none;">
		<span></span>
	</li>

    <li style="list-style-type: none;">
		<span></span>
	</li>
</ul>

<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-sm">
                    <div class="card-header justify-content-center">
                        <div class="title text-md-center">
                        
                            <h4><img src="{{url('/images/amadeus-icon2.png')}}" alt="Amadeus-icon" style="width:30px; mix-blend-mode: multiply;"> Amadeus Marine Ltd. - Drawing Database</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                <!-- <label class="form-label">Email Address</label> -->
                                <div class="col-md-6">
                                    <input id="email" placeholder="Enter your Email Address" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                <!-- <label class="form-label">Password</label> -->
                                <div class="col-md-6">
                                    <input placeholder="Enter your Password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    
                                <button type="submit" class="btn btn-primary" style="width:100%; border-radius: 20px; padding:10px 10px 10px 10px;">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer justify-content-center">

                        <p style="color:#1a6061; text-align:center;">Don’t have an account? Kindly request to Administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <br>
        <p> &copy; 2021 - <img src="{{url('/images/amadeus-icon3.png')}}" alt="Amadeus-icon" style="width:20px;"> Amadeus Marine Ltd.</p>
    </div>
@endguest
@endsection
