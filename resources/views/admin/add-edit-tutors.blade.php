@extends('admin.layouts.master')
@if($rPath == 'edit')
    @section('title', 'Update Tutor')
@else
    @section('title', 'Add Tutor')
@endif
@section('content')
<style>
.form-horizontal .control-label {
  text-align: right;
  margin-bottom: 0;
  padding-top: 7px;
}
.form-horizontal .form-group {
    margin-left: -15px;
    margin-right: -15px;
}
.form-group {
    margin-bottom: 15px;
    display: flex;
  }
.form-horizontal .form-group:after, .form-horizontal .form-group:before {
    content: " ";
    display: table;
}
.col-md-6 {
    width: 50%;
    /* float: right; */
}
.card {
    background-color: #fff;
    border: 1px solid #f2f5f8;
    border-radius: 4px;
    -webkit-box-shadow: none;
    box-shadow: none;
    display: block;
    margin-bottom: 10px;
    position: relative;
}
.card-body {
    padding: 15px;
    position: relative;
}
.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    position: relative;
    min-height: 1px;
    padding-left: 15px;
    padding-right: 15px;
}
label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
    color: black !important;
}
.custom-control {
    cursor: pointer;
    font-weight: 400;
    line-height: 14px;
    margin-bottom: 0;
    min-height: 14px;
    min-width: 14px;
    position: relative;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    vertical-align: middle;
}
.radio-div {
  display: flex;
}
</style>
  <div class="wrapper">
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
            <div class="collapse navbar-collapse justify-content-end" id="navigation">

            <ul class="navbar-nav">

              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{Session::get('sct_admin')->first_name}}
                  <p>
                    <span class="d-lg-none d-md-block">Some Actions</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="{{ url('dashboard/logout') }}">Logout</a>
                </div>
              </li>

            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <!-- <div class="panel-header panel-header-sm">


</div> -->
      <div class="content">
        <div class="row">
          <div class="col-md-12">
              @include('admin.includes.alerts')
              <div class="card">
                  <div class="card-body">

                    @if($rPath == 'edit')
                    <?php $profilePhoto=''; ?>
                    @if($tutor->image != '')
                    <?php
                    $profilePhoto = url('frontend-assets/images/dashboard/profile-photos/'.$tutor->image);
                     ?>
                  @endif

          <form class="form-horizontal employers-form" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
              <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
              <input type="hidden" name="prevLogo" value="{{ $tutor->image }}">

              <div class="form-group">
                  <label class="control-label col-md-3 text-right">&nbsp;</label>
                  <div class="col-md-6">
                      Required fields are marked *
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Name : *</label>
                  <div class="col-md-6">
                      <input type="text" class="form-control" name="first_name" required="" value="{{ $tutor->first_name }}">
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Email : *</label>
                  <div class="col-md-6">
                      <input type="email" class="form-control" name="email" required="" value="{{ $tutor->email }}">
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Phone : *</label>
                  <div class="col-md-6">
                      <input type="text" class="form-control" name="phone" required="" value="{{ $tutor->phone }}">
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Blurb : *</label>
                  <div class="col-md-6">
                    <textarea name="description" class="form-control" placeholder="Blurb">{{$tutor->description}}</textarea>
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Time Zone : *</label>
                  <div class="col-md-6">
                    <select class="form-control border-input" name="time_zone" id="time_zone">
                      <option value="Pacific Time" {{$tutor->time_zone == 'Pacific Time' ? 'selected=="selected"':''}}>Pacific Time</option>
                      <option value="Mountain Time" {{$tutor->time_zone == 'Mountain Time' ? 'selected=="selected"':''}}>Mountain Time</option>
                      <option value="Central Time" {{$tutor->time_zone == 'Central Time' ? 'selected=="selected"':''}}>Central Time</option>
                      <option value="Eastern Time" {{$tutor->time_zone == 'Eastern Time' ? 'selected=="selected"':''}}>Eastern Time</option>
                    </select>
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">Profile Photo : *</label>
                  <div class="col-md-6">
                      <div class="input-group input-group-file">
                          <input class="form-control p-image" readonly="" type="text">
                          <span class="input-group-btn upload-img-btn">
                              <label class="btn btn-primary file-upload-btn">
                                  <input name="profilePhoto" class="file-upload-input" type="file" onchange="getFileName(this,'p-image')">
                                  <span class="nc-icon nc-album-2" style="color:white;"></span>
                              </label>
                          </span>
                      </div>
                  </div>
              </div>
              @if($profilePhoto !='')
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">&nbsp;</label>
                  <div class="col-md-6">
                      <span style="background-color: #f8f8f8;padding: 10px;text-align: center;display: block;">
                          <img src="{{ $profilePhoto }}" alt="{{ $tutor['user_firstname'].' '.$tutor['user_lastname'] }}" style="max-width: 200px;">
                      </span>
                  </div>
              </div>
              @endif
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">password : *</label>
                  <div class="col-md-6">
                      <input type="password" class="form-control" name="password" placeholder="Enter Password">
                  </div>
              </div>
              <div class="form-group radio-div">
                  <label class="control-label col-md-3 text-right">Role :</label>
                  <div class="col-md-6 radio-div">
                      <label class="custom-control custom-control-primary custom-radio">
                          <input name="role" class="custom-control-input" type="radio" value="admin" {{ $tutor->role == 'admin' ? 'checked="checked"' : '' }}>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-label">Admin</span>
                      </label>
                      <label class="custom-control custom-control-primary custom-radio" style="margin-left:20px;">
                          <input name="role" class="custom-control-input" type="radio" value="tutor" {{ $tutor->role == 'tutor' ? 'checked="checked"' : '' }}>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-label">Tutor</span>
                      </label>
                  </div>
              </div>
              <div class="form-group radio-div">
                  <label class="control-label col-md-3 text-right">Status :</label>
                  <div class="col-md-6 radio-div">
                      <label class="custom-control custom-control-primary custom-radio">
                          <input name="status" class="custom-control-input" type="radio" value="active" {{ $tutor->status == 'active' ? 'checked="checked"' : '' }}>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-label">Active</span>
                      </label>
                      <label class="custom-control custom-control-primary custom-radio" style="margin-left:20px;">
                          <input name="status" class="custom-control-input" type="radio" value="inactive" {{ $tutor->status == 'inactive' ? 'checked="checked"' : '' }}>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-label">Inactive</span>
                      </label>
                  </div>
              </div>
              <div class="form-group">
                  <label class="control-label col-md-3 text-right">&nbsp;</label>
                  <div class="col-md-6">
                      <button class="btn btn-block btn-primary do-save" type="submit" name="save">Save</button>
                  </div>
              </div>
          </form>
                  @else
                  <form class="form-horizontal employers-form" method="post" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">&nbsp;</label>
                          <div class="col-md-6">
                              Required fields are marked *
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">Name : *</label>
                          <div class="col-md-6">
                              <input type="text" class="form-control" name="first_name" required="">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">Email : *</label>
                          <div class="col-md-6">
                              <input type="email" class="form-control" name="email" required="">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">password : *</label>
                          <div class="col-md-6">
                              <input type="password" class="form-control" name="password">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">Role :</label>
                          <div class="col-md-6 radio-div">
                              <label class="custom-control custom-control-primary custom-radio">
                                  <input name="role" class="custom-control-input" type="radio" value="admin">
                                  <span class="custom-control-indicator"></span>
                                  <span class="custom-control-label">Admin</span>
                              </label>
                              <label class="custom-control custom-control-primary custom-radio" style="margin-left:20px;">
                                  <input name="role" class="custom-control-input" type="radio" value="tutor" checked>
                                  <span class="custom-control-indicator"></span>
                                  <span class="custom-control-label">Tutor</span>
                              </label>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label col-md-3 text-right">&nbsp;</label>
                          <div class="col-md-6">
                              <button class="btn btn-block btn-primary do-save" type="submit" name="save">Save</button>
                          </div>
                      </div>
                  </form>
                  @endif


                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
      function myFunction() {

          var x =confirm("you want to delete this job ");
          if (x)
          {
              return true;
          }
          else
          {
              event.preventDefault();
              return false;
          }
      }
  </script>
@endsection

@section('script')
<script>
function getFileName(obj,aClass){
    var vValue = $(obj).val();
    vValue = vValue.replace("C:\\fakepath\\",'');
    $('.'+aClass).val(vValue);
}
$('select').on('change', function() {
  var value=this.value;
  var id=$(this).parent().attr("data-id");
   $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
  $.ajax({
          type: "POST",
          url: "{{ url('dashboard/post_portal') }}",
          data: {job_id:id,value:value},
          success: function(data){
            //$('#treeviews').html(data);
            if(data ==1){
            toastr.success("Status Update");
            }
            console.log(data);
          }

    });

});
</script>
@endsection
