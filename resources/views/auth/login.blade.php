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

                        <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                            action="#">
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
                                    <button type="button" onclick="sendOTP()"
                                        id="sendotp_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        Send OTP
                                    </button>
                                    <div class="error" id="password_required_new"></div>
                                    <div class="or-line mb-4 mt-3">
                                        <span>OR</span>
                                    </div>
                                    <a href="{{ route('register.phone') }}"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        <i class="fa fa-phone"> </i> {{ trans('lang.signup_with_phone') }}
                                    </a>
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

            var loginPhoneNumber = '';

            function normalizePhone(phone) {
                return (phone || '').toString().replace(/\s+/g, '').trim();
            }

            function pickVendorUserFromSnapshots(snapshots, phone) {
                var matchedUser = null;
                var normalizedPhone = normalizePhone(phone);

                snapshots.docs.forEach(function(doc) {
                    var data = doc.data() || {};
                    var dataPhone = normalizePhone(data.phoneNumber);
                    var isVendor = data.role && data.role.toLowerCase() === 'vendor';
                    var isActive = data.active !== false && data.isActive !== false;

                    console.log('[login debug] role:', data.role, '| phone:', data.phoneNumber, '| active:', data.active, '| isActive:', data.isActive);

                    if (!matchedUser && isVendor && dataPhone === normalizedPhone && isActive) {
                        matchedUser = data;
                    }
                });

                return matchedUser;
            }

            function sendOTP() {
                var phone = jQuery("#phone").val();
                var countryCode = jQuery("#country_selector").val();

                if (!phone || !countryCode) {
                    jQuery("#password_required_new").html("Telefon raqamini kiriting.");
                    return;
                }

                loginPhoneNumber = '+' + countryCode + phone;
                jQuery("#password_required_new").html("");
                jQuery("#sendotp_btn").prop('disabled', true).text('Yuborilmoqda...');

                // Avval Firestore da foydalanuvchi borligini tekshiramiz
                database.collection("users")
                    .where("phoneNumber", "==", loginPhoneNumber)
                    .get().then(function(snapshots) {
                        var userData = pickVendorUserFromSnapshots(snapshots, loginPhoneNumber);

                        if (!userData) {
                            jQuery("#password_required_new").html("Foydalanuvchi topilmadi yoki faol emas.");
                            jQuery("#sendotp_btn").prop('disabled', false).text('Send OTP');
                            return;
                        }

                        // Eskiz orqali OTP yuborish
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('sendOtp') }}",
                            data: { phone: loginPhoneNumber },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(data) {
                                if (data.auto_verified) {
                                    // SMS yubora olmadi — session orqali avtomatik tasdiq
                                    jQuery("#verificationcode").val('auto');
                                    applicationVerifier();
                                    return;
                                }
                                jQuery("#phone-box").hide();
                                jQuery("#otp-box").show();
                                jQuery("#verify_btn").show();
                                jQuery("#sendotp_btn").hide();
                            },
                            error: function(xhr) {
                                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'SMS yuborishda xatolik.';
                                jQuery("#password_required_new").html(msg);
                                jQuery("#sendotp_btn").prop('disabled', false).text('Send OTP');
                            }
                        });
                    }).catch(function(err) {
                        jQuery("#password_required_new").html(err.message);
                        jQuery("#sendotp_btn").prop('disabled', false).text('Send OTP');
                    });
            }

            function applicationVerifier() {
                var otp = jQuery("#verificationcode").val();
                if (!otp) {
                    jQuery("#password_required_new").html("OTP kodni kiriting.");
                    return;
                }

                jQuery("#verify_btn").prop('disabled', true).text('Tekshirilmoqda...');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('verifyOtp') }}",
                    data: { phone: loginPhoneNumber, otp: otp },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        // OTP to'g'ri - Firestore dan user ma'lumotini olamiz
                        database.collection("users")
                            .where('phoneNumber', '==', loginPhoneNumber)
                            .get().then(async function(snapshots_login) {
                                var userData = pickVendorUserFromSnapshots(snapshots_login, loginPhoneNumber);
                                if (userData) {
                                    if (userData.hasOwnProperty('sectionId') && userData.sectionId != null && userData.sectionId != '') {
                                        await database.collection('sections').where('id', '==', userData.sectionId).get().then(async function(snaps) {
                                            if (snaps.docs.length > 0) {
                                                var section_data = snaps.docs[0].data();
                                                if (section_data.adminCommision && section_data.adminCommision.enable) {
                                                    commissionModel = true;
                                                }
                                            }
                                        });
                                    }

                                    var isSubscribed = '';
                                    if (subscriptionModel || commissionModel) {
                                        isSubscribed = (userData.hasOwnProperty('subscriptionPlanId') && userData.subscriptionPlanId) ? 'true' : 'false';
                                    }

                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('setToken') }}",
                                        data: {
                                            id: userData.id,
                                            userId: userData.id,
                                            email: loginPhoneNumber,
                                            password: '',
                                            firstName: userData.firstName,
                                            lastName: userData.lastName,
                                            profilePicture: '',
                                            isSubscribed: isSubscribed
                                        },
                                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                        success: function(res) {
                                            if (res.access) {
                                                // Save vendor Firestore ID before redirecting
                                                database.collection('vendors')
                                                    .where('author', '==', userData.id)
                                                    .get().then(function(vendorSnaps) {
                                                        var firestoreVendorId = '';
                                                        if (!vendorSnaps.empty) {
                                                            firestoreVendorId = vendorSnaps.docs[0].data().id || '';
                                                        }
                                                        $.ajax({
                                                            type: 'POST',
                                                            url: "{{ route('saveVendorFirestoreId') }}",
                                                            data: { firestore_vendor_id: firestoreVendorId },
                                                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                                            complete: function() {
                                                                if (userData.hasOwnProperty('subscriptionPlanId') && userData.subscriptionPlanId) {
                                                                    window.location = "{{ route('dashboard') }}";
                                                                } else if (subscriptionModel || commissionModel) {
                                                                    window.location = "{{ route('subscription-plan.show') }}";
                                                                } else if (userData.hasOwnProperty('sectionId') && userData.sectionId) {
                                                                    window.location = "{{ route('dashboard') }}";
                                                                } else {
                                                                    window.location = "{{ route('store') }}";
                                                                }
                                                            }
                                                        });
                                                    });
                                            }
                                        }
                                    });
                                } else {
                                    jQuery("#password_required_new").html("Foydalanuvchi topilmadi yoki faol emas.");
                                    jQuery("#verify_btn").prop('disabled', false).text('OTP Verify');
                                }
                            });
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'OTP xato.';
                        jQuery("#password_required_new").html(msg);
                        jQuery("#verify_btn").prop('disabled', false).text('OTP Verify');
                    }
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
        </script>

    </body>

</html>
