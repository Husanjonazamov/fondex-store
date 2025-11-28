<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png')}}">


        <!-- Fonts -->

        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    </head>

    <body>
        <?php if (isset($_COOKIE['store_panel_color'])) { ?>
        <style type="text/css">
            a,
            a:hover,
            a:focus {
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

            .form-group.default-admin .crediantials-field>a {
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                height: 20px;
            }

            .btn-primary,
            .btn-primary.disabled,
            .btn-primary:hover,
            .btn-primary.disabled:hover {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            [type="checkbox"]:checked+label::before {
                border-right: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
                border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            .form-material .form-control,
            .form-material .form-control.focus,
            .form-material .form-control:focus {
                background-image: linear-gradient(<?php echo $_COOKIE['store_panel_color']; ?>, <?php echo $_COOKIE['store_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
            }

            .btn-primary.active,
            .btn-primary:active,
            .btn-primary:focus,
            .btn-primary.disabled.active,
            .btn-primary.disabled:active,
            .btn-primary.disabled:focus,
            .btn-primary.active.focus,
            .btn-primary.active:focus,
            .btn-primary.active:hover,
            .btn-primary.focus:active,
            .btn-primary:active:focus,
            .btn-primary:active:hover,
            .open>.dropdown-toggle.btn-primary.focus,
            .open>.dropdown-toggle.btn-primary:focus,
            .open>.dropdown-toggle.btn-primary:hover,
            .btn-primary.focus,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled).active:focus,
            .btn-primary:not(:disabled):not(.disabled):active:focus,
            .show>.btn-primary.dropdown-toggle:focus {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border-color: <?php echo $_COOKIE['store_panel_color']; ?>;
                box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color']; ?>;
            }
            .error {
                color: red;
            }
        </style>
        <?php } ?>

        <?php
        $countries = file_get_contents(public_path('countriesdata.json'));
        $countries = json_decode($countries);
        $countries = (array) $countries;
        $newcountries = [];
        $newcountriesjs = [];
        foreach ($countries as $keycountry => $valuecountry) {
            $newcountries[$valuecountry->phoneCode] = $valuecountry;
            $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
        }
        ?>

        <section id="wrapper">

            <div class="login-register" <?php if (isset($_COOKIE['store_panel_color'])){ ?>
                style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>; <?php } ?>">

                <div class="login-logo text-center py-3" style="margin-top:5%;">

                    <a href="#"
                        style="display: inline-block;background: #fff;padding: 10px;border-radius: 5px;"><img
                            src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

                </div>

                <div class="login-box card" style="margin-bottom:0%;">

                    <div class="card-body">

                        @if (count($errors) > 0)
                            @foreach ($errors->all() as $message)
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    <span>{{ $message }}</span>
                                </div>
                            @endforeach
                        @endif

                        <form class="form-horizontal form-material" name="login" id="login-box" action="#">
                            @csrf
                            <div class="box-title m-b-20">{{ __('Login') }}</div>
                            <div class="form-group ">
                                <div class="col-xs-12">
                                    <input class="form-control" placeholder="{{ __('Email Address') }}" id="email"
                                        type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        autofocus>
                                </div>
                                <div class="error email_error"></div>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input id="password" placeholder="{{ __('Password') }}" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">
                                </div>
                                <div class="error password_error"></div>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="error" id="password_required"></div>
                            </div>
                            <div class="forgot-password">
                                <p><a href="{{ url('forgot-password') }}" class="standard-link"
                                        target="_blank">{{ trans('lang.forgot_password') }}?</a></p>
                            </div>


                           

                            <div class="form-group text-center m-t-20">


                                <div class="col-xs-12">
                                    <button type="button" onclick="loginClick()" id="login_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    <button type="button" onclick="loginWithPhoneClick()" id="loginphon_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }} With Phone
                                    </button>
                                    <button type="button" onclick="googleAuth()"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">

                                        <i class="fa fa-google"> </i> Continue with Google

                                    </button>

                                    <div class="or-line mb-4 ">
                                        <span>OR</span>
                                    </div>
                                    <a href="{{ route('register') }}" id="signup_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ trans('lang.sign_up') }}
                                    </a>
                                    <a href="{{ route('register.phone') }}"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary"
                                        id="btn-signup-phone">

                                        <i class="fa fa-phone"> </i> {{ trans('lang.signup_with_phone') }}

                                    </a>



                                </div>
                            </div>
                        </form>

                        <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                            action="#" style="display:none;">
                            @csrf
                            <div class="box-title m-b-20">{{ __('Login') }}</div>
                            <div class="form-group " id="phone-box">
                                <div class="col-xs-12">
                                    <select name="country" id="country_selector">
                                        <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                        <?php $selected = ''; ?>
                                        <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                            value="<?php echo $keycy; ?>">
                                            +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                        <?php } ?>
                                    </select>
                                    <input class="form-control" placeholder="Phone" id="phone" type="text"
                                        class="form-control" name="phone" value="{{ old('phone') }}" required
                                        autocomplete="phone" autofocus>
                                </div>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group " id="otp-box" style="display:none;">
                                <input class="form-control" placeholder="OTP" id="verificationcode" type="text"
                                    class="form-control" name="otp" value="{{ old('otp') }}" required
                                    autocomplete="otp" autofocus>
                            </div>
                            <div id="recaptcha-container" style="display:none;"></div>

                            <div class="form-group text-center m-t-20">
                                <div class="col-xs-12">
                                    <button type="button" style="display:none;" onclick="applicationVerifier()"
                                        id="verify_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        OTP Verify
                                    </button>
                                    <button type="button" style="display:none;" onclick="sendOTP()"
                                        id="sendotp_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        Send OTP
                                    </button>
                                    <button type="button" onclick="loginBackClick()"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }} With Email
                                    </button>
                                    <div class="error" id="password_required_new"></div>

                                </div>
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
            var subscriptionModel = false;
            var onlyPhoneNumber = '';
            var businessModel = database.collection('settings').doc("vendor");

            businessModel.get().then(async function(snapshots) {

                var businessModelSettings = snapshots.data();

                if (businessModelSettings.hasOwnProperty('subscription_model') &&

                    businessModelSettings.subscription_model == true) {

                    subscriptionModel = true;

                }

            });
            
            var commissionModel = false;

            function loginClick() {

                var email = $("#email").val();
                var password = $("#password").val();
                $(".email_error").hide();

                $(".password_error").hide();
                if(email=='') {

                    $(".email_error").show();

                    $(".email_error").html("");

                    $(".email_error").append("<p>{{ trans('lang.enter_owners_email') }}</p>");

                    window.scrollTo(0,0);

                    return;

                } else if(password=='') {

                    $(".password_error").show();

                    $(".password_error").html("");

                    $(".password_error").append("<p>{{ trans('lang.enter_owners_password_error') }}</p>");

                    window.scrollTo(0,0);

                    return;

                }
                firebase.auth().signInWithEmailAndPassword(email, password).then(function(result) {
                        // var userEmail = result.user.email;
                        var userEmail = result.user.email.toLowerCase().trim(); // Convert email to lowercase newly added
                        console.log("User Email: ", userEmail);
                        database.collection("users").where("email", "==", userEmail).get().then(async function(snapshots) {
                            var userData = snapshots.docs[0].data();
                            if (userData.active == true) {
                                if (userData.role == "vendor") {
                                    if (userData.hasOwnProperty('sectionId') && userData.sectionId != null && userData.sectionId != '') {
                                        await database.collection('sections').where('id', '==', userData.sectionId).get().then(async function(snapshots) {
                                            var section_data = snapshots.docs[0].data();
                                            if (section_data.adminCommision != null && section_data
                                                .adminCommision != '') {
                                                if (section_data.adminCommision.enable) {
                                                    commissionModel = true;
                                                }
                                            }
                                        });
                                    }
                                    var userToken = result.user.getIdToken();
                                    var uid = result.user.uid;
                                    var user = userData.id;
                                    var firstName = userData.firstName;
                                    var lastName = userData.lastName;
                                    var imageURL = userData.profilePictureURL;
                                    if (subscriptionModel || commissionModel) {
                                        if (userData.hasOwnProperty('subscriptionPlanId') && userData
                                            .subscriptionPlanId != '' && userData.subscriptionPlanId != null
                                        ) {
                                            var isSubscribed = 'true';
                                        } else {

                                            var isSubscribed = 'false';
                                        }
                                    } else {

                                        var isSubscribed = '';
                                    }
                                    var url = "{{ route('setToken') }}";
                                    $.ajax({
                                        type: 'POST',
                                        url: url,
                                        data: {
                                            id: uid,
                                            userId: user,
                                            email: email,
                                            password: password,
                                            firstName: firstName,
                                            lastName: lastName,
                                            profilePicture: imageURL,
                                            isSubscribed: isSubscribed
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(data) {
                                            if (data.access) {
                                                if (userData.hasOwnProperty('subscriptionPlanId') &&
                                                    userData.subscriptionPlanId != '' && userData
                                                    .subscriptionPlanId != null) {
                                                    window.location = "{{ route('dashboard') }}";
                                                } else {
                                                    if (subscriptionModel || commissionModel) {

                                                        window.location =
                                                            "{{ route('subscription-plan.show') }}";

                                                    } else {
                                                        window.location =
                                                            "{{ route('dashboard') }}";
                                                    }
                                                }

                                            }
                                        }
                                    })

                                } else {

                                }
                            } else {
                                $("#password_required").css('color', 'black').html(
                                    "<p>{{ trans('lang.waiting_for_approval') }}</p>");
                                return false;
                            }

                        })

                    })
                    .catch(function(error) {
                        $("#password_required").html(error.message);
                    });
                return false;
            }

            var provider = new firebase.auth.PhoneAuthProvider();

            function loginWithPhoneClick() {
                jQuery("#login-box").hide();
                jQuery("#login-with-phone-box").show();
                jQuery("#phone-box").show();
                jQuery("#recaptcha-container").show();
                jQuery("#sendotp_btn").show();
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {}
                });
            }

            function loginBackClick() {
                jQuery("#login-box").show();
                jQuery("#login-with-phone-box").hide();
                jQuery("#sendotp_btn").hide();
            }

            function sendOTP() {

                if (jQuery("#phone").val() && jQuery("#country_selector").val()) {
                    var phoneNumber = '+' + jQuery("#country_selector").val() + '' + jQuery("#phone").val();
                    onlyPhoneNumber = jQuery("#phone").val();
                    database.collection("users").where("phoneNumber", "==", phoneNumber).where("role", "==", 'vendor').where(
                        "active", "==", true).get().then(async function(snapshots) {
                        if (snapshots.docs.length) {
                            var userData = snapshots.docs[0].data();
                            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                                .then(function(confirmationResult) {
                                    window.confirmationResult = confirmationResult;
                                    if (confirmationResult.verificationId) {
                                        jQuery("#phone-box").hide();
                                        jQuery("#recaptcha-container").hide();
                                        jQuery("#otp-box").show();
                                        jQuery("#verify_btn").show();
                                    }
                                });
                        } else {
                            jQuery("#password_required_new").html("User is inactive or not found.");
                        }
                    });
                }
            }

            function applicationVerifier() {
                window.confirmationResult.confirm(document.getElementById("verificationcode").value)
                    .then(function(result) {                        
                        database.collection("users").where('phoneNumber', "==", result.user.phoneNumber /* onlyPhoneNumber */).get().then(
                            async function(snapshots_login) {
                                userData = snapshots_login.docs[0].data();
                                if (userData) {
                                    if (userData.role == "vendor" && userData.active == true) {
                                        if (userData.hasOwnProperty('sectionId') && userData.sectionId != null && userData.sectionId != '') {
                                            await database.collection('sections').where('id', '==', userData.sectionId).get().then(async function(snapshots) {
                                                var section_data = snapshots.docs[0].data();
                                                if (section_data.adminCommision != null && section_data
                                                    .adminCommision != '') {
                                                    if (section_data.adminCommision.enable) {
                                                        commissionModel = true;
                                                    }
                                                }
                                            });
                                        }
                                        var uid = userData.id;
                                        var user = userData.id;
                                        var firstName = userData.firstName;
                                        var phoneNumber = userData.phoneNumber;
                                        var lastName = userData.lastName;
                                        var imageURL = '';
                                        if (subscriptionModel || commissionModel) {
                                            if (userData.hasOwnProperty('subscriptionPlanId') && userData
                                                .subscriptionPlanId != '' && userData.subscriptionPlanId != null
                                            ) {
                                                var isSubscribed = 'true';
                                            } else {

                                                var isSubscribed = 'false';
                                            }
                                        } else {

                                            var isSubscribed = '';
                                        }
                                        var url = "{{ route('setToken') }}";

                                        $.ajax({
                                            type: 'POST',
                                            url: url,
                                            data: {
                                                id: uid,
                                                userId: user,
                                                email: phoneNumber,
                                                password: '',
                                                firstName: firstName,
                                                lastName: lastName,
                                                profilePicture: imageURL,
                                                isSubscribed: isSubscribed
                                            },
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            success: function(data) {
                                                if (data.access) {
                                                    if (userData.hasOwnProperty('subscriptionPlanId') &&
                                                        userData.subscriptionPlanId != '' && userData
                                                        .subscriptionPlanId != null) {
                                                        window.location = "{{ route('dashboard') }}";
                                                    } else {
                                                        if (subscriptionModel || commissionModel) {

                                                            window.location =
                                                                "{{ route('subscription-plan.show') }}";

                                                        } else {
                                                            if (userData.hasOwnProperty('sectionId') &&
                                                                userData.sectionId != null &&
                                                                userData.sectionId != '') {
                                                                window.location =
                                                                    "{{ route('dashboard') }}";
                                                            } else {
                                                                window.location =
                                                                    "{{ route('store') }}";
                                                            }

                                                        }
                                                    }
                                                }
                                            }
                                        });

                                    } else {
                                        jQuery("#password_required_new").html("User is inactive or not found.");
                                    }
                                }
                            })
                    }).catch(function(error) {
                        jQuery("#password_required_new").html(error.message);
                    });
            }            

            var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);

            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() +
                    '.png" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }

            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }

                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
                var $state = $(
                    '<span><img class="img-flag" /> <span></span></span>'
                );
                $state.find("span").text(state.text);
                $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");

                return $state;
            }

            jQuery(document).ready(function() {

                jQuery("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "Select Country",
                    allowClear: true
                });

            });
            var ref = database.collection('settings').doc("globalSettings");

            $(document).ready(function() {
                ref.get().then(async function(snapshots) {
                    var globalSettings = snapshots.data();
                    store_panel_color = globalSettings.store_panel_color;
                    setCookie('store_panel_color', store_panel_color, 365);
                })

            });

            function setCookie(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                let expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            function googleAuth() {
                var provider=new firebase.auth.GoogleAuthProvider();
                firebase.auth().signInWithPopup(provider)
                    .then(function(result) {
                        var user=result.user;
                        saveUserData(user);
                    }).catch(function(error) {
                        console.error("Google Sign-In Error:",error.message);

                    });
            }



            function saveUserData(user) {
                jQuery('#data-table_processing').show();
                database.collection("users").doc(user.uid).get().then(async function(snapshots_login) {
                    var userData=snapshots_login.data();
                    if(userData) {
                        if(userData.role=="vendor"&&userData.active) {
                            var uid=userData.id;
                            var firstName=userData.firstName;
                            var phoneNumber=userData.phoneNumber;
                            var lastName=userData.lastName;
                            var imageURL='';
                            var documentVerify=userData.hasOwnProperty('isDocumentVerify')? userData.isDocumentVerify:false;
                            setCookie('documentVerify',documentVerify);
                            if(subscriptionModel||commisionModel) {
                                if(userData.hasOwnProperty('subscriptionPlanId')&&userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                                    var isSubscribed='true';
                                } else {
                                    var isSubscribed='false';
                                }
                            } else {
                                var isSubscribed='';
                            }
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('setToken') }}",
                                data: {
                                    id: uid,
                                    userId: uid,
                                    email: phoneNumber,
                                    password: '',
                                    firstName: firstName,
                                    lastName: lastName,
                                    profilePicture: imageURL,
                                    provider: "google",
                                    isSubscribed:isSubscribed
                                },

                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },

                                success: function(data) {
                                    if(data.access) {
                                        jQuery('#data-table_processing').hide();
                                        if(userData.hasOwnProperty('subscriptionPlanId')&&userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                                            if(documentVerify===true|| documentVerificationEnable===false) {
                                                window.location="{{ route('dashboard') }}";
                                            } else {
                                                window.location="{{ route('vendors.document') }}";
                                            }

                                        } else {
                                            if(subscriptionModel||commisionModel) {
                                                window.location=
                                                    "{{ route('subscription-plan.show') }}";

                                            } else {
                                                if(documentVerify==true|| documentVerificationEnable==false) {
                                                    window.location= "{{ route('dashboard') }}";
                                                } else {
                                                    window.location= "{{ route('vendors.document') }}";
                                                }
                                            }
                                        }
                                    } else {
                                        jQuery('#data-table_processing').hide();
                                        $(".email_error").hide();
                                        $(".password_error").show();
                                        $(".password_error").html("");
                                        window.scrollTo(0,0);
                                        $(".password_error").append( "<p>{{ trans('lang.set_token_error') }}</p>");

                                    }

                                },

                                error: function() {
                                    jQuery('#data-table_processing').hide();
                                    $(".email_error").hide();
                                    $(".password_error").show();
                                    $(".password_error").html("");
                                    window.scrollTo(0,0);
                                    $(".password_error").append(
                                        "<p>{{ trans('lang.set_token_error') }}</p>");
                                }

                            });

                        } else {
                            jQuery('#data-table_processing').hide();
                            $(".email_error").hide();
                            $(".password_error").show();
                            $(".password_error").html("");
                            window.scrollTo(0,0);
                            $(".password_error").append("<p>{{ trans('lang.user_active_error') }}</p>");
                        }

                    } else {
                        var loginType='google';
                        var phoneNumber=user.phoneNumber||'';
                        var firstName=user.displayName? user.displayName.split(' ')[0]:'';
                        var lastName=user.displayName? user.displayName.split(' ')[1]:'';
                        var uuid=user.uid;
                        var email=user.email||'';
                        var photoURL=user.photoURL||'';
                        var createdAtman=firebase.firestore.Timestamp.fromDate(new Date());
                        var redirectUrl=
                            `{{ url('register') }}?uuid=${encodeURIComponent(uuid)}&loginType=${encodeURIComponent(loginType)}&phoneNumber=${encodeURIComponent(phoneNumber)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}&photoURL=${encodeURIComponent(photoURL)}&createdAt=${createdAtman.toDate()}`;
                        jQuery('#data-table_processing').hide();
                        window.location.href=redirectUrl;
                    }

                }).catch(function(error) {
                    console.log(error);
                    jQuery('#data-table_processing').hide();
                    $(".email_error").hide();
                    $(".password_error").show();
                    $(".password_error").html("");
                    window.scrollTo(0,0);
                    $(".password_error").append("<p>"+error.message+"</p>");

                });

            }
        </script>

    </body>

</html>
