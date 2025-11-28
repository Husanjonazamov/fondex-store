 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
     <!-- Fonts -->
     <link rel="dns-prefetch" href="//fonts.gstatic.com">
     <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
     <!-- Styles -->
     <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
     <link href="{{ asset('css/style.css') }}" rel="stylesheet">
     <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"rel="stylesheet">
     <!-- @yield('style') -->
 </head>
 <body>
     <?php if (isset($_COOKIE['store_panel_color'])) { ?>
     <style type="text/css">
         a,
         a:hover,
         a:focus {
             color: <?php echo $_COOKIE['store_panel_color']; ?>;
         }
         .form-group.default-admin {
             padding: 10px;
             font-size: 14px;
             color: #000;
             font-weight: 600;
             border-radius: 10px;
             box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
             margin: 20px 10px 10px 10px;
         }
         .form-group.default-admin .crediantials-field {
             position: relative;
             padding-right: 15px;
             text-align: left;
             padding-top: 5px;
             padding-bottom: 5px;
         }
         .form-group.default-admin .crediantials-field>a {
             position: absolute;
             right: 0;
             top: 0;
             bottom: 0;
             margin: auto;
             height: 20px;
         }
         .btn-primary,
         .btn-primary.disabled,
         .btn-primary:hover,
         .btn-primary.disabled:hover {
             background: <?php echo $_COOKIE['store_panel_color']; ?>;
             border: 1px solid<?php echo $_COOKIE['store_panel_color']; ?>;
         }
         [type="checkbox"]:checked+label::before {
             border-right: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
             border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
         }
         .form-material .form-control,
         .form-material .form-control.focus,
         .form-material .form-control:focus {
             background-image: linear-gradient(<?php echo $_COOKIE['store_panel_color']; ?>, <?php echo $_COOKIE['store_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
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
         .show>.btn-primary.dropdown-toggle:focus {
             background: <?php echo $_COOKIE['store_panel_color']; ?>;
             border-color: <?php echo $_COOKIE['store_panel_color']; ?>;
             box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color']; ?>;
         }
         .error {
             color: red;
         }
     </style>
     <?php } ?>
<div class="siddhi-checkout siddhi-checkout-payment">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="pb-2 align-items-starrt sec-title col">
                            <h2 class="m-0">{{trans('lang.pay_with_razorpay')}}</h2>
                        </div>
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default payment-wrap">
                                <table class="payment-table m-4">
                                    <tbody>
                                    <tr>
                                        <td>
                                            {{ trans('lang.pay_total') }} : {{ $formatted_price }}
                                        </td>
                                        <td class="text-right payment-button">
                                            <form action="{{ route('razorpaypayment') }}" method="POST">
                                                @csrf
                                                <script src="https://checkout.razorpay.com/v1/checkout.js"
                                                        data-key="{{ $razorpayKey }}"
                                                        data-amount="{{$amount*100}}"
                                                        data-buttontext="Pay {{ $formatted_price }}"
                                                        data-name="{{env('APP_NAME', 'Foodie')}}"
                                                        data-description="Rozerpay"
                                                        data-image="https://www.itsolutionstuff.com/frontTheme/images/logo.png"
                                                        data-prefill.name="{{$authorName}}"
                                                        data-prefill.email="{{$email}}"
                                                        data-theme.color="#ff7529">
                                                </script>
                                            </form>
                                        </td>
                                    </tr>
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
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
     <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
     <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
     <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
     <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
     <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
     <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
     <script src="{{ asset('js/crypto-js.js') }}"></script>
     <script src="{{ asset('js/jquery.cookie.js') }}"></script>
     <script src="{{ asset('js/jquery.validate.js') }}"></script>
     <script>
        $(".razorpay-payment-button").addClass('btn btn-primary');
     </script>
</body>