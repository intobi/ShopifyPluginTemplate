
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome page</title>

    {{--<meta property="og:title" content="The Rock" />--}}
    {{--<meta property="og:type" content="video.movie" />--}}
    {{--<meta property="og:url" content="http://www.imdb.com/title/tt0117500/" />--}}
    <meta property="og:image" content="https://usetimerly.com/imgs/TimerlyLogo_facebook.png" />


    <link href="css/app.css" rel="stylesheet">
    <link href="css/landing.css" rel="stylesheet">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.7/vue.js"></script>
    <script src="https://rawgit.com/Wlada/vue-carousel-3d/master/dist/vue-carousel-3d.min.js"></script>

    <!--PROOF PIXEL-->
    <script src='https://cdn.useproof.com/proof.js?acc=OVzJvQGSFTdFO1Yj6UIbJbN79uA2' async></script>
    <!--END PROOF PIXEL-->
</head>

<body>

<br><br>
        {!! Form::open(['route' => 'shopify.install', 'method' => 'GET']) !!}
        {!! Form::hidden('slogin', app('request')->input('trialtype')) !!}
        <div class="container">
            {{--<div class="login-input">--}}
            <div class="input-group mb-3">
                {!! Form::text('shop', '', ['class' => 'form-control', 'placeholder' => 'Enter your shopify url', 'required']) !!}
                {!! $errors->first('shop', '<span class="help-block">:message</span>') !!}
                <div class="input-group-append">
                    <button class="btn  btn-lg btn-danger" type="submit"><p>INSTALL</p></button>
                </div>
            </div>
            <p style="font-size: 1.125rem;margin-left:  10px;text-align: left;margin-top: -14px;/* font-weight: 600; */color: #80808070;">eg: myshopifyname.myshopify.com</p>
            {{--</div>--}}

        </div>

        {!! Form::close() !!}



<script type="text/javascript" src="js/app.js"></script>
</body>
</html>
