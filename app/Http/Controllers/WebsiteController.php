<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomePageContent;
use App\Models\AboutPageContent;
use App\Models\ServicePage;
use App\Models\VolumetricCalculatorPage;
use App\Models\NetworkOffice;
use App\Models\TermsAndConditionPage;
use App\Models\PrivacyPolicyPage;
use App\Models\RefundAndCancellationPolicyPage;
use App\Models\ContactUsPage;
use App\Models\WarehousingSolutionsPage;
use App\Models\WarehousingTestimonial;
use App\Models\Testimonial;
use App\Models\EcommerceLogisticsSolutionsPage;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\ExpressAirFreightSolutionsPage;
use App\Models\BarcodeGeneratorPage;
use App\Models\ShippingRateCalculatorPage;
use App\Models\HsnFinderPage;
use App\Models\Faq;
use App\Models\FactNumberSectionCommonPage;

class WebsiteController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Fetch hero, about without ordering (just pluck directly)
        $heroData = HomePageContent::where('section', 'hero')->pluck('content', 'field_name');
        $aboutData = HomePageContent::where('section', 'about')->pluck('content', 'field_name');

        // Process steps – fetch sorted, group by sort_order into step arrays
        $processCollection = HomePageContent::where('section', 'process')
            ->orderBy('sort_order')
            ->get();

        // sort_order=0 contains section_tag and heading (the section header)
        // sort_order>=1 contains step_title+step_desc pairs for each step
        $processSteps = $processCollection->groupBy('sort_order')
            ->filter(function ($group, $key) {
                return $key > 0; // exclude sort_order 0 (header fields)
            })
            ->map(function ($group) {
                return $group->pluck('content', 'field_name')->toArray();
            })
            ->toArray();

        // Re-create $processData for backward compatibility with the view header (lines 433, 436)
        $processData = $processCollection->where('sort_order', 0)->pluck('content', 'field_name')->toArray();

        // Service cards – fetch sorted, then group into individual card arrays by sort_order
        $serviceCardCollection = HomePageContent::where('section', 'service_card')
            ->orderBy('sort_order')
            ->get();

        // Group by sort_order — each card's rows share the same sort_order value (1, 2, 3)
        $cardChunks = $serviceCardCollection->groupBy('sort_order')->values();
        $serviceCard1 = isset($cardChunks[0]) ? $cardChunks[0]->pluck('content', 'field_name')->toArray() : [];
        $serviceCard2 = isset($cardChunks[1]) ? $cardChunks[1]->pluck('content', 'field_name')->toArray() : [];
        $serviceCard3 = isset($cardChunks[2]) ? $cardChunks[2]->pluck('content', 'field_name')->toArray() : [];

        // Shipping solutions – fetch sorted, group by sort_order into individual card arrays
        $shippingCollection = HomePageContent::where('section', 'shipping_solutions')
            ->orderBy('sort_order')
            ->get();

        // sort_order=0 contains heading+description (section header – already accessible via $shippingCollection)
        // sort_order>=1 contains card fields for each solution card
        $shippingCardChunks = $shippingCollection->groupBy('sort_order')->filter(function ($group, $key) {
            return $key > 0; // exclude sort_order 0 (header fields)
        })->values();

        $shippingSolution1 = isset($shippingCardChunks[0]) ? $shippingCardChunks[0]->pluck('content', 'field_name')->toArray() : [];
        $shippingSolution2 = isset($shippingCardChunks[1]) ? $shippingCardChunks[1]->pluck('content', 'field_name')->toArray() : [];
        $shippingSolution3 = isset($shippingCardChunks[2]) ? $shippingCardChunks[2]->pluck('content', 'field_name')->toArray() : [];
        $shippingSolution4 = isset($shippingCardChunks[3]) ? $shippingCardChunks[3]->pluck('content', 'field_name')->toArray() : [];

        // Build a single loopable array of shipping solution cards (each card's fields grouped by sort_order).
        // Each card array contains: card_label, card_title, card_desc, card_point1, card_point2, card_cta, card_image.
        // Used by the front-end "Tailor-Made Shipping Option" section to render nav + cards dynamically.
        $shippingCards = $shippingCardChunks->map(function ($group) {
            return $group->pluck('content', 'field_name')->toArray();
        })->values()->toArray();

        // Also keep $shippingSolutions for backward compatibility with view header (lines 550-557)
        $shippingSolutions = $shippingCollection;

        // Services heading section
        $servicesHeading = HomePageContent::where('section', 'services_heading')
            ->pluck('content', 'field_name');

        // Testimonial heading section (heading + description for the testimonial section header)
        $testimonialHeading = HomePageContent::where('section', 'testimonial_heading')
            ->pluck('content', 'field_name');

        // Testimonials from separate table (common across all pages)
        $testimonials = Testimonial::active()->ordered()->get();

        // Worldwide Marketplaces logos from partnership_page table (section = ecosystem_global)
        $marketplaceLogos = \App\Models\PartnershipPage::bySection('ecosystem_global')->active()->ordered()->get();

        // FAQ grouping - fetched from unified faq table
        $faqs = Faq::byPage('home')->active()->ordered()->get();

        return view('index', compact(
            'heroData',
            'aboutData',
            'processData',
            'processSteps',
            'serviceCard1',
            'serviceCard2',
            'serviceCard3',
            'shippingSolutions',
            'shippingCards',
            'shippingSolution1',
            'shippingSolution2',
            'shippingSolution3',
            'shippingSolution4',
            'servicesHeading',
            'testimonialHeading',
            'testimonials',
            'marketplaceLogos',
            'faqs'
        ));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        $aboutContent = AboutPageContent::all();
        
        // Group by section type
        $heroContent = $aboutContent->where('section_type', 'hero')->first();
        $overview = $aboutContent->where('section_type', 'overview')->first();
        $missionVisionIntro = $aboutContent->where('section_type', 'mission_vision_intro')->first();
        $mission = $aboutContent->where('section_type', 'mission')->first();
        $vision = $aboutContent->where('section_type', 'vision')->first();
        $journeyIntro = $aboutContent->where('section_type', 'journey_intro')->first();
        $milestones = $aboutContent->where('section_type', 'milestone')->sortBy('display_order')->values();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqHeader = $aboutContent->where('section_type', 'faq_header')->first();
        $faqs = Faq::byPage('about')->active()->ordered()->get();
        $commonStats = $this->getCommonStats();

        return view('about', compact(
            'heroContent',
            'overview',
            'missionVisionIntro',
            'mission',
            'vision',
            'journeyIntro',
            'milestones',
            'testimonials',
            'faqHeader',
            'faqs',
            'commonStats'
        ));
    }


    // service pages
    public function service(){
        $services = ServicePage::bySection('services')->active()->ordered()->get();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqs = Faq::byPage('service')->active()->ordered()->get();
        $partners = ServicePage::bySection('partners')->active()->ordered()->get();
        $commonStats = $this->getCommonStats();
        
        return view('service', compact('services', 'testimonials', 'faqs', 'partners', 'commonStats'));
    }

    // volumetric calculator page
    public function volumetricCalculator(){
        $heroData = VolumetricCalculatorPage::bySection('hero')->where('page', 'volumetric-calculator')->first();
        $featuresHeader = VolumetricCalculatorPage::bySection('features_header')->where('page', 'volumetric-calculator')->first();
        $features = VolumetricCalculatorPage::bySection('features')->where('page', 'volumetric-calculator')->ordered()->get();
        $trackCta = VolumetricCalculatorPage::bySection('track_cta')->where('page', 'volumetric-calculator')->first();
        $testimonialsHeader = VolumetricCalculatorPage::bySection('testimonials_header')->where('page', 'volumetric-calculator')->first();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqSidebar = VolumetricCalculatorPage::bySection('faq_sidebar')->where('page', 'volumetric-calculator')->first();
        $faqs = Faq::byPage('volumetric-calculator')->active()->ordered()->get();
        $calculator = VolumetricCalculatorPage::bySection('calculator')->where('page', 'volumetric-calculator')->first();
        
        return view('volumetric-calculator', compact(
            'heroData', 'featuresHeader', 'features', 'trackCta', 
            'testimonialsHeader', 'testimonials', 'faqSidebar', 'faqs', 'calculator'
        ));
    }

    public function network(){
        $indiaOffices = NetworkOffice::india()->active()->ordered()->get();
        $overseasOffices = NetworkOffice::overseas()->active()->ordered()->get();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqs = \App\Models\Faq::byPage('network')->active()->ordered()->get();
        
        return view('network', compact('indiaOffices', 'overseasOffices', 'testimonials', 'faqs'));
    }

    public function termsAndConditions(){
        $pageMeta = TermsAndConditionPage::bySection('_page_meta')->first();
        $sections = TermsAndConditionPage::where('section_key', '!=', '_page_meta')
            ->ordered()
            ->get();
        
        return view('terms-and-conditions', compact(
            'pageMeta', 'sections'
        ));
    }

    public function privacyPolicy(){
        $pageMeta = PrivacyPolicyPage::bySection('_page_meta')->latest('id')->first();
        $dataCollection = PrivacyPolicyPage::bySection('data_collection')->latest('id')->first();
        $dataUsage = PrivacyPolicyPage::bySection('data_usage')->latest('id')->first();
        $dataSharing = PrivacyPolicyPage::bySection('data_sharing')->latest('id')->first();
        $dataSecurity = PrivacyPolicyPage::bySection('data_security')->latest('id')->first();
        $userRights = PrivacyPolicyPage::bySection('user_rights')->latest('id')->first();
        $cookiesPolicy = PrivacyPolicyPage::bySection('cookies_policy')->latest('id')->first();
        $policyUpdates = PrivacyPolicyPage::bySection('policy_updates')->latest('id')->first();

        // Fetch ALL additional sections that don't match predefined keys
        $knownKeys = ['_page_meta', 'data_collection', 'data_usage', 'data_sharing', 'data_security', 'user_rights', 'cookies_policy', 'policy_updates'];
        $additionalSections = PrivacyPolicyPage::whereNotIn('section_key', $knownKeys)
            ->ordered()
            ->get()
            ->groupBy('section_key')
            ->map(function ($items) {
                return $items->last(); // Get the latest record for each section_key
            });
        
        return view('privacy-policy', compact(
            'pageMeta', 'dataCollection', 'dataUsage', 'dataSharing',
            'dataSecurity', 'userRights', 'cookiesPolicy', 'policyUpdates',
            'additionalSections'
        ));
    }

    public function refundAndCancellationPolicy(){
        $pageMeta = RefundAndCancellationPolicyPage::bySection('_page_meta')->first();
        $cancellationPolicy = RefundAndCancellationPolicyPage::bySection('cancellation_policy')->first();
        $refundEligibility = RefundAndCancellationPolicyPage::bySection('refund_eligibility')->first();
        $refundProcess = RefundAndCancellationPolicyPage::bySection('refund_process')->first();
        $nonRefundableItems = RefundAndCancellationPolicyPage::bySection('non_refundable_items')->first();
        $serviceDelays = RefundAndCancellationPolicyPage::bySection('service_delays')->first();
        
        return view('refund-and-cancellation-policy', compact(
            'pageMeta', 'cancellationPolicy', 'refundEligibility', 'refundProcess', 
            'nonRefundableItems', 'serviceDelays'
        ));
    }

    public function partner()
    {
        $hero = \App\Models\PartnershipPage::bySection('hero')->active()->first();
        $aboutSection = \App\Models\PartnershipPage::bySection('about')->active()->first();
        $features = \App\Models\PartnershipPage::bySection('features')->active()->ordered()->get();
        $ecosystemSection = \App\Models\PartnershipPage::bySection('ecosystem')->active()->first();
        $ecosystemGlobalCards = \App\Models\PartnershipPage::bySection('ecosystem_global')->active()->ordered()->get();
        $ecosystemPartnerCards = \App\Models\PartnershipPage::bySection('ecosystem_partner')->active()->ordered()->get();
        $faqSection = \App\Models\PartnershipPage::bySection('faq')->active()->first();
        $faqItems = Faq::byPage('partnership')->active()->ordered()->get();
        $formSection = \App\Models\PartnershipPage::bySection('partner_form')->active()->first();
        // $partnerLogos is now globally shared via AppServiceProvider
        return view('partnership', compact('hero', 'aboutSection', 'features', 'ecosystemSection', 'ecosystemGlobalCards', 'ecosystemPartnerCards', 'faqSection', 'faqItems', 'formSection'));
    }

    public function webinar()
    {
        $webinars = \App\Models\WebinarPage::whereNull('section')->active()->ordered()->get();
        $heroContent = \App\Models\WebinarPage::bySection('hero')->active()->first();
        return view('webinar', compact('webinars', 'heroContent'));
    }

    public function currencyCalculator()
    {
        $hero = \App\Models\CurrencyCalculatorPage::bySection('hero')->active()->ordered()->first();
        $featuresHeader = \App\Models\CurrencyCalculatorPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featureCards = \App\Models\CurrencyCalculatorPage::bySection('features')->where('item_key', 'like', 'feature_card_%')->active()->ordered()->get();
        return view('currency-calculator', compact('hero', 'featuresHeader', 'featureCards'));
    }

    public function worldWeather()
    {
        $hero = \App\Models\WorldWeatherPage::bySection('hero')->active()->ordered()->first();
        $weatherHeader = \App\Models\WorldWeatherPage::bySection('weather_cities')->where('item_key', 'weather_cities_header')->active()->first();
        $weatherCities = \App\Models\WorldWeatherPage::bySection('weather_cities')->where('item_key', 'like', 'city_%')->active()->ordered()->get();
        $featuresHeader = \App\Models\WorldWeatherPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featureCards = \App\Models\WorldWeatherPage::bySection('features')->where('item_key', 'like', 'feature_card_%')->active()->ordered()->get();
        return view('world-weather', compact('hero', 'weatherHeader', 'weatherCities', 'featuresHeader', 'featureCards'));
    }

    public function worldTime()
    {
        $hero = \App\Models\WorldTimePage::bySection('hero')->active()->ordered()->first();
        $timeHeader = \App\Models\WorldTimePage::bySection('time_cities')->where('item_key', 'time_cities_header')->active()->first();
        $timeCities = \App\Models\WorldTimePage::bySection('time_cities')->where('item_key', 'like', 'city_%')->active()->ordered()->get();
        $featuresHeader = \App\Models\WorldTimePage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featureCards = \App\Models\WorldTimePage::bySection('features')->where('item_key', 'like', 'feature_card_%')->active()->ordered()->get();
        return view('world-time', compact('hero', 'timeHeader', 'timeCities', 'featuresHeader', 'featureCards'));
    }

    public function contactUs(){
        $pageMeta = ContactUsPage::bySection('hero')->first();
        $contactInfo = ContactUsPage::bySection('contact_info')->first();
        
        return view('contact-us', compact('pageMeta', 'contactInfo'));
    }

    public function warehousingSolutions(){
        $heroContent = WarehousingSolutionsPage::bySection('hero')->active()->first();
        $overviewContent = WarehousingSolutionsPage::bySection('overview')->active()->first();
        $featuresHeaderContent = WarehousingSolutionsPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featuresContent = WarehousingSolutionsPage::bySection('features')->where('item_key', '!=', 'features_header')->active()->ordered()->get();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqHeaderContent = WarehousingSolutionsPage::bySection('faq')->active()->ordered()->first();
        $faqContent = Faq::byPage('warehousing')->active()->ordered()->get();
        $ctaContent = WarehousingSolutionsPage::bySection('cta')->active()->first();
        $commonStats = $this->getCommonStats();
        
        return view('warehousing-solutions', compact(
            'heroContent', 'overviewContent', 'featuresHeaderContent', 'featuresContent', 'testimonials', 'faqContent', 'faqHeaderContent', 'ctaContent', 'commonStats'
        ));
    }

    // e-commerce logistics solutions page
    public function ecommerceLogisticsSolutions(){
        $heroContent = EcommerceLogisticsSolutionsPage::bySection('hero')->active()->first();
        $overviewContent = EcommerceLogisticsSolutionsPage::bySection('overview')->active()->first();
        $featuresHeaderContent = EcommerceLogisticsSolutionsPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featuresContent = EcommerceLogisticsSolutionsPage::bySection('features')->where('item_key', '!=', 'features_header')->active()->ordered()->get();
        $testimonialsHeader = EcommerceLogisticsSolutionsPage::bySection('testimonials')->where('item_key', 'testimonials_header')->active()->first();
        $testimonials = EcommerceLogisticsSolutionsPage::bySection('testimonials')->where('item_key', '!=', 'testimonials_header')->active()->ordered()->get();
        $faqHeader = EcommerceLogisticsSolutionsPage::bySection('faq')->where('item_key', 'faq_header')->active()->first();
        $faqs = Faq::byPage('ecommerce-logistics')->active()->ordered()->get();
        $commonStats = $this->getCommonStats();
        
        return view('e-commerce-logistics-solutions', compact(
            'heroContent', 'overviewContent', 'featuresHeaderContent', 'featuresContent',
            'testimonialsHeader', 'testimonials', 'faqHeader', 'faqs', 'commonStats'
        ));
    }

    // Express Air Freight Solutions page
    public function expressAirFreightSolutions(){
        $heroContent = ExpressAirFreightSolutionsPage::bySection('hero')->active()->first();
        $overviewContent = ExpressAirFreightSolutionsPage::bySection('overview')->active()->first();
        $featuresHeaderContent = ExpressAirFreightSolutionsPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featuresContent = ExpressAirFreightSolutionsPage::bySection('features')->where('item_key', '!=', 'features_header')->active()->ordered()->get();
        $testimonialsHeader = ExpressAirFreightSolutionsPage::bySection('testimonials')->where('item_key', 'testimonials_header')->active()->first();
        $testimonials = ExpressAirFreightSolutionsPage::bySection('testimonials')->where('item_key', '!=', 'testimonials_header')->active()->ordered()->get();
        $faqHeader = ExpressAirFreightSolutionsPage::bySection('faq')->where('item_key', 'faq_header')->active()->first();
        $faqs = Faq::byPage('express-air')->active()->ordered()->get();
        $commonStats = $this->getCommonStats();
        
        return view('express-air-freight-solutions', compact(
            'heroContent', 'overviewContent', 'featuresHeaderContent', 'featuresContent',
            'testimonialsHeader', 'testimonials', 'faqHeader', 'faqs', 'commonStats'
        ));
    }

    public function trackOrder()
    {
        $trackOrderPage = \App\Models\TrackOrderPage::ordered()->get();
        $heroContent = \App\Models\TrackOrderPage::bySection('hero')->active()->first();
        $trackFormContent = \App\Models\TrackOrderPage::bySection('track_form')->active()->first();
        $featuresHeader = \App\Models\TrackOrderPage::bySection('features')->where('item_key', 'features_header')->active()->first();
        $featuresContent = \App\Models\TrackOrderPage::bySection('features')->where('item_key', '!=', 'features_header')->active()->ordered()->get();
        $aboutContent = \App\Models\TrackOrderPage::bySection('about')->active()->first();
        $ctaContent = \App\Models\TrackOrderPage::bySection('cta')->active()->first();
        $faqHeader = \App\Models\TrackOrderPage::bySection('faq')->where('item_key', 'faq_header')->active()->first();
        $faqs = Faq::byPage('track-order')->active()->ordered()->get();
        
        return view('track-order', compact(
            'trackOrderPage', 'heroContent', 'trackFormContent',
            'featuresHeader', 'featuresContent', 'aboutContent',
            'ctaContent', 'faqHeader', 'faqs'
        ));
    }

    public function searchTracking(Request $request)
    {
        $request->validate([
            'awb_number' => 'required|string|min:3',
        ]);

        $awbNumber = $request->input('awb_number');

        $trackingRecords = \App\Models\Tracking::where('awb_number', $awbNumber)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($trackingRecords->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No tracking information found for this AWB number. Please check the number and try again.',
            ]);
        }

        $shipper = $trackingRecords->first()->shipper;
        $consignee = $shipper ? $shipper->consigneeInfo : null;
        $shipment = $trackingRecords->first()->shipment;

        $statusMap = \App\Models\Tracking::getStatusTitleMap();

        $history = $trackingRecords->map(function ($record) use ($statusMap) {
            return [
                'status' => $record->status,
                'title' => $record->title ?? ($statusMap[$record->status] ?? ucfirst(str_replace('_', ' ', $record->status))),
                'timestamp' => $record->created_at ? $record->created_at->format('d M Y, h:i A') : null,
                'uwc_id' => $record->uwc_id,
            ];
        });

        $currentStatus = $trackingRecords->last()->status;
        $currentTitle = $trackingRecords->last()->title ?? ($statusMap[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus)));

        $shipmentDetails = null;
        if ($shipper) {
            $shipmentDetails = [
                'awb_number' => $shipper->awb_number,
                'shipping_method' => $shipper->shipping_method,
                'shipper_name' => $shipper->contact_person,
                'shipper_company' => $shipper->company_name,
                'shipper_city' => $shipper->city,
                'shipper_state' => $shipper->state,
                'shipper_phone' => $shipper->phone_number,
                'shipper_email' => $shipper->email,
            ];
        }

        $consigneeDetails = null;
        if ($consignee) {
            $consigneeDetails = [
                'consignee_name' => $consignee->contact_person ?? $consignee->consignee_name,
                'consignee_city' => $consignee->city,
                'consignee_state' => $consignee->state,
                'consignee_country' => $consignee->delivery_destination,
                'consignee_phone' => $consignee->phone_number,
            ];
        }

        return response()->json([
            'success' => true,
            'awb_number' => $awbNumber,
            'current_status' => $currentStatus,
            'current_title' => $currentTitle,
            'history' => $history,
            'shipment' => $shipmentDetails,
            'consignee' => $consigneeDetails,
        ]);
    }

    public function eBooks()
    {
        $ebooks = \App\Models\Ebook::whereNull('section')->active()->ordered()->get();
        $heroContent = \App\Models\Ebook::bySection('hero')->active()->first();
        $sectionHeader = \App\Models\Ebook::bySection('section_header')->active()->first();
        $faqHeader = \App\Models\Ebook::bySection('faq')->where('item_key', 'faq_header')->active()->first();
        $faqs = Faq::byPage('e-books')->active()->ordered()->get();
        return view('e-books', compact('ebooks', 'heroContent', 'sectionHeader', 'faqHeader', 'faqs'));
    }

    public function blogs()
    {
        $pageMeta = (object) [
            'content' => [
                'badge' => 'Knowledge Base',
                'title' => 'Read Our <span class="moving-gradient-text">Blogs & Articles.</span>',
                'description' => 'Explore expert perspectives, success stories, and shipping strategies shaping the future of commerce.',
            ]
        ];
        $blogs = Blog::active()->ordered()->get();
        $categories = BlogCategory::active()->get();

        return view('blogs', compact('pageMeta', 'blogs', 'categories'));
    }

    /**
     * Display a single blog detail page.
     */
    public function documentDownload()
    {
        $documents = \App\Models\DocumentDownloadPage::active()->ordered()->get();
        $pageMeta = \App\Models\DocumentDownloadPage::bySection('page_meta')->active()->first();
        return view('document-download', compact('documents', 'pageMeta'));
    }

    public function blogDetail($slug)
    {
        $blog = Blog::with('category')->where('slug', $slug)->firstOrFail();

        // Get other blogs for trending sidebar
        $trendingBlogs = Blog::active()
            ->where('id', '!=', $blog->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('blog-detail', compact('blog', 'trendingBlogs'));
    }

    /**
     * Display the barcode generator page.
     */
    public function barcodeGenerator()
    {
        $heroContent = BarcodeGeneratorPage::bySection('hero')->where('status', true)->first();
        $featuresHeading = BarcodeGeneratorPage::bySection('features_heading')->where('status', true)->first();
        $features = BarcodeGeneratorPage::bySection('features')->where('status', true)->orderBy('display_order')->get();
        $trackCta = BarcodeGeneratorPage::bySection('track_cta')->where('status', true)->first();
        $testimonialsHeader = BarcodeGeneratorPage::bySection('testimonials_header')->where('status', true)->first();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqHeader = BarcodeGeneratorPage::bySection('faq_header')->where('status', true)->first();
        $faqs = Faq::byPage('barcode-generator')->active()->ordered()->get();
        $faqContactSidebar = BarcodeGeneratorPage::bySection('faq_contact_sidebar')->where('status', true)->first();

        return view('barcode-generator', compact(
            'heroContent',
            'featuresHeading',
            'features',
            'trackCta',
            'testimonialsHeader',
            'testimonials',
            'faqHeader',
            'faqs',
            'faqContactSidebar'
        ));
    }

    /**
     * Display the shipping rate calculator page.
     */
    public function shippingRateCalculator()
    {
        $heroContent = ShippingRateCalculatorPage::bySection('hero')->where('status', true)->first();
        $featuresHeading = ShippingRateCalculatorPage::bySection('features_heading')->where('status', true)->first();
        $features = ShippingRateCalculatorPage::bySection('features')->where('status', true)->orderBy('display_order')->get();
        $trackCta = ShippingRateCalculatorPage::bySection('track_cta')->where('status', true)->first();
        $testimonialsHeader = ShippingRateCalculatorPage::bySection('testimonials_header')->where('status', true)->first();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqHeader = ShippingRateCalculatorPage::bySection('faq_header')->where('status', true)->first();
        $faqs = Faq::byPage('shipping-rate-calculator')->active()->ordered()->get();
        $faqContactSidebar = ShippingRateCalculatorPage::bySection('faq_contact_sidebar')->where('status', true)->first();

        // Fetch default rates (customer_id = 0) grouped by service, ordered by weight range and zone
        $defaultRates = \App\Models\CourierRate::with('service')
            ->where('customer_id', 0)
            ->orderBy('service_id')
            ->orderBy('wt_range_start')
            ->orderBy('zone_no')
            ->get()
            ->groupBy('service_id');

        return view('shipping-rate-calculator', compact(
            'heroContent',
            'featuresHeading',
            'features',
            'trackCta',
            'testimonialsHeader',
            'testimonials',
            'faqHeader',
            'faqs',
            'faqContactSidebar',
            'defaultRates'
        ));
    }

    /**
     * Display the HSN finder page.
     */
    public function hsnFinder()
    {
        $heroContent = HsnFinderPage::bySection('hero')->where('status', true)->first();
        $featuresHeading = HsnFinderPage::bySection('features_heading')->where('status', true)->first();
        $features = HsnFinderPage::bySection('features')->where('status', true)->orderBy('display_order')->get();
        $testimonialsHeader = HsnFinderPage::bySection('testimonials_header')->where('status', true)->first();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqHeader = HsnFinderPage::bySection('faq_header')->where('status', true)->first();
        $faqs = Faq::byPage('hsn-finder')->active()->ordered()->get();
        $faqContactSidebar = HsnFinderPage::bySection('faq_contact_sidebar')->where('status', true)->first();
        $trackCta = HsnFinderPage::bySection('track_cta')->where('status', true)->first();

        return view('hsn-finder', compact(
            'heroContent',
            'featuresHeading',
            'features',
            'testimonialsHeader',
            'testimonials',
            'faqHeader',
            'faqs',
            'faqContactSidebar',
            'trackCta'
        ));
    }

    /**
     * Handle newsletter subscriber email from footer subscribe form.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->input('email');

        // Check if already subscribed
        $existing = \App\Models\Subscriber::where('email', $email)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already subscribed!',
            ]);
        }

        \App\Models\Subscriber::create([
            'email' => $email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing!',
        ]);
    }

    /**
     * Handle FAQ query form submission from "Get Support" form.
     */
    public function submitFaqQuery(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'message'   => 'required|string',
        ]);

        \App\Models\FaqQuery::create([
            'full_name' => $request->input('full_name'),
            'email'     => $request->input('email'),
            'phone'     => $request->input('phone'),
            'message'   => $request->input('message'),
            'page_name' => $request->input('page_name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your query has been submitted. We will get back to you shortly.',
        ]);
    }

    /**
     * Get common stats/fact numbers for the shared stats section.
     */
    private function getCommonStats()
    {
        return FactNumberSectionCommonPage::active()->ordered()->get();
    }
}
