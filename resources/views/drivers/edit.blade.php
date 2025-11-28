@extends('layouts.app')

@section('content')
<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-themecolor">{{trans('lang.driver_plural')}}</h3>
		</div>

		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
				<li class="breadcrumb-item"><a href= "{!! route('drivers') !!}" >{{trans('lang.driver_plural')}}</a></li>
				<li class="breadcrumb-item active">{{trans('lang.driver_edit')}}</li>
			</ol>
		</div>
		<div>

			<div class="card-body">
				
				<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>

				<div class="row">

					<div class="col-md-6">

							<div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
	                <input type="text" class="col-9 form-control user_first_name">
	              </div>
	              
	              <div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.email')}}</label>
	                <input type="text" class="col-9 form-control user_email" disabled>
	              </div>

	              <div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
	                <input type="text" class="col-9 form-control user_phone">
	              </div>

	              <div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.active')}}</label>
	                <input type="checkbox" class="col-9 form-check-inline user_active">
	              </div>    

					</div>

					<div class="col-md-6">

							<div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
	                <input type="text" class="col-9 form-control user_last_name">
	            </div>
	            
	            <div class="form-group row">
	                <label class="col-3 control-label">{{trans('lang.role')}}</label>
	                <input type="text" class="col-9 form-control user_role" disabled id="user_role">
	            </div>
	            

	            <div class="form-group row">
	              <label class="col-3 control-label">{{trans('lang.extra_image')}}</label>
	              <input type="file" onChange="handleFileSelect(event)" class="col-9 form-control">
	              <div id="uploding_image"></div>
	            </div>

				</div>
			
			</div>

		</div>	
		

		<div class="form-group col-12 text-right">
			<button type="button" class="btn btn-primary  save_user_btn" ><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
			<a href="{!! route('users') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
		</div>

	</div>

</div>

</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>

<script>

var id = "<?php echo $id;?>";
var database = firebase.firestore();
var ref = database.collection('users').where("id","==",id);

var photo ="";

$(document).ready(function(){
  ref.get().then( async function(snapshots){
var user = snapshots.docs[0].data();

$(".user_first_name").val(user.firstName);

$(".user_last_name").val(user.lastName);
$(".user_email").val(user.email);
$(".user_role").val(user.role);

$('.'+user.role).prop('selected',true);
$(".user_phone").val(user.phoneNumber);
photo = user.profilePictureURL;
if(user.isActive){
  $(".user_active").prop('checked',true);
}

if(user.role == "vendor"){

   await database.collection('vendors').where("author","==",user.id).get().then( async function(snapshots){
  if(snapshots.docs.length >0){

    
        var data = snapshots.docs[0].data();

        $("#user_vendor_name").append('<label class="col-3 control-label">{{trans('lang.vendor')}}</label><input type="text" class="col-9 form-control vendor_vendor" value="'+data.title+'">');
          

  }else{

    $("#user_vendor_name").append('<label class="col-3 control-label">{{trans('lang.vendor')}}</label><input type="text" class="col-9 form-control vendor_vendor" >');
  }


}); 
    
      

}

  jQuery("#data-table_processing").hide();
    
  })


  
$(".save_user_btn").click(function(){

 var userFirstName = $(".user_first_name").val();
 var userLastName = $(".user_last_name").val();
 var email = $(".user_email").val();
 var userPhone = $(".user_phone").val();
 var active = $(".user_active").is(":checked");
 var role = $("#user_role").val();
 var user_name = userFirstName+" "+userLastName;

 	var vendorStoreSelect = $("#vendor_vendor_select option:selected").val();
 			alert("This is for demo, We can't allow to edit");

 		 if(vendorStoreSelect != undefined && role == "vendor"){
       database.collection('users').doc(id).update({'firstName':userFirstName,'lastName':userLastName,'email':email,'phoneNumber':userPhone,'isActive':active,'profilePictureURL':photo,'vendorID':vendorStoreSelect,'role':role}).then(function(result) {
                
             });

               database.collection('vendors').doc(vendorStoreSelect).update({'author':id,'authorProfilePic':photo,'authorName':user_name}).then(function(result) {
                    window.location.href = '{{ route("drivers")}}';         
             });



    }else{

       database.collection('users').doc(id).update({'firstName':userFirstName,'lastName':userLastName,'email':email,'phoneNumber':userPhone,'isActive':active,'profilePictureURL':photo,'role':role}).then(function(result) {
                
                window.location.href = '{{ route("drivers")}}';

             }); 

    } 
    
})

  $( "#user_role" ).change(function() {
    $("#user_vendor_set_option").hide();
    var user_role = $("#user_role option:selected").val();

    if(user_role == "vendor"){
      $("#user_vendor_set_option").show();
       database.collection('vendors').get().then( async function(snapshots){
  
          snapshots.docs.forEach((listval) => {
              var data = listval.data();

                
                $('#vendor_vendor_select').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
                
 

          })

      }); 
  }

}); 


})
var storageRef = firebase.storage().ref('images');
function handleFileSelect(evt) {
  var f = evt.target.files[0];
  var reader = new FileReader();

  reader.onload = (function(theFile) {
    return function(e) {
        
      var filePayload = e.target.result;
      // Generate a location that can't be guessed using the file's contents and a random number
      var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
    
        var val =f.name;       
      var ext=val.split('.')[1];
      var docName=val.split('fakepath')[1];
      var filename = (f.name).replace(/C:\\fakepath\\/i, '')

      var timestamp = Number(new Date());      
      var uploadTask = storageRef.child(filename).put(theFile);
      uploadTask.on('state_changed', function(snapshot){
      // Observe state change events such as progress, pause, and resume
      // Get task progress, including the number of bytes uploaded and the total number of bytes to be uploaded
      var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
    
      jQuery("#uploding_image").text("Image is uploading...");
     
    }, function(error) {
      // Handle unsuccessful uploads
    }, function() {
        uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
            jQuery("#uploding_image").text("Upload is completed");
            photo = downloadURL;

      });   
    });
    
    };
  })(f);
  reader.readAsDataURL(f);
}   


</script>
@endsection