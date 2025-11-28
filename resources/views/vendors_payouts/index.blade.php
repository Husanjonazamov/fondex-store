@extends('layouts.app')


<?php

error_reporting(E_ALL ^ E_NOTICE);
?>

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vendors_payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.vendors_payout_plural')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/payment.png') }}"></span> 
                        <h3 class="mb-0">{{trans('lang.vendors_payout_plural')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.vendors_payout_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.vendors_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">   

                    <a class="btn-primary btn rounded-full" href="{!! route('payments.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.vendors_payout_create')}}</a>
                    
                     </div>
                   </div>                
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="example24"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                       
                                    <th>{{trans('lang.paid_amount')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.vendors_payout_note')}}</th>
                                    <th>Admin {{trans('lang.vendors_payout_note')}}</th>
                                    <th>{{trans('lang.status')}}</th>
                                    <th>{{trans('lang.withdraw_method')}}</th>
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
    var vendorUserId = "<?php echo $id; ?>";
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

    var append_list = '';
    var ref = '';
    var refData = ''
    getVendorId(vendorUserId).then(data => {
        vendorId = data;

        refData = database.collection('payouts').where('vendorID', '==', vendorId);
        ref = refData.orderBy('paidDate', 'desc');

        $(document).ready(function () {

            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            jQuery("#data-table_processing").show();

            var fieldConfig = {
                columns: [
                    { key: 'paidDate', header: "{{trans('lang.date')}}" },
                    { key: 'amount', header: "{{trans('lang.paid_amount')}}" },                            
                    { key: 'note', header: "{{trans('lang.vendors_payout_note')}}" }, 
                    { key: 'adminNote', header: "{{trans('lang.avendors_payout_note')}}" }, 
                    { key: 'paymentStatus', header: "{{trans('lang.status')}}" }, 
                    { key: 'withdrawMethod', header: "{{trans('lang.withdraw_method')}}" }, 
                ],
                
                fileName: "{{trans('lang.vendor_payout_list')}}",
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

                    const orderableColumns = ['amount','paidDate','note','adminNote','paymentStatus','withdrawMethod'];

                    const orderByField = orderableColumns[orderColumnIndex];

                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }

                    try {
                        const querySnapshot = await ref.get();
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
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("paidDate") && childData.paidDate != '') {
                                try {
                                    date = childData.paidDate.toDate().toDateString();
                                    time = childData.paidDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {

                                }
                            }
                            var paidDate = date + ' ' + time ;

                            if (searchValue) {
                                if (
                                    (childData.amount && String(childData.amount).toLowerCase().includes(searchValue)) ||
                                    (childData.note && childData.note.toLowerCase().includes(searchValue)) ||
                                    (childData.adminNote && childData.adminNote.toLowerCase().includes(searchValue)) ||
                                    (childData.paymentStatus && childData.paymentStatus.toLowerCase().includes(searchValue)) ||
                                    (childData.withdrawMethod && childData.withdrawMethod.toLowerCase().includes(searchValue)) ||
                                    (paidDate && paidDate.toString().toLowerCase().indexOf(searchValue) > -1)
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
                            if (orderByField === 'paidDate' && a[orderByField] != '' && b[orderByField] != '') {
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
                columnDefs: [
                    {
                        targets: 1,
                        type: 'date',
                        render: function (data) {

                            return data;
                        }
                    },
                    {orderable: false, targets: [4]},
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
    })

async function buildHTML(val) {
            html=[];
            var price_val = 0;
            if (currencyAtRight) {
                price_val = parseFloat(val.amount).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                price_val = currentCurrency + "" + parseFloat(val.amount).toFixed(decimal_degits);
            }
            html.push('<td>' + price_val + '</td>');
            var date = val.paidDate.toDate().toDateString();
            var time = val.paidDate.toDate().toLocaleTimeString('en-US');

            html.push('<td>' + date + ' ' + time + '</td>');
            if (val.note != undefined) {
                html.push('<td>' + val.note + '</td>');
            } else {
                html.push('<td></td>');
            }
            if (val.adminNote != undefined) {
                html.push('<td>' + val.adminNote + '</td>');
            } else {
                html.push('<td></td>');
            }
            
            html.push('<td>' + val.paymentStatus + '</td>');

            if (val.withdrawMethod) {
                var selectedwithdrawMethod =  val.withdrawMethod == "bank" ? "Bank Transfer" : val.withdrawMethod;
                html.push('<span style="text-transform:capitalize">' + selectedwithdrawMethod + '</span>');
            } else {
                html.push('<td></td>');
            }
        return html;
}

    function prev() {
        if (endarray.length == 1) {
            return false;
        }
        end = endarray[endarray.length - 2];

        if (end != undefined || end != null) {
            jQuery("#data-table_processing").show();


            if (jQuery("#selected_search").val() == 'note' && jQuery("#search").val().trim() != '') {
                listener = refData.orderBy('note').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAt(end).get();

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
            if (jQuery("#selected_search").val() == 'note' && jQuery("#search").val().trim() != '') {

                listener = refData.orderBy('note').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAfter(start).get();

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

    function searchclear() {
        jQuery("#search").val('');
        searchtext();
    }


    function searchtext() {

        jQuery("#data-table_processing").show();

        append_list.innerHTML = '';

        if (jQuery("#selected_search").val() == 'note' && jQuery("#search").val().trim() != '') {

            wherequery = refData.orderBy('note').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').get();

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

    async function getVendorId(vendorUser) {
        var vendorId = '';
        var ref;
        await database.collection('vendors').where('author', "==", vendorUser).get().then(async function (vendorSnapshots) {
            var vendorData = vendorSnapshots.docs[0].data();
            vendorId = vendorData.id;
        })

        return vendorId;
    }


</script>

@endsection
