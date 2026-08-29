<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WebsiteAdminController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');

// Root view routes
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/service', [WebsiteController::class, 'service'])->name('service');
Route::get('/services', [WebsiteController::class, 'service'])->name('services');
Route::get('/network', [WebsiteController::class, 'network'])->name('network');
Route::get('/contact-us', [WebsiteController::class, 'contactUs'])->name('contact-us');
Route::post('/contact-us', [WebsiteController::class, 'submitContactQuery'])->name('contact-us.submit');
Route::get('/volumetric-calculator', [WebsiteController::class, 'volumetricCalculator'])->name('volumetric-calculator');
Route::get('/terms-and-conditions', [WebsiteController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/privacy-policy', [WebsiteController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/refund-and-cancellation-policy', [WebsiteController::class, 'refundAndCancellationPolicy'])->name('refund-and-cancellation-policy');
Route::get('/warehousing-solutions', [WebsiteController::class, 'warehousingSolutions'])->name('warehousing-solutions');
Route::get('/e-commerce-logistics-solutions', [WebsiteController::class, 'ecommerceLogisticsSolutions'])->name('e-commerce-logistics-solutions');
Route::get('/express-air-freight-solutions', [WebsiteController::class, 'expressAirFreightSolutions'])->name('express-air-freight-solutions');
Route::get('/e-books', [WebsiteController::class, 'eBooks'])->name('e-books');
Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('blogs');
Route::get('/blogdetails/{slug}', [WebsiteController::class, 'blogDetail'])->name('blog.detail');
Route::get('/tracking', [WebsiteController::class, 'trackOrder'])->name('tracking');
Route::post('/tracking/search', [WebsiteController::class, 'searchTracking'])->name('tracking.search');
Route::get('/webinar', [WebsiteController::class, 'webinar'])->name('webinar');
Route::get('/currency-calculator', [WebsiteController::class, 'currencyCalculator'])->name('currency-calculator');
Route::get('/world-weather', [WebsiteController::class, 'worldWeather'])->name('world-weather');
Route::get('/world-time', [WebsiteController::class, 'worldTime'])->name('world-time');
Route::get('/partnership', [WebsiteController::class, 'partner'])->name('partnership');
Route::get('/document-download', [WebsiteController::class, 'documentDownload'])->name('document-download');
Route::get('/barcode-generator', [WebsiteController::class, 'barcodeGenerator'])->name('barcode-generator');
Route::get('/shipping-rate-calculator', [WebsiteController::class, 'shippingRateCalculator'])->name('shipping-rate-calculator');
Route::get('/shipping-rate-calculator/locations', [WebsiteController::class, 'shippingRateLocations'])->name('shipping-rate-calculator.locations');
Route::post('/shipping-rate-calculator/rates', [WebsiteController::class, 'calculateShippingRates'])->name('shipping-rate-calculator.rates');
Route::get('/hsn-finder', [WebsiteController::class, 'hsnFinder'])->name('hsn-finder');

// Newsletter Subscribe Route
Route::post('/subscribe', [WebsiteController::class, 'subscribe'])->name('subscribe');

// FAQ Query Submit Route
Route::post('/faq-query', [WebsiteController::class, 'submitFaqQuery'])->name('faq-query.submit');

Route::get('/index', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/login', [CustomerController::class, 'login'])->name('login');
Route::get('/get-started', [CustomerController::class, 'register'])->name('register');





// Admin routes
Route::prefix('admin')->middleware('log.activity')->group(function () {
    
    // Public Authentication Routes (No Middleware)
    Route::get('/', [AdminController::class, 'login'])->name('admin.login');
    Route::get('/login', [AdminController::class, 'login']);
    Route::post('/login', [AdminController::class, 'loginPost'])->name('admin.login.post');
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('admin.csrf-token');
    Route::get('/register', [AdminController::class, 'register'])->name('admin.register');
    Route::get('/forgot-password', [AdminController::class, 'forgotPassword'])->name('admin.forgot-password');
    Route::get('/reset-password', [AdminController::class, 'resetPassword'])->name('admin.reset-password');
    Route::get('/email-verification', [AdminController::class, 'emailVerification'])->name('admin.email-verification');
    Route::get('/two-step-verification', [AdminController::class, 'twoStepVerification'])->name('admin.two-step-verification');
    Route::get('/lock-screen', [AdminController::class, 'lockScreen'])->name('admin.lock-screen');
    
    // Protected Routes (With AdminAuth Middleware)
    Route::middleware('admin.auth')->group(function () {
        // Logout Route
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
        // Dashboard Routes
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard-chart-data', [AdminController::class, 'dashboardChartData'])->name('admin.dashboard-chart-data');
        Route::get('/delivery-dashboard', [AdminController::class, 'deliveryDashboard'])->name('admin.delivery-dashboard');
        Route::get('/delivery-dashboard-chart-data', [AdminController::class, 'deliveryDashboardChartData'])->name('admin.delivery-dashboard-chart-data');
        Route::get('/delivery-orders', [AdminController::class, 'deliveryOrders'])->name('admin.delivery-orders');
        Route::post('/pickup-delivery', [AdminController::class, 'pickupDelivery'])->name('admin.pickup-delivery');
        Route::post('/received-in-hub', [AdminController::class, 'receivedInHub'])->name('admin.received-in-hub');
        Route::get('/notifications-data', [AdminController::class, 'notificationsData'])->name('admin.notifications.data');
        Route::patch('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.read-all');
        Route::patch('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.read');
        Route::get('/leads-dashboard', [AdminController::class, 'leadsDashboard'])->name('admin.leads-dashboard');
        Route::get('/project-dashboard', [AdminController::class, 'projectDashboard'])->name('admin.project-dashboard');
        
        // CRM Routes
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies');
    Route::post('/assign-delivery', [AdminController::class, 'assignDelivery'])->name('admin.assign-delivery');
    Route::post('/receive-shipment', [AdminController::class, 'receiveShipment'])->name('admin.receive-shipment');
    Route::post('/generate-label', [AdminController::class, 'generateLabel'])->name('admin.generate-label');
    Route::post('/ready-to-dispatch', [AdminController::class, 'readyToDispatch'])->name('admin.ready-to-dispatch');
    Route::get('/delivery-persons', [AdminController::class, 'deliveryPersons'])->name('admin.delivery-persons');
    Route::post('/delivery-persons', [AdminController::class, 'storeDeliveryPerson'])->name('admin.delivery-persons.store');
    Route::put('/delivery-persons/{id}', [AdminController::class, 'updateDeliveryPerson'])->name('admin.delivery-persons.update');

    // Create User (Admin Management) Routes
    Route::get('/create-user', [AdminController::class, 'createUser'])->name('admin.create-user');
    Route::post('/create-user', [AdminController::class, 'storeUser'])->name('admin.create-user.store');
    Route::put('/create-user/{id}', [AdminController::class, 'updateUser'])->name('admin.create-user.update');
    Route::get('/create-user/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.create-user.delete');
    Route::get('/deals', [AdminController::class, 'deals'])->name('admin.deals');
    Route::get('/leads', [AdminController::class, 'leads'])->name('admin.leads');
    Route::get('/pipeline', [AdminController::class, 'pipeline'])->name('admin.pipeline');
    Route::get('/campaign', [AdminController::class, 'campaign'])->name('admin.campaign');
    Route::get('/projects', [AdminController::class, 'projects'])->name('admin.projects');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('admin.tasks');
    Route::get('/proposals', [AdminController::class, 'proposals'])->name('admin.proposals');
    Route::get('/contracts', [AdminController::class, 'contracts'])->name('admin.contracts');
    Route::get('/estimations', [AdminController::class, 'estimations'])->name('admin.estimations');
    Route::get('/invoices', [AdminController::class, 'invoices'])->name('admin.invoices');
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/change-service', [AdminController::class, 'service'])->name('admin.service');
    Route::get('/activities', [AdminController::class, 'activities'])->name('admin.activities');
    
    // Shipping Routes
    // Route::get('/create-shipment', [AdminController::class, 'createShipment'])->name('admin.create-shipment');
    Route::get('/modify-shipment', [AdminController::class, 'modifyShipment'])->name('admin.modify-shipment');
    Route::get('/select-shipment', [AdminController::class, 'selectShipment'])->name('admin.select-shipment');
    Route::get('/shipment-report', [AdminController::class, 'shipmentReport'])->name('admin.shipment-report');
    
    // Manifest Routes
    Route::get('/create-manifest', [AdminController::class, 'createManifest'])->name('admin.create-manifest');
    Route::get('/edit-manifest', [AdminController::class, 'editManifest'])->name('admin.edit-manifest');
    Route::get('/dispatch-manifest', [AdminController::class, 'dispatchManifest'])->name('admin.dispatch-manifest');
    Route::get('/manifest-report', [AdminController::class, 'manifestReport'])->name('admin.manifest-report');
    
    // Account Routes
    Route::get('/wallet-recharge', [AdminController::class, 'walletRecharge'])->name('admin.wallet-recharge');
    Route::get('/account-ledger', [AdminController::class, 'accountLedger'])->name('admin.account-ledger');
    Route::get('/sale-report', [AdminController::class, 'saleReport'])->name('admin.sale-report');
    Route::get('/payment-report', [AdminController::class, 'paymentReport'])->name('admin.payment-report');
    
    // Report Routes
    Route::get('/status-report', [AdminController::class, 'statusReport'])->name('admin.status-report');
    Route::get('/hold-report', [AdminController::class, 'holdReport'])->name('admin.hold-report');
    Route::get('/un-manifest-report', [AdminController::class, 'unManifestReport'])->name('admin.un-manifest-report');
    
    // CRM Settings Routes
    Route::get('/sources', [AdminController::class, 'sources'])->name('admin.sources');
    Route::get('/lost-reason', [AdminController::class, 'lostReason'])->name('admin.lost-reason');
    Route::get('/contact-stage', [AdminController::class, 'contactStage'])->name('admin.contact-stage');
    Route::get('/industry', [AdminController::class, 'industry'])->name('admin.industry');
    Route::get('/calls', [AdminController::class, 'calls'])->name('admin.calls');
    
    // User Management Routes
    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('admin.manage-users');
    Route::get('/roles-permissions', [AdminController::class, 'rolesPermissions'])->name('admin.roles-permissions');
    Route::get('/delete-request', [AdminController::class, 'deleteRequest'])->name('admin.delete-request');
    
    // Membership Routes
    Route::get('/membership-plans', [AdminController::class, 'membershipPlans'])->name('admin.membership-plans');
    Route::get('/membership-addons', [AdminController::class, 'membershipAddons'])->name('admin.membership-addons');
    Route::get('/membership-transactions', [AdminController::class, 'membershipTransactions'])->name('admin.membership-transactions');
    
    // Content Routes
    Route::get('/pages', [AdminController::class, 'pages'])->name('admin.pages');
    
    // Blog Management Routes
    Route::get('/change-blog', [WebsiteAdminController::class, 'changeBlog'])->name('admin.change-blog');
    Route::post('/store-blog', [WebsiteAdminController::class, 'storeBlog'])->name('admin.store-blog');
    Route::post('/update-blog/{id}', [WebsiteAdminController::class, 'updateBlog'])->name('admin.update-blog');
    Route::delete('/delete-blog/{id}', [WebsiteAdminController::class, 'deleteBlog'])->name('admin.delete-blog');
    
    Route::post('/upload-blog-image', [WebsiteAdminController::class, 'uploadBlogImage'])->name('admin.upload-blog-image');
    Route::post('/upload-multiple-blog-images', [WebsiteAdminController::class, 'uploadMultipleBlogImages'])->name('admin.upload-multiple-blog-images');
    Route::post('/upload-e-commerce-image', [WebsiteAdminController::class, 'uploadEcommerceImage'])->name('admin.upload-e-commerce-image');
    Route::post('/upload-warehousing-image', [WebsiteAdminController::class, 'uploadWarehousingImage'])->name('admin.upload-warehousing-image');
    Route::post('/upload-volumetric-image', [WebsiteAdminController::class, 'uploadVolumetricImage'])->name('admin.upload-volumetric-image');
    Route::post('/upload-shipping-rate-image', [WebsiteAdminController::class, 'uploadShippingRateImage'])->name('admin.upload-shipping-rate-image');
    Route::get('/create-blog', [WebsiteAdminController::class, 'createBlog'])->name('admin.create-blog');
    Route::get('/edit-blog/{id}', [WebsiteAdminController::class, 'editBlog'])->name('admin.edit-blog');
    
    // E-Book Management Routes
    Route::get('/change-ebook', [WebsiteAdminController::class, 'changeEbook'])->name('admin.change-ebook');
    Route::post('/store-ebook', [WebsiteAdminController::class, 'storeEbook'])->name('admin.store-ebook');
    Route::post('/update-ebook/{id}', [WebsiteAdminController::class, 'updateEbook'])->name('admin.update-ebook');
    Route::delete('/delete-ebook/{id}', [WebsiteAdminController::class, 'deleteEbook'])->name('admin.delete-ebook');
    Route::get('/create-ebook', [WebsiteAdminController::class, 'createEbook'])->name('admin.create-ebook');
    Route::get('/edit-ebook/{id}', [WebsiteAdminController::class, 'editEbook'])->name('admin.edit-ebook');
    Route::get('/get-ebook/{id}', [WebsiteAdminController::class, 'getEbook'])->name('admin.get-ebook');
    
    // Track Order Page Management Routes
    Route::get('/change-track-order', [AdminController::class, 'changeTrackOrder'])->name('admin.change-track-order');
    Route::post('/store-track-order', [AdminController::class, 'storeTrackOrder'])->name('admin.store-track-order');
    Route::post('/update-track-order/{id}', [AdminController::class, 'updateTrackOrder'])->name('admin.update-track-order');
    Route::delete('/delete-track-order/{id}', [AdminController::class, 'deleteTrackOrder'])->name('admin.delete-track-order');
    Route::get('/create-track-order', [AdminController::class, 'createTrackOrder'])->name('admin.create-track-order');
    Route::get('/edit-track-order/{id}', [AdminController::class, 'editTrackOrder'])->name('admin.edit-track-order');
    Route::get('/get-track-order/{id}', [AdminController::class, 'getTrackOrder'])->name('admin.get-track-order');
    
    // Webinar Page Management Routes
    Route::get('/change-webinar', [AdminController::class, 'changeWebinar'])->name('admin.change-webinar');
    Route::post('/store-webinar', [AdminController::class, 'storeWebinar'])->name('admin.store-webinar');
    Route::post('/update-webinar/{id}', [AdminController::class, 'updateWebinar'])->name('admin.update-webinar');
    Route::delete('/delete-webinar/{id}', [AdminController::class, 'deleteWebinar'])->name('admin.delete-webinar');
    Route::get('/create-webinar', [AdminController::class, 'createWebinar'])->name('admin.create-webinar');
    Route::get('/edit-webinar/{id}', [AdminController::class, 'editWebinar'])->name('admin.edit-webinar');
    Route::get('/get-webinar/{id}', [AdminController::class, 'getWebinar'])->name('admin.get-webinar');
    
    // Partnership Page Management Routes
    Route::get('/change-partnership', [WebsiteAdminController::class, 'changePartnership'])->name('admin.change-partnership');
    Route::post('/store-partnership', [WebsiteAdminController::class, 'storePartnership'])->name('admin.store-partnership');
    Route::post('/update-partnership/{id}', [WebsiteAdminController::class, 'updatePartnership'])->name('admin.update-partnership');
    Route::delete('/delete-partnership/{id}', [WebsiteAdminController::class, 'deletePartnership'])->name('admin.delete-partnership');
    Route::get('/create-partnership', [WebsiteAdminController::class, 'createPartnership'])->name('admin.create-partnership');
    Route::get('/edit-partnership/{id}', [WebsiteAdminController::class, 'editPartnership'])->name('admin.edit-partnership');
    Route::get('/get-partnership/{id}', [WebsiteAdminController::class, 'getPartnership'])->name('admin.get-partnership');
    
    // Edit All Partnership Content
    Route::get('/edit-all-partnership', [WebsiteAdminController::class, 'editAllPartnership'])->name('admin.edit-all-partnership');
    Route::post('/update-all-partnership', [WebsiteAdminController::class, 'updateAllPartnership'])->name('admin.update-all-partnership');

    // Document Download Page Management Routes
    Route::get('/change-document-download', [WebsiteAdminController::class, 'changeDocumentDownload'])->name('admin.change-document-download');
    Route::post('/store-document-download', [WebsiteAdminController::class, 'storeDocumentDownload'])->name('admin.store-document-download');
    Route::post('/update-document-download/{id}', [WebsiteAdminController::class, 'updateDocumentDownload'])->name('admin.update-document-download');
    Route::delete('/delete-document-download/{id}', [WebsiteAdminController::class, 'deleteDocumentDownload'])->name('admin.delete-document-download');
    Route::get('/create-document-download', [WebsiteAdminController::class, 'createDocumentDownload'])->name('admin.create-document-download');
    Route::get('/edit-document-download/{id}', [WebsiteAdminController::class, 'editDocumentDownload'])->name('admin.edit-document-download');
    Route::get('/get-document-download/{id}', [WebsiteAdminController::class, 'getDocumentDownload'])->name('admin.get-document-download');
    Route::get('/edit-all-document-download', [WebsiteAdminController::class, 'editAllDocumentDownload'])->name('admin.edit-all-document-download');
    Route::post('/update-all-document-download', [WebsiteAdminController::class, 'updateAllDocumentDownload'])->name('admin.update-all-document-download');
    Route::post('/update-document-download-page-meta', [WebsiteAdminController::class, 'updateDocumentDownloadPageMeta'])->name('admin.update-document-download-page-meta');
    
    // Currency Calculator Page Management Routes
    Route::get('/change-currency-calculator', [WebsiteAdminController::class, 'changeCurrencyCalculator'])->name('admin.change-currency-calculator');
    Route::post('/store-currency-calculator', [WebsiteAdminController::class, 'storeCurrencyCalculator'])->name('admin.store-currency-calculator');
    Route::post('/update-currency-calculator/{id}', [WebsiteAdminController::class, 'updateCurrencyCalculator'])->name('admin.update-currency-calculator');
    Route::delete('/delete-currency-calculator/{id}', [WebsiteAdminController::class, 'deleteCurrencyCalculator'])->name('admin.delete-currency-calculator');
    Route::get('/create-currency-calculator', [WebsiteAdminController::class, 'createCurrencyCalculator'])->name('admin.create-currency-calculator');
    Route::get('/edit-currency-calculator/{id}', [WebsiteAdminController::class, 'editCurrencyCalculator'])->name('admin.edit-currency-calculator');
    Route::get('/get-currency-calculator/{id}', [WebsiteAdminController::class, 'getCurrencyCalculator'])->name('admin.get-currency-calculator');
    
    // World Weather Page Management Routes
    Route::get('/change-world-weather', [WebsiteAdminController::class, 'changeWorldWeather'])->name('admin.change-world-weather');
    Route::post('/store-world-weather', [WebsiteAdminController::class, 'storeWorldWeather'])->name('admin.store-world-weather');
    Route::post('/update-world-weather/{id}', [WebsiteAdminController::class, 'updateWorldWeather'])->name('admin.update-world-weather');
    Route::delete('/delete-world-weather/{id}', [WebsiteAdminController::class, 'deleteWorldWeather'])->name('admin.delete-world-weather');
    Route::get('/create-world-weather', [WebsiteAdminController::class, 'createWorldWeather'])->name('admin.create-world-weather');
    Route::get('/edit-world-weather/{id}', [WebsiteAdminController::class, 'editWorldWeather'])->name('admin.edit-world-weather');
    Route::get('/get-world-weather/{id}', [WebsiteAdminController::class, 'getWorldWeather'])->name('admin.get-world-weather');
    
    // World Time Page Management Routes
    Route::get('/change-world-time', [WebsiteAdminController::class, 'changeWorldTime'])->name('admin.change-world-time');
    Route::post('/store-world-time', [WebsiteAdminController::class, 'storeWorldTime'])->name('admin.store-world-time');
    Route::post('/update-world-time/{id}', [WebsiteAdminController::class, 'updateWorldTime'])->name('admin.update-world-time');
    Route::delete('/delete-world-time/{id}', [WebsiteAdminController::class, 'deleteWorldTime'])->name('admin.delete-world-time');
    Route::get('/create-world-time', [WebsiteAdminController::class, 'createWorldTime'])->name('admin.create-world-time');
    Route::get('/edit-world-time/{id}', [WebsiteAdminController::class, 'editWorldTime'])->name('admin.edit-world-time');
    Route::get('/get-world-time/{id}', [WebsiteAdminController::class, 'getWorldTime'])->name('admin.get-world-time');
    
    Route::get('/blog-categories', [AdminController::class, 'blogCategories'])->name('admin.blog-categories');
    Route::get('/blog-comments', [AdminController::class, 'blogComments'])->name('admin.blog-comments');
    Route::get('/blog-tags', [AdminController::class, 'blogTags'])->name('admin.blog-tags');
    Route::get('/countries', [AdminController::class, 'countries'])->name('admin.countries');
    Route::get('/states', [AdminController::class, 'states'])->name('admin.states');
    Route::get('/cities', [AdminController::class, 'cities'])->name('admin.cities');
    Route::get('/get-blog/{id}', [WebsiteAdminController::class, 'getBlog'])->name('admin.get-blog');
    Route::get('/testimonials', [WebsiteAdminController::class, 'testimonials'])->name('admin.testimonials');
    Route::get('/faq', [WebsiteAdminController::class, 'faq'])->name('admin.faq');
    
    // Support Routes
    Route::get('/contact-messages', [AdminController::class, 'contactMessages'])->name('admin.contact-messages');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('admin.tickets');
    
    // Settings Routes
    Route::get('/profile-settings', [AdminController::class, 'profileSettings'])->name('admin.profile-settings');
    Route::get('/security-settings', [AdminController::class, 'securitySettings'])->name('admin.security-settings');
    Route::get('/notifications-settings', [AdminController::class, 'notificationsSettings'])->name('admin.notifications-settings');
    Route::get('/connected-apps', [AdminController::class, 'connectedApps'])->name('admin.connected-apps');
    Route::get('/company-settings', [AdminController::class, 'companySettings'])->name('admin.company-settings');
    Route::get('/localization-settings', [AdminController::class, 'localizationSettings'])->name('admin.localization-settings');
    Route::get('/prefixes-settings', [AdminController::class, 'prefixesSettings'])->name('admin.prefixes-settings');
    Route::get('/preference-settings', [AdminController::class, 'preferenceSettings'])->name('admin.preference-settings');
    Route::get('/appearance-settings', [AdminController::class, 'appearanceSettings'])->name('admin.appearance-settings');
    Route::get('/language-settings', [AdminController::class, 'languageSettings'])->name('admin.language-settings');
    Route::get('/invoice-settings', [AdminController::class, 'invoiceSettings'])->name('admin.invoice-settings');
    Route::get('/printers-settings', [AdminController::class, 'printersSettings'])->name('admin.printers-settings');
    Route::get('/custom-fields-setting', [AdminController::class, 'customFieldsSetting'])->name('admin.custom-fields-setting');
    Route::get('/email-settings', [AdminController::class, 'emailSettings'])->name('admin.email-settings');
    Route::get('/sms-gateways', [AdminController::class, 'smsGateways'])->name('admin.sms-gateways');
    Route::get('/gdpr-cookies', [AdminController::class, 'gdprCookies'])->name('admin.gdpr-cookies');
    Route::get('/payment-gateways', [AdminController::class, 'paymentGateways'])->name('admin.payment-gateways');
    Route::get('/bank-accounts', [AdminController::class, 'bankAccounts'])->name('admin.bank-accounts');
    Route::get('/tax-rates', [AdminController::class, 'taxRates'])->name('admin.tax-rates');
    Route::get('/currencies', [AdminController::class, 'currencies'])->name('admin.currencies');
    Route::get('/sitemap', [AdminController::class, 'sitemap'])->name('admin.sitemap');
    Route::get('/clear-cache', [AdminController::class, 'clearCache'])->name('admin.clear-cache');
    Route::get('/storage', [AdminController::class, 'storage'])->name('admin.storage');
    Route::get('/cronjob', [AdminController::class, 'cronjob'])->name('admin.cronjob');
    Route::get('/ban-ip-address', [AdminController::class, 'banIpAddress'])->name('admin.ban-ip-address');
    Route::get('/system-backup', [AdminController::class, 'systemBackup'])->name('admin.system-backup');
    Route::get('/database-backup', [AdminController::class, 'databaseBackup'])->name('admin.database-backup');
    Route::get('/system-update', [AdminController::class, 'systemUpdate'])->name('admin.system-update');
    
    // Applications Routes
    Route::get('/chat', [AdminController::class, 'chat'])->name('admin.chat');
    Route::get('/video-call', [AdminController::class, 'videoCall'])->name('admin.video-call');
    Route::get('/audio-call', [AdminController::class, 'audioCall'])->name('admin.audio-call');
    Route::get('/call-history', [AdminController::class, 'callHistory'])->name('admin.call-history');
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/email', [AdminController::class, 'email'])->name('admin.email');
    Route::get('/todo', [AdminController::class, 'todo'])->name('admin.todo');
    Route::get('/notes', [AdminController::class, 'notes'])->name('admin.notes');
    Route::get('/file-manager', [AdminController::class, 'fileManager'])->name('admin.file-manager');
    Route::get('/social-feed', [AdminController::class, 'socialFeed'])->name('admin.social-feed');
    Route::get('/kanban-view', [AdminController::class, 'kanbanView'])->name('admin.kanban-view');
    Route::get('/invoice-app', [AdminController::class, 'invoiceApp'])->name('admin.invoice-app');
    
    // Reports Routes
    Route::get('/lead-reports', [AdminController::class, 'leadReports'])->name('admin.lead-reports');
    Route::get('/deal-reports', [AdminController::class, 'dealReports'])->name('admin.deal-reports');
    Route::get('/contact-reports', [AdminController::class, 'contactReports'])->name('admin.contact-reports');
    Route::get('/company-reports', [AdminController::class, 'companyReports'])->name('admin.company-reports');
    Route::get('/project-reports', [AdminController::class, 'projectReports'])->name('admin.project-reports');
    Route::get('/task-reports', [AdminController::class, 'taskReports'])->name('admin.task-reports');
    
    // UI Interface Routes
    Route::get('/ui-accordion', [AdminController::class, 'uiAccordion'])->name('admin.ui-accordion');
    Route::get('/ui-alerts', [AdminController::class, 'uiAlerts'])->name('admin.ui-alerts');
    Route::get('/ui-avatar', [AdminController::class, 'uiAvatar'])->name('admin.ui-avatar');
    Route::get('/ui-badges', [AdminController::class, 'uiBadges'])->name('admin.ui-badges');
    Route::get('/ui-breadcrumb', [AdminController::class, 'uiBreadcrumb'])->name('admin.ui-breadcrumb');
    Route::get('/ui-buttons', [AdminController::class, 'uiButtons'])->name('admin.ui-buttons');
    Route::get('/ui-buttons-group', [AdminController::class, 'uiButtonsGroup'])->name('admin.ui-buttons-group');
    Route::get('/ui-cards', [AdminController::class, 'uiCards'])->name('admin.ui-cards');
    Route::get('/ui-carousel', [AdminController::class, 'uiCarousel'])->name('admin.ui-carousel');
    Route::get('/ui-collapse', [AdminController::class, 'uiCollapse'])->name('admin.ui-collapse');
    Route::get('/ui-dropdowns', [AdminController::class, 'uiDropdowns'])->name('admin.ui-dropdowns');
    Route::get('/ui-ratio', [AdminController::class, 'uiRatio'])->name('admin.ui-ratio');
    Route::get('/ui-grid', [AdminController::class, 'uiGrid'])->name('admin.ui-grid');
    Route::get('/ui-images', [AdminController::class, 'uiImages'])->name('admin.ui-images');
    Route::get('/ui-links', [AdminController::class, 'uiLinks'])->name('admin.ui-links');
    Route::get('/ui-list-group', [AdminController::class, 'uiListGroup'])->name('admin.ui-list-group');
    Route::get('/ui-modals', [AdminController::class, 'uiModals'])->name('admin.ui-modals');
    Route::get('/ui-offcanvas', [AdminController::class, 'uiOffcanvas'])->name('admin.ui-offcanvas');
    Route::get('/ui-pagination', [AdminController::class, 'uiPagination'])->name('admin.ui-pagination');
    Route::get('/ui-placeholders', [AdminController::class, 'uiPlaceholders'])->name('admin.ui-placeholders');
    Route::get('/ui-popovers', [AdminController::class, 'uiPopovers'])->name('admin.ui-popovers');
    Route::get('/ui-progress', [AdminController::class, 'uiProgress'])->name('admin.ui-progress');
    Route::get('/ui-scrollspy', [AdminController::class, 'uiScrollspy'])->name('admin.ui-scrollspy');
    Route::get('/ui-spinner', [AdminController::class, 'uiSpinner'])->name('admin.ui-spinner');
    Route::get('/ui-nav-tabs', [AdminController::class, 'uiNavTabs'])->name('admin.ui-nav-tabs');
    Route::get('/ui-toasts', [AdminController::class, 'uiToasts'])->name('admin.ui-toasts');
    Route::get('/ui-tooltips', [AdminController::class, 'uiTooltips'])->name('admin.ui-tooltips');
    Route::get('/ui-typography', [AdminController::class, 'uiTypography'])->name('admin.ui-typography');
    Route::get('/ui-utilities', [AdminController::class, 'uiUtilities'])->name('admin.ui-utilities');
    
    // Advanced UI Routes
    Route::get('/ui-dragula', [AdminController::class, 'uiDragula'])->name('admin.ui-dragula');
    Route::get('/ui-clipboard', [AdminController::class, 'uiClipboard'])->name('admin.ui-clipboard');
    Route::get('/ui-rangeslider', [AdminController::class, 'uiRangeslider'])->name('admin.ui-rangeslider');
    Route::get('/ui-sweetalerts', [AdminController::class, 'uiSweetalerts'])->name('admin.ui-sweetalerts');
    Route::get('/ui-lightbox', [AdminController::class, 'uiLightbox'])->name('admin.ui-lightbox');
    Route::get('/ui-rating', [AdminController::class, 'uiRating'])->name('admin.ui-rating');
    Route::get('/ui-scrollbar', [AdminController::class, 'uiScrollbar'])->name('admin.ui-scrollbar');
    
    // Forms Routes
    Route::get('/form-basic-inputs', [AdminController::class, 'formBasicInputs'])->name('admin.form-basic-inputs');
    Route::get('/form-checkbox-radios', [AdminController::class, 'formCheckboxRadios'])->name('admin.form-checkbox-radios');
    Route::get('/form-input-groups', [AdminController::class, 'formInputGroups'])->name('admin.form-input-groups');
    Route::get('/form-grid-gutters', [AdminController::class, 'formGridGutters'])->name('admin.form-grid-gutters');
    Route::get('/form-mask', [AdminController::class, 'formMask'])->name('admin.form-mask');
    Route::get('/form-fileupload', [AdminController::class, 'formFileupload'])->name('admin.form-fileupload');
    Route::get('/form-horizontal', [AdminController::class, 'formHorizontal'])->name('admin.form-horizontal');
    Route::get('/form-vertical', [AdminController::class, 'formVertical'])->name('admin.form-vertical');
    Route::get('/form-floating-labels', [AdminController::class, 'formFloatingLabels'])->name('admin.form-floating-labels');
    Route::get('/form-validation', [AdminController::class, 'formValidation'])->name('admin.form-validation');
    Route::get('/form-select', [AdminController::class, 'formSelect'])->name('admin.form-select');
    Route::get('/form-wizard', [AdminController::class, 'formWizard'])->name('admin.form-wizard');
    Route::get('/form-pickers', [AdminController::class, 'formPickers'])->name('admin.form-pickers');
    Route::get('/form-editors', [AdminController::class, 'formEditors'])->name('admin.form-editors');
    
    // Tables Routes
    Route::get('/tables-basic', [AdminController::class, 'tablesBasic'])->name('admin.tables-basic');
    Route::get('/data-tables', [AdminController::class, 'dataTables'])->name('admin.data-tables');
    
    // Charts Routes
    Route::get('/chart-apex', [AdminController::class, 'chartApex'])->name('admin.chart-apex');
    Route::get('/chart-c3', [AdminController::class, 'chartC3'])->name('admin.chart-c3');
    Route::get('/chart-js', [AdminController::class, 'chartJs'])->name('admin.chart-js');
    Route::get('/chart-morris', [AdminController::class, 'chartMorris'])->name('admin.chart-morris');
    Route::get('/chart-flot', [AdminController::class, 'chartFlot'])->name('admin.chart-flot');
    Route::get('/chart-peity', [AdminController::class, 'chartPeity'])->name('admin.chart-peity');
    
    // Icons Routes
    Route::get('/icon-fontawesome', [AdminController::class, 'iconFontawesome'])->name('admin.icon-fontawesome');
    Route::get('/icon-tabler', [AdminController::class, 'iconTabler'])->name('admin.icon-tabler');
    Route::get('/icon-bootstrap', [AdminController::class, 'iconBootstrap'])->name('admin.icon-bootstrap');
    Route::get('/icon-remix', [AdminController::class, 'iconRemix'])->name('admin.icon-remix');
    Route::get('/icon-feather', [AdminController::class, 'iconFeather'])->name('admin.icon-feather');
    Route::get('/icon-ionic', [AdminController::class, 'iconIonic'])->name('admin.icon-ionic');
    Route::get('/icon-material', [AdminController::class, 'iconMaterial'])->name('admin.icon-material');
    Route::get('/icon-pe7', [AdminController::class, 'iconPe7'])->name('admin.icon-pe7');
    Route::get('/icon-simpleline', [AdminController::class, 'iconSimpleline'])->name('admin.icon-simpleline');
    Route::get('/icon-themify', [AdminController::class, 'iconThemify'])->name('admin.icon-themify');
    Route::get('/icon-weather', [AdminController::class, 'iconWeather'])->name('admin.icon-weather');
    Route::get('/icon-typicon', [AdminController::class, 'iconTypicon'])->name('admin.icon-typicon');
    Route::get('/icon-flag', [AdminController::class, 'iconFlag'])->name('admin.icon-flag');
    
    // Maps Routes
    Route::get('/maps-vector', [AdminController::class, 'mapsVector'])->name('admin.maps-vector');
    Route::get('/maps-leaflet', [AdminController::class, 'mapsLeaflet'])->name('admin.maps-leaflet');
    
    // Error Pages Routes
    Route::get('/error-404', [AdminController::class, 'error404'])->name('admin.error-404');
    Route::get('/error-500', [AdminController::class, 'error500'])->name('admin.error-500');
    Route::get('/blank-page', [AdminController::class, 'blankPage'])->name('admin.blank-page');
    Route::get('/coming-soon', [AdminController::class, 'comingSoon'])->name('admin.coming-soon');
    Route::get('/under-maintenance', [AdminController::class, 'underMaintenance'])->name('admin.under-maintenance');
    
    // Other Routes
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::get('/get-quote', [AdminController::class, 'getQuote'])->name('admin.get-quote');
    Route::get('/track-shipment', [AdminController::class, 'trackShipment'])->name('admin.track-shipment');
    Route::get('/my-profile', [AdminController::class, 'myProfile'])->name('admin.my-profile');
    Route::post('/update-profile', [AdminController::class, 'updateProfile'])->name('admin.update-profile');
    
    // Custom Form Routes
    Route::get('/csb5-form', [AdminController::class, 'csb5Form'])->name('admin.csb5-form');
    Route::get('/form-kyc', [AdminController::class, 'formKyc'])->name('admin.form-kyc');
    
    // Website Management Routes
    Route::get('/change-about-us', [WebsiteAdminController::class, 'changeAboutUs'])->name('admin.change-about-us');
    Route::post('/update-about-us', [WebsiteAdminController::class, 'updateAboutUs'])->name('admin.update-about-us');
    Route::get('/get-about-content', [WebsiteAdminController::class, 'getAboutContent'])->name('admin.get-about-content');
    Route::post('/update-about-content/{id}', [WebsiteAdminController::class, 'updateAboutContent'])->name('admin.update-about-content');
    Route::delete('/delete-about-content/{id}', [WebsiteAdminController::class, 'deleteAboutContent'])->name('admin.delete-about-content');
    
    // Home Page Management Routes
    Route::get('/change-home', [WebsiteAdminController::class, 'changeHome'])->name('admin.change-home');
    Route::get('/update-home', [WebsiteAdminController::class, 'updateHome'])->name('admin.update-home');
    Route::get('/get-home-content/{id}', [WebsiteAdminController::class, 'getHomeContent'])->name('admin.get-home-content');
    Route::post('/update-home-content/{id}', [WebsiteAdminController::class, 'updateHomeContent'])->name('admin.update-home-content');
    Route::post('/update-multiple-home-content', [WebsiteAdminController::class, 'updateMultipleHomeContent'])->name('admin.update-multiple-home-content');
    Route::post('/update-about-media', [WebsiteAdminController::class, 'updateAboutMedia'])->name('admin.update-about-media');
    
    // Service Page Management Routes
    Route::post('/update-service-content/{id}', [WebsiteAdminController::class, 'updateServiceContent'])->name('admin.update-service-content');
    Route::delete('/delete-service-content/{id}', [AdminController::class, 'deleteServiceContent'])->name('admin.delete-service-content');
    Route::get('/change-volumetric-calculator', [WebsiteAdminController::class, 'volumetricCalculator'])->name('admin.change-volumetric-calculator');
    Route::get('/get-volumetric-calculator-content/{id}', [WebsiteAdminController::class, 'getVolumetricCalculatorContent'])->name('admin.get-volumetric-calculator-content');
    Route::post('/update-volumetric-calculator-content/{id}', [WebsiteAdminController::class, 'updateVolumetricCalculatorContent'])->name('admin.update-volumetric-calculator-content');
    Route::delete('/delete-volumetric-calculator-content/{id}', [WebsiteAdminController::class, 'deleteVolumetricCalculatorContent'])->name('admin.delete-volumetric-calculator-content');
    
    // Network Page Management Routes
    Route::get('/change-network', [WebsiteAdminController::class, 'changeNetwork'])->name('admin.change-network');
    Route::put('/update-network-office/{id}', [WebsiteAdminController::class, 'updateNetworkOffice'])->name('admin.update-network-office');
    Route::post('/store-network-office', [WebsiteAdminController::class, 'storeNetworkOffice'])->name('admin.store-network-office');
    Route::delete('/delete-network-office/{id}', [WebsiteAdminController::class, 'deleteNetworkOffice'])->name('admin.delete-network-office');
    Route::post('/store-faq', [WebsiteAdminController::class, 'storeFaq'])->name('admin.store-faq');
    Route::put('/update-faq/{id}', [WebsiteAdminController::class, 'updateFaq'])->name('admin.update-faq');
    Route::delete('/delete-faq/{id}', [WebsiteAdminController::class, 'deleteFaq'])->name('admin.delete-faq');
    Route::post('/store-testimonial', [WebsiteAdminController::class, 'storeTestimonial'])->name('admin.store-testimonial');
    Route::put('/update-testimonial/{id}', [WebsiteAdminController::class, 'updateTestimonial'])->name('admin.update-testimonial');
    Route::delete('/delete-testimonial/{id}', [WebsiteAdminController::class, 'deleteTestimonial'])->name('admin.delete-testimonial');
    Route::get('/change-terms-and-conditions', [WebsiteAdminController::class, 'changeTermsAndConditions'])->name('admin.change-terms-and-conditions');
    Route::post('/store-terms-and-conditions-content', [WebsiteAdminController::class, 'storeTermsAndConditionsContent'])->name('admin.store-terms-and-conditions-content');
    Route::post('/update-terms-and-conditions-content/{id}', [WebsiteAdminController::class, 'updateTermsAndConditionsContent'])->name('admin.update-terms-and-conditions-content');
    Route::delete('/delete-terms-and-conditions-content/{id}', [WebsiteAdminController::class, 'deleteTermsAndConditionsContent'])->name('admin.delete-terms-and-conditions-content');
    
    // Privacy Policy Page Management Routes
    Route::get('/change-privacy-policy', [WebsiteAdminController::class, 'changePrivacyPolicy'])->name('admin.change-privacy-policy');
    Route::post('/store-privacy-policy-content', [WebsiteAdminController::class, 'storePrivacyPolicyContent'])->name('admin.store-privacy-policy-content');
    Route::post('/update-privacy-policy-content/{id}', [WebsiteAdminController::class, 'updatePrivacyPolicyContent'])->name('admin.update-privacy-policy-content');
    Route::delete('/delete-privacy-policy-content/{id}', [WebsiteAdminController::class, 'deletePrivacyPolicyContent'])->name('admin.delete-privacy-policy-content');
    
    // Refund & Cancellation Policy Page Management Routes
    Route::get('/change-refund-and-cancellation-policy', [WebsiteAdminController::class, 'changeRefundAndCancellationPolicy'])->name('admin.change-refund-and-cancellation-policy');
    Route::post('/update-refund-and-cancellation-policy-content/{id}', [WebsiteAdminController::class, 'updateRefundAndCancellationPolicyContent'])->name('admin.update-refund-and-cancellation-policy-content');
    Route::delete('/delete-refund-and-cancellation-policy-content/{id}', [WebsiteAdminController::class, 'deleteRefundAndCancellationPolicyContent'])->name('admin.delete-refund-and-cancellation-policy-content');

    // Contact Page Management Routes
    Route::get('/change-contact-page', [WebsiteAdminController::class, 'changeContactPage'])->name('admin.change-contact-page');
    Route::post('/update-contact-page-content/{id}', [WebsiteAdminController::class, 'updateContactPageContent'])->name('admin.update-contact-page-content');
    Route::delete('/delete-contact-page-content/{id}', [WebsiteAdminController::class, 'deleteContactPageContent'])->name('admin.delete-contact-page-content');

    // Warehousing Solutions Page Management Routes
    Route::get('/change-warehousing-solutions', [WebsiteAdminController::class, 'changeWarehousingSolutions'])->name('admin.change-warehousing-solutions');
    Route::post('/store-warehousing-solutions-content', [WebsiteAdminController::class, 'storeWarehousingSolutionsContent'])->name('admin.store-warehousing-solutions-content');
    Route::post('/update-warehousing-solutions-content/{id}', [WebsiteAdminController::class, 'updateWarehousingSolutionsContent'])->name('admin.update-warehousing-solutions-content');
    Route::delete('/delete-warehousing-solutions-content/{id}', [WebsiteAdminController::class, 'deleteWarehousingSolutionsContent'])->name('admin.delete-warehousing-solutions-content');

    // E-Commerce Logistics Solutions Page Management Routes
    Route::get('/change-e-commerce-logistics-solutions', [WebsiteAdminController::class, 'changeEcommerceLogisticsSolutions'])->name('admin.change-e-commerce-logistics-solutions');
    Route::get('/get-e-commerce-logistics-solutions-content/{id}', [WebsiteAdminController::class, 'getEcommerceLogisticsSolutionsContent'])->name('admin.get-e-commerce-logistics-solutions-content');
    Route::post('/store-e-commerce-logistics-solutions-content', [WebsiteAdminController::class, 'storeEcommerceLogisticsSolutionsContent'])->name('admin.store-e-commerce-logistics-solutions-content');
    Route::post('/update-e-commerce-logistics-solutions-content/{id}', [WebsiteAdminController::class, 'updateEcommerceLogisticsSolutionsContent'])->name('admin.update-e-commerce-logistics-solutions-content');
    Route::delete('/delete-e-commerce-logistics-solutions-content/{id}', [WebsiteAdminController::class, 'deleteEcommerceLogisticsSolutionsContent'])->name('admin.delete-e-commerce-logistics-solutions-content');

    // Express Air Freight Solutions Page Management Routes
    Route::get('/change-express-air-freight-solutions', [WebsiteAdminController::class, 'changeExpressAirFreightSolutions'])->name('admin.change-express-air-freight-solutions');
    Route::post('/store-express-air-freight-solutions-content', [WebsiteAdminController::class, 'storeExpressAirFreightSolutionsContent'])->name('admin.store-express-air-freight-solutions-content');
    Route::post('/update-express-air-freight-solutions-content/{id}', [WebsiteAdminController::class, 'updateExpressAirFreightSolutionsContent'])->name('admin.update-express-air-freight-solutions-content');
    Route::delete('/delete-express-air-freight-solutions-content/{id}', [WebsiteAdminController::class, 'deleteExpressAirFreightSolutionsContent'])->name('admin.delete-express-air-freight-solutions-content');

    // Barcode Generator Page Management Routes
    Route::get('/change-barcode-generator', [WebsiteAdminController::class, 'changeBarcodeGenerator'])->name('admin.change-barcode-generator');
    Route::post('/update-barcode-generator-content/{id}', [WebsiteAdminController::class, 'updateBarcodeGeneratorContent'])->name('admin.update-barcode-generator-content');
    Route::delete('/delete-barcode-generator-content/{id}', [WebsiteAdminController::class, 'deleteBarcodeGeneratorContent'])->name('admin.delete-barcode-generator-content');

    // Shipping Rate Calculator Page Management Routes
    Route::get('/change-shipping-rate-calculator', [WebsiteAdminController::class, 'changeShippingRateCalculator'])->name('admin.change-shipping-rate-calculator');
    Route::post('/update-shipping-rate-calculator-content/{id}', [WebsiteAdminController::class, 'updateShippingRateCalculatorContent'])->name('admin.update-shipping-rate-calculator-content');
    Route::delete('/delete-shipping-rate-calculator-content/{id}', [WebsiteAdminController::class, 'deleteShippingRateCalculatorContent'])->name('admin.delete-shipping-rate-calculator-content');

    // HSN Finder Page Management Routes
    Route::get('/change-hsn-finder', [WebsiteAdminController::class, 'changeHsnFinder'])->name('admin.change-hsn-finder');
    Route::post('/update-hsn-finder-content/{id}', [WebsiteAdminController::class, 'updateHsnFinderContent'])->name('admin.update-hsn-finder-content');
    Route::delete('/delete-hsn-finder-content/{id}', [WebsiteAdminController::class, 'deleteHsnFinderContent'])->name('admin.delete-hsn-finder-content');

    // Common Stats (Fact Number Section) Management Routes
    Route::get('/change-common-stats', [WebsiteAdminController::class, 'changeCommonStats'])->name('admin.change-common-stats');
    Route::post('/update-common-stats/{id}', [WebsiteAdminController::class, 'updateCommonStats'])->name('admin.update-common-stats');
    Route::delete('/delete-common-stats/{id}', [WebsiteAdminController::class, 'deleteCommonStats'])->name('admin.delete-common-stats');

    // Partners Section (Logos) Management Routes
    Route::get('/change-partner-logos', [WebsiteAdminController::class, 'changePartnerLogos'])->name('admin.change-partner-logos');
    Route::post('/store-partner-logo', [WebsiteAdminController::class, 'storePartnerLogo'])->name('admin.store-partner-logo');
    Route::post('/update-partner-logo/{id}', [WebsiteAdminController::class, 'updatePartnerLogo'])->name('admin.update-partner-logo');
    Route::delete('/delete-partner-logo/{id}', [WebsiteAdminController::class, 'deletePartnerLogo'])->name('admin.delete-partner-logo');

    // Subscribers Management Routes
    Route::get('/change-subscribers', [WebsiteAdminController::class, 'changeSubscribers'])->name('admin.change-subscribers');

    // FAQ Queries Management Routes
    Route::get('/change-faq-queries', [WebsiteAdminController::class, 'changeFaqQueries'])->name('admin.change-faq-queries');

    // KYC Management Routes
    Route::get('/kyc-pending', [AdminController::class, 'kycPending'])->name('admin.kyc-pending');
    Route::get('/kyc-approved', [AdminController::class, 'kycApproved'])->name('admin.kyc-approved');
    Route::get('/kyc-rejected', [AdminController::class, 'kycRejected'])->name('admin.kyc-rejected');
    Route::post('/kyc-pending/approve/{id}', [AdminController::class, 'approveKyc'])->name('admin.kyc-pending.approve');
    Route::post('/kyc-pending/reject/{id}', [AdminController::class, 'rejectKyc'])->name('admin.kyc-pending.reject');
    Route::post('/customer/{id}/reset-password', [AdminController::class, 'resetCustomerPassword'])->name('admin.customer.reset-password');
    Route::post('/customer/{id}/recharge-wallet', [AdminController::class, 'rechargeCustomerWallet'])->name('admin.customer.recharge-wallet');

    // Customer Profile, Activate/Deactivate, Excel Export
    Route::get('/customer-profile/{id}', [AdminController::class, 'customerProfile'])->name('admin.customer-profile');
    Route::get('/customer/{id}/kyc-documents/download', [AdminController::class, 'downloadCustomerKycDocuments'])->name('admin.customer.kyc-documents.download');
    Route::get('/customer/{id}/kyc-document/{document}/download', [AdminController::class, 'downloadCustomerKycDocument'])
        ->where('document', '[a-z_]+')
        ->name('admin.customer.kyc-document.download');
    Route::post('/customer/{id}/toggle-status', [AdminController::class, 'toggleCustomerStatus'])->name('admin.customer.toggle-status');
    Route::post('/customer/{id}/toggle-shipment-access', [AdminController::class, 'toggleShipmentAccess'])->name('admin.customer.toggle-shipment-access');
    Route::get('/kyc-export', [AdminController::class, 'exportKycExcel'])->name('admin.kyc-export');

    // Manage Rate Routes
    Route::get('/manage-rate', [AdminController::class, 'manageRate'])->name('admin.manage-rate');
    Route::get('/manage-rate/get-customer-rates', [AdminController::class, 'getCustomerRates'])->name('admin.manage-rate.get-customer-rates');
    Route::get('/manage-rate/export-customer-rates', [AdminController::class, 'exportCustomerRates'])->name('admin.manage-rate.export-customer-rates');
    Route::post('/manage-rate/update/{id}', [AdminController::class, 'updateRate'])->name('admin.manage-rate.update');
    Route::post('/manage-rate/update-customer/{id}', [AdminController::class, 'updateCustomerRate'])->name('admin.manage-rate.update-customer');
    Route::post('/manage-rate/update-customer-end-date/{id}', [AdminController::class, 'updateCustomerEndDate'])->name('admin.manage-rate.update-customer-end-date');
    Route::post('/manage-rate/update-new-rate', [AdminController::class, 'updateNewCustomerRate'])->name('admin.manage-rate.update-new-rate');
    Route::post('/manage-rate/add', [AdminController::class, 'addRate'])->name('admin.manage-rate.add');
    Route::get('/manage-rate/sample', [AdminController::class, 'downloadRateSample'])->name('admin.manage-rate.sample');
    Route::post('/manage-rate/upload', [AdminController::class, 'uploadRateExcel'])->name('admin.manage-rate.upload');

    // Surcharge Management Routes
    Route::get('/manage-surcharges', [AdminController::class, 'manageSurcharges'])->name('admin.manage-surcharges');
    Route::post('/manage-surcharges/store', [AdminController::class, 'storeSurcharge'])->name('admin.manage-surcharges.store');
    Route::post('/manage-surcharges/update/{id}', [AdminController::class, 'updateSurcharge'])->name('admin.manage-surcharges.update');
    Route::post('/manage-surcharges/delete/{id}', [AdminController::class, 'deleteSurcharge'])->name('admin.manage-surcharges.delete');

    // Add Zone & Add Country Routes
    Route::get('/add-zone', [AdminController::class, 'addZone'])->name('admin.add-zone');
    Route::post('/add-zone', [AdminController::class, 'storeZone'])->name('admin.add-zone.store');
    Route::get('/add-zone/sample', [AdminController::class, 'downloadZoneSample'])->name('admin.add-zone.sample');
    Route::post('/add-zone/upload', [AdminController::class, 'uploadZoneExcel'])->name('admin.add-zone.upload');
    Route::get('/add-zone/skipped', [AdminController::class, 'downloadSkippedZones'])->name('admin.add-zone.skipped');
    Route::get('/add-country', [AdminController::class, 'addCountry'])->name('admin.add-country');
    Route::post('/add-country', [AdminController::class, 'storeCountry'])->name('admin.add-country.store');

    // Courier Services Routes (enable/disable services that show rates to customers)
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::post('/services/{id}/toggle-status', [AdminController::class, 'toggleServiceStatus'])->name('admin.services.toggle-status');

    // Super Admin Routes
    Route::get('/company', [AdminController::class, 'company'])->name('admin.company');
    Route::get('/subscription', [AdminController::class, 'subscription'])->name('admin.subscription');
    Route::get('/packages', [AdminController::class, 'packages'])->name('admin.packages');
    Route::get('/domain', [AdminController::class, 'domain'])->name('admin.domain');
    Route::get('/purchase-transaction', [AdminController::class, 'purchaseTransaction'])->name('admin.purchase-transaction');
    
    // Layout Routes
    Route::get('/layout-mini', [AdminController::class, 'layoutMini'])->name('admin.layout-mini');
    Route::get('/layout-hoverview', [AdminController::class, 'layoutHoverview'])->name('admin.layout-hoverview');
    Route::get('/layout-hidden', [AdminController::class, 'layoutHidden'])->name('admin.layout-hidden');
    Route::get('/layout-fullwidth', [AdminController::class, 'layoutFullwidth'])->name('admin.layout-fullwidth');
    Route::get('/layout-rtl', [AdminController::class, 'layoutRtl'])->name('admin.layout-rtl');
    Route::get('/layout-dark', [AdminController::class, 'layoutDark'])->name('admin.layout-dark');
    });
});



// Customer routes
Route::prefix('customer')->name('customer.')->middleware('log.activity')->group(function () {
    // Route::get('/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/register', [CustomerController::class, 'register'])->name('customer.register');
    Route::post('/login', [CustomerController::class, 'login'])->name('customer.login');
    Route::get('/about', [CustomerController::class, 'about'])->name('customer.about');
    Route::get('/contact', [CustomerController::class, 'contact'])->name('customer.contact');
    Route::get('/privacy-policy', [CustomerController::class, 'privacyPolicy'])->name('customer.privacy-policy');
    Route::get('/terms-and-conditions', [CustomerController::class, 'termsAndConditions'])->name('customer.terms-and-conditions');
    Route::get('/shipping-policy', [CustomerController::class, 'shippingPolicy'])->name('customer.shipping-policy');
    Route::get('/refund-policy', [CustomerController::class, 'refundPolicy'])->name('customer.refund-policy');
    Route::get('/cancellation-policy', [CustomerController::class, 'cancellationPolicy'])->name('customer.cancellation-policy');
    // Route::get('/', [CustomerController::class, 'login'])->name('login');
    Route::post('/check-phone', [CustomerController::class, 'checkPhone'])->name('check.phone');
    Route::post('/verify-otp', [CustomerController::class, 'verifyOtp'])->name('verify.otp');
    // Email / Password authentication
    Route::post('/login-password', [CustomerController::class, 'loginWithPassword'])->name('login.password');
    Route::get('/forgot-password', [CustomerController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [CustomerController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [CustomerController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [CustomerController::class, 'resetPassword'])->name('password.update');
    Route::post('/send-registration-otp', [CustomerController::class, 'sendRegistrationOtp'])->name('send.registration.otp');
    Route::post('/verify-registration-otp', [CustomerController::class, 'verifyRegistrationOtp'])->name('verify.registration.otp');
    // Route::get('/register', [CustomerController::class, 'register'])->name('register');
    Route::post('/register', [CustomerController::class, 'store'])->name('register.store');
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard-chart-data', [CustomerController::class, 'dashboardChartData'])->name('dashboard-chart-data');
    Route::post('/logout', [CustomerController::class, 'logout'])->name('logout');
    Route::get('/companies', [CustomerController::class, 'companies'])->name('companies');
    Route::get('/exporter-customers', [CustomerController::class, 'exporterCustomers'])->name('exporter-customers');
    Route::post('/exporter-customers', [CustomerController::class, 'storeExporterCustomer'])->name('exporter-customers.store');
    Route::post('/verify-exporter-customer-aadhar', [KycController::class, 'verifyExporterCustomerAadhar'])->name('verify.exporter-customer-aadhar');
    Route::get('/create-shipment', [CustomerController::class, 'createShipment'])->name('create-shipment');
    Route::get('/zones-by-destination', [CustomerController::class, 'getZonesByDestination'])->name('zones-by-destination');
    Route::get('/csb5-form', [CustomerController::class, 'csb5Form'])->name('csb5-form');
    Route::post('/csb5-form', [CustomerController::class, 'storeCsb5Form'])->name('csb5-form.store');
    Route::post('/csb5-form/standalone', [CustomerController::class, 'storeCsb5Form'])->name('csb5-form.standalone.store');
    Route::post('/kyc-draft', [KycController::class, 'saveKycDraft'])->name('kyc.draft.save');
    Route::post('/kyc-draft-file', [KycController::class, 'uploadKycDraftFile'])->name('kyc.draft-file');
    Route::post('/kyc-submit', [KycController::class, 'kycSubmit'])->name('kyc.submit');
    Route::post('/kyc-agreement/accept', [CustomerController::class, 'acceptMerchantAgreement'])->name('kyc.agreement.accept');
    Route::post('/verify-gst', [KycController::class, 'verifyGst'])->name('verify.gst');
    Route::post('/verify-aadhar', [KycController::class, 'verifyAadhar'])->name('verify.aadhar');
    Route::post('/verify-pan', [KycController::class, 'verifyPan'])->name('verify.pan');
    Route::get('/kyc-personal', [KycController::class, 'personalKyc'])->name('kyc.personal');
    Route::post('/kyc-personal', [KycController::class, 'storePersonalKyc'])->name('kyc.personal.store');
    Route::get('/kyc-summary', [KycController::class, 'kycSummary'])->name('kyc.summary');
    Route::get('/kyc-agreement/download', [KycController::class, 'downloadSignedAgreement'])->name('kyc.agreement.download');
    Route::post('/create-shipment', [CustomerController::class, 'storeShipment'])->name('create-shipment.store');
    Route::get('/bulk-upload', [BulkUploadController::class, 'bulkUpload'])->name('bulk-upload');
    Route::post('/bulk-upload', [BulkUploadController::class, 'processBulkUpload'])->name('bulk-upload.process');
    Route::post('/bulk-upload/preview', [BulkUploadController::class, 'previewBulkUpload'])->name('bulk-upload.preview');
    Route::post('/ups-rate', [CustomerController::class, 'getUpsRate'])->name('ups.rate');
    Route::post('/ups-ship', [CustomerController::class, 'createUpsShipment'])->name('ups.ship');
    Route::get('/view-all-shipments', [CustomerController::class, 'viewAllShipments'])->name('view-all-shipments');
    Route::get('/shipment-label/{invoiceId}', [CustomerController::class, 'getShipmentLabel'])->name('shipment-label');
    Route::get('/transaction-history', [CustomerController::class, 'transactionHistory'])->name('transaction-history');
    Route::get('/wallet-history', [CustomerController::class, 'walletHistory'])->name('wallet-history');
    Route::get('/my-profile', [CustomerController::class, 'myProfile'])->name('my-profile');
    Route::post('/pay-now', [CustomerController::class, 'payNow'])->name('pay-now');
    Route::post('/wallet-recharge', [CustomerController::class, 'walletRecharge'])->name('wallet-recharge');
    Route::get('/wallet-recharge/callback', [CustomerController::class, 'walletRechargeCallback'])->name('wallet-recharge.callback');
    Route::get('/cashfree-checkout', [CustomerController::class, 'cashfreeCheckout'])->name('cashfree-checkout');
    Route::post('/cancel-shipment/{id}', [CustomerController::class, 'cancelShipment'])->name('cancel-shipment');
    Route::post('/mark-packed', [CustomerController::class, 'markPacked'])->name('mark-packed');
    Route::post('/manifest', [CustomerController::class, 'manifestShipment'])->name('manifest');
    Route::post('/bulk-manifest', [CustomerController::class, 'bulkManifestShipments'])->name('bulk-manifest');
    Route::post('/manifest-ship-global-fallback', [CustomerController::class, 'manifestWithShipGlobalFallback'])->name('manifest-ship-global-fallback');
    Route::post('/cancel-shipment-by-shipper', [CustomerController::class, 'cancelShipmentByShipperId'])->name('cancel-shipment-by-shipper');
    Route::get('/search-hs-codes', [CustomerController::class, 'searchHsCodes'])->name('search-hs-codes');
});

// Cashfree payment webhook (outside customer auth; signature-verified server-side)
Route::post('/payment/webhook/cashfree', [PaymentWebhookController::class, 'handleCashfree'])->name('payment.webhook.cashfree');