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

        $(document).ready(function() {
            jQuery("#data-table_processing").show();

            database.collection('settings').doc('placeHolderImage').get().then(function(snap) {
                if (snap.exists) placeholderImage = snap.data().image;
            });

            var fieldConfig = {
                columns: [
                    { key: 'name',       header: "{{ trans('lang.item_info') }}" },
                    { key: 'finalPrice', header: "{{ trans('lang.item_price') }}" },
                    { key: 'category',   header: "{{ trans('lang.item_category_id') }}" },
                    { key: 'publish',    header: "{{ trans('lang.item_publish') }}" },
                ],
                fileName: "{{ trans('lang.item_table') }}",
            };

            // Fetch all data first, then build DataTable
            $.ajax({
                url: '{{ route('items.fetch') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('items.fetch OK:', JSON.stringify(response).substring(0, 300));

                    if (response.error === 'vendor_not_synced') {
                        console.warn('vendor_not_synced, checking again in 5s...');
                        setTimeout(function() { window.location.reload(); }, 5000);
                        return;
                    }

                    var items = (response.data && response.data.results) ? response.data.results : (response.results || []);
                    console.log('Items count:', items.length);

                    if (items.length === 0) {
                        jQuery("#data-table_processing").hide();
                        $('#itemTable').DataTable({
                            pageLength: 10,
                            "language": {
                                "zeroRecords": "{{ trans('lang.no_record_found') }}",
                                "emptyTable": "{{ trans('lang.no_record_found') }}"
                            }
                        });
                        return;
                    }

                    // Collect unique categoryIDs and fetch names from Firestore
                    var categoryIds = [...new Set(items.map(function(i){ return i.categoryID; }).filter(Boolean))];
                    var categoryMap = {};

                    var fetchCats = categoryIds.length > 0
                        ? database.collection('vendor_categories').where('id', 'in', categoryIds.slice(0,10)).get()
                              .then(function(snap){ snap.forEach(function(d){ categoryMap[d.data().id] = d.data().title || d.data().name || d.data().id; }); })
                              .catch(function(){ })
                        : Promise.resolve();

                    fetchCats.then(function() {

                    // Build table rows
                    var rows = [];
                    items.forEach(function(item) {
                        var finalPrice = (item.disPrice && item.disPrice != '0') ? item.disPrice : item.price;
                        var createdAt = '';
                        if (item.createdAt) {
                            try {
                                var d = new Date(item.createdAt);
                                if (!isNaN(d.getTime())) createdAt = d.toDateString() + ' ' + d.toLocaleTimeString('en-US');
                            } catch(e) {}
                        }
                        item.finalPrice  = parseInt(finalPrice || 0);
                        item.createdDate = createdAt;
                        item.publish     = (item.publish === true || item.publish === 'Yes' || item.publish == 1) ? 'Yes' : 'No';

                        var route1 = '{{ route('items.edit', ':id') }}'.replace(':id', item.id);
                        var img = item.photo
                            ? '<img class="rounded" style="width:50px" src="' + item.photo + '" onerror="this.src=\'' + placeholderImage + '\'">'
                            : '<img class="rounded" style="width:50px" src="' + placeholderImage + '">';
                        var nameCol   = img + '<a href="' + route1 + '" class="left_space redirecttopage" data-url="' + route1 + '">' + (item.name || '') + '</a>';
                        var priceCol  = finalPrice || 0;
                        var catName   = (item.categoryID && categoryMap[item.categoryID]) ? categoryMap[item.categoryID] : (item.category || item.categoryID || '');
                        var catCol    = '<span class="category_' + (item.categoryID||'') + '">' + catName + '</span>';
                        var pubCol    = item.publish === 'Yes' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                        var actCol    = '<span class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + item.id + '" name="item-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>';

                        rows.push([nameCol, priceCol, catCol, pubCol, createdAt, actCol]);
                    });

                    $('.total_count').text(rows.length);
                    jQuery("#data-table_processing").hide();

                    $('#itemTable').DataTable({
                        pageLength: 10,
                        processing: false,
                        serverSide: false,
                        responsive: true,
                        data: rows,
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
                    }); // end fetchCats.then
                },
                error: function(xhr, status, err) {
                    console.error('items.fetch FAILED:', status, err, xhr.responseText);
                    jQuery("#data-table_processing").hide();
                    if (xhr.status == 504) {
                        alert("The server is taking too long to respond (504 Gateway Time-out). Please refresh the page in a few moments.");
                    } else {
                        alert("Failed to fetch products. Please check the console for details.");
                    }
                }
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
            window.location = "{{ url()->current() }}";
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
