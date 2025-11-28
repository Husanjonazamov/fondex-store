@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.subscription_list') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.subscription_list') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="container-fluid">
        <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/subscription.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.subscription_list')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.subscription_list')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.subscription_list_table_text')}}</p>
                   </div>
                                  
                 </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="example24"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.image') }}</th>
                                            <th>{{ trans('lang.plan_name') }}</th>
                                            <th>{{ trans('lang.price') }}</th>
                                            <th>{{ trans('lang.payment_method') }}</th>
                                            <th>{{ trans('lang.active_at') }}</th>
                                            <th>{{ trans('lang.expire_at') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
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
    </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var vedorUserId = "<?php echo $id; ?>";
        var placeholderImage = '';
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var activePlan='';
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
      
        ref = database.collection('subscription_history');
        $(document).ready(function() {
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            jQuery("#data-table_processing").show();
            const table = $('#example24').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: async function(data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['', 'name', 'price', 'payment_type', 'createdAt',
                        'expiry_date', ''
                    ]; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {
                        const querySnapshot = await ref.where('user_id', '==', vedorUserId).orderBy('createdAt','desc').get();
                        if (!querySnapshot || querySnapshot.empty) {
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
                        
                        await Promise.all(querySnapshot.docs.map(async (doc,index) => {
                            let childData = doc.data();
                            if(index==0){
                                activePlan=childData.id;
                            }
                            childData.id = doc
                                .id; // Ensure the document ID is included in the data
                            if (currencyAtRight) {
                                price = parseFloat(childData.subscription_plan.price)
                                    .toFixed(decimal_degits) + "" + currentCurrency;
                            } else {
                                price = currentCurrency + "" + parseFloat(childData
                                    .subscription_plan.price).toFixed(
                                    decimal_degits);
                            }
                            childData.price = price ? price : 0.00;
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("expiry_date") && childData
                                .expiry_date != '' && childData.expiry_date != null) {
                                try {
                                    date = childData.expiry_date.toDate()
                                        .toDateString();
                                    time = childData.expiry_date.toDate()
                                        .toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var expiresAt = date + ' ' + time;
                            childData.expiresAt = expiresAt;
                            if (childData.hasOwnProperty("createdAt") && childData
                                .createdAt != '' && childData.createdAt != null) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate()
                                        .toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var createdAt = date + ' ' + time;
                            childData.buyOn = createdAt;
                            if (searchValue) {
                                if (
                                    (childData.subscription_plan.name && childData
                                        .subscription_plan.name.toString().toLowerCase()
                                        .includes(searchValue)) ||
                                    (childData.subscription_plan.price && childData
                                        .subscription_plan.price.toString()
                                        .toLowerCase().includes(searchValue)) ||
                                    (childData.payment_type && childData.payment_type
                                        .toString().toLowerCase().includes(searchValue)
                                    ) ||
                                    (expiresAt && expiresAt.toString().toLowerCase()
                                        .indexOf(searchValue) > -1) ||
                                    (createdAt && createdAt.toString().toLowerCase()
                                        .indexOf(searchValue) > -1)) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString()
                                .toLowerCase()
                                .trim() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString()
                                .toLowerCase()
                                .trim() : '';
                            if (orderByField === 'expiresAt' && a[orderByField] != '' && b[
                                    orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField]
                                        .toDate())
                                        .getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField]
                                        .toDate())
                                        .getTime() : 0;
                                } catch (err) {}
                            }
                            aValue = a[orderByField] ? a[orderByField].toString().toLowerCase()
                                .trim() : '';
                            bValue = b[orderByField] ? b[orderByField].toString().toLowerCase()
                                .trim() : '';
                            if (orderByField === 'price') {
                                aValue = a[orderByField] ? parseFloat(a[orderByField].replace(
                                    /[^0-9.]/g, '')) || 0 : 0;
                                bValue = b[orderByField] ? parseFloat(b[orderByField].replace(
                                    /[^0-9.]/g, '')) || 0 : 0;
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
                        
                        const formattedRecords = await Promise.all(paginatedRecords.map(async (
                            childData) => {
                            
                            return await buildHTML(childData);
                            
                        }));
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            data: formattedRecords
                        });
                    } catch (error) {
                        console.error("Error fetching data from Firestore:", error);
                        jQuery('#overlay').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }
                },
                order: [
                    ['4', 'desc']
                ],
                columnDefs: [{
                        targets: 4,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        orderable: false,
                        targets: [0, 6]
                    },
                ],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}"
                },
            });
            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }
        });
        async function buildHTML(val) {
            var html = [];
            var id = val.id;
            var activeClass= (id==activePlan) ? '<span class="badge badge-success">{{trans("lang.active")}}</span>' : '';
            var route1 = "{{ route('my-subscription.show', ['id' => ':id']) }}".replace(':id', id);
            html.push('<td><img  onerror="this.onerror=null;this.src=\'' + placeholderImage +
                '\'" class="rounded" style="width:50px" src="' + val.subscription_plan.image + '" alt="image"></td>'
                );
            html.push('<td>' + val.subscription_plan.name +' '+ activeClass+'</td>');
            html.push('<td>' + val.price + '</td>');
            if (val.payment_type.toString().toLowerCase() == "stripe") {
                image = '{{ asset('images/stripe.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '"  onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "razorpay") {
                image = '{{ asset('images/razorpay.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "paypal") {
                image = '{{ asset('images/paypal.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase()== "payfast") {
                image = '{{ asset('images/payfast.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase()== "paystack") {
                image = '{{ asset('images/paystack.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "flutterwave") {
                image = '{{ asset('images/flutterwave.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "mercadopago") {
                image = '{{ asset('images/marcadopago.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase()== "wallet") {
                image = '{{ asset('images/emart_wallet.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "paytm") {
                image = '{{ asset('images/paytm.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "xendit") {
                image = '{{ asset('images/xendit.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "orangepay") {
                image = '{{ asset('images/orangeMoney.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else if (val.payment_type.toString().toLowerCase() == "midtrans") {
                image = '{{ asset('images/midtrans.png') }}';
                payment_method = '<img style="width:100px" alt="image" src="' + image + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
            } else {
                payment_method = val.payment_type;
            }
            html.push('<td>' + payment_method + '</td>');
            html.push('<td>' + val.buyOn + '</td>');
            (val.expiry_date!=null) ? html.push('<td>' + val.expiresAt + '</td>') : html.push('<td>{{trans("lang.unlimited")}}</td>')            
            html.push('<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-eye"></i></a></span>');
            return html;
        }
    </script>
@endsection
