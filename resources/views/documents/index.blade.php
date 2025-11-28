@extends('layouts.app')
@section('content')
<div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{trans('lang.document_verification')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.document_verification')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item">
                                    <a class="nav-link active vendor-name"
                                       href="{!! url()->current() !!}">{{trans('lang.document_details')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10 doc-body"></div>
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close"
                                                    data-dismiss="modal"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <embed id="docImage"
                                                       src=""
                                                       frameBorder="0"
                                                       scrolling="auto"
                                                       height="100%"
                                                       width="100%"
                                                       style="height: 540px;"
                                                ></embed>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{trans('lang.close')}}</button>
                                            </div>
                                        </div>
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
@section('scripts')z
<script>
    var id = "<?php echo $id;?>";
    var database = firebase.firestore();
    var ref = database.collection('users').where("id", "==", id);
    var docsRef = database.collection('documents').where('enable', '==', true).where('type','==','vendor');
  
    var docref = database.collection('documents_verify').doc(id);
   
    var back_photo = '';
    var front_photo = '';
    var backFileName = '';
    var frontFileName = '';
    var backFileOld = '';
    var frontFileOld = '';
    var fcmToken = "";
    $(document).ready( function () {
        jQuery("#data-table_processing").show();
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var img = button.data('image');
            var modal = $(this);
            modal.find('#docImage').attr('src', img);
        });
        ref.get().then(async function (snapshots) {
            var vendor = snapshots.docs[0].data();
            if (vendor.hasOwnProperty('fcmToken') && vendor.fcmToken != "" && vendor.fcmToken != null) {
                fcmToken = vendor.fcmToken;
            }
        });
    });
        var html = '';
        var count = 0;
     docsRef.get().then(async function (docSnapshot) {
            html += '<table id="taxTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">';
            html += "<thead>";
            html += '<tr>';
            html += '<th>Name</th>';
            html += '<th>Status</th>';
            html += '<th>Action</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            html += '</tbody>';
            html += '</table>';
       
            if (docSnapshot.docs.length) {
                var documents = docSnapshot.docs;
               
                documents.forEach((ele) => {
                    var doc = ele.data();
                    var docRefs = database.collection('documents_verify').doc(id);
                  
                    docRefs.get().then(async function (docrefSnapshot) {
                        var docRef = docrefSnapshot.data() && docrefSnapshot.data().documents ? docrefSnapshot.data().documents.filter(docId => docId.documentId == doc.id)[0] : [];
                        var trhtml = '';
                        trhtml += '<tr>';
                        if (docRef && docRef.hasOwnProperty('backImage') && docRef.hasOwnProperty('frontImage')) {
                            if (docRef.backImage != '' && docRef.frontImage != '' && docRef.backImage!=null && docRef.frontImage != null  && doc.backSide && doc.frontSide  ) {
                                                            trhtml += '<td>' + doc.title + '&nbsp;&nbsp;<a href="#" class="badge badge-info py-2 px-3 font-weight-bold" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.frontImage + '" data-id="front" class="open-image">{{trans('lang.view_front_image')}}</a>&nbsp;<a href="#" class="badge badge-info py-2 px-3 font-weight-bold" data-toggle="modal" data-target="#exampleModal"  data-image="' + docRef.backImage + '" data-id="back" class="open-image">{{trans('lang.view_back_image')}}</a></td>';
                                                                                                                                                    } else if (docRef.backImage != '' && docRef.backImage != null  && doc.backSide ) {
                                trhtml += '<td>' + doc.title + '&nbsp;<a href="#" data-toggle="modal" class="badge badge-info py-2 px-3 font-weight-bold" data-target="#exampleModal" data-id="back" data-image="' + docRef.backImage + '" class="open-image">{{trans('lang.view_back_image')}}</a></td>';
                                                            } else if (docRef.frontImage != '' && docRef.frontImage != null  && doc.frontSide ) {
                                trhtml += '<td>' + doc.title + '&nbsp;<a href="#" data-toggle="modal" class="badge badge-info py-2 px-3 font-weight-bold" data-target="#exampleModal" data-id="front" class="open-image" data-image="' + docRef.frontImage + '">{{trans('lang.view_front_image')}}</a></td>';
                                                            } else {
                                trhtml += '<td>' + doc.title + '</td>';
                            }
                        } else {
                            trhtml += '<td>' + doc.title + '</td>';
                        }
                        var status = docRef  && docRef.status == "approved" ? 'approved' : ((docRef  && docRef.status == "rejected") ? "rejected" : ((docRef && docRef.status == "uploaded") ? 'uploaded' : 'pending'));
                        var display_status = '';
                        if (status == "approved") {
                            display_status = '<span class="badge badge-success py-2 px-3 font-weight-bold text-capitalize">' + status + '</span>';
                        } else if (status == "rejected") {
                            display_status = '<span class="badge badge-danger py-2 px-3 font-weight-boldtext-capitalize">' + status + '</span>';
                        } else if (status == "uploaded") {
                            display_status = '<span class="badge badge-primary py-2 px-3 font-weight-bold text-capitalize">' + status + '</span>';
                        } else if (status == "pending") {
                            display_status = '<span class="badge badge-warning py-2 px-3 font-weight-bold text-capitalize">' + status + '</span>';
                        }
                        trhtml += '<td>' + display_status + '</td>';
                        trhtml += '<td class="action-btn">';
                        if(status=="pending" || status=="rejected"){
                        trhtml += '<a href="' + (`/document/upload/` + doc.id.trim()) + '" data-id="' + doc.id + '"><i class="fa fa-edit"></i></a>&nbsp;';
                        }
                        trhtml += '</td>';
                        trhtml += '</tr>';
                        $("tbody").append(trhtml);
                        count = count + 1;
                        if (count == docSnapshot.docs.length) {
                            $('#taxTable').DataTable({
                                order: [[0, 'asc']],
                                columnDefs: [
                                    {orderable: false, targets: [1, 2]}
                                ],
                            });
                        }
                    })
                });
            }
            $(".doc-body").append(html);
            jQuery("#data-table_processing").hide();
        });
    $(document.body).on('click', '.redirecttopage', function () {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });
  </script>
@endsection
