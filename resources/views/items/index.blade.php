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

            var itemTable = $('#itemTable').DataTable({
                pageLength: 10,
                processing: false,
                serverSide: false,
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3, 5]
                }],
                order: [4, 'asc'],
                "language": {
                    "zeroRecords": "{{ trans('lang.no_record_found') }}",
                    "emptyTable": "{{ trans('lang.no_record_found') }}",
                    "processing": "" 
                },
                dom: 'lfrtipB',
                buttons: [{
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: ['excel', 'pdf', 'csv']
                }],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                }
            });

            var totalFetched = 0;
            var categoryMap = {};
            var searchTimeout = null;
            var currentSearch = '';

            function fetchItems(nextUrl = null, searchQuery = '') {
                jQuery("#data-table_processing").show();
                
                $.ajax({
                    url: '{{ route('items.fetch') }}',
                    type: 'GET',
                    data: { 
                        next_url: nextUrl,
                        search: searchQuery
                    },
                    dataType: 'json',
                    success: async function(response) {
                        try {
                            if (response.error === 'vendor_not_synced') {
                                setTimeout(function() { window.location.reload(); }, 5000);
                                return;
                            }

                            var items = (response.data && response.data.results) ? response.data.results : (response.results || []);
                            
                            // Fetch missing categories from Firestore
                            var categoryIds = [...new Set(items.map(i => i.categoryID).filter(id => id && !categoryMap[id]))];
                            if (categoryIds.length > 0) {
                                try {
                                    const catSnap = await database.collection('vendor_categories').where('id', 'in', categoryIds.slice(0, 10)).get();
                                    catSnap.forEach(d => { categoryMap[d.data().id] = d.data().title || d.data().name; });
                                } catch (e) { console.error('Firestore cat fetch error:', e); }
                            }

                            var newRows = [];
                            items.forEach(function(item) {
                                var finalPrice = (item.disPrice && item.disPrice != '0') ? item.disPrice : item.price;
                                var createdAt = item.createdAt ? new Date(item.createdAt).toLocaleString() : '';
                                var routeEdit = '{{ route('items.edit', ':id') }}'.replace(':id', item.id);
                                
                                var imgUrl = item.photo || placeholderImage;
                                var img = '<img class="rounded" style="width:50px" src="' + imgUrl + '" onerror="this.src=\'' + placeholderImage + '\'">';
                                var nameCol = img + '<a href="' + routeEdit + '" class="left_space">' + (item.name || '') + '</a>';
                                var catName = categoryMap[item.categoryID] || item.category || 'N/A';
                                var pubCol = (item.publish === 'Yes' || item.publish === true) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                                var actCol = '<span class="action-btn"><a href="' + routeEdit + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + item.id + '" name="item-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>';

                                newRows.push([nameCol, finalPrice, catName, pubCol, createdAt, actCol]);
                            });

                            itemTable.rows.add(newRows).draw(false);
                            totalFetched += items.length;
                            $('.total_count').text(totalFetched);

                            if (response.next_url) {
                                $('.food-limit-note').text('Loading more products... (' + totalFetched + ' items fetched)');
                                fetchItems(response.next_url, searchQuery); 
                            } else {
                                $('.food-limit-note').text('Total ' + totalFetched + ' products loaded.');
                                jQuery("#data-table_processing").hide();
                            }
                        } catch (err) {
                            console.error('Success handler Error:', err);
                            jQuery("#data-table_processing").hide();
                        }
                    },
                    error: function(xhr) {
                        jQuery("#data-table_processing").hide();
                        console.error('Fetch failed', xhr);
                    }
                });
            }

            // Hook into DataTable search box to trigger API-side search
            $('.dataTables_filter input').unbind().bind('keyup', function(e) {
                var searchTerm = $(this).val();
                if (searchTerm === currentSearch) return;
                
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentSearch = searchTerm;
                    totalFetched = 0;
                    itemTable.clear().draw();
                    fetchItems(null, currentSearch);
                }, 700); // 700ms debounce
            });

            // Start the initial fetching process
            fetchItems();
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
