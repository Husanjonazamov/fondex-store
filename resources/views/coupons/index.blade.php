@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.coupon_table')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/coupon.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.coupon_table')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.coupon_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.coupon_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">  
                        <a class="btn-primary btn rounded-full" href="{!! route('coupons.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.coupon_create')}}</a>
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
                                       
                                <th>{{trans('lang.coupon_code')}}</th>

                                <th>{{trans('lang.coupon_discount')}}</th>

                                <th>{{trans('lang.coupon_description')}}</th>
                                <th>{{trans('lang.coupon_privacy')}}</th>

                                <th>{{trans('lang.coupon_expires_at')}}</th>

                                <th>{{trans('lang.coupon_enabled')}}</th>

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
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var vendorUserId = "<?php echo $id; ?>";
    var vendorID = '';

    var decimal_degits = 0;
    var currentCurrency = '';
    var currencyAtRight = false;
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
    getVendorId(vendorUserId).then(data => {
        vendorID = data;
        ref = database.collection('coupons').where('vendorID', '==', vendorID);
        $(document).ready(function () {

            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            jQuery("#data-table_processing").show();
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

                    const orderableColumns = ['code','discount','description','privacy','expiresAt','isinable',''];

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

                            var privacy = '';
                            if (childData.hasOwnProperty('isPublic') && childData.isPublic) {
                                privacy = "{{trans("lang.public")}}";
                            } else {
                                privacy = "{{trans("lang.private")}}";
                            }
                            childData.privacy = privacy ? privacy : 0.00;
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("expiresAt") && childData.expiresAt != '') {
                                try {
                                    date = childData.expiresAt.toDate().toDateString();
                                    time = childData.expiresAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {

                                }
                            }
                            var expiresAt = date + ' ' + time ;

                            var isinable = '';
                            if (childData.isEnabled) {
                                isinable = "Yes";
                            } else {
                                isinable = "No";
                            }
                            childData.isinable = isinable ? isinable : '';

                            if (searchValue) {
                                if (
                                    (childData.code && childData.code.toLowerCase().includes(searchValue)) ||
                                    (childData.discount && String(childData.discount).toLowerCase().includes(searchValue)) ||
                                    (childData.description && childData.description.toLowerCase().includes(searchValue)) ||
                                    (childData.privacy && childData.privacy.toLowerCase().includes(searchValue)) ||
                                    (isinable && isinable.toLowerCase().includes(searchValue)) ||
                                    (expiresAt && expiresAt.toString().toLowerCase().indexOf(searchValue) > -1)
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
                            if (orderByField === 'expiresAt' && a[orderByField] != '' && b[orderByField] != '') {
                                try {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                } catch (err) {
                                }
                            }
                            if (orderByField === 'discount') {
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
                order: [4, 'desc'],
                columnDefs: [
                    {
                        targets: 4,
                        type: 'date',
                        render: function (data) {

                            return data;
                        }
                    },
                    {orderable: false, targets: [3,5,6]},
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}",
                    "processing": "" // Remove default loader
                },
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
            newdate = '';
            if (currencyAtRight) {
                if (val.discountType == 'Percent' || val.discountType == 'Percentage') {
                    discount_price = val.discount + "%";
                } else {
                    discount_price = parseFloat(val.discount).toFixed(decimal_degits) + "" + currentCurrency;
                }
            } else {
                if (val.discountType == 'Percent' || val.discountType == 'Percentage') {
                    discount_price = val.discount + "%";
                } else {
                    discount_price = currentCurrency + "" + parseFloat(val.discount).toFixed(decimal_degits);
                }
            }
            var id = val.id;
            var route1 = '{{route("coupons.edit",":id")}}';
            route1 = route1.replace(':id', id);
            html.push('<td  data-url="' + route1 + '" class="redirecttopage">' + val.code + '</td>');
            html.push('<td>' + discount_price + '</td>');
             html.push('<td>' + val.description + '</td>');
            if (val.hasOwnProperty('isPublic') && val.isPublic) {
                html.push('<td class="success"><span class="badge badge-success py-2 px-3">{{trans("lang.public")}}</sapn></td>');
        } else {
                html.push('<td class="danger"><span class="badge badge-danger py-2 px-3">{{trans("lang.private")}}</sapn></td>');
        }
            var date = '';
            var time = '';
            if (val.hasOwnProperty("expiresAt")) {

                try {
                    date = val.expiresAt.toDate().toDateString();
                    time = val.expiresAt.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
                html.push('<td>' + date  +' ' + time  +'</td>');
            } else {
                html.push('<td></td>');
            }
            if (val.isEnabled) {
                html.push('<td><span class="badge badge-success">Yes</span></td>');
            } else {
                html.push('<td><span class="badge badge-danger">No</span></td>');
            }
            html.push('<span class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + val.id + '" name="coupon_delete_btn" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>');

            return html;
        }

    function prev() {
        if (endarray.length == 1) {
            return false;
        }
        end = endarray[endarray.length - 2];

        if (end != undefined || end != null) {
            jQuery("#data-table_processing").show();

            if (jQuery("#selected_search").val() == 'code' && jQuery("#search").val().trim() != '') {

                listener = ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAt(end).get();
            } else if (jQuery("#selected_search").val() == 'description' && jQuery("#search").val().trim() != '') {

                listener = ref.orderBy('description').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAt(end).get();

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

            if (jQuery("#selected_search").val() == 'code' && jQuery("#search").val().trim() != '') {

                listener = ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAfter(start).get();
            } else if (jQuery("#selected_search").val() == 'description' && jQuery("#search").val().trim() != '') {
                listener = ref.orderBy('description').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').startAfter(start).get();
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

        if (jQuery("#selected_search").val() == 'code' && jQuery("#search").val().trim() != '') {

            wherequery = ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').get();

        } else if (jQuery("#selected_search").val() == 'description' && jQuery("#search").val().trim() != '') {

            wherequery = ref.orderBy('description').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val() + '\uf8ff').get();

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

    $(document).on("click", "a[name='coupon_delete_btn']", async function (e) {
        var id = this.id;
        await deleteDocumentWithImage('coupons', id, 'image');
        window.location = "{{! url()->current() }}";
    });

    async function getVendorId(vendorUser) {
        var vendorID = '';
        var ref;
        await database.collection('vendors').where('author', "==", vendorUser).get().then(async function (vendorSnapshots) {
            var vendorData = vendorSnapshots.docs[0].data();
            vendorID = vendorData.id;
        })

        return vendorID;
    }

</script>

@endsection
