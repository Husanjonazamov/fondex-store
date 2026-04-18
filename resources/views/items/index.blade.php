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
                                <div class="mb-3 d-flex">
                                    <input type="text" id="product-search-input" class="form-control" placeholder="Mahsulot nomini qidirish..." style="max-width:350px;">
                                    <button class="btn btn-primary ml-2" id="product-search-btn"><i class="mdi mdi-magnify"></i> Qidirish</button>
                                    <button class="btn btn-secondary ml-2" id="product-search-clear" style="display:none;">Tozalash</button>
                                </div>
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
                                <div class="d-flex justify-content-between align-items-center mt-3" id="pagination-wrapper" style="display:none">
                                    <div></div>
                                    <nav><ul class="pagination mb-0" id="pagination-controls"></ul></nav>
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
        var vendorId = "<?php echo $vendorId; ?>";
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
                vendorId = vendorUserId;
                return;
            }

            var data = snapshots.docs[0].data();
            section_id = data.sectionId;

            // Try to get vendor ID from vendors collection, fallback to vendorUserId
            try {
                const vendorSnap = await database.collection('vendors').where('author', '==', vendorUserId).get();
                vendorId = (!vendorSnap.empty && vendorSnap.docs[0].data().id) ? vendorSnap.docs[0].data().id : vendorUserId;
            } catch(e) {
                vendorId = vendorUserId;
            }
            console.log('vendorId:', vendorId);

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

        var vendorIdReady = fetchSectionId();

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

            var itemTable;
            try {
                itemTable = $('#itemTable').DataTable({
                    paging:     false,
                    info:       false,
                    searching:  false,
                    processing: false,
                    serverSide: false,
                    columnDefs: [{ orderable: false, targets: [0, 3, 5] }],
                    order: [[4, 'asc']],
                    language: {
                        zeroRecords: "{{ trans('lang.no_record_found') }}",
                        emptyTable:  "{{ trans('lang.no_record_found') }}",
                    }
                });
            } catch(e) {
                console.error('DataTable init FAILED:', e);
            }

            var categoryMap  = {};
            var cursorStack  = [];   // history of cursors for "prev"
            var nextCursor   = null;
            var currentPage  = 1;
            var totalLoaded  = 0;
            var currentSearch = '';

            function buildRow(item) {
                var finalPrice = (item.disPrice && parseFloat(item.disPrice) > 0) ? item.disPrice : item.price;
                var createdAt  = item.createdAt ? new Date(item.createdAt).toLocaleString() : '';
                var routeEdit  = '{{ route('items.edit', ':id') }}'.replace(':id', item.id);
                var img        = '<img class="rounded" style="width:50px" src="' + (item.photo || placeholderImage) + '" onerror="this.src=\'' + placeholderImage + '\'">';
                var nameCol    = img + '<a href="' + routeEdit + '" class="left_space">' + (item.name || '') + '</a>';
                var catName    = categoryMap[item.categoryID] || item.category || '';
                var pubCol     = item.publish === 'Yes' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                var actCol     = '<span class="action-btn"><a href="' + routeEdit + '"><i class="mdi mdi-lead-pencil"></i></a><a id="' + item.id + '" name="item-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></span>';
                return [nameCol, parseFloat(finalPrice) || 0, catName, pubCol, createdAt, actCol];
            }

            function renderPagination(hasNext) {
                var hasPrev = cursorStack.length > 0;
                var html = '';
                html += '<li class="page-item' + (!hasPrev ? ' disabled' : '') + '"><a class="page-link" href="#" id="btn-prev">&laquo; Oldingi</a></li>';
                html += '<li class="page-item active"><span class="page-link">' + currentPage + '</span></li>';
                html += '<li class="page-item' + (!hasNext ? ' disabled' : '') + '"><a class="page-link" href="#" id="btn-next">Keyingi &raquo;</a></li>';
                $('#pagination-controls').html(html);
                $('#pagination-wrapper').toggle(hasPrev || hasNext);
                $('.total_count').text('Sahifa ' + currentPage);
            }

            function fetchPage(cursor) {
                if (!itemTable) return;
                jQuery("#data-table_processing").show();
                itemTable.clear().draw();

                vendorIdReady.then(function() {
                    var data = { vendor_id: vendorId };
                    if (cursor) data.cursor = cursor;
                    if (currentSearch) data.search = currentSearch;

                    $.ajax({
                        url: '{{ route('items.fetch') }}',
                        type: 'GET',
                        timeout: 30000,
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            var items = response.results || [];
                            nextCursor = response.next_cursor || null;

                            itemTable.rows.add(items.map(buildRow)).draw();
                            jQuery("#data-table_processing").hide();
                            renderPagination(response.has_next);

                            var catIds = [...new Set(items.map(function(i){ return i.categoryID; }).filter(Boolean))];
                            if (catIds.length > 0) {
                                database.collection('vendor_categories').where('id', 'in', catIds.slice(0, 10)).get()
                                    .then(function(snap) {
                                        snap.forEach(function(d){ categoryMap[d.data().id] = d.data().title || d.data().name || ''; });
                                    }).catch(function(){});
                            }
                        },
                        error: function(xhr) {
                            jQuery("#data-table_processing").hide();
                            console.error('fetch error:', xhr.status, xhr.responseText);
                        }
                    });
                });
            }

            $(document).on('click', '#btn-next', function(e) {
                e.preventDefault();
                if (!nextCursor) return;
                cursorStack.push(nextCursor);
                currentPage++;
                fetchPage(nextCursor);
                $('html,body').animate({ scrollTop: 0 }, 200);
            });

            $(document).on('click', '#btn-prev', function(e) {
                e.preventDefault();
                if (cursorStack.length === 0) return;
                cursorStack.pop();
                currentPage--;
                var prevCursor = cursorStack.length > 0 ? cursorStack[cursorStack.length - 1] : null;
                fetchPage(prevCursor);
                $('html,body').animate({ scrollTop: 0 }, 200);
            });

            $('#product-search-btn').on('click', function() {
                var q = $('#product-search-input').val().trim();
                currentSearch = q;
                cursorStack = [];
                nextCursor = null;
                currentPage = 1;
                $('#product-search-clear').toggle(q.length > 0);
                fetchPage(null);
            });

            $('#product-search-input').on('keypress', function(e) {
                if (e.which === 13) $('#product-search-btn').click();
            });

            $('#product-search-clear').on('click', function() {
                $('#product-search-input').val('');
                currentSearch = '';
                cursorStack = [];
                nextCursor = null;
                currentPage = 1;
                $(this).hide();
                fetchPage(null);
            });

            fetchPage(null);
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
