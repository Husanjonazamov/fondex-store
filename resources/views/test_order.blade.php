@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h3>Test Order Yaratish</h3>
        </div>
    </div>
    <div class="card-body">
        <div id="status" class="alert alert-info">Ma'lumotlar yuklanmoqda...</div>
        <div id="info_box" style="display:none; background:#f5f5f5; padding:12px; border-radius:6px; margin-bottom:15px;">
            <b>Firebase UID (cuser_id):</b> <span id="show_cuser"></span><br>
            <b>Vendor ID:</b> <span id="show_vendor_id"></span><br>
            <b>Section ID:</b> <span id="show_section"></span><br>
            <b>Vendor nomi:</b> <span id="show_vendor_name"></span>
        </div>
        <button id="create_btn" class="btn btn-primary btn-lg" style="display:none">
            <i class="fa fa-plus"></i> &nbsp; Test Order Yaratish (Order Placed)
        </button>
        <div id="result" style="margin-top:20px;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var db = firebase.firestore();

    // section_id va cuser_id layout/menu dan global keladi
    var myUserId  = (typeof cuser_id !== 'undefined') ? cuser_id : '';
    var mySectionId = (typeof section_id !== 'undefined') ? section_id : '';

    $('#show_cuser').text(myUserId || 'topilmadi');
    $('#show_section').text(mySectionId || 'topilmadi');

    if (!myUserId) {
        $('#status').removeClass('alert-info').addClass('alert-danger')
            .text('cuser_id topilmadi. Iltimos, login qiling.');
        return;
    }

    var vendorDocData = null;

    // 1. users dan vendorID ni olish
    db.collection('users').where('id', '==', myUserId).get().then(function(snap) {
        if (snap.empty) {
            $('#status').removeClass('alert-info').addClass('alert-danger')
                .text('Firestore da user topilmadi. cuser_id: ' + myUserId);
            return;
        }

        var userData = snap.docs[0].data();
        var vendorID = userData.vendorID || '';
        $('#show_vendor_id').text(vendorID || 'topilmadi');

        if (!vendorID) {
            $('#status').removeClass('alert-info').addClass('alert-danger')
                .text('User da vendorID yo\'q');
            return;
        }

        // 2. vendors dan vendor ma'lumotini olish
        db.collection('vendors').where('id', '==', vendorID).get().then(function(vSnap) {
            if (vSnap.empty) {
                $('#status').removeClass('alert-info').addClass('alert-danger')
                    .text('Vendor topilmadi. vendorID: ' + vendorID);
                return;
            }

            vendorDocData = vSnap.docs[0].data();
            var vendorSectionId = vendorDocData.section_id || mySectionId || '';

            $('#show_vendor_name').text(vendorDocData.title || 'N/A');
            $('#show_section').text(vendorSectionId || 'topilmadi');
            $('#info_box').show();
            $('#status').removeClass('alert-info').addClass('alert-success')
                .text('Vendor topildi! Tugmani bosib test order yarating.');
            $('#create_btn').show().data('section_id', vendorSectionId);

        }).catch(function(e) {
            $('#status').removeClass('alert-info').addClass('alert-danger').text('Vendor xatosi: ' + e.message);
        });

    }).catch(function(e) {
        $('#status').removeClass('alert-info').addClass('alert-danger').text('User xatosi: ' + e.message);
    });

    // Test order yaratish
    $('#create_btn').click(function() {
        if (!vendorDocData) { alert('Vendor yuklanmagan'); return; }

        var sectionId = $(this).data('section_id') || '';
        var orderId   = db.collection('vendor_orders').doc().id;

        var testOrder = {
            'id'           : orderId,
            'status'       : 'Order Placed',
            'createdAt'    : firebase.firestore.FieldValue.serverTimestamp(),
            'totalPrice'   : 50000,
            'section_id'   : sectionId,
            'vendorID'     : vendorDocData.id || vendorDocData.vendorID || '',
            'vendor'       : vendorDocData,
            'author'       : {
                'id'          : myUserId,
                'firstName'   : 'Test',
                'lastName'    : 'Mijoz',
                'phoneNumber' : '+998901234567',
            },
            'address'      : {
                'name'     : 'Test Mijoz',
                'address'  : 'Test ko\'chasi 1',
                'locality' : 'Toshkent',
                'landmark' : '',
            },
            'products'     : [{
                'id'       : 'test_product_001',
                'name'     : 'Test Mahsulot',
                'price'    : 50000,
                'quantity' : 1,
                'photo'    : '',
                'totalPrice': 50000,
            }],
            'takeAway'        : false,
            'payment_method'  : 'Cash On Delivery',
            'orderNotes'      : 'Bu test order — tasdiqlash uchun',
        };

        $('#create_btn').prop('disabled', true).text('Yaratilmoqda...');

        db.collection('vendor_orders').doc(orderId).set(testOrder).then(function() {
            var editUrl = '{{ url("/orders/edit") }}/' + orderId;
            $('#result').html(
                '<div class="alert alert-success">' +
                '<b>✓ Test order yaratildi!</b><br>' +
                'Order ID: <code>' + orderId + '</code><br>' +
                'Section ID: <code>' + sectionId + '</code><br><br>' +
                '<a href="{{ route("orders") }}" class="btn btn-default"><i class="fa fa-list"></i> Orders list</a> &nbsp; ' +
                '<a href="' + editUrl + '" class="btn btn-success"><i class="fa fa-check"></i> Ko\'rish & Tasdiqlash</a>' +
                '</div>'
            );
            $('#create_btn').prop('disabled', false).text('Yana Test Order Yaratish');
        }).catch(function(e) {
            $('#result').html('<div class="alert alert-danger">Xato: ' + e.message + '</div>');
            $('#create_btn').prop('disabled', false).text('Test Order Yaratish');
        });
    });
});
</script>
@endsection
