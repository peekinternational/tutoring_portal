<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::match(['get','post'],'/register', 'frontend\RegisterController@register');
Route::get('/login', 'frontend\RegisterController@accountLogin')->name('login');
Route::post('/login', 'frontend\RegisterController@checklogin');
Route::get('logout', 'frontend\RegisterController@logout')->name('logout');
Route::get('refreshCaptcha', 'frontend\RegisterController@refreshCaptcha')->name('refresh');
Route::group(['middleware' => 'auth'], function() {
Route::group(['prefix' => 'user-portal'], function () {
	Route::get('/', function(){
		return view('frontend.dashboard.dashboard');
	});
	Route::get('/dashboard', 'frontend\DashboardController@index');
  Route::match(['get','post'],'/manage-profile', 'frontend\DashboardController@show');
  Route::match(['get','post'],'/manage-profile-tutor', 'frontend\DashboardController@show_tutor');
  Route::post('/profile/picture', 'frontend\DashboardController@profilePicture');
  Route::get('/students', 'frontend\StudentController@show');
  Route::match(['get','post'],'student/add','frontend\StudentController@addEditStudent');
  Route::match(['get','post'],'student/edit/{id}','frontend\StudentController@addEditStudent');
  Route::delete('student/delete','frontend\StudentController@deleteStudent');
  Route::get('/agreements','frontend\DashboardController@getAgreements');
  Route::get('/view_agreement/{id}','frontend\DashboardController@ViewAgreementDetails');
  Route::get('/show_agreement/{id}','frontend\DashboardController@showAgreement');
  Route::post('/sign-agreement','frontend\DashboardController@SignAgreement');
  Route::get('/faqs','frontend\DashboardController@faqs');
  Route::get('/credits','frontend\DashboardController@credits');
  Route::post('/buy-credit','frontend\DashboardController@buyCredit');
  Route::post('/subscribe_process', 'frontend\DashboardController@subscribe_process');
  Route::get('/tutors', 'frontend\DashboardController@studentTutor');
  Route::get('/tutor-students', 'frontend\DashboardController@TutorStudents');

  });
});
/////////////////////////////// Admin //////////////////////////////
Route::match(['get','post'],'/admin/login', 'Admin\AdminController@admin_login');
Route::group(['middleware' => ['auth'=>'admin']], function () {
Route::group(['prefix' => 'dashboard'], function () {
	Route::get('/', function(){
		return view('/admin.index');
	 });
Route::match(['get','post'],'/logout', 'Admin\AdminController@logout');
Route::match(['get','post'],'/view_admins','Admin\AdminController@all_admin');
Route::match(['get','post'],'admin/add','Admin\AdminController@addEditAdmin');
Route::match(['get','post'],'admin/edit/{id}','Admin\AdminController@addEditAdmin');
Route::delete('admin/delete','Admin\AdminController@deleteAdmin');

Route::match(['get','post'],'/view_customers','Admin\AdminController@all_customers');
Route::match(['get','post'],'customer/add','Admin\AdminController@addEditCustomer');
Route::match(['get','post'],'customer/edit/{id}','Admin\AdminController@addEditCustomer');
Route::delete('customer/delete','Admin\AdminController@deleteCustomer');

Route::match(['get','post'],'/view_students','Admin\AdminController@all_students');
Route::match(['get','post'],'student/add','Admin\AdminController@addEditStudent');
Route::match(['get','post'],'student/edit/{id}','Admin\AdminController@addEditStudent');
Route::delete('student/delete','Admin\AdminController@deleteStudent');

Route::match(['get','post'],'/view_tutors','Admin\AdminController@all_tutors');
Route::match(['get','post'],'tutor/add','Admin\AdminController@addEditTutor');
Route::match(['get','post'],'tutor/edit/{id}','Admin\AdminController@addEditTutor');
Route::delete('tutor/delete','Admin\AdminController@deleteTutor');

Route::match(['get','post'],'/view_agreements','Admin\AdminController@all_agreement');
Route::match(['get','post'],'agreement/add','Admin\AdminController@addEditAgreement');
Route::match(['get','post'],'agreement/edit/{id}','Admin\AdminController@addEditAgreement');
Route::delete('agreement/delete','Admin\AdminController@deleteAgreement');
Route::get('/show_agreement/{id}','Admin\AdminController@showAgreement');
Route::get('/get_all_user/{id}','Admin\AdminController@getUserList');
Route::get('/sendAgreement/{id}/{userID}','Admin\AdminController@sendAgreement');
Route::match(['get','post'],'/awaiting_signature','Admin\AdminController@awaiting_signature_agreements');
Route::match(['get','post'],'/signed_agreements','Admin\AdminController@signed_agreements');
Route::delete('pending-agreement/delete','Admin\AdminController@deletePendingAgreement');
Route::match(['get','post'],'/FAQ','Admin\AdminController@addEditFAQ');
Route::get('/get_all_tutors/{id}','Admin\AdminController@getTutorList');
Route::post('AssignTutor','Admin\AdminController@AssignTutor');
Route::get('/DeleteAssignTutor/{id}/{tutor_id}','Admin\AdminController@DeleteAssignTutor');

 });
});