<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Facade\SCT;
use App\User;
use App\Student;
use Hash;
use DB;
use Mail;
use Session;
use Storage;
use Response;

class AdminController extends Controller
{

    use AuthenticatesUsers;
    protected $redirectTo = '/dashboard';

    // public function __construct()
    // {
    //     $this->middleware('admin')->except('logout');
    // }

    public function accountLogin(Request $request){
       return view('frontend.login');
    }

    public function username(){
        return 'email';
    }

    // public function admin_login(Request $request)
    // {
    // if($request->isMethod('post')){
    //   // dd($request->all());
    //   $this->validate($request, [
    //     'email' => 'required',
    //     'password' => 'required',
    //   ]);
    //
    //   $user_data = array(
    //     'email'  => $request->get('email'),
    //     'password' => $request->get('password'),
    //     'role' => 'admin'
    //   );
    //
    //   if(!Auth::attempt($user_data)){
    //     // $fNotice = 'Please check your mobile for verification code';
    //     $request->session()->flash('loginAlert', 'Invalid Email & Password');
    //     return redirect('admin/login');
    //   }
    //   if ( Auth::check() ) {
    //     // dd(Auth::user());
    //   }
    //   return redirect('dashboard/view_customers');
    //   }
    //   return view('admin.login-page');
    // }

    public function admin_login(Request $request)
   {
        if ($request->session()->exists('sct_admin')) {
           return redirect('/dashboard/view_customers');
       }


   if($request->isMethod('post')){

          $email = $request->input('email');
           // $password = Hash::make(trim($request->input('password')));
           $password = trim($request->input('password'));
           // dd($password);
     $user = $this->doLogin($email,$password);
     if($user == 'invalid'){
       $request->session()->flash('loginAlert', 'Invalid Email & Password');

         return redirect('admin/login');

     }
     else{

       $request->session()->put('sct_admin', $user);



         return redirect('dashboard/view_customers');

     }


   }
       return view('/admin.login-page');
   }

   public function doLogin($email,$password){
       /* do login */
       // dd($password);
       $user = DB::table('users')->where('email','=',$email)->where('role','admin')->first();
       // dd($user);
       if(empty($user)){
           return 'invalid';
       }else{
         if (!Hash::check($password, $user->password)) {
           return 'invalid';
         }else {
           // dd($user);
           return $user;
         }
       }
   /* end */
 }

 public function logout(Request $request){
      // Session::flush();
      Session::forget('sct_admin');
       // Auth::logout();
       return redirect('admin/login');
 }


    public function all_admin(Request $request)
    {
      $all_admin = User::where('role','admin')->orderBy('id','desc')->get();
       return view('admin.view_admin',compact('all_admin'));
    }

    public function addEditAdmin(Request $request){
      // dd($request->all());
      $adminId = 0;
        $rPath = $request->segment(3);
        if($request->isMethod('post')){
            $adminId = $request->input('admin_id');
            $this->validate($request, [
                'first_name' => 'required|max:100',
                'email' => 'required|email|max:255',
            ]);
            if($adminId == 0 || $adminId ==null){

                $this->validate($request, [
                    'password' => 'required|min:6|max:16',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required',
                    // 'password' => 'required|min:6|regex:/[a-z]/|regex:/[0-9]/',
                  ],[
                    // 'password.regex'=> 'Password must contain 1 character from a-z, 1 digit from 0-9',
                  ]);
            }
            $admin =new User;
            $admin->first_name = $request->input('first_name');
            $admin->email = $request->input('email');
            $admin->role = $request->input('role');
            $admin->status = 'active';

            if($request->input('password') != '' && $request->input('password') != NULL){
                $admin->password =Hash::make(trim($request->input('password')));
            }
            if($adminId == ''){
                $adminId = $admin->save();
                $sMsg = 'New Admin Added';
            }else{
              $admin='';
              $admin = User::findOrFail($adminId);
              $admin->first_name = $request->input('first_name');
              $admin->email = $request->input('email');
              $admin->role = $request->input('role');
              $admin->status = 'active';
              $admin->save();
                $sMsg = 'Admin Updated';
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/view_admins');
        }else{
            $admin = array();
            $adminId = '0';
            if($rPath == 'edit'){
                $adminId = $request->segment(4);
                $admin = User::findOrFail($adminId);
                // dd($user);
                if($admin == null){
                    $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                    return redirect('dashboard/view_admins');
                }
            }
            return view('admin.add-edit-admins',compact('admin','rPath','adminId'));
        }
    }

    public function deleteAdmin(Request $request)
    {
      if($request->isMethod('delete')){
    		$admin_id = trim($request->input('admin_id'));
        $admin = User::find($admin_id);
        $admin->delete();
    		$request->session()->flash('message' , 'Admin Deleted Successfully');
    	}
    	return redirect(url()->previous());
    }

    public function all_customers(Request $request)
    {
      // dd($request->all());
      $app = session()->get('sct_admin');
      if ($app =="") {
        return redirect('/admin');
      }
    	if($request->isMethod('post')){
    		$request->session()->put('clientsSearch',$request->all());
    	}

    	if($request->input('reset') && $request->input('reset') == 'true'){
    		$request->session()->forget('clientsSearch');
    		return redirect('dashboard/view_customers');
    	}
      $s_app = $request->session()->get('clientsSearch');
      if ($s_app ==null) {
        $s_app=[];
      }
      // dd($s_app);
    $all_customer = DB::table('users')->where('role','=','customer')
                  ->where(function ($query) use ($s_app) {
                      if(count($s_app) > 0){
                          if($s_app['search'] != ''){
                              $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                          }
                      }
                  })->orderBy('first_name','asc')->paginate(15);

      return view('admin.view_customers',compact('all_customer'));
    }


    public function addEditCustomer(Request $request){
      // dd($request->all());
      $customerId = 0;
        $rPath = $request->segment(3);
        if($request->isMethod('post')){
            $customerId = $request->input('customer_id');
            $this->validate($request, [
                'first_name' => 'required|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
            ]);
            if($customerId == 0 || $customerId ==null){

                $this->validate($request, [
                    'password' => 'required|min:6|max:16',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required',
                    // 'password' => 'required|min:6|regex:/[a-z]/|regex:/[0-9]/',
                  ],[
                    // 'password.regex'=> 'Password must contain 1 character from a-z, 1 digit from 0-9',
                  ]);
            }
            $customer =new User;
            $customer->first_name = $request->input('first_name');
            $customer->last_name = $request->input('last_name');
            $customer->email = $request->input('email');
            $customer->phone = $request->input('phone');
            $customer->address = $request->input('address');
            $customer->role = 'customer';
            $customer->status = 'active';

            if($request->input('password') != '' && $request->input('password') != NULL){
                $customer->password =Hash::make(trim($request->input('password')));
            }
            $credit_cost = $request->input('credit_cost');
            $credit_balance = $request->input('credit_balance');

            if($customerId == ''){
                $customerId = $customer->save();
                $sMsg = 'New Customer Added';
            }else{
              $customer='';
              $customer = User::findOrFail($customerId);
              $customer->first_name = $request->input('first_name');
              $customer->last_name = $request->input('last_name');
              $customer->email = $request->input('email');
              $customer->phone = $request->input('phone');
              $customer->address = $request->input('address');
              $customer->city = $request->input('city');
              $customer->state = $request->input('state');
              $customer->zip = $request->input('zip');
              $customer->role = 'customer';
              $customer->status = 'active';
              if($request->input('password') != '' && $request->input('password') != NULL){
                  $customer->password =Hash::make(trim($request->input('password')));
              }
              $customer->save();
                $sMsg = 'Customer Updated';
                $credit_id = $request->input('credit_id');
                if ($credit_cost !='') {
                  $input['credit_cost'] = $request->input('credit_cost');
                  $input['user_id'] = $customerId;
                  $input['credit_balance'] = $request->input('credit_balance');
                  if ($credit_id !='') {
                    DB::table('credits')->where('credit_id',$credit_id)->update($input);

                  }else {
                    DB::table('credits')->insert($input);
                  }
                }
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/view_customers');
        }else{
            $customer = array();
            $customerId = '0';
            if($rPath == 'edit'){
                $customerId = $request->segment(4);
                $customer = User::findOrFail($customerId);
                $credit = DB::table('credits')->where('user_id',$customerId)->first();
                // dd($credit);
                if($customer == null){
                    $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                    return redirect('dashboard/view_customers');
                }
            }
            return view('admin.add-edit-customers',compact('customer','rPath','customerId','credit'));
        }
    }

    public function deleteCustomer(Request $request)
    {
      if($request->isMethod('delete')){
        $customer_id = trim($request->input('customer_id'));
        $customer = User::find($customer_id);
        $customer->delete();
        $request->session()->flash('message' , 'Customer Deleted Successfully');
      }
      return redirect(url()->previous());
    }

    public function all_students(Request $request)
    {
      // dd($request->all());
      $app = session()->get('sct_admin');
      if ($app =="") {
        return redirect('/admin');
      }
      if($request->isMethod('post')){
        $request->session()->put('studentsSearch',$request->all());
      }

      if($request->input('reset') && $request->input('reset') == 'true'){
        $request->session()->forget('studentsSearch');
        return redirect('dashboard/view_students');
      }
      $s_app = $request->session()->get('studentsSearch');
      if ($s_app ==null) {
        $s_app=[];
      }
      // dd($s_app);
      $type ='';
      $all_client='';
      $all_student='';
    if ($s_app ==[] ) {
      $type ='client_search';
      $all_client = DB::table('users')->where('role','=','customer')
                    ->where(function ($query) use ($s_app) {
                        if(count($s_app) > 0){
                            if($s_app['search'] != ''){
                                $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                            }
                        }
                    })->orderBy('first_name','asc')->paginate(15);

                    foreach ($all_client as &$student) {
                      $student->student=DB::table('students')->where('user_id','=',$student->id)
                      ->where(function ($query) use ($s_app) {
                          if(count($s_app) > 0){
                              if($s_app['search'] != ''){
                                  $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                              }
                          }
                      })->orderBy('student_name','asc')->get();
                    }

    }elseif($s_app['searchBy']!='student_name') {
      $type ='client_search';
      // dd("client");
    $all_client = DB::table('users')->where('role','=','customer')
                  ->where(function ($query) use ($s_app) {
                      if(count($s_app) > 0){
                          if($s_app['search'] != ''){
                              $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                          }
                      }
                  })->orderBy('first_name','asc')->paginate(15);

              foreach ($all_client as &$student) {
                $student->student=DB::table('students')->where('user_id','=',$student->id)
                ->orderBy('student_name','asc')->get();
          }
        }else {
          $type='student_search';
          $all_student = DB::table('students')
                        ->where(function ($query) use ($s_app) {
                            if(count($s_app) > 0){
                                if($s_app['search'] != ''){
                                    $query->where($s_app['searchBy'], 'like', '%'.$s_app['search'].'%');
                                }
                            }
                        })->orderBy('student_name','asc')->paginate(15);
        }

          // dd($type);
      // $all_student = Student::orderBy('student_id','desc')->get();
       return view('admin.view_students',compact('all_client','all_student','type'));
    }

    public function addEditStudent(Request $request){
      // dd($request->all());
      $studentId = 0;
        $rPath = $request->segment(3);
        if($request->isMethod('post')){
            $studentId = $request->input('student_id');
            $this->validate($request, [
                'student_name' => 'required|max:100',
                'college' => 'required',
                'subject' => 'required',
                'grade' => 'required',
                // 'email' => 'required|email|max:255',
            ],[
              'college.required' =>'School/College field is required',
              'subject.required' =>'Subject field is required',
              'grade.required' =>'Grade/Level field is required',
            ]);

            $student =new Student;
            $student->student_name = $request->input('student_name');
            $student->user_id = $request->input('user_id');
            $student->email = $request->input('email');
            $student->college = $request->input('college');
            $student->grade = $request->input('grade');
            $student->subject = $request->input('subject');
            $student->goal = $request->input('goal');
            if($studentId == ''){
                $studentId = $student->save();
                $sMsg = 'New Student Added';
            }else{
              $student='';
              $student = Student::findOrFail($studentId);
              $student->student_name = $request->input('student_name');
              $student->user_id = $request->input('user_id');
              $student->email = $request->input('email');
              $student->college = $request->input('college');
              $student->grade = $request->input('grade');
              $student->subject = $request->input('subject');
              $student->goal = $request->input('goal');
              $student->save();
                $sMsg = 'Student Updated';
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/view_students');
        }else{
            $student = array();
            $studentId = '0';
            if($rPath == 'edit'){
                $studentId = $request->segment(4);
                $student = Student::findOrFail($studentId);
                // dd($student);
                if($student == null){
                    $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                    return redirect('dashboard/view_students');
                }
                // dd($student);
            }
            $users = User::where('role','customer')->orderBy('first_name','asc')->get();
            return view('admin.add-edit-students',compact('student','rPath','studentId','users'));
        }
    }

    public function deleteStudent(Request $request)
    {
      if($request->isMethod('delete')){
        $student_id = trim($request->input('student_id'));
        $student = Student::find($student_id);
        $student->delete();
        $request->session()->flash('message' , 'Student Deleted Successfully');
      }
      return redirect(url()->previous());
    }

    public function all_tutors(Request $request)
    {
      $all_tutor = User::where('role','<>','customer')->orderBy('id','desc')->get();
       return view('admin.view_teachers',compact('all_tutor'));
    }

    public function addEditTutor(Request $request){
      // dd($request->all());
      $tutorId = 0;
        $rPath = $request->segment(3);
        if($request->isMethod('post')){
            $tutorId = $request->input('tutor_id');
            $this->validate($request, [
                'first_name' => 'required|max:100',
                'email' => 'required|email|max:255',
            ]);
            if($tutorId == 0 || $tutorId ==null){

                $this->validate($request, [
                    'password' => 'required|min:6|max:16',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required',
                    // 'password' => 'required|min:6|regex:/[a-z]/|regex:/[0-9]/',
                  ],[
                    // 'password.regex'=> 'Password must contain 1 character from a-z, 1 digit from 0-9',
                  ]);
            }
            $tutor =new User;
            $tutor->first_name = $request->input('first_name');
            $tutor->email = $request->input('email');
            $tutor->role = $request->input('role');
            $tutor->status = 'active';

            if($request->input('password') != '' && $request->input('password') != NULL){
                $tutor->password =Hash::make(trim($request->input('password')));
            }
            if($tutorId == ''){
                $tutorId = $tutor->save();
                $sMsg = 'New Tutor Added';
            }else{
              $tutor='';
              $tutor = User::findOrFail($tutorId);
              $tutor->first_name = $request->input('first_name');
              $tutor->email = $request->input('email');
              $tutor->role = $request->input('role');
              $tutor->phone = $request->input('phone');
              $tutor->status = 'active';
              $tutor->description = $request->input('description');
              if($request->hasFile('profilePhoto')){
                  $image = $request->file('profilePhoto');

                  $tutor->image = 'profile-'.time().'-'.rand(000000,999999).'.'.$image->getClientOriginalExtension();
                  $destinationPath = public_path('/frontend-assets/images/dashboard/profile-photos/');
                  $image->move($destinationPath, $tutor->image);
                  if($request->input('prevLogo') != ''){
                      @unlink(public_path('/frontend-assets/images/dashboard/profile-photos/'.$request->input('prevLogo')));
                  }

              }else{
                  $tutor->image = $request->input('prevLogo');
              }
              if($request->input('password') != '' && $request->input('password') != NULL){
                  $tutor->password =Hash::make(trim($request->input('password')));
              }
              $tutor->save();
                $sMsg = 'Tutor Updated';
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/view_tutors');
        }else{
            $tutor = array();
            $tutorId = '0';
            if($rPath == 'edit'){
                $tutorId = $request->segment(4);
                $tutor = User::findOrFail($tutorId);
                // dd($user);
                if($tutor == null){
                    $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                    return redirect('dashboard/view_tutors');
                }
            }
            return view('admin.add-edit-tutors',compact('tutor','rPath','tutorId'));
        }
    }

    public function deleteTutor(Request $request)
    {
      if($request->isMethod('delete')){
        $tutor_id = trim($request->input('tutor_id'));
        $tutor = User::find($tutor_id);
        $tutor->delete();
        $request->session()->flash('message' , 'Tutor Deleted Successfully');
      }
      return redirect(url()->previous());
    }

    public function all_agreement(Request $request)
    {
      $all_agreement = DB::table('aggreements')->orderBy('aggreement_id','desc')->get();
       return view('admin.view_aggreements',compact('all_agreement'));
    }

    public function awaiting_signature_agreements(Request $request)
    {
      $pending_agreement = DB::table('signed_aggreements')->where('status','Awaiting Signature')->orderBy('signed_id','desc')->get();
       return view('admin.pending_aggreements',compact('pending_agreement'));
    }
    public function signed_agreements(Request $request)
    {
      $signed_agreement = DB::table('signed_aggreements')->where('status','Signed')->orderBy('signed_id','desc')->get();
       return view('admin.signed_aggreements',compact('signed_agreement'));
    }

    public function deletePendingAgreement(Request $request)
    {
      if($request->isMethod('delete')){
        $signed_id = trim($request->input('signed_id'));
        $agreement = DB::table('signed_aggreements')->where('signed_id',$signed_id)->delete();
        $request->session()->flash('message' , 'Agreement Deleted Successfully');
      }
      return redirect(url()->previous());
    }


    public function addEditAgreement(Request $request){
      // dd($request->all());
      $agreementId = 0;
        $rPath = $request->segment(3);
        if($request->isMethod('post')){
            $agreementId = $request->input('aggreement_id');
            $this->validate($request, [
                'aggrement_name' => 'required|max:100',
            ]);
            if($agreementId == 0 || $agreementId ==null){

                $this->validate($request, [
                    'agrreement_file' => 'required|mimes:pdf|max:10000',
                  ],[
                    'agrreement_file.required' => 'Please Upload PDF File',
                  ]);
            }
            $input['aggreement_name'] = $request->input('aggrement_name');
            if($request->file('agrreement_file') != ''){
               $agrreement_file = $request->file('agrreement_file');
               // dd($agrreement_file);
              $upload = $request->file('agrreement_file')->store('agrreement_file');
              $input['file'] = $upload;
            }

            if($agreementId == ''){

                DB::table('aggreements')->insert($input);
                $sMsg = 'New Agreement Added';
            }else{

              DB::table('aggreements')->where('aggreement_id',$agreementId)->update($input);
                $sMsg = 'Agreement Updated';
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/view_agreements');
        }else{
            $agreement = array();
            $agreementId = '0';
            if($rPath == 'edit'){
                $agreementId = $request->segment(4);
                $agreement = DB::table('aggreements')->where('aggreement_id',$agreementId)->first();
                // dd($user);
                if($request->has('download')){
                  $pdf = PDF::loadView('pdfview');
                  return $pdf->download('pdfview.pdf');
                }
                if($agreement == null){
                    $request->session()->flash('alert',['message' => 'No Record Found', 'type' => 'danger']);
                    return redirect('dashboard/view_agreements');
                }
            }
            return view('admin.add-edit-aggreements',compact('agreement','rPath','agreementId'));
        }
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

    public function deleteAgreement(Request $request)
    {
      if($request->isMethod('delete')){
        $agreement_id = trim($request->input('aggreement_id'));
        $tutor = DB::table('aggreements')->where('aggreement_id',$agreement_id)->delete();
        $request->session()->flash('message' , 'Agreement Deleted Successfully');
      }
      return redirect(url()->previous());
    }

    public function getUserList(Request $request,$id)
    {
      $clients = User::where('role','customer')->orderBy('first_name','asc')->get();
      $tutors = User::where('role','tutor')->orderBy('first_name','asc')->get();
      return view('admin.ajax-users-list',compact('clients','tutors','id'));
    }

    public function sendAgreement(Request $request,$agreement_id,$user_id)
    {
      $get_agreement = DB::table('aggreements')->where('aggreement_id',$agreement_id)->first();
      $get_user = DB::table('users')->where('id',$user_id)->first();
      $agreement_name = $get_agreement->aggreement_name;
      $agreement_file= $get_agreement->file;
      $input['aggreement_id'] = $agreement_id;
      $input['user_id'] = $user_id;
      $input['aggreement_name'] = $agreement_name;
      $input['aggreement_file'] = $agreement_file;
      $input['status'] = 'Awaiting Signature';
      $send = DB::table('signed_aggreements')->insertGetId($input);
      $toemail =  $get_user->email;
      // dd($send);
      Mail::send('mail.new_agreement_email',['user' =>$get_user,'agreement_id'=>$agreement_id],
      function ($message) use ($toemail)
      {

        $message->subject('Smart Cookie Tutors.com - New Agreement Available for Review');
        $message->from('admin@SmartCookieTutors.com', 'Smart Cookie Tutors');
        $message->to($toemail);
      });
      echo $send;
    }

    public function addEditFAQ(Request $request){
      // dd($request->all());
      $faqId = 0;
        $faqId = $request->input('faq_id');
        if($request->isMethod('post')){
          $input ['description'] = $request->input('description');

            if($faqId == ''){
                $faqId = DB::table('faqs')->insertGetId($input);
                $sMsg = 'New FAQ Added';
            }else{
              $faqId = DB::table('faqs')->where('faq_id',$faqId)->update($input);

                $sMsg = 'FAQ Updated';
            }
            $request->session()->flash('alert',['message' => $sMsg, 'type' => 'success']);
            return redirect('dashboard/FAQ');
        }else{
            $faq = array();
            $faqId = '0';
            $faq = DB::table('faqs')->first();
            // dd($faq);
            return view('admin.add-edit-faqs',compact('faq','faqId'));
        }
    }

    public function getTutorList(Request $request,$id)
    {
      $tutors = User::where('role','<>','customer')->orderBy('id','desc')->get();
      return view('admin.ajax-tutors-list',compact('tutors','id'));
    }

    public function AssignTutor(Request $request)
    {
      $student_id = $request->input('student_id');
      $student = DB::table('students')->where('student_id',$student_id)->first();
      $user_id = $student->user_id;
      $input['tutor_id'] = $request->input('tutor_id');
      $input['student_id'] = $student_id;
      $input['user_id'] = $user_id;
      $input['hourly_pay_rate'] = $request->input('amount');
      $assign = DB::table('tutor_assign')->insertGetId($input);
      echo $assign;
    }

    public function DeleteAssignTutor(Request $request, $id, $tutor_id)
    {
      $unassign = DB::table('tutor_assign')->where('student_id',$id)->where('tutor_id',$tutor_id)->delete();
      dd($unassign);
    }




    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
