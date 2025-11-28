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
     <style type="text/css">
         form {
             width: 480px;
             margin: 20px auto;
         }

         .group {
             background: white;
             box-shadow: 0 7px 14px 0 rgba(49, 49, 93, 0.10),
                 0 3px 6px 0 rgba(0, 0, 0, 0.08);
             border-radius: 4px;
             margin-bottom: 20px;
         }

         label {
             position: relative;
             color: #8898AA;
             font-weight: 300;
             height: 40px;
             line-height: 40px;
             margin-left: 20px;
             display: block;
         }

         .group label:not(:last-child) {
             border-bottom: 1px solid #F0F5FA;
         }

         label>span {
             width: 20%;
             text-align: right;
             float: left;
         }

         .field {
             background: transparent;
             font-weight: 300;
             border: 0;
             color: #31325F;
             outline: none;
             padding-right: 10px;
             padding-left: 10px;
             cursor: text;
             width: 100%;
             height: 40px;
             float: right;
         }

         .field::-webkit-input-placeholder {
             color: #CFD7E0;
         }

         .field::-moz-placeholder {
             color: #CFD7E0;
         }

         .field:-ms-input-placeholder {
             color: #CFD7E0;
         }

         button {
             float: left;
             display: block;
             background: #666EE8;
             color: white;
             box-shadow: 0 7px 14px 0 rgba(49, 49, 93, 0.10),
                 0 3px 6px 0 rgba(0, 0, 0, 0.08);
             border-radius: 4px;
             border: 0;
             margin-top: 20px;
             font-size: 15px;
             font-weight: 400;
             width: 100%;
             height: 40px;
             line-height: 38px;
             outline: none;
         }

         button:focus {
             background: #555ABF;
         }

         button:active {
             background: #43458B;
         }

         .outcome {
             float: left;
             width: 100%;
             padding-top: 8px;
             min-height: 24px;
             text-align: center;
         }

         .success,
         .error {
             display: none;
             font-size: 13px;
         }

         .success.visible,
         .error.visible {
             display: inline;
         }

         .error {
             color: #E4584C;
         }

         .success {
             color: #666EE8;
         }

         .success .token {
             font-weight: 500;
             font-size: 13px;
         }
     </style>
     <div class="siddhi-checkout siddhi-checkout-payment">
         <div class="container position-relative">
             <div class="py-5 row">
                 <div class="col-md-12 mb-3">
                     <div>
                         <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                            <div class="pb-2 align-items-starrt sec-title col">
                                <h2 class="m-0">{{ trans('lang.pay_with_stripe') }}</h2>
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
                                                     {{ trans('lang.stripe_payment') }}
                                                 </td>
                                                 <td class="text-right payment-buttons">
                                                     <form action="<?php echo route('process-stripe'); ?>" method="post">
                                                         @csrf
                                                         <div class="form-group row">
                                                             <label
                                                                 class="col-sm-2 text-left col-form-label">{{ trans('lang.card') }}</label>
                                                             <div class="col-sm-9">
                                                                 <div id="card-element" class="field"></div>
                                                             </div>
                                                         </div>
                                 </div>
                                 <input type="hidden" name="token_id" id="token_id">
                                 <button type="submit"
                                     class="btn btn-primary">{{ trans('lang.pay') }} {{ $formatted_price }}</button>
                                 <div class="outcome">
                                     <div class="error" role="alert"></div>
                                     <div class="success">
                                         {{ trans('lang.success!') }}
                                     </div>
                                 </div>
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
     <script src="https://js.stripe.com/v3/"></script>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
     <script type="text/javascript">
         var stripe = Stripe('<?php echo $stripeKey; ?>');
         var elements = stripe.elements();
         var card = elements.create('card', {
             style: {
                 base: {
                     iconColor: '#666EE8',
                     color: '#31325F',
                     lineHeight: '40px',
                     fontWeight: 300,
                     fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                     fontSize: '15px',
                     '::placeholder': {
                         color: '#CFD7E0',
                     },
                 },
             }
         });
         card.mount('#card-element');

         function setOutcome(result) {
             var successElement = document.querySelector('.success');
             var errorElement = document.querySelector('.error');
             successElement.classList.remove('visible');
             errorElement.classList.remove('visible');
             if (result.token) {
                 $("#token_id").val(result.token.id);
                 var form = document.querySelector('form');
                 $.ajax({
                     type: 'POST',
                     url: "<?php echo route('process-stripe'); ?>",
                     data: {
                         _token: '<?php echo csrf_token(); ?>',
                         token_id: result.token.id,
                     },
                     success: function(data) {
                         data = JSON.parse(data);
                         if (data.status == true) {
                             successElement.textContent = data.message;
                             successElement.classList.add('visible');
                             window.location.href = '<?php echo route('success'); ?>';
                         } else {
                             errorElement.textContent = data.message;
                             errorElement.classList.add('visible');
                         }
                     },
                     error: function(XMLHttpRequest, textStatus, errorThrown) {
                         errorElement.textContent = XMLHttpRequest.responseJSON.message;
                         errorElement.classList.add('visible');
                     }
                 });
             } else if (result.error) {
                 errorElement.textContent = result.error.message;
                 errorElement.classList.add('visible');
             }
         }
         card.on('change', function(event) {
             setOutcome(event);
         });
         document.querySelector('form').addEventListener('submit', function(e) {
             e.preventDefault();
             var form = document.querySelector('form');
             stripe.createToken(card).then(setOutcome);
         });
     </script>
