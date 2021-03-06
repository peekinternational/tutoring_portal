@extends('frontend.dashboard.layout.master')

@if($rPath == 'edit')
    @section('title', 'Update Session')
@else
    @section('title', 'Add Session')
@endif

@section('styling')
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
				<a class="navbar-brand" href="#">Session Management</a>
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
				<div class="col-lg-9 col-md-9 app-view-mainCol">
					<div class="cards">
						<div class="header">
							<h3 class="title">{{ $rPath == 'edit' ? 'Update Session' : 'Add Session' }}</h3>
							<hr>
						</div>
            @include('frontend.dashboard.menu.alerts')
						<div class="content">
							@if($rPath == 'edit')
							<form class="form-horizontals profile-form" action="" method="post">
								{{ csrf_field() }}
								<input type="hidden" name="session_id" value="{{$session->session_id}}">
                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Students</label>
											<select class="form-control border-input" name="student_id" id="student_id" required>
                        <option value="">Select Student</option>
                        @foreach($assign_students as $students)
												<option value="{{$students->student_id}},{{$students->user_id}}" {{$session->student_id == $students->student_id ? 'selected=="selected"':''}}>{{$students->student_name}}</option>
                        @endforeach
											</select>
										</div>
									</div>
								</div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Subject</label>
                      <input type="text" class="form-control border-input" placeholder="Enter Subject" id="subject" name="subject" value="{{$session->subject}}" required>
                    </div>
                  </div>
                </div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Date</label>
											<!-- <input type="date" class="form-control border-input" placeholder="Select Date" id="date" name="date" value="{{$session->date}}" required> -->
											<input type="text" class="form-control border-input date-picker-year" placeholder="Select Date" id="date" name="date" value="{{$session->date}}" required>
										</div>
									</div>
								</div>
                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Time</label>
                      <input type="time" class="form-control" id="time" name="time" value="{{$session->time}}" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Duration</label>
                      <select class="form-control border-input" name="duration" id="duration">
												<option value="0:30" {{$session->duration == '0:30' ? 'selected="selected"' : ''}}>0:30</option>
												<option value="1:00" {{$session->duration == '1:00' ? 'selected="selected"' : ''}}>1:00</option>
												<option value="1:30" {{$session->duration == '1:30' ? 'selected="selected"' : ''}}>1:30</option>
												<option value="2:00" {{$session->duration == '2:00' ? 'selected="selected"' : ''}}>2:00</option>
											</select>
                    </div>
									</div>
								</div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Location</label>
                      <input type="text" class="form-control border-input" placeholder="Enter Location" id="location" name="location" value="{{$session->location}}" required>
                    </div>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="initial_session" id="initial_session" value="1" {{$session->session_type == 'First Session' ? 'checked':''}}> Initial Session
                      </label>
                    </div>
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="recurs_weekly" id="recurs_weekly" value="1" {{$session->recurs_weekly == 'Yes' ? 'checked':''}} > Recurs Weekly
                      </label>
                    </div>
                  </div>
                </div>
								<div class="text-center">
									<button type="submit" class="btn btn-info btn-wd btn-green">Save</button>
								</div>

								<div class="clearfix"></div>
							</form>
							@else
							<form class="form-horizontals profile-form" action="" method="post">
								{{ csrf_field() }}
                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Students</label>
											<select class="form-control border-input" name="student_id" id="student_id" required>
                        <option value="">Select Student</option>
                        @foreach($assign_students as $students)
												<option value="{{$students->student_id}},{{$students->user_id}}">{{$students->student_name}}</option>
                        @endforeach
											</select>
										</div>
									</div>
								</div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Subject</label>
                      <input type="text" class="form-control border-input" placeholder="Enter Subject" id="subject" name="subject" required>
                    </div>
                  </div>
                </div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Date</label>
											<!-- <input type="date" class="form-control border-input" placeholder="Select Date" id="date" name="date" value="" required> -->
											<input type="text" class="form-control border-input date-picker-year" placeholder="Select Date" id="date" name="date" value="" required>
										</div>
									</div>
								</div>
                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Time</label>
                      <input type="time" class="form-control" id="time" name="time" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Duration</label>
                      <select class="form-control border-input" name="duration" id="duration">
												<option value="0:30">0:30</option>
												<option value="1:00" selected>1:00</option>
												<option value="1:30">1:30</option>
												<option value="2:00">2:00</option>
											</select>
                    </div>
									</div>
								</div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Location</label>
                      <input type="text" class="form-control border-input" placeholder="Enter Location" id="location" name="location" required>
                    </div>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="initial_session" id="initial_session" value="1" checked> Initial Session
                      </label>
                    </div>
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="recurs_weekly" id="recurs_weekly" value="1"> Recurs Weekly
                      </label>
                    </div>
                  </div>
                </div>
								<div class="text-center">
									<button type="submit" class="btn btn-info btn-wd btn-green">Save</button>
								</div>

								<div class="clearfix"></div>
							</form>
							@endif
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>


</div>
@endsection

@section('script')
<script>
$('#initial_session').click(function () {
  var initial_session =  $('#initial_session').val();
if (this.checked == true) {
  $('#duration option[value="1:00"]').prop('selected', true)
  $('#recurs_weekly').prop('disabled',true);
}else {
  $('#recurs_weekly').prop('disabled',false);

}
})

// $('#student_id').on('change', function () {
//   var student_user_id = $(this).val();
//   var tutor_id = "{{auth()->user()->id}}";
//   var result = student_user_id.split(',');
//   var student_id = result[0];
//   $.ajax({
//     url: "{{ url('check_initialSession') }}/"+student_id+"/"+tutor_id,
//     success: function(response){
//       // console.log(response);
//       if (response == 0) {
//         $('#initial_session').prop('checked',true);
//         $('#recurs_weekly').prop('disabled',true);
//       }else {
//         $('#initial_session').prop('checked',false);
//         $('#recurs_weekly').prop('disabled',false);
//       }
//     }
//   });
// });

$('.date-picker-year').datetimepicker({
  format:'yyyy-mm-dd',
  // endDate: '+0d',
  startDate: '+0d',
  weekStart: 1,
  todayBtn:  1,
  autoclose: 1,
  todayHighlight: 1,
  startView: 2,
  minView: 2,
  forceParse: 0
});
</script>

@endsection
