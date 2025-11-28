@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.advertisement_details') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('advertisements') !!}">{{ trans('lang.advertisement_plural') }}</a></li>
                    <li class="breadcrumb-item">{{ trans('lang.advertisement_details') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card-body pb-5 p-0">
                <div class="text-right print-btn pb-3">
                    <a href="{{ route('advertisement.chat', $id) }}">
                        <button type="button" class="fa fa-commenting"></button>
                    </a>
                </div>
                <div class="order_detail" id="order_detail">
                    <div class="order_detail-top">
                        <div class="row">
                            <div class="order_edit-genrl col-lg-7 col-md-12">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.general_details') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="order_detail-top-box">
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.id') }}
                                                        : </strong><span id="advId"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.date_created') }}
                                                        : </strong><span id="createdAt"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col advType">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.advertisement_type') }}
                                                        : </strong><span id="advType"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.duration') }}:</strong>
                                                    <span id="duration"></span></label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-5">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.advertisement_details') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box">
                                            <p>
                                                <strong>{{ trans('lang.title') }}: </strong><span id="advTitle"></span>
                                            </p>
                                            <p>
                                                <strong>{{ trans('lang.description') }}: </strong>
                                                <span id="advDesc"></span>

                                            </p>
                                            <div class="row">
                                                <div class="preview-box col-md-3 image-div">
                                                    <h4>{{ trans('lang.profile_image') }}</h4>
                                                    <div id="profileImage"></div>
                                                </div>
                                                <div class="preview-box col-md-4 image-div">
                                                    <h4>{{ trans('lang.cover_image') }}</h4>
                                                    <div id="coverImage"></div>
                                                </div>
                                                <div class="preview-box col-md-12 video-div">
                                                    <h4>{{ trans('lang.video') }}</h4>
                                                    <div id="video"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order_edit-genrl col-lg-5 col-md-12">
                                <div class="card mt-4">
                                    <div class="card-header bg-white">
                                        <h3>{{ trans('lang.advertisement_status') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="order_detail-top-box">
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.payment_status') }}:</strong>
                                                    <span id="paymentStatus"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.status') }}:</strong>
                                                    <span id="status"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col d-none paused-note-div">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.paused_note') }}:</strong>
                                                    <span id="paused_note" class="text-danger"></span></label>
                                            </div>
                                            <div class="form-group row widt-100 gendetail-col d-none cancel-note-div">
                                                <label class="col-12 control-label"><strong>{{ trans('lang.cancel_note') }}:</strong>
                                                    <span id="cancel_note" class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <a href="{!! route('advertisements') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}
                </a>
            </div>
        </div>
    </div>

@endsection
@section('style')
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var id = "<?php echo $id; ?>";
        var database = firebase.firestore();
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        });
        var ref = database.collection('advertisements').where("id", "==", id);

        ref.get().then(async function(snapshots) {
            var data = snapshots.docs[0].data();
            $('#advId').html(data.id);

            var date1 = data.createdAt.toDate().toDateString();
            var date = new Date(date1);
            var dd = String(date.getDate()).padStart(2, '0');
            var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = date.getFullYear();
            var createdAt_val = yyyy + '-' + mm + '-' + dd;
            var time = data.createdAt.toDate().toLocaleTimeString('en-US');
            $('#createdAt').html(createdAt_val + ' ' + time);
            (data.type == 'restaurant_promotion') ? $('#advType').html('{{ trans('lang.restaurant_promotion') }}'): $('#advType').html('{{ trans('lang.video_promotion') }}');
            if (data.type == 'restaurant_promotion') {
                $('.video-div').addClass('d-none');
                $('#profileImage').append('<img src="' + data.profileImage + '" style="width: 100px; height: 100px;" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"> ');
                $('#coverImage').append('<img src="' + data.coverImage + '" style="width: 100px; height: 100px;" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"> ')
            } else {
                $('.image-div').addClass('d-none');
                $('#video').append('<video width="400px" height="250px" controls="controls" src="' + data.video + '" type="video/mp4"></video>')
            }
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var startDate = data.startDate.toDate().toLocaleDateString('en-US', options);
            var endDate = data.endDate.toDate().toLocaleDateString('en-US', options);
            $('#duration').html(startDate + '-' + endDate);
            startDate = new Date(data.startDate.seconds * 1000);
            endDate = new Date(data.endDate.seconds * 1000);
            if (data.paymentStatus) {
                $('#paymentStatus').html('<span class="badge badge-success py-2 px-3">{{ trans('lang.paid') }}</span>');
                $("#update_pay_status").prop('checked', true);
            } else {
                $('#paymentStatus').html('<span class="badge badge-danger py-2 px-3">{{ trans('lang.unpaid') }}</span>');
                $("#update_pay_status").prop('checked', false);
            }
            var status = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            if (data.hasOwnProperty('isPaused') && data.isPaused) {
                $('.paused-note-div').removeClass('d-none');
                if (data.hasOwnProperty('pauseNote') && data.pauseNote != null && data.pauseNote != '') {
                    $('#paused_note').html(data.pauseNote);
                }
                $('#status').html('<span class="badge badge-info py-2 px-3">{{ trans('lang.paused') }}</span>');
            } else {
                if (data.status == 'approved') {
                    const ExpiryDate = data.endDate;
                    if (ExpiryDate && new Date(ExpiryDate.seconds * 1000) < new Date()) {
                        $('#status').html('<span class="badge badge-danger py-2 px-3">{{ trans('lang.expired') }}</span>');
                    } else {
                        const startDate = data.startDate;
                        if (startDate && new Date(startDate.seconds * 1000) < new Date() && data.paymentStatus) {
                            $('#status').html('<span class="badge badge-success py-2 px-3">{{ trans('lang.running') }}</span>');
                        } else {
                            $('#status').html('<span class="badge badge-success py-2 px-3">{{ trans('lang.approved') }}</span>');

                        }
                    }
                } else if (data.status == 'pending' || data.status == 'updated') {

                    $('#status').html('<span class="badge badge-info py-2 px-3">{{ trans('lang.pending') }}</span>');
                } else {
                    $('.cancel-note-div').removeClass('d-none');
                    if (data.hasOwnProperty('canceledNote') && data.canceledNote != null && data.canceledNote != '') {
                        $('#cancel_note').html(data.canceledNote);
                    }
                    $('#status').html('<span class="badge badge-danger py-2 px-3">{{ trans('lang.canceled') }}</span>');
                }
            }
            $('#advTitle').html(data.title);
            $('#advDesc').html(data.description);

        })
    </script>
@endsection
