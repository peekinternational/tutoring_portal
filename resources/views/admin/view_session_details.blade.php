@extends('admin.layouts.master')
@section('content')
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
            <a class="navbar-brand" href="#pablo">Session Details</a>
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
            <div class="card">
              <div class="card-header">
                <!-- <h4 class="card-title"> Clients List <a href="{{url('dashboard/admin/add')}}" style="float:right;font-size: 15px;font-size: 12px; color:white;" type="button" class="btn btn-md btn-primary">Add Customer</a></h4> -->
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  @if(session()->has('message'))
                    <div class="row">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <strong>Message:</strong>{{session()->get('message')}}
                      </div>
                    </div>
                  @endif
                  <table class="table">
                    <thead class=" text-primary">
                      <th>Tutor Name</th>
                      <th>Student Name</th>
                      <th>Credit Balance</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Duration</th>
                      <th>Tutoring Subject</th>
                      <th>Recurs Weekly</th>
                      <th>Status</th>
                      <th class="text-right">Action</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          @if(SCT::getClientName($session->tutor_id) !='')
                          {{SCT::getClientName($session->tutor_id)->first_name}} {{SCT::getClientName($session->tutor_id)->last_name}}
                         @endif
                        </td>
                        <td> {{SCT::getStudentName($session->student_id)->student_name}}</td>
                        <td>
                          @if(SCT::getClientCredit($session->user_id) !='')
                           {{SCT::getClientCredit($session->user_id)->credit_balance}}
                          @endif
                         </td>
                        <?php
                        if ($session->added_by == 'Admin') {
                          $tutor_timezone = $session->admin_timezone;
                          $admin_timezone = Session::get('sct_admin')->time_zone;
                          // Check session time zone and admin time zone
                          if ($tutor_timezone == $admin_timezone) {
                            $time = date('h:i a', strtotime($session->time));
                            $date = date('M d, Y', strtotime($session->date));
                          }else {
                              if ($tutor_timezone == 'Pacific Time') {
                                date_default_timezone_set("America/Los_Angeles");
                              }elseif ($tutor_timezone == 'Mountain Time') {
                                date_default_timezone_set("America/Denver");
                              }elseif ($tutor_timezone == 'Central Time') {
                                date_default_timezone_set("America/Chicago");
                              }elseif ($tutor_timezone == 'Eastern Time') {
                                date_default_timezone_set("America/New_York");
                              }
                              $time1 = date('h:i a', strtotime($session->time));
                              $date = date('M d, Y', strtotime($session->date));
                              $time_zone =SCT::getClientName($session->tutor_id)->time_zone;
                              // dd($time_zone);
                              $db_time = $session->date." ".$time1;
                              $datetime = new DateTime($db_time);
                              if ($time_zone == 'Pacific Time') {
                                $la_time = new DateTimeZone('America/Los_Angeles');
                                $datetime->setTimezone($la_time);
                              }elseif ($time_zone == 'Mountain Time') {
                                $la_time = new DateTimeZone('America/Denver');
                                $datetime->setTimezone($la_time);
                              }elseif ($time_zone == 'Central Time') {
                                $la_time = new DateTimeZone('America/Chicago');
                                $datetime->setTimezone($la_time);
                              }elseif ($time_zone == 'Eastern Time') {
                                $la_time = new DateTimeZone('America/New_York');
                                $datetime->setTimezone($la_time);
                              }
                              $newdatetime = $datetime->format('Y-m-d h:i a');
                              $get_datetime = explode(' ',$newdatetime);
                              $time2 = $get_datetime[1];
                              $time3 = $get_datetime[2];
                              $time = $time2." ".$time3;
                          }
                        }else {
                          // Added by Tutor
                          $tutor_timezone = Session::get('sct_admin')->time_zone;
                        if ($tutor_timezone == 'Pacific Time') {
                          date_default_timezone_set("America/Los_Angeles");
                        }elseif ($tutor_timezone == 'Mountain Time') {
                          date_default_timezone_set("America/Denver");
                        }elseif ($tutor_timezone == 'Central Time') {
                          date_default_timezone_set("America/Chicago");
                        }elseif ($tutor_timezone == 'Eastern Time') {
                          date_default_timezone_set("America/New_York");
                        }
                        $time1 = date('h:i a', strtotime($session->time));
                        $date = date('M d, Y', strtotime($session->date));
                        $time_zone =SCT::getClientName($session->tutor_id)->time_zone;
                        // dd($time_zone);
                        $db_time = $session->date." ".$time1;
                        $datetime = new DateTime($db_time);
                        if ($time_zone == 'Pacific Time') {
                          $la_time = new DateTimeZone('America/Los_Angeles');
                          $datetime->setTimezone($la_time);
                        }elseif ($time_zone == 'Mountain Time') {
                          $la_time = new DateTimeZone('America/Denver');
                          $datetime->setTimezone($la_time);
                        }elseif ($time_zone == 'Central Time') {
                          $la_time = new DateTimeZone('America/Chicago');
                          $datetime->setTimezone($la_time);
                        }elseif ($time_zone == 'Eastern Time') {
                          $la_time = new DateTimeZone('America/New_York');
                          $datetime->setTimezone($la_time);
                        }
                        $newdatetime = $datetime->format('Y-m-d h:i a');
                        $get_datetime = explode(' ',$newdatetime);
                        $time2 = $get_datetime[1];
                        $time3 = $get_datetime[2];
                        $time = $time2." ".$time3;
                      }

                         ?>
                         <td> {{$date}}</td>
                        <td>{{$time}}</td>
                        <td> {{$session->duration}}</td>
                        <td> {{$session->subject}}</td>
                        <td> {{$session->recurs_weekly}}</td>
                        <td>
                          <?php
                            if ($session->status == 'Confirm') {
                              $status = 'Confirmed';
                            }elseif ($session->status == 'Cancel') {
                              $status = 'Canceled';
                            }elseif ($session->status == 'End'){
                              $status = 'Completed';
                            }else {
                              $status = $session->status;
                            }
                           ?>
                          {{$status}}
                        </td>
                        <td class="text-right">
                          <a href="{{url('/dashboard/session/edit/'.$session->session_id)}}" data-toggle="tooltip" data-original-title="Update"><i class="fa fa-edit text-primary"></i></a>
                          <!-- <i class="fa fa-eye text-success"></i> -->
                          <a href="javascript:0;" onclick="deleteEmployer('{{ $session->session_id }}')"> <i class="fa fa-trash text-danger"></i> </a>
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
                          <form method="post" action="{{ url('dashboard/cancel-session') }}">
                              <input type="hidden" name="_method" value="delete">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="session_id" class="actionId">
                              <div class="form-group">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" name="notify_client" id="notify_client" value="1" checked> <span style="margin-left:20px;">Notify client of cancellation</span>
                                </label>
                              </div>
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
