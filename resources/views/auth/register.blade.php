
@include('auth.default')
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
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
<div class="container">
    <div class="row page-titles ">

        <div class="col-md-12 align-self-center text-center">
            <h3 class="text-themecolor  ">{{trans('lang.sign_up_with_us')}}</h3>
        </div>

        <div class="card-body">
        <div id="data-table_processing" class="page-overlay" style="display:none;">
            <div class="overlay-text">
                <img src="{{asset('images/spinner.gif')}}">
            </div>
        </div>
            <div class="error_top"></div>
            <div class="alert alert-success" style="display:none;"></div>
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <fieldset>
                        <legend>{{trans('lang.admin_area')}}</legend>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_first_name"
                                    onkeypress="return chkAlphabets(event,'error')" required>
                                <div id="error" class="err"></div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_first_name_help") }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_last_name"
                                    onkeypress="return chkAlphabets(event,'error1')">
                                <div id="error1" class="err"></div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_last_name_help") }}
                                </div>
                            </div>
                        </div>


                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.email')}}</label>
                            <div class="col-7">
                                <input type="email" class="form-control user_email" required>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_email_help") }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.password')}}</label>
                            <div class="col-7">
                                <input type="password" class="form-control user_password" required>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_password_help") }}
                                </div>
                            </div>
                        </div>
                       
                        <div class="form-group row width-50" id="phone-box">
                            <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                            <div class="col-7">
                                <select name="country" id="country_selector">
                                    <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                    <?php $selected = ''; ?>
                                    
                                    <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                        value="<?php echo  $keycy; ?>">
                                        +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                    <?php } ?>
                                </select>
                                <input type="text" class="form-control user_phone"
                                    onkeypress="return chkAlphabets2(event,'error2')">
                                <div id="error2" class="err"></div>
                                <div class="form-text text-muted w-50">
                                    {{ trans("lang.user_phone_help") }}
                                </div>
                            </div>
                        </div>


                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.user_profile_picture')}}</label>
                            <input type="file" onChange="handleFileSelectowner(event)" class="col-7">
                            <div id="uploding_image_owner"></div>
                            <div class="uploaded_image_owner" style="display:none;"><img id="uploaded_image_owner"
                                    src="" width="150px" height="150px;"></div>
                        </div>
                        <input type="hidden" id="hidden_uuid" />
                        <input type="hidden" id="hidden_loginType" />

                    </fieldset>
                  
                </div>
            </div>
        </div>

        <div class="form-group col-12 text-center">
            <button type="button" class="btn btn-primary  create_vendor_btn"><i class="fa fa-save"></i>
                {{trans('lang.save')}}
            </button>

            <div class="or-line mb-4 ">
                <span>OR</span>
            </div>

            <div class="new-acc d-flex align-items-center justify-content-center">

                <a href="{{route('register.phone')}}" class="btn btn-primary" id="btn-signup-phone">

                    <i class="fa fa-phone"> </i> {{trans('lang.signup_with_phone')}}

                </a>

            </div>
            <a href="{{route('login')}}">

                <p class="text-center m-0"> {{trans('lang.already_an_account')}} {{trans('lang.sign_in')}}</p>

            </a>
        </div>


    </div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"
    integrity="sha512-VaRptAfSxXFAv+vx33XixtIVT9A/9unb1Q8fp63y1ljF+Sbka+eMJWoDAArdm7jOYuLQHVx5v60TQ+t3EA8weA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>

<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script>
    var database = firebase.firestore();
    var geoFirestore = new GeoFirestore(database);

    var photo = "";
    var menuPhotoCount = 0;
    var vendorMenuPhotos = "";
    var vendorOwnerId = "";
    var vendorOwnerOnline = false;
    var photocount = 0;
    var ref_sections = database.collection('sections');
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();

    var storageRef = firebase.storage().ref('images');
    var restaurnt_photos = [];
    var restaurant_photos_filename = [];
    var ownerphoto = '';
    var ownerFileName = '';
    var vendor_menu_photos = [];
    var vendor_menu_filename = [];

    var autoAprroveVendor = database.collection('settings').doc("vendor");

    var sections_list = [];

    var adminEmail = '';
    var userFullPhoneNumber = '';

    var emailSetting = database.collection('settings').doc('emailSetting');
    var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');

    var emailTemplatesData = null;

    var currentCurrency = '';
    var currencyAtRight = false;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
    });

    var dine_in_active = false;
    $(document).ready(async function () {
        jQuery("#data-table_processing").show();

        const urlParams = new URLSearchParams(window.location.search);          

        const firstName = urlParams.get('firstName') ? decodeURIComponent(urlParams.get('firstName')) : '';
        const lastName = urlParams.get('lastName') ? decodeURIComponent(urlParams.get('lastName')) : '';
        const email = urlParams.get('email') ? decodeURIComponent(urlParams.get('email')) : '';
        const uuid = urlParams.get('uuid') || '';
        const loginType = urlParams.get('loginType') || '';  
        
        if (firstName) {
            $(".user_first_name").val(firstName);          
        }
        if (lastName) {
            $(".user_last_name").val(lastName);          
        }
        if (email) {
            $(".user_email").val(email);
            $(".user_email").attr("readonly", true);           
        }
        if (uuid) {
            $("#hidden_uuid").val(uuid);
        }
        if (loginType) {
            $("#hidden_loginType").val(loginType);
        }
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
             // --- ADD THIS BLOCK TO SET DEFAULT COUNTRY CODE ---
    var globalSettingsRef = database.collection('settings').doc('globalSettings');
    globalSettingsRef.get().then(async function (snapshot) {
        var globalSettings = snapshot.data();
        if (globalSettings && globalSettings.defaultCountryCode) {
            var defaultPhoneCode = globalSettings.defaultCountryCode.replace('+', '').trim();

            // Find the option with matching phoneCode
            var $option = $("#country_selector option").filter(function() {
                return $(this).val() === defaultPhoneCode;
            });

            if ($option.length > 0) {
                $("#country_selector").val(defaultPhoneCode).trigger('change');
            } else {
                console.warn("Default country code not found in list:", defaultPhoneCode);
            }
        }
    }).catch(function (error) {
        console.error("Error fetching global settings: ", error);
    });
    // --- END OF DEFAULT COUNTRY LOGIC ---
        await email_templates.get().then(async function (snapshots) {
            emailTemplatesData = snapshots.docs[0].data();
        });

        await emailSetting.get().then(async function (snapshots) {
            var emailSettingData = snapshots.data();

            adminEmail = emailSettingData.userName;
        });
       
        jQuery("#data-table_processing").hide();
    })

    $(".create_vendor_btn").click(async function () {
        $(".error_top").hide();

        var userFirstName = $(".user_first_name").val();
        var userLastName = $(".user_last_name").val();
        // var email = $(".user_email").val();
        var email = $(".user_email").val().toLowerCase().trim(); // Convert email to lowercase newly added
        var password = $(".user_password").val();
        var country_code = $("#country_selector").val();
        var userPhone = $(".user_phone").val();
        userFullPhoneNumber = '+' + country_code + '' + userPhone;
       
        var vendor_active = false;
        await autoAprroveVendor.get().then(async function (snapshots) {
            var vendordata = snapshots.data();
            if (vendordata.auto_approve_vendor == true) {
                vendor_active = true;
            }
        });

        var user_name = userFirstName + " " + userLastName;        
        var user_id = '';
        var name = userFirstName + " " + userLastName;
        var existingUUID = $("#hidden_uuid").val();
        var loginType = $("#hidden_loginType").val();

        // If coming from Google, use the passed UUID; else, create new one
        if (loginType === "google" && existingUUID) {
            user_id = existingUUID;
        } else {
            user_id = "<?php echo uniqid(); ?>";
        }

        if (userFirstName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");
            window.scrollTo(0, 0);
        }else if (userLastName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_lastname_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (email == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");
            window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_password_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (userPhone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");
            window.scrollTo(0, 0);
        }  else {
            jQuery("#data-table_processing").show();         


                if (loginType === "google" && existingUUID) {
                    // Directly store user in Firestore using passed Google UUID
                    await storeImageData().then(async (IMG) => {
                        database.collection('users').doc(existingUUID).set({
                            'firstName': userFirstName,
                            'lastName': userLastName,
                            'email': email,
                            'phoneNumber': userFullPhoneNumber,
                            'profilePictureURL': IMG.ownerImage,
                            'role': 'vendor',
                            'id': existingUUID,
                            'active': vendor_active,
                            'vendorID': null,
                            'createdAt': createdAt,
                            'sectionId': '',
                            'isDocumentVerify': false,
                        }).then(async function () {
                            autoAprroveVendor.get().then(async function (snapshots) {
                                var formattedDate = new Date();
                                var month = formattedDate.getMonth() + 1;
                                var day = formattedDate.getDate();
                                var year = formattedDate.getFullYear();

                                month = month < 10 ? '0' + month : month;
                                day = day < 10 ? '0' + day : day;

                                formattedDate = day + '-' + month + '-' + year;

                                var message = emailTemplatesData.message;
                                message = message.replace(/{userid}/g, user_id);
                                message = message.replace(/{username}/g, userFirstName + ' ' + userLastName);
                                message = message.replace(/{useremail}/g, email);
                                message = message.replace(/{userphone}/g, userPhone);
                                message = message.replace(/{date}/g, formattedDate);

                                emailTemplatesData.message = message;

                                var url = "{{url('send-email')}}";

                                var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail]);

                                if (sendEmailStatus) {

                                    var vendordata = snapshots.data();
                                    if (vendordata.auto_approve_vendor == false) {
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append("<p>{{trans('lang.signup_waiting_approval')}}</p>");
                                        window.scrollTo(0, 0);
                                        setTimeout(function () {
                                            window.location.href = '{{ route("login")}}';
                                        }, 5000);
                                    } else {
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append("<p>{{trans('lang.thank_you_signup_msg')}}</p>");
                                        window.scrollTo(0, 0);
                                        setTimeout(function () {
                                            window.location.href = '{{ route("login")}}';
                                        }, 5000);
                                    }
                                }
                            });

                        });
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show().html("<p>" + err + "</p>");
                        window.scrollTo(0, 0);
                    });
                } else {
                    firebase.auth().createUserWithEmailAndPassword(email, password)
                        .then(async function (firebaseUser) {
                            user_id = firebaseUser.user.uid;
                            await storeImageData().then(async (IMG) => {
                                database.collection('users').doc(user_id).set({
                                    'firstName': userFirstName,
                                    'lastName': userLastName,
                                    // 'email': email,
                                    'email': email.toLowerCase().trim(),// Store email in lowercase newly added
                                    'phoneNumber': userFullPhoneNumber,
                                    'profilePictureURL': IMG.ownerImage,
                                    'role': 'vendor',
                                    'id': user_id,
                                    'active': vendor_active,
                                    'vendorID': null,
                                    createdAt: createdAt,
                                    'sectionId': null,
                                    'isDocumentVerify': false,
                                }).then(async function () {
                                    autoAprroveVendor.get().then(async function (snapshots) {
                                        var formattedDate = new Date();
                                        var month = formattedDate.getMonth() + 1;
                                        var day = formattedDate.getDate();
                                        var year = formattedDate.getFullYear();

                                        month = month < 10 ? '0' + month : month;
                                        day = day < 10 ? '0' + day : day;

                                        formattedDate = day + '-' + month + '-' + year;

                                        var message = emailTemplatesData.message;
                                        message = message.replace(/{userid}/g, user_id);
                                        message = message.replace(/{username}/g, userFirstName + ' ' + userLastName);
                                        message = message.replace(/{useremail}/g, email);
                                        message = message.replace(/{userphone}/g, userPhone);
                                        message = message.replace(/{date}/g, formattedDate);

                                        emailTemplatesData.message = message;

                                        var url = "{{url('send-email')}}";

                                        var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail]);

                                        if (sendEmailStatus) {

                                            var vendordata = snapshots.data();
                                            if (vendordata.auto_approve_vendor == false) {
                                                $(".alert-success").show();
                                                $(".alert-success").html("");
                                                $(".alert-success").append("<p>{{trans('lang.signup_waiting_approval')}}</p>");
                                                window.scrollTo(0, 0);
                                                setTimeout(function () {
                                                    window.location.href = '{{ route("login")}}';
                                                }, 5000);
                                            } else {
                                                $(".alert-success").show();
                                                $(".alert-success").html("");
                                                $(".alert-success").append("<p>{{trans('lang.thank_you_signup_msg')}}</p>");
                                                window.scrollTo(0, 0);
                                                setTimeout(function () {
                                                    window.location.href = '{{ route("login")}}';
                                                }, 5000);
                                            }
                                        }
                                    });

                                });
                            });
                        })
                        .catch(function (error) {
                            jQuery("#data-table_processing").hide();
                            $(".error_top").show().html("<p>" + error.message + "</p>");
                        });
                }

        }

    })

    async function sendEmail(url, subject, message, recipients) {

        var checkFlag = false;

        await $.ajax({

            type: 'POST',
            data: {
                subject: subject,
                message: btoa(message),
                recipients: recipients
            },
            url: url,
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });

        return checkFlag;

    }


    $(document).on("click", ".remove-btn", function () {
        var id = $(this).attr('data-id');
        var photo_remove = $(this).attr('data-img');
        $("#photo_" + id).remove();
        index = restaurnt_photos.indexOf(photo_remove);
        if (index > -1) {
            restaurnt_photos.splice(index, 1); // 2nd parameter means remove one item only
        }

    });

    function removeImage(photo_remove, photocount) {

        $("#photo_" + photocount).remove();
        index = restaurnt_photos.indexOf(photo_remove);
        if (index > -1) {
            restaurnt_photos.splice(index, 1); // 2nd parameter means remove one item only
        }
    }

    var storageRef = firebase.storage().ref('images');

    function handleFileSelectowner(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {

                var filePayload = e.target.result;
                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')

                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                ownerphoto = filePayload;
                ownerFileName = filename;
                $("#uploaded_image_owner").attr('src', ownerphoto);
                $(".uploaded_image_owner").show();

            };
        })(f);
        reader.readAsDataURL(f);
    }

    function chkAlphabets(event, msg) {
        if (!(event.which >= 97 && event.which <= 122) && !(event.which >= 65 && event.which <= 90)) {
            document.getElementById(msg).innerHTML = "Accept only Alphabets";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    function chkAlphabets2(event, msg) {
        if (!(event.which >= 48 && event.which <= 57)
        ) {
            document.getElementById(msg).innerHTML = "Accept only Number";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    function chkAlphabets3(event, msg) {
        if (!((event.which >= 48 && event.which <= 57) || (event.which >= 97 && event.which <= 122))) {
            document.getElementById(msg).innerHTML = "Special characters not accepted ";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }
    async function storeImageData() {
        var newPhoto = [];
        newPhoto['ownerImage'] = '';
        try {
            if (ownerphoto != '') {
                ownerphoto = ownerphoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(ownerFileName).putString(ownerphoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto['ownerImage'] = downloadURL;
                ownerphoto = downloadURL;
            }
        } catch (error) {
        }
        return newPhoto;
    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags"
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".svg");
        return $state;
    }



</script>
