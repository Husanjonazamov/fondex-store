@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.order_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.order_plural')}}</li>
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
                        <h3 class="mb-0">{{trans('lang.order_plural')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.order_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.order_table_text')}}</p>
                   </div>              
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="orderTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{trans('lang.order_id')}}</th>
                                    <th>{{trans('lang.order_user_id')}}</th>
                                    <th>{{trans('lang.order_order_status_id')}}</th>
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.order_type')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>                         
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
    var user_id = "<?php echo $id; ?>";
    var user_number = [];
    var refData = database.collection('vendor_orders').where('vendor.author', "==", user_id);
    var ref = database.collection('vendor_orders').orderBy('createdAt', 'desc').where('vendor.author', "==", user_id);

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
       
        var fieldConfig = {
                columns: [
                    { key: 'id', header: "{{trans('lang.order_id')}}" },
                    { key: 'clientName', header: "{{trans('lang.order_user_id')}}" },                            
                    { key: 'status', header: "{{trans('lang.order_order_status_id')}}" }, 
                    { key: 'amount', header: "{{trans('lang.amount')}}" },
                    { key: 'orderType', header: "{{trans('lang.order_type')}}" },
                    { key: 'createdAt', header: "{{trans('lang.date')}}" },
                    
                ],
                
                fileName: "{{trans('lang.order_list')}}",
            };
       
        const table = $('#orderTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;                    
                const orderableColumns = ['id', 'clientName', 'status', 'amount', 'orderType', 'createdAt','']; // Ensure this matches the actual column names
                
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                await ref.get().then(async function (querySnapshot) {
                    if (querySnapshot.empty) {
                        $('.total_count').text(0);
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }

                    let records = [];
                    let filteredRecords = [];

                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        if(childData.hasOwnProperty('author') && childData.author != ''){
                            childData.clientName = childData.author.firstName + ' ' + childData.author.lastName;                            
                        }else{
                            childData.clientName = '{{trans("lang.unknown")}}';
                        }
                        var price =  await buildHTMLProductstotal(childData);
                        childData.amount = price;
                        
                        if (val.takeAway) {
                            childData.orderType = '{{trans("lang.order_takeaway")}}';
                        } else {
                            childData.orderType = '{{trans("lang.order_delivery")}}';
                        }  
                        
                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }                                
                            }
                            var createdAt = date + ' ' + time;
                            if (
                                (childData.clientName && childData.clientName.toString().toLowerCase().includes(searchValue)) ||
                                (childData.amount && childData.amount.toString().includes(searchValue))
                                || (childData.orderType && childData.orderType.toString().toLowerCase().includes(searchValue)) || (childData.amount && childData.amount.toString().toLowerCase().includes(searchValue)) || (childData.id && childData.id.toString().toLowerCase().includes(searchValue)) || (createdAt && createdAt.toString().toLowerCase().includes(searchValue)) || (childData.status && childData.status.toString().toLowerCase().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));
                    
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ;
                        let bValue = b[orderByField] ;                       
                        if (orderByField === 'amount') {  
                            aValue = a[orderByField] ? parseFloat(String(a[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                            bValue = b[orderByField] ? parseFloat(String(b[orderByField]).replace(/[^0-9.]/g, '')) || 0 : 0;
                        }else{
                            aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : ''
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
                
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData = await buildHTML(childData);
                        
                        records.push(getData);
                    }));

                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        recordsFiltered: totalRecords, 
                        filteredData: filteredRecords,
                        data: records // The actual data to display in the table
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },
            columnDefs: [
                {orderable: false, targets: [6]},
            ],
            order: [5, 'desc'],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
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

    });

    async function buildHTML(val) {
        html=[];
        var id = val.id;
        var route1 = '{{route("orders.edit",":id")}}';
        route1 = route1.replace(':id', id);

        var printRoute = '{{route("vendors.orderprint",":id")}}';
        printRoute = printRoute.replace(':id', id);


        html.push('<a href="'+route1+'">' + val.id + '</a>');
        html.push(val.clientName);

        if (val.status == 'Order Placed') {
            html.push('<span class="badge badge-warning ">' + val.status + '</span>');

        } else if (val.status == 'Order Accepted') {
            html.push('<span class="badge badge-info ">' + val.status + '</span>');

        } else if (val.status == 'Order Rejected') {
            html.push('<span class="badge badge-danger ">' + val.status + '</span>');

        } else if (val.status == 'Driver Pending') {
            html.push('<span class="badge badge-secondary ">' + val.status + '</span>');

        } else if (val.status == 'Driver Rejected') {
            html.push('<span class="badge badge-danger ">' + val.status + '</span>');

        } else if (val.status == 'Order Shipped') {
            html.push('<span class="badge badge-primary ">' + val.status + '</span>');

        } else if (val.status == 'In Transit') {
            html.push('<span class="badge badge-info ">' + val.status + '</span>');

        } else if (val.status == 'Order Completed') {
            html.push('<span class="badge badge-success ">' + val.status + '</span>');

        }else if(val.status == 'Order Cancelled') {
            html.push('<span class="badge badge-dark ">' + val.status + '</span>');

        }else{
            html.push('');
        }

        html.push(val.amount);
        html.push(val.orderType);

        var date = '';
        var time = '';
        if (val.hasOwnProperty("createdAt")) {
            try {
                date = val.createdAt.toDate().toDateString();
                time = val.createdAt.toDate().toLocaleTimeString('en-US');
            } catch (err) {

            }
            html.push('<span class="dt-time">' + date + ' ' + time + '</span>');
        } else {
            html.push('');
        }

        html.push('<span class="action-btn"><a href="' + printRoute + '"><i class="mdi mdi-printer" style="font-size:20px;"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + val.id + '" class="do_not_delete" name="order-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>');
        return html;
    }

    $(document).on("click", "a[name='order-delete']", function (e) {
        var id = this.id;
        database.collection('vendor_orders').doc(id).delete().then(function (result) {
            window.location.href = '{{ url()->current() }}';
        });


    });

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

        /* aarti's code */
        var special_dicount = 0;
       
            if (snapshotsProducts.hasOwnProperty('specialDiscount')) {
                
                if (snapshotsProducts.specialDiscount.specialType && snapshotsProducts.specialDiscount.special_discount) {
                        special_dicount = snapshotsProducts.specialDiscount.special_discount;
                       
                }
            }
      

        if(special_dicount)    
        {
            total_price = total_price - special_dicount;
        }

        /* end code */

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
