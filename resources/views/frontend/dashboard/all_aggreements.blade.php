@extends('frontend.dashboard.layout.master')

@section('title', 'Agreements')

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
        <a class="navbar-brand" href="#">Agreements</a>
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
              <!-- <a href="{{url('user-portal/student/add')}}" class="btn btn-green pull-right">New Student</a> -->
              <h3 class="title"><img src="{{asset('/frontend-assets/images/glasses.png')}}" class="glass-img" alt="logo"><span class="page-title">Agreements</span></h3>
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
              <div class="table-responsive">
                <table class="table  table-bordered hidden-xs hidden-sm">
                  <thead>
                    <tr>
                      <th>Agreement Name</th>
                      <th>Status</th>
                      <th>Signed Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($agreements as $agreement)
                    <tr>
                      <td>{{$agreement->aggreement_name}}</td>
                      <td>{{$agreement->status}}</td>
                      <td>{{$agreement->date}}</td>
                      <td>
                        <a href="{{ url('user-portal/view_agreement/'.$agreement->aggreement_id) }}" class="btn btn-green" data-toggle="tooltip" data-original-title="Update">View</a>&nbsp;&nbsp;&nbsp;
                        <!-- <a href="javascript:;" onclick="deleteEmployer('{{ $agreement->aggreement_id }}')" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash"></i></a> -->
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="hidden-sm hidden-xs">
                  {{$agreements->render()}}
                </div>
              </div>
              <div class="hidden-md hidden-lg">
                @foreach($agreements_mobile as $agreement)
                <table class="table table-bordered ">
                  <thead>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Agreement Name</td>
                      <td>{{$agreement->aggreement_name}}</td>
                    </tr>
                    <tr>
                      <td>Status</td>
                      <td>{{$agreement->status}}</td>
                    </tr>
                    <tr>
                      <td>Signed Date</td>
                      <td>{{$agreement->date}}</td>
                    </tr>
                    <tr>
                      <td>Action</td>
                      <td>
                        <a href="{{ url('user-portal/view_agreement/'.$agreement->aggreement_id) }}" class="btn btn-green" data-toggle="tooltip" data-original-title="Update">View</a>&nbsp;&nbsp;&nbsp;
                        <!-- <a href="javascript:;" onclick="deleteEmployer('{{ $agreement->aggreement_id }}')" data-toggle="tooltip" data-original-title="Delete"><i class="ti-trash"></i></a> -->
                      </td>
                    </tr>
                    </tr>
                  </tbody>
                </table>
                @endforeach
                {{$agreements_mobile->render()}}
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
