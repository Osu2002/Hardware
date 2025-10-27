<?php

use App\Http\Controllers\Frontend\FrontendAuthController;
use App\Http\Controllers\Frontend\FrontEndCustomerController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ContactController2;
use App\Http\Controllers\Frontend\InquiryController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\CustomHelpers;
use App\Services\ApiClient\ApiClient;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register frontend routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "frontend" middleware group. Now create something great!
|
*/

Route::get('oauth/{driver}', [FrontendAuthController::class, 'redirectToProvider'])->name('social.oauth');
Route::post('oauth/{driver}', [FrontendAuthController::class, 'redirectToProvider'])->name('social.oauth');
Route::get('oauth/{driver}/callback', [FrontendAuthController::class, 'handleProviderCallback'])->name('social.callback');

if (config('app.maintenance_mode')) {
    Route::get('/', [HomeController::class, 'maintain'])->name('maintain');
     Route::get('/home', [HomeController::class, 'index'])->name('index');
} else {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::redirect('/home', '/');
}

  Route::get('/about', [PageController::class, 'about'])->name('about');
   // routes/web.php
    Route::get('/auction/{id}/{model}/{slug}', [PageController::class, 'auction'])
     ->name('auction');


  Route::get('/auction-stat/{model}/{slug}', [PageController::class, 'auctionStat'])->name('auction.stat');

  Route::get('/live-auction', [PageController::class, 'Live_auction'])->name('live.auction');
  // detail page for a single vehicle
  Route::get('/live-auction/{id}', [PageController::class, 'auction'])
     ->name('live.auction.show');

  Route::get('/service', [PageController::class, 'service'])->name('service');
  Route::get('/japan-stock', [PageController::class, 'available'])->name('available');
  Route::get('/faq', [PageController::class, 'faq'])->name('faq');
  Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');
  Route::get('/contact', [PageController::class, 'contact'])->name('contact');
  Route::get('/user-login', [PageController::class, 'login'])->name('user.login');
  Route::get('/available-cars/{countrySlug?}', [PageController::class, 'available'])->name('available');
  Route::get('/faq', [PageController::class, 'faq'])->name('faq');
  Route::get('/register', [PageController::class, 'register'])->name('register');
  Route::get('/view-details/{model}/{slug}', [PageController::class, 'product'])->name('product');

Route::get('/dealer-register', [PageController::class, 'dealerregister'])->name('dealer.register');
Route::post('/dealer-store', [PageController::class, 'dealerstore'])->name('front.end.dealer.store');
Route::get('/profile', [PageController::class, 'profile'])->name('profile');
Route::post('/profile-update', [FrontendAuthController::class, 'profileUpdate'])->name('profile.update');
Route::post('/profile-password-update', [FrontendAuthController::class, 'profilePasswordUpdate'])->name('profile.password.update');
Route::get('/how-to-order', [PageController::class, 'HowtoOrder'])->name('how.to.order');
Route::get('/knowledge-center', [PageController::class, 'knowledge'])->name('knowledge.center');
Route::get('/privacy-policy', [PageController::class, 'PrivacyPolicy'])->name('privacy.policy');
// Route خروج: '/forgot-password', [PageController::class, 'forgotpassword'])->name('forgotpassword');
Route::get('/app/auction', [PageController::class, 'appAuction'])->name('app.auction');
Route::get('/forgot-password', [PageController::class, 'forgotpassword'])->name('forgotpassword');
Route::post('/forgot-password', [FrontendAuthController::class, 'forgotPasswordSubmit'])->name('forgotpassword.submit');
Route::get('/reset-password/{token}', [PageController::class, 'NewPassword'])->name('forget-password-link');
Route::post('/reset-password', [FrontendAuthController::class, 'newPassword'])->name('forget-password-link.store');
Route::post('/app-register', [FrontendAuthController::class, 'makeRegister'])->name('front.end.customer.store');
Route::post('/login', [FrontendAuthController::class, 'makeLogin'])->name('front.end.customer.login');
Route::get('/logout', [FrontendAuthController::class, 'makeLogout'])->name('front.end.customer.logout');
Route::get('/submit-contact', [PageController::class, 'getdata'])->name('index.getdata');
Route::post('/enroll-to-affiliate', [FrontendAuthController::class, 'enrollToAffiliate'])->name('frontend.enroll-affiliate');
Route::post('/submit-inquiry', [InquiryController::class, 'submit'])->name('submit-inquiry');
Route::get('/app-download', [PageController::class, 'AppDownload'])->name('appdownload');

Route::get('/test-email', function() {
    $contact = \App\Models\Contact::first() ?? \App\Models\Contact::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'inquiry_type' => 'Test Inquiry',
        'message' => 'This is a test message.'
    ]);

    // \Illuminate\Support\Facades\Mail::to('test@example.com')env('ADMIN_EMAIL'))->send(new \App\Mail\ContactFormSubmitted($contact));
    \Illuminate\Support\Facades\Mail::to($contact->email)->send(new \App\Mail\ContactFormConfirmation($contact));

    return 'Test emails sent. Check Mailchimp inbox.';
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::get('/contacts', [ContactController2::class, 'allMessages'])->name('admin.contacts');
    Route::get('/contacts/{contact}', [ContactController2::class, 'show'])->name('admin.contacts.show');
    Route::post('/contact/{contact}/status', [ContactController2::class, 'updateStatus'])->name('admin.contacts.status');
});

    Route::post('/submit-contact2', [ContactController2::class, 'submit'])->name('submit.contact');



Route::get('/export-all-stats', [PageController::class, 'exportVehicleStatsList']);
Route::get('/export-test', [PageController::class, 'testexcel']);


Route::get('/export-models-view', [PageController::class, 'exportView'])->name('export.models.view');
Route::get('/export-models', [PageController::class, 'exportMultipleModelStats'])->name('export.models');
Route::get('/view-stats', [PageController::class, 'viewStats'])->name('view.stats');


