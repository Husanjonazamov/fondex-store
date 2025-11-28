@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.wallet_transaction_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.wallet_transaction_plural')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/wallet.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.wallet_transaction_plural')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.wallet_transaction_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.wallet_table_text')}}</p>
                   </div>             
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="example24"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                       
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.payment_methods')}}</th>
                                    <th>{{trans('lang.vendors_payout_note')}}</th>
                                    <th>{{trans('lang.payment_status')}}</th>
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
<script>
    var database = firebase.firestore();
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var vendorId = "<?php echo $id; ?>";
    var refData = database.collection('wallet').where('user_id', '==', vendorId).orderBy('date', 'desc');
    var search = jQuery("#search").val();

    $(document.body).on('keyup', '#search', function () {
        search = jQuery(this).val();
    });


    var append_list = '';

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

    var place_image = '';
    var ref_place = database.collection('settings').doc("placeHolderImage");
    ref_place.get().then(async function(snapshots) {

        var placeHolderImage = snapshots.data();
        place_image = placeHolderImage.image;

    });

    $(document).ready(function () {


        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#data-table_processing").show();
       

        var fieldConfig = {
                columns: [
                    { key: 'date', header: "{{trans('lang.date')}}" },
                    { key: 'amount', header: "{{trans('lang.amount')}}" },                            
                    { key: 'payment_method', header: "{{trans('lang.payment_methods')}}" }, 
                    { key: 'note', header: "{{trans('lang.vendors_payout_note')}}" }, 
                    { key: 'payment_status', header: "{{trans('lang.payment_status')}}" }, 
                  
                ],
                fileName: "{{trans('lang.wallet_table')}}",
            };
        const table = $('#example24').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;

                const orderableColumns = ['amount','date','payment_method','note','payment_status',''];

                const orderByField = orderableColumns[orderColumnIndex];

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                try {
                    const querySnapshot = await refData.get();
                    if (!querySnapshot || querySnapshot.empty) {
                        $('.total_count').text(0);
                        $('#data-table_processing').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                        return;
                    }

                    let records = [];
                    let filteredRecords = [];

                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id;
                        var note = '';
                        if (childData.hasOwnProperty('isTopUp') && childData.isTopUp) {
                            note = "{{trans('lang.order_amount_credited')}}";
                        }
                        if (childData.hasOwnProperty('isTopUp') && !childData.isTopUp) {
                            note = "{{trans('lang.admin_commision_debited')}}";
                        }
                        childData.note = note ? note : "";
                        var date = '';
                        var time = '';
                        if (childData.hasOwnProperty("date") && childData.date != '') {
                            try {
                                date = childData.date.toDate().toDateString();
                                time = childData.date.toDate().toLocaleTimeString('en-US');
                            } catch (err) {

                            }
                        }
                        var createdAt = date + ' ' + time ;

                        if (searchValue) {
                            if (
                                (childData.amount && String(childData.amount).toLowerCase().includes(searchValue)) ||
                                (childData.payment_method && childData.payment_method.toLowerCase().includes(searchValue)) ||
                                (childData.note && childData.note.toLowerCase().includes(searchValue)) ||
                                (childData.payment_status && childData.payment_status.toLowerCase().includes(searchValue)) ||
                                (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1)
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));

                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase().trim() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase().trim() : '';
                        if (orderByField === 'date' && a[orderByField] != '' && b[orderByField] != '') {
                            try {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            } catch (err) {
                            }
                        }
                        if (orderByField === 'amount') {
                            aValue = a[orderByField] ? parseFloat(String(a[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                            bValue = b[orderByField] ? parseFloat(String(b[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                        }
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });

                    const totalRecords = filteredRecords.length;
                    $('.total_count').text(totalRecords);
                    const paginatedRecords = filteredRecords.slice(start, start + length);

                    const formattedRecords = await Promise.all(paginatedRecords.map(async (childData) => {
                        return await buildHTML(childData);
                    }));

                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        filteredData: filteredRecords,
                        data: formattedRecords
                    });

                } catch (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                }
            },
            order: [1, 'desc'],
            columnDefs: [{
                targets: 1,
                type: 'date',
                render: function (data) {

                    return data;
                }
            },
                {
                    orderable: false,
                    targets: [2, 4, 5]
                },
                {targets: 0, type: "html-num-fmt"},
            ],
            "language": {
                "zeroRecords": "{{trans('lang.no_record_found')}}",
                "emptyTable": "{{trans('lang.no_record_found')}}",
                "processing": "" // Remove default loader
            },
            dom: 'lfrtipB',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf',fieldConfig);
                            }
                        },   
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv',fieldConfig);
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                $(".dataTables_filter").append($(".dt-buttons").detach());
                $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3; 
                }).remove();
            }
        });
        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        $('#search-input').on('input', debounce(function () {
            const searchValue = $(this).val();
            if (searchValue.length >= 3) {
                $('#data-table_processing').show();
                table.search(searchValue).draw();
            } else if (searchValue.length === 0) {
                $('#data-table_processing').show();
                table.search('').draw();
            }
        }, 300));

    });

    async function buildHTML(val) {
        var html = [];

        amount = val.amount;
        if (!isNaN(amount)) {
            amount = parseFloat(amount).toFixed(decimal_degits);
        }

        var note = '';
        if (val.hasOwnProperty('isTopUp') && val.isTopUp) {
           // note = "{{trans('lang.order_amount_credited')}}";

            if (currencyAtRight) {
                html.push('<td class="text-green" data-html="true" data-order="' + amount + '"><span class="text-green">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</span></td>');
            } else {
                html.push('<td class="text-green" data-html="true" data-order="' + amount + '"><span class="text-green">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</span></td>');
            }
        } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {
           // note = "{{trans('lang.admin_commision_debited')}}";

            if (currencyAtRight) {
                html.push('<td class="text-red" data-html="true" data-order="' + amount + '"><span class="text-red">(' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + ')</span></td>');
            } else {
                html.push('<td class="text-red" data-html="true" data-order="' + amount + '"><span class="text-red">(' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + ')</span></td>');
            }

        } else {
            if (currencyAtRight) {
                html.push('<td class="" data-html="true" data-order="' + amount + '"><span class="text-green">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</span></td>');
            } else {
                html.push('<td class="" data-html="true" data-order="' + amount + '"><span class="text-green">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</span></td>');
            }
        }


        var date = "";
        var time = "";
        try {
            if (val.hasOwnProperty("date")) {
                date = val.date.toDate().toDateString();
                time = val.date.toDate().toLocaleTimeString('en-US');
            }
        } catch (err) {

        }

        html.push('<td>' + date + ' ' + time + '</td>');

        var payment_method = '';

        if (val.payment_method) {


            if (val.payment_method == "Stripe") {
                image = '{{asset("images/payment/stripe.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "RazorPay") {
                image = '{{asset("images/payment/razorepay.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'"  >';

            } else if (val.payment_method == "Paypal") {
                image = '{{asset("images/payment/paypal.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "PayFast") {
                image = '{{asset("images/payfast.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "PayStack") {
                image = '{{asset("images/payment/paystack.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "FlutterWave") {
                image = '{{asset("images/payment/flutter_wave.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "Mercado Pago") {
                image = '{{asset("images/payment/marcado_pago.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "Wallet" || val.payment_method == "tax") {
                image = '{{asset("images/payment/emart_wallet.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "Paytm") {
                image = '{{asset("images/payment/paytm.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "Cancelled Order Payment") {
                image = '{{asset("images/payment/cancel_order.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';

            } else if (val.payment_method == "Refund Amount") {
                image = '{{asset("images/payment/refund_amount.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';
            } else if (val.payment_method == "Referral Amount") {
                image = '{{asset("images/payment/reffral_amount.png")}}';
                payment_method = '<img class="size" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + place_image + '\'" >';
            } else {
                payment_method = val.payment_method;
            }
        }

        html.push('<td class="payment_images">' + payment_method + '</td>');
        html.push('<td>' + val.note + '</td>');

        if (val.payment_status == 'success') {
            html.push('<td class="success"><span class="badge badge-success">' + val.payment_status + '</span></td>');
        } else if (val.payment_status == 'undefined') {
            html.push('<td class="undefined"><span class="badge badge-secondary">' + val.payment_status + '</span></td>');
        } else if (val.payment_status == 'Refund success') {
            html.push('<td class="refund_success"><span class="badge badge-success">' + val.payment_status + '</span></td>');
        } else {
            html.push('<td><span class="badge badge-info">' + val.payment_status + '</span></td>');
        }

        if (val.hasOwnProperty('order_id') && val.order_id != null && val.order_id != "") {
            var order_view = '{{route("orders.edit",":id")}}';
            order_view = order_view.replace(':id', val.order_id);
            html.push('<span class="action-btn"><a href="' + order_view + '"><i class="mdi mdi-eye"></i></a></span>');
        } else {
            html.push('<td></td>');
        }

        return html;

    }
</script>


@endsection
