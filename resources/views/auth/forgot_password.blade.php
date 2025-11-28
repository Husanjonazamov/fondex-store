<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->

    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>

<body>
<?php if (isset($_COOKIE['store_panel_color'])) { ?>
    <style type="text/css">
        a, a:hover, a:focus {
            color: <?php echo $_COOKIE['store_panel_color']; ?>;
        }

        .form-group.default-admin {
            padding: 10px;
            font-size: 14px;
            color: #000;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
            margin: 20px 10px 10px 10px;
        }

        .form-group.default-admin .crediantials-field {
            position: relative;
            padding-right: 15px;
            text-align: left;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .form-group.default-admin .crediantials-field > a {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            height: 20px;
        }

        .btn-primary, .btn-primary.disabled, .btn-primary:hover, .btn-primary.disabled:hover {
            background: <?php echo $_COOKIE['store_panel_color']; ?>;
            border: 1px solid<?php echo $_COOKIE['store_panel_color']; ?>;
        }

        [type="checkbox"]:checked + label::before {
            border-right: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
        }

        .form-material .form-control, .form-material .form-control.focus, .form-material .form-control:focus {
            background-image: linear-gradient(<?php echo $_COOKIE['store_panel_color']; ?>, <?php echo $_COOKIE['store_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
        }

        .btn-primary.active, .btn-primary:active, .btn-primary:focus, .btn-primary.disabled.active, .btn-primary.disabled:active, .btn-primary.disabled:focus, .btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary.focus:active, .btn-primary:active:focus, .btn-primary:active:hover, .open > .dropdown-toggle.btn-primary.focus, .open > .dropdown-toggle.btn-primary:focus, .open > .dropdown-toggle.btn-primary:hover, .btn-primary.focus, .btn-primary:focus, .btn-primary:not(:disabled):not(.disabled).active:focus, .btn-primary:not(:disabled):not(.disabled):active:focus, .show > .btn-primary.dropdown-toggle:focus {
            background: <?php echo $_COOKIE['store_panel_color']; ?>;
            border-color: <?php echo $_COOKIE['store_panel_color']; ?>;
            box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color']; ?>;
        }
    </style>
<?php } ?>

<section id="wrapper">

    <div class="login-register" <?php if (isset($_COOKIE['store_panel_color'])){ ?>
         style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>; <?php } ?>">

        <div class="login-logo text-center py-3" style="margin-top:5%;">

            <a href="#" style="display: inline-block;background: #fff;padding: 10px;border-radius: 5px;"><img
                        src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

        </div>

        <div class="login-box card" style="margin-bottom:0%;">

            <div class="card-body">

                <form class="form-horizontal form-material" name="login" id="login-box" action="#">
                    @csrf
                    <div class="box-title m-b-20">{{trans('lang.forgot_password')}}</div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <label for="email_address" class="text-dark">{{trans('lang.user_email')}}</label>
                            <input class="form-control" placeholder="{{ __('Enter Email') }}" id="email_address"
                                   type="email"
                                   name="email_address"
                                   autocomplete="email" autofocus></div>
                        <div class="error" id="email_address_error"></div>


                    </div>

                    <div class="form-group text-center m-t-20">

                        <div class="col-xs-12">
                            <button type="button" onclick="callForgotPassword()" id="login_btn"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                {{ __('Send Link') }}
                            </button>
                            <a href="{{route('login')}}" id="signup_btn"
                               class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                {{ trans('lang.cancel') }}
                            </a>


                        </div>
                    </div>
                    <div class="form-group">
                        <div class="error" id="authentication_error"></div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script type="text/javascript">

    var database = firebase.firestore();

    function callForgotPassword() {
        var email_address = $('#email_address').val();

        $('.error').html("");
        if (email_address == "") {
            $('#email_address_error').html("{{trans('lang.email_address_error')}}");

        } else {
            database.collection("users").where("email", "==", email_address).where('role', '==', 'vendor').get().then(async function (snapshots) {

                if (snapshots.docs.length > 0) {

                    firebase.auth().sendPasswordResetEmail(email_address).then((result) => {

                        $('#authentication_error').html("{{trans('lang.email_sent')}}");

                    }).catch((error) => {

                        $('#authentication_error').html(error.message);

                    });
                } else {

                    $('#authentication_error').html("{{trans('lang.email_user')}}");
                }
            });

        }
    }

</script>

</body>

</html>
