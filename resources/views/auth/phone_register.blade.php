<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">


        <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
        <!-- <link rel="icon" id="favicon" type="image/x-icon" href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>"> -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png')}}">

        <!-- Fonts -->

        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">

        <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <!--  @yield('style')-->

        <?php if (isset($_COOKIE['store_panel_color'])){ ?>
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

            <?php } ?>
        </style>


    </head>

    <body>

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

            <?php if (isset($_COOKIE['store_panel_color'])){ ?>

            <div class="login-register" style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>;">
                <?php } else{ ?>

                <div class="login-register" style="background-color:#FF683A;">
                    <?php } ?>


                    <div class="login-logo text-center py-3" style="margin-top:5%;">

                        <a href="#"
                            style="display: inline-block;background: #fff ; padding: 10px;border-radius: 5px;"><img
                                src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

                    </div>

                    <div class="login-box card" style="margin-bottom:0%;">


                        <div class="card-body">
                            <div class="error_top"></div>
                            <div class="alert alert-success" style="display:none;"></div>

                            <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                                action="#">
                                @csrf
                                <div class="box-title m-b-20">{{ trans('lang.sign_up_with_us') }}</div>
                                <div class="form-group" id="firstName_div">

                                    <label for="firstName" class="text-dark">{{ trans('lang.first_name') }}</label>

                                    <input type="text" placeholder="Enter FirstName" class="form-control"
                                        id="firstName" required>
                                    <input type="hidden" id="hidden_fName" />
                                </div>

                                <div class="form-group" id="lastName_div">

                                    <label for="lastName" class="text-dark">{{ trans('lang.last_name') }}</label>

                                    <input type="text" placeholder="Enter LastName" class="form-control"
                                        id="lastName" required>
                                    <input type="hidden" id="hidden_lName" />
                                </div>

                                <div class="form-group" id="email_div">
                                    <label class="text-dark">{{trans('lang.email')}}</label>                                    
                                        <input type="email" placeholder="Enter Email" class="form-control user_email" id="email" required>
                                        <input type="hidden" id="hidden_email" />
                                </div>

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
                                    <div class="otp_error">

                                    </div>
                                </div>
                                <div id="recaptcha-container" style="display:none;"></div>

                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <button type="button" style="display:none;" onclick="applicationVerifier()"
                                            id="verify_btn"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_verify') }}
                                        </button>
                                        <button type="button" onclick="sendOTP()" id="send-code"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_send') }}
                                        </button>


                                    </div>
                                </div>
                            </form>
                            <div class="new-acc d-flex align-items-center justify-content-center mt-4 mb-3">

                                <a href="{{ url('login') }}">

                                    <p class="text-center m-0"> {{ trans('lang.already_an_account') }}
                                        {{ trans('lang.sign_in') }}</p>

                                </a>

                            </div>


                        </div>

                        <!-- </div>

            </div>

        </div> -->

                    </div>

                </div>
            </div>

        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('js/waves.js') }}"></script>
        <script src="{{ asset('js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('js/custom.min.js') }}"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
        <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
        <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>

        <script type="text/javascript">
            var database = firebase.firestore();
            var geoFirestore = new GeoFirestore(database);

            var createdAt = firebase.firestore.FieldValue.serverTimestamp();

            var vendor_active = false;
            var autoAprroveVendor = database.collection('settings').doc("vendor");
            autoAprroveVendor.get().then(async function(snapshots) {
                var vendordata = snapshots.data();
                if (vendordata.auto_approve_vendor == true) {
                    vendor_active = true;
                }
            });

            var adminEmail = '';

            var emailSetting = database.collection('settings').doc('emailSetting');
            var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');

            var emailTemplatesData = null;

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

            jQuery(document).ready(async function() {

                await email_templates.get().then(async function(snapshots) {
                    emailTemplatesData = snapshots.docs[0].data();
                });

                await emailSetting.get().then(async function(snapshots) {
                    var emailSettingData = snapshots.data();

                    adminEmail = emailSettingData.userName;
                });

                jQuery("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "Select Country",
                    allowClear: true
                });
            });


            var registerPhoneNumber = '';

            function sendOTP() {
                var firstName = $('#firstName').val();
                var lastName  = $('#lastName').val();
                var email     = $('#email').val();
                var phone     = jQuery("#phone").val();
                var country   = jQuery("#country_selector").val();

                if (firstName == '') {
                    $(".error_top").show().html("<p>{{ trans('lang.enter_owners_name_error') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (phone == "") {
                    $(".error_top").show().html("<p>{{ trans('lang.enter_owners_phone') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (email == "") {
                    $(".error_top").show().html("<p>{{ trans('lang.enter_owners_email') }}</p>");
                    window.scrollTo(0, 0);
                    return;
                }

                registerPhoneNumber = '+' + country + phone;

                database.collection("users").where('phoneNumber', '==', registerPhoneNumber).get().then(function(snapshots) {
                    if (snapshots.docs.length > 0) {
                        $(".error_top").show().html("<p>Bu telefon raqam bilan allaqachon hisob mavjud.</p>");
                        window.scrollTo(0, 0);
                        return;
                    }

                    $('#hidden_fName').val(firstName);
                    $('#hidden_lName').val(lastName);
                    $('#hidden_email').val(email);

                    $('#send-code').prop('disabled', true).text('Yuborilmoqda...');

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('sendOtp') }}",
                        data: { phone: registerPhoneNumber },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            $('#firstName_div').hide();
                            $('#lastName_div').hide();
                            $('#email_div').hide();
                            $('#phone-box').hide();
                            jQuery("#verify_btn").show();
                            jQuery("#otp-box").show();
                            $('#send-code').hide();
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'SMS yuborishda xatolik.';
                            $(".error_top").show().html("<p>" + msg + "</p>");
                            window.scrollTo(0, 0);
                            $('#send-code').prop('disabled', false).text('{{ trans("lang.otp_send") }}');
                        }
                    });
                });
            }

            function applicationVerifier() {
                var code = $('#verificationcode').val();
                if (code == "") {
                    $('.otp_error').html('OTP kodni kiriting.');
                    return;
                }

                jQuery("#verify_btn").prop('disabled', true).text('Tekshirilmoqda...');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('verifyOtp') }}",
                    data: { phone: registerPhoneNumber, otp: code },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: async function() {
                        var firstName = $('#hidden_fName').val();
                        var lastName  = $('#hidden_lName').val();
                        var email     = $('#hidden_email').val();
                        var uuid      = database.collection('users').doc().id;
                        var coordinates = new firebase.firestore.GeoPoint(0, 0);

                        try {
                            await database.collection("users").doc(uuid).set({
                                'email': email,
                                'firstName': firstName,
                                'lastName': lastName,
                                'id': uuid,
                                'phoneNumber': registerPhoneNumber,
                                'role': "vendor",
                                'profilePictureURL': "",
                                'vendorID': '',
                                'active': vendor_active,
                                'coordinates': new firebase.firestore.GeoPoint(0, 0),
                                'createdAt': createdAt,
                                'sectionId': '',
                                'isDocumentVerify': false,
                            });

                            // Email xabarnoma yuborish
                            if (emailTemplatesData) {
                                var formattedDate = new Date();
                                var month = formattedDate.getMonth() + 1;
                                var day   = formattedDate.getDate();
                                var year  = formattedDate.getFullYear();
                                month = month < 10 ? '0' + month : month;
                                day   = day < 10 ? '0' + day : day;
                                formattedDate = day + '-' + month + '-' + year;

                                var message = emailTemplatesData.message;
                                message = message.replace(/{userid}/g, uuid);
                                message = message.replace(/{username}/g, firstName + ' ' + lastName);
                                message = message.replace(/{useremail}/g, email);
                                message = message.replace(/{userphone}/g, registerPhoneNumber);
                                message = message.replace(/{date}/g, formattedDate);

                                await sendEmail("{{ url('send-email') }}", emailTemplatesData.subject, message, [adminEmail]);
                            }

                            if (vendor_active == false) {
                                $(".alert-success").show().html("<p>{{ trans('lang.signup_waiting_approval') }}</p>");
                            } else {
                                $(".alert-success").show().html("<p>{{ trans('lang.thank_you_signup_msg') }}</p>");
                            }
                            window.scrollTo(0, 0);
                            setTimeout(function() {
                                window.location.href = '{{ route('login') }}';
                            }, 5000);

                        } catch(error) {
                            console.error("Register error:", error);
                            var errMsg = error && error.message ? error.message : JSON.stringify(error);
                            $(".otp_error").html("Xatolik: " + errMsg);
                            jQuery("#verify_btn").prop('disabled', false).text('{{ trans("lang.otp_verify") }}');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'OTP xato.';
                        $('.otp_error').html(msg);
                        jQuery("#verify_btn").prop('disabled', false).text('{{ trans("lang.otp_verify") }}');
                    }
                });
            }

            async function sendEmail(url, subject, message, recipients) {

                var checkFlag = false;

                await $.ajax({

                    type: 'POST',
                    data: {
                        subject: subject,
                        message: message,
                        recipients: recipients
                    },
                    url: url,
                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        checkFlag = true;
                    },
                    error: function(xhr, status, error) {
                        checkFlag = true;
                    }
                });

                return checkFlag;

            }
        </script>
