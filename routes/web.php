<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

include __DIR__ . '/admin.php';

//Ajax
Route::group(['prefix' => 'ajax'], function () {
    Route::post('/get_district', 'AjaxController@getDistrict')->name('ajax_get_district');
    Route::post('/load-more', 'AjaxController@loadMore')->name('ajax_load_more');
});

//Route::group(['prefix' => 'member'], function () {
//    Route::get('/', 'Member\MemberController@index')->name('member');
//    Route::get('/profile', 'Member\MemberController@profile');
//    Route::post('/profile', 'Member\MemberController@updateProfile')->name('member_profile');
//    Route::get('/order-detail/{order_id}', 'Member\MemberController@orderDetail')->name('member_order_detail');
//    Route::get('/register', 'Member\RegisterController@showRegistrationForm');
//    Route::post('/register', 'Member\RegisterController@register')->name('member_register');
//    Route::get('/verify/{member_id}/{created_at}', 'Member\VerificationController@verify')->name('member_verify');
//    Route::get('/resend', 'Member\VerificationController@resend')->name('member_resend_verify');
//    Route::get('/login', 'Member\LoginController@showLoginForm')->name('member_login');
//    Route::post('/login', 'Member\LoginController@login')->name('member_login');
//    Route::get('/logout', 'Member\LoginController@logout')->name('member_logout');
//    Route::get('/reset-password', 'Member\ForgotPasswordController@showFormReset');
//    Route::post('/reset-password', 'Member\ForgotPasswordController@sendMailReset')->name('member_reset_password');
//});

Route::get('/', 'HomeController@index')->name('home_page');
Route::get('/home', 'HomeController@index');
Route::get('/sitemap.xml', 'HomeController@siteMap')->name('site_map');
Route::match(['get', 'post'], '/contact', 'HomeController@contact')->name('contact');
Route::get('/search', 'HomeController@search')->name('search');
Route::post('/request-for-quote', 'HomeController@requestForQuote')->name('request_for_quote');
//Route::get('test-send-mail', 'HomeController@testSendMail');
//Route::get('/page/{slug}.html', 'HomeController@page')->where(['slug' => '[a-z0-9\-]+'])->name('page_content');

Route::get('tat-ca-bai-viet', 'PostController@index')->name('all_post');
Route::get('{slug}', 'SlugController@index')->where(['slug' => '[a-z0-9\-]+'])->name('category');
Route::get('{slug}-{id}.html', 'PostController@detail')->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+'])->name('post_detail');
Route::get('/service/{slug}-{id}.html', 'ServiceController@detail')->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+'])->name('service_detail');
Route::get('/service/{slug}', 'ServiceController@categoryDetail')->where(['slug' => '[a-z0-9\-]+'])->name('category_service_detail');

Route::get('/recruitment/{slug}-{id}.html', 'RecruitmentController@detail')->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+'])->name('recruitment_detail');
Route::post('/recruitment/application', 'RecruitmentController@application')->name('application');
