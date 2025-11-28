@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.subscription_details') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item">{{ trans('lang.subscription_details') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{ trans('lang.processing') }}
                </div>
                <div class="card-body pb-5 p-0">
                    <div class="order_detail" id="order_detail">
                        <div class="order_detail-top">
                            <div class="row">
                                <div class="order_edit-genrl col-lg-7 col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="order_detail-top-box">
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.plan_name') }}
                                                            : </strong><span id="plan_name"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.plan_price') }}
                                                            : </strong><span id="plan_price"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.item_limit') }}
                                                            : </strong><span id="item_limit"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.order_limit') }}
                                                            : </strong><span id="order_limit"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.active_at') }}
                                                            : </strong><span id="active_at"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.expire_at') }}
                                                            : </strong><span id="expire_at"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col payment_method">
                                                    <label class="col-12 control-label"><strong>{{ trans('lang.payment_methods') }}
                                                            : </strong><span id="payment_method"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="order_addre-edit col-lg-5 col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h3>{{ trans('lang.features') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="address order_detail-top-box features">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="order_addre-edit">
                                        <div class="card mt-4">
                                            <div class="card-header bg-white">
                                                <h3>{{ trans('lang.description') }}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="address order_detail-top-box">
                                                    <p>
                                                        <span id="description"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <a href="{!! route('my-subscriptions') !!}" class="btn btn-default"><i
                                class="fa fa-undo"></i>{{ trans('lang.back') }}
                        </a>
                    </div>
                </div>
        </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('style')
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var id = "<?php echo $id; ?>";
        var adminCommission_val = 0;
        var database = firebase.firestore();
        var ref = database.collection('subscription_history').where("id", "==", id);
        var currentCurrency = '';
        var currencyAtRight = false;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var decimal_degits = 0;
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        var placeholderImage = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        $(document).ready(function() {
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            jQuery("#data-table_processing").show();
            ref.get().then(async function(snapshot) {
                var data = snapshot.docs[0].data();
                $('#plan_name').html(data.subscription_plan.name);
                if (currencyAtRight) {
                    price = parseFloat(data.subscription_plan.price)
                        .toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    price = currentCurrency + "" + parseFloat(data
                        .subscription_plan.price).toFixed(
                        decimal_degits);
                }
                $('#plan_price').html(price);
                $('#item_limit').html((data.subscription_plan.itemLimit!='-1') ? data.subscription_plan.itemLimit : "{{trans('lang.unlimited')}}");
                $('#order_limit').html((data.subscription_plan.orderLimit!='-1') ? data.subscription_plan.orderLimit : "{{trans('lang.unlimited')}}");
                var date = '';
                var time = '';
                if (data.hasOwnProperty("expiry_date") && data
                    .expiry_date != '' && data.expiry_date != null ) {
                    try {
                        date = data.expiry_date.toDate()
                            .toDateString();
                        time = data.expiry_date.toDate()
                            .toLocaleTimeString('en-US');
                    } catch (err) {
                    }
                    $('#expire_at').html(date + ' ' + time);
                }else{
                    $('#expire_at').html("{{trans("lang.unlimited")}}");
                }
                
                var date = '';
                var time = '';
                if (data.hasOwnProperty("createdAt") && data.createdAt != '' && data.createdAt !=
                    null) {
                    try {
                        date = data.createdAt.toDate()
                            .toDateString();
                        time = data.createdAt.toDate()
                            .toLocaleTimeString('en-US');
                    } catch (err) {
                    }
                }
                $('#active_at').html(date + ' ' + time);
                if (data.payment_type.toString().toLowerCase() == "stripe") {
                    image = '{{ asset('images/stripe.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "razorpay") {
                    image = '{{ asset('images/razorpay.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "paypal") {
                    image = '{{ asset('images/paypal.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "payfast") {
                    image = '{{ asset('images/payfast.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "paystack") {
                    image = '{{ asset('images/paystack.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "flutterwave") {
                    image = '{{ asset('images/flutter_wave.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "mercadopago") {
                    image = '{{ asset('images/marcadopago.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "wallet") {
                    image = '{{ asset('images/emart_wallet.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "paytm") {
                    image = '{{ asset('images/paytm.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"  >';
                } else if (data.payment_type.toString().toLowerCase() == "xendit") {
                    image = '{{ asset('images/xendit.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "orangepay") {
                    image = '{{ asset('images/orangeMoney.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else if (data.payment_type.toString().toLowerCase() == "midtrans") {
                    image = '{{ asset('images/midtrans.png') }}';
                    payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" >';
                } else {
                    payment_method = data.payment_type;
                }
                $('#payment_method').html(payment_method);
                var html = '';
                if (data.subscription_plan.hasOwnProperty('features') && data.subscription_plan.features!=null ) {
                    const translations = {
                        chatingOption: "{{ trans('lang.chating_option') }}",
                        generateQrCode: "{{ trans('lang.generate_qr_code') }}",
                        mobileAppAccess: "{{ trans('lang.mobile_app_access') }}"
                    };
                    var features = data.subscription_plan.features;
                    html += `
                                        ${features.chat? `<li>${translations.chatingOption}</li>`:''}
                                        ${features.qrCodeGenerate? `<li>${translations.generateQrCode}</li>`:''}
                                        ${features.ownerMobileApp? `<li>${translations.mobileAppAccess}</li>`:''}       
                            `;
                } 
                if (data.subscription_plan.hasOwnProperty('plan_points') && data.subscription_plan.plan_points!=null ) {
                    
                    data.subscription_plan.plan_points.map(async (list) => {
                        html += `<li>${list}</li>`
                    });
                }
                $('.features').html(html);
                $('#description').html(data.subscription_plan.description);
            });
            jQuery("#data-table_processing").hide();
        });
    </script>
@endsection
