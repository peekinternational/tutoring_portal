<?php
namespace App\Classes;
use DB;
use Session;
use Carbon\Carbon;

class SCT {
  public function checkAggrementSend($aggrement_id,$user_id)
  {
    $aggreement = DB::table('signed_aggreements')->where('aggreement_id',$aggrement_id)->where('user_id',$user_id)->first();
    return $aggreement;
  }
  public function GetUser($user_id)
  {
    $user = DB::table('users')->where('id',$user_id)->first();
    return $user;
  }
  public function checkCredit($user_id)
  {
    $user = DB::table('credits')->where('user_id',$user_id)->first();
    return $user;
  }
  public function checkTutorAssign($student_id,$tutor_id)
  {
    $assign = DB::table('tutor_assign')->where('student_id',$student_id)->where('tutor_id',$tutor_id)->first();
    return $assign;
  }
  public function getStudentName($student_id)
  {
    $student = DB::table('students')->where('student_id',$student_id)->first();
    return $student;
  }
  public function getClientName($user_id)
  {
    $user = DB::table('users')->where('id',$user_id)->first();
    return $user;
  }
  public function getClientCredit($user_id)
  {
    $user = DB::table('credits')->where('user_id',$user_id)->first();
    return $user;
  }
}

?>