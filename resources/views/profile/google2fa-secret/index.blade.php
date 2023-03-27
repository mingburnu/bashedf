@extends('layouts.app')

@section('content')
    <div class="container-fluid p-5 shadow-sm">
        <h2 class="font-weight-bold">Google Authenticator</h2>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card ">
                    <div class="card-body text-center">
                        <p>Set up your two factor authentication by scanning the barcode below. Alternatively, you can
                            use the code {{ $user->google2fa_secret }}</p>
                        <div>
                            <img class="block mx-auto" src="{{ $user->qr_code }}" alt="">
                        </div>
                        <p>You must set up your Google Authenticator app before continuing. You will be unable to login
                            otherwise</p>
                        <div>
                            <a href="{{route('profile.index')}}">
                                <button class="btn btn-success">Complete Registration</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection