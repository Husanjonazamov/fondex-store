@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.item_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.item_plural') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="row px-5 mb-2">
            <div class="col-12">
                <span class="font-weight-bold text-danger food-limit-note"></span>
            </div>
        </div>
        <div class="container-fluid">
            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                {{ trans('lang.processing') }}
            </div>
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/item_image.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.item_plural') }}</h3>
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
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.item_plural') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.item_table_text') }}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">

                                        <a class="btn-primary btn rounded-full" href="{!! route('items.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{ trans('lang.item_create') }}</a>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="itemTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>

                                                <th>{{ trans('lang.item_info') }}</th>
                                                <th>{{ trans('lang.item_price') }}</th>
                                                <th>{{ trans('lang.item_category_id') }}</th>
                                                <th>{{ trans('lang.item_publish') }}</th>
                                                <th>{{ trans('lang.date_created') }}</th>
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
@endsection

@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var vendorUserId = "<?php echo $id; ?>";
        var vendorId;
        var ref;
        var append_list = '';
        var placeholderImage = '';
        var activeCurrencyref = database.collection('currencies').where('isActive', "==", true);
        var activeCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var subscriptionModel = false;
        var commissionModel = false;
        var section_id = '';
        var subscriptionBusinessModel = database.collection('settings').doc("vendor");
        var date = '';
        var time = '';

        subscriptionBusinessModel.get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });

        async function fetchSectionId() {

            const snapshots = await database.collection('users').where('id', '==', vendorUserId).get();

            if (snapshots.empty) {
                console.error('No user found');
                return;
            }

            var data = snapshots.docs[0].data();
            section_id = data.sectionId;

            await fetchSectionData();
        }

        async function fetchSectionData() {

            const section = database.collection('sections').where('id', '==', section_id);
            const sectionSnapshot = await section.get();

            if (sectionSnapshot.empty) {
                console.error('No section found');
                return;
            }

            var section_data = sectionSnapshot.docs[0].data();

            if (section_data.adminCommision != null && section_data.adminCommision != '') {
                if (section_data.adminCommision.enable) {
                    commissionModel = true;
                }
            }

        }

        fetchSectionId();

        activeCurrencyref.get().then(async function(currencySnapshots) {
            currencySnapshotsdata = currencySnapshots.docs[0].data();
            activeCurrency = currencySnapshotsdata.symbol;
            currencyAtRight = currencySnapshotsdata.symbolAtRight;

            if (currencySnapshotsdata.decimal_degits) {
                decimal_degits = currencySnapshotsdata.decimal_degits;
            }
        })
        getVendorId(vendorUserId).then(data => {
            vendorId = data;
            ref = database.collection('vendor_products').where('vendorID', "==", vendorId);
            $(document).ready(function() {

                    $(document.body).on('click', '.redirecttopage', function() {
                        var url = $(this).attr('data-url');
                        window.location.href = url;
                    });

                    jQuery("#data-table_processing").show();

                    var placeholder = database.collection('settings').doc('placeHolderImage');
                    placeholder.get().then(async function(snapshotsimage) {
                        var placeholderImageData = snapshotsimage.data();
                        placeholderImage = placeholderImageData.image;
                    })

                    var fieldConfig = {
                        columns: [{
                                key: 'name',
                                header: "{{ trans('lang.item_info') }}"
                            },
                            {
                                key: 'finalPrice',
                                header: "{{ trans('lang.item_price') }}"
                            },
                            {
                                key: 'category',
                                header: "{{ trans('lang.item_category_id') }}"
                            },
                            {
                                key: 'publish',
                                header: "{{ trans('lang.item_publish') }}"
                            },
                        ],

                        fileName: "{{ trans('lang.item_table') }}",
                    };

                    const table = $('#itemTable').DataTable({
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
                                const orderableColumns = ['name', 'finalPrice', 'category', '', 'createdDate', '']; // Ensure this matches the actual column names

                                const orderByField = orderableColumns[
                                    orderColumnIndex]; // Adjust the index to match your table

                                if (searchValue.length >= 3 || searchValue.length === 0) {
                                    $('#data-table_processing').show();
                                }

                                await ref.get().then(async function(querySnapshot) {
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

                                        await Promise.all(querySnapshot.docs.map(async (
                                                doc) => {
                                                let childData = doc.data();
                                                childData.id = doc
                                                    .id; // Ensure the document ID is included in the data
                                                var finalPrice = 0;
                                                if (childData.hasOwnProperty(
                                                        'disPrice') && childData
                                                    .disPrice != '' && childData
                                                    .disPrice != '0') {
                                                    finalPrice = childData.disPrice;
                                                } else {
                                                    finalPrice = childData.price;
                                                }
                                                if (childData.hasOwnProperty("createdAt") && childData.createdAt != '' && childData.createdAt != null) {
                                                    try {
                                                        date = childData.createdAt.toDate().toDateString();
                                                        time = childData.createdAt.toDate()
                                                            .toLocaleTimeString('en-US');
                                                    } catch (err) {}
                                                }
                                                var createdAt = date + ' ' + time;
                                                childData.createdDate=createdAt;
                                                childData.foodName = childData.name;
                                                childData.finalPrice = parseInt(
                                                    finalPrice);
                                                var category =
                                                    await productCategory(childData
                                                        .categoryID);
                                                if (category == '') {
                                                    category =
                                                        '{{ trans('lang.unknown') }}';
                                                }
                                                childData.category = category;
                                                childData.publish = childData
                                                    .publish ? 'Yes' : 'No';
                                                if (searchValue) {
                                                    if (
                                                        (childData.name && childData
                                                            .name.toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) ||
                                                        (childData.finalPrice &&
                                                            childData.finalPrice
                                                            .toString().includes(
                                                                searchValue)) ||
                                                        (childData.category &&
                                                            childData.category
                                                            .toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) || (
                                                            childData.publish &&
                                                            childData.publish
                                                            .toString()
                                                            .toLowerCase().includes(
                                                                searchValue)) ||
                                                        (createdAt && createdAt.toString().toLowerCase()
                                                            .indexOf(searchValue) > -1)
                                                ) {
                                                    filteredRecords.push(
                                                        childData);
                                                }
                                            } else {
                                                filteredRecords.push(childData);
                                            }
                                        }));

                                    filteredRecords.sort((a, b) => {
                                        let aValue = a[orderByField];
                                        let bValue = b[orderByField];

                                        if (orderByField === 'finalPrice') {
                                            aValue = a[orderByField] ? parseFloat(a[
                                                orderByField]) : 0.0;
                                            bValue = b[orderByField] ? parseFloat(b[
                                                orderByField]) : 0.0;
                                        }else if (orderByField === 'createdDate' && a[orderByField] != '' && b[orderByField] != '' && a[orderByField] != null && b[orderByField] != null) {
                                            aValue = a[orderByField] ? new Date(a[orderByField]).getTime() : 0;
                                            bValue = b[orderByField] ? new Date(b[orderByField]).getTime() : 0;
                                        }  else {
                                            aValue = a[orderByField] ? a[
                                                    orderByField].toString()
                                                .toLowerCase() : '';
                                            bValue = b[orderByField] ? b[
                                                    orderByField].toString()
                                                .toLowerCase() : ''
                                        }

                                        if (orderDirection === 'asc') {
                                            return (aValue > bValue) ? 1 : -1;
                                        } else {
                                            return (aValue < bValue) ? 1 : -1;
                                        }

                                    });

                                    const totalRecords = filteredRecords.length; $('.total_count').text(totalRecords);
                                    const paginatedRecords = filteredRecords.slice(start,
                                        start + length);

                                    await Promise.all(paginatedRecords.map(async (
                                        childData) => {
                                        var getData = await buildHTML(
                                            childData);

                                        records.push(getData);
                                    }));

                                    $('#data-table_processing').hide(); // Hide loader
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: totalRecords, // Total number of records in Firestore
                                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                                        filteredData: filteredRecords,
                                        data: records // The actual data to display in the table
                                    });
                                }).catch(function(error) {
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
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 3, 5]
                        }, ],
                        order: [4, 'asc'],
                        "language": {
                            "zeroRecords": "{{ trans('lang.no_record_found') }}",
                            "emptyTable": "{{ trans('lang.no_record_found') }}",
                            "processing": "" // Remove default loader
                        },
                        dom: 'lfrtipB',
                        buttons: [{
                            extend: 'collection',
                            text: '<i class="mdi mdi-cloud-download"></i> Export as',
                            className: 'btn btn-info',
                            buttons: [{
                                    extend: 'excelHtml5',
                                    text: 'Export Excel',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'excel', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: 'Export PDF',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'pdf', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'csvHtml5',
                                    text: 'Export CSV',
                                    action: function(e, dt, button, config) {
                                        exportData(dt, 'csv', fieldConfig);
                                    }
                                }
                            ]
                        }],
                        initComplete: function() {
                            $(".dataTables_filter").append($(".dt-buttons").detach());
                            $('.dataTables_filter input').attr('placeholder', 'Search here...')
                                .attr('autocomplete', 'new-password').val('');
                            $('.dataTables_filter label').contents().filter(function() {
                                return this.nodeType === 3;
                            }).remove();
                        }
                    });
            });
        })

        async function buildHTML(val) {
            var html = [];

            var id = val.id;
            var route1 = '{{ route('items.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            var price_val = 0;
            var price_s = '';
            if (val.photo == '') {
                html.push('<img class="rounded" style="width:50px" src="' + placeholderImage +
                    '" alt="image" ><a data-url="' + route1 + '" href="' + route1 +
                    '" class="left_space redirecttopage">' + val.name + '</a>');
            } else {
                html.push('<img class="rounded" style="width:50px" src="' + val.photo +
                    '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage +
                    '\'"><a data-url="' + route1 + '" href="' + route1 + '" class="left_space redirecttopage">' +
                    val.name + '</a>');
            }


            if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {
                if (currencyAtRight) {
                    price_val = parseFloat(val.disPrice).toFixed(decimal_degits) + '' + activeCurrency;
                    price_s = parseFloat(val.price).toFixed(decimal_degits) + '' + activeCurrency;

                } else {
                    price_val = activeCurrency + '' + parseFloat(val.disPrice).toFixed(decimal_degits);
                    price_s = activeCurrency + '' + parseFloat(val.price).toFixed(decimal_degits);
                }
                html.push(price_val + " " + '<s>' + price_s + '</s>');
            } else {
                if (currencyAtRight) {
                    price_val = parseFloat(val.price).toFixed(decimal_degits) + '' + activeCurrency;
                } else {
                    price_val = activeCurrency + '' + parseFloat(val.price).toFixed(decimal_degits);
                }
                html.push(price_val);
            }
            html.push('<span class="category_' + val.categoryID + '">' + val.category + '</span>');
            
            if (val.publish == "Yes") {
                html.push('<span class="badge badge-success">Yes</span>');
            } else {
                html.push('<span class="badge badge-danger">No</span>');
            }
            html.push(val.createdDate);
            html.push('<span class="action-btn"><a href="' + route1 +
                '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + val.id +
                '" class="do_not_delete" name="item-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>'
            );
            return html;
        }

        async function productCategory(category) {
            var productCategory = '';
            await database.collection('vendor_categories').where("id", "==", category).get().then(async function(
                snapshotss) {

                if (snapshotss.docs[0]) {
                    var category_data = snapshotss.docs[0].data();
                    productCategory = category_data.title;
                }
            });
            return productCategory;
        }

        $(document).on("click", "a[name='item-delete']", async function(e) {
            const id = this.id;
            await deleteDocumentWithImage('vendor_products', id, 'photo', 'photos');
            window.location = "{{ !url()->current() }}";
        });

        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            await database.collection('vendors').where('author', "==", vendorUser).get().then(async function(
                vendorSnapshots) {
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
                if (subscriptionModel || commissionModel) {
                    if (vendorData.hasOwnProperty('subscription_plan') && vendorData.subscription_plan != null && vendorData.subscription_plan != '') {
                        itemLimit = vendorData.subscription_plan.itemLimit;
                        if (itemLimit != '-1') {
                            $('.food-limit-note').html(
                                '{{ trans('lang.note') }} : {{ trans('lang.your_item_limit_is') }} ' +
                                itemLimit + ' {{ trans('lang.so_only_first') }} ' + itemLimit +
                                ' {{ trans('lang.items_will_visible_to_customer') }}')
                        }
                    }
                }
            })

            return vendorId;
        }
    </script>
@endsection
