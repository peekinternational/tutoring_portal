<div class="sidebar hidden-xs hidden-sm" data-background-color="black" data-active-color="danger">
  <div class="sidebar-wrapper">
    <div class="logo">
      <!-- <a href="#" class="simple-text" id="dash_Name">
        {{auth()->user()->first_name}}
      </a> -->
      <span class="simple-text" id="dash_Name">{{auth()->user()->first_name}}</span>

    </div>
    <ul class="nav">
      <!-- <li  class="{{ request()->is('user-portal/dashboard') ? 'active' : '' }}">
        <a href="{{url('user-portal/dashboard')}}">
          <i class="ti-panel"></i>
          <p>Dashboard</p>
        </a>
      </li> -->
      @if(auth()->user()->role == 'customer')
      <li class="{{ request()->is('user-portal/manage-profile') ? 'active' : '' }}">
        <a href="{{url('user-portal/manage-profile')}}">
          <i class="ti-user"></i>
          <p>Client Profile</p>
        </a>
      </li>
      @elseif(auth()->user()->role == 'tutor' || auth()->user()->role == 'admin')
      <li class="{{ request()->is('user-portal/manage-profile-tutor') ? 'active' : '' }}">
        <a href="{{url('user-portal/manage-profile-tutor')}}">
          <i class="ti-user"></i>
          <p>Tutor Profile</p>
        </a>
      </li>
      @endif
      @if(auth()->user()->role == 'customer')
      <li class="{{ request()->is('user-portal/students') ? 'active' : '' }}">
        <a href="{{url('/user-portal/students')}}">
          <i class="ti-user"></i>
          <p>Students</p>
        </a>
      </li>
      <li class="{{ request()->is('user-portal/tutors') ? 'active' : '' }}">
        <a href="{{url('/user-portal/tutors')}}">
          <i class="ti-user"></i>
          <p>Tutors</p>
        </a>
      </li>
      <li class="{{ request()->is('user-portal/client-sessions') ? 'active' : '' }}">
        <a href="{{url('/user-portal/client-sessions')}}">
          <i class="ti-calendar"></i>
          <p>Sessions</p>
        </a>
      </li>
      @endif
      @if(auth()->user()->role == 'tutor' || auth()->user()->role == 'admin')
      <li class="{{ request()->is('user-portal/tutor-students') ? 'active' : '' }}">
        <a href="{{url('/user-portal/tutor-students')}}">
          <i class="ti-user"></i>
          <p>Students</p>
        </a>
      </li>
      <li class="{{ request()->is('user-portal/tutor-sessions') ? 'active' : '' }}">
        <a href="{{url('/user-portal/tutor-sessions')}}">
          <i class="ti-calendar"></i>
          <p>Sessions</p>
        </a>
      </li>
      <li class="{{ request()->is('user-portal/tutor-timesheets') ? 'active' : '' }}">
        <a href="{{url('/user-portal/tutor-timesheets')}}">
          <i class="ti-calendar"></i>
          <p>Timesheets</p>
        </a>
      </li>
      @endif
      <li class="{{ request()->is('user-portal/agreements') ? 'active' : '' }}">
        <a href="{{url('/user-portal/agreements')}}">
          <!-- <i class="ti-pencil-alt2"></i> -->
          <i class="ti-files"></i>
          <p>Agreements</p>
        </a>
      </li>
      @if(auth()->user()->role == 'customer')
      <li class="{{ request()->is('user-portal/faqs') ? 'active' : '' }}">
        <a href="{{url('/user-portal/faqs')}}">
          <!-- <i class="ti-pencil-alt2"></i> -->
          <i class="ti-book"></i>
          <p>FAQs</p>
        </a>
      </li>
      <li class="{{ request()->is('user-portal/credits') ? 'active' : '' }}">
        <a href="{{url('/user-portal/credits')}}">
          <i class="ti-credit-card"></i>
          <p>Credits</p>
        </a>
      </li>
      @endif
      <li>
        <a href="{{url('/logout')}}">
          <i class="fas fa-sign-out-alt"></i>
          <p>Logout</p>
        </a>
      </li>

      <!-- <li class="{{ request()->is('user-portal/create-extension') ? 'active' : '' }}">
        <a href="{{url('user-portal/create-extension')}}">
          <i class="ti-pulse"></i>
          <p>Telephone Setting</p>
        </a>
      </li> -->



      <!-- <li class="{{ request()->is('user-portal/disposition-call-report') ? 'active' : '' }}">
        <a href="{{url('user-portal/disposition-call-report')}}">
          <i class="ti-map"></i>
          <p>Disposition of calls report</p>
        </a>
      </li> -->
      <?php
      // dd(request());
      // dd(request()->path());
      ?>
      <!-- <li class="{{ request()->is('user-portal/update-pricing-plan') ? 'active' : '' }}">
        <a href="{{url('/user-portal/update-pricing-plan')}}">
          <i class="ti-image"></i>
          <p>Upgrade package</p>
        </a>
      </li> -->
      <!-- <li class="{{ request()->is('user-portal/billing-info') ? 'active' : '' }}">
        <a href="{{url('/user-portal/billing-info')}}">
          <i class="ti-receipt"></i>
          <p>Billing Information</p>
        </a>
      </li> -->



      <!-- <li class="{{ request()->is('') ? 'active' : '' }}">
        <a href="">
          <i class="ti-comments"></i>
          <p>WebRTC</p>
        </a>
      </li> -->
      <!--  <li class="{{ request()->is('dashboard/affiliate') ? 'active' : '' }}">
        <a href="/dashboard/affiliate">
          <i class="ti-image"></i>
          <p>Affiliate Program</p>
        </a>
      </li>
      <li class="{{ request()->is('dashboard/user_upload') ? 'active' : '' }}">
        <a href="/dashboard/user_upload">
          <i class="ti-image"></i>
          <p>Image/Video Upload</p>
        </a>
      </li>
      <li class="{{ request()->is('dashboard/affiliatedata') ? 'active' : '' }}">
        <a href="/dashboard/affiliatedata">
          <i class="ti-image"></i>
          <p>Local Data Upload</p>
        </a>
      </li> -->

    </ul>
  </div>
</div>
