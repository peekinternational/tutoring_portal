@extends('frontend.dashboard.layout.master')

@section('title', 'Students')

@section('styling')
<style>
::-webkit-scrollbar {
  -webkit-appearance: none;
}

::-webkit-scrollbar:vertical {
  width: 12px;
}

::-webkit-scrollbar:horizontal {
  height: 12px;
}

::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, .5);
  border-radius: 10px;
  border: 2px solid #ffffff;
}

::-webkit-scrollbar-track {
  border-radius: 10px;
  background-color: #ffffff;
}
</style>
@endsection
@section('content')

@include('frontend.dashboard.menu.menu')
<?php
$searchBy = array('student_name' => 'Student Name', 'email' => 'Student Email', 'first_name' => 'Client First Name');
$s_app = Session()->get('TutorStudentSearch');
?>
<div class="main-panel">
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar bar1"></span>
        <span class="icon-bar bar2"></span>
        <span class="icon-bar bar3"></span>
        </button>
        <a class="navbar-brand" href="#">Students</a>
      </div>
    </div>
  </nav>
  <div class="content">
    <div class="container-fluid app-view-mainCol">
      <div class="row">
        <!-- <div class="col-lg-4 col-md-5 app-view-mainCol">
          <div class="cards cards-user">
            <div class="image">
              <img src="{{asset('frontend-assets/images/dashboard/background.jpg')}}" alt="...">
            </div>
            <div class="content">
              <div class="author">
                <div class="re-img-box">
                  <img class="avatar border-white" src="" alt="...">
                  <div class="re-img-toolkit">
                    <div class="re-file-btn">
                      Change <i class="fa fa-camera"></i>
                      <input type="file" class="upload" id="imageFile"  name="image"  onchange="uploadpicture(this)">
                    </div>
                  </div>
                </div>
                <h4 class="title" id="userName">Zeeshan<br>
                </h4>
              </div>
            </div>
            <hr>
            <div class="text-center">
              <div class="row">
              </div>
            </div>
          </div>
        </div> -->
        <div class="col-lg-12 col-md-12 app-view-mainCol">
          <div class="cards">
            <div class="header">
              <h3 class="title"><img src="{{asset('/frontend-assets/images/glasses.png')}}" class="glass-img" alt="logo"><span class="page-title">Students</span></h3>
              <hr>
              <form method="post" action="{{ url('user-portal/tutor-students') }}">
                <div class="row">
                      {{ csrf_field() }}
                      <div class="col-md-4">
                          <label>Search By</label>
                          <select class="form-control select2" name="searchBy">
                            @foreach($searchBy as $x => $y)
                                <option value="{{ $x }}" {{ $x == $s_app['searchBy'] ? 'selected="selected"' : '' }}>{{ $y }}</option>
                            @endforeach
                          </select>
                      </div>
                      <div class="col-md-4">
                          <label>Search String</label>
                          <input type="text" class="form-control" name="search" placeholder="Type here ..." value="{{ $s_app['search'] }}" style="line-height: 2;">
                      </div>
                      <div class="col-md-4" style="margin-top: 0;">
                          <label style="display: block;">&nbsp;</label>
                          <button class="btn btn-primary" type="submit" name="filter" style="background:#10C5A7;border:1px solid #10C5A7;">Search</button>
                          @if($s_app !=null)
                              <a class="btn btn-default" href="{{ url('user-portal/tutor-students?reset=true') }}">Reset</a>
                          @endif
                      </div>
                  </div>
            </form>
              @include('frontend.dashboard.menu.alerts')
              @if(Session::has('message'))
        			<div class="alert alert-success">
        				 {{ Session::get('message') }}
        				 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        				 <span aria-hidden="true">&times;</span>
        				 </button>
        			</div>
        			@endif
            </div>
            <div class="content">
              <div class="table-responsive">
                <table class="table  table-bordered hidden-xs hidden-sm">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Client Name</th>
                      <th>Client Credit</th>
                      <th>Hourly Rate</th>
                      <th>Email</th>
                      <th>Subject</th>
                      <th>Grade</th>
                      <th>School/College</th>
                      <th>Goal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // dd($students);
                     ?>
                    @foreach($students as $student)
                    <tr>
                      <td>{{$student->student_name}}</td>
                      <td>{{SCT::getClientName($student->user_id)->first_name}} {{SCT::getClientName($student->user_id)->last_name}}</td>
                      @if(SCT::getClientCredit($student->user_id) !='')
                      <td>{{SCT::getClientCredit($student->user_id)->credit_balance}}</td>
                      @else
                      <td>0</td>
                      @endif
                      <td>${{$student->hourly_pay_rate}}</td>
                      <td>{{$student->email}}</td>
                      <td>{{$student->subject}}</td>
                      <td>{{$student->grade}}</td>
                      <td>{{$student->college}}</td>
                      <td>{{$student->goal}}</td>

                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="hidden-sm hidden-xs">
                  {{$students->render()}}
                </div>
              </div>
              <div class="hidden-md hidden-lg">
              @foreach($student_mobile as $student)
              <table class="table table-bordered ">
                <thead>
                </thead>
                <tbody>
                  <tr>
                    <td>Name</td>
                    <td>{{$student->student_name}}</td>
                  </tr>
                  <tr>
                    <td>Client Name</td>
                    <td>{{SCT::getClientName($student->user_id)->first_name}} {{SCT::getClientName($student->user_id)->last_name}}
                    </td>
                  </tr>
                    <tr>
                      <td>Client Credit</td>
                      @if(SCT::getClientCredit($student->user_id) !='')
                      <td>{{SCT::getClientCredit($student->user_id)->credit_balance}}</td>
                      @else
                      <td>0</td>
                      @endif
                    </tr>
                     <tr>
                       <td>Hourly Rate</td>
                       <td>${{$student->hourly_pay_rate}}</td>
                     </tr>
                     <tr>
                       <td>Email</td>
                       <td>{{$student->email}}</td>
                     </tr>
                     <tr>
                       <td>Grade</td>
                       <td>{{$student->grade}}</td>
                     </tr>
                     <tr>
                       <td>School/College</td>
                       <td>{{$student->college}}</td>
                     </tr>
                     <tr>
                       <td>Goal</td>
                       <td>{{$student->goal}}</td>
                     </tr>
                </tbody>
              </table>
              @endforeach
                {{$student_mobile->render()}}
              </div>
            </div>
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="modal-warning" role="dialog" class="modal fade in" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content bg-warning animated bounceIn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <span class="icon icon-exclamation-triangle icon-5x"></span>
                    <h3>Are you sure?</h3>
                    <p>You will not be able to undo this action.</p>
                    <div class="m-t-lg">
                        <form method="post" action="{{ url('user-portal/student/delete') }}">
                            <input type="hidden" name="_method" value="delete">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="student_id" class="actionId">
                            <button class="btn btn-danger" type="submit">Continue</button>
                            <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function deleteEmployer(userId){
    $('.actionId').val(userId);
    $('#modal-warning').modal();
}
function doAction(){
    var userId = $('.actionId').val();
    if(userId != ''){
        alert('delete this '+userId);
    }
}
</script>
@endsection
