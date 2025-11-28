@extends('layouts.app')
@section('content')
<div class="withdraw-method-list page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.withdrawal_method')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                 
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">Processing...</div>
                       
                       <div class="title-head d-flex align-items-center mb-4 border-bottom pb-4"> 
                        <h3 class="mb-0">{{trans('lang.withdrawal_method')}}</h3>
                        <a href="{!! route('withdraw-method.create') !!}" class="ml-auto btn-primary btn">{{trans('lang.add_withdrawal_method')}}</a>
                        </div>
                        <div class="list-data" id="list-data">
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<script type="text/javascript">
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";
    var pagesize = 10;
    var docId = '';
    var append_list = '';
    var stripeObj = '';
    var razorpayObj = '';
    var paypalObj = '';
    var flutterwaveObj = '';
    
    var razorpayWithdrawEnabled = false;
    var razorpaySettings = database.collection('settings').doc('razorpaySettings');
    razorpaySettings.get().then(async function (snapshots) {
        var razorpayData = snapshots.data();
        if (razorpayData.isWithdrawEnabled) {
            razorpayWithdrawEnabled = true;
        }
    });
    
    var paypalWithdrawEnabled = false;
    var paypalSettings = database.collection('settings').doc('paypalSettings');
    paypalSettings.get().then(async function (snapshots) {
        var paypalData = snapshots.data();
        if (paypalData.isWithdrawEnabled) {
            paypalWithdrawEnabled = true;
        }
    });
    
    var flutterWaveWithdrawEnabled = false;
    var flutterWaveSettings = database.collection('settings').doc('flutterWave');
    flutterWaveSettings.get().then(async function (snapshots) {
        var flutterWaveData = snapshots.data();
        if (flutterWaveData.isWithdrawEnabled) {
            flutterWaveWithdrawEnabled = true;
        }
    });
    var stripeWithdrawEnabled = false;
    var stripeSettings = database.collection('settings').doc('stripeSettings');
    stripeSettings.get().then(async function (snapshots) {
        var stripeWaveData = snapshots.data();
        if (stripeWaveData.isWithdrawEnabled) {
            stripeWithdrawEnabled = true;
        }
    });

    var place_image = '';
    var ref_place = database.collection('settings').doc("placeHolderImage");
    ref_place.get().then(async function(snapshots) {

        var placeHolderImage = snapshots.data();
        place_image = placeHolderImage.image;

    });
    var ref = database.collection('withdraw_method').where('userId', '==', vendorUserId);
    
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#data-table_processing").show();
        append_list = document.getElementById('list-data');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            var html = '';
            if(snapshots.docs.length > 0){
                html = await getListData(snapshots);
                if(html != ''){
                    append_list.innerHTML = html;
                    append_list.innerHTML = html;
                }else{
                    html = html + '<p class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</p>'
                    append_list.innerHTML = html;
                }
            }else{
                html = html + '<p class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</p>'
                append_list.innerHTML = html;
            }
            jQuery("#data-table_processing").hide();
        });
    });
    async function getListData(snapshots) {
        var data = snapshots.docs[0].data();
        docId = data.id;
        if (!data.hasOwnProperty('stripe') && !data.hasOwnProperty('razorpay') && !data.hasOwnProperty('paypal') && !data.hasOwnProperty('flutterwave')) {
            return '<p class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</p>';
        }
        var paymentMethodArr = {};
        if (data.hasOwnProperty('stripe') && stripeWithdrawEnabled) {
            paymentMethodArr['stripe'] = data.stripe;
            stripeObj = data.stripe;
        }
        if (data.hasOwnProperty('razorpay') && razorpayWithdrawEnabled) {
            paymentMethodArr['razorpay'] = data.razorpay;
            razorpayObj = data.razorpay;
        }
        if (data.hasOwnProperty('paypal') && paypalWithdrawEnabled) {
            paymentMethodArr['paypal'] = data.paypal;
            paypalObj = data.paypal;
        }
        if (data.hasOwnProperty('flutterwave') && flutterWaveWithdrawEnabled) {
            paymentMethodArr['flutterwave'] = data.flutterwave;
            flutterwaveObj = data.flutterwave;
        }
        html = '';
        $.each(paymentMethodArr, function(key, data) {
            html = html + '<div class="method '+data.name+' d-flex border-bottom pb-3 mb-3 justify-content-between align-items-center">';
                html = html + '<div class="image d-flex align-items-center col-md-4 pl-0"><img src="{!! asset("images/'+key+'.png") !!}" onerror="this.onerror=null;this.src=\'' + place_image + '\'"  style="width: 90px;"><h4 class="d-block text-center mt-2 text-dark ml-3">' + data.name + '</h4></div>';
                html = html + '<div class="action d-flex col-md-4 justify-content-end pr-0">';
                    html = html + '<div class="edit pr-3">';
                        html = html + '<a href="javascript:void(0)" data-method="' + data.name + '" name="action_edit" class="action-edit text-green" >{{trans("lang.edit")}}</a>';
                    html = html + '</div>';
                    html = html + '<div class="delete">';
                        html = html + '<a id="' + data.name + '" name="action_delete" class="action-delete" href="javascript:void(0)">{{trans("lang.remove")}}</a></div>';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        });
        return html;
    }
    $(document).on("click", "a[name='action_delete']", function (e) {
        if (confirm("{{trans('lang.app_payment_method_delete_alert')}}")) {
            var name = this.id;
            if (name == 'Stripe') {
                database.collection('withdraw_method').doc(docId).update({
                    'stripe': firebase.firestore.FieldValue.delete(),
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
            if (name == 'FlutterWave') {
                database.collection('withdraw_method').doc(docId).update({
                    'flutterwave': firebase.firestore.FieldValue.delete(),
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
            if (name == 'RazorPay') {
                database.collection('withdraw_method').doc(docId).update({
                    'razorpay': firebase.firestore.FieldValue.delete(),
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
            if (name == 'PayPal') {
                database.collection('withdraw_method').doc(docId).update({
                    'paypal': firebase.firestore.FieldValue.delete(),
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
        }
    });
    $(document).on("click", "a[name='action_edit']", function (e) {
        var paymentMethod = $(this).attr('data-method');
        
        if (paymentMethod == 'Stripe') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_accountId')}}</label>';
            html     = html + '<div class="col-12">';
                    html = html + '<input type="text" class="form-control stripe_accountId" value="' + stripeObj.accountId + '">';
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
            html         = html + '<input type="text" class="form-control razorpay_accountId" value="' + razorpayObj.accountId + '">';
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
                    html = html + '<input type="text" class="form-control paypalEmail" value="' + paypalObj.email + '">';
                    html = html + '<div class="form-text text-muted">';
                            html = html + ' {!! trans('lang.app_setting_paypal_email_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
        }else if (paymentMethod == 'FlutterWave') {
            var html = '';
            html = html + '<input type="hidden" value="'+paymentMethod+'" id="method_name">';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_flutterwave_accountnumber')}}</label>';
                html = html + '<div class="col-12">';
                    html = html + '<input type="text" class=" form-control flutterwave_accountNumber" value="' + flutterwaveObj.accountNumber + '">';
                    html = html + '<div class="form-text text-muted">';
                        html = html + '{!! trans('lang.app_setting_flutterwave_accountnumber_help') !!}';
                    html = html + '</div>';
                html = html + '</div>';
            html = html + '</div>';
            html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-5 control-label">{{trans('lang.app_setting_flutterwave_bankcode')}}</label>';
                html = html + '<div class="col-12">';
                    html = html + '<input type="text" class=" form-control flutterwave_bankCode" value="' + flutterwaveObj.bankCode + '">';
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
        var id = docId;
        if (methodName == 'Stripe') {
            var accountId = $('.stripe_accountId').val();
            if (accountId == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_stripe_accountId_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var stripeObject = { 'name': methodName, 'accountId': accountId}
                database.collection('withdraw_method').doc(id).update({
                    'stripe': stripeObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
        } else if (methodName == 'RazorPay') {
            
            var accountId = $('.razorpay_accountId').val();
            if (accountId == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_razorpay_accountId_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var razorpayObject = { 'name': methodName, 'accountId': accountId}
                database.collection('withdraw_method').doc(id).update({
                    'razorpay': razorpayObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
        } else if (methodName == 'PayPal') {
            var email = $('.paypalEmail').val();
            if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_setting_paypal_email_help')}}</p>");
                window.scrollTo(0, 0);
            } else {
                var paypalObject = { 'name': methodName, 'email': email}
                database.collection('withdraw_method').doc(id).update({
                    'paypal': paypalObject,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
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
                    'accountNumber': accountNumber,
                    'bankCode': bankCode,
                }
                database.collection('withdraw_method').doc(id).update({
                    'flutterwave': flutterwaveObj,
                }).then(function (result) {
                    window.location.href = '{{ route("withdraw-method")}}';
                })
            }
        }
    })
</script>
@endsection