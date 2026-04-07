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
            if (!currencySnapshots.empty) {
                currencySnapshotsdata = currencySnapshots.docs[0].data();
                activeCurrency = currencySnapshotsdata.symbol;
                currencyAtRight = currencySnapshotsdata.symbolAtRight;

                if (currencySnapshotsdata.decimal_degits) {
                    decimal_degits = currencySnapshotsdata.decimal_degits;
                }
            }
        });

        $(document.body).on('click', '.redirecttopage', function() {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });

        // Initialize DataTable immediately so it doesn't hang if getVendorId fails
        $(document).ready(function() {
            jQuery("#data-table_processing").show();

            var placeholder = database.collection('settings').doc('placeHolderImage');
            placeholder.get().then(async function(snapshotsimage) {
                if (snapshotsimage.exists) {
                    var placeholderImageData = snapshotsimage.data();
                    placeholderImage = placeholderImageData.image;
                }
            });

            getVendorId(vendorUserId).then(data => {
                vendorId = data;
                // If the table was already initialized and needs a refresh with new vendorId, 
                // but since the API uses vendorUserId (UUID), it might work without this.
                if (table) {
                    table.ajax.reload();
                }
            }).catch(err => {
                console.warn("Could not load vendorId from Firestore, using PHP ID only.", err);
            });

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
                            pageLength: 10,
                            processing: false,
                            serverSide: true,
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

                                $.getJSON('{{ route('items.fetch') }}', async function(response) {
                                    console.log("API Response received:", response);
                                    try {
                                        let rawResults = (response.data && response.data.results) ? response.data.results : (response.results || []);
                                        console.log("Raw items count:", rawResults.length);
                                        
                                        // Filter items for CURRENT vendor only
                                        let querySnapshot = rawResults.filter(item => {
                                            return (item.vendorID == vendorUserId || item.vendor_id == vendorUserId || item.vendorID == vendorId || item.vendor_id == vendorId);
                                        });
                                        console.log("Filtered items for vendor " + vendorUserId + ":", querySnapshot.length);

                                        if (!querySnapshot || querySnapshot.length === 0) {
                                            $('.total_count').text(0);
                                            $('#data-table_processing').hide();
                                            callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                                            return;
                                        }

                                        // Try to fetch categories once, but don't hang for too long
                                        let categoriesMap = {};
                                        try {
                                            console.log("Fetching categories from Firestore...");
                                            const catSnapshot = await Promise.race([
                                                database.collection('vendor_categories').get(),
                                                new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 5000))
                                            ]);
                                            catSnapshot.forEach(doc => {
                                                const catData = doc.data();
                                                categoriesMap[catData.id] = catData.title;
                                            });
                                            console.log("Categories loaded:", Object.keys(categoriesMap).length);
                                        } catch (catErr) {
                                            console.warn("Failed to load categories from Firestore, proceeding without them:", catErr);
                                        }

                                        let filteredRecords = [];
                                        
                                        for (const childData of querySnapshot) {
                                            var finalPrice = childData.disPrice && childData.disPrice != '0' ? childData.disPrice : childData.price;
                                            
                                            var date = '';
                                            var time = '';
                                            if (childData.createdAt) {
                                                try {
                                                    let dateObj = (childData.createdAt && childData.createdAt.toDate) ? childData.createdAt.toDate() : new Date(childData.createdAt);
                                                    if (!isNaN(dateObj.getTime())) {
                                                        date = dateObj.toDateString();
                                                        time = dateObj.toLocaleTimeString('en-US');
                                                    }
                                                } catch (err) { }
                                            }
                                            
                                            var createdAt = date + ' ' + time;
                                            childData.createdDate = createdAt;
                                            childData.foodName = childData.name;
                                            childData.finalPrice = parseInt(finalPrice || 0);

                                            childData.category = categoriesMap[childData.categoryID] || (childData.category || '{{ trans('lang.unknown') }}');
                                            childData.id = childData.id || childData._id; // Ensure ID exists
                                            childData.publish = (childData.publish === true || childData.publish === 'Yes' || childData.publish === 'yes' || childData.publish == 1) ? 'Yes' : 'No';

                                            if (!searchValue || 
                                                (childData.name && childData.name.toString().toLowerCase().includes(searchValue)) ||
                                                (childData.finalPrice && childData.finalPrice.toString().includes(searchValue)) ||
                                                (childData.category && childData.category.toString().toLowerCase().includes(searchValue)) ||
                                                (childData.publish && childData.publish.toString().toLowerCase().includes(searchValue)) ||
                                                (createdAt && createdAt.toString().toLowerCase().includes(searchValue))
                                            ) {
                                                filteredRecords.push(childData);
                                            }
                                        }

                                        filteredRecords.sort((a, b) => {
                                            let aValue = a[orderByField];
                                            let bValue = b[orderByField];

                                            if (orderByField === 'finalPrice') {
                                                aValue = parseFloat(a[orderByField] || 0);
                                                bValue = parseFloat(b[orderByField] || 0);
                                            } else if (orderByField === 'createdDate') {
                                                aValue = a[orderByField] ? new Date(a[orderByField]).getTime() : 0;
                                                bValue = b[orderByField] ? new Date(b[orderByField]).getTime() : 0;
                                            } else {
                                                aValue = (a[orderByField] || '').toString().toLowerCase();
                                                bValue = (b[orderByField] || '').toString().toLowerCase();
                                            }

                                            return orderDirection === 'asc' ? (aValue > bValue ? 1 : -1) : (aValue < bValue ? 1 : -1);
                                        });

                                        const totalRecords = filteredRecords.length;
                                        $('.total_count').text(totalRecords);
                                        const paginatedRecords = filteredRecords.slice(start, start + length);

                                        let records = [];
                                        await Promise.all(paginatedRecords.map(async (childData) => {
                                            records.push(await buildHTML(childData));
                                        }));

                                        $('#data-table_processing').hide();
                                        callback({
                                            draw: data.draw,
                                            recordsTotal: totalRecords,
                                            recordsFiltered: totalRecords,
                                            data: records
                                        });

                                    } catch (error) {
                                        console.error("Error processing items:", error);
                                        $('#data-table_processing').hide();
                                        callback({
                                            draw: data.draw,
                                            recordsTotal: 0,
                                            recordsFiltered: 0,
                                            data: []
                                        });
                                    }
                                }).fail(function(error) {
                                    console.error("Error fetching items:", error);
                                    $('#data-table_processing').hide();
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: 0,
                                        recordsFiltered: 0,
                                        data: []
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
            var vId = '';
            try {
                const vendorSnapshots = await database.collection('vendors').where('author', "==", vendorUser).get();
                if (!vendorSnapshots.empty) {
                    var vendorData = vendorSnapshots.docs[0].data();
                    vId = vendorData.id;
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
                }
            } catch (error) {
                console.error("Error in getVendorId:", error);
            }
            return vId;
        }
    </script>
@endsection
