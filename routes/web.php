<?php

use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\BackendAuthController;
use App\Http\Controllers\BackendUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FrontendUserController;
use App\Http\Controllers\HomebannerController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LiveAuctionManufacturerController;
use App\Http\Controllers\ManufactureController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VehicleManufacture;
use App\Http\Controllers\VehicleModel;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\VehicleColorController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleModelController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\Admin\FuelPriceController;

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

Route::group(['middleware' => ['auth:sanctum', 'verified', 'web']], function () {
    Route::get('/two-step-verification', [BackendAuthController::class, 'twoStepRequest'])->name('2fa.request');
    Route::post('/two-step-verification', [BackendAuthController::class, 'twoStepVerify'])->name('2fa.verify');
    Route::get('/activate', [BackendAuthController::class, 'activateProfileView'])->name('profile.activate.request');
    Route::post('/activate', [BackendAuthController::class, 'activateProfile'])->name('profile.activate');
});


Route::group(['middleware' => ['auth:sanctum', 'verified', 'web']], function () {

    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    https://mailtrap.io/inboxes
    //Inquiry
    Route::prefix('inquiry')->group(function () {
        Route::get('/', [InquiryController::class, 'index'])->middleware(['can:inquiry.view'])->name('inquiry.index');
        Route::get('/get/data', [InquiryController::class, 'getData'])->middleware(['can:inquiry.view'])->name('inquiry.getdata');
        // Route::get('/create', [InquiryController::class, 'create'])->middleware(['can:inquiry.create'])->name('inquiry.create');
        // Route::post('/store', [InquiryController::class, 'store'])->middleware(['can:inquiry.create'])->name('inquiry.store');
        // Route::put('/update/status', [VehicleTypeController::class, 'updateStatus'])->middleware(['can:vehicle_type.edit'])->name('vehicle_type.change.status');
        Route::get('/edit/{id}', [InquiryController::class, 'edit'])->middleware(['can:inquiry.edit'])->name('inquiry.edit');
        Route::post('/', [InquiryController::class, 'update'])->middleware(['can:inquiry.edit'])->name('inquiry.update');
        Route::post('/delete', [InquiryController::class, 'destroy'])->middleware(['can:inquiry.delete'])->name('inquiry.delete');
    });

    // leads
    Route::prefix('lead')->group(function () {
        Route::get('/', [LeadsController::class, 'index'])->middleware(['can:lead.view'])->name('lead.index');
        Route::post('/store', [LeadsController::class, 'store'])->middleware(['can:lead.view'])->name('lead.store');
        Route::get('/get/data', [LeadsController::class, 'getData'])->middleware(['can:lead.view'])->name('lead.getdata');
        Route::get('/get/itemdata', [LeadsController::class, 'getItemData'])->middleware(['can:lead.view'])->name('lead.itemdata');
        Route::get('/create', [LeadsController::class, 'create'])->middleware(['can:lead.create'])->name('lead.create');
        Route::get('/edit/{id}', [LeadsController::class, 'edit'])->middleware(['can:lead.edit'])->name('lead.edit');
        Route::post('/update', [LeadsController::class, 'update'])->middleware(['can:lead.edit'])->name('lead.update');
        Route::put('/status-update', [LeadsController::class, 'updateStatus'])->middleware(['can:lead.view'])->name('lead.status.update');
        Route::post('/delete', [LeadsController::class, 'destroy'])->middleware(['can:lead.delete'])->name('lead.delete');
    });
    // dealer
    Route::prefix('dealer')->group(function () {
        Route::get('/', [DealerController::class, 'index'])->middleware(['can:dealer.view'])->name('dealer.index');
        Route::post('/store', [DealerController::class, 'store'])->middleware(['can:dealer.view'])->name('dealer.store');
        Route::get('/get/data', [DealerController::class, 'getData'])->middleware(['can:dealer.view'])->name('dealer.getdata');
        Route::get('/get/itemdata', [DealerController::class, 'getItemData'])->middleware(['can:dealer.view'])->name('dealer.itemdata');
        Route::get('/create', [DealerController::class, 'create'])->middleware(['can:dealer.create'])->name('dealer.create');
        Route::get('/edit/{id}', [DealerController::class, 'edit'])->middleware(['can:dealer.edit'])->name('dealer.edit');
        Route::post('/update', [DealerController::class, 'update'])->middleware(['can:dealer.edit'])->name('dealer.update');
        Route::put('/status-update', [DealerController::class, 'updateStatus'])->middleware(['can:dealer.view'])->name('dealer.status.update');
        Route::post('/delete', [DealerController::class, 'destroy'])->middleware(['can:dealer.delete'])->name('dealer.delete');
    });
    //Vehicle type
    Route::prefix('vehicle_type')->group(function () {
        Route::get('/vehicle_type', [VehicleTypeController::class, 'index'])->middleware(['can:vehicle_type.view'])->name('vehicle.type.index');
        Route::get('/get/data', [VehicleTypeController::class, 'getData'])->middleware(['can:vehicle_type.view'])->name('vehicle.type.getdata');
        Route::get('/create', [VehicleTypeController::class, 'create'])->middleware(['can:vehicle_type.create'])->name('vehicle_type.create');
        Route::post('/store', [VehicleTypeController::class, 'store'])->middleware(['can:vehicle_type.create'])->name('vehicle_type.store');
        Route::put('/update/status', [VehicleTypeController::class, 'updateStatus'])->middleware(['can:vehicle_type.edit'])->name('vehicle_type.change.status');
        Route::get('/edit/{id}', [VehicleTypeController::class, 'edit'])->middleware(['can:vehicle_type.edit'])->name('vehicle_type.edit');
        Route::post('/update', [VehicleTypeController::class, 'update'])->middleware(['can:vehicle_type.edit'])->name('vehicle_type.update');
        Route::post('/delete', [VehicleTypeController::class, 'destroy'])->middleware(['can:vehicle_type.delete'])->name('vehicle_type.delete');
    });
    //Vehicle Manufacture
    Route::prefix('vehicle_manufacture_local')->group(function () {
        Route::get('/vehicle_manufacture', [ManufactureController::class, 'index'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle_manufacture.index');
        Route::get('/get/data', [ManufactureController::class, 'getData'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle_manufacture.getdata');
        Route::get('/create', [ManufactureController::class, 'create'])->middleware(['can:vehicle_manufacture.create'])->name('vehicle_manufacture.create');
        Route::post('/store', [ManufactureController::class, 'store'])->middleware(['can:vehicle_manufacture.create'])->name('vehicle_manufacture.store');
        Route::put('/update/status', [ManufactureController::class, 'updateStatus'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.change.status');
        Route::get('/edit/{id}', [ManufactureController::class, 'edit'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.edit');
        Route::post('/update', [ManufactureController::class, 'update'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.update');
        Route::post('/delete', [ManufactureController::class, 'destroy'])->middleware(['can:vehicle_manufacture.delete'])->name('vehicle_manufacture.delete');
    });
    //Vehicle Manufacture (Live Auction)
    Route::prefix('vehicle_manufacture_live')->group(function () {
        Route::get('/vehicle_manufacture', [LiveAuctionManufacturerController::class, 'index'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle_manufacture.live.index');
        Route::get('/get/data', [LiveAuctionManufacturerController::class, 'getData'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle_manufacture.live.getdata');
        Route::get('/create', [LiveAuctionManufacturerController::class, 'create'])->middleware(['can:vehicle_manufacture.create'])->name('vehicle_manufacture.live.create');
        Route::post('/store', [LiveAuctionManufacturerController::class, 'store'])->middleware(['can:vehicle_manufacture.create'])->name('vehicle_manufacture.live.store');
        Route::put('/update/status', [LiveAuctionManufacturerController::class, 'updateStatus'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.live.change.status');
        Route::get('/edit/{id}', [LiveAuctionManufacturerController::class, 'edit'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.live.edit');
        Route::post('/update', [LiveAuctionManufacturerController::class, 'update'])->middleware(['can:vehicle_manufacture.edit'])->name('vehicle_manufacture.live.update');
        Route::post('/delete', [LiveAuctionManufacturerController::class, 'destroy'])->middleware(['can:vehicle_manufacture.delete'])->name('vehicle_manufacture.live.delete');
    });

    // vehicle model
    Route::prefix('vehicle_model')->group(function () {
        Route::get('/vehicle_model_view', [VehicleModelController::class, 'index'])->middleware(['can:vehicle_model.view'])->name('vehicle.model');
        Route::post('/store/data', [VehicleModelController::class, 'store'])->middleware(['can:vehicle_model.view'])->name('vehicle.model.storedata');
        Route::get('/get/data', [VehicleModelController::class, 'getData'])->middleware(['can:vehicle_model.view'])->name('vehicle.model.getdata');
        Route::get('/create', [VehicleModelController::class, 'create'])->middleware(['can:vehicle_model.create'])->name('vehicle_model.create');
        Route::get('/edit/{id}', [VehicleModelController::class, 'edit'])->middleware(['can:vehicle_model.edit'])->name('vehicle_model.edit');
        Route::post('/update', [VehicleModelController::class, 'update'])->middleware(['can:vehicle_model.edit'])->name('vehicle_model.update');
        Route::put('/status-update', [VehicleModelController::class, 'updateStatus'])->middleware(['can:vehicle_model.view'])->name('vehicle_model.status.update');
        Route::post('/delete', [VehicleModelController::class, 'delete'])->middleware(['can:vehicle_model.delete'])->name('vehicle_model.delete');
    });

    // vehicle manufacture
    Route::prefix('vehicle_manufacture')->group(function () {
        Route::get('/vehicle_manufacture_view', [VehicleManufacture::class, 'index'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle.manufacture');
        Route::get('/get/data', [VehicleManufacture::class, 'getData'])->middleware(['can:vehicle_manufacture.view'])->name('vehicle.manufacture.getdata');
        Route::get('/create', [VehicleManufacture::class, 'create'])->middleware(['can:vehicle_manufacture.create'])->name('vehicle.manufacture.create');
        // Route::get('/vehicle_type', [VehicleTypeController::class,'index'])->middleware(['can:vehicle_type.view'])->name('vehicle.type');
        // Route::post('/general', [SettingsController::class, 'generalSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.general.update');
        // Route::get('/mail', [SettingsController::class, 'mailSettings'])->middleware(['can:settings.view'])->name('settings.mail');
        // Route::post('/mail', [SettingsController::class, 'mailSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.mail.update');
        // Route::get('/social-auth', [SettingsController::class, 'socialAuthSettings'])->middleware(['can:settings.view'])->name('settings.social-auth');
        // Route::post('/social-auth', [SettingsController::class, 'socialAuthSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.social-auth.update');
        // Route::get('/content-pages/{id}', [SettingsController::class, 'socialAuthSettings'])->middleware(['can:settings.view'])->name('settings.content-pages.edit');
        // Route::post('/content-pages', [SettingsController::class, 'socialAuthSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.content-pages.update');
    });



    // vehicle color
    Route::prefix('vehicle_color')->group(function () {
        Route::get('/vehicle_color_view', [VehicleColorController::class, 'index'])->middleware(['can:vehicle_color.view'])->name('vehicle.color');
        Route::post('/store/data', [VehicleColorController::class, 'store'])->middleware(['can:vehicle_color.view'])->name('vehicle.color.storedata');
        Route::get('/get/data', [VehicleColorController::class, 'getData'])->middleware(['can:vehicle_color.view'])->name('vehicle.color.getdata');
        Route::get('/create', [VehicleColorController::class, 'create'])->middleware(['can:vehicle_color.create'])->name('vehicle.color.create');
        Route::get('/edit/{id}', [VehicleColorController::class, 'edit'])->middleware(['can:vehicle_color.edit'])->name('vehicle.color.edit');
        Route::post('/update', [VehicleColorController::class, 'update'])->middleware(['can:vehicle_color.edit'])->name('vehicle.color.update');
        Route::put('/status-update', [VehicleColorController::class, 'updateStatus'])->middleware(['can:vehicle_color.view'])->name('vehicle.color.status.update');
        Route::post('/delete', [VehicleColorController::class, 'delete'])->middleware(['can:vehicle_color.delete'])->name('vehicle.color.delete');
    });

    //Vehicle
    Route::prefix('vehicle')->group(function () {
        Route::get('/vehicle', [VehicleController::class, 'index'])->middleware(['can:vehicle.view'])->name('vehicle.index');
        Route::get('/get/data', [VehicleController::class, 'getData'])->middleware(['can:vehicle.view'])->name('vehicle.getdata');
        Route::get('/create', [VehicleController::class, 'create'])->middleware(['can:vehicle.create'])->name('vehicle.create');
        Route::post('/store', [VehicleController::class, 'store'])->middleware(['can:vehicle.create'])->name('vehicle.store');
        Route::put('/update/status', [VehicleController::class, 'updateStatus'])->middleware(['can:vehicle.edit'])->name('vehicle.change.status');
        Route::get('/edit/{id}', [VehicleController::class, 'edit'])->middleware(['can:vehicle.edit'])->name('vehicle.edit');
        Route::post('/update', [VehicleController::class, 'update'])->middleware(['can:vehicle.edit'])->name('vehicle.update');
        Route::post('/delete', [VehicleController::class, 'destroy'])->middleware(['can:vehicle.delete'])->name('vehicle.delete');
        Route::post('/remove-image', [VehicleController::class, 'removeImage'])->middleware(['can:vehicle.edit'])->name('vehicle.image.remove');

    });
    // vehicle features
    Route::prefix('vehicle_feature')->group(function () {
        Route::get('/vehicle_feature_view', [FeatureController::class, 'index'])->middleware(['can:vehicle_feature.view'])->name('vehicle.feature');
        Route::post('/store/data', [FeatureController::class, 'store'])->middleware(['can:vehicle_feature.view'])->name('vehicle.feature.storedata');
        Route::get('/get/data', [FeatureController::class, 'getData'])->middleware(['can:vehicle_feature.view'])->name('vehicle.feature.getdata');
        Route::get('/create', [FeatureController::class, 'create'])->middleware(['can:vehicle_feature.create'])->name('vehicle.feature.create');
        Route::get('/edit/{id}', [FeatureController::class, 'edit'])->middleware(['can:vehicle_feature.edit'])->name('vehicle.feature.edit');
        Route::post('/update', [FeatureController::class, 'update'])->middleware(['can:vehicle_feature.edit'])->name('vehicle.feature.update');
        Route::put('/status-update', [FeatureController::class, 'updateStatus'])->middleware(['can:vehicle_feature.view'])->name('vehicle.feature.status.update');
        Route::post('/delete', [FeatureController::class, 'delete'])->middleware(['can:vehicle_feature.delete'])->name('vehicle.feature.delete');
    });

    // vehicle testimonials
    Route::prefix('vehicle-testimonials')->group(function () {
        Route::get('/view', [TestimonialController::class, 'index'])->middleware(['can:testimonials.view'])->name('testimonials.index');
        Route::post('/store/data', [TestimonialController::class, 'store'])->middleware(['can:testimonials.view'])->name('testimonials.store');
        Route::get('/get/data', [TestimonialController::class, 'getData'])->middleware(['can:testimonials.view'])->name('testimonials.getdata');
        Route::get('/create', [TestimonialController::class, 'create'])->middleware(['can:testimonials.create'])->name('testimonials.create');
        Route::get('/edit/{id}', [TestimonialController::class, 'edit'])->middleware(['can:testimonials.edit'])->name('testimonials.edit');
        Route::post('/update', [TestimonialController::class, 'update'])->middleware(['can:testimonials.edit'])->name('testimonials.update');
        Route::put('/status-update', [TestimonialController::class, 'updateStatus'])->middleware(['can:testimonials.view'])->name('testimonials.status.update');
        Route::post('/delete', [TestimonialController::class, 'destroy'])->middleware(['can:testimonials.delete'])->name('testimonials.delete');
    });

    // Newsletter
    Route::prefix('newsletter')->group(function () {
        Route::get('/newsletter', [NewsletterController::class, 'index'])->middleware(['can:newsletter.view'])->name('newsletter.index');
        Route::get('/get/data', [NewsletterController::class, 'getData'])->middleware(['can:newsletter.view'])->name('newsletter.getdata');
        Route::get('/create', [NewsletterController::class, 'create'])->middleware(['can:newsletter.create'])->name('newsletter.create');
        Route::post('/store', [NewsletterController::class, 'store'])->middleware(['can:newsletter.create'])->name('newsletter.store');
        Route::put('/update/status', [NewsletterController::class, 'updateStatus'])->middleware(['can:newsletter.edit'])->name('newsletter.change.status');
        Route::get('/edit/{id}', [NewsletterController::class, 'edit'])->middleware(['can:newsletter.edit'])->name('newsletter.edit');
        Route::post('/update', [NewsletterController::class, 'update'])->middleware(['can:newsletter.edit'])->name('newsletter.update');
        Route::post('/delete', [NewsletterController::class, 'destroy'])->middleware(['can:newsletter.delete'])->name('newsletter.delete');
    });
    // Homebanner
    Route::prefix('homebanner')->group(function () {
        Route::get('/homebanner', [HomebannerController::class, 'index'])->middleware(['can:homebanner.view'])->name('homebanner.index');
        Route::get('/get/data', [HomebannerController::class, 'getData'])->middleware(['can:homebanner.view'])->name('homebanner.getdata');
        Route::get('/create', [HomebannerController::class, 'create'])->middleware(['can:homebanner.create'])->name('homebanner.create');
        Route::post('/store', [HomebannerController::class, 'store'])->middleware(['can:homebanner.create'])->name('homebanner.store');
        Route::put('/update/status', [HomebannerController::class, 'updateStatus'])->middleware(['can:homebanner.edit'])->name('homebanner.change.status');
        Route::get('/edit/{id}', [HomebannerController::class, 'edit'])->middleware(['can:homebanner.edit'])->name('homebanner.edit');
        Route::post('/update', [HomebannerController::class, 'update'])->middleware(['can:homebanner.edit'])->name('homebanner.update');
        Route::post('/delete', [HomebannerController::class, 'destroy'])->middleware(['can:homebanner.delete'])->name('homebanner.delete');
    });
    // events
    Route::prefix('events')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->middleware(['can:events.view'])->name('events.index');
        Route::get('/get/data', [EventController::class, 'getData'])->middleware(['can:events.view'])->name('events.getdata');
        Route::get('/create', [EventController::class, 'create'])->middleware(['can:events.create'])->name('events.create');
        Route::post('/store', [EventController::class, 'store'])->middleware(['can:events.create'])->name('events.store');
        Route::put('/update/status', [EventController::class, 'updateStatus'])->middleware(['can:events.edit'])->name('events.change.status');
        Route::get('/edit/{id}', [EventController::class, 'edit'])->middleware(['can:events.edit'])->name('events.edit');
        Route::post('/update', [EventController::class, 'update'])->middleware(['can:events.edit'])->name('events.update');
        Route::post('/delete', [EventController::class, 'destroy'])->middleware(['can:events.delete'])->name('events.delete');
    });
    // Customer
    Route::prefix('customer')->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->middleware(['can:customer.view'])->name('customer.index');
        Route::get('/get/data', [CustomerController::class, 'getData'])->middleware(['can:customer.view'])->name('customer.getdata');
        Route::get('/create', [CustomerController::class, 'create'])->middleware(['can:customer.create'])->name('customer.create');
        Route::post('/store', [CustomerController::class, 'store'])->middleware(['can:customer.create'])->name('customer.store');
        Route::put('/update/status', [CustomerController::class, 'updateStatus'])->middleware(['can:customer.edit'])->name('customer.change.status');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->middleware(['can:customer.edit'])->name('customer.edit');
        Route::post('/update', [CustomerController::class, 'update'])->middleware(['can:customer.edit'])->name('customer.update');
        Route::post('/delete', [CustomerController::class, 'destroy'])->middleware(['can:customer.delete'])->name('customer.delete');
        Route::get('/affilate-customer/{id}', [CustomerController::class, 'affiliateIndex'])->middleware(['can:customer.view'])->name('affilate-customer.view');
        Route::get('/affilate-customer/get/data/{id}', [CustomerController::class, 'affiliateCustomerGetData'])->middleware(['can:customer.view'])->name('affilate-customer.getData');
        Route::post('/affiliate-enroll/{id}', [CustomerController::class, 'confirmAffiliateRequest'])->middleware(['can:customer.view'])->name('affilate-customer.enroll');
    });
    // Country
    Route::prefix('country')->group(function () {
        Route::get('/country', [CountryController::class, 'index'])->middleware(['can:country.view'])->name('country.index');
        Route::get('/get/data', [CountryController::class, 'getData'])->middleware(['can:country.view'])->name('country.getdata');
        Route::get('/create', [CountryController::class, 'create'])->middleware(['can:country.create'])->name('country.create');
        Route::post('/store', [CountryController::class, 'store'])->middleware(['can:country.create'])->name('country.store');
        Route::put('/update/status', [CountryController::class, 'updateStatus'])->middleware(['can:country.edit'])->name('country.change.status');
        Route::get('/edit/{id}', [CountryController::class, 'edit'])->middleware(['can:country.edit'])->name('country.edit');
        Route::post('/update', [CountryController::class, 'update'])->middleware(['can:country.edit'])->name('country.update');
        Route::post('/delete', [CountryController::class, 'destroy'])->middleware(['can:country.delete'])->name('country.delete');
        Route::get('/country-codes', [CountryController::class, 'countryCodes'])->name('country.codes');
    });
    // advertisements
    Route::prefix('advertisements')->group(function () {
        Route::get('/advertisements-view', [AdvertisementController::class, 'index'])->middleware(['can:advertisements.view'])->name('advertisements.index');
        Route::get('/get/data', [AdvertisementController::class, 'getData'])->middleware(['can:advertisements.view'])->name('advertisements.getdata');
        Route::get('/create', [AdvertisementController::class, 'create'])->middleware(['can:advertisements.create'])->name('advertisements.create');
        Route::post('/store', [AdvertisementController::class, 'store'])->middleware(['can:advertisements.create'])->name('advertisements.store');
        Route::put('/update/status', [AdvertisementController::class, 'updateStatus'])->middleware(['can:advertisements.edit'])->name('advertisements.change.status');
        Route::get('/edit/{id}', [AdvertisementController::class, 'edit'])->middleware(['can:advertisements.edit'])->name('advertisements.edit');
        Route::post('/update', [AdvertisementController::class, 'update'])->middleware(['can:advertisements.edit'])->name('advertisements.update');
        Route::post('/delete', [AdvertisementController::class, 'destroy'])->middleware(['can:advertisements.delete'])->name('advertisements.delete');
    });

    // profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [BackendAuthController::class, 'showProfile'])->name('profile');
        Route::put('/update-user-photo', [BackendAuthController::class, 'uploadProfilePhoto'])->middleware(['can:profile.updatePhoto'])->name('profile.update-photo');
        Route::put('/update-user-info', [BackendAuthController::class, 'updateUserInfo'])->middleware(['can:profile.updateInfo'])->name('profile.update-info');
        Route::put('/change-password', [BackendAuthController::class, 'changePassword'])->middleware(['can:profile.updatePassword'])->name('profile.update-password');
        Route::post('/disable', [BackendAuthController::class, 'disableProfile'])->middleware(['can:profile.deactivate'])->name('profile.disable');
        Route::post('/delete', [BackendAuthController::class, 'deleteProfile'])->middleware(['can:profile.delete'])->name('profile.delete');
    });
    // settings
    Route::prefix('settings')->group(function () {
        Route::get('/general', [SettingsController::class, 'generalSettings'])->middleware(['can:settings.view'])->name('settings.general');
        Route::post('/general', [SettingsController::class, 'generalSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.general.update');
        Route::get('/mail', [SettingsController::class, 'mailSettings'])->middleware(['can:settings.view'])->name('settings.mail');
        Route::post('/mail', [SettingsController::class, 'mailSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.mail.update');
        Route::get('/social-auth', [SettingsController::class, 'socialAuthSettings'])->middleware(['can:settings.view'])->name('settings.social-auth');
        Route::post('/social-auth', [SettingsController::class, 'socialAuthSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.social-auth.update');
        Route::get('/content-pages/{id}', [SettingsController::class, 'socialAuthSettings'])->middleware(['can:settings.view'])->name('settings.content-pages.edit');
        Route::post('/content-pages', [SettingsController::class, 'socialAuthSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.content-pages.update');
        Route::get('/affiliate-pages', [SettingsController::class, 'affiliateSettings'])->middleware(['can:settings.view'])->name('settings.affiliate-pages');
        Route::post('/affiliate-pages/update', [SettingsController::class, 'affiliateSettingsUpdate'])->middleware(['can:settings.edit'])->name('settings.affiliate-pages.update');
    });
    // media library
    Route::prefix('media-library')->group(function () {
        Route::get('/', [MediaLibraryController::class, 'index'])->middleware(['can:media.view'])->name('media.index');
        Route::post('/upload', [MediaLibraryController::class, 'upload'])->middleware(['can:media.create'])->name('media.upload');
        Route::get('/download/{id}', [MediaLibraryController::class, 'download'])->middleware(['can:media.edit'])->name('media.download');
        Route::post('/delete', [MediaLibraryController::class, 'delete'])->middleware(['can:media.delete'])->name('media.delete');
    });
    // rolses & permissions
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware(['can:roles-permissions.view'])->name('settings.roles');
        Route::get('/getdata', [RoleController::class, 'getdata'])->middleware(['can:roles-permissions.view'])->name('settings.roles.getdata');
        Route::get('/create', [RoleController::class, 'create'])->middleware(['can:roles-permissions.create'])->name('settings.roles.create');
        Route::post('/store', [RoleController::class, 'store'])->middleware(['can:roles-permissions.create'])->name('settings.roles.store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->middleware(['can:roles-permissions.edit'])->name('settings.roles.edit');
        Route::post('/update', [RoleController::class, 'update'])->middleware(['can:roles-permissions.edit'])->name('settings.roles.update');
        Route::get('/permissions/{id}', [RoleController::class, 'editPermissions'])->middleware(['can:roles-permissions.edit'])->name('settings.roles.permissions');
        Route::post('/permissions/update', [RoleController::class, 'updatePermissions'])->middleware(['can:roles-permissions.edit'])->name('settings.roles.permissions.update');
        Route::post('/delete', [RoleController::class, 'destroy'])->middleware(['can:roles-permissions.delete'])->name('settings.roles.delete');
    });
    // backend users
    Route::prefix('backend-users')->group(function () {
        Route::get('/', [BackendUserController::class, 'index'])->middleware(['can:backend-user.view'])->name('settings.users');
        Route::get('/get/data', [BackendUserController::class, 'getData'])->middleware(['can:backend-user.view'])->name('settings.users.getdata');
        Route::get('/create', [BackendUserController::class, 'create'])->middleware(['can:backend-user.create'])->name('settings.users.create');
        Route::post('/store', [BackendUserController::class, 'store'])->middleware(['can:backend-user.create'])->name('settings.users.store');
        Route::get('/edit/{id}', [BackendUserController::class, 'edit'])->middleware(['can:backend-user.edit'])->name('settings.users.edit');
        Route::post('/update', [BackendUserController::class, 'update'])->middleware(['can:backend-user.edit'])->name('settings.users.update');
        Route::put('/update/status', [BackendUserController::class, 'updateStatus'])->middleware(['can:backend-user.edit'])->name('settings.users.change.status');
        Route::put('/update/password', [BackendUserController::class, 'updatePassword'])->middleware(['can:backend-user.edit'])->name('settings.users.change.password');
        Route::post('/suspend', [BackendUserController::class, 'softDelete'])->middleware(['can:backend-user.delete'])->name('settings.users.delete');
        Route::post('/restore', [BackendUserController::class, 'restore'])->middleware(['can:backend-user.delete'])->name('settings.users.restore');
        Route::post('/delete', [BackendUserController::class, 'delete'])->middleware(['can:backend-user.delete'])->name('settings.users.remove');
    });
    // frontend users
    Route::prefix('front-users')->group(function () {
        Route::get('/', [FrontendUserController::class, 'index'])->middleware(['can:frontend-user.view'])->name('settings.front-user');
        Route::get('/get/data', [FrontendUserController::class, 'getData'])->middleware(['can:frontend-user.view'])->name('settings.front-user.getdata');
        Route::get('/create', [FrontendUserController::class, 'create'])->middleware(['can:frontend-user.create'])->name('settings.front-user.create');
        Route::post('/store', [FrontendUserController::class, 'store'])->middleware(['can:frontend-user.create'])->name('settings.front-user.store');
        Route::get('/edit/{id}', [FrontendUserController::class, 'edit'])->middleware(['can:frontend-user.edit'])->name('settings.front-user.edit');
        Route::post('/update', [FrontendUserController::class, 'update'])->middleware(['can:frontend-user.edit'])->name('settings.front-user.update');
        Route::put('/update/status', [FrontendUserController::class, 'updateStatus'])->middleware(['can:frontend-user.edit'])->name('settings.front-user.change.status');
        Route::put('/update/password', [FrontendUserController::class, 'updatePassword'])->middleware(['can:frontend-user.edit'])->name('settings.front-user.change.password');
        Route::post('/delete', [FrontendUserController::class, 'destroy'])->middleware(['can:frontend-user.delete'])->name('settings.front-user.delete');
    });

    // Route::post('/submit-contact', [ContactController::class, 'submit'])->name('submit.contact');


Route::prefix('fuels')->group(function(){
  Route::get('/',               [FuelPriceController::class,'index'])->name('fuels.index');
  Route::get('getdata',         [FuelPriceController::class,'getData'])->name('fuels.getdata');
  Route::get('create',          [FuelPriceController::class,'create'])->name('fuels.create');
  Route::post('/',              [FuelPriceController::class,'store'])->name('fuels.store');
  Route::get('{fuelPrice}/edit',[FuelPriceController::class,'edit'])->name('fuels.edit');
  Route::put('{fuelPrice}',     [FuelPriceController::class,'update'])->name('fuels.update');
  Route::delete('{fuelPrice}',  [FuelPriceController::class,'destroy'])->name('fuels.destroy');
});





});
