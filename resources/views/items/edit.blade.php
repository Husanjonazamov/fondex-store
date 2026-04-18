@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">

            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.item_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('items') !!}">{{ trans('lang.item_plural') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.item_edit') }}</li>
                </ol>
            </div>
            <div>

                <div class="card-body">
                    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}
                    </div>
                    <div class="error_top" style="display:none"></div>
                    <div class="row vendor_payout_create">
                        <div class="vendor_payout_create-inner">

                            <fieldset>

                                <legend>{{ trans('lang.item_information') }}</legend>

                                <div class="form-group row width-100" id="admin_commision_info">
                                    <div class="m-3">
                                        <div class="form-text font-weight-bold text-danger h6">
                                            {{ trans('lang.price_instruction') }}</div>
                                        <div class="form-text font-weight-bold text-danger h6" id="admin_commision"></div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.item_name') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control item_name" required>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_name_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.item_price') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_price" required>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_price_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.item_discount') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_discount">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_discount_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.item_category_id') }}</label>
                                    <div class="col-7">
                                        <select id='item_category' class="form-control" required>
                                            <option value="">{{ trans('lang.select_category') }}</option>
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_category_id_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check width-50 mb-3" id="is_digital_div" style="display: none;">
                                    <input type="checkbox" class="is_digital_product" id="is_digital_product">
                                    <label class="col-3 control-label" for="item_publish">{{ trans('lang.item_is_digital') }}</label>
                                </div>

                                <div class="form-group width-50" id="upload_file_div" style="display: none;">
                                    <label class="col-3 control-label">{{ trans('lang.item_upload_file') }}</label>
                                    <div class="col-7">
                                        <input type="file" onChange="handleZipUpload(event)" id="digital_product_file">
                                        <div id="uploding_zip" class="placeholder_img_thumb"></div>
                                        <div class="form-text text-muted max_file_size"></div>
                                        <div class="form-text text-muted">{{ trans('lang.item_upload_file_ext') }}</div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.item_quantity') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_quantity">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_quantity_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50 brandDiv" style="display: none;">
                                    <label class="col-3 control-label">{{ trans('lang.brand') }}</label>
                                    <div class="col-7">
                                        <select id='brand' class="form-control" required>
                                            <option value="">{{ trans('lang.select_brand') }}</option>
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.brand_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-100" id="attributes_div">
                                    <label class="col-3 control-label">{{ trans('lang.item_attribute_id') }}</label>
                                    <div class="col-7">
                                        <select id='item_attribute' class="form-control chosen-select" required multiple="multiple" style="display: none;"></select>
                                    </div>
                                </div>

                                <div class="form-group row width-100" id="attributes_div_values">
                                    <div class="item_attributes" id="item_attributes"></div>
                                    <div class="item_variants" id="item_variants"></div>
                                    <input type="hidden" id="attributes" value="" />
                                    <input type="hidden" id="variants" value="" />
                                </div>

                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.item_image') }}</label>
                                    <div class="col-7">
                                        <input type="file" id="product_image" onChange="handleFileSelectProduct(event)">
                                        <div class="placeholder_img_thumb product_image"></div>
                                        <div id="uploding_image"></div>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.item_image_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.item_description') }}</label>
                                    <div class="col-7">
                                        <textarea rows="8" class="form-control item_description" id="item_description"></textarea>
                                    </div>
                                </div>
                                <div class="form-check width-100">
                                    <input type="checkbox" class="item_publish" id="item_publish">
                                    <label class="col-3 control-label" for="item_publish">{{ trans('lang.item_publish') }}</label>
                                </div>

                                <div class="form-check width-100 food_delivery_div d-none">
                                    <input type="checkbox" class="item_nonveg" id="item_nonveg">
                                    <label class="col-3 control-label" for="item_nonveg">{{ trans('lang.non_veg') }}</label>
                                </div>

                                <div class="form-check width-100 food_delivery_take_away d-none">
                                    <input type="checkbox" class="item_take_away_option" id="item_take_away_option">
                                    <label class="col-3 control-label" for="item_take_away_option">{{ trans('lang.item_take_away') }}</label>
                                </div>

                            </fieldset>

                            <fieldset class="food_delivery_div d-none">

                                <legend>{{ trans('lang.ingredients') }}</legend>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.calories') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_calories">
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.grams') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_grams">
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.fats') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_fats">
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.proteins') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control item_proteins">
                                    </div>
                                </div>

                            </fieldset>

                            <fieldset>
                                <legend>{{ trans('lang.item_add_one') }}</legend>

                                <div class="form-group add_ons_list extra-row">
                                </div>

                                <div class="form-group row width-100">
                                    <div class="col-7">
                                        <button type="button" onclick="addOneFunction()" class="btn btn-primary" id="add_one_btn">{{ trans('lang.item_add_one') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group row width-100" id="add_ones_div" style="display:none">

                                    <div class="row">
                                        <div class="col-6">
                                            <label class="col-3 control-label">{{ trans('lang.item_title') }}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control add_ons_title">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="col-3 control-label">{{ trans('lang.item_price') }}</label>
                                            <div class="col-7">
                                                <input type="number" class="form-control add_ons_price">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row save_add_one_btn width-100" style="display:none">
                                    <div class="col-7">
                                        <button type="button" onclick="saveAddOneFunction()" class="btn btn-primary">
                                            {{ trans('lang.save_add_ones') }}
                                        </button>
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>
                                <legend>{{ trans('lang.product_specification') }}</legend>

                                <div class="form-group product_specification extra-row">
                                    <div class="row" id="product_specification_heading" style="display: none;">
                                        <div class="col-6">
                                            <label class="col-2 control-label">{{ trans('lang.lable') }}</label>

                                        </div>
                                        <div class="col-6">
                                            <label class="col-3 control-label">{{ trans('lang.value') }}</label>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-100">
                                    <div class="col-7">
                                        <button type="button" onclick="addProductSpecificationFunction()" class="btn btn-primary" id="add_one_btn">
                                            {{ trans('lang.add_product_specification') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group row width-100" id="add_product_specification_div" style="display:none">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="col-2 control-label">{{ trans('lang.lable') }}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control add_label">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="col-3 control-label">{{ trans('lang.value') }}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control add_value">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row save_product_specification_btn width-100" style="display:none">
                                    <div class="col-7">
                                        <button type="button" onclick="saveProductSpecificationFunction()" class="btn btn-primary">{{ trans('lang.save_product_specification') }}
                                        </button>
                                    </div>
                                </div>

                            </fieldset>
                        </div>
                    </div>

                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  save_item_btn"><i class="fa fa-save"></i>
                        {{ trans('lang.save') }}
                    </button>
                    <a href="{!! route('items') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>

    <script>
        var productId = "<?php echo $id; ?>";
        var database = firebase.firestore();
        var ref = database.collection('vendor_products').where("id", "==", productId);
        var storage = firebase.storage();

        var photos = [];
        var new_added_photos = [];
        var new_added_photos_filename = [];
        var photosToDelete = [];
        var variant_photos = [];
        var variant_filename = [];
        var variantImageToDelete = [];
        var variant_vIds = [];
        var productImagesCount = 0;
        var productImageFile = null;
        var photo = "";
        var addOnesTitle = [];
        var addOnesPrice = [];
        var vendor_categories = '';
        var brand = '';
        var brand_list = '';
        var attributes_list = [];
        var placeholderImage = '';
        var sectionName = '';
        var product_specification = {};
        var section_id;
        var placeholder = database.collection('settings').doc('placeHolderImage');
        var digital_product_file = '';
        var digital_product_file_name = '';
        var digital_product_old_file = '';
        var digital_product_ext = '';
        var section_flag = '';
        var allowed_file_size = '';
        var categories_list = [];
        placeholder.get().then(async function(snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })

        var digitalProductRef = database.collection('settings').doc("digitalProduct");
        digitalProductRef.get().then(async function(snapshots) {
            var digitalProductData = snapshots.data();
            allowed_file_size = digitalProductData.fileSize;
            $(".max_file_size").text('{{ trans('lang.item_upload_file_max') }}' + allowed_file_size + 'Mb');
        })

        $(document).ready(function() {

            jQuery(document).on("click", ".mdi-cloud-upload", function() {
                var variant = jQuery(this).data('variant');
                var fileurl = $('[id="variant_' + variant + '_url"]').val();
                if (fileurl) {
                    variantImageToDelete.push(fileurl);

                }
                var photo_remove = $(this).attr('data-img');
                index = variant_photos.indexOf(photo_remove);
                if (index > -1) {
                    variant_photos.splice(index, 1); // 2nd parameter means remove one item only
                }
                var file_remove = $(this).attr('data-file');
                fileindex = variant_filename.indexOf(file_remove);
                if (fileindex > -1) {
                    variant_filename.splice(fileindex, 1); // 2nd parameter means remove one item only
                }
                variantindex = variant_vIds.indexOf(variant);
                if (variantindex > -1) {
                    variant_vIds.splice(variantindex, 1); // 2nd parameter means remove one item only
                }
                $('[id="variant_' + variant + '_url"]').val('');
                $('[id="file_' + variant + '"]').click();
            });

            jQuery(document).on("click", ".mdi-delete", function() {
                var variant = jQuery(this).data('variant');
                var fileurl = $('[id="variant_' + variant + '_url"]').val();
                if (fileurl) {
                    variantImageToDelete.push(fileurl);


                }

                var photo_remove = $(this).attr('data-img');
                index = variant_photos.indexOf(photo_remove);
                if (index > -1) {
                    variant_photos.splice(index, 1); // 2nd parameter means remove one item only
                }
                var file_remove = $(this).attr('data-file');
                fileindex = variant_filename.indexOf(file_remove);
                if (fileindex > -1) {
                    variant_filename.splice(fileindex, 1); // 2nd parameter means remove one item only
                }
                variantindex = variant_vIds.indexOf(variant);
                if (variantindex > -1) {
                    variant_vIds.splice(variantindex, 1); // 2nd parameter means remove one item only
                }

                $('[id="variant_' + variant + '_image"]').empty();
                $('[id="variant_' + variant + '_url"]').val('');
            });

            jQuery("#data-table_processing").show();

            ref.get().then(async function(snapshots) {
                if (snapshots.empty) {
                    console.error("Product not found in Firestore.");
                    jQuery("#data-table_processing").hide();
                    return;
                }
                var product = snapshots.docs[0].data();

                if (product.hasOwnProperty('product_specification')) {

                    product_specification = product.product_specification;
                    if (product_specification != null && product_specification != "") {
                        product_specification = {};
                        $.each(product.product_specification, function(key, value) {
                            product_specification[key] = value;
                        });
                    }

                    for (var key in product.product_specification) {
                        $('#product_specification_heading').show();
                        $(".product_specification").append('<div class="row" style="margin-top:5px;" id="add_product_specification_iteam_' + key + '">' +
                            '<div class="col-5"><input class="form-control" type="text" value="' + key + '" disabled ></div>' +
                            '<div class="col-5"><input class="form-control" type="text" value="' + product.product_specification[key] + '" disabled ></div>' +
                            '<div class="col-2"><button class="btn" type="button" onclick=deleteProductSpecificationSingle("' + key + '")><span class="fa fa-trash"></span></button></div></div>');
                    }
                }

                if (product.hasOwnProperty('photo')) {

                    photo = product.photo;

                    if (product.photos.length > 0) {
                        photos = product.photos;
                    } else {
                        if (photo != '' && photo != null) {
                            photos.push(photo);
                        }
                    }

                    if (photos.length > 0) {
                        photos.forEach((element, index) => {
                            $(".product_image").append('<span class="image-item" id="photo_' + index + '"><span class="remove-btn" data-id="' + index + '" data-img="' + photos[index] + '" data-status="old"><i class="fa fa-remove"></i></span><img class="rounded" width="50px" id="" height="auto" src="' + photos[index] + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>');
                        })
                    } else if (photo != '' && photo != null) {
                        $(".product_image").append('<span class="image-item" id="photo_1"><img class="rounded" width="50px" id="" height="auto" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>');
                    } else {
                        $(".product_image").append('<span class="image-item" id="photo_1"><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

                    }
                }

                await database.collection('vendors').where('id', "==", product.vendorID).get().then(async function(vendorSnapshots) {
                    if (!vendorSnapshots.empty) {
                        var vendorData = vendorSnapshots.docs[0].data();
                        vendorID = vendorData.id;
                        section_id = vendorData.section_id;

                        if (section_id) {
                            var section = database.collection('sections').where('id', '==', section_id);
                            section.get().then(async function(snapshots) {
                                if (!snapshots.empty) {
                                    var section_data = snapshots.docs[0].data();
                                    section_flag = section_data.serviceTypeFlag;
                                    if (section_data.serviceTypeFlag == "ecommerce-service") {
                                        $('.brandDiv').show();
                                        $("#is_digital_div").show();
                                    }

                                    if (section_data.serviceTypeFlag == "delivery-service") {
                                        $('.food_delivery_take_away').removeClass('d-none');

                                        if (section_data.is_product_details) {
                                            $('.food_delivery_div').removeClass('d-none');
                                        }
                                    }
                                }
                            });
                        } else {
                            $('.brandDiv').hide();
                            $("#brand").val('');
                            $("#is_digital_div").hide();

                        }

                        if (section_id != undefined && section_id != '') {

                            vendor_categories = await database.collection('vendor_categories').where('section_id', '==', section_id);
                            brand = database.collection('brands').where('sectionId', '==', vendorData.section_id).get();
                        } else {

                            vendor_categories = await database.collection('vendor_categories');
                            var brand = database.collection('brands');
                        }

                        vendor_categories = vendor_categories.where('publish', '==', true).get();

                        vendor_categories.then(async function(snapshots) {

                            snapshots.docs.forEach((listval) => {
                                var data = listval.data();
                                categories_list.push(data);
                            });
                            var categoryIDs = []
                            categoryIDs = vendorData.categoryID;
                            categories_list.forEach((val) => {
                                if (categoryIDs.includes(val.id)) {
                                    $('#item_category').append($("<option></option>")
                                        .attr("value", val.id)
                                        .text(val.title));
                                }
                            })
                            $('#item_category').val(product.categoryID);
                        });

                        await brand.then(async function(snapshots) {
                            if (!snapshots.empty) {
                                snapshots.docs.forEach((listval) => {
                                    var data = listval.data();
                                    if (data.id == product.brandID) {
                                        $('#brand').append($("<option selected></option>")
                                            .attr("value", data.id)
                                            .text(data.title));
                                    } else {
                                        $('#brand').append($("<option></option>")
                                            .attr("value", data.id)
                                            .text(data.title));
                                    }
                                });
                                $('#brand').val(product.brandID);
                            }
                        });
                    }
                });

                var selected_attributes = [];
                if (product.item_attribute) {
                    $.each(product.item_attribute.attributes, function(index, attribute) {
                        selected_attributes.push(attribute.attribute_id);
                    });

                    $('#attributes').val(JSON.stringify(product.item_attribute.attributes));
                    $('#variants').val(JSON.stringify(product.item_attribute.variants));
                }

                var attributes = database.collection('vendor_attributes');

                attributes.get().then(async function(snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var data = listval.data();
                        attributes_list.push(data);

                        var selected = '';
                        if ($.inArray(data.id, selected_attributes) !== -1) {
                            var selected = 'selected="selected"';
                        }
                        var option = '<option value="' + data.id + '" ' + selected + '>' + data.title + '</option>'
                        $('#item_attribute').append(option);
                    })
                    $("#item_attribute").show().chosen({
                        "placeholder_text": "{{ trans('lang.select_attribute') }}",
                        "search_contains": true,
                        "disable_search": false
                    });

                    if (product.item_attribute) {
                        $("#item_attribute").attr("onChange", "selectAttribute('" + btoa(JSON.stringify(product.item_attribute)) + "')");
                        selectAttribute(btoa(JSON.stringify(product.item_attribute)));
                    } else {
                        $("#item_attribute").attr("onChange", "selectAttribute()");
                        selectAttribute();
                    }
                });

                if (section_id) {
                    database.collection('sections').doc(section_id).get().then(async function(snapshots) {
                        if (snapshots.exists) {
                            var data = snapshots.data();
                            if (data.serviceTypeFlag == "ecommerce-service" || data.serviceTypeFlag == "delivery-service") {
                                $("#attributes_div").show();
                                $("#item_attribute_chosen").css({
                                    'width': '100%'
                                });
                            } else {
                                $("#attributes_div").remove();
                                $("#attributes_div_values").remove();
                            }
                        }
                    });
                }

                photo = product.photo;
                $(".item_name").val(product.name);
                $(".item_price").val(product.price);
                $(".item_quantity").val(product.quantity);
                $(".item_discount").val(product.disPrice);

                $(".item_featured").val();
                if (product.hasOwnProperty("calories")) {
                    $(".item_calories").val(product.calories)
                }
                if (product.hasOwnProperty("grams")) {
                    $(".item_grams").val(product.grams);
                }
                if (product.hasOwnProperty("proteins")) {
                    $(".item_proteins").val(product.proteins)
                }
                if (product.hasOwnProperty("fats")) {
                    $(".item_fats").val(product.fats);
                }

                $("#item_description").val(product.description);

                if (product.publish) {
                    $(".item_publish").prop('checked', true);
                }

                if (product.nonveg) {

                    $(".item_nonveg").prop('checked', true);
                }

                if (product.takeawayOption) {
                    $(".item_take_away_option").prop('checked', true);
                }

                if (product.hasOwnProperty('addOnsTitle')) {
                    product.addOnsTitle.forEach((element, index) => {
                        $(".add_ons_list").append('<div class="row" style="margin-top:5px;" id="add_ones_list_iteam_' + index + '"><div class="col-5"><input class="form-control" type="text" value="' + element + '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="' + product.addOnsPrice[index] + '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick="deleteAddOnesSingle(' + index +
                            ')"><span class="fa fa-trash"></span></button></div></div>');
                    })
                    addOnesTitle = product.addOnsTitle;
                    addOnesPrice = product.addOnsPrice;
                }

                if (product.hasOwnProperty("isDigitalProduct") && product.hasOwnProperty("digitalProduct")) {
                    if (product.isDigitalProduct) {
                        $("#is_digital_product").prop('checked', true);
                        $("#is_digital_div").show();
                        $("#upload_file_div").show();
                    }
                    if (product.digitalProduct) {
                        var documentType = (product.digitalProduct).split("?")[0];
                        ext = documentType.split(".").pop();
                        if (ext == 'zip') {

                            $("#uploding_zip").html('<span class="image-item zip-file mt-2"><span class="" data-itemid="' + product.id + '" data-file="' + product.digitalProduct + '"></span><a href="' + product.digitalProduct + '" download><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');

                        } else if (ext == 'pdf') {

                            $("#uploding_zip").html('<span class="image-item zip-file mt-2"><span class="" data-itemid="' + product.id + '" data-file="' + product.digitalProduct + '"></i></span><a href="' + product.digitalProduct + '"><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');

                        } else {

                            $("#uploding_zip").html('<span class="image-item zip-file mt-2"><span class="" data-itemid="' + product.id + '" data-file="' + product.digitalProduct + '"></span><img width="100px" height="auto" src="' + product.digitalProduct + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>');

                        }
                        digital_product_file = product.digitalProduct;
                        digital_product_old_file = product.digitalProduct;
                    }
                }

                jQuery("#data-table_processing").hide();

            })


            $(".save_item_btn").click(async function() {
                var name = $(".item_name").val();
                var price = $(".item_price").val();
                var item_quantity = $(".item_quantity").val();
                var discount = $(".item_discount").val();
                var category = $("#item_category option:selected").val();
                var brand = $("#brand option:selected").val();
                var itemCalories = parseInt($(".item_calories").val());
                var itemGrams = parseInt($(".item_grams").val());
                var itemProteins = parseInt($(".item_proteins").val());
                var itemFats = parseInt($(".item_fats").val());
                var description = $("#item_description").val();
                var itemPublish = $(".item_publish").is(":checked");
                var nonveg = $(".item_nonveg").is(":checked");
                var veg = !nonveg;
                var itemTakeaway = $(".item_take_away_option").is(":checked");
                var is_digital_product = $("#is_digital_product").is(":checked");

                if (discount == '') {
                    discount = "0";
                }

                if (!itemCalories) {
                    itemCalories = 0;
                }
                if (!itemGrams) {
                    itemGrams = 0;
                }
                if (!itemFats) {
                    itemFats = 0;
                }
                if (!itemProteins) {
                    itemProteins = 0;
                }

                if (photos != '') {
                    photo = photos[0]
                }


                if (name == '') {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_item_name_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (price == '') {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_item_price_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (price <= 0) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_positive_price_error') }}</p>");
                    window.scrollTo(0, 0);

                } else if (item_quantity == '' || item_quantity < -1) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    if (item_quantity == '') {
                        $(".error_top").append("<p>{{ trans('lang.enter_item_quantity_error') }}</p>");
                    } else {
                        $(".error_top").append("<p>{{ trans('lang.invalid_item_quantity_error') }}</p>");
                    }
                    window.scrollTo(0, 0);
                } else if (category == '') {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_item_category_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (brand == '' && $('.brandDiv').is(':visible') == true) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_brand_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (parseInt(price) < parseInt(discount)) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.price_should_not_less_then_discount_error') }}</p>");
                    window.scrollTo(0, 0);

                } else if (description == '') {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_item_description_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (is_digital_product == true && digital_product_file == '') {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.upload_digital_file_error') }}</p>");
                    window.scrollTo(0, 0);
                } else {

                    //start-item attribute
                    var attributes = [];
                    var variants = [];

                    if ($('#attributes').val().length > 0) {
                        var attributes = $.parseJSON($('#attributes').val());
                    }
                    if ($('#variants').val().length > 0) {
                        var variantsSet = $.parseJSON($('#variants').val());
                        var isValid = false; // Flag to track validation

                        await storeVariantImageData().then(async (vIMG) => {
                            $.each(variantsSet, function(key, variant) {
                                var variant_id = uniqid();
                                var variant_sku = variant;
                                var variant_price = $('#price_' + variant).val();
                                var variant_quantity = $('#qty_' + variant).val();
                                var variant_image = $('#variant_' + variant + '_url').val();
                                // Validation for variant_price
                                if (!variant_price || parseFloat(variant_price) <= 0) {
                                    $(".error_top").show();
                                    $(".error_top").html("");
                                    $(".error_top").append("<p>{{ trans('lang.enter_positive_variant_price_error') }}</p>");
                                    window.scrollTo(0, 0);
                                    isValid = true;
                                    return false; // Exit loop
                                }
                                variants.push({
                                    'variant_id': variant_id,
                                    'variant_sku': variant_sku,
                                    'variant_price': variant_price,
                                    'variant_quantity': variant_quantity,
                                    'variant_image': variant_image
                                });
                            });
                        }).catch(err => {
                            jQuery("#data-table_processing").hide();
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + err + "</p>");
                            window.scrollTo(0, 0);
                        });
                        if (isValid) {
                            return;

                        }
                        $(".error_top").hide().html("");

                    }

                    var item_attribute = null;
                    if (attributes.length > 0 && variants.length > 0) {
                        var item_attribute = {
                            'attributes': attributes,
                            'variants': variants
                        };
                    }
                    //end-item attribute
                    await storeImageData().then(async (IMG) => {
                        await storeDigitalImageData().then(async (DigitalImg) => {

                            if (IMG.length > 0) {
                                photo = IMG[0];
                            }
                            database.collection('vendor_products').doc(productId).update({
                                'name': name,
                                'price': price,
                                'quantity': parseInt(item_quantity),
                                'disPrice': discount,
                                'categoryID': category,
                                'brandID': brand,
                                'photo': photo,
                                'photo_base64': photos.length > 0 ? photos[0] : '',
                                'photos': IMG,
                                'calories': itemCalories,
                                "grams": itemGrams,
                                'proteins': itemProteins,
                                'fats': itemFats,
                                'description': description,
                                'publish': itemPublish,
                                'section_id': section_id,
                                'nonveg': nonveg,
                                'veg': veg,
                                'addOnsTitle': addOnesTitle,
                                'addOnsPrice': addOnesPrice,
                                'takeawayOption': itemTakeaway,
                                'item_attribute': item_attribute,
                                'product_specification': product_specification,
                                'isDigitalProduct': is_digital_product,
                                'digitalProduct': DigitalImg ? DigitalImg : '',
                            }).then(function(result) {
                                var fd = new FormData();
                                fd.append('name', name);
                                fd.append('price', price);
                                fd.append('quantity', parseInt(item_quantity));
                                fd.append('disPrice', discount);
                                fd.append('categoryID', category);
                                fd.append('brandID', brand);
                                fd.append('photo', photo);
                                fd.append('description', description);
                                fd.append('publish', itemPublish);
                                fd.append('section_id', section_id);
                                fd.append('id', productId);
                                fd.append('vendorID', vendorID);
                                if (productImageFile) {
                                    fd.append('image', productImageFile, productImageFile.name);
                                }
                                $.ajax({
                                    url: '{{ route('items.sync') }}',
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: fd,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.success) {
                                            window.location.href = '{{ route('items') }}';
                                        } else {
                                            jQuery("#data-table_processing").hide();
                                            $(".error_top").show().html("<p>" + response.message + "</p>");
                                            window.scrollTo(0, 0);
                                        }
                                    },
                                    error: function(xhr) {
                                        jQuery("#data-table_processing").hide();
                                        var msg = "Error syncing with external API";
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            msg = xhr.responseJSON.message;
                                        }
                                        $(".error_top").show().html("<p>" + msg + "</p>");
                                        window.scrollTo(0, 0);
                                    }
                                });
                            });
                        }).catch(err => {
                            jQuery("#data-table_processing").hide();
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + err + "</p>");
                            window.scrollTo(0, 0);
                        });
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + err + "</p>");
                        window.scrollTo(0, 0);
                    });

                }
            })


        })

        var storageRef = firebase.storage().ref('images');

        window.handleFileSelectProduct = function handleFileSelectProduct(evt) {
            var f = evt.target.files[0];
            productImageFile = f;
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {

                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var fName = val.split('.')[0];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    productImagesCount++;
                    photos_html = '<span class="image-item" id="photo_' + productImagesCount + '"><span class="remove-btn" data-id="' + productImagesCount + '" data-img="' + filePayload + '" data-status="new"><i class="fa fa-remove"></i></span><img class="rounded" width="50px" id="" height="auto" src="' + filePayload + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>'
                    $(".product_image").append(photos_html);
                    new_added_photos.push(filePayload);
                    new_added_photos_filename.push(filename);
                    $("#product_image").val('');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        async function storeImageData() {
            var newPhoto = [];
            if (photos.length > 0) {
                newPhoto = photos;
            }
            if (new_added_photos.length > 0) {
                await Promise.all(new_added_photos.map(async (foodPhoto, index) => {

                    foodPhoto = foodPhoto.replace(/^data:image\/[a-z]+;base64,/, "");
                    var uploadTask = await storageRef.child(new_added_photos_filename[index]).putString(foodPhoto, 'base64', {
                        contentType: 'image/jpg'
                    });
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto.push(downloadURL);
                }));
            }

            if (photosToDelete.length > 0) {
                await Promise.all(photosToDelete.map(async (delImage) => {
                    imageBucket = delImage.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {

                        await delImage.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }

                }));

            }
            return newPhoto;
        }

        $(document).on("click", ".remove-btn", function() {
            var id = $(this).attr('data-id');
            var photo_remove = $(this).attr('data-img');
            var status = $(this).attr('data-status');
            if (status == "old") {

                photosToDelete.push(firebase.storage().refFromURL(photo_remove));
            }

            $("#photo_" + id).remove();
            index = photos.indexOf(photo_remove);
            if (index > -1) {
                photos.splice(index, 1); // 2nd parameter means remove one item only
            }
            index = new_added_photos.indexOf(photo_remove);
            if (index > -1) {
                new_added_photos.splice(index, 1); // 2nd parameter means remove one item only
                new_added_photos_filename.splice(index, 1);
            }


        });

        $(document).on("click", ".delete-btn", function() {
            var id = $(this).attr('data-id');
            var photo_remove = $(this).attr('data-img');
            $("#photo_" + id).remove();
            index = photos.indexOf(photo_remove);
            if (index > -1) {
                photos.splice(index, 1); // 2nd parameter means remove one item only
            }
        });

        jQuery(document).on("click", "#is_digital_product", function() {
            if (jQuery(this).is(':checked') && section_flag == "ecommerce-service") {
                $("#upload_file_div").show();
            } else {
                $("#upload_file_div").hide();
            }
        });

        function handleVariantFileSelect(evt, vid) {
            var f = evt.target.files[0];
            var reader = new FileReader();

            reader.onload = (function(theFile) {
                return function(e) {

                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var timestamp = Number(new Date());
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var filename = 'variant_' + vid + '_' + timestamp + '.' + ext;
                    variant_filename.push(filename);
                    variant_photos.push(filePayload);
                    variant_vIds.push(vid);
                    $('[id="variant_' + vid + '_image"]').empty();
                    $('[id="variant_' + vid + '_image"]').html('<img class="rounded" style="width:50px" src="' + filePayload + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image"><i class="mdi mdi-delete" data-variant="' + vid + '" data-img="' + filePayload + '" data-file="' + filename + '" data-status="new"></i>');
                    $('#upload_' + vid).attr('data-img', filePayload);
                    $('#upload_' + vid).attr('data-file', filename);
                };
            })(f);
            reader.readAsDataURL(f);
        }

        async function storeVariantImageData() {
            var newPhoto = [];

            if (variant_photos.length > 0) {
                await Promise.all(variant_photos.map(async (variantPhoto, index) => {
                    variantPhoto = variantPhoto.replace(/^data:image\/[a-z]+;base64,/, "");
                    var uploadTask = await storageRef.child(variant_filename[index]).putString(variantPhoto, 'base64', {
                        contentType: 'image/jpg'
                    });
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    $('[id="variant_' + variant_vIds[index] + '_url"]').val(downloadURL);
                    newPhoto.push(downloadURL);
                }));
            }
            if (variantImageToDelete.length > 0) {
                await Promise.all(variantImageToDelete.map(async (delImage) => {
                    var delImageUrlRef = await storage.refFromURL(delImage);
                    imageBucket = delImageUrlRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                        await delImageUrlRef.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }

                }));
            }
            return newPhoto;
        }

        function handleZipUpload(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();

            reader.onload = (function(theFile) {
                return function(e) {

                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var size = f.size;

                    var max_file_size = parseInt(allowed_file_size) * 1000000;
                    if (size > max_file_size) {
                        $("#digital_product_file").val('');
                        alert('{{ trans('lang.max_file_limit_error') }}' + allowed_file_size + 'Mb');
                        return false;
                    }

                    if (ext == "jpg" || ext == "jpeg" || ext == "png" || ext == "gif" || ext == "zip" || ext == "pdf") {

                        var docName = val.split('fakepath')[1];
                        var filename = (f.name).replace(/C:\\fakepath\\/i, '')

                        var timestamp = Number(new Date());
                        var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                        digital_product_file = filePayload;
                        digital_product_file_name = filename;
                        if (ext == "zip") {
                            digital_product_ext = 'zip';
                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""   data-file="' + filePayload + '"></span><a href="' + filePayload + '" download><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');
                        } else if (ext == 'pdf') {
                            digital_product_ext = 'pdf';
                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""   data-file="' + filePayload + '"></span><a href="' + filePayload + '" target="_blank"><i class="fa fa-file-text" style="font-size:45px"></i></a></span>');
                        } else {
                            digital_product_ext = 'image';
                            $("#uploding_zip").html('<span class="image-item zip-file"><span class=""  data-file="' + filePayload + '"></span><img width="100px" id="" height="auto" src="' + filePayload + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>');
                        }

                        $("#digital_product_file").val('');

                    } else {
                        $("#digital_product_file").val('');
                        alert('{{ trans('lang.enter_valid_file_ext') }}')
                        return false;
                    }

                };
            })(f);
            reader.readAsDataURL(f);
        }

        async function storeDigitalImageData() {
            var newPhoto = '';
            try {
                if (digital_product_file != '') {
                    if (digital_product_old_file != "" && digital_product_file != digital_product_old_file) {
                        var oldImageUrlRef = await storage.refFromURL(digital_product_old_file);
                        imageBucket = oldImageUrlRef.bucket;
                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";

                        if (imageBucket == envBucket) {
                            await oldImageUrlRef.delete().then(() => {
                                console.log("Old file deleted!")
                            }).catch((error) => {
                                console.log("ERR File delete ===", error);
                            });
                        } else {
                            console.log('Bucket not matched');
                        }
                    }

                    if (digital_product_file != digital_product_old_file) {

                        digital_product_file = digital_product_file.replace(/^data:image\/[a-z]+;base64,/, "");
                        if (digital_product_ext == 'zip' || digital_product_ext == "pdf") {
                            var uploadTask = await storageRef.child(digital_product_file_name).put(digital_product_file);
                        } else {
                            var uploadTask = await storageRef.child(digital_product_file_name).putString(digital_product_file, 'base64', {
                                contentType: 'image/jpg'
                            });
                        }
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        newPhoto = downloadURL;
                        digital_product_file = downloadURL;
                    }
                }
            } catch (error) {
                console.log("ERR ===", error);
            }

            return newPhoto;
        }

        function addOneFunction() {
            $("#add_ones_div").show();
            $(".save_add_one_btn").show();
        }

        function addProductSpecificationFunction() {
            $("#add_product_specification_div").show();
            $(".save_product_specification_btn").show();
        }

        function saveProductSpecificationFunction() {
            var optionlabel = $(".add_label").val();
            var optionvalue = $(".add_value").val();
            $(".add_label").val('');
            $(".add_value").val('');
            if (optionlabel != '' && optionlabel != '') {

                product_specification[optionlabel] = optionvalue;
                $(".product_specification").append('<div class="row" style="margin-top:5px;" id="add_product_specification_iteam_' + optionlabel + '"><div class="col-5"><input class="form-control" type="text" value="' + optionlabel + '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="' + optionvalue + '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick=deleteProductSpecificationSingle("' + optionlabel +
                    '")><span class="fa fa-trash"></span></button></div></div>');
            } else {
                alert("Please enter Label and Value");
            }
        }

        function deleteProductSpecificationSingle(index) {

            delete product_specification[index];
            $("#add_product_specification_iteam_" + index).hide();
        }

        function saveAddOneFunction() {
            var optiontitle = $(".add_ons_title").val();
            var optionPricevalue = $(".add_ons_price").val();
            var optionPrice = $(".add_ons_price").val();
            $(".add_ons_price").val('');
            $(".add_ons_title").val('');
            if (optiontitle != '' && optionPricevalue != '') {
                addOnesPrice.push(optionPrice);
                addOnesTitle.push(optiontitle);
                var index = addOnesTitle.length - 1;
                $(".add_ons_list").append('<div class="row" style="margin-top:5px;" id="add_ones_list_iteam_' + index + '"><div class="col-5"><input class="form-control" type="text" value="' + optiontitle + '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="' + optionPrice + '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick="deleteAddOnesSingle(' + index + ')"><span class="fa fa-trash"></span></button></div></div>');
            } else {
                alert("Please enter Title and Price");
            }
        }

        function deleteAddOnesSingle(index) {
            addOnesTitle.splice(index, 1);
            addOnesPrice.splice(index, 1);
            $("#add_ones_list_iteam_" + index).hide();
        }

        function selectAttribute(item_attribute = '') {

            if (item_attribute) {
                var item_attribute = $.parseJSON(atob(item_attribute));
            }

            var html = '';
            $("#item_attribute").find('option:selected').each(function() {
                var $this = $(this);
                var selected_options = [];
                if (item_attribute) {
                    $.each(item_attribute.attributes, function(index, attribute) {
                        if ($this.val() == attribute.attribute_id) {
                            selected_options.push(attribute.attribute_options);
                        }
                    });
                }
                html += '<div class="row" id="attr_' + $this.val() + '">';
                html += '<div class="col-md-3">';
                html += '<label>' + $this.text() + '</label>';
                html += '</div>';
                html += '<div class="col-lg-9">';
                html += '<input type="text" class="form-control" id="attribute_options_' + $this.val() + '" value="' + selected_options + '" placeholder="Add attribute values" data-role="tagsinput" onchange="variants_update(\'' + btoa(JSON.stringify(item_attribute)) + '\')">';
                html += '</div>';
                html += '</div>';
            });
            $("#item_attributes").html(html);
            $("#item_attributes input[data-role=tagsinput]").tagsinput();

            if ($("#item_attribute").val().length == 0) {
                $("#attributes").val('');
                $("#variants").val('');
                $("#item_variants").html('');
                return;
            }

            // Explicitly call variants_update to ensure prices/quantities load from saved data
            if (item_attribute) {
                console.log('selectAttribute item_attribute:', JSON.stringify(item_attribute));
                variants_update(btoa(JSON.stringify(item_attribute)));
            } else {
                console.log('selectAttribute: no item_attribute, calling without data');
                variants_update();
            }
        }

        function variants_update(item_attributeX = '') {

            if (item_attributeX) {
                var item_attributeX = $.parseJSON(atob(item_attributeX));
                console.log('variants_update item_attributeX:', JSON.stringify(item_attributeX));
            } else {
                console.log('variants_update: no item_attributeX');
            }

            var html = '';
            var item_attribute = $("#item_attribute").map(function(idx, ele) {
                return $(ele).val();
            }).get();

            if (item_attribute.length > 0) {

                var attributes = [];
                var attributeSet = [];
                $.each(item_attribute, function(index, attribute) {
                    var attribute_options = $("#attribute_options_" + attribute).val();
                    if (attribute_options) {
                        var attribute_options = attribute_options.split(',');
                        attribute_options = $.map(attribute_options, function(value) {
                            return value.replace(/[^a-zA-Z0-9]/g, '');
                        });
                        attributeSet.push(attribute_options);
                        attributes.push({
                            'attribute_id': attribute,
                            'attribute_options': attribute_options
                        });
                    }
                });

                if (attributeSet.length > 0) {

                    $('#attributes').val(JSON.stringify(attributes));

                    var variants = getCombinations(attributeSet);
                    $('#variants').val(JSON.stringify(variants));

                    html += '<div class="mb-2"><input type="text" id="variant_search" class="form-control" placeholder="Variantlarni qidirish..." oninput="filterVariants(this.value)"></div>';
                    html += '<table class="table table-bordered" id="variants_table">';
                    html += '<thead class="thead-light">';
                    html += '<tr>';
                    html += '<th class="text-center"><span class="control-label">Variant</span></th>';
                    html += '<th class="text-center"><span class="control-label">Variant Price</span></th>';
                    html += '<th class="text-center"><span class="control-label">Variant Quantity</span></th>';
                    html += '<th class="text-center"><span class="control-label">Variant Image</span></th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody id="variants_tbody">';
                    $.each(variants, function(index, variant) {

                        var variant_price = 1;
                        var variant_qty = 1;
                        var variant_image = variant_image_url = '';
                        if (item_attributeX) {
                            var variant_info = $.map(item_attributeX.variants, function(v, i) {
                                var normalizedSku = v.variant_sku.replace(/[^a-zA-Z0-9]/g, '');
                                if (normalizedSku == variant || v.variant_sku == variant) {
                                    return v;
                                }
                            });
                            if (variant_info[0]) {
                                variant_price = variant_info[0].variant_price;
                                variant_qty = variant_info[0].variant_quantity;
                                if (variant_info[0].variant_image) {
                                    variant_image = '<img class="rounded" style="width:50px" src="' + variant_info[0].variant_image + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"><i class="mdi mdi-delete" data-variant="' + variant + '"></i>';
                                    variant_image_url = variant_info[0].variant_image;
                                }
                            }
                        }

                        html += '<tr>';
                        html += '<td><label for="" class="control-label">' + variant + '</label></td>';
                        html += '<td>';
                        html += '<input type="number" id="price_' + variant + '" value="' + variant_price + '" min="0" class="form-control">';
                        html += '</td>';
                        html += '<td>';
                        html += '<input type="number" id="qty_' + variant + '" value="' + variant_qty + '" min="-1" class="form-control">';
                        html += '</td>';
                        html += '<td>';
                        html += '<div class="variant-image">';
                        html += '<div class="upload">';
                        html += '<div class="image" id="variant_' + variant + '_image">' + variant_image + '</div>';
                        html += '<div class="icon"><i class="mdi mdi-cloud-upload" data-variant="' + variant + '"></i></div>';
                        html += '</div>';
                        html += '<div id="variant_' + variant + '_process"></div>';
                        html += '<div class="input-file">';
                        html += '<input type="file" id="file_' + variant + '" onChange="handleVariantFileSelect(event,\'' + variant + '\')" class="form-control" style="display:none;">';
                        html += '<input type="hidden" id="variant_' + variant + '_url" value="' + variant_image_url + '">';
                        html += '</div>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    html += '</tbody>';
                    html += '</table>';
                }
            }
            $("#item_variants").html(html);
        }

        function getCombinations(arr) {
            if (arr.length) {
                if (arr.length == 1) {
                    return arr[0];
                } else {
                    var result = [];
                    var allCasesOfRest = getCombinations(arr.slice(1));
                    for (var i = 0; i < allCasesOfRest.length; i++) {
                        for (var j = 0; j < arr[0].length; j++) {
                            result.push(arr[0][j] + '-' + allCasesOfRest[i]);
                        }
                    }
                    return result;
                }
            }
        }


        function uniqid(prefix = "", random = false) {
            const sec = Date.now() * 1000 + Math.random() * 1000;
            const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
            return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}` : ""}`;
        }
        // Search/filter variants table
        window.filterVariants = function(query) {
            query = query.toLowerCase();
            $('#variants_tbody tr').each(function() {
                var label = $(this).find('td:first label').text().toLowerCase();
                $(this).toggle(label.indexOf(query) !== -1);
            });
        };

        // Clear error message when user updates the price field
        $(document).on('input', '[id^="price_"]', function() {
            if (parseFloat($(this).val()) > 0) {
                $(".error_top").hide().html("");
            }
        });
    </script>
@endsection
