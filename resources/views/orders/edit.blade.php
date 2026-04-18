@extends('layouts.app')
@section('content')
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
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.order_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item">{{ trans('lang.order_edit') }}</li>
                </ol>
            </div>
        </div>
        <div class="card-body">
            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                {{ trans('lang.processing') }}
            </div>
            <div class="text-right print-btn">
                <a href="{{ route('vendors.orderprint', $id) }}">
                    <button type="button" class="fa fa-print"></button>
                </a>
            </div>
            <div class="order_detail" id="order_detail">
                <div class="order_detail-top">
                    <div class="row">
                        <div class="order_edit-genrl col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h3>{{ trans('lang.general_details') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="order_detail-top-box">
                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.date_created') }}
                                                    : </strong><span id="createdAt"></span></label>
                                        </div>
                                        <div class="form-group row widt-100 gendetail-col payment_method">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.payment_methods') }}
                                                    : </strong><span id="payment_method"></span></label>
                                        </div>
                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.order_type') }}
                                                    :</strong>
                                                <span id="order_type"></span></label>
                                        </div>
                                        <div class="form-group row widt-100 gendetail-col schedule_date"></div>
                                        <div class="form-group row widt-100 gendetail-col prepare_time"></div>
                                        <div class="form-group row widt-100 gendetail-col" id="ccname_div" style="display:none">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.courier_company_name') }}
                                                    :</strong>
                                                <span id="ccname"></span></label>
                                        </div>
                                        <div class="form-group row widt-100 gendetail-col" id="ccid_div" style="display:none">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.courier_tracking_id') }}
                                                    :</strong>
                                                <span id="ccid"></span></label>
                                        </div>
                                        <div class="form-group row width-100 ">
                                            <label class="col-3 control-label">{{ trans('lang.status') }}:</label>
                                            <div class="col-7">
                                                <select id="order_status" class="form-control">
                                                    <option value="Order Placed" id="order_placed">
                                                        {{ trans('lang.order_placed') }}
                                                    </option>
                                                    <option value="Order Accepted" id="order_accepted">
                                                        {{ trans('lang.order_accepted') }}
                                                    </option>
                                                    <option value="Order Rejected" id="order_rejected">
                                                        {{ trans('lang.order_rejected') }}
                                                    </option>
                                                    <option value="Driver Pending" id="driver_pending">
                                                        {{ trans('lang.driver_pending') }}
                                                    </option>
                                                    <option value="Driver Rejected" id="driver_rejected">
                                                        {{ trans('lang.driver_rejected') }}
                                                    </option>
                                                    <option value="Order Shipped" id="order_shipped">
                                                        {{ trans('lang.order_shipped') }}
                                                    </option>
                                                    <option value="In Transit" id="in_transit">
                                                        {{ trans('lang.in_transit') }}
                                                    </option>
                                                    <option value="Order Completed" id="order_completed">
                                                        {{ trans('lang.order_completed') }}
                                                    </option>
                                                    <option value="Order Cancelled" id="order_canceled">
                                                        {{ trans('lang.order_canceled') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label"></label>
                                            <div class="col-7 text-right">
                                                <button type="button" class="btn btn-primary save_order_btn show_popup" >
                                                    <i class="fa fa-save"></i> {{ trans('lang.update') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-items-list mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <table cellpadding="0" cellspacing="0" class="table table-striped table-valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.item') }}</th>
                                                    <th class="d-head" style="display:none;">{{ trans('lang.file') }}</th>
                                                    <th>{{ trans('lang.price') }}</th>
                                                    <th>{{ trans('lang.qty') }}</th>
                                                    <th>{{ trans('lang.extra') }}</th>
                                                    <th>{{ trans('lang.total') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="order_products">
                                            </tbody>
                                        </table>
                                        <div class="order-data-row order-totals-items">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive bk-summary-table">
                                                        <table class="order-totals">
                                                            <tbody id="order_products_total">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="order_edit-genrl col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h3>{{ trans('lang.billing_details') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="address order_detail-top-box">
                                        <div class="form-group row widt-100 gendetail-col">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.name') }}:</strong>
                                                <span id="billing_name"></span></label>
                                        </div>
                                        <div class="form-group row widt-100 gendetail-col" id="billing_adrs">
                                            <label class="col-12 control-label"><strong>{{ trans('lang.address') }}
                                                    :</strong>
                                                <span id="billing_line1"></span>
                                                <span id="billing_line2"></span>
                                                <span id="billing_country"></span></label>
                                        </div>
                                        <p><strong>{{ trans('lang.email_address') }}:</strong>
                                            <span id="billing_email"></span>
                                        </p>
                                        <p><strong>{{ trans('lang.phone') }}:</strong>
                                            <span id="billing_phone"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="order_addre-edit mt-4 driver_details_hide">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.driver_detail') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box">
                                            <p>
                                                <span id="driver_firstName"></span> <span id="driver_lastName"></span><br>
                                            </p>
                                            <p><strong>{{ trans('lang.email_address') }}:</strong>
                                                <span id="driver_email"></span>
                                            </p>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span id="driver_phone"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="resturant-detail mt-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-header-title">{{ trans('lang.vendor') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <a href="#" class="row redirecttopage" id="resturant-view">
                                            <div class="col-4">
                                                <img src="" class="resturant-img rounded-circle" alt="vendor" width="70px" height="70px">
                                            </div>
                                            <div class="col-8">
                                                <h4 class="vendor-title"></h4>
                                            </div>
                                        </a>
                                        <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                        <p><strong>{{ trans('lang.phone') }}:</strong>
                                            <span id="vendor_phone"></span>
                                        </p>
                                        <p><strong>{{ trans('lang.address') }}:</strong>
                                            <span id="vendor_address"></span>
                                        </p>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="order_detail-review mt-4">
                                <div class="rental-review ">
                                    <div class="card">
                                        <div class="card-header bg-white box-header">
                                            <h3>{{ trans('lang.customer_reviews') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="review-inner">
                                                <div id="customers_rating_and_review">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary save_order_btn"><i class="fa fa-save"></i>
                {{ trans('lang.save') }}
            </button>
            <a href="javascript:window.history.go(-1);" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}
            </a>
        </div>        
    </div>
    <!-- </div>
    </div> -->
    <div class="modal fade" id="addPreparationTimeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.add_preparation_time') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{ trans('lang.time') }}</label>
                                    <div class="col-12">
                                        <input type="text" name="prepare_time" class="form-control time-picker" id="prepare_time">
                                        <div id="add_prepare_time_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-prepare-time-btn">{{ trans('submit') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">{{ trans('close') }}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignDriverModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.assign_order') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <div class="col-12">
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#addDeliverymanModal" class="add-deliveryman btn btn-success"><i class="fa fa-plus"></i>{{ trans('lang.add_delivery_man') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="form-group row width-100" id="driver_list_div">
                                    <div class="col-12">
                                        <div class="select2-container-full">
                                            <label>{{ trans('lang.select_deliveryman') }}</label>
                                            <select name="deliveryman" class="form-control" id="deliveryman_list">
                                            </select>
                                        </div>
                                        <div id="select_deliveryman" style="color:red"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" id="random-courier-btn"><i class="mdi mdi-shuffle-variant mr-1"></i>Tasodifiy kuryer</button>
                        <button type="button" class="btn btn-primary" id="order-assign-btn">{{ trans('lang.assign') }}
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            {{ trans('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addDeliverymanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.deliveryman_details') }}</h5>
                    <button type="button" class="close"  data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="error_top"></div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.first_name') }}</label>
                                <div class="col-12">
                                    <input type="text" class="form-control user_first_name" required>
                                    <div id="firstname_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.last_name') }}</label>
                                <div class="col-12">
                                    <input type="text" class="form-control user_last_name">
                                    <div id="lastname_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.email') }}</label>
                                <div class="col-12">
                                    <input type="email" class="form-control user_email" required>
                                    <div id="email_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.password') }}</label>
                                <div class="col-12">
                                    <input type="password" class="form-control password" required>
                                    <div id="password_err" class="text-danger err"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100 form-material">
                                <label class="col-3 control-label">{{ trans('lang.user_phone') }}</label>
                                <div class="col-12">
                                    <div class="phone-box position-relative" id="phone-box">
                                        <select name="country" id="country_selector">
                                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                            <?php $selected = ''; ?>
                                            <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">
                                                +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                            <?php } ?>
                                        </select>
                                        {{-- <input class="form-control user_phone" placeholder="Phone" id="phone" type="phone" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus> --}}
                                         <input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'phone_err')">
                                        <div id="phone_err" class="text-danger err"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.zone') }}<span class="required-field"></span></label>
                                <div class="col-12">
                                    <select id='zone' class="form-control">
                                        <option value="">{{ trans('lang.select_zone') }}</option>
                                    </select>
                                    <div id="zone_err" class="text-danger err"></div>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="add-deliveryman-btn">{{ trans('submit') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary"  data-dismiss="modal" aria-label="Close">{{ trans('close') }}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="orderTrakingModal" tabindex="-1" role="dialog" aria-labelledby="orderTrakingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderTrakingModalLabel">{{ trans('lang.please_provide_details') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="courierCompanyName" class="col-form-label">{{ trans('lang.courier_company_name') }}</label>
                        <input type="text" class="form-control" id="courierCompanyName">
                    </div>
                    <div class="form-group">
                        <label for="courierTrackingId" class="col-form-label">{{ trans('lang.courier_tracking_id') }}</label>
                        <input type="text" class="form-control" id="courierTrackingId">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save_btn">{{ trans('lang.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <style type="text/css">
    </style>
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var id_rendom = "<?php echo uniqid(); ?>";
        var adminCommission = 0;
        var id = "<?php echo $id; ?>";
        var pid = "<?php echo $pid; ?>";
        var aid = "<?php echo $aid; ?>";
        var rid = "<?php echo $rid; ?>";
        var fcmToken = '';
        var old_order_status = '';
        var payment_shared = false;
        var vendorname = '';
        var vendorId = '';
        var userId = '';
        var order_sectionId = '';
        var add_reff_amount = false;
        var referralAmount = '';
        var referralBy = '';
        var driverId = '';
        var deliveryChargeVal = 0;
        var tip_amount_val = 0;
        var tip_amount = 0;
        var total_price_val = 0;
        var adminCommission_val = 0;
        var vendorAuthor = '';
        var basePrice = 0;
        var total_tax_amount = 0;
        var subscriptionTotalOrders = -1;
        var subscriptionModel = false;
        var commissionModel = false;
        var database = firebase.firestore();
        var subscriptionBusinessModel = database.collection('settings').doc("vendor");
        var paymentMethod = '';
        var sectionId = "";
        subscriptionBusinessModel.get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });
        if (pid != '') {
            var ref = database.collection('vendor_orders').where("id", "==", pid);
        } else if (rid != '') {
            var ref = database.collection('vendor_orders').where("id", "==", rid);
        } else if (aid != '') {
            var ref = database.collection('vendor_orders').where("id", "==", aid);
        } else {
            var ref = database.collection('vendor_orders').where("id", "==", id);
        }
      $(document).ready(function () {
        jQuery("#data-table_processing").show();
        jQuery("#country_selector").select2({
			templateResult: formatState,
			templateSelection: formatState2,
			placeholder: "Select Country",
			allowClear: true
		});
    });
        var reviewAttributes = {};
        var append_procucts_list = '';
        var append_procucts_total = '';
        var total_price = 0;
        var currentCurrency = '';
        var currencyAtRight = false;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        var orderPreviousStatus = '';
        var orderPaymentMethod = '';
        var orderCustomerId = '';
        var orderPaytableAmount = 0;
        var orderTakeAwayOption = false;
        var manfcmTokenVendor = '';
        var fcmTokenDriver = '';
        var manname = '';
        var decimal_degits = 0;
        var service_type = '';
        var delivery_enable = false;
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        var restaurantRejectedSubject = '';
        var restaurantRejectedMessage = '';
        var restaurantAcceptedSubject = '';
        var restaurantAcceptedMessage = '';
        var restaurantTakeawayCompletedSubject = '';
        var restaurantTakeawayCompletedMessage = '';
        var storeAcceptedSubject = '';
        var storeAcceptedMessage = '';
        var storeCompletedSubject = '';
        var storeCompletedMessage = '';
        var dineinCanceledSubject = '';
        var dineinCanceledMessage = '';
        var dineinAcceptedSubject = '';
        var dineinAcceptedMessage = '';
        var scheduleOrderSubject = '';
        var scheduleOrderMessage = '';
        var storeOrderInTransitSubject = "";
        var storeOrderInTransitMsg = "";
        var selfDeliveryOrderAssignSubject = '';
        var selfDeliveryOrderAssignMsg = '';
        var selfDeliveryDriverCancelledSub = '';
        var selfDeliveryDriverCancelledMsg = '';
        var selfDeliveryCustomerCancelledSub = '';
        var selfDeliveryCustomerCancelledMsg = '';
        var isSelfDeliveryGlobally = true;
        var isSelfDeliveryByVendor = false;
        var singleOrderReceive = false;
        var refDriverNearBy = database.collection('settings').doc("DriverNearBy");
        refDriverNearBy.get().then(async function(snapshot) {
            var data = snapshot.data();
            if (data.singleOrderReceive) {
                singleOrderReceive = true;
            }
        })
        var scheduleOrderAcceptData = {};
        var scheduleOrderNotificationRef = database.collection('settings').doc("scheduleOrderNotification");
        scheduleOrderNotificationRef.get().then(async function(snapshot) {
            var data = snapshot.data();
            if (data) {
                scheduleOrderAcceptData.notifyTime = data.notifyTime;
                scheduleOrderAcceptData.timeUnit = data.timeUnit;
            }
        })
        database.collection('dynamic_notification').get().then(async function(snapshot) {
            if (snapshot.docs.length > 0) {
                snapshot.docs.map(async (listval) => {
                    val = listval.data();
                    if (val.type == "restaurant_rejected") {
                        restaurantRejectedSubject = val.subject;
                        restaurantRejectedMessage = val.message;
                    } else if (val.type == "restaurant_accepted") {
                        restaurantAcceptedSubject = val.subject;
                        restaurantAcceptedMessage = val.message;
                    } else if (val.type == "takeaway_completed") {
                        restaurantTakeawayCompletedSubject = val.subject;
                        restaurantTakeawayCompletedMessage = val.message;
                    } else if (val.type == "store_accepted") {
                        storeAcceptedSubject = val.subject;
                        storeAcceptedMessage = val.message;
                    } else if (val.type == "store_intransit") {
                        storeOrderInTransitSubject = val.subject;
                        storeOrderInTransitMsg = val.message;
                    } else if (val.type == "store_completed") {
                        storeCompletedSubject = val.subject;
                        storeCompletedMessage = val.message;
                    } else if (val.type == "dinein_canceled") {
                        dineinCanceledSubject = val.subject;
                        dineinCanceledMessage = val.message;
                    } else if (val.type == "dinein_accepted") {
                        dineinAcceptedSubject = val.subject;
                        dineinAcceptedMessage = val.message;
                    } else if (val.type == "schedule_order") {
                        scheduleOrderSubject = val.subject;
                        scheduleOrderMessage = val.message;
                    } else if (val.type == "assign_order") {
                        selfDeliveryOrderAssignSubject = val.subject;
                        selfDeliveryOrderAssignMsg = val.message;
                    } else if (val.type == "driver_cancelled") {
                        selfDeliveryDriverCancelledSub = val.subject;
                        selfDeliveryDriverCancelledMsg = val.message;
                    } else if (val.type == "restaurant_cancelled") {
                        selfDeliveryCustomerCancelledSub = val.subject;
                        selfDeliveryCustomerCancelledMsg = val.message;
                    }
                });
            }
        });
        var geoFirestore = new GeoFirestore(database);
        var place_image = '';
        var ref_place = database.collection('settings').doc("placeHolderImage");
        ref_place.get().then(async function(snapshots) {
            var placeHolderImage = snapshots.data();
            place_image = placeHolderImage.image;
        });
        $(document).ready(function() {
            $('.time-picker').timepicker({
                timeFormat: "HH:mm",
                showMeridian: false,
                format24: true,
                dropdown: false
            });
            $('.time-picker').timepicker().on('changeTime.timepicker', function(e) {
                var hours = e.time.hours,
                    min = e.time.minutes;
                if (hours < 10) {
                    $(e.currentTarget).val('0' + hours + ':' + min);
                }
            });
            var alovelaceDocumentRef = database.collection('vendor_orders').doc();
            if (alovelaceDocumentRef.id) {
                id_rendom = alovelaceDocumentRef.id;
            }
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            $(document.body).on('click', '#save_btn', function() {
                var courierCompanyName = $("#courierCompanyName").val();
                var courierTrackingId = $("#courierTrackingId").val();
                status = "Order Accepted";
                callWalletTransaction(status);
            });
            jQuery("#data-table_processing").show();
            ref.get().then(async function(snapshots) {
                var order = snapshots.docs[0].data();
                vendor_zoneId = order.vendor?.zoneId || '';
                await getDeliverymanList(order.vendorID);
                getUserReview(order);
                if (order.vendor.section_id != undefined && order.vendor.section_id != '') {
                    await database.collection('sections').doc(order.vendor.section_id).get().then(
                        async function(snapshot) {
                            service_type = snapshot.data().serviceTypeFlag;
                            delivery_enable = snapshot.data().dine_in_active;
                            if (snapshot.data().adminCommision != null && snapshot.data()
                                .adminCommision != '') {
                                if (snapshot.data().adminCommision.enable) {
                                    commissionModel = true;
                                }
                            }
                        });
                    async function loadZones(zoneId) {
                        const $select = $('#zone');
                        $select.empty().append('<option value="">{{ trans('lang.select_zone') }}</option>');
                        if (!zoneId) return $select.append('<option disabled>No zone</option>');
                        const doc = await database.collection('zone').doc(zoneId).get();
                        if (!doc.exists) return $select.append('<option disabled>Zone not found</option>');
                        const data = doc.data();
                        
                        $select.append(`<option value="${doc.id}" selected>${data.name}</option>`);
                    }

                    $('#addDeliverymanModal').on('shown.bs.modal', () => loadZones(vendor_zoneId));
                }
                append_procucts_list = document.getElementById('order_products');
                append_procucts_list.innerHTML = '';
                append_procucts_total = document.getElementById('order_products_total');
                append_procucts_total.innerHTML = '';
                if (order.address.name) {
                    $("#billing_name").text(order.address.name);
                } else {
                    $("#billing_name").text(order.author.firstName + ' ' + order.author.lastName);
                }
                $("#trackng_number").text(id);
                var billingAddressstring = '';
                if (order.address.hasOwnProperty('address')) {
                    $("#billing_line1").text(order.address.address);
                }
                if (order.address.hasOwnProperty('locality')) {
                    billingAddressstring = billingAddressstring + order.address.locality;
                }
                if (order.address.hasOwnProperty('landmark') && order.address.landmark != null) {
                    billingAddressstring = billingAddressstring + " " + order.address.landmark;
                }
                if (order.takeAway == true) {
                    billingAddressstring = '';
                    $('#billing_adrs').hide();
                }
                $("#billing_line2").text(billingAddressstring);
                if (order.author.hasOwnProperty('phoneNumber')) {
                    $("#billing_phone").text(order.author.phoneNumber);
                }
                if (order.address.hasOwnProperty('country')) {
                    $("#billing_country").text(order.address.country);
                }
                if (order.author.hasOwnProperty('email')) {
                    $("#billing_email").html('<a href="mailto:' + order.author.email + '">' + order
                        .author.email + '</a>');
                }
                if (order.createdAt) {
                    var date1 = order.createdAt.toDate().toDateString();
                    var date = new Date(date1);
                    var dd = String(date.getDate()).padStart(2, '0');
                    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = date.getFullYear();
                    var createdAt_val = yyyy + '-' + mm + '-' + dd;
                    var time = order.createdAt.toDate().toLocaleTimeString('en-US');
                    $('#createdAt').text(createdAt_val + ' ' + time);
                }
                var payment_method = '';
                if (order.payment_method) {
                    paymentMethod = order.payment_method;
                    if (order.payment_method == "stripe") {
                        image = '{{ asset('images/payment/stripe.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "cod") {
                        image = '{{ asset('images/payment/cashondelivery.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "razorpay") {
                        image = '{{ asset('images/payment/razorepay.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "paypal") {
                        image = '{{ asset('images/payment/paypal.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "payfast") {
                        image = '{{ asset('images/payment/payfast.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '" width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "paystack") {
                        image = '{{ asset('images/payment/paystack.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "flutterwave") {
                        image = '{{ asset('images/payment/flutter_wave.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "mercadoPago" || order.payment_method ==
                        "mercado pago" || order.payment_method == "mercadopago") {
                        image = '{{ asset('images/payment/marcado_pago.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "wallet") {
                        image = '{{ asset('images/payment/emart_wallet.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "paytm") {
                        image = '{{ asset('images/payment/paytm.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "cancelled order payment") {
                        image = '{{ asset('images/payment/cancel_order.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "refund amount") {
                        image = '{{ asset('images/payment/refund_amount.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else if (order.payment_method == "referral amount") {
                        image = '{{ asset('images/payment/reffral_amount.png') }}';
                        payment_method = '<img alt="image" src="' + image +
                            '"  width="30%" height="30%" onerror="this.onerror=null;this.src=\'' + place_image + '\'">';
                    } else {
                        payment_method = order.payment_method;
                    }
                }
                $('#payment_method').html('<span>' + payment_method + '</span>');
                if (order.hasOwnProperty('takeAway') && order.takeAway) {
                    $('#order_completed').show();
                    $('#order_type').text('{{ trans('lang.order_takeaway') }}');
                    $('.payment_method').hide();
                    orderTakeAwayOption = true;
                } else {
                    $('#order_type').text('{{ trans('lang.order_delivery') }}');
                    $('.payment_method').show();
                    $('#order_completed').hide();
                }
                if (service_type != undefined && service_type != '' && service_type ==
                    "ecommerce-service") {
                    $('#order_placed').hide();
                    $('#order_accepted').hide();
                    $('#order_rejected').hide();
                    $('#driver_pending').hide();
                    $('#driver_rejected').hide();
                    $('#order_shipped').hide();
                    $('#order_completed').show();
                    $('#ccname_div').show();
                    $('#ccid_div').show();
                } else {
                    $('#order_placed').hide();
                    $('#driver_pending').hide();
                    $('#driver_rejected').hide();
                    $('#order_shipped').hide();
                    $('#in_transit').hide();
                    $('#ccname_div').hide();
                    $('#ccid_div').hide();
                }
                if (order.courierCompanyName != '' && order.courierCompanyName != undefined) {
                    $("#courierCompanyName").val(order.courierCompanyName);
                    $("#ccname").text(order.courierCompanyName);
                }
                if (order.courierTrackingId != '' && order.courierTrackingId != undefined) {
                    $("#courierTrackingId").val(order.courierTrackingId);
                    $("#ccid").text(order.courierTrackingId);
                }
                if ((order.driver != '' && order.driver != undefined) && order.takeAway == false) {
                    $('#driver_email').html('<a href="mailto:' + order.driver.email + '">' + order
                        .driver.email + '</a>');
                    $('#driver_firstName').text(order.driver.firstName);
                    $('#driver_lastName').text(order.driver.lastName);
                    $('#driver_phone').text(order.driver.phoneNumber);
                } else {
                    $('.driver_details_hide').empty();
                }
                if (order.driverID != '' && order.driverID != undefined) {
                    driverId = order.driverID;
                }
                if (order.vendor && order.vendor.author != '' && order.vendor.author != undefined) {
                    vendorAuthor = order.vendor.author;
                }
                fcmToken = order.author.fcmToken;
                vendorname = order.vendor.title;
                fcmTokenVendor = order.vendor.fcmToken;
                customername = order.author.firstName;
                vendorId = order.vendor.id;
                zoneId = order.vendor.zoneId;
                userId = order.author.id;
                order_sectionId = order.section_id;
                if (order_sectionId != '' && order_sectionId != undefined) {
                    database.collection('sections').doc(order_sectionId).get().then(async function(
                        snapshots) {
                        var secInfo = snapshots.data();
                        if (secInfo != undefined) {
                            referralAmount = parseFloat(secInfo.referralAmount);
                        }
                    });
                }
                if (userId != '' && userId != undefined) {
                    database.collection('referral').doc(userId).get().then(async function(snapshots) {
                        var refInfo = snapshots.data();
                        if (refInfo != undefined) {
                            referralBy = refInfo.referralBy;
                        }
                    });
                    database.collection('vendor_orders').where('author.id', '==', userId).get().then(
                        async function(snapshots) {
                            if (snapshots.docs.length == 1) {
                                add_reff_amount = true;
                            }
                        });
                }
                old_order_status = order.status;
                if (order.payment_shared != undefined) {
                    payment_shared = order.payment_shared;
                }
                var productsListHTML = buildHTMLProductsList(order.products);
                var productstotalHTML = buildHTMLProductstotal(order);
                if (productsListHTML != '') {
                    append_procucts_list.innerHTML = productsListHTML;
                }
                if (productstotalHTML != '') {
                    append_procucts_total.innerHTML = productstotalHTML;
                }
                orderPreviousStatus = order.status;
                if (order.hasOwnProperty('payment_method')) {
                    orderPaymentMethod = order.payment_method;
                }
                $("#order_status option[value='" + order.status + "']").attr("selected", "selected");
                if (order.status == 'Order Placed' || service_type == "ecommerce-service") {
                    $('#order_canceled').hide();
                }
                var curr_status = $("#order_status").val();
                if (order.status == "Order Rejected" || order.status == "Driver Rejected" || order.status == "Order Cancelled" || order.status == "Order Completed") {
                    $("#order_status").prop("disabled", true);
                }
                if (order.status != 'Order Placed') {
                    $('#order_accepted').hide();
                    $('#order_rejected').hide();
                }
                if (service_type != undefined && service_type != '' && service_type ==
                    "delivery-service") {
                    if (order.status == "Order Accepted" || order.status == "Driver Pending") {
                        $("#order_status").prop("disabled", true);
                    }
                }
                var price = 0;
                if (order.vendorID) {
                    var vendor = database.collection('vendors').where("id", "==", order.vendorID);
                    await vendor.get().then(async function(snapshotsnew) {
                        var vendordata = snapshotsnew.docs[0].data();
                        if (vendordata.hasOwnProperty('isSelfDelivery') && vendordata.isSelfDelivery) {
                            isSelfDeliveryByVendor = true;
                        }
                        if (subscriptionModel || commissionModel) {
                            if (vendordata.hasOwnProperty('subscriptionTotalOrders') &&
                                vendordata.subscriptionTotalOrders != null && vendordata
                                .subscriptionTotalOrders != '') {
                                subscriptionTotalOrders = vendordata
                                    .subscriptionTotalOrders;
                            }
                        }
                        if (vendordata.photo) {
                            $('.resturant-img').attr('src', vendordata.photo);
                        } else {
                            $('.resturant-img').attr('src', place_image);
                        }
                        if (vendordata.title) {
                            $('.vendor-title').html(vendordata.title);
                        }
                        if (vendordata.phonenumber) {
                            $('#vendor_phone').text(vendordata.phonenumber);
                        }
                        if (vendordata.location) {
                            $('#vendor_address').text(vendordata.location);
                        }
                        sectionId = vendordata.section_id;
                    });
                }
                if (order.hasOwnProperty('scheduleTime') && order.scheduleTime != null && order
                    .scheduleTime != '') {
                    scheduleTime = order.scheduleTime;
                    var scheduleDate = scheduleTime.toDate().toDateString();
                    var time = order.scheduleTime.toDate().toLocaleTimeString('en-US');
                    var scheduleDate = new Date(scheduleDate);
                    var dd = String(scheduleDate.getDate()).padStart(2, '0');
                    var mm = String(scheduleDate.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = scheduleDate.getFullYear();
                    var scheduleDate = yyyy + '-' + mm + '-' + dd;
                    var scheduleDateTime = scheduleDate + ' ' + time;
                    $('.schedule_date').append(
                        '<label class="col-12 control-label"><strong>{{ trans('lang.schedule_date_time') }}:</strong><span id=""> ' +
                        scheduleDateTime + '</span></label>')
                }
                if (order.hasOwnProperty('estimatedTimeToPrepare') && order.estimatedTimeToPrepare !=
                    null && order.estimatedTimeToPrepare != '') {
                    prepareTime = order.estimatedTimeToPrepare;
                    var [h, m] = prepareTime.split(":");
                    var hour = h;
                    if (h.charAt(0) == "0") {
                        hour = h.charAt(1);
                    }
                    time = (h == "00") ? m + " minutes" : hour + " hours " + m + " minutes";
                    $('.prepare_time').append(
                        '<label class="col-12 control-label "><strong>{{ trans('lang.prepare_time') }}:</strong><span id=""> ' +
                        time + '</span></label>')
                }
                if (isSelfDeliveryByVendor && isSelfDeliveryGlobally && order.status == 'Order Placed' && !order.takeAway) {
                    var newOption = $('<option>', {
                        value: 'Order Accepted',
                        text: "{{ trans('lang.assign_delivery_man') }}"
                    });
                    $('#order_status option[value="Order Rejected"]').before(newOption);
                    $('#order_accepted').hide();
                }
                jQuery("#data-table_processing").hide();
            })

            async function getDeliverymanList(vendorID) {
                database.collection('users').where('role', '==', 'driver').where('vendorID', '==', vendorID).where('isActive', '==', true).get().then(async function(snapshot) {
                    if (snapshot.docs.length > 0) {
                        snapshot.docs.forEach((listval) => {
                            var data = listval.data();
                            if (singleOrderReceive) {
                                let option = $("<option></option>")
                                    .attr("value", data.id)
                                    .attr("fcm", data.fcmToken);
                                if (data.hasOwnProperty('inProgressOrderID') &&
                                    data.inProgressOrderID !== null &&
                                    data.inProgressOrderID !== '' &&
                                    data.inProgressOrderID.length > 0) {
                                    option.prop("disabled", true)
                                        .text(data.firstName + ' ' + data.lastName + ' (Occupied)');
                                } else {
                                    option.text(data.firstName + ' ' + data.lastName);
                                }
                                $('#deliveryman_list').append(option);
                            } else {
                                $('#deliveryman_list').append($("<option></option>")
                                    .attr("value", data.id)
                                    .attr("fcm", data.fcmToken)
                                    .text(data.firstName + ' ' + data.lastName));
                            }
                        });
                        $('#deliveryman_list').select2();
                    }
                })
            }

            $('#deliveryman_list').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-results__option').each(function() {
                        let $this = $(this);
                        if ($this.text().includes('(Occupied)')) {
                            $this.addClass('occupied-option'); // Add custom class
                        }
                    });
                }, 0);
            });
            function getTwentyFourFormat(h, timeslot) {
                if (h < 10 && timeslot == "PM") {
                    h = parseInt(h) + 12;
                } else if (h < 10 && timeslot == "AM") {
                    h = '0' + h;
                }
                return h;
            }
            $('#random-courier-btn').click(function() {
                var freeOptions = $('#deliveryman_list option:not(:disabled):not([value=""])');
                if (freeOptions.length === 0) {
                    $('#select_deliveryman').html('Bosh kuryer topilmadi');
                    return;
                }
                var randomIndex = Math.floor(Math.random() * freeOptions.length);
                var randomVal = $(freeOptions[randomIndex]).val();
                $('#deliveryman_list').val(randomVal).trigger('change');
                $('#select_deliveryman').html('');
            });

            $('#order-assign-btn').click(function() {
                var deliveryman = $('#deliveryman_list').val();
                if (deliveryman == '' || deliveryman == null) {
                    $('#select_deliveryman').html('{{ trans('lang.select_deliveryman') }}');
                    return false;
                }
                $('#assignDriverModal').modal('hide');
                $('#addPreparationTimeModal').modal('show');
            });
            $('#add-prepare-time-btn').click(async function() {
                var preparationTime = $('#prepare_time').val();
                if (preparationTime == '') {
                    $('#add_prepare_time_error').text('{{ trans('lang.add_prepare_time_error') }}');
                    return false;
                }
                if (isSelfDeliveryByVendor && isSelfDeliveryGlobally && !orderTakeAwayOption) {
                    var deliveryman = $('#deliveryman_list').val();
                    var orderRequestData = [];
                    var inProgressOrderID = [];
                    var driverData = '';
                    await database.collection('users').where('id', '==', deliveryman).get().then(async function(snapshot) {
                        if (snapshot.docs.length > 0) {
                            driverData = snapshot.docs[0].data();
                            fcmTokenDriver = driverData.fcmToken;
                            if (driverData.hasOwnProperty('orderRequestData') && driverData.orderRequestData != null && driverData.orderRequestData != '') {
                                orderRequestData = driverData.orderRequestData;
                            }
                            if (driverData.hasOwnProperty('inProgressOrderID') && driverData.inProgressOrderID != null && driverData.inProgressOrderID != '') {
                                inProgressOrderID = driverData.inProgressOrderID
                            }
                        }
                        orderRequestData.push(id);
                        inProgressOrderID.push(id);
                    })
                    await database.collection('users').doc(deliveryman).update({
                        'orderRequestData': orderRequestData,
                        'inProgressOrderID': inProgressOrderID
                    });
                    var updatedData = {
                        'status': "In Transit",
                        'estimatedTimeToPrepare': preparationTime,
                        'driverID': deliveryman,
                        'driver': driverData
                    }
                } else {
                    var updatedData = {
                        'status': "Order Accepted",
                        'estimatedTimeToPrepare': preparationTime
                    }
                }
                database.collection('vendor_orders').doc(id).update(updatedData).then(async function(result) {
                    status = updatedData.status;
                    callWalletTransaction(status);
                });
            });
            async function callWalletTransaction(status) {
                var orderStatus = status;
                var date = firebase.firestore.FieldValue.serverTimestamp();
                var courierCompanyName = $("#courierCompanyName").val();
                var courierTrackingId = $("#courierTrackingId").val();
                database.collection('vendor_orders').doc(id).update({
                    'status': status,
                    'courierCompanyName': courierCompanyName,
                    'courierTrackingId': courierTrackingId
                }).then(async function(result) {
                    var wId = database.collection('temp').doc().id;
                    database.collection('wallet').doc(wId).set({
                        'amount': parseFloat(basePrice),
                        'date': date,
                        'id': wId,
                        'isTopUp': true,
                        'order_id': "<?php echo $id; ?>",
                        'payment_method': 'Wallet',
                        'payment_status': 'success',
                        'transactionUser': 'vendor',
                        'user_id': vendorAuthor
                    }).then(async function(result) {
                        var vendorAmount = basePrice;
                        if (total_tax_amount != 0 || total_tax_amount != '') {
                            var wId = database.collection('temp').doc().id;
                            database.collection('wallet').doc(wId).set({
                                'amount': parseFloat(total_tax_amount),
                                'date': date,
                                'id': wId,
                                'isTopUp': true,
                                'order_id': "<?php echo $id; ?>",
                                'payment_method': 'tax',
                                'payment_status': 'success',
                                'transactionUser': 'vendor',
                                'user_id': vendorAuthor,
                                'note': 'Order Tax credited'
                            }).then(async function(result) {})
                        }
                        database.collection('users').where('id', '==', vendorAuthor)
                            .get().then(async function(snapshotsnew) {
                                var vendordata = snapshotsnew.docs[0].data();
                                if (vendordata) {
                                    if (parseInt(subscriptionTotalOrders) != -1) {
                                        subscriptionTotalOrders = parseInt(subscriptionTotalOrders) - 1;
                                        await database.collection('vendors').doc(vendordata.vendorID).update({
                                            'subscriptionTotalOrders': subscriptionTotalOrders.toString()
                                        })
                                    }                                    
                                    if (isNaN(vendordata.wallet_amount) ||
                                        vendordata.wallet_amount == undefined) {
                                        vendorWallet = 0;
                                    } else {
                                        vendorWallet = parseFloat(vendordata
                                            .wallet_amount);
                                    }
                                    newVendorWallet = vendorWallet + vendorAmount + parseFloat(total_tax_amount);
                                    database.collection('users').doc(
                                        vendorAuthor).update({
                                        'wallet_amount': parseFloat(
                                            newVendorWallet)
                                    }).then(async function(result) {
                                        callAjax(orderStatus);
                                    })
                                } else {
                                    callAjax(orderStatus);
                                }
                            });
                    });
                });
            }
            async function callAjax(orderStatus) {
                if (isSelfDeliveryByVendor && isSelfDeliveryGlobally) {
                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': fcmTokenDriver,
                            'vendorname': manname,
                            'orderStatus': 'Order Accepted',
                            'subject': selfDeliveryOrderAssignSubject,
                            'message': selfDeliveryOrderAssignMsg
                        },
                    });
                }
                var subject = storeAcceptedSubject;
                var message = storeAcceptedMessage;
                if (orderStatus == "In Transit") {
                    subject = storeOrderInTransitSubject;
                    message = storeOrderInTransitMsg;
                }
                await $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-status-notification'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        'fcm': manfcmTokenVendor,
                        'vendorname': manname,
                        'orderStatus': orderStatus,
                        'subject': subject,
                        'message': message
                    },
                    success: function(data) {
                        window.location.href = '{{ route('orders') }}';
                    },
                    error: function(xhr) {
                        console.error('order-status-notification error:', xhr.responseText);
                        window.location.href = '{{ route('orders') }}';
                    }
                });
            }
            $(".save_order_btn").click(async function() {
                var courierCompanyName = $("#courierCompanyName").val();
                var courierTrackingId = $("#courierTrackingId").val();
                var clientName = $(".client_name").val();
                var orderStatus = $("#order_status").val();
                var selectedOrderStatus = $("#order_status option:selected").val();
                if (parseInt(subscriptionTotalOrders) == 0) {
                    alert("{{ trans('lang.can_not_accept_more_orders') }}");
                    return false;
                }
                if (selectedOrderStatus != "Order Rejected" && selectedOrderStatus != "Order Cancelled" && selectedOrderStatus != "In Transit" && selectedOrderStatus != "Order Completed" && isSelfDeliveryByVendor === true) {                
                    $('#assignDriverModal').modal('show');
                    return false;
                }else{
                    $('#assignDriverModal').modal('hide');
                }
                if (old_order_status != orderStatus) {
                    if (orderStatus == "Order Placed") {
                        manfcmTokenVendor = fcmTokenVendor;
                        manname = customername;
                    } else {
                        manfcmTokenVendor = fcmToken;
                        manname = vendorname;
                    }
                    if (orderStatus == "Order Accepted" || orderStatus == "In Transit") {                        
                        ref.get().then(async function(snapshot) {
                            order = snapshot.docs[0].data();
                            id = order.id;
                            var scheduleTime = '';
                            if (order.hasOwnProperty('scheduleTime') && order.scheduleTime != null && order.scheduleTime != '') {
                                const scheduleTime = order.scheduleTime.toDate();
                                const now = new Date();
                                var notifyTime = scheduleOrderAcceptData.notifyTime;
                                var timeUnit = scheduleOrderAcceptData.timeUnit;
                                let notifyBeforeMs = 0;
                                if (timeUnit === 'minute') {
                                    notifyBeforeMs = notifyTime * 60 * 1000;
                                } else if (timeUnit === 'hour') {
                                    notifyBeforeMs = notifyTime * 60 * 60 * 1000;
                                } else {
                                    notifyBeforeMs = notifyTime * 24 * 60 * 60 * 1000;
                                }
                                const windowStart = new Date(scheduleTime.getTime() - notifyBeforeMs);
                                const windowEnd = scheduleTime;
                                var endDate = order.scheduleTime.toDate().toDateString() + ' ' + order.scheduleTime.toDate().toLocaleTimeString('en-US');
                                var startDate = windowStart.toDateString() + ' ' + windowStart.toLocaleTimeString('en-US');
                                if (now >= windowStart && now <= windowEnd) {
                                    if (isSelfDeliveryGlobally && isSelfDeliveryByVendor && !orderTakeAwayOption) {
                                        if (orderStatus != "Order Rejected" && orderStatus != "Order Cancelled") {
                                            $('#assignDriverModal').modal('show');
                                            return false;
                                        }else{
                                            $('#assignDriverModal').modal('hide');
                                        }
                                    } else {
                                        $('#addPreparationTimeModal').modal('show');
                                    }
                                } else if (now < windowStart) {
                                    alert("{{ trans('lang.you_can_accept_order_between') }}" + startDate + ' - ' + endDate); // too early
                                    return false;
                                } else {
                                    alert("{{ trans('lang.you_can_accept_order_between') }}" + startDate + ' - ' + endDate); // too late
                                    return false;
                                }
                            } else {
                                if (service_type != undefined && service_type != '' &&
                                    service_type == "delivery-service" && delivery_enable) {
                                    if (isSelfDeliveryGlobally && isSelfDeliveryByVendor && !orderTakeAwayOption) {
                                        if (orderStatus != "Order Rejected" && orderStatus != "Order Cancelled") {
                                            $('#assignDriverModal').modal('show');
                                            return false;
                                        }else{
                                            $('#assignDriverModal').modal('hide');
                                        }
                                    } else {
                                        $('#addPreparationTimeModal').modal('show');
                                    }
                                } else {
                                    callWalletTransaction(orderStatus);
                                }
                            }
                        })
                    } else if (orderStatus == 'Order Cancelled') {
                        await getRefund();
                    } else {
                        database.collection('vendor_orders').doc(id).update({
                            'status': orderStatus,
                            'courierCompanyName': courierCompanyName,
                            'courierTrackingId': courierTrackingId
                        }).then(async function(result) {
                            var subject = '';
                            var message = '';
                            if (orderStatus == "Order Completed" && orderTakeAwayOption ==
                                true) {
                                subject = restaurantTakeawayCompletedSubject;
                                message = restaurantTakeawayCompletedMessage;
                            } else if (orderStatus == "Order Completed" &&
                                orderTakeAwayOption == false) {
                                subject = storeCompletedSubject;
                                message = storeCompletedMessage;
                            } else if (orderStatus == "Order Rejected") {
                                subject = restaurantRejectedSubject;
                                message = restaurantRejectedMessage;
                            } else if (orderStatus == "In Transit" && service_type ==
                                "ecommerce-service") {
                                subject = storeOrderInTransitSubject;
                                message = storeOrderInTransitMsg;
                            } else if (orderStatus == "Order Accepted" && service_type ==
                                "ecommerce-service") {
                                subject = storeAcceptedSubject;
                                message = storeAcceptedMessage;
                            } else if (orderStatus == "Order Accepted" && service_type !=
                                "ecommerce-service") {
                                subject = restaurantAcceptedSubject;
                                message = restaurantAcceptedMessage;
                            }
                            if (orderStatus != orderPreviousStatus && payment_shared ==
                                false) {
                                if (orderStatus == 'Order Completed') {
                                    driverAmount = parseFloat(deliveryChargeVal) +
                                        parseFloat(tip_amount);
                                    if (driverId && driverAmount) {
                                        var driver = database.collection('users').where("id", "==", driverId);
                                        await driver.get().then(async function(snapshotsdriver) {
                                            var driverdata = snapshotsdriver.docs[0].data();
                                            if (driverdata) {
                                                if (isNaN(driverdata.wallet_amount) || driverdata.wallet_amount == undefined) {
                                                    driverWallet = 0;
                                                } else {
                                                    driverWallet = driverdata.wallet_amount;
                                                }
                                                if (service_type != undefined && service_type != '' && service_type == "ecommerce-service") {
                                                    if (orderPaymentMethod == 'cod') {
                                                        driverWallet = parseFloat(driverWallet) - parseFloat(total_price);
                                                    } else {
                                                        driverWallet = parseFloat(driverWallet) + parseFloat(driverAmount);
                                                    }
                                                } else {
                                                    if (orderPaymentMethod == 'cod' && orderTakeAwayOption == true) {
                                                        driverWallet = parseFloat(driverWallet) - parseFloat(total_price);
                                                    } else {
                                                        driverWallet = parseFloat(driverWallet) + parseFloat(driverAmount);
                                                    }
                                                }
                                                if (!isNaN(driverWallet)) {
                                                    await database.collection(
                                                            'users').doc(
                                                            driverdata.id)
                                                        .update({
                                                            'wallet_amount': parseFloat(
                                                                driverWallet
                                                            )
                                                        }).then(async function(
                                                            result) {});
                                                }
                                            }
                                        })
                                    }
                                    await database.collection('vendor_orders').doc(id)
                                        .update({
                                            'payment_shared': true
                                        }).then(async function(result) {});
                                    if (service_type == "ecommerce-service" &&
                                        add_reff_amount == true) {
                                        database.collection('users').doc(referralBy).get()
                                            .then(async function(snapshots) {
                                                var refUserInfo = snapshots.data();
                                                if (refUserInfo != undefined) {
                                                    if (refUserInfo.hasOwnProperty(
                                                            'wallet_amount')) {
                                                        var current_wallet_amount =
                                                            parseFloat(refUserInfo
                                                                .wallet_amount);
                                                    } else {
                                                        var current_wallet_amount =
                                                            0;
                                                    }
                                                    //update reff user wallet balance
                                                    var refUserWallet =
                                                        current_wallet_amount +
                                                        referralAmount;
                                                    database.collection('users')
                                                        .doc(referralBy).update({
                                                            'wallet_amount': refUserWallet
                                                        });
                                                    //add refferal history
                                                    var id_random =
                                                        "<?php echo uniqid(); ?>";
                                                    database.collection('wallet')
                                                        .doc(id_random).set({
                                                            'amount': referralAmount,
                                                            'date': new Date(),
                                                            'id': id_random,
                                                            'driverId': vendorId,
                                                            'isTopUp': true,
                                                            'order_id': '',
                                                            'payment_method': 'Referral Amount',
                                                            'payment_status': 'success',
                                                            'transactionUser': 'driver',
                                                            'user_id': referralBy,
                                                        })
                                                }
                                            });
                                    }
                                }
                                await $.ajax({
                                    type: 'POST',
                                    url: "<?php echo route('order-status-notification'); ?>",
                                    data: {
                                        _token: '<?php echo csrf_token(); ?>',
                                        'fcm': manfcmTokenVendor,
                                        'vendorname': manname,
                                        'orderStatus': orderStatus,
                                        'subject': subject,
                                        'message': message,
                                    },
                                    success: function(data) {
                                        if (orderPreviousStatus !=
                                            'Order Rejected' &&
                                            orderPreviousStatus !=
                                            'Driver Rejected' &&
                                            orderPaymentMethod != 'cod' &&
                                            orderTakeAwayOption == false) {
                                            if (orderStatus ==
                                                'Order Rejected' ||
                                                orderStatus == 'Driver Rejected'
                                            ) {
                                                var walletId =
                                                    "<?php echo uniqid(); ?>";
                                                var canceldateNew = new Date();
                                                var orderCancelDate = new Date(
                                                    canceldateNew.setHours(
                                                        23, 59, 59, 999));
                                                database.collection('wallet')
                                                    .doc(walletId).set({
                                                        'amount': parseFloat(
                                                            orderPaytableAmount
                                                        ),
                                                        'date': new Date(),
                                                        'id': walletId,
                                                        'payment_status': 'success',
                                                        'user_id': orderCustomerId,
                                                        'payment_method': 'Cancelled Order Payment'
                                                    }).then(function(result) {
                                                        database.collection(
                                                                'users')
                                                            .where("id",
                                                                "==",
                                                                orderCustomerId
                                                            ).get()
                                                            .then(async function(
                                                                userSnapshots
                                                            ) {
                                                                if (userSnapshots
                                                                    .docs
                                                                    .length >
                                                                    0
                                                                ) {
                                                                    data =
                                                                        userSnapshots
                                                                        .docs[
                                                                            0
                                                                        ]
                                                                        .data();
                                                                    var wallet_amount =
                                                                        0;
                                                                    if (data
                                                                        .wallet_amount !=
                                                                        undefined &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        '' &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        null &&
                                                                        !
                                                                        isNaN(
                                                                            data
                                                                            .wallet_amount
                                                                        )
                                                                    ) {
                                                                        wallet_amount
                                                                            =
                                                                            parseFloat(
                                                                                data
                                                                                .wallet_amount
                                                                            );
                                                                    }
                                                                    var newWalletAmount =
                                                                        wallet_amount +
                                                                        parseFloat(
                                                                            orderPaytableAmount
                                                                        );
                                                                    database
                                                                        .collection(
                                                                            'users'
                                                                        )
                                                                        .doc(
                                                                            orderCustomerId
                                                                        )
                                                                        .update({
                                                                            'wallet_amount': parseFloat(
                                                                                newWalletAmount
                                                                            )
                                                                        })
                                                                        .then(
                                                                            function(
                                                                                result
                                                                            ) {
                                                                                window
                                                                                    .location
                                                                                    .href =
                                                                                    '{{ route('orders') }}';
                                                                            }
                                                                        )
                                                                } else {
                                                                    window
                                                                        .location
                                                                        .href =
                                                                        '{{ route('orders') }}';
                                                                }
                                                            });
                                                    })
                                            } else {
                                                window.location.href =
                                                    '{{ route('orders') }}';
                                            }
                                        } else {
                                            window.location.href =
                                                '{{ route('orders') }}';
                                        }
                                    }
                                });
                            } else {
                                $.ajax({
                                    type: 'POST',
                                    url: "<?php echo route('order-status-notification'); ?>",
                                    data: {
                                        _token: '<?php echo csrf_token(); ?>',
                                        'fcm': manfcmTokenVendor,
                                        'vendorname': manname,
                                        'orderStatus': orderStatus,
                                        'subject': subject,
                                        'message': message,
                                    },
                                    success: function(data) {
                                        if (orderPreviousStatus !=
                                            'Order Rejected' &&
                                            orderPreviousStatus !=
                                            'Driver Rejected' &&
                                            orderPaymentMethod != 'cod' &&
                                            orderTakeAwayOption == false) {
                                            if (orderStatus ==
                                                'Order Rejected' ||
                                                orderStatus == 'Driver Rejected'
                                            ) {
                                                var walletId =
                                                    "<?php echo uniqid(); ?>";
                                                var canceldateNew = new Date();
                                                var orderCancelDate = new Date(
                                                    canceldateNew.setHours(
                                                        23, 59, 59, 999));
                                                database.collection('wallet')
                                                    .doc(walletId).set({
                                                        'amount': parseFloat(
                                                            orderPaytableAmount
                                                        ),
                                                        'date': new Date(),
                                                        'id': walletId,
                                                        'payment_status': 'success',
                                                        'user_id': orderCustomerId,
                                                        'payment_method': 'Cancelled Order Payment'
                                                    }).then(function(result) {
                                                        database.collection(
                                                                'users')
                                                            .where("id",
                                                                "==",
                                                                orderCustomerId
                                                            ).get()
                                                            .then(async function(
                                                                userSnapshots
                                                            ) {
                                                                if (userSnapshots
                                                                    .docs
                                                                    .length >
                                                                    0
                                                                ) {
                                                                    data =
                                                                        userSnapshots
                                                                        .docs[
                                                                            0
                                                                        ]
                                                                        .data();
                                                                    var wallet_amount =
                                                                        0;
                                                                    if (data
                                                                        .wallet_amount !=
                                                                        undefined &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        '' &&
                                                                        data
                                                                        .wallet_amount !=
                                                                        null &&
                                                                        !
                                                                        isNaN(
                                                                            data
                                                                            .wallet_amount
                                                                        )
                                                                    ) {
                                                                        wallet_amount
                                                                            =
                                                                            parseFloat(
                                                                                data
                                                                                .wallet_amount
                                                                            );
                                                                    }
                                                                    var newWalletAmount =
                                                                        wallet_amount +
                                                                        parseFloat(
                                                                            orderPaytableAmount
                                                                        );
                                                                    database
                                                                        .collection(
                                                                            'users'
                                                                        )
                                                                        .doc(
                                                                            orderCustomerId
                                                                        )
                                                                        .update({
                                                                            'wallet_amount': parseFloat(
                                                                                newWalletAmount
                                                                            )
                                                                        })
                                                                        .then(
                                                                            function(
                                                                                result
                                                                            ) {
                                                                                window
                                                                                    .location
                                                                                    .href =
                                                                                    '{{ route('orders') }}';
                                                                            }
                                                                        )
                                                                } else {
                                                                    window
                                                                        .location
                                                                        .href =
                                                                        '{{ route('orders') }}';
                                                                }
                                                            });
                                                    })
                                            } else {
                                                window.location.href =
                                                    '{{ route('orders') }}';
                                            }
                                        } else {
                                            window.location.href =
                                                '{{ route('orders') }}';
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            })
        })
        function getUserReview(vendorOrder, reviewAttr) {
            var refUserReview = database.collection('items_review').where('orderid', "==", vendorOrder.id);
            refUserReview.get().then(async function(userreviewsnapshot) {
                var reviewHTML = '';
                reviewHTML = buildRatingsAndReviewsHTML(vendorOrder, userreviewsnapshot);
                if (userreviewsnapshot.docs.length > 0) {
                    jQuery("#customers_rating_and_review").append(reviewHTML);
                } else {
                    jQuery("#customers_rating_and_review").html('<h4>No Reviews Found</h4>');
                }
            });
        }
        function buildRatingsAndReviewsHTML(vendorOrder, userreviewsnapshot) {
            var allreviewdata = [];
            var reviewhtml = '';
            userreviewsnapshot.docs.forEach((listval) => {
                var reviewDatas = listval.data();
                reviewDatas.id = listval.id;
                allreviewdata.push(reviewDatas);
            });
            reviewhtml += '<div class="user-ratings">';
            allreviewdata.forEach((listval) => {
                var val = listval;
                vendorOrder.products.forEach((productval) => {
                    if (productval.id == val.productId) {
                        rating = val.rating;
                        reviewhtml = reviewhtml +
                            '<div class="reviews-members py-3 border mb-3"><div class="media">';
                        if (productval.photo) {
                            photo = productval.photo;
                        } else {
                            photo = place_image;
                        }
                        reviewhtml = reviewhtml + '<a href="javascript:void(0);"><img alt="#" src="' +
                            photo + '" onerror="this.onerror=null;this.src=\'' + place_image +
                            '\'" class=" img-circle img-size-32 mr-2" style="width:60px;height:60px"></a>';
                        reviewhtml = reviewhtml +
                            '<div class="media-body d-flex"><div class="reviews-members-header"><h6 class="mb-0"><a class="text-dark" href="javascript:void(0);">' +
                            productval.name +
                            '</a></h6><div class="star-rating"><div class="d-inline-block" style="font-size: 14px;">';
                        reviewhtml = reviewhtml + ' <ul class="rating" data-rating="' + rating + '">';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                        reviewhtml = reviewhtml + '</ul>';
                        reviewhtml = reviewhtml + '</div></div>';
                        reviewhtml = reviewhtml + '</div>';
                        reviewhtml = reviewhtml + '<div class="review-date ml-auto">';
                        if (val.createdAt != null && val.createdAt != "") {
                            var review_date = val.createdAt.toDate().toLocaleDateString('en', {
                                year: "numeric",
                                month: "short",
                                day: "numeric"
                            });
                            reviewhtml = reviewhtml + '<span>' + review_date + '</span>';
                        }
                        reviewhtml = reviewhtml + '</div>';
                        var photos = '';
                        if (val.photos.length > 0) {
                            photos += '<div class="photos"><ul>';
                            $.each(val.photos, function(key, img) {
                                photos += '<li><img src="' + img + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" width="100"></li>';
                            });
                            photos += '</ul></div>';
                        }
                        reviewhtml = reviewhtml +
                            '</div></div><div class="reviews-members-body w-100"><p class="mb-2">' + val
                            .comment + '</p>' + photos + '</div>';
                        if (val.hasOwnProperty('reviewAttributes') && val.reviewAttributes != null) {
                            reviewhtml += '<div class="attribute-ratings feature-rating mb-2">';
                            var label_feature = "{{ trans('lang.byfeature') }}";
                            reviewhtml += '<h3 class="mb-2">' + label_feature + '</h3>';
                            reviewhtml += '<div class="media-body">';
                            $.each(val.reviewAttributes, function(aid, data) {
                                var at_id = aid;
                                var at_title = reviewAttributes[aid];
                                var at_value = data;
                                reviewhtml +=
                                    '<div class="feature-reviews-members-header d-flex mb-3">';
                                reviewhtml += '<h6 class="mb-0">' + at_title + '</h6>';
                                reviewhtml = reviewhtml +
                                    '<div class="rating-info ml-auto d-flex">';
                                reviewhtml = reviewhtml + '<div class="star-rating">';
                                reviewhtml = reviewhtml + ' <ul class="rating" data-rating="' +
                                    at_value + '">';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                                reviewhtml = reviewhtml + '</ul>';
                                reviewhtml += '</div>';
                                reviewhtml += '<div class="count-rating ml-2">';
                                reviewhtml += '<span class="count">' + at_value + '</span>';
                                reviewhtml += '</div>';
                                reviewhtml += '</div></div>';
                            });
                            reviewhtml += '</div></div>';
                        }
                        reviewhtml += '</div>';
                    }
                    reviewhtml += '</div>';
                });
            });
            reviewhtml += '</div>';
            return reviewhtml;
        }
        function buildHTMLProductsList(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];
            var totalProductPrice = 0;
            snapshotsProducts.forEach((product) => {
                getProductInfo(product);
                var val = product;
                var product_id = (val.variant_info && val.variant_info.variant_id) ? val.variant_info.variant_id :
                    val.id;
                html = html + '<tr class="product-row" data-pid="' + product_id + '" data-qty="' + (val.quantity || 1) + '" data-extras="0">';
                var extra_html = '';
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extra_html = extra_html + '<span>';
                    var extra_count = 1;
                    try {
                        product.extras.forEach((extra) => {
                            if (extra_count > 1) {
                                extra_html = extra_html + ',' + extra;
                            } else {
                                extra_html = extra_html + extra;
                            }
                            extra_count++;
                        })
                    } catch (error) {
                    }
                    extra_html = extra_html + '</span>';
                }
                html = html + '<td class="order-product"><div class="order-product-box">';
                if (val.photo != '') {
                    if (val.photo) {
                        photo = val.photo;
                    } else {
                        photo = place_image;
                    }
                    html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' +
                        photo + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" alt="image">';
                } else {
                    html = html + '<img class="img-circle img-size-32 mr-2" style="width:60px;height:60px;" src="' +
                        place_image + '" alt="image">';
                }
                html = html + '</div><div class="orders-tracking"><h6>' + val.name +
                    '</h6><div class="orders-tracking-item-details">';
                if (val.variant_info) {
                    html = html + '<div class="variant-info">';
                    html = html + '<ul>';
                    $.each(val.variant_info.variant_options, function(label, value) {
                        html = html + '<li class="variant"><span class="label">' + label +
                            '</span><span class="value">' + value + '</span></li>';
                    });
                    html = html + '</ul>';
                    html = html + '</div>';
                }
                if (extra_count > 1 || product.size) {
                    html = html + '<strong>{{ trans('lang.extras') }} :</strong>';
                }
                if (extra_count > 1) {
                    html = html +
                        '<div class="extra"><span>{{ trans('lang.extras') }} :</span><span class="ext-item">' +
                        extra_html + '</span></div>';
                }
                if (product.size) {
                    html = html +
                        '<div class="type"><span>{{ trans('lang.type') }} :</span><span class="ext-size">' +
                        product.size + '</span></div>';
                }
                var effectivePrice = (val.disPrice && parseFloat(val.disPrice) > 0) ? val.disPrice : val.price;
                price_item = parseFloat(effectivePrice).toFixed(decimal_degits);
                totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);
                var extras_price = 0;
                if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                    extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(
                        decimal_degits);
                    if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                        extras_price = extras_price_item;
                    }
                    totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                }
                totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);
                if (currencyAtRight) {
                    price_val = parseFloat(price_item).toFixed(decimal_degits) + "" + currentCurrency;
                    extras_price_val = parseFloat(extras_price).toFixed(decimal_degits) + "" + currentCurrency;
                    totalProductPrice_val = parseFloat(totalProductPrice).toFixed(decimal_degits) + "" +
                        currentCurrency;
                } else {
                    price_val = currentCurrency + "" + parseFloat(price_item).toFixed(decimal_degits);
                    extras_price_val = currentCurrency + "" + parseFloat(extras_price).toFixed(decimal_degits);
                    totalProductPrice_val = currentCurrency + "" + parseFloat(totalProductPrice).toFixed(
                        decimal_degits);
                }
                checkIsDownloadedItem(product.id);
                html = html + '</div></div></td>';
                html = html + '<td class="d-btn" data-pid="' + product.id + '" style="display:none;"></td>';
                html = html + '<td class="text-green text-center"><span class="item-price item-price-' + product_id + '">' + price_val +
                    '</span><br><span class="base-price-' + product_id + ' text-muted"></span></td><td> × ' + val
                    .quantity + '</td><td class="text-green"> + ' + extras_price_val +
                    '</td><td class="text-green item-total-' + product_id + '" data-raw-total="' + totalProductPrice + '">  ' + totalProductPrice_val + '</td>';
                html = html + '</tr>';
                total_price += parseFloat(totalProductPrice);
            });
            totalProductPrice = 0;
            return html;
        }
        function getProductInfo(product) {
            if (!product.id) return;
            database.collection('vendor_products').doc(product.id).get().then(async function(snapshots) {
                if (snapshots.exists) {
                    var productData = snapshots.data();
                    if (product.variant_info && product.variant_info.variant_id) {
                        var variant_info = $.map(productData.item_attribute.variants, function(v, i) {
                            if (v.variant_sku == product.variant_info.variant_sku) {
                                return v;
                            }
                        });
                        base_price = parseFloat(variant_info[0].variant_price);
                        var product_id = product.variant_info.variant_id;
                    } else {
                        if (parseFloat(productData.disPrice) != 0) {
                            var base_price = productData.disPrice;
                        } else {
                            var base_price = productData.price;
                        }
                        var product_id = product.id;
                    }
                    if (currencyAtRight) {
                        base_price_format = parseFloat(base_price).toFixed(decimal_degits) + "" +
                            currentCurrency;
                    } else {
                        base_price_format = currentCurrency + "" + parseFloat(base_price).toFixed(
                            decimal_degits);
                    }
                    $(".base-price-" + product_id).text('(Base Price: ' + base_price_format + ')');

                    // Update item price and row total with discounted price
                    var qty = parseInt($('.product-row[data-pid="' + product_id + '"]').data('qty') || 1);
                    var extras = parseFloat(product.extras_price || 0) * qty;
                    var new_unit_price = parseFloat(base_price);
                    var new_total = (new_unit_price * qty) + extras;
                    if (currencyAtRight) {
                        var new_price_val = new_unit_price.toFixed(decimal_degits) + "" + currentCurrency;
                        var new_total_val = new_total.toFixed(decimal_degits) + "" + currentCurrency;
                    } else {
                        var new_price_val = currentCurrency + "" + new_unit_price.toFixed(decimal_degits);
                        var new_total_val = currentCurrency + "" + new_total.toFixed(decimal_degits);
                    }
                    $(".item-price-" + product_id).text(new_price_val);
                    $(".item-total-" + product_id).text("  " + new_total_val).attr('data-raw-total', new_total.toFixed(decimal_degits));

                    // Recalculate and update subtotal
                    var recalc_total = 0;
                    $('[data-raw-total]').each(function() {
                        recalc_total += parseFloat($(this).attr('data-raw-total') || 0);
                    });
                    if (currencyAtRight) {
                        var new_subtotal = recalc_total.toFixed(decimal_degits) + "" + currentCurrency;
                    } else {
                        var new_subtotal = currentCurrency + "" + recalc_total.toFixed(decimal_degits);
                    }
                    $(".sub_total").text("(" + new_subtotal + ")");
                }
            });
        }
        function checkIsDownloadedItem(productId) {
            database.collection('vendor_products').doc(productId).get().then(async function(snapshots) {
                var productInfo = snapshots.data();
                if (productInfo != undefined) {
                    if (productInfo.hasOwnProperty('isDigitalProduct') && productInfo.hasOwnProperty(
                            'digitalProduct') && productInfo.isDigitalProduct == true && productInfo
                        .digitalProduct) {
                        $(".d-head").show();
                        $(".d-btn").show();
                        $(".d-btn[data-pid='" + productId + "']").html('<a href="' + productInfo
                            .digitalProduct + '" class="btn btn-primary"><i class="fa fa-download"></i></a>'
                        );
                    }
                }
            });
        }
        var refReviewAttributes = database.collection('review_attributes');
        refReviewAttributes.get().then(async function(snapshots) {
            if (snapshots != undefined) {
                snapshots.forEach((doc) => {
                    var data = doc.data();
                    reviewAttributes[data.id] = data.title;
                });
            }
        });
        function buildHTMLProductstotal(snapshotsProducts) {
            var html = '';
            var alldata = [];
            var number = [];
            adminCommissionValue = snapshotsProducts.adminCommission;
            var adminCommissionType = snapshotsProducts.adminCommissionType;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
            var extras = snapshotsProducts.extras;
            var extras_price = snapshotsProducts.extras_price;
            var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
            var takeAway = snapshotsProducts.takeAway;
            var tip_amount = snapshotsProducts.tip_amount;
            var notes = snapshotsProducts.notes;
            var tax_amount = snapshotsProducts.vendor.tax_amount;
            var status = snapshotsProducts.status;
            var products = snapshotsProducts.products;
            var deliveryCharge = snapshotsProducts.deliveryCharge;
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
            if (products) {
                products.forEach((product) => {
                    var val = product;
                });
            }
            total_price = parseFloat(total_price);
            if (currencyAtRight) {
                var sub_total = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var sub_total = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }
            html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.sub_total') }}</span></td></tr>';
            html = html +
                '<tr class="final-rate"><td class="label">{{ trans('lang.sub_total') }}</td><td class="sub_total" style="color:green">(' +
                sub_total + ')</td></tr>';
            var priceWithCommision = total_price;
            if (intRegex.test(discount) || floatRegex.test(discount)) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.discount') }}</span></td></tr>';
                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);
                if (currencyAtRight) {
                    discount_val = parseFloat(discount).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + parseFloat(discount).toFixed(decimal_degits);
                }
                couponCode_html = '';
                if (couponCode) {
                    couponCode_html = '</br><small>{{ trans('lang.coupon_codes') }} :' + couponCode + '</small>';
                }
                html = html + '<tr><td class="label">{{ trans('lang.discount') }}' + couponCode_html +
                    '</td><td class="discount" style="color:red">(-' + discount_val + ')</td></tr>';
            }
            var specialDiscount_ = 0;
            specialDiscountlabel = '';
            specialDiscounttype = '';
            try {
                if (snapshotsProducts.hasOwnProperty('specialDiscount')) {
                    if (snapshotsProducts.specialDiscount.specialType && snapshotsProducts.specialDiscount
                        .special_discount) {
                        if (snapshotsProducts.specialDiscount.specialType == "percent") {
                            specialDiscount_ = snapshotsProducts.specialDiscount.special_discount;
                            specialDiscounttype = "%";
                        } else {
                            specialDiscount_ = snapshotsProducts.specialDiscount.special_discount;
                            specialDiscounttype = "fix";
                        }
                        specialDiscountlabel = snapshotsProducts.specialDiscount.special_discount_label;
                    }
                }
            } catch (error) {
            }
            if (!isNaN(specialDiscount_) && specialDiscount_ != 0) {
                if (currencyAtRight) {
                    html = html +
                        '<tr><td class="label">{{ trans('lang.special_offer') }}</td><td class="deliveryCharge" style="color:red">(-' +
                        specialDiscount_ + '' + currentCurrency + ')(' + snapshotsProducts.specialDiscount
                        .special_discount + ' ' + specialDiscounttype + ')</td></tr>';
                } else {
                    html = html +
                        '<tr><td class="label">{{ trans('lang.special_offer') }}</td><td class="deliveryCharge" style="color:red">(-' +
                        currentCurrency + specialDiscount_ + ')(' + snapshotsProducts.specialDiscount.special_discount +
                        ' ' + specialDiscounttype + ')</td></tr>';
                }
                total_price = total_price - specialDiscount_;
            }
            var total_item_price = total_price;
            var tax = 0;
            taxlabel = '';
            taxlabeltype = '';
            if (snapshotsProducts.hasOwnProperty('taxSetting') && snapshotsProducts.taxSetting.length > 0) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.tax_calculation') }}</span></td></tr>';
                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                    var data = snapshotsProducts.taxSetting[i];
                    if (data.type && data.tax) {
                        if (data.type == "percentage") {
                            tax = (data.tax * total_price) / 100;
                            taxlabeltype = "%";
                            var taxvalue = data.tax;
                        } else {
                            tax = data.tax;
                            taxlabeltype = "";
                            if (currencyAtRight) {
                                var taxvalue = parseFloat(data.tax).toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                var taxvalue = currentCurrency + "" + parseFloat(data.tax).toFixed(decimal_degits);
                            }
                        }
                        taxlabel = data.title;
                    }
                    total_tax_amount += parseFloat(tax);
                    if (!isNaN(tax) && tax != 0) {
                        if (currencyAtRight) {
                            html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype +
                                ')</td><td class="tax_amount" id="greenColor" style="color:green">+' + parseFloat(tax)
                                .toFixed(decimal_degits) + '' + currentCurrency + '</td></tr>';
                        } else {
                            html = html + '<tr><td class="label">' + taxlabel + " (" + taxvalue + taxlabeltype +
                                ')</td><td class="tax_amount" id="greenColor" style="color:green">+' + currentCurrency +
                                parseFloat(tax).toFixed(decimal_degits) + '</td></tr>';
                        }
                    }
                }
                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
            }
            var totalAmount = total_price;
            if (intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) {
                html = html +
                    '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.delivery_charge') }}</span></td></tr>';
                deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);
                totalAmount += parseFloat(deliveryCharge);
                if (currencyAtRight) {
                    deliveryCharge_val = parseFloat(deliveryCharge).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    deliveryCharge_val = currentCurrency + "" + parseFloat(deliveryCharge).toFixed(decimal_degits);
                }
                if (takeAway == '' || takeAway == false) {
                    deliveryChargeVal = deliveryCharge;
                    html = html +
                        '<tr><td class="label">{{ trans('lang.deliveryCharge') }}</td><td class="deliveryCharge" style="color:green">+' +
                        deliveryCharge_val + '</td></tr>';
                }
            }
            if (intRegex.test(tip_amount) || floatRegex.test(tip_amount)) {
                html = html + '<tr><td class="seprater" colspan="2"><hr><span>{{ trans('lang.tip') }}</span></td></tr>';
                tip_amount = parseFloat(tip_amount).toFixed(decimal_degits);
                totalAmount += parseFloat(tip_amount);
                if (currencyAtRight) {
                    tip_amount_val = parseFloat(tip_amount).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    tip_amount_val = currentCurrency + "" + parseFloat(tip_amount).toFixed(decimal_degits);
                }
                if (takeAway == '' || takeAway == false) {
                    html = html +
                        '<tr><td class="label">{{ trans('lang.tip_amount') }}</td><td class="tip_amount_val" style="color:green">+' +
                        tip_amount_val + '</td></tr>';
                }
            }
            html += '<tr><td class="seprater" colspan="2"><hr></td></tr>';
            orderPaytableAmount = totalAmount;
            if (currencyAtRight) {
                total_price_val = parseFloat(totalAmount).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                total_price_val = currentCurrency + "" + parseFloat(totalAmount).toFixed(decimal_degits);
            }
            html = html +
                '<tr class="grand-total"><td class="label">{{ trans('lang.total_amount') }}</td><td class="total_price_val">' +
                total_price_val + '</td></tr>';
            if (intRegex.test(adminCommissionValue) || floatRegex.test(adminCommissionValue)) {
                var adminCommHtml = "";
                if (adminCommissionType == "percentage") {
                    basePrice = (priceWithCommision / (1 + (parseFloat(adminCommissionValue) / 100)));
                    adminCommission_val = parseFloat(priceWithCommision - basePrice);
                    adminCommHtml = "(" + adminCommissionValue + "%)";
                } else {
                    basePrice = priceWithCommision - adminCommissionValue;
                    adminCommission_val = parseFloat(priceWithCommision - basePrice);
                }
                if (currencyAtRight) {
                    adminCommission = parseFloat(adminCommission_val).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    adminCommission = currentCurrency + "" + parseFloat(adminCommission_val).toFixed(decimal_degits);
                }
                // html = html + '<tr><td class="label"><small>{{ trans('lang.admin_commission') }} ' + adminCommHtml +
                //     '</small> </td><td style="color:red"><small>( ' + adminCommission + ' )</small></td></tr>';
            }
            if (notes) {
                html = html + '<tr><td class="label">{{ trans('lang.notes') }}</td><td class="adminCommission_val">' +
                    notes + '</td></tr>';
            }
            return html;
        }
        function PrintElem(elem) {
            jQuery('#' + elem).printThis({
                debug: false,
                importStyle: true,
                loadCSS: [
                    '<?php echo asset('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>',
                    '<?php echo asset('css/style.css'); ?>',
                    '<?php echo asset('css/colors/blue.css'); ?>',
                    '<?php echo asset('css/icons/font-awesome/css/font-awesome.css'); ?>',
                    '<?php echo asset('assets/plugins/toast-master/css/jquery.toast.css'); ?>',
                ],
            });
        }
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
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
        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        var newcountriesjs = JSON.parse(newcountriesjs);
        $('#addDeliverymanModal').on('shown.bs.modal', function() {
            // $('#assignDriverModal').hide();
            $('#assignDriverModal').modal('hide');
        })

        $('#add-deliveryman-btn').on('click', async function () {
            
            $('.err').text('');
            $('.error_top').text('');
            
            var userFirstName = $(".user_first_name").val().trim();
            var userLastName = $(".user_last_name").val().trim();
            var email = $(".user_email").val().trim();
            var password = $(".password").val();
            var countryCode = '+' + $("#country_selector").val();
            var userPhone = $(".user_phone").val().trim();
            
            var serviceFlag = '';
            if(sectionId){
                var sectionRef = await database.collection('sections').doc(sectionId).get();
                var sectionData = sectionRef.data();
                serviceFlag = sectionData.serviceTypeFlag;
            }
            
            var hasError = false;
            if (userFirstName === '') {
                $("#firstname_err").text("{{ trans('lang.user_first_name_help') }}");
                hasError = true;
            }
            if (userLastName === '') {
                $("#lastname_err").text("{{ trans('lang.user_last_name_help') }}");
                hasError = true;
            }
            if (email === '') {
                $("#email_err").text("{{ trans('lang.user_email_help') }}");
                hasError = true;
            }
            if (password === '') {
                $("#password_err").text("{{ trans('lang.user_password_help') }}");
                hasError = true;
            }
            if (userPhone === '') {
                $("#phone_err").text("{{ trans('lang.user_phone_help') }}");
                hasError = true;
            }
            if(!serviceFlag){
                hasError = true;
                $("#error_top").text("{{ trans('lang.something_went_wrong') }}");
            }

            if (hasError) {
                return false; 
            }
            
            var id = database.collection('tmp').doc().id;

            firebase.auth().createUserWithEmailAndPassword(email, password)
                .then(async function (firebaseUser) {
                    user_id = firebaseUser.user.uid;
                    await database.collection('users').doc(user_id).set({
                        'firstName': userFirstName,
                        'lastName': userLastName,
                        'email': email,
                        'countryCode': countryCode,
                        'phoneNumber': userPhone,
                        'role': 'driver',
                        'id': user_id,
                        'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                        'provider': "email",
                        'appIdentifier': "web",
                        'vendorID': vendorId,
                        'active': true,
                        'isDocumentVerify': true,
                        'zoneId': zoneId,
                        'isActive': true,
                        'serviceType': serviceFlag,  
                        'sectionId' : sectionId
                    });
                    window.location.reload();
                })
                .catch(err => {
                    // Show Firebase errors at the top
                    $(".error_top").text(err.message);
                });
        });

        async function getRefund() {
            $('#data-table_processing').show();
            ref.get().then(async function(snapshot) {
                orderData = snapshot.docs[0].data();
                try {
                    const vendorId = orderData.vendor?.author;
                    const customerId = orderData.author?.id;
                    let vendorAmount = 0,
                        deliveryCharge = 0,
                        tipAmount = 0,
                        customerAmount = 0;
                    let vendorFcm = '',
                        customerFcm = '',
                        driverFcm = '';
                    let vendorTaxAmount = 0;
                    let vendorBaseAmount = 0;
                    const walletSnapshot = await database.collection('wallet')
                        .where('user_id', '==', vendorId)
                        .where('order_id', '==', orderData.id)
                        .where('isTopUp', '==', true)
                        .get();
                    for (const doc of walletSnapshot.docs) {
                        const data = doc.data();
                        if (data.payment_method == 'tax') {
                            vendorTaxAmount = parseFloat(data.amount);
                        } else {
                            vendorBaseAmount = parseFloat(data.amount);
                        }
                        vendorAmount += parseFloat(data.amount || 0);
                    }
                    if (vendorAmount) {
                        const vendorDoc = await database.collection('users').doc(vendorId).get();
                        if (vendorDoc.exists) {
                            const vendorData = vendorDoc.data();
                            vendorFcm = vendorData.fcmToken || '';
                            const vendorWallet = parseFloat(vendorData.wallet_amount || 0);
                            await vendorDoc.ref.update({
                                wallet_amount: vendorWallet - vendorAmount
                            });
                        }
                        const walletId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletId).set({
                            amount: vendorBaseAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletId,
                            isTopUp: false,
                            order_id: orderData.id,
                            payment_method: "Wallet",
                            payment_status: 'success',
                            user_id: vendorId,
                            transactionUser: 'vendor',
                            note: 'Order amount refunded to customer'
                        });
                        const walletTaxId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletTaxId).set({
                            amount: vendorTaxAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletTaxId,
                            isTopUp: false,
                            order_id: orderData.id,
                            payment_method: "tax",
                            payment_status: 'success',
                            user_id: vendorId,
                            transactionUser: 'vendor',
                            note: 'Order tax refunded to customer'
                        });
                    }
                    if (orderData.payment_method !== 'cod') {
                        deliveryCharge = parseFloat(orderData.deliveryCharge || 0);
                        tipAmount = parseFloat(orderData.tip_amount || 0);
                        customerAmount = deliveryCharge + tipAmount + vendorAmount + adminCommission;
                        const customerDoc = await database.collection('users').doc(customerId).get();
                        if (customerDoc.exists) {
                            const customerData = customerDoc.data();
                            customerFcm = customerData.fcmToken || '';
                            const customerWallet = parseFloat(customerData.wallet_amount || 0);
                            await customerDoc.ref.update({
                                wallet_amount: customerWallet + customerAmount
                            });
                        }
                        const walletId = database.collection("tmp").doc().id;
                        await database.collection('wallet').doc(walletId).set({
                            amount: customerAmount,
                            date: firebase.firestore.FieldValue.serverTimestamp(),
                            id: walletId,
                            isTopUp: true,
                            order_id: orderData.id,
                            payment_method: "Wallet",
                            payment_status: 'success',
                            user_id: customerId,
                            transactionUser: 'customer',
                            note: 'Order amount refunded'
                        });
                    } else {
                        const customerDoc = await database.collection('users').doc(customerId).get();
                        if (customerDoc.exists) {
                            customerFcm = customerDoc.data().fcmToken || '';
                        }
                    }
                    if (orderData.hasOwnProperty('driverID') && orderData.driverID != null && orderData.driverID != '') {
                        await database.collection('users').doc(orderData.driverID).get().then(async function(snapshot) {
                            let newOrderRequestData = [];
                            let inProgressOrderID = [];
                            if (snapshot.exists) {
                                var driverData = snapshot.data();
                                driverFcm = driverData.fcmToken;
                                if (driverData.orderRequestData !== undefined) {
                                    newOrderRequestData = driverData.orderRequestData.filter(function(oid) {
                                        return oid !== id;
                                    });
                                }
                                if (driverData.inProgressOrderID !== undefined) {
                                    inProgressOrderID = driverData.inProgressOrderID.filter(function(oid) {
                                        return oid !== id;
                                    });
                                }
                                await database.collection('users').doc(driverData.id).update({
                                    'inProgressOrderID': inProgressOrderID,
                                    'orderRequestData': newOrderRequestData
                                })
                            }
                        })
                        await database.collection('vendor_orders').doc(orderData.id).update({
                            'status': 'Order Cancelled',
                            'driverID': null,
                            'driver': null
                        });
                    } else {
                        await database.collection('vendor_orders').doc(orderData.id).update({
                            'status': 'Order Cancelled'
                        });
                    }
                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': customerFcm,
                            'vendorname': manname,
                            'orderStatus': 'Order Cancelled',
                            'subject': selfDeliveryCustomerCancelledSub,
                            'message': selfDeliveryCustomerCancelledMsg
                        },
                        success: function(data) {
                        }
                    });
                    await $.ajax({
                        type: 'POST',
                        url: "<?php echo route('order-status-notification'); ?>",
                        data: {
                            _token: '<?php echo csrf_token(); ?>',
                            'fcm': driverFcm,
                            'vendorname': manname,
                            'orderStatus': 'Order Cancelled',
                            'subject': selfDeliveryDriverCancelledSub,
                            'message': selfDeliveryDriverCancelledMsg
                        },
                        success: function(data) {
                        }
                    });
                    window.location.href = '{{ route('orders') }}';
                } catch (error) {
                    console.error("Error in getRefund:", error);
                }
            })
            $('#data-table_processing').hide();
        }

        function chkAlphabets2(event,msg){
            if(!(event.which>=48  && event.which<=57))
            {
                document.getElementById(msg).innerHTML="Accept only Number";
                return false;
            }
            else
            {
                document.getElementById(msg).innerHTML="";
                return true;
            }
        }
        
        $('#addDeliverymanModal').on('hidden.bs.modal', function () {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });

    </script>
@endsection
