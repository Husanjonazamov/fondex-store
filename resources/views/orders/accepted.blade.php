@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.accepted_orders')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item "><a href="{{url('orders')}}">{{trans('lang.order_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.accepted_orders')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
        {{trans('lang.processing')}}
        </div>
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/order.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.accepted_orders')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>  
                  
                </div>
            </div>
        </div> 
    
       </div>
       <div class="table-list">
       <div class="row">
           <div class="col-12">
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.accepted_orders')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.acceptorder_table_text')}}</p>
                   </div>              
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="example24"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.order_user_id')}}</th>
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.order_type')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>     
                                <tbody id="append_list1">


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


@endsection


@section('scripts')
    <script type="text/javascript">

        var database = firebase.firestore();
        var offest = 1;
        var pagesize = 50;
        var end = null;
        var endarray = [];
        var start = null;
        var user_id = "<?php echo $id; ?>";

        var refData = database.collection('vendor_orders').where('vendor.author', "==", user_id).where('status', '==', 'Order Accepted');
        var ref = database.collection('vendor_orders').orderBy('createdAt', 'desc').where('vendor.author', "==", user_id).where('status', '==', 'Order Accepted');

        var append_list = '';
        var user_number = [];

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

        $(document).ready(function () {

            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });

            jQuery("#data-table_processing").show();
            append_list = document.getElementById('append_list1');
            append_list.innerHTML = '';
            ref.get().then(async function (snapshots) {
                html = '';
                html = await buildHTML(snapshots);
                if (snapshots.docs.length > 0) {
                    $('.total_count').text(snapshots.docs.length); 
                    html = await buildHTML(snapshots);
                }
                else
                {
                    $('.total_count').text(0); 
                }
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                }

                if (snapshots.docs.length < pagesize) {

                    jQuery("#data-table_paginate").hide();
                } else {

                    jQuery("#data-table_paginate").show();
                }
                var table = $('#example24').DataTable({
                    order: [],
                    columnDefs: [
                        {
                            targets: 4,
                            type: 'date',
                            render: function (data) {

                                return data;
                            }
                        },
                        {orderable: false, targets: [5]},
                    ],
                    order: [['4', 'desc']],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                    responsive: true
                });
                table.on('search.dt', function() {
                    var filteredCount = table.rows({ search: 'applied' }).count();
                    $('.total_count').text(filteredCount);  // Update count
                });

            });

        });

        async function buildHTML(snapshots) {
            var html='';
            await Promise.all(snapshots.docs.map(async (listval) => {
                var val = listval.data();
                let result = user_number.filter(obj => {
                    return obj.id == val.author;
                })

                if (result.length > 0) {
                    val.phoneNumber = result[0].phoneNumber;
                    val.isActive = result[0].isActive;

                } else {
                    val.phoneNumber = '';
                    val.isActive = false;
                }

                var getData = await getListData(val);
                html += getData;
            }));
            return html;
        }

        async function getListData(val) {
            html='';
            html = html + '<tr>';
            newdate = '';
            var id = val.id;
            var route1 = '{{route("orders.edit",":id")}}';
            route1 = route1.replace(':id', id);

            var printRoute = '{{route("vendors.orderprint",":id")}}';
            printRoute = printRoute.replace(':id', id);


            html = html + '<td data-url="' + route1 + '" class="redirecttopage">' + val.id + '</td>';
            html = html + '<td>' + val.author.firstName + ' ' + val.author.lastName + '</td>';

            var price = 0;

            var price =  buildHTMLProductstotal(val);

            html = html + '<td>' + price + '</td>';
            if (val.takeAway) {
                html = html + '<td>{{trans("lang.order_takeaway")}}</td>';

            } else {
                html = html + '<td>{{trans("lang.order_delivery")}}</td>';
            }
            var date = '';
        var time = '';
        if (val.hasOwnProperty("createdAt")) {
            try {
                date = val.createdAt.toDate().toDateString();
                time = val.createdAt.toDate().toLocaleTimeString('en-US');
            } catch (err) {

            }
            html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';
        } else {
            html = html + '<td></td>';
        }


            html = html + '<td class="action-btn"><a href="' + printRoute + '"><i class="fa fa-print" style="font-size:20px;"></i></a><a href="' + route1 + '"><i class="fa fa-edit"></i></a><a id="' + val.id + '" class="do_not_delete" name="order-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>';


            html = html + '</tr>';
            return html;

        }

        function prev() {
            if (endarray.length == 1) {
                return false;
            }
            end = endarray[endarray.length - 2];

            if (end != undefined || end != null) {
                jQuery("#data-table_processing").show();
                if (jQuery("#selected_search").val() == 'id' && jQuery("#search").val().trim() != '') {

                    listener = refData.orderBy('id').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAt(end).get();
                } else {
                    listener = ref.startAt(end).limit(pagesize).get();
                }

                listener.then((snapshots) => {
                    html = '';
                    html = buildHTML(snapshots);
                    jQuery("#data-table_processing").hide();
                    if (html != '') {
                        append_list.innerHTML = html;
                        start = snapshots.docs[snapshots.docs.length - 1];
                        endarray.splice(endarray.indexOf(endarray[endarray.length - 1]), 1);

                        if (snapshots.docs.length < pagesize) {

                            jQuery("#users_table_previous_btn").hide();
                        }

                    }
                });
            }
        }


        function next() {
            if (start != undefined || start != null) {

                jQuery("#data-table_processing").hide();

                if (jQuery("#selected_search").val() == 'id' && jQuery("#search").val().trim() != '') {

                    listener = refData.orderBy('id').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAt(end).get();
                } else {
                    listener = ref.startAfter(start).limit(pagesize).get();
                }
                listener.then((snapshots) => {

                    html = '';
                    html = buildHTML(snapshots);
                    jQuery("#data-table_processing").hide();
                    if (html != '') {
                        append_list.innerHTML = html;
                        start = snapshots.docs[snapshots.docs.length - 1];

                        if (endarray.indexOf(snapshots.docs[0]) != -1) {
                            endarray.splice(endarray.indexOf(snapshots.docs[0]), 1);
                        }
                        endarray.push(snapshots.docs[0]);
                    }
                });
            }
        }

        $(document).on("click", "a[name='order-delete']", function (e) {
            var id = this.id;
            database.collection('vendor_orders').doc(id).delete().then(function (result) {
                window.location.href = '{{ url()->current() }}';
            });


        });

        function searchclear() {
            jQuery("#search").val('');
            location.reload();
        }

        function searchtext() {

            var offest = 1;
            jQuery("#data-table_processing").show();

            append_list.innerHTML = '';

            if (jQuery("#selected_search").val() == 'id' && jQuery("#search").val().trim() != '') {

                wherequery = refData.orderBy('id').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').get();

            } else {

                wherequery = ref.limit(pagesize).get();
            }

            wherequery.then((snapshots) => {
                html = '';
                html = buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];

                    endarray.push(snapshots.docs[0]);
                    if (snapshots.docs.length < pagesize) {

                        jQuery("#data-table_paginate").hide();
                    } else {

                        jQuery("#data-table_paginate").show();
                    }
                }
            });

        }

        function buildHTMLProductstotal(snapshotsProducts) {

            var adminCommission = snapshotsProducts.adminCommission;
            var discount = snapshotsProducts.discount;
            var couponCode = snapshotsProducts.couponCode;
            var extras = snapshotsProducts.extras;
            var extras_price = snapshotsProducts.extras_price;
            var rejectedByDrivers = snapshotsProducts.rejectedByDrivers;
            var takeAway = snapshotsProducts.takeAway;
            var tip_amount = snapshotsProducts.tip_amount;
            var status = snapshotsProducts.status;
            var products = snapshotsProducts.products;
            var deliveryCharge = snapshotsProducts.deliveryCharge;
            var totalProductPrice = 0;
            var total_price = 0;

            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

            if (products) {

                products.forEach((product) => {

                    var val = product;

                    price_item = parseFloat(val.price).toFixed(decimal_degits);

                    extras_price_item = (parseFloat(val.extras_price) * parseInt(val.quantity)).toFixed(decimal_degits);

                    totalProductPrice = parseFloat(price_item) * parseInt(val.quantity);
                    var extras_price = 0;
                    if (parseFloat(extras_price_item) != NaN && val.extras_price != undefined) {
                        extras_price = extras_price_item;
                    }
                    totalProductPrice = parseFloat(extras_price) + parseFloat(totalProductPrice);
                    totalProductPrice = parseFloat(totalProductPrice).toFixed(decimal_degits);

                    total_price += parseFloat(totalProductPrice);

                });
            }

            if (intRegex.test(discount) || floatRegex.test(discount)) {

                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);

                if (currencyAtRight) {
                    discount_val = discount + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + discount;
                }

            }

            var tax = 0;
            taxlabel = '';
            taxlabeltype = '';

            if (snapshotsProducts.hasOwnProperty('taxSetting')) {
                var total_tax_amount = 0;
                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                    var data = snapshotsProducts.taxSetting[i];

                    if (data.type && data.tax) {
                        if (data.type == "percentage") {
                            tax = (data.tax * total_price) / 100;
                            taxlabeltype = "%";
                        } else {
                            tax = data.tax;
                            taxlabeltype = "fix";
                        }
                        taxlabel = data.title;
                    }
                    total_tax_amount += parseFloat(tax);
                }
                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
            }

            if ((intRegex.test(deliveryCharge) || floatRegex.test(deliveryCharge)) && !isNaN(deliveryCharge)) {

                deliveryCharge = parseFloat(deliveryCharge).toFixed(decimal_degits);
                total_price += parseFloat(deliveryCharge);

                if (currencyAtRight) {
                    deliveryCharge_val = deliveryCharge + "" + currentCurrency;
                } else {
                    deliveryCharge_val = currentCurrency + "" + deliveryCharge;
                }
            }


            if (intRegex.test(tip_amount) || floatRegex.test(tip_amount) && !isNaN(tip_amount)) {

                tip_amount = parseFloat(tip_amount).toFixed(decimal_degits);
                total_price += parseFloat(tip_amount);
                total_price = parseFloat(total_price).toFixed(decimal_degits);

                if (currencyAtRight) {
                    tip_amount_val = tip_amount + "" + currentCurrency;
                } else {
                    tip_amount_val = currentCurrency + "" + tip_amount;
                }
            }

            if (currencyAtRight) {
                var total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }


            return total_price_val;
        }

    </script>


@endsection
