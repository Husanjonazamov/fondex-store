@extends('layouts.app')

@section('content')
<?php if ($id == 'create') {
    $id = '';
} ?>
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vendors_payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>


                <li class="breadcrumb-item"><a
                            href="{!! route('payments') !!}">{{trans('lang.vendors_payout_table')}}</a>
                </li>

                <li class="breadcrumb-item">{{trans('lang.vendors_payout_create')}}</li>
            </ol>
        </div>
    </div>

    <div class="card-body">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="error_top"></div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.vendors_payout_create')}}</legend>
                    <?php if ($id == '') { ?>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{ trans('lang.vendors_payout_vendor_id')}}</label>
                            <div class="col-7">
                                <select id="select_vendor" class="form-control">
                                    <option value="">{{ trans('lang.select_vendor') }}</option>
                                </select>
                                <div class="form-text text-muted">
                                    {{ trans("lang.vendors_payout_vendor_id_help") }}
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.vendors_payout_amount')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control payout_amount">
                            <div class="form-text text-muted" min="0">
                                {{ trans("lang.vendors_payout_amount_placeholder") }}
                            </div>
                        </div>
                    </div>


                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.vendors_payout_note')}}</label>
                        <div class="col-7">
                            <textarea type="text" rows="8" class="form-control payout_note"></textarea>
                        </div>
                    </div>

                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.withdrawal_method')}}</label>
                        <div class="col-7">
                            <select id="withdraw_method" class="form-control">
                                 <option value="">{{trans('lang.select_withdrawal_method')}}</option>
                            </select>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>

    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary save_vendor_payout_btn"><i class="fa fa-save"></i>
            {{trans('lang.save')}}
        </button>
        <a href="{!! route('payments') !!}" class="btn btn-default"><i
                    class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
</div>
</div>

@endsection

@section('scripts')

<script>

    var database = firebase.firestore();
    var email_templates = database.collection('email_templates').where('type', '==', 'payout_request');

    var emailTemplatesData = null;

    var adminEmail = '';
    var withdrawMethodRef = database.collection('withdraw_method').where('userId', '==', vendorUserId);
    var emailSetting = database.collection('settings').doc('emailSetting');
    
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

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

    async function remainingPrice(vendorID) {
        var remaining = 0;

        await database.collection('users').where("vendorID", "==", vendorID).get().then(async function (snapshotss) {
            if (snapshotss.docs.length) {
                userdata = snapshotss.docs[0].data();
                if (isNaN(userdata.wallet_amount) || userdata.wallet_amount == undefined) {
                    remaining = 0;
                } else {
                    remaining = userdata.wallet_amount;
                }

            }
        });
        return remaining;
    }

    async function remainingPriceOLD(vendorID) {
        var paid_price = 0;
        var total_price = 0;
        var remaining = 0;
        var adminCommission = 0;
        var commission = 0;
        await database.collection('payouts').where('vendorID', '==', vendorID).get().then(async function (payoutSnapshots) {
            payoutSnapshots.docs.forEach((payout) => {
                var payoutData = payout.data();
                paid_price = parseFloat(paid_price) + parseFloat(payoutData.amount);
            })

            await database.collection('vendor_orders').where('vendor.id', '==', vendorID).where("status", "in", ["Order Completed"]).get().then(async function (orderSnapshots) {

                orderSnapshots.docs.forEach((order) => {
                    var orderData = order.data();
                    var productTotalmain = 0;
                    orderData.products.forEach((product) => {
                        var extras_price = 0;
                        if (product.price && product.quantity != 0) {
                            if (product.extras_price != undefined) {
                                extras_price = parseFloat(product.extras_price) * parseInt(product.quantity);
                            }
                            var productTotal = (parseFloat(product.price) * parseFloat(product.quantity)) + parseFloat(extras_price);

                            total_price = parseFloat(total_price) + parseFloat(productTotal);
                            productTotalmain = parseFloat(productTotalmain) + parseFloat(productTotal);
                        }
                    })
                

                    if (orderData.adminCommission != undefined && orderData.adminCommissionType != undefined) {
                        if (orderData.adminCommissionType == "Percent") {
                            commission = (productTotalmain * parseFloat(orderData.adminCommission)) / 100;

                        } else {
                            commission = parseFloat(orderData.adminCommission);
                        }
                    } else if (orderData.adminCommission != undefined) {
                        commission = parseFloat(orderData.adminCommission);
                    }

                })

                if (adminCommission != undefined) {
                    total_price = parseFloat(total_price) - parseFloat(commission);
                }

                remaining = parseFloat(total_price) - parseFloat(paid_price);
            });
        });
        return remaining;
    }

    var userName = '';
    var userContact = '';

    $(document).ready(function () {
        $("#data-table_processing").show();

        email_templates.get().then(async function (snapshots) {
            emailTemplatesData = snapshots.docs[0].data();

        });

        withdrawMethodRef.get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                paymentMethodArr = [];
                var data = snapshots.docs[0].data();
                if (data.stripe && data.stripe.enable && stripeWithdrawEnabled) {
                    paymentMethodArr.push({ 'text': data.stripe.name, 'value': 'stripe' });
                }
                if (data.razorpay && data.razorpay.enable && razorpayWithdrawEnabled) {
                    paymentMethodArr.push({ 'text': data.razorpay.name, 'value': 'razorpay' });
                }
                if (data.paypal && data.paypal.enable && paypalWithdrawEnabled) {
                    paymentMethodArr.push({ 'text': data.paypal.name, 'value': 'paypal' });
                }
                if (data.flutterwave && data.flutterwave.enable && flutterWaveWithdrawEnabled) {
                    paymentMethodArr.push({ 'text': data.flutterwave.name, 'value': 'flutterwave' });
                }

                $('#withdraw_method').append($("<option></option>")
                        .attr("value", 'bank')
                        .text('Bank Transfer'));
                paymentMethodArr.forEach((listval) => {
                    $('#withdraw_method').append($("<option></option>")
                        .attr("value", listval.value)
                        .text(listval.text));
                })
            }
        })

        emailSetting.get().then(async function (snapshots) {
            var emailSettingData = snapshots.data();

            adminEmail = emailSettingData.userName;
        });

        database.collection('vendors').get().then(async function (snapshots) {

            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#select_vendor').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            })

        });
        $("#data-table_processing").hide();

        var payoutId = "<?php echo uniqid(); ?>";

        $(".save_vendor_payout_btn").click(async function () {
            $("#data-table_processing").show();
            var vendorUserId = "<?php echo $id; ?>";
            var vendorId = '';
            var vendorEmail = await getVendorEmail(vendorUserId);

            getVendorId(vendorUserId).then(data => {

                vendorId = data;
                var remaining = 0;

                remainingPrice(vendorId).then(data => {

                    var remaining = data;
                    var withdrawMethod=$('#withdraw_method').val();
                    var amount = parseFloat($(".payout_amount").val());
                    var note = $(".payout_note").val();
                    var date = new Date(Date.now());

                    if (isNaN(amount) || amount == " " || amount == null) {
                        $("#data-table_processing").hide();
                        $(".error_top").show();
                        $(window).scrollTop(0);
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.vendors_payout_amount_placeholder')}}</p>");
                    }else if (amount > remaining) {
                        $("#data-table_processing").hide();
                        $(".error_top").show();
                        $(window).scrollTop(0);
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.insufficient_payment_error')}}</p>");
                    } else if(withdrawMethod==''){
                        $("#data-table_processing").hide();
                        $(".error_top").show();
                        $(window).scrollTop(0);
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{trans('lang.select_withdrawal_method')}}</p>");
                    }else {
                        $("#data-table_processing").show();
                        database.collection('payouts').doc(payoutId).set({
                            'vendorID': vendorId,
                            'amount': amount,
                            'note': note,
                            'id': payoutId,
                            'paymentStatus': 'Pending',
                            'paidDate': date,
                            'withdrawMethod':withdrawMethod
                        }).then(async function () {

                            if (vendorId != '' && (amount != '' || amount != NaN)) {
                                if (remaining > 0) {
                                    price = remaining - amount;
                                }else {
                                    price = amount;
                                }
                                database.collection('users').doc(vendorUserId).update({ 'wallet_amount': price });
                            }
                            
                            if (currencyAtRight) {
                                amount = parseInt(amount).toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                amount = currentCurrency + "" + parseInt(amount).toFixed(decimal_degits);
                            }

                            var formattedDate = new Date();
                            var month = formattedDate.getMonth() + 1;
                            var day = formattedDate.getDate();
                            var year = formattedDate.getFullYear();

                            month = month < 10 ? '0' + month : month;
                            day = day < 10 ? '0' + day : day;

                            formattedDate = day + '-' + month + '-' + year;

                            var subject = emailTemplatesData.subject;
                            subject = subject.replace(/{userid}/g, vendorUserId);

                            emailTemplatesData.subject = subject;

                            var message = emailTemplatesData.message;
                            message = message.replace(/{userid}/g, vendorUserId);
                            message = message.replace(/{date}/g, formattedDate);
                            message = message.replace(/{amount}/g, amount);
                            message = message.replace(/{payoutrequestid}/g, payoutId);
                            message = message.replace(/{username}/g, userName);
                            message = message.replace(/{usercontactinfo}/g, userContact);

                            emailTemplatesData.message = message;

                            var url = "{{url('send-email')}}";
                            if(vendorEmail != '' && vendorEmail != null){
                                var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [adminEmail, vendorEmail]);
                                if (sendEmailStatus) {
                                    window.location.href = "{{route('payments')}}";
                                    $("#data-table_processing").hide();
                                }
                            }else{
                                window.location.href = "{{route('payments')}}";
                                $("#data-table_processing").hide();
                            }
                        });
                    }
                });
            });
        })
    })

    async function getVendorId(vendorUser) {
        var vendorId = '';
        var ref;
        await database.collection('vendors').where('author', "==", vendorUser).get().then(async function (vendorSnapshots) {
            var vendorData = vendorSnapshots.docs[0].data();
            vendorId = vendorData.id;

        });

        return vendorId;
    }

    async function getVendorEmail(vendorUser) {
        var userEmail = '';

        await database.collection('users').where('id', "==", vendorUser).get().then(async function (vendorSnapshots) {

            if (vendorSnapshots.docs[0]) {
                var vendorData = vendorSnapshots.docs[0].data();
                userEmail = vendorData.email;
                userName = vendorData.firstName + " " + vendorData.lastName;
                userContact = vendorData.phoneNumber;

            }

        });

        return userEmail;
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
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });

        return checkFlag;

    }


</script>

@endsection