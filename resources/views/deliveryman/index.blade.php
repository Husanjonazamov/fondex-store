@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.deliveryman') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.advertisement_plural') }}</li>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item active">
                                    <a class="nav-link" href="{!! route('deliveryman') !!}"><i class="fa fa-list mr-2"></i>{{ trans('lang.deliveryman') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('deliveryman.create') !!}"><i class="fa fa-plus mr-2"></i>{{ trans('lang.create_deliveryman') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="deliverymanTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>
                                        <th>{{ trans('lang.image') }}</th>
                                        <th>{{ trans('lang.user_name') }}</th>
                                        <th>{{ trans('lang.email') }}</th>
                                        <th>{{ trans('lang.active') }}</th>
                                        <th>{{ trans('lang.date') }}</th>
                                        <th>{{ trans('lang.dashboard_total_orders') }}</th>
                                        <th>{{ trans('lang.actions') }}</th>
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
@endsection
@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var offest = 1;
        var pagesize = 10;
        var end = null;
        var endarray = [];
        var start = null;
        var vendorUserId = "<?php echo $id; ?>";

        var vendorId;
        var ref;
        var append_list = '';
        var placeholderImage = '';
        ref = database.collection('users').where("role", "==", "driver");

        $(document).ready(function() {

            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });
            var placeholder = database.collection('settings').doc('placeHolderImage');
            placeholder.get().then(async function(snapshotsimage) {
                var placeholderImageData = snapshotsimage.data();
                placeholderImage = placeholderImageData.image;
            })
            const table = $('#deliverymanTable').DataTable({
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
                    const orderableColumns = ['','', 'driverName', 'email', '', 'createdAt', '', ''];
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {

                        const Vendor = await getVendorId(vendorUserId);
                        const querySnapshot = await ref.where('vendorID', "==", Vendor).get();
                        if (!querySnapshot || querySnapshot.empty) {
                            console.error("No data found in Firestore.");
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
                            childData.id = doc.id;
                            childData.driverName = childData.firstName + ' ' + childData.lastName || " "

                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            };
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdAt")) {
                                try {
                                    date = childData.createdAt.toDate().toDateString();
                                    time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch (err) {}
                            }
                            childData.createdDate = date + ' ' + time;


                            if (searchValue) {
                                if (
                                    (childData.driverName && childData.driverName.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.createdDate && childData.createdDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.email && childData.email.toString().includes(searchValue))
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                            if (orderByField === 'createdAt') {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            }
                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
                        const paginatedRecords = filteredRecords.slice(start, start + length);
                        await Promise.all(paginatedRecords.map(async (childData) => {
                            if (childData.id) {
                                const totalOrderSnapShot = await database.collection('vendor_orders').where('driverID', '==', childData.id).get();
                                const orders = totalOrderSnapShot.size;
                               
                                childData.orders = orders;
                            } else {
                                childData.orders = 0;
                            }
                        }));
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
                order: [5, 'desc'],
                columnDefs: [{
                        orderable: false,
                        targets: [0,1, 7]
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
            var route1 = '{{ route('deliveryman.edit', ':id') }}';
            route1 = route1.replace(':id', id);

            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>');
            
            var driverImage = val.profilePictureURL == '' || val.profilePictureURL == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + val.profilePictureURL + '" alt="image">'
            html.push('<td>'+driverImage+'</td>')
            html.push('<td><a href="' + route1 + '">' + val.driverName + '</a></td>');
            html.push('<td>' + val.email + '</td>');
            if (val.active) {
                html.push('<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>')
            } else {
                html.push('<td><label class="switch"><input type="checkbox"  id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>')
            }
            html.push('<td>' + val.createdDate + '</td>');
            html.push('<td>' + val.orders + '</td>');

            var action = '';
            action = action + '<span class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
            action = action + '<a id="' + val.id + '" class="do_not_delete" name="deliveryman-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
            action = action + '</span>';
            html.push(action);
            return html;
        }

        $("#is_active").click(function() {
            $("#deliverymanTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#deliverymanTable .is_open:checked').length) {
                if (confirm('Are You Sure want to Delete Selected Data ?')) {
                    jQuery("#data-table_processing").show();
                    $('#deliverymanTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('users', dataId, ['profilePictureURL']);
                        await deleteDriverData(dataId).then(function() {
                            window.location.reload();
                        });
                    });
                }
            } else {
                alert('Please Select Any One Record .');
            }
        });

        $(document).on("click", "a[name='deliveryman-delete']", async function(e) {
            var id = this.id;
            await deleteDocumentWithImage('users', id, ['profilePictureURL']);
            await deleteDriverData(id).then(function() {
                window.location.reload();
            });
        });
        async function deleteDriverData(driverId) {
            //delete user from authentication  
            var dataObject = {
                "data": {
                    "uid": driverId
                }
            };
            var projectId = '<?php echo env('FIREBASE_PROJECT_ID'); ?>';
            jQuery.ajax({
                url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                method: 'POST',
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(dataObject),
                success: function(data) {
                    console.log('Delete user success:', data.result);
                },
                error: function(xhr, status, error) {
                    var responseText = JSON.parse(xhr.responseText);
                    console.log('Delete user error:', responseText.error);
                }
            });
        }

        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            await database.collection('vendors').where('author', "==", vendorUser).get().then(async function(vendorSnapshots) {
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
            });
            return vendorId;
        }
        $(document).on("click", "input[name='isActive']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            if (ischeck) {
                database.collection('users').doc(id).update({
                    'active': true
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            } else {
                database.collection('users').doc(id).update({
                    'active': false
                }).then(function(result) {
                    jQuery("#data-table_processing").hide();
                });
            }
        });
    </script>
@endsection
