@extends('frontend.dashboard.layout.master')

@section('title', 'Sessions')

@section('styling')
<link rel="stylesheet" type="text/css" href="{{ asset('frontend-assets/css/fullcalendar.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('frontend-assets/css/fullcalendar.print.min.css') }}" media='print'>
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.print.css"> -->
<style>
#calendar {
  max-width: 900px;
  margin: 0 auto;
}
.past div.fc-time, .past div.fc-title {
            text-decoration: line-through;
        }
a.cancel .fc-content .fc-title ,a.cancel .fc-content .fc-time {
  text-decoration: line-through;
}
a.low-credit .fc-content .fc-title ,a.low-credit .fc-content .fc-time {
  background: #dcdc25 !important;
  border: 1px solid #dcdc25 !important;
}
a.low-credit  {
  border-color: #dcdc25 !important;
  background: #dcdc25 !important;
}
a.low-credit .fc-content {
  border-color: #dcdc25 !important;
  background: #dcdc25 !important;
}
</style>
@endsection
@section('content')

@include('frontend.dashboard.menu.menu')

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
        <a class="navbar-brand" href="#">Sessions</a>
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
              <a href="{{url('user-portal/session/add')}}" class="btn btn-green pull-right">Schedule New Session</a>
              <h3 class="title">Sessions</h3>
              <hr>
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

              <!-- Calendar Start -->
              <div id='calendar'></div>
              <!-- Calendar Ends -->
              <div class="" style="margin-top:20px;">
                @if(count($sessions) > 0)
                <ul style="list-style-type: none;">
                  @foreach($sessions as $session)
                  <?php
                  $time = date('h:i a', strtotime($session->time));
                   $date = date('M d, Y', strtotime($session->date));
                   ?>
                   @if(SCT::checkCredit($session->user_id)->credit_balance == 0.5)
                   <li><a href="{{url('user-portal/tutor-sessions-details/'.$session->session_id)}}" style="background: #dcdc25;color: white;border-radius: 4px;"><span style="padding: 10px;">@if($session->status == 'Cancel' || $session->status == 'Insufficient Credit') <strike>{{$session->duration}} {{$session->student_name}} </strike> @else {{$session->duration}} {{$session->student_name}} (half hour credit) @endif</span> </a></li>
                   @else
                   <li><a href="{{url('user-portal/tutor-sessions-details/'.$session->session_id)}}" style="background: #10C5A7;color: white;border-radius: 4px;"><span style="padding: 10px;">@if($session->status == 'Cancel' || $session->status == 'Insufficient Credit') <strike>{{$session->duration}} {{$session->student_name}}</strike> @else {{$session->duration}} {{$session->student_name}} @endif</span> </a></li>
                   @endif
                   <!-- @if(SCT::checkCredit($session->user_id)->credit_balance == 0.5)
                   <li><a href="{{url('user-portal/tutor-sessions-details/'.$session->session_id)}}" style="background: #dcdc25;color: white;border-radius: 4px;"><span style="padding: 10px;">@if($session->status == 'Cancel' || $session->status == 'Insufficient Credit') <strike>{{$time}} {{$date}} {{$session->subject}} Session</strike> @else {{$time}} {{$date}} {{$session->subject}} Session (half hour credit) @endif</span> </a></li>
                   @else
                   <li><a href="{{url('user-portal/tutor-sessions-details/'.$session->session_id)}}" style="background: #10C5A7;color: white;border-radius: 4px;"><span style="padding: 10px;">@if($session->status == 'Cancel' || $session->status == 'Insufficient Credit') <strike>{{$time}} {{$date}} {{$session->subject}} Session</strike> @else {{$time}} {{$date}} {{$session->subject}} Session @endif</span> </a></li>
                   @endif -->
                   @endforeach
                </ul>
                @else
                <h4>No sessions scheduled</h4>
                @endif
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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script> -->
<script src="{{asset('/frontend-assets/js/moment.min.js')}}"></script>
<script src="{{asset('/frontend-assets/js/fullcalendar.min.js')}}"></script>
<script src="{{asset('/frontend-assets/js/jquery-ui.min.js')}}"></script>
<script>
$(document).ready(function() {


  $('#calendar').on('click', '.fc-day-grid-event', function(){
    $('.file_menu').css('display','block');
  });

  $('#calendar').fullCalendar({
    editable: true,
    defaultView: 'agendaWeek',
      eventLimit: true,

      events: function(start, end, timezone, callback) {
        $.ajax({
          url: "{{url('/user-portal/gettutorCallenderData')}}",
          datatype : 'json',
          success: function(doc) {
          // console.log(doc);
            var events = [];

            $.each(JSON.parse(doc), function(k, v) {
              if (v.status == 'Cancel' || v.status == 'Insufficient Credit') {
                events.push({
                 id : v.session_id,
                     className : 'cancel',
                     title: v.duration+' '+v.student_name,
                     // title: v.subject+' session',
                     start: v.date, // will be parsed
                     // start: v.date+'T'+v.time,
                     // start: '2020-07-08T16:00:00',
                     url: "{{url('/user-portal/tutor-sessions-details')}}/"+v.session_id,
                   });
              }
              else if (v.credit == 0.5) {
                events.push({
                 id : v.session_id,
                     className : 'low-credit',
                     title: v.duration+' '+v.student_name,
                     // title: v.subject+' session',
                     start: v.date, // will be parsed
                     // start: v.date+'T'+v.time,
                     // start: '2020-07-08T16:00:00',
                     url: "{{url('/user-portal/tutor-sessions-details')}}/"+v.session_id,
                   });
              }
              else {
                  events.push({
                   id : v.session_id,
                    title: v.duration+' '+v.student_name,
                       // title: v.subject+' session',
                       start: v.date, // will be parsed
                       // start: v.date+'T'+v.time,
                       // start: '2020-07-08T16:00:00',
                       url: "{{url('/user-portal/tutor-sessions-details')}}/"+v.session_id,
                     });
              }

        });

            callback(events);

          }
        });
      },
      header: {
            left: '',
            center: 'prev title next',
            right: ''
        },
        contentHeight: 'auto',
        defaultView: 'basicWeek',
        eventColor: '#10C5A7',
        timeFormat: 'h:mma',
        // timeFormat: 'h(:mm)a',
        eventClick:  function(event, jsEvent, view) {

          var team_id = event.id;

          $.ajax({
          // url: "{{ url('/golf/getDateDetails/')}}/"+team_id,
          dataType: "json",
          success: function(res) {

            console.log(res);

            $('.file_menu').html("");

            if(res && res.length > 0) {

              $('#modalTitle').html(event.title);

              for (var i = 0; i < res.length; ++i) {

                 // $('.file_menu').append('<li><a href="{{url("golf/golf-booking/")}}/'+res[i].timing_id+'">'+event.start._i+' '+res[i].time+'<br>('+res[i].dimension_name+')</a></li>');

              }

              $('#modalDate').html(new Date(event.start._i).getDate());
              $('#fullCalModal').modal();

            }

           }

        });

        }

});
});

// $(document).ready(function() {
//
//     $('#calendar').fullCalendar({
//       header: {
//         left: 'prev,next today',
//         center: 'title',
//         // right: 'month,agendaWeek,agendaDay,listWeek'
//         right: 'day'
//
//       },
//       defaultDate: '2018-03-12',
//       navLinks: true, // can click day/week names to navigate views
//
//       weekNumbers: true,
//       weekNumbersWithinDays: true,
//       weekNumberCalculation: 'ISO',
//
//       editable: true,
//       eventLimit: true, // allow "more" link when too many events
//       events: [
//         {
//           title: 'All Day Event',
//           start: '2018-03-01'
//         },
//         {
//           title: 'Long Event',
//           start: '2018-03-07',
//           end: '2018-03-10'
//         },
//         {
//           id: 999,
//           title: 'Repeating Event',
//           start: '2018-03-09T16:00:00'
//         },
//         {
//           id: 999,
//           title: 'Repeating Event',
//           start: '2018-03-16T16:00:00'
//         },
//         {
//           title: 'Conference',
//           start: '2018-03-11',
//           end: '2018-03-13'
//         },
//         {
//           title: 'Meeting',
//           start: '2018-03-12T10:30:00',
//           end: '2018-03-12T12:30:00'
//         },
//         {
//           title: 'Lunch',
//           start: '2018-03-12T12:00:00'
//         },
//         {
//           title: 'Meeting',
//           start: '2018-03-12T14:30:00'
//         },
//         {
//           title: 'Happy Hour',
//           start: '2018-03-12T17:30:00'
//         },
//         {
//           title: 'Dinner',
//           start: '2018-03-12T20:00:00'
//         },
//         {
//           title: 'Birthday Party',
//           start: '2018-03-13T07:00:00'
//         },
//         {
//           title: 'Click for Google',
//           url: 'http://google.com/',
//           start: '2018-03-28'
//         }
//       ]
//     });
//
//   });

</script>
@endsection
