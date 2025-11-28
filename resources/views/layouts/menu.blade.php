<nav class="sidebar-nav">

    <ul id="sidebarnav">

        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                href="{!! url('dashboard') !!}" aria-expanded="false">

                <i class="mdi mdi-home"></i>

                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>

            </a>
        </li>


    </ul>

    <p class="web_version"></p>

</nav>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="{{ asset('js/geofirestore.js') }}"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";

    var commisionModel = false;
    var subscriptionModel = false;
    var documentVerificationEnable = false;
    var vendorId = null;
    var dineIn = false;
    var specialOffer = false;
    var enableAdvertisement = false;
    var enableSelfDelivery = false;
    var section_id = '';
    var service_type = '';
    var subscriptionBusinessModel = database.collection('settings').doc("vendor");
    subscriptionBusinessModel.get().then(async function(snapshots) {
        var subscriptionSetting = snapshots.data();
        if (subscriptionSetting.subscription_model == true) {
            subscriptionModel = true;
        }
    });
    var ref = database.collection('settings').doc("specialDiscountOffer");
    ref.get().then(async function(snapshots) {
        var specialDiscountOffer = snapshots.data();
        if (specialDiscountOffer.isEnable) {
            specialOffer = true;
        }
    });
    database.collection('documents_verify')
        .where('id', '==', vendorUserId)
        .get()
        .then(function(querySnapshot) {
            var allApproved = false;

            querySnapshot.forEach(function(doc) {
                var data = doc.data();
                // ---- 1. documents is an ARRAY → loop through it ----
                if (Array.isArray(data.documents)) {
                    data.documents.forEach(function(item) {
                        var st = (item.status || '').toString().trim().toLowerCase();
                        console.log("Document status if:", st);
                        if (st == 'approved') {
                            allApproved = true; // stop early if you want
                        }
                    });
                } else {
                    // ---- 2. fallback: older docs that have status directly ----
                    var st = (data.status || '').toString().trim().toLowerCase();
                    console.log("Document status else:", st);
                    if (st == 'approved') {
                        allApproved = true; // stop early if you want
                    }
                }
            });

            allDocsApproved = allApproved;
      database.collection('settings').doc("document_verification_settings").get()
                .then(function(settingDoc) {
                    var isStoreVerification = false;
                    if (settingDoc.exists) {
                        var settingData = settingDoc.data();
                        console.log("Document Verification Settings:", settingData);
                        isStoreVerification = settingData.isStoreVerification;
                    }
                    // Only show store if ALL documents are approved
                    if (allApproved == true || isStoreVerification == false) {
                        var newLi = `
                <li class="{{ request()->routeIs('store') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{!! url('store') !!}" aria-expanded="false">
                        <i class="mdi mdi-store"></i>
                        <span class="hide-menu">{{ trans('lang.mystore_plural') }}</span>
                    </a>
                </li>`;
                        $('#sidebarnav').append(newLi);
                    }
            })
                .catch(function(err) {
                    console.log("Error reading document_verification_settings:", err);
                });
           
        })
        .catch(function(error) {
            console.log("Error checking documents:", error);
        });
    database.collection('settings').doc("document_verification_settings").get().then(async function(snapshots) {
        var documentVerification = snapshots.data();
        if (documentVerification.isStoreVerification) {
            documentVerificationEnable = true;
            var newLi = `
                <li class="{{ request()->routeIs('vendors.document') ? 'active' : '' }}">
                <a class="waves-effect waves-dark" href="{!! url('document-list') !!}" aria-expanded="false">
                    <i class="mdi mdi-file-document"></i>
                    <span class="hide-menu">{{ trans('lang.document_plural') }}</span>
                </a>
            </li>`;
            $('#sidebarnav').append(newLi);

        }else{
            documentVerificationEnable = false;
            var newLi = `
                <li class="{{ request()->routeIs('vendors.document') ? 'active' : '' }}">
                <a class="waves-effect waves-dark" href="{!! url('document-list') !!}" aria-expanded="false">
                    <i class="mdi mdi-file-document"></i>
                    <span class="hide-menu">{{ trans('lang.document_plural') }}</span>
                </a>
            </li>`;
            $('#sidebarnav').append(newLi);
        }
    })
    database.collection('settings').doc("globalSettings").get().then(async function(
        settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isEnableAdsFeature) {
                enableAdvertisement = true;
            }
            if (settingData.isSelfDelivery) {
                enableSelfDelivery = true;
            }
        }
    })
    var newLi = '';

    database.collection('users').doc(vendorUserId).get().then(async function(usersnapshots) {
        var userData = usersnapshots.data();
        var checkVendor = null;
        var username = userData.firstName + ' ' + userData.lastName;
        $('#username').text(username);

        if (userData.hasOwnProperty('profilePictureURL') && userData.profilePictureURL !=
            "") {
            $('.userimage').attr('src', userData.profilePictureURL);
        }

        if (userData.hasOwnProperty('sectionId')) {
            section_id = userData.sectionId;
            service_type = await getSectionServiceType(section_id);

        }

        if (userData.hasOwnProperty('vendorID') && userData.vendorID != '' && userData
            .vendorID != null) {
            vendorId = userData.vendorID;
            checkVendor = userData.vendorID;
        }

        if (subscriptionModel == true || commisionModel == true) {
            newLi += `<li class="{{ request()->routeIs('subscription-plan.show') ? 'active' : '' }}">
                            <a class="waves-effect waves-dark" href="{!! route('subscription-plan.show') !!}" aria-expanded="false">
                                <i class="mdi mdi-crown"></i>
                                <span class="hide-menu">{{ trans('lang.change_subscription') }}</span>
                            </a>
                        </li>`;

        }
        newLi += `<li class="{{ request()->routeIs('my-subscriptions') ? 'active' : '' }}">
                                    <a class="waves-effect waves-dark" href="{!! url('my-subscriptions') !!}" aria-expanded="false">
                                        <i class="mdi mdi-wallet-membership"></i>
                                        <span class="hide-menu">{{ trans('lang.my_subscriptions') }}</span>
                                    </a>
                                 </li>`;


        if (checkVendor != null) {
            newLi += `<li class="{{ request()->routeIs('items') ? 'active' : '' }}">
                                    <a class="waves-effect waves-dark" href="{!! url('items') !!}" aria-expanded="false">
                                        <i class="mdi mdi-shopping"></i>
                                        <span class="hide-menu">{{ trans('lang.item_plural') }}</span>
                                    </a>
                                </li>
                        <li class="{{ request()->routeIs('orders') ? 'active' : '' }}">
                            <a class="waves-effect waves-dark" href="{!! url('orders') !!}" aria-expanded="false">
                                <i class="mdi mdi-reorder-horizontal"></i>
                                <span class="hide-menu">{{ trans('lang.order_plural') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coupons') ? 'active' : '' }}"><a class="waves-effect waves-dark" href="{!! url('coupons') !!}" aria-expanded="false">
                                <i class="mdi mdi-sale"></i>
                                <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
                            </a>
                        </li>`;
            if (enableAdvertisement) {
                var adsParentActive =
                    "{{ request()->routeIs('advertisements.pending') || request()->routeIs('advertisements') ? 'active' : '' }}";
                var pendingActive = "{{ request()->routeIs('advertisements.pending') ? 'active' : '' }}";
                var listActive = "{{ request()->routeIs('advertisements') ? 'active' : '' }}";

                var dropdownShow = (pendingActive || listActive) ? 'show' : '';
                var ariaExpanded = (pendingActive || listActive) ? 'true' : 'false';
                newLi += `<li class="${adsParentActive}"><a class="has-arrow waves-effect waves-dark" href="#"
                                                    data-toggle="collapse" data-target="#adsDropdown" aria-expanded="${ariaExpanded}">
                                                    <i class="mdi mdi-newspaper"></i>
                                                    <span class="hide-menu">{{ trans('lang.advertisement_plural') }}</span>
                                                </a>
                                                <ul id="adsDropdown" aria-expanded="false" class="collapse ${dropdownShow}">
                                                    <li class="${pendingActive}"><a class="${pendingActive}" href="{!! url('advertisements/pending') !!}">{{ trans('lang.pending') }}</a></li>
                                                    <li class="${listActive}"><a class="${listActive}" href="{!! url('advertisements') !!}">{{ trans('lang.ads_list') }}</a></li>
                                                </ul>
                                            </li>`;
            }

            if (enableSelfDelivery && service_type == 'Multivendor Delivery Service') {
                newLi += `<li class="{{ request()->routeIs('deliveryman') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                                    href="{!! url('deliveryman') !!}" aria-expanded="false">
                                    <i class="mdi mdi-run"></i>
                                    <span class="hide-menu">{{ trans('lang.delivery_man') }}</span>
                                </a>
                            </li>`;
            }
            newLi += `<li class="{{ request()->routeIs('payments') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{!! url('payments') !!}" aria-expanded="false">
                                <i class="mdi mdi-wallet"></i>
                                <span class="hide-menu">{{ trans('lang.payouts_plural') }}</span>
                            </a>

                        </li>`;
            if (specialOffer) {
                newLi += `<li class="{{ request()->routeIs('specialOffer') ? 'active' : '' }}">
                            <a class="waves-effect waves-dark" href="{!! url('special-offer') !!}" aria-expanded="false">
                                <i class="fa fa-table "></i>
                                <span class="hide-menu">{{ trans('lang.special_offer') }}</span>
                            </a>
                        </li>`;
            }
            if (dineIn) {

                newLi += `<li class="dineInHistory {{ request()->routeIs('booktable') ? 'active' : '' }}"><a class="waves-effect waves-dark"
                                                    href="{!! url('booktable') !!}" aria-expanded="false">
                                                    <i class="fa fa-table "></i>
                                                    <span class="hide-menu">DINE IN Booking History</span>
                                                </a>
                                            </li>`;
            }

        }
        newLi += `<li class="{{ request()->routeIs('wallettransaction.index') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{!! url('wallettransaction') !!}" aria-expanded="false">
                                    <i class="mdi mdi-swap-horizontal"></i>
                                    <span class="hide-menu">{{ trans('lang.wallet_transaction_plural') }}</span>
                                </a>
                            </li>

                            <li class="{{ request()->routeIs('withdraw-method') ? 'active' : '' }}">
                                <a class=" waves-effect waves-dark" href="{!! url('withdraw-method') !!}" aria-expanded="false">
                                    <i class="fa fa-credit-card "></i>
                                    <span class="hide-menu">{{ trans('lang.withdrawal_method') }}</span>
                                </a>
                            </li>`;
        if (enableAdvertisement && allDocsApproved) {
            newLi += `<li class="waves-effect waves-dark p-2">
                        <div class="promo-card">
                            <div class="position-relative">
                                <img src="{{ asset('images/advertisement_promo.png') }}" class="mw-100" alt="">
                                <h4 class="mb-2 mt-3">Want to get highlighted?</h4>
                                <p class="mb-4">
                                    Create ads to get highlighted on the app and web browser
                                </p>
                                <a href="{{ route('advertisements.create') }}" class="btn btn-primary">Create Ads</a>
                            </div>
                        </div>
                    </li>`
        }

        $('#sidebarnav').append(newLi);

        if (commisionModel || subscriptionModel) {
            if (userData.hasOwnProperty('subscriptionPlanId') && userData.subscriptionPlanId != null) {
                var isSubscribed = true;
            } else {
                var isSubscribed = false;
            }
        } else {
            var isSubscribed = '';
        }

        var url = "{{ route('setSubcriptionFlag') }}";
        $.ajax({

            type: 'POST',

            url: url,

            data: {

                email: "{{ Auth::user()->email }}",
                isSubscribed: isSubscribed
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function(data) {
                if (data.access) {

                }
            }

        })
    });

    async function getSectionServiceType(section_id) {

        var sectionsRef = database.collection('sections').where('id', '==', section_id);

        await sectionsRef.get().then(async function(snapshots) {
            var datas = snapshots.docs[0].data();
            service_type = datas.serviceType;
            var enabledDiveInFuture = datas.dine_in_active;
            if (enabledDiveInFuture) {
                dineIn = true;
            }
            var commissionSetting = datas.adminCommision;
            if (commissionSetting.enable == true) {
                commisionModel = true;
            }
        });
        return service_type
    }
</script>
