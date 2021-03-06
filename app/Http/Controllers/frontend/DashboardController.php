<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Cashier\Cashier;
use App\Facade\SCT;
use App\User;
use DB;
use Session;
use Hash;
use Mail;
use Storage;
use Response;
use DateTime;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
     {
        $user=User::find(auth()->user()->id);
          $user2 = auth()->user();
          // dd($user2);
         return view('frontend.dashboard.index',compact('user'));
     }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show(Request $request)
     {
         if($request->isMethod('post')){
           // dd($request->all());
             $post =User::find($request->user()->id);
             $post->first_name = $request->input('first_name');

             $post->last_name = $request->input('last_name');
             $post->phone = $request->input('phone');
             $post->address = $request->input('address');
             $post->city = $request->input('city');
             $post->state = $request->input('state');
             $post->zip = $request->input('zip');
             $post->time_zone = $request->input('time_zone');
             $automated_emai = '';
             $automated_emai = $request->input('automated_email');
             if ($automated_emai !='') {
               $post->automated_email = 'Subscribe';
             }else {
               $post->automated_email = 'Unsubscribe';
             }
             if ($request->input('password') !=null) {
               $post->password =Hash::make(trim($request->input('password')));
             }
             $post->save();
         }
          $user = User::findOrFail($request->user()->id);
          // dd($user);
          return view('frontend.dashboard.profile',compact('user'));
     }

     public function show_tutor(Request $request)
     {
         if($request->isMethod('post')){
           // dd($request->all());
             $post =User::find($request->user()->id);
             $post->first_name = $request->input('first_name');

             $post->last_name = $request->input('last_name');
             $post->phone = $request->input('phone');
             $post->description = $request->input('description');
             $post->time_zone = $request->input('time_zone');
             // $post->address = $request->input('address');
             if ($request->input('password') !=null) {
               $post->password =Hash::make(trim($request->input('password')));
             }
             $post->save();
         }
          $user = User::findOrFail($request->user()->id);
          // dd($user);
          return view('frontend.dashboard.profile-tutor',compact('user'));
     }

     public function profilePicture(Request $request){

       if(!$request->ajax()){
         exit('Directory access is forbidden');
       }

       $userinfo=auth()->user();
       if($request->file('user_image') != ''){

         $fName = $_FILES['user_image']['name'];
         $ext = @end(@explode('.', $fName));
         if(!in_array(strtolower($ext), array('png','jpg','jpeg'))){
           exit('1');
         }

         $user = User::find($userinfo->id);

         $image = $request->file('user_image');
         $profilePicture = 'profile-'.time().'-'.rand(000000,999999).'.'.$image->getClientOriginalExtension();
         $destinationPath = public_path('/frontend-assets/images/dashboard/profile-photos');
         $image->move($destinationPath, $profilePicture);
         $user_old_image = $user->image;
         User::where('id',$userinfo->id)->update(array('image' => $profilePicture));
         if($user_old_image != ''){
           @unlink(public_path('/frontend-assets/images/dashboard/profile-photos/'.$user_old_image));
         }
         echo url('/frontend-assets/images/dashboard/profile-photos/'.$profilePicture);
       }

     }

     public function getAgreements(Request $request)
     {
       $user_id = auth()->user()->id;
       $agreements = DB::table('signed_aggreements')->where('user_id',$user_id)->paginate(15);
       $agreements_mobile = DB::table('signed_aggreements')->where('user_id',$user_id)->paginate(1);
       return view('frontend.dashboard.all_aggreements',compact('agreements','agreements_mobile'));
     }

     public function ViewAgreementDetails(Request $request ,$id)
     {
        $agreement=DB::table('signed_aggreements')->where('aggreement_id',$id)->where('user_id',auth()->user()->id)->first();
        return view('frontend.dashboard.view-aggreement',compact('agreement'));

     }

     public function showAgreement(Request $request ,$id)
     {
        // dd('hello');
        $document=DB::table('aggreements')->where('aggreement_id',$id)->first();
         $filePath = $document->file;
         $type = Storage::mimeType($filePath);
         // dd($type);
         if( ! Storage::exists($filePath) ) {
         abort(404);
         }
         $pdfContent = Storage::get($filePath);
         return Response::make($pdfContent, 200, [
         'Content-Type'        => $type,
         'Content-Disposition' => 'inline; filename="'.$filePath.'"'
         ]);
     }

     public function SignAgreement(Request $request)
     {
       // dd($request->all());
       $agreement_id =$request->input('aggreement_id');
       $input['user_name'] = $request->input('user_name');
       $input['date'] = $request->input('date');
       $input['status'] = 'Signed';
       $agreement =DB::table('signed_aggreements')->where('aggreement_id',$agreement_id)->where('user_id',auth()->user()->id)->update($input);
       $request->session()->flash('message',"Agreement Signed Successfull");
       return redirect('/user-portal/agreements');
     }

    public function faqs(Request $request)
    {
      $faq = DB::table('faqs')->first();
      return view('frontend.dashboard.view-faqs',compact('faq'));
    }

    public function credits(Request $request)
    {
      $credit = DB::table('credits')->where('user_id',auth()->user()->id)->first();
      return view('frontend.dashboard.view-credits',compact('credit'));
    }

    public function buyCredit(Request $request)
       {
          // dd($request->all());
          $credit_id = $request->input('credit_id');
          $credit_cost = $request->input('credit_cost');
          $credit_balance = $request->input('credit_balance');
          $total = $credit_cost*$credit_balance;
          // dd($total);
           return view('frontend.dashboard.show2', compact('credit_id','credit_cost','credit_balance','total'));
       }

       public function subscribe_process(Request $request)
       {
         // dd($request->all());
         try {
           $stripe = \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
           $userId=auth()->user()->id;
           $user = User::find($userId);
           $tokenId= $request->stripeToken;
           $total = $request->total*100;
           $credit_id = $request->input('credit_id');
           $credit_balance = $request->input('credit_balance');
           $credit_cost = $request->input('credit_cost');
           $get_credit = DB::table('credits')->where('credit_id',$credit_id)->first();

           // dd($get_credit);
           $customer = $user->createAsStripeCustomer($tokenId,[
             'email' => $user->email,
           ]);
           $stripeCharge = $user->charge($total);
           $new_credit_balance = $get_credit->credit_balance+$credit_balance;
           $status = $get_credit->status;
           $data['stripeToken'] = $tokenId;
           DB::table('users')->where('id',$userId)->update($data);

           $receipt =$stripeCharge['receipt_url'];
           $input['credit_balance'] = $new_credit_balance;
           $input['status'] = 'Purchased Before';
           $input['receipt_url'] = $receipt;
           DB::table('credits')->where('credit_id',$credit_id)->update($input);

           $new_credit = DB::table('credits')->where('credit_id',$credit_id)->first();
           if ($user->automated_email == 'Subscribe') {
             $toemail =  $user->email;
             // dd($send);
             Mail::send('mail.purchase_credit_email',['user' =>$user,'credit'=>$new_credit],
             function ($message) use ($toemail)
             {

               $message->subject('Smart Cookie Tutors.com - New Credit Purchased');
               $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
               $message->to($toemail);
             });
         }


           $admins = User::where('role','admin')->get();
          if ($status == 'First Purchase') {
           foreach ($admins as $admin) {
             $toemail =  $admin->email;
             // dd($send);
             Mail::send('mail.new_user_credit_email',['user' =>$user,'credit'=>$new_credit],
             function ($message) use ($toemail)
             {
               $message->subject('Smart Cookie Tutors.com - New Credit Purchased');
               $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
               $message->to($toemail);
             });
           }

           $tutor_assign = DB::table('tutor_assign')->where('user_id',$user->id)->first();
           $tutor_id = $tutor_assign->tutor_id;
           $student_id = $tutor_assign->student_id;
           $tutor_data = DB::table('users')->where('id',$tutor_id)->first();
           $student_data = DB::table('students')->where('student_id',$student_id)->first();
             $toemail =  $tutor_data->email;
             // dd($send);
             Mail::send('mail.purchase_credit_tutor_email',['user' =>$user,'credit'=>$new_credit, 'tutor'=>$tutor_data, 'student'=>$student_data],
             function ($message) use ($toemail)
             {
               $message->subject('Smart Cookie Tutors.com - New Credit Purchased');
               $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
               $message->to($toemail);
             });
         }else {
           $tutor_assign = DB::table('tutor_assign')->where('user_id',$user->id)->groupby('tutor_id')->get();
           foreach ($tutor_assign as $key) {
           $get_tutor_students = DB::table('tutor_assign')->where('tutor_id',$key->tutor_id)->where('user_id',$user->id)->groupby('student_id')->get();
           // dd($get_tutor_students);
           foreach ($get_tutor_students as $key2) {
             $key2->student_name = SCT::getStudentName($key2->student_id)->student_name;
           }
           // dd($tutor_assign);
           $tutor_data = DB::table('users')->where('id',$key->tutor_id)->first();
             $toemail =  $tutor_data->email;
             // dd($send);
             Mail::send('mail.client_purchase_additional_credit',['user' =>$user, 'tutor'=>$tutor_data, 'students'=>$get_tutor_students],
             function ($message) use ($toemail)
             {
               $message->subject('Smart Cookie Tutors.com - New Credit Purchased');
               $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
               $message->to($toemail);
             });
           }

         }

         $user_session = DB::table('sessions')->where('user_id',$user->id)->where('status','Insufficient Credit')->get();
         if (count($user_session) > 0) {
           foreach ($user_session as $session) {
             $tutor_id = $session->tutor_id;
             $date = $session->date;
             $time = date("h:i a" ,strtotime($session->time));
             // dd($time);
             $prev_session2 = DB::table('sessions')->where('tutor_id',$tutor_id)->where('date',$date)->where('status','confirm')->get();
             $check_session = DB::table('sessions')->where('tutor_id',$tutor_id)->where('date',$date)->where('time',$time)->where('status','Confirm')->first();
             if (count($prev_session2) > 0) {
               foreach ($prev_session2 as $prev) {
                 $check_time1 = $time;
                 $check_time2 = date("h:i a" ,strtotime($prev->time));
                 $check_time3 = DateTime::createFromFormat('H:i a', $check_time1);
                 $check_time4 = DateTime::createFromFormat('H:i a', $check_time2);
                 $prev_time = $prev->time;
                 $duration = $prev->duration;
                 if ($duration == '0:30') {
                   $new_time = date("H:i", strtotime('+30 minutes',strtotime($prev_time)));
                   $new_time2 = date("H:i", strtotime('-30 minutes',strtotime($prev_time)));
                 }elseif ($duration == '1:00') {
                   $new_time = date("H:i", strtotime('+1 hour',strtotime($prev_time)));
                   $new_time2 = date("H:i", strtotime('-1 hour',strtotime($prev_time)));
                 }elseif ($duration == '1:30') {
                   $new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($prev_time)));
                   $new_time2 = date("H:i", strtotime('-1 hour +30 minutes',strtotime($prev_time)));
                 }elseif ($duration == '2:00') {
                   $new_time = date("H:i", strtotime('+2 hours',strtotime($prev_time)));
                   $new_time2 = date("H:i", strtotime('-2 hours',strtotime($prev_time)));
                 }
                 // dd($prev_time,$new_time,$new_time2);
                 $prev_time = date("h:i a" ,strtotime($prev_time));
                 $new_time = date("h:i a" ,strtotime($new_time));
                 $new_time2 = date("h:i a" ,strtotime($new_time2));
                 $time1 = DateTime::createFromFormat('H:i a', $time);
                 $time2 = DateTime::createFromFormat('H:i a', $new_time);
                 $time3 = DateTime::createFromFormat('H:i a', $new_time2);
                 if ($time1 < $time2 && $time1 > $time3) {
                   $input2['status'] = 'Cancel';
                   DB::table('sessions')->where('session_id',$session->session_id)->where('status','Insufficient Credit')->update($input2);
                   $request->session()->flash('message', 'Thank you for your credit purchase but your previously assigned session can not reinstated due to tutor confilicting session');
               }elseif ($check_time3 == $check_time4) {
                   $status_info['status'] = 'Cancel';
                   $update_info =  DB::table('sessions')->where('session_id',$session->session_id)->update($status_info);
                   $request->session()->flash('message', 'Thank you for your credit purchase but your previously assigned session can not reinstated due to tutor confilicting session');
                 }else {
                   $input2['status'] = 'Confirm';
                   $input2['mail_status'] = '0';
                   DB::table('sessions')->where('session_id',$session->session_id)->where('status','Insufficient Credit')->update($input2);
                 }
               }
         }
         else {
           if ($session->date >= date('Y-m-d')) {
             $input2['status'] = 'Confirm';
             $input2['mail_status'] = '0';
           }elseif ($session->date < date('Y-m-d')) {
             $input2['status'] = 'Cancel';
           }
           DB::table('sessions')->where('session_id',$session->session_id)->where('status','Insufficient Credit')->update($input2);
           $request->session()->flash('message', 'Thank you for your credit purchase, your session is reinstated');
         }

           }
         }else {
           $request->session()->flash('message', 'Thank you for your credit purchase!');
         }

           // dd($stripeCharge);

           // $request->session()->flash('message', 'Thank you for your credit purchase!');
           return redirect('/user-portal/credits');
         } catch (\Exception $ex) {
           return $ex->getMessage();
         }

       }

       public function studentTutor(Request $request)
       {
         $tutors = DB::table('tutor_assign')
                  ->join('users','users.id','=','tutor_assign.tutor_id')
                  ->where('tutor_assign.user_id','=',auth()->user()->id)->orderby('users.first_name')->paginate(10);
         $tutor_mobile = DB::table('tutor_assign')
                  ->join('users','users.id','=','tutor_assign.tutor_id')
                  ->where('tutor_assign.user_id','=',auth()->user()->id)->orderby('users.first_name')->paginate(1);
                  // dd($tutor_mobile);
        return view('frontend.dashboard.tutors',compact('tutors','tutor_mobile'));
       }

       public function TutorStudents(Request $request)
       {

       	if($request->isMethod('post')){
       		$request->session()->put('TutorStudentSearch',$request->all());
       	}

       	if($request->input('reset') && $request->input('reset') == 'true'){
       		$request->session()->forget('TutorStudentSearch');
       		return redirect('user-portal/tutor-students');
       	}
         $s_app = $request->session()->get('TutorStudentSearch');
         // dd($s_app);
         if ($s_app ==null) {
           $s_app=[];
         }
         if ($s_app !=null && $s_app['searchBy'] == 'first_name') {
           $students = DB::table('tutor_assign')
                        ->join('users','users.id','=','tutor_assign.user_id')
                        ->join('students','students.student_id','=','tutor_assign.student_id')
                        ->select('users.first_name','users.last_name','students.*','tutor_assign.*')
                         ->where('tutor_assign.tutor_id','=',auth()->user()->id)
                         ->where(function ($query) use ($s_app) {
                             if(count($s_app) > 0){
                                 if($s_app['search'] != ''){
                                     $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                                 }
                             }
                         })->orderBy('first_name','asc')->paginate(15);
           $student_mobile = DB::table('tutor_assign')
                        ->join('users','users.id','=','tutor_assign.user_id')
                        ->join('students','students.student_id','=','tutor_assign.student_id')
                        ->select('users.first_name','users.last_name','students.*','tutor_assign.*')
                         ->where('tutor_assign.tutor_id','=',auth()->user()->id)
                         ->where(function ($query) use ($s_app) {
                             if(count($s_app) > 0){
                                 if($s_app['search'] != ''){
                                     $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                                 }
                             }
                         })->orderBy('first_name','asc')->paginate(1);
         }else {

         $students = DB::table('tutor_assign')
                      ->join('students','students.student_id','=','tutor_assign.student_id')
                       ->where('tutor_assign.tutor_id','=',auth()->user()->id)
                       ->where(function ($query) use ($s_app) {
                           if(count($s_app) > 0){
                               if($s_app['search'] != ''){
                                   $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                               }
                           }
                       })->orderBy('student_name','asc')->paginate(15);
         $student_mobile = DB::table('tutor_assign')
                      ->join('students','students.student_id','=','tutor_assign.student_id')
                       ->where('tutor_assign.tutor_id','=',auth()->user()->id)
                       ->where(function ($query) use ($s_app) {
                           if(count($s_app) > 0){
                               if($s_app['search'] != ''){
                                   $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                               }
                           }
                       })->orderBy('student_name','asc')->paginate(1);
                     }

                  // dd($students);
        return view('frontend.dashboard.tutor-students',compact('students','student_mobile'));
       }

       public function UnsubscribeEmail(Request $request)
       {
         if(auth()->user()->automated_email == 'Unsubscribe'){
           $request->session()->flash('message',"You have already Unsubscribed Emails");
           return redirect('/user-portal/manage-profile');
         }
         return view('frontend.dashboard.unsubscribe-email');
       }

       public function UnsubscribeEmailConfirm(Request $request)
       {
         $input['automated_email']='Unsubscribe';
         DB::table('users')->where('id',auth()->user()->id)->update($input);
         $request->session()->flash('message',"Your subscription preferences have been successfully updated");
         return redirect('/user-portal/manage-profile');
       }

       /////////////// Sessions //////////////////////////
       public function tutorSessions(Request $request)
       {
         $sessions = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date','>=',date("Y-m-d"))->orderBy('date','asc')->orderBy('time','asc')->limit(5)->get();
         foreach ($sessions as &$key) {
           $key->student_name =SCT::getStudentName($key->student_id)->student_name;
         }
         return view('frontend.dashboard.tutor_sessions',compact('sessions'));
       }

       public function get_session_data(Request $request) {

         // $sessions = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date','>=',date("Y-m-d"))->orderBy('date','asc')->get();
         $sessions = DB::table('sessions')->where('tutor_id',auth()->user()->id)->orderBy('date','asc')->orderBy('time','asc')->get();
         foreach ($sessions as &$key) {
           $key->credit =DB::table('credits')->where('user_id',$key->user_id)->first()->credit_balance;
           $key->student_name =SCT::getStudentName($key->student_id)->student_name;
             if ($key->added_by == 'Admin') {
               $tutor_timezone = $key->admin_timezone;
               $admin_timezone =SCT::getClientName($key->tutor_id)->time_zone;
               // Check session time zone and admin time zone
               if ($tutor_timezone == $admin_timezone) {
                 $time = date('h:i a', strtotime($key->time));
                 $date = date('M d, ', strtotime($key->date));
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
                   $db_time = $key->date." ".$key->time;
                   $datetime = new DateTime($db_time);
                   $time_zone =SCT::getClientName($key->tutor_id)->time_zone;
                   // dd($time_zone);
                   $db_time = $key->date." ".$key->time;
                   $datetime = new DateTime($db_time);
                   if ($time_zone == 'Pacific Time') {
                     $la_time = new \DateTimeZone('America/Los_Angeles');
                     $datetime->setTimezone($la_time);
                   }elseif ($time_zone == 'Mountain Time') {
                     $la_time = new \DateTimeZone('America/Denver');
                     $datetime->setTimezone($la_time);
                   }elseif ($time_zone == 'Central Time') {
                     $la_time = new \DateTimeZone('America/Chicago');
                     $datetime->setTimezone($la_time);
                   }elseif ($time_zone == 'Eastern Time') {
                     $la_time = new \DateTimeZone('America/New_York');
                     $datetime->setTimezone($la_time);
                   }
                   $newdatetime = $datetime->format('Y-m-d h:i a');
                   $get_datetime = explode(' ',$newdatetime);
                   $time2 = $get_datetime[1];
                   $time3 = $get_datetime[2];
                   $time = $time2." ".$time3;
               }
             }else {

               $tutor_timezone =SCT::getClientName($key->tutor_id)->time_zone;
             if ($tutor_timezone == 'Pacific Time') {
               date_default_timezone_set("America/Los_Angeles");
             }elseif ($tutor_timezone == 'Mountain Time') {
               date_default_timezone_set("America/Denver");
             }elseif ($tutor_timezone == 'Central Time') {
               date_default_timezone_set("America/Chicago");
             }elseif ($tutor_timezone == 'Eastern Time') {
               date_default_timezone_set("America/New_York");
             }
             $db_time = $key->date." ".$key->time;
             $datetime = new DateTime($db_time);
             $time_zone =SCT::getClientName($key->tutor_id)->time_zone;
             if ($time_zone == 'Pacific Time') {
               $la_time = new \DateTimeZone('America/Los_Angeles');
               $datetime->setTimezone($la_time);
             }elseif ($time_zone == 'Mountain Time') {
               $la_time = new \DateTimeZone('America/Denver');
               $datetime->setTimezone($la_time);
             }elseif ($time_zone == 'Central Time') {
               $la_time = new \DateTimeZone('America/Chicago');
               $datetime->setTimezone($la_time);
             }elseif ($time_zone == 'Eastern Time') {
               $la_time = new \DateTimeZone('America/New_York');
               $datetime->setTimezone($la_time);
             }
             $newdatetime = $datetime->format('Y-m-d h:i a');
             $get_datetime = explode(' ',$newdatetime);
             $time2 = $get_datetime[1];
             $time3 = $get_datetime[2];
             $time = $time2." ".$time3;
           }
             $time2 = date('H:i:s',strtotime($time));
             $key->time = $time;
             $key->time2 = $time2;
         }
         // dd($session);
         echo json_encode($sessions);

       }

       public function tutorSessionsDetails(Request $request,$id)
       {
         $session = DB::table('sessions')->where('session_id',$id)->where('tutor_id',auth()->user()->id)->first();
         return view('frontend.dashboard.tutor-session-details',compact('session'));
       }

       public function addEditSession(Request $request){
         $session_id = 0;
           $rPath = $request->segment(3);
           if($request->isMethod('post')){
             // dd($request->all());
             $date= $request->input('date');
             $time= $request->input('time');
             $session_id = $request->input('session_id');
             $data = $request->input('student_id');
              $data = explode(',',$data);
              $student_id = $data[0];
              $user_id = $data[1];

             $tutor_timezone = SCT::getClientName(auth()->user()->id)->time_zone;
             if ($tutor_timezone == 'Pacific Time') {
               date_default_timezone_set("America/Los_Angeles");
             }elseif ($tutor_timezone == 'Mountain Time') {
               date_default_timezone_set("America/Denver");
             }elseif ($tutor_timezone == 'Central Time') {
               date_default_timezone_set("America/Chicago");
             }elseif ($tutor_timezone == 'Eastern Time') {
               date_default_timezone_set("America/New_York");
             }
             $date1 = date('Y-m-d H:i:s', strtotime("$date $time"));
             $date2 =date("Y-m-d H:i:s");
             if ($date2 > $date1) {
               // dd($date1,$date2);
               $sMsg = 'Unable to schedule a session in the past. Please correct the session date or time and try again.';
               $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
               return redirect(url()->previous());
             }
             // Check confliting session
             $prev_session = DB::table('sessions')->where('date',$date)->where('time',$time)->where('tutor_id',auth()->user()->id)->where('session_id','<>',$session_id)->where('status','confirm')->first();
             if ($prev_session !=null) {
               $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
               $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
               return redirect(url()->previous());
             }

             // Check confliting session before end
             $prev_session2 = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date',$date)->where('session_id','<>',$session_id)->where('status','confirm')->get();
             foreach ($prev_session2 as $prev) {
               $prev_time = $prev->time;
               $prev_duration = $prev->duration;
               if ($prev_duration == '0:30') {
                 $new_time = date("H:i", strtotime('+30 minutes',strtotime($prev_time)));
               }elseif ($prev_duration == '1:00') {
                 $new_time = date("H:i", strtotime('+1 hour',strtotime($prev_time)));
               }elseif ($prev_duration == '1:30') {
                 $new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($prev_time)));
               }elseif ($prev_duration == '2:00') {
                 $new_time = date("H:i", strtotime('+2 hours',strtotime($prev_time)));
               }
               $time = date("h:i a" ,strtotime($time));
               $prev_time = date("h:i a" ,strtotime($prev_time));
               $new_time = date("h:i a" ,strtotime($new_time));
               $time1 = DateTime::createFromFormat('H:i a', $time);
               $time2 = DateTime::createFromFormat('H:i a', $prev_time);
               $time3 = DateTime::createFromFormat('H:i a', $new_time);
               if ($time1 > $time2 && $time1 < $time3) {
                 // dd($time1,$time2,$time3,"exist");
                 $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                 $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                 return redirect(url()->previous());
               }
             }
             // Check confliting session before end with other tutor
             $prev_session22 = DB::table('sessions')->where('user_id',$user_id)->where('student_id',$student_id)->where('date',$date)->where('session_id','<>',$session_id)->where('status','confirm')->get();
             foreach ($prev_session22 as $prev) {
               $prev_time = $prev->time;
               $prev_duration = $prev->duration;
               if ($prev_duration == '0:30') {
                 $new_time = date("H:i", strtotime('+30 minutes',strtotime($prev_time)));
               }elseif ($prev_duration == '1:00') {
                 $new_time = date("H:i", strtotime('+1 hour',strtotime($prev_time)));
               }elseif ($prev_duration == '1:30') {
                 $new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($prev_time)));
               }elseif ($prev_duration == '2:00') {
                 $new_time = date("H:i", strtotime('+2 hours',strtotime($prev_time)));
               }
               $time = date("h:i a" ,strtotime($time));
               $prev_time = date("h:i a" ,strtotime($prev_time));
               $new_time = date("h:i a" ,strtotime($new_time));
               $time1 = DateTime::createFromFormat('H:i a', $time);
               $time2 = DateTime::createFromFormat('H:i a', $prev_time);
               $time3 = DateTime::createFromFormat('H:i a', $new_time);
               if ($time1 >= $time2 && $time1 < $time3) {
                 // dd($time1,$time2,$time3,"exist");
                 $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                 $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                 return redirect(url()->previous());
               }
             }

             // Check confliting session before start
             $prev_session3 = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date',$date)->where('session_id','<>',$session_id)->where('status','confirm')->orderby('time','asc')->get();
             foreach ($prev_session3 as $prev) {
               $new_session_old_time = $time;
               $new_session_duration = $request->input('duration');
               $old_session_time = $prev->time;
               if ($new_session_duration == '0:30') {
                 $new_session_new_time = date("H:i", strtotime('+30 minutes',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '1:00') {
                 $new_session_new_time = date("H:i", strtotime('+1 hour',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '1:30') {
                 $new_session_new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '2:00') {
                 $new_session_new_time = date("H:i", strtotime('+2 hours',strtotime($new_session_old_time)));
               }
               $old_session_time = date("h:i a" ,strtotime($old_session_time));
               $new_session_old_time = date("h:i a" ,strtotime($new_session_old_time));
               $new_session_new_time = date("h:i a" ,strtotime($new_session_new_time));
               $time1 = DateTime::createFromFormat('H:i a', $old_session_time);
               $time2 = DateTime::createFromFormat('H:i a', $new_session_old_time);
               $time3 = DateTime::createFromFormat('H:i a', $new_session_new_time);
               if ($time1 > $time2 && $time1 < $time3) {
                 // dd($time1,$time2,$time3,"exist");
                 $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                 $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                 return redirect(url()->previous());
               }
             }
             // Check confliting session before start with other tutor
             $prev_session33 = DB::table('sessions')->where('user_id',$user_id)->where('student_id',$student_id)->where('date',$date)->where('session_id','<>',$session_id)->where('status','confirm')->orderby('time','asc')->get();
             foreach ($prev_session33 as $prev) {
               $new_session_old_time = $time;
               $new_session_duration = $request->input('duration');
               $old_session_time = $prev->time;
               if ($new_session_duration == '0:30') {
                 $new_session_new_time = date("H:i", strtotime('+30 minutes',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '1:00') {
                 $new_session_new_time = date("H:i", strtotime('+1 hour',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '1:30') {
                 $new_session_new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($new_session_old_time)));
               }elseif ($new_session_duration == '2:00') {
                 $new_session_new_time = date("H:i", strtotime('+2 hours',strtotime($new_session_old_time)));
               }
               $old_session_time = date("h:i a" ,strtotime($old_session_time));
               $new_session_old_time = date("h:i a" ,strtotime($new_session_old_time));
               $new_session_new_time = date("h:i a" ,strtotime($new_session_new_time));
               $time1 = DateTime::createFromFormat('H:i a', $old_session_time);
               $time2 = DateTime::createFromFormat('H:i a', $new_session_old_time);
               $time3 = DateTime::createFromFormat('H:i a', $new_session_new_time);
               if ($time1 > $time2 && $time1 < $time3) {
                 // dd($time1,$time2,$time3,"exist");
                 $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                 $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                 return redirect(url()->previous());
               }
             }

               // dd($student_id,$user_id);

               $input['tutor_id'] =auth()->user()->id;
               $input['student_id'] = $student_id;
               $input['user_id'] = $user_id;
               $input['subject']= $request->input('subject');
               $input['date']= $request->input('date');
               $input['time']= $request->input('time');
               $input['duration']= $request->input('duration');
               $input['location']= $request->input('location');
               $session_type = $request->input('initial_session');
               if ($session_type !='') {
                 $input['session_type']  = 'First Session';
               }else {
                 $input['session_type']  = 'Session Before';
               }
               $recurs_weekly = $request->input('recurs_weekly');
               if ($recurs_weekly !='') {
                 $input['recurs_weekly']  = 'Yes';
               }else {
                 $input['recurs_weekly']  = 'No';
               }
               $input['status']  = 'Confirm';

               $credit_agreement = DB::table('signed_aggreements')->where('user_id',$user_id)->where('status','Awaiting Signature')->first();
               if ($credit_agreement !='') {
                 $sMsg = 'You cannot schedule this session because the client has pending agreements to sign';
                 $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                 return redirect(url()->previous());
               }
               // Recurs Weekly Conflicting Check
               if ($input['recurs_weekly'] == 'Yes') {
                 for ($i=0; $i <52 ; $i++) {
                   if ($i==0) {
                     $old_date = $date;
                   }
                   $new_date = date('Y-m-d', strtotime('+7days',strtotime($old_date)));
                   // Check confliting session before end
                   $prev_session2 = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date',$new_date)->where('session_id','<>',$session_id)->where('status','confirm')->get();
                   foreach ($prev_session2 as $prev) {
                     $prev_time = $prev->time;
                     $prev_duration = $prev->duration;
                     if ($prev_duration == '0:30') {
                       $new_time = date("H:i", strtotime('+30 minutes',strtotime($prev_time)));
                     }elseif ($prev_duration == '1:00') {
                       $new_time = date("H:i", strtotime('+1 hour',strtotime($prev_time)));
                     }elseif ($prev_duration == '1:30') {
                       $new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($prev_time)));
                     }elseif ($prev_duration == '2:00') {
                       $new_time = date("H:i", strtotime('+2 hours',strtotime($prev_time)));
                     }
                     $time = date("h:i a" ,strtotime($time));
                     $prev_time = date("h:i a" ,strtotime($prev_time));
                     $new_time = date("h:i a" ,strtotime($new_time));
                     $time1 = DateTime::createFromFormat('H:i a', $time);
                     $time2 = DateTime::createFromFormat('H:i a', $prev_time);
                     $time3 = DateTime::createFromFormat('H:i a', $new_time);
                     if ($time1 >= $time2 && $time1 < $time3) {
                       // dd($time1,$time2,$time3,"exist");
                       $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                       $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                       return redirect(url()->previous());
                     }
                   }

                   // Check confliting session before start
                   $prev_session3 = DB::table('sessions')->where('tutor_id',auth()->user()->id)->where('date',$new_date)->where('session_id','<>',$session_id)->where('status','confirm')->orderby('time','asc')->get();
                   foreach ($prev_session3 as $prev) {
                     $new_session_old_time = $time;
                     $new_session_duration = $request->input('duration');
                     $old_session_time = $prev->time;
                     if ($new_session_duration == '0:30') {
                       $new_session_new_time = date("H:i", strtotime('+30 minutes',strtotime($new_session_old_time)));
                     }elseif ($new_session_duration == '1:00') {
                       $new_session_new_time = date("H:i", strtotime('+1 hour',strtotime($new_session_old_time)));
                     }elseif ($new_session_duration == '1:30') {
                       $new_session_new_time = date("H:i", strtotime('+1 hour +30 minutes',strtotime($new_session_old_time)));
                     }elseif ($new_session_duration == '2:00') {
                       $new_session_new_time = date("H:i", strtotime('+2 hours',strtotime($new_session_old_time)));
                     }
                     $old_session_time = date("h:i a" ,strtotime($old_session_time));
                     $new_session_old_time = date("h:i a" ,strtotime($new_session_old_time));
                     $new_session_new_time = date("h:i a" ,strtotime($new_session_new_time));
                     $time1 = DateTime::createFromFormat('H:i a', $old_session_time);
                     $time2 = DateTime::createFromFormat('H:i a', $new_session_old_time);
                     $time3 = DateTime::createFromFormat('H:i a', $new_session_new_time);
                     if ($time1 > $time2 && $time1 < $time3) {
                       // dd($time1,$time2,$time3,"exist");
                       $sMsg = 'Unable to schedule session due to one or more conflicting sessions. Please correct the session date or time and try again.';
                       $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                       return redirect(url()->previous());
                     }
                   }
                   $old_date = $new_date;
                }
               }


               if($session_id == ''){
                     $credit_balance='';
                     $check_credit = DB::table('credits')->where('user_id',$user_id)->first();
                     if ($check_credit !=null) {
                       $credit_balance = $check_credit->credit_balance;
                     }

                     if ($credit_balance !='' && $credit_balance > 0) {
                       $session_id = DB::table('sessions')->insertGetId($input);
                       $get_session2 = DB::table('sessions')->where('session_id',$session_id)->first();
                       if ($recurs_weekly !='') {
                       for ($i=0; $i <52 ; $i++) {
                         if ($i==0) {
                           $old_date = $get_session2->date;
                         }
                         $new_date = date('Y-m-d', strtotime('+7days',strtotime($old_date)));
                         $input3['tutor_id'] = $get_session2->tutor_id;
                         $input3['student_id'] = $get_session2->student_id;
                         $input3['user_id'] = $get_session2->user_id;
                         $input3['subject']=$get_session2->subject;
                         $input3['date']= $new_date;
                         $input3['time']= $get_session2->time;
                         $input3['duration']= $get_session2->duration;
                         $input3['location']= $get_session2->location;
                         $input3['session_type'] = $get_session2->session_type;
                         $input3['recurs_weekly'] = $get_session2->recurs_weekly;
                         $input3['status']  = 'Confirm';
                         $recurs_weekly_session = DB::table('sessions')->insert($input3);
                        $old_date = $new_date;
                       }
                     }

                     }else {
                       $sMsg = 'You cannot schedule this session because the client has 0 credits remaining';
                       $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                       return redirect(url()->previous());
                     }
                     $get_session = DB::table('sessions')->where('session_id',$session_id)->first();
                     $tutor_timezone = SCT::getClientName($get_session->tutor_id)->time_zone;
                     if ($tutor_timezone == 'Pacific Time') {
                       date_default_timezone_set("America/Los_Angeles");
                     }elseif ($tutor_timezone == 'Mountain Time') {
                       date_default_timezone_set("America/Denver");
                     }elseif ($tutor_timezone == 'Central Time') {
                       date_default_timezone_set("America/Chicago");
                     }elseif ($tutor_timezone == 'Eastern Time') {
                       date_default_timezone_set("America/New_York");
                     }
                     $date = $get_session->date;
                     $time = $get_session->time;

                     $combinedDT = date('Y-m-d H:i:s', strtotime("$get_session->date $get_session->time"));
                     $date1 =date("Y-m-d H:i");
                     $date2 = date("Y-m-d H:i", strtotime('-30 hours',strtotime($combinedDT)));
                     // dd($date1,$date2);
                     if ($date1 >= $date2) {
                       $user_data = DB::table('users')->where('id',$get_session->user_id)->first();
                       $tutor = DB::table('users')->where('id',$get_session->tutor_id)->first();
                       $student = DB::table('students')->where('student_id',$get_session->student_id)->first();
                       if ($user_data->automated_email == 'Subscribe') {
                       $toemail=$user_data->email;
                         Mail::send('mail.last_minute_session_email',['user' =>$user_data,'tutor'=>$tutor,'student'=>$student,'session'=>$get_session],
                         function ($message) use ($toemail)
                         {

                           // $message->subject('Smart Cookie Tutors.com - Last Minute Session '.date('H:i:s'));
                           $message->subject('Smart Cookie Tutors.com - Last Minute Session');
                           $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                           $message->to($toemail);
                         });
                     }
                   }

                     $sMsg = 'New Session Added';

               }else{
                     $credit_balance='';
                     $check_credit = DB::table('credits')->where('user_id',$user_id)->first();

                     if ($check_credit !=null) {
                       $credit_balance = $check_credit->credit_balance;
                     }
                     if ($credit_balance !='' && $credit_balance > 0) {
                       if ($input['recurs_weekly']  == 'Yes') {
                         $get_session_edit = DB::table('sessions')->where('session_id',$session_id)->first();
                         if ($get_session_edit->recurs_weekly == "No") {
                           for ($i=0; $i <52 ; $i++) {
                             if ($i==0) {
                               $old_date = $get_session_edit->date;
                             }
                             $new_date = date('Y-m-d', strtotime('+7days',strtotime($old_date)));
                             $input3['tutor_id'] = $get_session_edit->tutor_id;
                             $input3['student_id'] = $get_session_edit->student_id;
                             $input3['user_id'] = $get_session_edit->user_id;
                             $input3['subject']=$input['subject'];
                             $input3['date']= $new_date;
                             $input3['time']= $input['time'];
                             $input3['duration']= $input['duration'];
                             $input3['location']= $input['location'];
                             $input3['session_type'] = $input['location'];
                             $input3['recurs_weekly'] = $input['recurs_weekly'];
                             $input3['status']  = 'Confirm';
                             $recurs_weekly_session = DB::table('sessions')->insert($input3);
                             $old_date = $new_date;
                           }
                         }
                         // dd($get_session_edit);
                       }
                       $session_id = DB::table('sessions')->where('session_id',$session_id)->update($input);
                     }else {
                       $sMsg = 'You cannot schedule this session because the client has 0 credits remaining';
                       $request->session()->flash('alert',['message' => $sMsg, 'type' => 'danger']);
                       return redirect(url()->previous());
                     }
                       $sMsg = 'Session Updated';
               }
               $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
               return redirect('user-portal/tutor-sessions');
           }else{
               $session = array();
               $session_id = '0';
               if($rPath == 'edit'){
                   $session_id = $request->segment(4);
                   $session = DB::table('sessions')->where('session_id',$session_id)->first();
                   // dd($session);
                   if($session == null){
                       $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                       return redirect('user-portal/tutor-sessions');
                   }
                   // dd($student);
               }
               $assign_students = DB::table('tutor_assign')
                        ->join('students','students.student_id','=','tutor_assign.student_id')
                        ->where('tutor_assign.tutor_id','=',auth()->user()->id)->orderBy('student_name','asc')->get();
               return view('frontend.dashboard.add-edit-sessions',compact('session','rPath','session_id','assign_students'));
           }
       }

       public function CheckInitialSession(Request $request, $student_id, $tutor_id)
       {
         $check = DB::table('sessions')->where('tutor_id',$tutor_id)->where('student_id',$student_id)->first();
         if ($check == null) {
           return 0;
         }else {
           return 1;
         }
       }

       public function EndSession(Request $request)
       {
         // dd($request->all());
         $session_id = $request->input('session_id');
         $duration = $request->input('duration');
         $session_data = DB::table('sessions')->where('session_id',$session_id)->first();
         $user_id = $session_data->user_id;
         $student_id = $session_data->student_id;
         $data['status'] = 'End';
         $data['duration'] = $duration;
         $session = DB::table('sessions')->where('session_id',$session_id)->update($data);
         echo $session;
       }

       public function CancelSession(Request $request){
         // dd($request->all());
         if($request->isMethod('delete')){
           $session_id = trim($request->input('session_id'));
           $notify_client ='';
           $notify_client = $request->input('notify_client');
           $type = $request->input('type');
           $session_details = DB::table('sessions')->where('session_id',$session_id)->first();
           if ($notify_client !='') {
             $user = DB::table('users')->where('id',$session_details->user_id)->first();
             $tutor = DB::table('users')->where('id',$session_details->tutor_id)->first();
             $student = DB::table('students')->where('student_id',$session_details->student_id)->first();
             // dd($student);
             if ($user->automated_email == 'Subscribe') {
               $toemail =  $user->email;
               Mail::send('mail.tutor_cancel_session_email',['user' =>$user,'tutor' =>$tutor,'student' =>$student,'session'=>$session_details],
               function ($message) use ($toemail)
               {
                 // $message->subject('Smart Cookie Tutors.com - Session Cancelled '.date('H:i:s'));
                 $message->subject('Smart Cookie Tutors.com - Session Cancelled');
                 $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                 $message->to($toemail);
               });
             }
           }
           $recurs_weekly = $session_details->recurs_weekly;
           $date = $session_details->date;
           $time = $session_details->time;
           $subject = $session_details->subject;
           $duration = $session_details->duration;
           $date = $session_details->date;
           $tutor_id = $session_details->tutor_id;
           $student_id = $session_details->student_id;
           if ($type == 'cancel_all') {
           if ($recurs_weekly == 'Yes') {
             $get_recurs_weekly_session = DB::table('sessions')->where('tutor_id',$tutor_id)
             ->where('student_id',$student_id)->where('time',$time)->where('subject',$subject)
             ->where('duration',$duration)->where('date','>',$date)->delete();
           }
         }
         $session = DB::table('sessions')->where('session_id',$session_id)->delete();


           $request->session()->flash('message' , 'Session Cancelled Successfully');
         }
         return redirect('user-portal/tutor-sessions');
       }

       public function clientSessions(Request $request)
       {
         $sessions = DB::table('sessions')->where('user_id',auth()->user()->id)->where('date','>=',date("Y-m-d"))->orderBy('date','asc')->orderBy('time','asc')->limit(2)->get();
         foreach ($sessions as &$key) {
           $key->student_name =SCT::getStudentName($key->student_id)->student_name;
         }
         return view('frontend.dashboard.client_sessions',compact('sessions'));
       }

       public function get_clientSession_data(Request $request) {
         // $sessions = DB::table('sessions')->where('user_id',auth()->user()->id)->where('date','>=',date("Y-m-d"))->get();
         $sessions = DB::table('sessions')->where('user_id',auth()->user()->id)->orderBy('date','asc')->orderBy('time','asc')->get();
         foreach ($sessions as &$key) {
           $key->credit =DB::table('credits')->where('user_id',$key->user_id)->first()->credit_balance;
           $key->student_name =SCT::getStudentName($key->student_id)->student_name;
           // Change Time Zone
           if ($key->added_by == 'Admin') {
             $tutor_timezone = $key->admin_timezone;
             $admin_timezone =SCT::getClientName($key->tutor_id)->time_zone;
             // Check session time zone and admin time zone
             if ($tutor_timezone == $admin_timezone) {
               $time = date('h:i a', strtotime($key->time));
               $date = date('M d, ', strtotime($key->date));
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
                 $db_time = $key->date." ".$key->time;
                 $datetime = new DateTime($db_time);
                 $time_zone =SCT::getClientName($key->user_id)->time_zone;
                 // dd($time_zone);
                 $db_time = $key->date." ".$key->time;
                 $datetime = new DateTime($db_time);
                 if ($time_zone == 'Pacific Time') {
                   $la_time = new \DateTimeZone('America/Los_Angeles');
                   $datetime->setTimezone($la_time);
                 }elseif ($time_zone == 'Mountain Time') {
                   $la_time = new \DateTimeZone('America/Denver');
                   $datetime->setTimezone($la_time);
                 }elseif ($time_zone == 'Central Time') {
                   $la_time = new \DateTimeZone('America/Chicago');
                   $datetime->setTimezone($la_time);
                 }elseif ($time_zone == 'Eastern Time') {
                   $la_time = new \DateTimeZone('America/New_York');
                   $datetime->setTimezone($la_time);
                 }
                 $newdatetime = $datetime->format('Y-m-d h:i a');
                 $get_datetime = explode(' ',$newdatetime);
                 $time2 = $get_datetime[1];
                 $time3 = $get_datetime[2];
                 $time = $time2." ".$time3;
             }
         }else {
           $tutor_timezone = SCT::getClientName($key->tutor_id)->time_zone;
           if ($tutor_timezone == 'Pacific Time') {
             date_default_timezone_set("America/Los_Angeles");
           }elseif ($tutor_timezone == 'Mountain Time') {
             date_default_timezone_set("America/Denver");
           }elseif ($tutor_timezone == 'Central Time') {
             date_default_timezone_set("America/Chicago");
           }elseif ($tutor_timezone == 'Eastern Time') {
             date_default_timezone_set("America/New_York");
           }
           $db_time = $key->date." ".$key->time;
           $datetime = new DateTime($db_time);
           $time_zone =SCT::getClientName($key->user_id)->time_zone;
           if ($time_zone == 'Pacific Time') {
             $la_time = new \DateTimeZone('America/Los_Angeles');
             $datetime->setTimezone($la_time);
           }elseif ($time_zone == 'Mountain Time') {
             $la_time = new \DateTimeZone('America/Denver');
             $datetime->setTimezone($la_time);
           }elseif ($time_zone == 'Central Time') {
             $la_time = new \DateTimeZone('America/Chicago');
             $datetime->setTimezone($la_time);
           }elseif ($time_zone == 'Eastern Time') {
             $la_time = new \DateTimeZone('America/New_York');
             $datetime->setTimezone($la_time);
           }
           $newdatetime = $datetime->format('Y-m-d h:i a');
           // dd($newdatetime);
           $get_datetime = explode(' ',$newdatetime);
           $time2 = $get_datetime[1];
           $time3 = $get_datetime[2];
           $time = $time2." ".$time3;
         }
           $time2 = date('H:i:s',strtotime($time));
           $key->time = $time;
           $key->time2 = $time2;
         }
         // dd($sessions);
         echo json_encode($sessions);
       }
       public function ClientSessionsDetails(Request $request,$id)
       {
         $session = DB::table('sessions')->where('session_id',$id)->where('user_id',auth()->user()->id)->first();
         return view('frontend.dashboard.client-session-details',compact('session'));
       }

       public function ClientCancelSession(Request $request){
         // dd($request->all());
         if($request->isMethod('post')){
           $session_id = trim($request->input('session_id'));
           $input['status'] = 'Cancel';
           $reason = $request->input('reason');
           $input['reason'] = $reason;

             $session_details = DB::table('sessions')->where('session_id',$session_id)->first();
             $user = DB::table('users')->where('id',$session_details->user_id)->first();
             $tutor = DB::table('users')->where('id',$session_details->tutor_id)->first();
             $student = DB::table('students')->where('student_id',$session_details->student_id)->first();
             // dd($student);
               $toemail =  $tutor->email;
               Mail::send('mail.client_cancel_session_email',['user' =>$user,'tutor' =>$tutor,'student' =>$student,'session'=>$session_details, 'reason'=> $reason],
               function ($message) use ($toemail)
               {
                 // $message->subject('Smart Cookie Tutors.com - Session Cancelled '.date('H:i:s'));
                 $message->subject('Smart Cookie Tutors.com - Session Cancelled');
                 $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                 $message->to($toemail);
               });

           $session = DB::table('sessions')->where('session_id',$session_id)->update($input);
           $request->session()->flash('message' , 'Session Cancelled Successfully');
         }
         return redirect('user-portal/client-sessions');
       }

       public function checkCredit($user_id)
       {
         $user = DB::table('credits')->where('user_id',$user_id)->first();
         // echo $user;
         echo json_encode($user);
       }

       public function tutorTimesheets(Request $request)
       {
         $timesheets = DB::table('timesheets')->where('tutor_id',auth()->user()->id)->orderBy('created_at','desc')->paginate(15);
         return view('frontend.dashboard.tutor_timesheets',compact('timesheets'));
       }
       public function getTimesheetData(Request $request) {
         $timesheets = DB::table('timesheets')->where('tutor_id',auth()->user()->id)->get();
         foreach ($timesheets as $timesheet ) {
           $endtime='';
           if ($timesheet->duration == '0:30') {
             $endtime = date('H:i:s', strtotime('+30 minutes',strtotime($timesheet->time)));
           }elseif ($timesheet->duration == '1:00') {
             $endtime = date('H:i:s', strtotime('+1 hour',strtotime($timesheet->time)));
           }elseif ($timesheet->duration == '1:30') {
             $endtime = date('H:i:s', strtotime('+1 hour +30 minutes',strtotime($timesheet->time)));
           }elseif ($timesheet->duration == '2:00') {
             $endtime = date('H:i:s', strtotime('+2 hour',strtotime($timesheet->time)));
           }
           $timesheet->endtime =$endtime;
           // $timesheet->date2 = date('M d, Y', strtotime($timesheet->date));
           $timesheet->student_name =SCT::getStudentName($timesheet->student_id)->student_name;

           // dd($endtime);

         }
         // dd($timesheets);
         echo json_encode($timesheets);
       }

       public function addEditTimeSheet(Request $request){
         $get_date =$request->input('date');
         $date2 = explode('T',$get_date);
         $date = $date2[0];
         $time='';
         if(count($date2)>1){
           $time = $date2[1];
         }
         // dd($date,$time);


         $timesheet_id = 0;
           $rPath = $request->segment(3);
           // dd($rPath);
           if($request->isMethod('post')){
             // dd($request->all());
              $timesheet_id = $request->input('timesheet_id');
              $data = $request->input('student_id');
               $data = explode(',',$data);
               $student_id = $data[0];
               $user_id = $data[1];
               $duration= $request->input('duration');
               $duration3='';
               // dd($student_id,$user_id);

               $input['tutor_id'] =auth()->user()->id;
               $input['student_id'] = $student_id;
               $input['user_id'] = $user_id;
               $input['date']= $request->input('date');
               $input['time']= $request->input('time');
               $input['duration']= $duration;
               if ($duration == '0:30') {
                 $duration3 = 0.5;
               }elseif ($duration == '1:00') {
                 $duration3 = 1;
               }elseif ($duration == '1:30') {
                 $duration3 = 1.5;
               }elseif ($duration == '2:00') {
                 $duration3 = 2;
               }
               $input['duration2']= $duration3;
               // dd($input);
               $input['description']= $request->input('description');
               $pay_rate = SCT::getAssignCost(auth()->user()->id,$student_id)->hourly_pay_rate;
               $input['hourly_pay_rate']= $pay_rate;
               if($timesheet_id == ''){
                     $duration = $request->input('duration');
                     $credit = DB::table('credits')->where('user_id',$user_id)->first();
                     $credit_balance = $credit->credit_balance;
                     if ($duration == '0:30') {
                       $credit_balance = $credit_balance-0.5;
                     }elseif ($duration == '1:00') {
                       $credit_balance = $credit_balance-1;
                     }elseif ($duration == '1:30') {
                       $credit_balance = $credit_balance-1.5;
                     }elseif ($duration == '2:00') {
                       $credit_balance = $credit_balance-2;
                     }
                     $timesheet_id = DB::table('timesheets')->insertGetId($input);
                     $input2['credit_balance'] = $credit_balance;
                     $update_credit = DB::table('credits')->where('user_id',$user_id)->update($input2);
                     $user = DB::table('users')->where('id',$user_id)->first();
                     if ($user->automated_email == 'Subscribe') {
                       if ($credit_balance <= 0) {
                       $toemail =  $user->email;
                       // dd($send);
                       Mail::send('mail.end_credits_email',['user' =>$user,'credit_balance'=>$credit_balance],
                       function ($message) use ($toemail)
                       {

                         $message->subject('Smart Cookie Tutors.com - Credit Balance');
                         $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                         $message->to($toemail);
                       });
                     }elseif ($credit_balance == 0.5) {
                       $toemail =  $user->email;
                       // dd($send);
                       Mail::send('mail.half_hour_credits_email',['user' =>$user,'credit_balance'=>$credit_balance],
                       function ($message) use ($toemail)
                       {

                         $message->subject('Smart Cookie Tutors.com - Credit Balance');
                         $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                         $message->to($toemail);
                       });
                     }

                   }
                     // dd($credit);
                     $sMsg = 'New Timesheet Added';

               }else{
                 $duration = $request->input('duration');
                 $timesheet_data = DB::table('timesheets')->where('timesheet_id',$timesheet_id)->first();
                 $duration2 = $timesheet_data->duration;
                 $credit = DB::table('credits')->where('user_id',$user_id)->first();
                 $credit_balance = $credit->credit_balance;

                 if ($duration != $duration2) {
                   if ($duration == '0:30') {
                     $duration = 0.5;
                   }elseif ($duration == '1:00') {
                     $duration = 1;
                   }elseif ($duration == '1:30') {
                     $duration = 1.5;
                   }elseif ($duration == '2:00') {
                     $duration = 2;
                   }

                   if ($duration2 == '0:30') {
                     $duration2 = 0.5;
                   }elseif ($duration2 == '1:00') {
                     $duration2 = 1;
                   }elseif ($duration2 == '1:30') {
                     $duration2 = 1.5;
                   }elseif ($duration2 == '2:00') {
                     $duration2 = 2;
                   }
                 }

                 if ($duration > $duration2) {
                   // $duration3 = $duration-$duration2;
                   $duration = $duration-$duration2;
                   // dd($duration.' new duration',$duration2.' old duration',$duration3);
                   if ($duration == 0.5) {
                     $credit_balance = $credit_balance-0.5;
                   }elseif ($duration == 1) {
                     $credit_balance = $credit_balance-1;
                   }elseif ($duration == 1.5) {
                     $credit_balance = $credit_balance-1.5;
                   }elseif ($duration == 2) {
                     $credit_balance = $credit_balance-2;
                   }
                 }elseif ($duration < $duration2) {
                   // $duration3 = $duration2-$duration;
                   $duration = $duration2-$duration;
                   // dd($duration.' new duration',$duration2.' old duration',$duration3);
                   if ($duration == 0.5) {
                     $credit_balance = $credit_balance+0.5;
                   }elseif ($duration == 1) {
                     $credit_balance = $credit_balance+1;
                   }elseif ($duration == 1.5) {
                     $credit_balance = $credit_balance+1.5;
                   }elseif ($duration == 2) {
                     $credit_balance = $credit_balance+2;
                   }
                 }

                     $timesheet_id = DB::table('timesheets')->where('timesheet_id',$timesheet_id)->update($input);
                     $input2['credit_balance'] = $credit_balance;
                     $update_credit = DB::table('credits')->where('user_id',$user_id)->update($input2);
                     $user = DB::table('users')->where('id',$user_id)->first();
                     if ($user->automated_email == 'Subscribe') {
                       if ($credit_balance <= 0) {
                       $toemail =  $user->email;
                       // dd($send);
                       Mail::send('mail.end_credits_email',['user' =>$user,'credit_balance'=>$credit_balance],
                       function ($message) use ($toemail)
                       {

                         $message->subject('Smart Cookie Tutors.com - Credit Balance');
                         $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                         $message->to($toemail);
                       });
                     }elseif ($credit_balance == 0.5) {
                       $toemail =  $user->email;
                       // dd($send);
                       Mail::send('mail.half_hour_credits_email',['user' =>$user,'credit_balance'=>$credit_balance],
                       function ($message) use ($toemail)
                       {

                         $message->subject('Smart Cookie Tutors.com - Credit Balance');
                         $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
                         $message->to($toemail);
                       });
                     }

                   }
                       $sMsg = 'Session Updated';
               }
               $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
               return redirect('user-portal/tutor-timesheets');
           }else{
               $timesheet = array();
               $timesheet_id = '0';
               if($rPath == 'edit'){
                   $timesheet_id = $request->segment(4);
                   $timesheet = DB::table('timesheets')->where('timesheet_id',$timesheet_id)->first();
                   // dd($timesheet);
                   if($timesheet_id == null){
                       $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                       return redirect('user-portal/tutor-timesheets');
                   }
                   // dd($student);
               }
               $assign_students = DB::table('tutor_assign')
                        ->join('students','students.student_id','=','tutor_assign.student_id')
                        ->where('tutor_assign.tutor_id','=',auth()->user()->id)->orderBy('student_name','asc')->get();
                $session_details = DB::table('sessions')->where('session_id',1)->first();
                // dd($session_details);
               return view('frontend.dashboard.add-edit-timesheets',compact('timesheet','rPath','timesheet_id','assign_students','session_details','date','time'));
           }
       }

       public function tutorTimesheetDetails(Request $request,$id)
       {
         $timesheet = DB::table('timesheets')->where('timesheet_id',$id)->where('tutor_id',auth()->user()->id)->first();
         return view('frontend.dashboard.tutor-timesheet-details',compact('timesheet'));
       }

       public function deleteTimesheet(Request $request)
       {
         if($request->isMethod('delete')){
          $timesheet_id = trim($request->input('timesheet_id'));
          $timesheet_data = DB::table('timesheets')->where('timesheet_id',$timesheet_id)->first();
          // dd($timesheet_data);
          $user_id = $timesheet_data->user_id;
          $duration = $timesheet_data->duration;
          $credit = DB::table('credits')->where('user_id',$user_id)->first();
          $credit_balance = $credit->credit_balance;
          if ($duration == '0:30') {
            $credit_balance = $credit_balance+0.5;
          }elseif ($duration == '1:00') {
            $credit_balance = $credit_balance+1;
          }elseif ($duration == '1:30') {
            $credit_balance = $credit_balance+1.5;
          }elseif ($duration == '2:00') {
            $credit_balance = $credit_balance+2;
          }
          $input2['credit_balance'] = $credit_balance;
          // dd($input2);
          $update_credit = DB::table('credits')->where('user_id',$user_id)->update($input2);
           $timesheet = DB::table('timesheets')->where('timesheet_id',$timesheet_id)->delete();
          $request->session()->flash('message' , 'Timesheet Deleted Successfully');
        }
        return redirect('/user-portal/tutor-timesheets');
       }

       public function SendEmail(Request $request)
       {
         // dd("Asd");
         $reason = "Reason";
         $session_details = DB::table('sessions')->where('session_id',113)->first();
         $user = DB::table('users')->where('id',2)->first();
         $tutor = DB::table('users')->where('id',10)->first();
         $student = DB::table('students')->where('student_id',12)->first();
         $get_tutor_students = DB::table('tutor_assign')->where('tutor_id',10)->where('user_id',2)->groupby('student_id')->get();
         // dd($get_tutor_students);
         $toemail='mwaqas.peek@gmail.com';
         // $toemail='nabeelirbab@gmail.com';
         // $toemail='toseef3@gmail.com';
         Mail::send('mail.client_cancel_session_email',['user' =>$user,'tutor' =>$tutor,'student' =>$student,'session'=>$session_details, 'reason'=> $reason],
         function ($message) use ($toemail)
         {
           // $message->subject('Smart Cookie Tutors.com - New Credit Purchased'.date('H:i:s'));
           $message->subject('Smart Cookie Tutors.com - New Credit Purchased');
           $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
           $message->to($toemail);
         });

   }

}
