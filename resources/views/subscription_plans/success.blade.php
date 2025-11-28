<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <!-- @yield('style') -->
</head>

<body>
    <?php if (isset($_COOKIE['store_panel_color'])) { ?>
    <style type="text/css">
        a,
        a:hover,
        a:focus {
            color:
                <?php    echo $_COOKIE['store_panel_color']; ?>
            ;
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
            background:
                <?php    echo $_COOKIE['store_panel_color']; ?>
            ;
            border: 1px solid<?php    echo $_COOKIE['store_panel_color']; ?>;
        }

        [type="checkbox"]:checked+label::before {
            border-right: 2px solid<?php    echo $_COOKIE['store_panel_color']; ?>;
            border-bottom: 2px solid<?php    echo $_COOKIE['store_panel_color']; ?>;
        }

        .form-material .form-control,
        .form-material .form-control.focus,
        .form-material .form-control:focus {
            background-image: linear-gradient(<?php    echo $_COOKIE['store_panel_color']; ?>,
                    <?php    echo $_COOKIE['store_panel_color']; ?>
                ), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
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
            background:
                <?php    echo $_COOKIE['store_panel_color']; ?>
            ;
            border-color:
                <?php    echo $_COOKIE['store_panel_color']; ?>
            ;
            box-shadow: 0 0 0 0.2rem<?php    echo $_COOKIE['store_panel_color']; ?>;
        }

        .error {
            color: red;
        }
    </style>
    <?php } ?>
    <div class="siddhi-checkout">
        <div class="container position-relative">
            <div id="data-table_processing" class="page-overlay" style="display:none;">
                <div class="overlay-text">
                    <img src="{{asset('images/spinner.gif')}}">
                </div>
            </div>
            <div class="py-5 row">
                <div class="col-md-12 mb-3">
                    <div>
                        <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                            <div class="siddhi-cart-item-profile bg-white p-3">
                                <div class="card card-default">
                                    <?php $authorName = @$cart['cart_order']['authorName']; ?>
                                    @if ($message = Session::get('success'))
                                                                        <div
                                                                            class="py-5 linus-coming-soon d-flex justify-content-center align-items-center">
                                                                            <div class="col-md-6">
                                                                                <div class="bg-white rounded text-center p-4 shadow-sm">
                                                                                    <h1 class="display-1 mb-4">🎉</h1>
                                                                                    <h1 class="font-weight-bold"><?php    if (@$authorName) {
        echo @$authorName.',';
    } ?>
                                                                                        {{ trans('lang.subscription_plan_activated_successfully') }}
                                                                                    </h1>
                                                                                    <a href="{{ url('/dashboard') }}"
                                                                                        class="btn rounded btn-primary btn-lg btn-block">{{ trans('lang.go_to_home') }}</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            var database=firebase.firestore();
            var id_order=database.collection('tmp').doc().id;
            var userId="<?php    echo $id; ?>";
            var userDetailsRef=database.collection('users').where('id',"==",userId);


            <?php    if (@$cart['payment_status'] == true && ! empty(@$cart['cart_order']['order_json'])) { ?>
            $("#data-table_processing").show();
            var order_json='<?php        echo json_encode($cart['cart_order']['order_json']); ?>';
            order_json=JSON.parse(order_json);
            var planId=order_json.planId;
            var sectionId=order_json.sectionId;
            var planData='';
            var expiryDay='';
            var vendorId=null;
            $(document).ready(async function() {
                await database.collection('users').where('id','==',userId).get().then(async function(snapshot) {
                    var userData=snapshot.docs[0].data();
                    if(userData.hasOwnProperty('vendorID')&&userData.vendorID!=''&&userData.vendorID!=null) {
                        vendorId=userData.vendorID;
                    }
                });
                var planRef=database.collection('subscription_plans').where('id','==',planId);
                await planRef.get().then(async function(snapshot) {
                    planData=snapshot.docs[0].data();
                    if(planData.expiryDay!='-1') {
                    var currentDate=new Date();
                    currentDate.setDate(currentDate.getDate()+parseInt(planData.expiryDay));
                    expiryDay=firebase.firestore.Timestamp.fromDate(currentDate);
                    }else{
                        expiryDay=null;
                    }
                });
                finalCheckout();
            });



            function finalCheckout() {
                userDetailsRef.get().then(async function(userSnapshots) {
                    var userDetails=userSnapshots.docs[0].data();
                    payment_method='<?php        echo $payment_method; ?>';
                    var createdAt=firebase.firestore.FieldValue.serverTimestamp();
                    await database.collection('users').doc(userId).update({
                        'subscription_plan': planData,
                        'subscriptionPlanId': planId,
                        'subscriptionExpiryDate': expiryDay,
                        'sectionId':sectionId
                    }).then(async function(result) {
                        if(vendorId!=null) {
                            await database.collection('vendors').doc(vendorId).update({
                                'subscription_plan': planData,
                                'subscriptionPlanId': planId,
                                'subscriptionExpiryDate': expiryDay,
                                'subscriptionTotalOrders':planData.orderLimit,
                                'section_id':sectionId
                            })
                        }
                        await database.collection('subscription_history').doc(id_order).set({
                            'id': id_order,
                            'user_id': userId,
                            'expiry_date': expiryDay,
                            'createdAt': createdAt,
                            'subscription_plan': planData,
                            'payment_type': payment_method
                        }).then(async function(snapshot) {
                            var url="{{ route('setSubcriptionFlag') }}";

                            $.ajax({

                                type: 'POST',
                                url: url,
                                data: {
                                    email: "<?php        echo Auth::user()->email; ?>",
                                    isSubscribed: 'true'
                                },

                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },

                                success: function(data) {
                                    if(data.access) {
                                        $("#data-table_processing").hide();
                                        window.location.href='{{ route('dashboard') }}';
                                    }
                                }


                            });

                        })
                    });
                });
            }
            <?php    } ?>
        </script>
    @endif
</body>