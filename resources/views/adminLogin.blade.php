@extends('layouts.app')

@section('title')
    Dashboard
@stop

@section('content')
    <div class="login-form">
        <div class="">
            <div>Log in as admin</div><br>
        </div>
        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">Email address</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">Password</label>
                <div class="password-container">
                    <input id="password" type="password" class="form-control" name="password" required>
                    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    Login <img src="images/right-arrow.svg" alt="" width="9px">
                </button>
            </div>
        </form>

    </div>
@stop