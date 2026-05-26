<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\customerController;
use App\Http\Controllers\WebsiteController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');

// Root view routes
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/service', [WebsiteController::class, 'service'])->name('service');
Route::get('/services', [WebsiteController::class, 'service'])->name('services');
Route::get('/network', [WebsiteController::class, 'network'])->name('network');
Route::get('/contact-us', [WebsiteController::class, 'contactUs'])->name('contact-us');
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
Route::get('/webinar', [WebsiteController::class, 'webinar'])->name('webinar');
Route::get('/currency-calculator', [WebsiteController::class, 'currencyCalculator'])->name('currency-calculator');
Route::get('/world-weather', [WebsiteController::class, 'worldWeather'])->name('world-weather');
Route::get('/world-time', [WebsiteController::class, 'worldTime'])->name('world-time');
Route::get('/partnership', [WebsiteController::class, 'partner'])->name('partnership');
Route::get('/document-download', [WebsiteController::class, 'documentDownload'])->name('document-download');
Route::get('/barcode-generator', [WebsiteController::class, 'barcodeGenerator'])->name('barcode-generator');
Route::get('/shipping-rate-calculator', [WebsiteController::class, 'shippingRateCalculator'])->name('shipping-rate-calculator');
Route::get('/hsn-finder', [WebsiteController::class, 'hsnFinder'])->name('hsn-finder');





// Admin routes
Route::prefix('admin')->group(function () {
    
    // Public Authentication Routes (No Middleware)
    Route::get('/', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'loginPost'])->name('admin.login.post');
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
        Route::get('/leads-dashboard', [AdminController::class, 'leadsDashboard'])->name('admin.leads-dashboard');
        Route::get('/project-dashboard', [AdminController::class, 'projectDashboard'])->name('admin.project-dashboard');
        
        // CRM Routes
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies');
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
    Route::get('/change-blog', [AdminController::class, 'changeBlog'])->name('admin.change-blog');
    Route::post('/store-blog', [AdminController::class, 'storeBlog'])->name('admin.store-blog');
    Route::post('/update-blog/{id}', [AdminController::class, 'updateBlog'])->name('admin.update-blog');
    Route::delete('/delete-blog/{id}', [AdminController::class, 'deleteBlog'])->name('admin.delete-blog');
    
    Route::post('/upload-blog-image', [AdminController::class, 'uploadBlogImage'])->name('admin.upload-blog-image');
    Route::post('/upload-multiple-blog-images', [AdminController::class, 'uploadMultipleBlogImages'])->name('admin.upload-multiple-blog-images');
    Route::get('/create-blog', [AdminController::class, 'createBlog'])->name('admin.create-blog');
    Route::get('/edit-blog/{id}', [AdminController::class, 'editBlog'])->name('admin.edit-blog');
    
    // E-Book Management Routes
    Route::get('/change-ebook', [AdminController::class, 'changeEbook'])->name('admin.change-ebook');
    Route::post('/store-ebook', [AdminController::class, 'storeEbook'])->name('admin.store-ebook');
    Route::post('/update-ebook/{id}', [AdminController::class, 'updateEbook'])->name('admin.update-ebook');
    Route::delete('/delete-ebook/{id}', [AdminController::class, 'deleteEbook'])->name('admin.delete-ebook');
    Route::get('/create-ebook', [AdminController::class, 'createEbook'])->name('admin.create-ebook');
    Route::get('/edit-ebook/{id}', [AdminController::class, 'editEbook'])->name('admin.edit-ebook');
    Route::get('/get-ebook/{id}', [AdminController::class, 'getEbook'])->name('admin.get-ebook');
    
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
    Route::get('/change-partnership', [AdminController::class, 'changePartnership'])->name('admin.change-partnership');
    Route::post('/store-partnership', [AdminController::class, 'storePartnership'])->name('admin.store-partnership');
    Route::post('/update-partnership/{id}', [AdminController::class, 'updatePartnership'])->name('admin.update-partnership');
    Route::delete('/delete-partnership/{id}', [AdminController::class, 'deletePartnership'])->name('admin.delete-partnership');
    Route::get('/create-partnership', [AdminController::class, 'createPartnership'])->name('admin.create-partnership');
    Route::get('/edit-partnership/{id}', [AdminController::class, 'editPartnership'])->name('admin.edit-partnership');
    Route::get('/get-partnership/{id}', [AdminController::class, 'getPartnership'])->name('admin.get-partnership');
    
    // Edit All Partnership Content
    Route::get('/edit-all-partnership', [AdminController::class, 'editAllPartnership'])->name('admin.edit-all-partnership');
    Route::post('/update-all-partnership', [AdminController::class, 'updateAllPartnership'])->name('admin.update-all-partnership');

    // Document Download Page Management Routes
    Route::get('/change-document-download', [AdminController::class, 'changeDocumentDownload'])->name('admin.change-document-download');
    Route::post('/store-document-download', [AdminController::class, 'storeDocumentDownload'])->name('admin.store-document-download');
    Route::post('/update-document-download/{id}', [AdminController::class, 'updateDocumentDownload'])->name('admin.update-document-download');
    Route::delete('/delete-document-download/{id}', [AdminController::class, 'deleteDocumentDownload'])->name('admin.delete-document-download');
    Route::get('/create-document-download', [AdminController::class, 'createDocumentDownload'])->name('admin.create-document-download');
    Route::get('/edit-document-download/{id}', [AdminController::class, 'editDocumentDownload'])->name('admin.edit-document-download');
    Route::get('/get-document-download/{id}', [AdminController::class, 'getDocumentDownload'])->name('admin.get-document-download');
    Route::get('/edit-all-document-download', [AdminController::class, 'editAllDocumentDownload'])->name('admin.edit-all-document-download');
    Route::post('/update-all-document-download', [AdminController::class, 'updateAllDocumentDownload'])->name('admin.update-all-document-download');
    Route::post('/update-document-download-page-meta', [AdminController::class, 'updateDocumentDownloadPageMeta'])->name('admin.update-document-download-page-meta');
    
    // Currency Calculator Page Management Routes
    Route::get('/change-currency-calculator', [AdminController::class, 'changeCurrencyCalculator'])->name('admin.change-currency-calculator');
    Route::post('/store-currency-calculator', [AdminController::class, 'storeCurrencyCalculator'])->name('admin.store-currency-calculator');
    Route::post('/update-currency-calculator/{id}', [AdminController::class, 'updateCurrencyCalculator'])->name('admin.update-currency-calculator');
    Route::delete('/delete-currency-calculator/{id}', [AdminController::class, 'deleteCurrencyCalculator'])->name('admin.delete-currency-calculator');
    Route::get('/create-currency-calculator', [AdminController::class, 'createCurrencyCalculator'])->name('admin.create-currency-calculator');
    Route::get('/edit-currency-calculator/{id}', [AdminController::class, 'editCurrencyCalculator'])->name('admin.edit-currency-calculator');
    Route::get('/get-currency-calculator/{id}', [AdminController::class, 'getCurrencyCalculator'])->name('admin.get-currency-calculator');
    
    // World Weather Page Management Routes
    Route::get('/change-world-weather', [AdminController::class, 'changeWorldWeather'])->name('admin.change-world-weather');
    Route::post('/store-world-weather', [AdminController::class, 'storeWorldWeather'])->name('admin.store-world-weather');
    Route::post('/update-world-weather/{id}', [AdminController::class, 'updateWorldWeather'])->name('admin.update-world-weather');
    Route::delete('/delete-world-weather/{id}', [AdminController::class, 'deleteWorldWeather'])->name('admin.delete-world-weather');
    Route::get('/create-world-weather', [AdminController::class, 'createWorldWeather'])->name('admin.create-world-weather');
    Route::get('/edit-world-weather/{id}', [AdminController::class, 'editWorldWeather'])->name('admin.edit-world-weather');
    Route::get('/get-world-weather/{id}', [AdminController::class, 'getWorldWeather'])->name('admin.get-world-weather');
    
    // World Time Page Management Routes
    Route::get('/change-world-time', [AdminController::class, 'changeWorldTime'])->name('admin.change-world-time');
    Route::post('/store-world-time', [AdminController::class, 'storeWorldTime'])->name('admin.store-world-time');
    Route::post('/update-world-time/{id}', [AdminController::class, 'updateWorldTime'])->name('admin.update-world-time');
    Route::delete('/delete-world-time/{id}', [AdminController::class, 'deleteWorldTime'])->name('admin.delete-world-time');
    Route::get('/create-world-time', [AdminController::class, 'createWorldTime'])->name('admin.create-world-time');
    Route::get('/edit-world-time/{id}', [AdminController::class, 'editWorldTime'])->name('admin.edit-world-time');
    Route::get('/get-world-time/{id}', [AdminController::class, 'getWorldTime'])->name('admin.get-world-time');
    
    Route::get('/blog-categories', [AdminController::class, 'blogCategories'])->name('admin.blog-categories');
    Route::get('/blog-comments', [AdminController::class, 'blogComments'])->name('admin.blog-comments');
    Route::get('/blog-tags', [AdminController::class, 'blogTags'])->name('admin.blog-tags');
    Route::get('/countries', [AdminController::class, 'countries'])->name('admin.countries');
    Route::get('/states', [AdminController::class, 'states'])->name('admin.states');
    Route::get('/cities', [AdminController::class, 'cities'])->name('admin.cities');
    Route::get('/get-blog/{id}', [AdminController::class, 'getBlog'])->name('admin.get-blog');
    Route::get('/testimonials', [AdminController::class, 'testimonials'])->name('admin.testimonials');
    Route::get('/faq', [AdminController::class, 'faq'])->name('admin.faq');
    
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
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Custom Form Routes
    Route::get('/csb5-form', [AdminController::class, 'csb5Form'])->name('admin.csb5-form');
    Route::get('/form-kyc', [AdminController::class, 'formKyc'])->name('admin.form-kyc');
    
    // Website Management Routes
    Route::get('/change-about-us', [AdminController::class, 'changeAboutUs'])->name('admin.change-about-us');
    Route::post('/update-about-us', [AdminController::class, 'updateAboutUs'])->name('admin.update-about-us');
    Route::get('/get-about-content', [AdminController::class, 'getAboutContent'])->name('admin.get-about-content');
    Route::post('/update-about-content/{id}', [AdminController::class, 'updateAboutContent'])->name('admin.update-about-content');
    Route::delete('/delete-about-content/{id}', [AdminController::class, 'deleteAboutContent'])->name('admin.delete-about-content');
    
    // Home Page Management Routes
    Route::get('/change-home', [AdminController::class, 'changeHome'])->name('admin.change-home');
    Route::get('/update-home', [AdminController::class, 'updateHome'])->name('admin.update-home');
    Route::post('/update-home-content/{id}', [AdminController::class, 'updateHomeContent'])->name('admin.update-home-content');
    Route::post('/update-multiple-home-content', [AdminController::class, 'updateMultipleHomeContent'])->name('admin.update-multiple-home-content');
    
    // Service Page Management Routes
    Route::post('/update-service-content/{id}', [AdminController::class, 'updateServiceContent'])->name('admin.update-service-content');
    Route::delete('/delete-service-content/{id}', [AdminController::class, 'deleteServiceContent'])->name('admin.delete-service-content');
    Route::get('/change-volumetric-calculator', [AdminController::class, 'volumetricCalculator'])->name('admin.change-volumetric-calculator');
    Route::post('/update-volumetric-calculator-content/{id}', [AdminController::class, 'updateVolumetricCalculatorContent'])->name('admin.update-volumetric-calculator-content');
    Route::delete('/delete-volumetric-calculator-content/{id}', [AdminController::class, 'deleteVolumetricCalculatorContent'])->name('admin.delete-volumetric-calculator-content');
    
    // Network Page Management Routes
    Route::get('/change-network', [AdminController::class, 'changeNetwork'])->name('admin.change-network');
    Route::put('/update-network-office/{id}', [AdminController::class, 'updateNetworkOffice'])->name('admin.update-network-office');
    Route::post('/store-network-office', [AdminController::class, 'storeNetworkOffice'])->name('admin.store-network-office');
    Route::delete('/delete-network-office/{id}', [AdminController::class, 'deleteNetworkOffice'])->name('admin.delete-network-office');
    Route::post('/store-faq', [AdminController::class, 'storeFaq'])->name('admin.store-faq');
    Route::put('/update-faq/{id}', [AdminController::class, 'updateFaq'])->name('admin.update-faq');
    Route::delete('/delete-faq/{id}', [AdminController::class, 'deleteFaq'])->name('admin.delete-faq');
    Route::get('/change-terms-and-conditions', [AdminController::class, 'changeTermsAndConditions'])->name('admin.change-terms-and-conditions');
    Route::post('/store-terms-and-conditions-content', [AdminController::class, 'storeTermsAndConditionsContent'])->name('admin.store-terms-and-conditions-content');
    Route::post('/update-terms-and-conditions-content/{id}', [AdminController::class, 'updateTermsAndConditionsContent'])->name('admin.update-terms-and-conditions-content');
    Route::delete('/delete-terms-and-conditions-content/{id}', [AdminController::class, 'deleteTermsAndConditionsContent'])->name('admin.delete-terms-and-conditions-content');
    
    // Privacy Policy Page Management Routes
    Route::get('/change-privacy-policy', [AdminController::class, 'changePrivacyPolicy'])->name('admin.change-privacy-policy');
    Route::post('/store-privacy-policy-content', [AdminController::class, 'storePrivacyPolicyContent'])->name('admin.store-privacy-policy-content');
    Route::post('/update-privacy-policy-content/{id}', [AdminController::class, 'updatePrivacyPolicyContent'])->name('admin.update-privacy-policy-content');
    Route::delete('/delete-privacy-policy-content/{id}', [AdminController::class, 'deletePrivacyPolicyContent'])->name('admin.delete-privacy-policy-content');
    
    // Refund & Cancellation Policy Page Management Routes
    Route::get('/change-refund-and-cancellation-policy', [AdminController::class, 'changeRefundAndCancellationPolicy'])->name('admin.change-refund-and-cancellation-policy');
    Route::post('/update-refund-and-cancellation-policy-content/{id}', [AdminController::class, 'updateRefundAndCancellationPolicyContent'])->name('admin.update-refund-and-cancellation-policy-content');
    Route::delete('/delete-refund-and-cancellation-policy-content/{id}', [AdminController::class, 'deleteRefundAndCancellationPolicyContent'])->name('admin.delete-refund-and-cancellation-policy-content');

    // Contact Page Management Routes
    Route::get('/change-contact-page', [AdminController::class, 'changeContactPage'])->name('admin.change-contact-page');
    Route::post('/update-contact-page-content/{id}', [AdminController::class, 'updateContactPageContent'])->name('admin.update-contact-page-content');
    Route::delete('/delete-contact-page-content/{id}', [AdminController::class, 'deleteContactPageContent'])->name('admin.delete-contact-page-content');

    // Warehousing Solutions Page Management Routes
    Route::get('/change-warehousing-solutions', [AdminController::class, 'changeWarehousingSolutions'])->name('admin.change-warehousing-solutions');
    Route::post('/store-warehousing-solutions-content', [AdminController::class, 'storeWarehousingSolutionsContent'])->name('admin.store-warehousing-solutions-content');
    Route::post('/update-warehousing-solutions-content/{id}', [AdminController::class, 'updateWarehousingSolutionsContent'])->name('admin.update-warehousing-solutions-content');
    Route::delete('/delete-warehousing-solutions-content/{id}', [AdminController::class, 'deleteWarehousingSolutionsContent'])->name('admin.delete-warehousing-solutions-content');

    // E-Commerce Logistics Solutions Page Management Routes
    Route::get('/change-e-commerce-logistics-solutions', [AdminController::class, 'changeEcommerceLogisticsSolutions'])->name('admin.change-e-commerce-logistics-solutions');
    Route::post('/store-e-commerce-logistics-solutions-content', [AdminController::class, 'storeEcommerceLogisticsSolutionsContent'])->name('admin.store-e-commerce-logistics-solutions-content');
    Route::post('/update-e-commerce-logistics-solutions-content/{id}', [AdminController::class, 'updateEcommerceLogisticsSolutionsContent'])->name('admin.update-e-commerce-logistics-solutions-content');
    Route::delete('/delete-e-commerce-logistics-solutions-content/{id}', [AdminController::class, 'deleteEcommerceLogisticsSolutionsContent'])->name('admin.delete-e-commerce-logistics-solutions-content');

    // Express Air Freight Solutions Page Management Routes
    Route::get('/change-express-air-freight-solutions', [AdminController::class, 'changeExpressAirFreightSolutions'])->name('admin.change-express-air-freight-solutions');
    Route::post('/store-express-air-freight-solutions-content', [AdminController::class, 'storeExpressAirFreightSolutionsContent'])->name('admin.store-express-air-freight-solutions-content');
    Route::post('/update-express-air-freight-solutions-content/{id}', [AdminController::class, 'updateExpressAirFreightSolutionsContent'])->name('admin.update-express-air-freight-solutions-content');
    Route::delete('/delete-express-air-freight-solutions-content/{id}', [AdminController::class, 'deleteExpressAirFreightSolutionsContent'])->name('admin.delete-express-air-freight-solutions-content');

    // Barcode Generator Page Management Routes
    Route::get('/change-barcode-generator', [AdminController::class, 'changeBarcodeGenerator'])->name('admin.change-barcode-generator');
    Route::post('/update-barcode-generator-content/{id}', [AdminController::class, 'updateBarcodeGeneratorContent'])->name('admin.update-barcode-generator-content');
    Route::delete('/delete-barcode-generator-content/{id}', [AdminController::class, 'deleteBarcodeGeneratorContent'])->name('admin.delete-barcode-generator-content');

    // Shipping Rate Calculator Page Management Routes
    Route::get('/change-shipping-rate-calculator', [AdminController::class, 'changeShippingRateCalculator'])->name('admin.change-shipping-rate-calculator');
    Route::post('/update-shipping-rate-calculator-content/{id}', [AdminController::class, 'updateShippingRateCalculatorContent'])->name('admin.update-shipping-rate-calculator-content');
    Route::delete('/delete-shipping-rate-calculator-content/{id}', [AdminController::class, 'deleteShippingRateCalculatorContent'])->name('admin.delete-shipping-rate-calculator-content');

    // HSN Finder Page Management Routes
    Route::get('/change-hsn-finder', [AdminController::class, 'changeHsnFinder'])->name('admin.change-hsn-finder');
    Route::post('/update-hsn-finder-content/{id}', [AdminController::class, 'updateHsnFinderContent'])->name('admin.update-hsn-finder-content');
    Route::delete('/delete-hsn-finder-content/{id}', [AdminController::class, 'deleteHsnFinderContent'])->name('admin.delete-hsn-finder-content');

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
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/index', [customerController::class, 'index'])->name('customer.index');
    Route::post('/register', [customerController::class, 'register'])->name('customer.register');
    Route::post('/login', [customerController::class, 'login'])->name('customer.login');
    Route::post('/logout', [customerController::class, 'logout'])->name('customer.logout');
    Route::get('/about', [customerController::class, 'about'])->name('customer.about');
    Route::get('/contact', [customerController::class, 'contact'])->name('customer.contact');
    Route::get('/privacy-policy', [customerController::class, 'privacyPolicy'])->name('customer.privacy-policy');
    Route::get('/terms-and-conditions', [customerController::class, 'termsAndConditions'])->name('customer.terms-and-conditions');
    Route::get('/shipping-policy', [customerController::class, 'shippingPolicy'])->name('customer.shipping-policy');
    Route::get('/refund-policy', [customerController::class, 'refundPolicy'])->name('customer.refund-policy');
    Route::get('/cancellation-policy', [customerController::class, 'cancellationPolicy'])->name('customer.cancellation-policy');
    Route::get('/', [customerController::class, 'login'])->name('login');
    Route::post('/check-phone', [customerController::class, 'checkPhone'])->name('check.phone');
    Route::post('/verify-otp', [customerController::class, 'verifyOtp'])->name('verify.otp');
    Route::get('/register', [customerController::class, 'register'])->name('register');
    Route::post('/register', [customerController::class, 'store'])->name('register.store');
    Route::get('/dashboard', [customerController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [customerController::class, 'logout'])->name('logout');
    Route::get('/companies', [customerController::class, 'companies'])->name('companies');
    Route::get('/create-shipment', [customerController::class, 'createShipment'])->name('create-shipment');
    Route::get('/csb5-form', [customerController::class, 'csb5Form'])->name('csb5-form');
    Route::post('/csb5-form', [customerController::class, 'storeCsb5Form'])->name('csb5-form.store');
    Route::post('/kyc-submit', [customerController::class, 'kycSubmit'])->name('kyc.submit');
    Route::post('/create-shipment', [customerController::class, 'storeShipment'])->name('create-shipment.store');
});