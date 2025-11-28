@extends('layouts.app')

@section('content')
        <div class="page-wrapper">

            <div class="row page-titles">

                <div class="col-md-5 align-self-center">

                    <h3 class="text-themecolor">{{trans('lang.order_review')}}</h3>

                </div>

                <div class="col-md-7 align-self-center">

                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                        <li class="breadcrumb-item active">{{trans('lang.order_review_table')}}</li>

                    </ol>

                </div>

                <div>

                </div>

            </div>
      

            <div class="container-fluid">
                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-body">

                                

                                <div class="table-responsive m-t-10">

                                    <table id="example24" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                        <thead>

                                            <tr>

                                                <th>{{trans('lang.order_id')}}</th>

                                                <th class="address-list">{{ trans('lang.order_review')}}</th>
                                                
                                                <th>{{ trans('lang.item_review_rate')}}</th>
                                                
                                                <th>{{ trans('lang.item_review_user_id')}}</th>

                                                <th>{{trans('lang.actions')}}</th>

                                            </tr>

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
    </div>



@endsection

@section('scripts')

<script type="text/javascript">
 
 var database = firebase.firestore();

    var offest=1;
    var pagesize=10; 
    var end = null;
    var endarray=[];
    var start = null;
    var user_number = [];
    var append_list = '';
    var vendorUserId = "<?php echo $id; ?>";
    var ref = '';

    getVendorId(vendorUserId).then(data => {
        vendorId= data;
        ref = database.collection('items_review').where('VendorId','==',vendorId);
        $(document).ready(function() {

            $(document.body).on('click', '.redirecttopage' ,function(){    
                var url=$(this).attr('data-url');
                window.location.href = url;
            }); 

            jQuery("#data-table_processing").show();  
            append_list = document.getElementById('append_list1');
            append_list.innerHTML='';
            ref.get().then( async function(snapshots){  
            html=''; 
            html=await buildHTML(snapshots);
            jQuery("#data-table_processing").hide();
            if(html!=''){
                append_list.innerHTML=html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
                if(snapshots.docs.length<pagesize){
                    jQuery("#data-table_paginate").hide();
                }
             }
            $('#example24').DataTable({
                    order: [],
                    columnDefs: [
                        {orderable: false, targets: [4]},
                    ],
                    order: [['0', 'desc']],

                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                    responsive: true
                });

          }); 
         
        });
    })
        async function buildHTML(snapshots) {
            var html='';
            await Promise.all(snapshots.docs.map(async (listval) => {
                var val = listval.data();
                var getData = await getListData(val);
                html += getData;
            }));
            return html;
        }

async function getListData(val) {
                html='';
                html=html+'<tr>';
                newdate='';
                var reviewId = val.Id;
                var route1 =  '{{route("orderReview.edit",":id")}}';
                route1 = route1.replace(':id', reviewId);

                var orderRoute =  '{{route("orders.edit",":id")}}';
                orderRoute = orderRoute.replace(':id', val.orderid);
                html=html+'<td data-url="'+orderRoute+'" class="redirecttopage">'+val.orderid+'</td>';
                html=html+'<td class="address-list">'+val.comment+'</td>';
                html=html + '<td><ul class="rating" data-rating="'+val.rating+'"><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li></ul></td>';


                const user_name=userName(val.CustomerId);
                html=html+'<td class="name_'+val.CustomerId+'">'+user_name+'</td>';
                html=html+'<td class="action-btn"><a href="'+route1+'"><i class="fa fa-edit"></i></a><a id="'+val.Id+'" name="item-review-delete" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>';
                
                html=html+'</tr>';
                return html;      

}

function prev(){
    if(endarray.length==1){
        return false;
    }
    end=endarray[endarray.length-2];
  
  if(end!=undefined || end!=null){
            jQuery("#data-table_processing").show();
                 if(jQuery("#selected_search").val()=='order_id' && jQuery("#search").val().trim()!=''){

                  listener=ref.orderBy('Id').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();

              }else{
                    listener = ref.startAt(end).limit(pagesize).get();
                }
                
                listener.then((snapshots) => {
                html='';
                html=buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if(html!=''){
                    append_list.innerHTML=html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length-1]),1);

                    if(snapshots.docs.length < pagesize){ 
   
                        jQuery("#users_table_previous_btn").hide();
                    }
                    
                }
            });
  }
}

function next(){
  if(start!=undefined || start!=null){

        jQuery("#data-table_processing").hide();

          if(jQuery("#selected_search").val()=='order_id' && jQuery("#search").val().trim()!=''){

              listener=ref.orderBy('Id').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAfter(start).get();

              }else{
                listener = ref.startAfter(start).limit(pagesize).get();
            }
          listener.then((snapshots) => {
            
                html='';
                html=buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if(html!=''){
                    append_list.innerHTML=html;
                    start = snapshots.docs[snapshots.docs.length - 1];

                    if(endarray.indexOf(snapshots.docs[0])!=-1){
                        endarray.splice(endarray.indexOf(snapshots.docs[0]),1);
                    }
                    endarray.push(snapshots.docs[0]);
                }
            });
    }
}

function searchclear(){
    jQuery("#search").val('');
    searchtext();
}


function searchtext(){


  jQuery("#data-table_processing").show();
  
  append_list.innerHTML='';  

   if(jQuery("#selected_search").val()=='order_id' && jQuery("#search").val().trim()!=''){

     wherequery=ref.orderBy('orderid').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

   }else{

    wherequery=ref.limit(pagesize).get();
   }

    

  
  wherequery.then((snapshots) => {
    html='';
    html=buildHTML(snapshots);
    jQuery("#data-table_processing").hide();
    if(html!=''){
        append_list.innerHTML=html;
        start = snapshots.docs[snapshots.docs.length - 1];
        endarray.push(snapshots.docs[0]);
        if(snapshots.docs.length < pagesize){ 
   
            jQuery("#data-table_paginate").hide();
        }else{

            jQuery("#data-table_paginate").show();
        }
    }
}); 

}

async function userName(userID) {
var userName='';
await database.collection('users').where("id","==",userID).get().then( async function(snapshotss){
  
            if(snapshotss.docs[0]){
                var user = snapshotss.docs[0].data();
                userName = user.firstName+" "+user.lastName;
                
            }else{

            } 
});
return userName;
} 


async function vendorName(vendorID) {
var vendorName ='';
await database.collection('vendors').where("id","==",vendorID).get().then( async function(snapshotss){
  
            if(snapshotss.docs[0]){
                var vendor = snapshotss.docs[0].data();
                vendorName = vendor.title;

                jQuery(".item_"+vendorID).html(vendorName);
                
            }else{
                jQuery(".item_"+vendorID).html('');

            } 
});
return vendorName;
} 

$(document).on("click","a[name='item-review-delete']", function (e) {
    var id = this.id;
    alert("This is for demo, We can't allow to delete");
   


});

async function getVendorId(vendorUser){
    var vendorId = '';
    var ref;
    await database.collection('vendors').where('author',"==",vendorUser).get().then(async function(vendorSnapshots){
        var vendorData = vendorSnapshots.docs[0].data();    
        vendorId = vendorData.id;
    })
    
            return vendorId;
}


</script>

@endsection
