<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-light-icon.png') }}">
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css') }}" rel="stylesheet">
        <?php } ?>
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{ asset('css/style_rtl.css') }}" rel="stylesheet">
        <?php } ?>
        <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">
        <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <!-- Datatable css -->

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

        <!-- @yield('style') -->
        <?php if (isset($_COOKIE['store_panel_color'])) { ?>
        <style type="text/css">
            .topbar {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .sidebar-nav ul li a {
                border-bottom: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .sidebar-nav ul li a:hover i {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .vendor_payout_create-inner fieldset legend {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            a {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            a:hover,
            a:focus {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            a.link:hover,
            a.link:focus {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            html body blockquote {
                border-left: 5px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .text-warning {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?> !important;
            }

            .text-info {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?> !important;
            }

            .sidebar-nav ul li a:hover {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .btn-primary {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .sidebar-nav>ul>li.active>a {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                border-left: 3px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .sidebar-nav>ul>li.active>a i {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .bg-info {
                background-color: <?php echo $_COOKIE['store_panel_color'];
                ?> !important;
            }

            .bellow-text ul li>span {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>
            }

            .table tr td.redirecttopage {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>
            }

            ul.rating {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            nav-link.active {
                background-color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .nav-tabs.card-header-tabs .nav-link:hover {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .nav-tabs .nav-item.show .nav-link,
            .nav-tabs .nav-link.active {
                color: #fff;
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .btn-warning,
            .btn-warning.disabled {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
                box-shadow: none;
            }

            .payment-top-tab .nav-tabs.card-header-tabs .nav-link.active,
            .payment-top-tab .nav-tabs.card-header-tabs .nav-link:hover {
                border-color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .nav-tabs.card-header-tabs .nav-link span.badge-success {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .nav-tabs.card-header-tabs .nav-link.active span.badge-success,
            .nav-tabs.card-header-tabs .nav-link:hover span.badge-success,
            .sidebar-nav ul li a.active,
            .sidebar-nav ul li a.active:hover,
            .sidebar-nav ul li.active a.has-arrow:hover,
            .topbar ul.dropdown-user li a:hover {
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .sidebar-nav ul li a.has-arrow:hover::after,
            .sidebar-nav .active>.has-arrow::after,
            .sidebar-nav li>.has-arrow.active::after,
            .sidebar-nav .has-arrow[aria-expanded="true"]::after,
            .sidebar-nav ul li a:hover {
                border-color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            [type="checkbox"]:checked+label::before {
                border-right: 2px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
                border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .btn-primary:hover,
            .btn-primary.disabled:hover {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .btn-primary.active,
            .btn-primary:active,
            .btn-primary:focus,
            .btn-primary.disabled.active,
            .btn-primary.disabled:active,
            .btn-primary.disabled:focus,
            .btn-primary.active.focus,
            .btn-primary.active:focus,
            .btn-primary.active:hover,
            .btn-primary.focus:active,
            .btn-primary:active:focus,
            .btn-primary:active:hover,
            .open>.dropdown-toggle.btn-primary.focus,
            .open>.dropdown-toggle.btn-primary:focus,
            .open>.dropdown-toggle.btn-primary:hover,
            .btn-primary.focus,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled).active:focus,
            .btn-primary:not(:disabled):not(.disabled):active:focus,
            .show>.btn-primary.dropdown-toggle:focus,
            .btn-warning:hover,
            .btn-warning:hover,
            .btn-warning.disabled:hover,
            .btn-warning.active.focus,
            .btn-warning.active:focus,
            .btn-warning.active:hover,
            .btn-warning.focus:active,
            .btn-warning:active:focus,
            .btn-warning:active:hover,
            .open>.dropdown-toggle.btn-warning.focus,
            .open>.dropdown-toggle.btn-warning:focus,
            .open>.dropdown-toggle.btn-warning:hover,
            .btn-warning.focus,
            .btn-warning:focus {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                border-color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
                box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .language-options select option,
            .pagination>li>a.page-link:hover {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .nav-tabs.card-header-tabs .active.nav-item .nav-link {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }

            .print-btn button {
                border: 2px solid<?php echo $_COOKIE['store_panel_color'];
                ?>;
                color: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }
            .review-rating-demo{background: <?php echo $_COOKIE['store_panel_color'];
                ?>;}
                .restaurant_payout_create-inner fieldset legend {
                background: <?php echo $_COOKIE['store_panel_color'];
                ?>;
            }
            .table tr td > span.badge {
    display: inline-block;
    padding: 10px 15px 10px 15px;
    line-height: 1;
    text-transform: capitalize;
    border-radius: 50px;
    text-align: center;
    font-size: 13px;
}
        </style>
        <?php } ?>

        <?php
            $id = Auth::user()->getvendorId();
            $vendorUserRow = \App\Models\VendorUsers::where('user_id', Auth::user()->id)->first();
            $firestoreVendorIdSaved = $vendorUserRow ? ($vendorUserRow->firestore_vendor_id ?? '') : '';
        ?>
        <script type="text/javascript">
            var cuser_id = '<?php echo $id; ?>';
            var _firestoreVendorIdSaved = '<?php echo $firestoreVendorIdSaved; ?>';
        </script>

    </head>

    <body>

        <div id="app" class="fix-header fix-sidebar card-no-border">

            <div id="main-wrapper">
                <div id="data-table_processing" class="page-overlay" style="display:none;">
                    <div class="overlay-text">
                        <img src="{{ asset('images/spinner.gif') }}">
                    </div>
                </div>

                <header class="topbar">

                    <nav class="navbar top-navbar navbar-expand-md navbar-light">
                        @include('layouts.header')
                    </nav>

                </header>

                <aside class="left-sidebar">
                    <!-- Sidebar scroll-->
                    <div class="scroll-sidebar">
                        @include('layouts.menu')
                    </div>
                    <!-- End Sidebar scroll-->
                </aside>
            </div>

            <main class="py-4">
                @yield('content')
            </main>
            <div class="modal fade" id="notification_order" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title order_subject" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="auth_accept_name" class="order_message"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('orders') }}" id="notification_url">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="notification_book_table_order" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title dinein_order_subject" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="auth_accept_name_book_table" class="dinein_order_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('booktable') }}" id="notification_book_table_url">Go</a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="notification_accepted_order" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title driver_accepted_subject" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="np_accept_name" class="driver_accepted_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('orders') }}" id="notification_accepted_a">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="advertisement_accepted_notification" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title advertisement_accepted_sub" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="advertisement_accepted_msg" class="advertisement_accepted_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('advertisement') }}" id="advertisement_accepted_route">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="advertisement_canceled_notification" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title advertisement_cancelled_sub" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="advertisement_cancelled_msg" class="advertisement_cancelled_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('advertisement') }}" id="advertisement_canceled_route">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="advertisement_paused_notification" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title advertisement_paused_sub" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="advertisement_paused_msg" class="advertisement_paused_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('advertisement') }}" id="advertisement_paused_route">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="advertisement_resumed_notification" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered notification-main" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title advertisement_resumed_sub" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6><span id="advertisement_resumed_msg" class="advertisement_resumed_msg"></span></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><a href="{{ url('advertisement') }}" id="advertisement_resumed_route">Go</a></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('js/waves.js') }}"></script>
        <script src="{{ asset('js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('js/custom.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>

        <!-- Datatable script -->

        <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.24/jspdf.plugin.autotable.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script type="text/javascript">
            jQuery(window).scroll(function() {
                var scroll = jQuery(window).scrollTop();
                if (scroll <= 60) {
                    jQuery("body").removeClass("sticky");
                } else {
                    jQuery("body").addClass("sticky");
                }
            });
        </script>

        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
        <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
        <script src="{{ asset('js/geofirestore.js') }}"></script>
        <script src="{{ asset('js/chosen.jquery.js') }}"></script>
        <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>

        @yield('scripts')

        <script type="text/javascript">
            var route1 = '{{ route('orders.edit', ':id') }}';
            var booktable = '{{ route('booktable.edit', ':id') }}';

            var languages_list_main = [];
            var database = firebase.firestore();

            var version = database.collection('settings').doc("Version");

            version.get().then(async function(snapshots) {
                if (snapshots.exists) {
                    var version_data = snapshots.data();

                    if (version_data == undefined) {
                        database.collection('settings').doc('Version').set({});
                    }
                    try {

                        $('.web_version').html("V:" + version_data.web_version);

                    } catch (error) {

                    }
                }

            });




            var orderPlacedSubject = '';
            var orderPlacedMsg = '';
            var dineInPlacedSubject = '';
            var dineInPlacedMsg = '';
            var driverAcceptedMsg = '';
            var driverAcceptedSubject = '';
            var scheduleOrderPlacedSubject = '';
            var scheduleOrderPlacedMsg = '';
            var storeOrderCompletedSubject = '';
            var storeOrderCompletedMsg = '';
            var storeOrderAcceptedSubject = '';
            var storeOrderAcceptedMsg = '';

            var storeOrderInTransitSubject = "";
            var storeOrderInTransitMsg = "";

            database.collection('dynamic_notification').get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {
                    snapshot.docs.map(async (listval) => {
                        val = listval.data();

                        if (val.type == "dinein_placed") {

                            dineInPlacedSubject = val.subject;
                            dineInPlacedMsg = val.message;

                        } else if (val.type == "order_placed") {

                            orderPlacedSubject = val.subject;
                            orderPlacedMsg = val.message;

                        } else if (val.type == "driver_accepted") {

                            driverAcceptedSubject = val.subject;
                            driverAcceptedMsg = val.message;

                        } else if (val.type == "schedule_order") {

                            scheduleOrderPlacedSubject = val.subject;
                            scheduleOrderPlacedMsg = val.message;
                        } else if (val.type == "store_completed") {
                            storeOrderCompletedSubject = val.subject;
                            storeOrderCompletedMsg = val.message;

                        } else if (val.type == "store_accepted") {
                            storeOrderAcceptedSubject = val.subject;
                            storeOrderAcceptedMsg = val.message;

                        } else if (val.type == "store_intransit") {
                            storeOrderInTransitSubject = val.subject;
                            storeOrderInTransitMsg = val.message;

                        } else if (val.type == "advertisement_approved") {
                            advApprovedSub = val.subject;
                            advApprovedMsg = val.message;
                        } else if (val.type == "advertisement_cancelled") {
                            advCancelledSub = val.subject;
                            advCancelledMsg = val.message;
                        } else if (val.type == "advertisement_paused") {
                            advPausedSub = val.subject;
                            advPausedMsg = val.message;
                        } else if (val.type == "advertisement_resumed") {
                            advResumedSub = val.subject;
                            advResumedMsg = val.message;
                        }


                    });
                }
            });


            var pageloadded = 0;
            database.collection('vendor_orders').where('vendor.author', "==", cuser_id).onSnapshot(function(doc) {
                if (pageloadded) {
                    doc.docChanges().forEach(function(change) {
                        val = change.doc.data();
                        if (section_id == val.section_id) {
                            if (change.type == "added") {
                                if (val.status == "Order Placed") {

                                    if (val.scheduleTime != undefined && val.scheduleTime != null && val
                                        .scheduleTime != '') {
                                        $('.order_subject').text(scheduleOrderPlacedSubject);
                                        $('.order_message').text(scheduleOrderPlacedMsg);
                                    } else {
                                        $('.order_subject').text(orderPlacedSubject);
                                        $('.order_message').text(orderPlacedMsg);
                                    }
                                    if (route1) {
                                        jQuery("#notification_url").attr("href", route1.replace(':id', val.id));
                                    }
                                    jQuery("#notification_order").modal('show');

                                }
                            } else if (change.type == "modified") {

                                if (val.status == "Order Placed") {

                                    if (!val.hasOwnProperty('estimatedTimeToPrepare')) {

                                        if (route1) {
                                            jQuery("#notification_url").attr("href", route1.replace(':id', val
                                                .id));
                                        }
                                        if (val.scheduleTime != undefined && val.scheduleTime != null && val
                                            .scheduleTime != '') {
                                            $('.order_subject').text(scheduleOrderPlacedSubject);
                                            $('.order_message').text(scheduleOrderPlacedMsg);
                                        } else {
                                            $('.order_subject').text(orderPlacedSubject);
                                            $('.order_message').text(orderPlacedMsg);
                                        }
                                        jQuery("#notification_order").modal('show');
                                    }

                                } else if (val.status == "Driver Accepted") {
                                    $('.driver_accepted_subject').text(driverAcceptedSubject);
                                    $('.driver_accepted_msg').text(driverAcceptedMsg);
                                    if (route1) {
                                        jQuery("#notification_accepted_a").attr("href", route1.replace(':id',
                                            val.id));
                                    }
                                    jQuery("#notification_accepted_order").modal('show');
                                }
                            }
                        }
                    });
                } else {
                    pageloadded = 1;
                }
            });

            var pageloadded_book = 0;
            database.collection('booked_table').where('vendor.author', "==", cuser_id).onSnapshot(function(doc) {
                if (pageloadded_book) {
                    doc.docChanges().forEach(function(change) {
                        val = change.doc.data();
                        if (change.type == "added") {
                            if (val.status == "Order Placed") {

                                if (booktable) {
                                    jQuery("#notification_book_table_url").attr("href", booktable.replace(':id',
                                        val.id));
                                }
                                $('.dinein_order_subject').text(dineInPlacedSubject);
                                $('.dinein_order_msg').text(dineInPlacedMsg);
                                jQuery("#notification_book_table_order").modal('show');
                            }
                        }

                    });
                } else {
                    pageloadded_book = 1;
                }
            });

            var pageLoadedAdvertisement = 0;
            database.collection('users').where('id', '==', cuser_id).get().then(async function(snapshots) {
                if (!snapshots.empty) {
                    var userData = snapshots.docs[0].data();
                    var photoURL = userData.profilePictureURL 
                        ? userData.profilePictureURL 
                        : "{{ asset('images/users/user-2.png') }}";

                    document.querySelectorAll('.userimage').forEach(function(img) {
                        img.src = photoURL;
                        img.onerror = function() {
                            this.onerror = null;
                            this.src = "{{ asset('images/users/user-2.png') }}";
                        };
                    });

                    if (userData.hasOwnProperty('vendorID') && userData.vendorID != '' && userData.vendorID != null) {
                        vendorId = userData.vendorID;
                        database.collection('advertisements').where('vendorId', "==", vendorId).onSnapshot(function(doc) {

                            if (pageLoadedAdvertisement) {

                                doc.docChanges().forEach(function(change) {
                                    val = change.doc.data();
                                    var recentlyModifiedAd = localStorage.getItem('storeModifiedAd');

                                    if (recentlyModifiedAd === val.id) {
                                        localStorage.removeItem('storeModifiedAd');
                                        return;
                                    }
                                    if (change.type == "modified") {
                                        var routeAdview = "{{ route('advertisements.view', ':id') }}";
                                        routeAdview = routeAdview.replace(':id', val.id);
                                        if (val.status == 'approved') {
                                            console.log('here')
                                            if (val.status == 'approved' && val.isPaused) {
                                                $('.advertisement_paused_sub').html(advPausedSub);
                                                $('.advertisement_paused_msg').html(advPausedMsg);
                                                $('#advertisement_paused_notification').modal('show');
                                                $('#advertisement_paused_route').attr('href', routeAdview);
                                            } else if (val.status == 'approved' && val.isPaused == null) {

                                                $('.advertisement_accepted_msg').html(advApprovedMsg);
                                                $('.advertisement_accepted_sub').html(advApprovedSub);
                                                $('#advertisement_accepted_notification').modal('show');
                                                $('#advertisement_accepted_route').attr('href', routeAdview);
                                            } else {
                                                $('.advertisement_resumed_sub').html(advResumedSub);
                                                $('.advertisement_resumed_msg').html(advResumedMsg);
                                                $('#advertisement_resumed_notification').modal('show');
                                                $('#advertisement_resumed_route').attr('href', routeAdview);
                                            }
                                        } else if (val.status == 'canceled') {
                                            $('.advertisement_cancelled_sub').html(advCancelledSub);
                                            $('.advertisement_cancelled_msg').html(advCancelledMsg);
                                            $('#advertisement_canceled_notification').modal('show');
                                            $('#advertisement_canceled_route').attr('href', routeAdview);
                                        }

                                    }
                                })
                            } else {
                                pageLoadedAdvertisement = 1;
                            }
                        })
                    }
                }
            });

            var langcount = 0;
            var languages_list = database.collection('settings').doc('languages');
            languages_list.get().then(async function(snapshotslang) {
                snapshotslang = snapshotslang.data();
                if (snapshotslang != undefined) {
                    snapshotslang = snapshotslang.list;
                    languages_list_main = snapshotslang;
                    snapshotslang.forEach((data) => {
                        if (data.isActive == true) {
                            langcount++;
                            $('#language_dropdown').append($("<option></option>").attr("value", data.slug)
                                .text(data.title));
                        }
                    });
                    if (langcount > 1) {
                        $("#language_dropdown_box").css('visibility', 'visible');
                    }
                    <?php if (session()->get('locale')) { ?>
                    $("#language_dropdown").val("<?php echo session()->get('locale'); ?>");
                    <?php } ?>

                }
            });

            var url = "{{ route('changeLang') }}";

            $(".changeLang").change(function() {
                var slug = $(this).val();
                languages_list_main.forEach((data) => {
                    if (slug == data.slug) {
                        if (data.is_rtl == undefined) {
                            setCookie('is_rtl', 'false', 365);
                        } else {

                            setCookie('is_rtl', data.is_rtl.toString(), 365);
                        }
                        window.location.href = url + "?lang=" + slug;
                    }
                });
            });

            function setCookie(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                let expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }

            database.collection('settings').doc("notification_setting").get().then(async function(snapshots) {
                var data = snapshots.data();
                serviceJson = data.serviceJson;
                if (serviceJson != '' && serviceJson != null) {
                    $.ajax({
                        type: 'POST',
                        data: {
                            serviceJson: btoa(serviceJson),
                        },
                        url: "{{ route('storeServiceFile') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            checkFlag = true;
                        }
                    });
                }
            });

            //On delete item delete image also from bucket general code
            const deleteDocumentWithImage = async (collection, id, singleImageField, arrayImageField) => {
                // Reference to the Firestore document
                const docRef = database.collection(collection).doc(id);
                try {
                    const doc = await docRef.get();
                    if (!doc.exists) {
                        console.log("No document found for deletion");
                        return;
                    }

                    const data = doc.data();

                    // Handle single image deletion
                    const item_file_name = data[singleImageField] || ''; // Single image field
                    if (item_file_name) {
                        await deleteImageFromBucket(item_file_name);
                    }

                    // Handle array of images deletion
                    const item_photos = data[arrayImageField] || []; // Photos array field
                    if (item_photos.length > 0) {
                        for (let i = 0; i < item_photos.length; i++) {
                            const photoUrl = item_photos[i];
                            if (photoUrl) {
                                await deleteImageFromBucket(photoUrl);
                            }
                        }
                    }

                    // Handle variant images deletion            
                    const item_attribute = data.item_attribute || {}; // Access item_attribute
                    const variants = item_attribute.variants || []; // Access variants array inside item_attribute
                    if (variants.length > 0) {
                        for (let i = 0; i < variants.length; i++) {
                            const variantImageUrl = variants[i].variant_image;
                            if (variantImageUrl) {
                                await deleteImageFromBucket(variantImageUrl);
                            }
                        }
                    }

                    // Optionally delete the Firestore document after image deletion
                    await docRef.delete();
                    console.log("Document and images deleted successfully.");
                } catch (error) {
                    console.error("Error deleting document and images:", error);
                }
            };

            const deleteImageFromBucket = async (imageUrl) => {
                try {
                    const storageRef = firebase.storage().ref();

                    // Check if the imageUrl is a full URL or just a child path
                    let oldImageUrlRef;
                    if (imageUrl.includes('https://')) {
                        // Full URL
                        oldImageUrlRef = storageRef.storage.refFromURL(imageUrl);
                    } else {
                        // Child path, use ref instead of refFromURL
                        oldImageUrlRef = storageRef.storage.ref(imageUrl);
                    }
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    var imageBucket = oldImageUrlRef.bucket;
                    // Check if the bucket name matches
                    if (imageBucket === envBucket) {
                        // Delete the image
                        await oldImageUrlRef.delete();
                        console.log("Image deleted successfully.");
                    }
                } catch (error) {

                }
            };

            function exportData(dt, format, config) {
                const {
                    columns,
                    fileName = 'Export',
                } = config;

                const filteredRecords = dt.ajax.json().filteredData;

                const fieldTypes = {};
                const dataMapper = (record) => {
                    return columns.map((col) => {
                        const value = record[col.key];
                        if (!fieldTypes[col.key]) {
                            if (value === true || value === false) {
                                fieldTypes[col.key] = 'boolean';
                            } else if (value && typeof value === 'object' && value.seconds) {
                                fieldTypes[col.key] = 'date';
                            } else if (typeof value === 'number') {
                                fieldTypes[col.key] = 'number';
                            } else if (typeof value === 'string') {
                                fieldTypes[col.key] = 'string';
                            } else {
                                fieldTypes[col.key] = 'string';
                            }
                        }

                        switch (fieldTypes[col.key]) {
                            case 'boolean':
                                return value ? 'Yes' : 'No';
                            case 'date':
                                return value ? new Date(value.seconds * 1000).toLocaleString() : '-';
                            case 'number':
                                return typeof value === 'number' ? value : 0;
                            case 'string':
                            default:
                                return value || '-';
                        }
                    });
                };

                const tableData = filteredRecords.map(dataMapper);

                const data = [columns.map(col => col.header), ...tableData];

                const columnWidths = columns.map((_, colIndex) =>
                    Math.max(...data.map(row => row[colIndex]?.toString().length || 0))
                );

                if (format === 'csv') {
                    const csv = data.map(row => row.map(cell => {
                        if (typeof cell === 'string' && (cell.includes(',') || cell.includes('\n') || cell.includes(
                                '"'))) {
                            return `"${cell.replace(/"/g, '""')}"`;
                        }
                        return cell;
                    }).join(',')).join('\n');

                    const blob = new Blob([csv], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    saveAs(blob, `${fileName}.csv`);
                } else if (format === 'excel') {
                    const ws = XLSX.utils.aoa_to_sheet(data, {
                        cellDates: true
                    });

                    ws['!cols'] = columnWidths.map(width => ({
                        wch: Math.min(width + 5, 30)
                    }));

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Data');
                    XLSX.writeFile(wb, `${fileName}.xlsx`);
                } else if (format === 'pdf') {

                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape for more width

                    doc.setFontSize(12);
                    doc.text(fileName, 14, 16);

                    doc.autoTable({
                        head: [columns.map(col => col.header)],
                        body: tableData,
                        startY: 20,
                        theme: 'striped',
                        styles: {
                            cellPadding: 1,
                            fontSize: 8,
                            overflow: 'linebreak',
                        },
                        columnStyles: {
                            0: {
                                cellWidth: 'auto'
                            }, // Adjust first column automatically
                        },
                        margin: {
                            top: 30,
                            bottom: 30
                        },
                        pageBreak: 'auto', // Ensures page break for long content
                    });

                    doc.save(`${fileName}.pdf`);

                } else {
                    console.error('Unsupported format');
                }
            }
        </script>

        {{-- Auto-save vendor Firestore ID if not yet saved --}}
        @if(empty($firestoreVendorIdSaved))
        <script>
        $(document).ready(function() {
            var db = firebase.firestore();
            console.log('[AutoSync] cuser_id:', cuser_id);
            db.collection('vendors').where('author', '==', cuser_id).get().then(function(snaps) {
                console.log('[AutoSync] vendors found:', snaps.size);
                if (!snaps.empty) {
                    var firestoreVendorId = snaps.docs[0].data().id || '';
                    console.log('[AutoSync] firestoreVendorId:', firestoreVendorId);
                    if (firestoreVendorId) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("saveVendorFirestoreId") }}',
                            data: { firestore_vendor_id: firestoreVendorId },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(r) { console.log('[AutoSync] saved OK:', r); },
                            error: function(xhr) { console.error('[AutoSync] save failed:', xhr.responseText); }
                        });
                    }
                } else {
                    console.warn('[AutoSync] No vendor found for cuser_id:', cuser_id);
                }
            }).catch(function(e) { console.error('[AutoSync] Firestore error:', e); });
        });
        </script>
        @endif

    </body>

</html>
