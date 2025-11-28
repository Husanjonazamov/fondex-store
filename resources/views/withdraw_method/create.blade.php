@extends('layouts.app')
@section('content')
<div class="withdrawal-method page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{!! route('withdraw-method') !!}">{{trans('lang.withdrawal_method')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.add_withdrawal_method')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
               <div class="title-head mb-4 border-bottom pb-4"> 
                <h1 class="font-weight-medium">{{trans('lang.add_withdrawal_method')}}</h1>
                <p class="description">{{trans('lang.withdrawal_method_description')}}</p>
                <h3>{{trans('lang.available_method')}}</h3>
              </div>  
                <div id="available_method"></div>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center btm-btn">
        <a href="{!! route('withdraw-method') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
    </div>
</div>
<div class="modal fade" id="addMethodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="method_title"></span> &nbsp;- {{trans('lang.add_withdrawal_method')}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="error_top" style="display:none"></div>
                <form class="modal-form">
                    <div id="append_fields"></div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-method-btn">{{trans('lang.save')}}</a></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">{{trans('lang.close')}}</a></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<script>
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";
    var checkAlreadyExist = false;
    var existId = '';
    var razorpaySetupDone = false;
    var paypalSetupDone = false;
    var stripeSetupDone = false;
    var flutterwaveSetupDone = false;
    
    database.collection('withdraw_method').where('userId', '==', vendorUserId).get().then(async function (Snapshot) {
        if (Snapshot.docs.length > 0) {
            checkAlreadyExist = true;
            var data = Snapshot.docs[0].data();
            existId = data.id;
            if (data.stripe) {
                stripeSetupDone = true;
            }
            if (data.razorpay) {
                razorpaySetupDone = true;
            }
            if (data.paypal) {
                paypalSetupDone = true;
            }
            if (data.flutterwave) {
                flutterwaveSetupDone = true;
            }
        }
    })
    var ref = database.collection('settings').doc('stripeSettings');
    var razorpayData = database.collection('settings').doc('razorpaySettings');
    var paypalData = database.collection('settings').doc('paypalSettings');
    var flutterWaveSettings = database.collection('settings').doc('flutterWave');
    var stripeSettings = database.collection('settings').doc('stripeSettings');
    $(document).ready(function () {
        jQuery("#data-table_processing").show();
    
        stripeSettings.get().then(async function (Snapshots) {
            var stripe = Snapshots.data();
            var html = '';
            if (stripe.isWithdrawEnabled) {
                html = html + '<div class="d-flex align-items-center mb-3 border-bottom pb-3">';
                html = html + '<div class="image d-flex align-items-center"><img src="{!! asset("images/stripe.png") !!}" style="width: 90px;"><h4 class="d-block text-center mt-2 text-dark ml-3">{{trans('lang.stripe')}}</h4></div>';
                html = html + '<div class="ml-auto">';
                if(stripeSetupDone){
                    html = html + '<span class="badge badge-success p-3"><i class="fa fa-check-circle"></i> {{trans('lang.setup_done')}}</span>';
                }else{
                    html = html + '<a href="javascript:void(0)" data-method="Stripe" class="btn btn-danger setup_btn"  name="setup_btn">{{trans('lang.setup')}}</a>';
                }
                html = html + '</div>';
                html = html + '</div>';
                $('#available_method').append(html);
            }
        });
        razorpayData.get().then(async function (razorpaySnapshots) {
            var razorPay = razorpaySnapshots.data();
            var html = '';
            if (razorPay.isWithdrawEnabled) {
                html = html + '<div class="d-flex align-items-center mb-3 border-bottom pb-3">';
                html = html + '<div class="image d-flex align-items-center"><img src="{!! asset("images/razorpay.png") !!}" style="width: 90px;"><h4 class="d-block text-center mt-2 text-dark ml-3">{{trans('lang.razorpay')}}</h4></div>';
                html = html + '<div class="ml-auto">';
                if (razorpaySetupDone) {
                    html = html + '<span class="badge badge-success p-3"><i class="fa fa-check-circle"></i> {{trans('lang.setup_done')}}</span>';
                } else {
                    html = html + '<a href="javascript:void(0)"  data-method="RazorPay" class="btn btn-danger setup_btn"  name="setup_btn">{{trans('lang.setup')}}</a>';
                }
                html = html + '</div>';
                html = html + '</div>';
                $('#available_method').append(html);
            }
        });
        
        paypalData.get().then(async function (paypalSnapshots) {
            var paypal = paypalSnapshots.data();
            var html = '';
            if (paypal.isWithdrawEnabled) {
                html = html + '<div class="d-flex align-items-center mb-3 border-bottom pb-3">';
                html = html + '<div class="image d-flex align-items-center"><img src="{!! asset("images/paypal.png") !!}" style="width: 90px;"><h4 class="d-block text-center mt-2 text-dark ml-3">{{trans('lang.paypal')}}</h4></div>';
                html = html + '<div class="ml-auto">';
                if (paypalSetupDone) {
                    html = html + '<span class="badge badge-success p-3"><i class="fa fa-check-circle"></i> {{trans('lang.setup_done')}}</span>';
                } else {
                    html = html + '<a href="javascript:void(0)"  data-method="PayPal" class="btn btn-danger" name="setup_btn">{{trans('lang.setup')}}</a>';
                }
                html = html + '</div>';
                html = html + '</div>';
                $('#available_method').append(html);
            }
        });
        flutterWaveSettings.get().then(async function (Snapshot) {
            var flutterwave = Snapshot.data();
            var html = '';
            if (flutterwave.isWithdrawEnabled) {
                html = html + '<div class="d-flex align-items-center mb-3 border-bottom pb-3">';
                html = html + '<div class="image d-flex align-items-center"><img src="{!! asset("images/flutterwave.png") !!}" style="width: 90px;"><h4 class="d-block text-center mt-2 text-dark ml-3">{{trans('lang.flutterwave')}}</h4></div>';
                html = html + '<div class="ml-auto">';
                if (flutterwaveSetupDone) {
                    html = html + '<span class="badge badge-success p-3"><i class="fa fa-check-circle"></i> {{trans('lang.setup_done')}}</span>';
                } else {
                    html = html + '<a href="javascript:void(0)"  data-method="FlutterWave" class="btn btn-danger" name="setup_btn" >{{trans('lang.setup')}}</a>';
                }
                html = html + '</div>';
                html = html + '</div>';
                $('#available_method').append(html);
            }
            
            jQuery("#data-table_processing").hide();
        });
    });
    $(document).on("click", "a[name='setup_btn']", function (e) {
        var paymentMethod = $(this).attr('data-method');
        if (paymentMethod == 'Stripe') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_accountId')}}</label>';
            html     = html + '<div class="col-12">';
                    html = html + '<input type="text" class="form-control stripe_accountId" value="">';
                    html = html + '<div class="form-text text-muted">';
                        html = html + '{!! trans('lang.app_setting_stripe_accountId_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        } else if (paymentMethod == 'RazorPay') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_accountId')}}</label>';
                html = html + '<div class="col-12">';
            html         = html + '<input type="text" class="form-control razorpay_accountId" value="">';
                    html = html + '<div class="form-text text-muted">';
                        html = html + '{!! trans('lang.app_setting_razorpay_accountId_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        } else if (paymentMethod == 'PayPal') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_paypal_email')}}</label>';
                html = html + '<div class="col-12">';
                    html = html + '<input type="text" class="form-control paypalEmail" value="">';
                    html = html + '<div class="form-text text-muted">';
                            html = html + ' {!! trans('lang.app_setting_paypal_email_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        
        } else if (paymentMethod == 'FlutterWave') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_flutterwave_accountnumber')}}</label>';
                html = html + '<div class="col-12">';
                    html = html + '<input type="text" class=" form-control flutterwave_accountNumber" value="">';
                    html = html + '<div class="form-text text-muted">';
                        html = html + '{!! trans('lang.app_setting_flutterwave_accountnumber_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_flutterwave_bankcode')}}</label>';
                html = html + '<div class="col-12">';
                    html = html + '<input type="text" class=" form-control flutterwave_bankCode" value="">';
                    html = html + '<div class="form-text text-muted">';
                        html = html + '{!! trans('lang.app_setting_flutterwave_bankcode_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        }
       
        $('#addMethodModal').find("#method_title").text(paymentMethod);
        $('#append_fields').html(html);
        $('#addMethodModal').modal('show');
    });
    $("#add-method-btn").click(function () {
        var methodName = $("#method_name").val();
        
        var id = (checkAlreadyExist == true) ? existId : database.collection('tmp').doc().id;
        
        if (methodName == 'Stripe') {
            var accountId = $('.stripe_accountId').val();
            
            if (accountId == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_stripe_accountId_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var stripeObject = { 'name': methodName, 'enable' : true, 'accountId': accountId }
                checkAlreadyExist == true ?
                (database.collection('withdraw_method').doc(id).update({
                    'stripe': stripeObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
                :
                (database.collection('withdraw_method').doc(id).set({
                    'stripe': stripeObject,
                    'id': id,
                    'userId': vendorUserId
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
            }
        } else if (methodName == 'RazorPay') {
            var accountId = $('.razorpay_accountId').val();
            if (accountId == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_razorpay_accountId_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var razorpayObject = { 'name': methodName, 'enable' : true, 'accountId': accountId}
                checkAlreadyExist == true ?
                (database.collection('withdraw_method').doc(id).update({
                    'razorpay': razorpayObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
                :
                (database.collection('withdraw_method').doc(id).set({
                    'razorpay': razorpayObject,
                    'id': id,
                    'userId': vendorUserId
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
            }
        } else if (methodName == 'PayPal') {
            var email = $('.paypalEmail').val();
            if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_paypal_email_help')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var paypalObject = { 'name': methodName, 'enable' : true, 'email': email}
                checkAlreadyExist == true ?
                (database.collection('withdraw_method').doc(id).update({
                    'paypal': paypalObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
                :
                (database.collection('withdraw_method').doc(id).set({
                    'paypal': paypalObject,
                    'id': id,
                    'userId': vendorUserId
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
                )
            }
        
        } else if (methodName == 'FlutterWave') {
            var accountNumber = $('.flutterwave_accountNumber').val();
            var bankCode = $('.flutterwave_bankCode').val();
            
            if (accountNumber == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_flutterwave_accountnumber_help')}}</p>");
                window.scrollTo(0, 0);
            } else if (bankCode == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_flutterwave_bankcode_help')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var flutterwaveObj = {
                    'name': methodName,
                    'enable' : true,
                    'accountNumber': accountNumber,
                    'bankCode': bankCode,
                }
                checkAlreadyExist == true ?
                    (database.collection('withdraw_method').doc(id).update({
                        'flutterwave': flutterwaveObj,
                    }).then(function (result) {
                        window.location.href = '{{ route("withdraw-method")}}';
                    })
                    )
                    :
                    (database.collection('withdraw_method').doc(id).set({
                        'flutterwave': flutterwaveObj,
                        'id': id,
                        'userId': vendorUserId
                    }).then(function (result) {
                        window.location.href = '{{ route("withdraw-method")}}';
                    })
                )
            }
        }
     })
</script>
@endsection