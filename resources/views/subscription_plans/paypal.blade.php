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
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalKey; ?>&currency=USD"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="siddhi-checkout siddhi-checkout-payment">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="pb-2 align-items-starrt sec-title col">
                            <h2 class="m-0">{{trans('lang.pay_with_paypal')}}</h2>
                        </div>
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default payment-wrap">
                                <table class="payment-table m-4">
                                <thead>
                                    <tr>
                                        <th class="p-1">
                                            {{ trans('lang.pay_total') }} : {{ $formatted_price }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div id="paypal-button-container"></div>
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
<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $amount; ?>' // Can reference variables or functions. Example: `value: document.getElementById('...').value`
                    }
                }]
            });
        },
        // Finalize the transaction after payer approval
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (orderData) {
                // Successful capture! For dev/demo purposes:
                if (orderData.status == "COMPLETED") {
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo route('process-paypal'); ?>",
                        data: {_token: '<?php echo csrf_token() ?>'},
                        success: function (data) {
                            data = JSON.parse(data);
                            if (data.status == true) {
                                window.location.href = '<?php echo route('success'); ?>';
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                        }
                    });
                }
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
            });
        }
    }).render('#paypal-button-container');
</script>
</body>
