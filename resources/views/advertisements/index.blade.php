@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.advertisement_plural') }}</h3>
                @if (request()->is('advertisements/pending'))
                    @php $type = 'pending'; @endphp
                @else
                    @php $type = 'all'; @endphp
                @endif
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
                                    <a class="nav-link" href="{!! route('advertisements') !!}"><i class="fa fa-list mr-2"></i>{{ trans('lang.advertisement_table') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('advertisements.create') !!}" onclick="localStorage.removeItem('copiedAdvertisement')"><i class="fa fa-plus mr-2"></i>{{ trans('lang.advertisement_create') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="advertisementTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label></th>
                                        <th>{{ trans('lang.ads_title') }}</th>
                                        <th> {{ trans('lang.ads_type') }}</th>
                                        <th> {{ trans('lang.duration') }}</th>
                                        <th> {{ trans('lang.status') }}</th>
                                        <th> {{ trans('lang.priority') }}</th>
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
    <div class="modal fade" id="advPauseModal" aria-hidden="true">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-status-image" alt="" class="mb-20" src="{{ asset('images/ad-pause.png') }}">
                                <h5 class="p-2" id="toggle-status-title">{{ trans('lang.are_you_sure_you_want_to_pause_the_request') }}</h5>
                            </div>
                            <div class="text-center mt-3" id="toggle-status-message">
                                <p class="toggal-status-msg">{{ trans('lang.this_add_will_paused_not_show_in_app_and_web') }}</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">

                                    <div class="col-12">
                                        <input type="text" placeholder="{{ trans('lang.your_note_here') }}" name="pause_reason" class="form-control" id="pause_reason">
                                        <div id="add_pause_error" class="font-weight-bold text-danger"></div>
                                    </div>
                                    <input type="hidden" id="pauseAdvId">
                                </div>
                            </div>
                        </div>
                        <div class="btn-container justify-content-center text-center">
                            <button type="button" id="toggle-status-ok-button" class="btn btn-primary min-w-120 confirm-pause-btn" toggle-ok-button="is_paid">{{ trans('lang.ok') }}</button>
                            <button id="reset_btn" type="reset" class="btn btn-cancel min-w-120" data-dismiss="modal">
                                {{ trans('lang.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="advResumeModal" aria-hidden="true">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-status-image" alt="" class="mb-20" src="{{ asset('images/ad-resume.png') }}">
                                <h5 class="p-2" id="toggle-status-title">{{ trans('lang.are_you_sure_you_want_to_resume_the_request') }}</h5>
                            </div>
                            <div class="text-center mt-3" id="toggle-status-message">
                                <p class="toggal-status-msg">{{ trans('lang.this_app_will_resume_and_show_in_app_and_web') }}</p>
                            </div>
                            <input type="hidden" id="resumeAdvId">
                        </div>

                        <div class="btn-container justify-content-center text-center">
                            <button type="button" id="toggle-status-ok-button" class="btn btn-primary min-w-120 confirm-resume-btn" toggle-ok-button="is_paid">{{ trans('lang.ok') }}</button>
                            <button id="reset_btn" type="reset" class="btn btn-cancel min-w-120" data-dismiss="modal">
                                {{ trans('lang.cancel') }}
                            </button>
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

        var type = "{{ $type }}";


        var vendorId;
        var ref;
        var append_list = '';
        var placeholderImage = '';
        ref = database.collection('advertisements').where("status", "in", ["approved", "canceled"]);
        if (type == 'pending') {
            ref = database.collection('advertisements').where("status", "in", ["pending", "updated"]);
        }

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
            const table = $('#advertisementTable').DataTable({
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
                    const orderableColumns = ['', 'title', 'type', 'duration', 'status', 'priority', ''];
                    const orderByField = orderableColumns[orderColumnIndex];
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#data-table_processing').show();
                    }
                    try {

                        const Vendor = await getVendorId(vendorUserId);
                        const querySnapshot = await ref.where('vendorId', "==", Vendor).get();
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
                            childData.id = doc
                                .id; // Ensure the document ID is included in the data
                            childData.vendorId = Vendor;

                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            };
                            const startDate = childData.startDate.toDate().toLocaleDateString('en-US', options);
                            const endDate = childData.endDate.toDate().toLocaleDateString('en-US', options);
                            const ExpiryDate = childData.endDate;
                            if (childData.status == 'approved') {
                                if (ExpiryDate && new Date(ExpiryDate.seconds * 1000) < new Date()) {
                                    childData.status = 'Expired';
                                } else {
                                    const startDate = childData.startDate;
                                    if (startDate && new Date(startDate.seconds * 1000) < new Date() && childData.paymentStatus) {
                                        childData.status = 'Running';
                                    } else {
                                        childData.status = 'Approved';

                                    }
                                }
                            } else if (childData.status == 'canceled') {
                                childData.status = 'Canceled';
                            } else {
                                childData.status = 'Pending';
                            }

                            if (searchValue) {
                                if (
                                    (childData.title && childData.title.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.type && childData.type.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.status && childData.status.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.priority && childData.priority.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.duration && childData.duration.toString().toLowerCase().includes(searchValue))


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

                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
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
                order: [1, 'asc'],
                columnDefs: [{
                        orderable: false,
                        targets: [0, 6]
                    },
                    {
                        type: "num",
                        targets: 5
                    }
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
            var route1 = '{{ route('advertisements.edit', ':id') }}';
            route1 = route1.replace(':id', id);

            var advertisementsView = '{{ route('advertisements.view', ':id') }}';
            advertisementsView = advertisementsView.replace(':id', id);

            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const startDate = val.startDate.toDate().toLocaleDateString('en-US', options);
            const endDate = val.endDate.toDate().toLocaleDateString('en-US', options);

            html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' +
                id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>');

            html.push('<td><a href="' + advertisementsView + '">' + val.title + '</a></td>');

            if (val.type === 'restaurant_promotion') {
                html.push('<td>Restaurant Promotion</td>');
            } else {
                html.push('<td>Video Promotion</td>');
            }

            html.push('<td>' + startDate + '-' + endDate + '</td>');
            if (val.hasOwnProperty('isPaused') && val.isPaused) {
                html.push('<td><span class="badge badge-info py-2 px-3" >{{ trans('lang.paused') }}</span></td>');
            } else {
                if (val.status == "Running") {
                    html.push('<td><span class="badge badge-info py-2 px-3" >{{ trans('lang.running') }}</span></td>');
                } else if (val.status == "Expired") {
                    html.push('<td><span class="badge badge-danger py-2 px-3" >{{ trans('lang.expired') }}</span></td>')
                } else if (val.status == "Pending") {
                    html.push('<td><span class="badge badge-primary py-2 px-3" >{{ trans('lang.pending') }}</span></td>')
                } else if (val.status == "Canceled") {
                    html.push('<td><span class="badge badge-danger py-2 px-3" >{{ trans('lang.canceled') }}</span></td>')
                } else {
                    html.push('<td><span class="badge badge-success py-2 px-3" >{{ trans('lang.approved') }}</span></td>')
                }
            }

            html.push('<td>' + val.priority + '</td>');

            var advertisementsChat = '{{ route('advertisement.chat', ':id') }}';
            advertisementsChat = advertisementsChat.replace(':id', id);

            var action = '';
            action = action + '<span class="action-btn"><a href="' + advertisementsView + '"><i class="fa fa-eye"></i></a><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
            action = action + '<a id="' + val.id +
                '" class="do_not_delete" name="advertisements-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
            action = action + '<a id="' + val.id +
                '"  name="advertisements-copy" href="javascript:void(0)"><i class="fa fa-copy"></i></a><a href="'+ advertisementsChat +'"><i class="fa fa-commenting"></i></a>';

            if (type != 'pending') {
                if (val.status == "Running") {
                    if (val.hasOwnProperty('isPaused') && val.isPaused) {
                        var actionClass = "fa fa-play-circle";
                        tooltipTxt = 'Play';
                    } else {
                        var actionClass = "fa fa-pause-circle";
                        tooltipTxt = 'Pause';
                    }

                    action = action + '<a href="javascript:void(0)" name="pause-btn" id="' + val.id + '" data-status="' + val.isPaused + '"  data-bs-toggle="tooltip" data-bs-placement="top" title="' + tooltipTxt + '"><i class="' + actionClass + '"></i></a>'
                }
            }
            action = action + '</span>';
            html.push(action);
            return html;
        }

        $("#is_active").click(function() {
            $("#advertisementTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#advertisementTable .is_open:checked').length) {
                if (confirm('Are You Sure want to Delete Selected Data ?')) {
                    jQuery("#data-table_processing").show();
                    $('#advertisementTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        await database.collection('advertisements').doc(dataId).get().then(async function(snapshots) {
                            var data = snapshots.data();
                            if (data.type == 'video_promotion') {
                                var checkVideoSize = await database.collection('advertisements').where('video', '==', data.video).get();
                                var videoSize = checkVideoSize.size;
                                if (videoSize > 1) {
                                    await deleteDocumentWithImage('advertisements', dataId);
                                } else {
                                    await deleteDocumentWithImage('advertisements', dataId, ['video']);
                                }

                            } else {
                                var checkprofileSize = await database.collection('advertisements').where('profileImage', '==', data.profileImage).get();
                                var profileSize = checkprofileSize.size;
                                var checkCoverSize = await database.collection('advertisements').where('coverImage', '==', data.coverImage).get();
                                var coverSize = checkCoverSize.size;
                                let fieldsToDelete = [];

                                if (profileSize === 1) {
                                    fieldsToDelete.push('profileImage');
                                }

                                if (coverSize === 1) {
                                    fieldsToDelete.push('coverImage');
                                }

                                if (fieldsToDelete.length > 0) {

                                    await deleteDocumentWithImage('advertisements', dataId, fieldsToDelete);
                                } else {
                                    await deleteDocumentWithImage('advertisements', dataId);
                                }
                            }
                        })

                        window.location.reload();
                    });
                }
            } else {
                alert('Please Select Any One Record .');
            }
        });

        $(document).on("click", "a[name='advertisements-delete']", async function(e) {
            var id = this.id;
            jQuery("#data-table_processing").show();
            await database.collection('advertisements').doc(id).get().then(async function(snapshots) {
                var data = snapshots.data();
                if (data.type == 'video_promotion') {
                    var checkVideoSize = await database.collection('advertisements').where('video', '==', data.video).get();
                    var videoSize = checkVideoSize.size;
                    if (videoSize > 1) {
                        await deleteDocumentWithImage('advertisements', id);
                    } else {
                        await deleteDocumentWithImage('advertisements', id, ['video']);
                    }

                } else {
                    var checkprofileSize = await database.collection('advertisements').where('profileImage', '==', data.profileImage).get();
                    var profileSize = checkprofileSize.size;
                    var checkCoverSize = await database.collection('advertisements').where('coverImage', '==', data.coverImage).get();
                    var coverSize = checkCoverSize.size;
                    let fieldsToDelete = [];

                    if (profileSize === 1) {
                        fieldsToDelete.push('profileImage');
                    }

                    if (coverSize === 1) {
                        fieldsToDelete.push('coverImage');
                    }

                    if (fieldsToDelete.length > 0) {

                        await deleteDocumentWithImage('advertisements', id, fieldsToDelete);
                    } else {
                       
                        await deleteDocumentWithImage('advertisements', id);
                    }
                }
            })

            window.location.reload();
        });

        async function getVendorId(vendorUser) {
            var vendorId = '';
            var ref;
            await database.collection('vendors').where('author', "==", vendorUser).get().then(async function(vendorSnapshots) {
                var vendorData = vendorSnapshots.docs[0].data();
                vendorId = vendorData.id;
            });
            return vendorId;
        }
        $(document).on("click", "a[name='pause-btn']", async function(e) {
            var id = this.id;
            var status = $(this).attr('data-status');
            if (status == null || status == "null") {
                $('#pauseAdvId').val(id);
                $('#advPauseModal').modal('show');
            } else if (status.toString() == "false") {
                $('#pauseAdvId').val(id);
                $('#advPauseModal').modal('show');
            } else {
                $('#resumeAdvId').val(id);
                $('#advResumeModal').modal('show');

            }

        });
        $('.confirm-resume-btn').click(async function() {
            var id = $('#resumeAdvId').val();
            localStorage.setItem('storeModifiedAd', id);
            await database.collection('advertisements').doc(id).update({
                'isPaused': false
            }).then(async function(snapshot) {

                window.location.href = '{{ route('advertisements') }}';
            })
        })
        $('.confirm-pause-btn').click(async function() {

            var id = $('#pauseAdvId').val();
            var reason = $('#pause_reason').val();
            if (reason == '') {
                $('#add_pause_error').html("{{ trans('lang.add_pause_note') }}").show();
                return false;
            }
            localStorage.setItem('storeModifiedAd', id);
            await database.collection('advertisements').doc(id).update({
                'isPaused': true,
                'pauseNote': reason
            }).then(async function(snapshot) {

                window.location.href = '{{ route('advertisements') }}';
            })

        })

        $(document).on('click', 'a[name="advertisements-copy"]', async function() {
            let id = this.id;
            await database.collection('advertisements').doc(id).get().then(async function(snapshot) {
                var advData = snapshot.data();
                localStorage.setItem('copiedAdvertisement', JSON.stringify(advData));
                window.location.href = "{{ route('advertisements.create') }}"
            })
        });
    </script>
@endsection
