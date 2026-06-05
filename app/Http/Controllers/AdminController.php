<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\NetworkOffice;
use App\Models\ShipmentInvoice;

class AdminController extends Controller
{
    public function index()
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login first');
        }
        
        return view('admin.dashboard');
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to login with admin guard
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->has('remember'))) {
            return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        }

        // Login failed
        return redirect()->route('admin.login')->with('error', 'Invalid email or password');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }

    public function companies()
    {
        // Fetch ALL shipments across all customers with related data
        $shipments = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->leftJoin('admin_user', 'shipment_invoice.assigned_delivery_person', '=', 'admin_user.id')
            ->select(
                'shipment_invoice.id',
                'shipment_invoice.invoice_number',
                'shipment_invoice.invoice_date',
                'shipment_invoice.invoice_amount',
                'shipment_invoice.incoterms',
                'shipment_invoice.invoice_currency',
                'shipment_invoice.reference_number',
                'shipment_invoice.status',
                'shipment_invoice.delivery_type',
                'shipment_invoice.assigned_delivery_person',
                'shipment_invoice.created_at',
                'shipment_invoice.updated_at',
                'shipper_info.id as shipper_id',
                'shipper_info.company_name as shipper_company',
                'shipper_info.contact_person as shipper_contact',
                'shipper_info.city as shipper_city',
                'shipper_info.state as shipper_state',
                'shipper_info.awb_number',
                'customers.id as customer_id',
                'customers.first_name',
                'customers.last_name',
                'customers.email as customer_email',
                'customers.phone_number as customer_phone',
                'consignee_info.consignee_name',
                'consignee_info.city as consignee_city',
                'consignee_info.state as consignee_state',
                'admin_user.name as delivery_person_name'
            )
            ->orderBy('shipment_invoice.created_at', 'desc')
            ->get();

        // Fetch delivery persons where type = 'Delivery_person'
        $deliveryPersons = Admin::where('type', 'Delivery_person')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'mobile']);

        return view('admin.companies', compact('shipments', 'deliveryPersons'));
    }

    /**
     * Assign delivery type and/or delivery person to a shipment.
     */
    public function assignDelivery(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'delivery_type' => 'required|string|in:DDU,DDP,Self',
                'delivery_person_id' => 'nullable|integer|exists:admin_user,id',
            ]);

            $updateData = [
                'delivery_type' => $request->delivery_type,
            ];

            // If Self is selected, assign delivery person; otherwise set to null
            if ($request->delivery_type === 'Self') {
                if (!$request->delivery_person_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a delivery person for Self delivery type.'
                    ]);
                }
                $updateData['assigned_delivery_person'] = $request->delivery_person_id;
            } else {
                $updateData['assigned_delivery_person'] = null;
            }

            ShipmentInvoice::where('id', $request->shipment_id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Delivery assignment saved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function createShipment()
    {
        return view('admin.create-shipment');
    }

    public function changeAboutUs()
    {
        $aboutContent = \App\Models\AboutPageContent::all();
        return view('admin.change-about-us', ['aboutContent' => $aboutContent]);
    }

    public function updateAboutUs(Request $request)
    {
        $request->validate([
            'about_content' => 'required|string',
        ]);

        // Here you would typically save the content to a database or file
        // For now, we'll just redirect back with a success message
        return redirect()->route('admin.change-about-us')->with('success', 'About Us page updated successfully!');
    }

    public function updateAboutContent(Request $request, $id)
    {
        try {
            // Validate basic fields first
            $request->validate([
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'icon_svg' => 'nullable|string',
                'status' => 'nullable|boolean',
                // Extra data (page_*) fields
                'page_badge_text' => 'nullable|string|max:255',
                'page_target_number' => 'nullable|string|max:50',
                'page_suffix' => 'nullable|string|max:50',
                'page_button_text' => 'nullable|string|max:255',
                'page_tag' => 'nullable|string|max:255',
                'page_color_scheme' => 'nullable|string|max:50',
                'page_year' => 'nullable|string|max:10',
                'page_card_color_class' => 'nullable|string|max:50',
                'page_rating' => 'nullable|numeric|max:999999',
                'page_countries' => 'nullable|string|max:255',
                'page_pin_codes' => 'nullable|string|max:255',
            ]);

            $content = \App\Models\AboutPageContent::findOrFail($id);
            
            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'icon_svg' => $request->icon_svg,
                'status' => $request->status ? 1 : 0,
                // Extra data fields
                'page_badge_text' => $request->page_badge_text,
                'page_target_number' => $request->page_target_number,
                'page_suffix' => $request->page_suffix,
                'page_button_text' => $request->page_button_text,
                'page_tag' => $request->page_tag,
                'page_color_scheme' => $request->page_color_scheme,
                'page_year' => $request->page_year,
                'page_card_color_class' => $request->page_card_color_class,
                'page_rating' => $request->page_rating,
                'page_countries' => $request->page_countries,
                'page_pin_codes' => $request->page_pin_codes,
            ];

            // Handle image upload separately
            if ($request->hasFile('image_file')) {
                $image = $request->file('image_file');
                
                // Validate image
                $request->validate([
                    'image_file' => 'image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'website_images/' . $imageName;
                
                // Ensure directory exists
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move file
                $image->move($uploadPath, $imageName);
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = $request->image;
            }
            
            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteAboutContent($id)
    {
        $content = \App\Models\AboutPageContent::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully!'
        ]);
    }

    public function changeHome()
    {
        $homeContent = \App\Models\HomePageContent::orderBy('sort_order')->get();
        return view('admin.change-home', ['homeContent' => $homeContent]);
    }

    public function getHomeContent($id)
    {
        $content = \App\Models\HomePageContent::findOrFail($id);
        return response()->json($content);
    }

    public function updateHomeContent(Request $request, $id)
    {
        $content = \App\Models\HomePageContent::findOrFail($id);
        
        // Handle image deletion
        if ($request->has('delete_image') && $request->delete_image == 'true') {
            // Delete the actual image file if it exists
            $currentContent = $content->content;
            if (preg_match('/website_images\/(.+)/i', $currentContent, $matches)) {
                $imagePath = public_path('public/website_images/' . $matches[1]);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Clear content field
            $content->update([
                'content' => ''
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);
        }
        
        // Handle file upload
        if ($request->hasFile('image_upload')) {
            $request->validate([
                'image_upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'
            ]);
            
            $file = $request->file('image_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->move(public_path('website_images'), $filename);
            
            // Update content with new image path
            $content->update([
                'content' => 'public/website_images/' . $filename
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded and content updated successfully!'
            ]);
        } else {
            // Handle regular content update
            $request->validate([
                'section' => 'required|string|max:100',
                'field_name' => 'required|string|max:100',
                'content' => 'required|string',
                'sort_order' => 'required|integer|min:0',
            ]);
            
            $content->update([
                'section' => $request->section,
                'field_name' => $request->field_name,
                'content' => $request->content,
                'sort_order' => $request->sort_order,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully!'
            ]);
        }
    }
    
    public function updateMultipleHomeContent(Request $request)
    {
        $request->validate([
            'content.*' => 'required|string',
            'id.*' => 'required|integer|exists:home_page_contents,id'
        ]);

        $contents = $request->input('content');
        $ids = $request->input('id');

        foreach ($ids as $index => $id) {
            if (isset($contents[$index])) {
                \App\Models\HomePageContent::where('id', $id)->update([
                    'content' => $contents[$index]
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully!'
        ]);
    }

    public function getAboutContent()
    {
        $content = [
            'hero' => \App\Models\AboutPageContent::where('section_type', 'hero')->first(),
            'stats' => \App\Models\AboutPageContent::where('section_type', 'stat')->orderBy('display_order')->get(),
            'overview' => \App\Models\AboutPageContent::where('section_type', 'overview')->first(),
            'missionVisionIntro' => \App\Models\AboutPageContent::where('section_type', 'mission_vision_intro')->first(),
            'mission' => \App\Models\AboutPageContent::where('section_type', 'mission')->first(),
            'vision' => \App\Models\AboutPageContent::where('section_type', 'vision')->first(),
            'journeyIntro' => \App\Models\AboutPageContent::where('section_type', 'journey_intro')->first(),
            'milestones' => \App\Models\AboutPageContent::where('section_type', 'milestone')->orderBy('display_order')->get(),
            'testimonials' => \App\Models\AboutPageContent::where('section_type', 'testimonial')->orderBy('display_order')->get(),
            'faqHeader' => \App\Models\AboutPageContent::where('section_type', 'faq_header')->first(),
            'faqs' => \App\Models\AboutPageContent::where('section_type', 'faq')->orderBy('display_order')->get(),
            'partners' => \App\Models\AboutPageContent::where('section_type', 'partner')->orderBy('display_order')->get(),
            'newsletter' => \App\Models\AboutPageContent::where('section_type', 'newsletter_cta')->first(),
        ];

        return response()->json($content);
    }

    public function csb5Form()
    {
        return view('admin.csb5-form');
    }

    public function formKyc()
    {
        return view('admin.form-kyc');
    }
    
    public function updateHome()
    {
        $homeContent = HomePageContent::orderBy('sort_order')->get();
        
        return view('admin.change-home', compact('homeContent'));
    }

    public function updateServiceContent(Request $request, $id)
    {
        try {
            $content = \App\Models\ServicePage::findOrFail($id);
            
            $updateData = [
                'section' => $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            // Handle content data based on section
            $contentData = [];
            switch($request->section) {
                case 'services':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'icon_svg' => $request->input('content.icon_svg'),
                        'color_class' => $request->input('content.color_class'),
                        'link' => $request->input('content.link'),
                    ];
                    // Write to normalized columns on the ServicePage model
                    $updateData['icon_svg'] = $request->input('content.icon_svg');
                    $updateData['color_scheme'] = $request->input('content.color_class');
                    // title/description/link have no dedicated columns on service_page,
                    // so store them in extra_content JSON for the accessor to merge
                    $updateData['extra_content'] = json_encode([
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'link' => $request->input('content.link'),
                    ]);
                    break;
                    
                case 'testimonials':
                    $contentData = [
                        'name' => $request->input('content.name'),
                        'text' => $request->input('content.text'),
                        'avatar' => $request->input('content.avatar'),
                        'rating' => (int) $request->input('content.rating'),
                    ];
                    $updateData['name'] = $request->input('content.name');
                    $updateData['text_content'] = $request->input('content.text');
                    $updateData['avatar_url'] = $request->input('content.avatar');
                    $updateData['rating'] = (int) $request->input('content.rating');
                    break;
                    
                case 'faq':
                    $contentData = [
                        'question' => $request->input('content.question'),
                        'answer' => $request->input('content.answer'),
                    ];
                    $updateData['question'] = $request->input('content.question');
                    $updateData['answer'] = $request->input('content.answer');
                    break;
                    
                case 'stats':
                    $contentData = [
                        'value' => $request->input('content.value'),
                        'label' => $request->input('content.label'),
                    ];
                    $updateData['stat_value'] = $request->input('content.value');
                    $updateData['stat_label'] = $request->input('content.label');
                    break;
                    
                case 'partners':
                    $contentData = [
                        'name' => $request->input('content.name'),
                        'logo_url' => $request->input('content.logo_url'),
                        'alt' => $request->input('content.alt'),
                    ];
                    $updateData['name'] = $request->input('content.name');
                    $updateData['logo_url'] = $request->input('content.logo_url');
                    $updateData['alt_text'] = $request->input('content.alt');
                    break;
            }
            
            $updateData['content'] = $contentData;
            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Service content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function volumetricCalculator()
    {
        $volumetricCalculatorContent = \App\Models\VolumetricCalculatorPage::orderBy('sort_order')->get();
        return view('admin.change-volumetric-calculator-page', ['volumetricCalculatorContent' => $volumetricCalculatorContent]);
    }

    public function getVolumetricCalculatorContent($id)
    {
        try {
            $content = \App\Models\VolumetricCalculatorPage::findOrFail($id);
            return response()->json($content);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    public function updateVolumetricCalculatorContent(Request $request, $id)
    {
        try {
            $content = \App\Models\VolumetricCalculatorPage::findOrFail($id);

            // Always write to the data JSON column + normalized columns + data_extra,
            // because the frontend view reads from ALL THREE sources directly.
            $updateData = [
                'section' => $request->section,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            $contentData = [];
            switch ($request->section) {
                case 'hero':
                    $contentData = [
                        'badge_text' => $request->input('content.badge_text'),
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    $updateData['data_button_text'] = $request->input('content.button_text');
                    $updateData['data_extra'] = json_encode([
                        'badge_text' => $request->input('content.badge_text'),
                        'button_url' => $request->input('content.button_url'),
                    ]);
                    break;
                case 'features_header':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    break;
                case 'features':
                    $contentData = [
                        'icon_class' => $request->input('content.icon_class'),
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    $updateData['data_icon'] = $request->input('content.icon_class');
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    break;
                case 'track_cta':
                    $contentData = [
                        'live_badge' => $request->input('content.live_badge'),
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    $updateData['data_button_text'] = $request->input('content.button_text');
                    $updateData['data_extra'] = json_encode([
                        'live_badge' => $request->input('content.live_badge'),
                        'button_url' => $request->input('content.button_url'),
                    ]);
                    break;
                case 'testimonials_header':
                    $contentData = [
                        'badge_url' => $request->input('content.badge_url'),
                        'badge_image' => $request->input('content.badge_image'),
                        'badge_alt' => $request->input('content.badge_alt'),
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    $updateData['data_extra'] = json_encode([
                        'badge_url' => $request->input('content.badge_url'),
                        'badge_image' => $request->input('content.badge_image'),
                        'badge_alt' => $request->input('content.badge_alt'),
                    ]);
                    break;
                case 'testimonials':
                    $contentData = [
                        'stars' => $request->input('content.stars'),
                        'text' => $request->input('content.text'),
                        'name' => $request->input('content.name'),
                        'image' => $request->input('content.image'),
                    ];
                    $updateData['data_image'] = $request->input('content.image');
                    $updateData['data_extra'] = json_encode([
                        'stars' => $request->input('content.stars'),
                        'text' => $request->input('content.text'),
                        'name' => $request->input('content.name'),
                    ]);
                    break;
                case 'faq_sidebar':
                    $contentData = [
                        'icon_image' => $request->input('content.icon_image'),
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    $updateData['data_image'] = $request->input('content.icon_image');
                    $updateData['data_title'] = $request->input('content.title');
                    $updateData['data_description'] = $request->input('content.description');
                    $updateData['data_button_text'] = $request->input('content.button_text');
                    $updateData['data_extra'] = json_encode([
                        'icon_image' => $request->input('content.icon_image'),
                        'button_url' => $request->input('content.button_url'),
                    ]);
                    break;
                case 'faq':
                    $contentData = [
                        'question' => $request->input('content.question'),
                        'answer' => $request->input('content.answer'),
                    ];
                    $updateData['data_extra'] = json_encode([
                        'question' => $request->input('content.question'),
                        'answer' => $request->input('content.answer'),
                    ]);
                    break;
                case 'calculator':
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    if ($parsed === null && json_last_error() !== JSON_ERROR_NONE) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid JSON for calculator data: ' . json_last_error_msg(),
                        ]);
                    }
                    $contentData = $parsed;
                    $updateData['data_extra'] = $rawJson;
                    break;
                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $contentData = $parsed !== null ? $parsed : [];
                    $updateData['data_extra'] = $rawJson;
                    break;
            }

            $updateData['data'] = $contentData;
            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Volumetric calculator content updated successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteVolumetricCalculatorContent($id)
    {
        $content = \App\Models\VolumetricCalculatorPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Volumetric calculator content deleted successfully!'
        ]);
    }

    public function changeTermsAndConditions()
    {
        $termsContent = \App\Models\TermsAndConditionPage::ordered()->get();
        return view('admin.change-terms-and-conditions', ['termsContent' => $termsContent]);
    }

    public function storeTermsAndConditionsContent(Request $request)
    {
        try {
            $request->validate([
                'section_key' => 'required|string|max:100',
                'title' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
                'paragraphs' => 'nullable|string',
                'effective_date' => 'nullable|string|max:50',
                'footer_heading' => 'nullable|string|max:255',
                'footer_email' => 'nullable|email|max:255',
            ]);

            $termsContent = new \App\Models\TermsAndConditionPage();
            $termsContent->section_key = $request->section_key;
            $termsContent->title = $request->title;
            $termsContent->paragraphs = $request->paragraphs;
            $termsContent->sort_order = $request->sort_order ?? 0;

            // Handle page meta data
            if ($request->section_key === '_page_meta') {
                $termsContent->effective_date = $request->effective_date;
                $termsContent->footer_heading = $request->footer_heading;
                $termsContent->footer_email = $request->footer_email;
            }

            $termsContent->save();

            return response()->json([
                'success' => true,
                'message' => 'Terms and conditions content added successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateTermsAndConditionsContent(Request $request, $id)
    {
        try {
            $content = \App\Models\TermsAndConditionPage::findOrFail($id);
            
            $updateData = [
                'title' => $request->title,
                'paragraphs' => $request->paragraphs,
            'sort_order' => $request->sort_order,
            ];
    
            // Handle page meta data
            if ($content->section_key === '_page_meta') {
                $updateData['effective_date'] = $request->effective_date;
                $updateData['footer_heading'] = $request->footer_heading;
                $updateData['footer_email'] = $request->footer_email;
            }

            $content->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Terms and conditions content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTermsAndConditionsContent($id)
    {
        $content = \App\Models\TermsAndConditionPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terms and conditions content deleted successfully!'
        ]);
    }

    public function storePrivacyPolicyContent(Request $request)
    {
        try {
            $request->validate([
                'section_key' => 'required|string|max:100',
                'title' => 'nullable|string|max:255',
                'sort_order' => 'nullable|integer|min:0',
                'paragraphs' => 'nullable|string',
                'effective_date' => 'nullable|string|max:50',
                'footer_heading' => 'nullable|string|max:255',
                'footer_email' => 'nullable|email|max:255',
            ]);

            $privacyContent = new \App\Models\PrivacyPolicyPage();
            $privacyContent->section_key = $request->section_key;
            $privacyContent->title = $request->title;
            $privacyContent->paragraphs = $request->paragraphs;
            $privacyContent->sort_order = $request->sort_order ?? 0;

            // Handle page meta data
            if ($request->section_key === '_page_meta') {
                $privacyContent->effective_date = $request->effective_date;
                $privacyContent->footer_heading = $request->footer_heading;
                $privacyContent->footer_email = $request->footer_email;
            }

            $privacyContent->save();

            return response()->json([
                'success' => true,
                'message' => 'Privacy policy content added successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function changePrivacyPolicy()
    {
        $privacyContent = \App\Models\PrivacyPolicyPage::ordered()->get();
        return view('admin.change-privacy-policy', ['privacyContent' => $privacyContent]);
    }

    public function updatePrivacyPolicyContent(Request $request, $id)
    {
        try {
            $content = \App\Models\PrivacyPolicyPage::findOrFail($id);
            
            $updateData = [
                'title' => $request->title,
                'paragraphs' => $request->paragraphs,
                'sort_order' => $request->sort_order,
            ];

            // Handle page meta data
            if ($content->section_key === '_page_meta') {
                $updateData['effective_date'] = $request->effective_date;
                $updateData['footer_heading'] = $request->footer_heading;
                $updateData['footer_email'] = $request->footer_email;
            }

            $content->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Privacy policy content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePrivacyPolicyContent($id)
    {
        $content = \App\Models\PrivacyPolicyPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Privacy policy content deleted successfully!'
        ]);
    }

    public function changeRefundAndCancellationPolicy()
    {
        $refundContent = \App\Models\RefundAndCancellationPolicyPage::ordered()->get();
        return view('admin.change-refund-and-cancellation-policy', ['refundContent' => $refundContent]);
    }

    public function changeContactPage()
    {
        $contactContent = \App\Models\ContactUsPage::ordered()->get();
        return view('admin.change-contact-page', ['contactContent' => $contactContent]);
    }

    public function updateContactPageContent(Request $request, $id)
    {
        try {
            $content = \App\Models\ContactUsPage::findOrFail($id);
            
            $updateData = [
                'section_key' => $request->section_key,
                'title' => $request->title,
                'paragraphs' => $request->paragraphs,
                'sort_order' => $request->sort_order,
                'address' => $request->address,
                'map_embed_url' => $request->map_embed_url,
            ];

            // Handle phone numbers as newline-separated text (phone_numbers_text column)
            if ($request->has('phone_numbers')) {
                $phoneNumbers = $request->input('phone_numbers');
                
                if (is_string($phoneNumbers)) {
                    $decoded = json_decode($phoneNumbers, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $updateData['phone_numbers_text'] = implode("\n", array_filter($decoded));
                    } else {
                        $updateData['phone_numbers_text'] = $phoneNumbers;
                    }
                } elseif (is_array($phoneNumbers)) {
                    $updateData['phone_numbers_text'] = implode("\n", array_filter($phoneNumbers));
                } else {
                    $updateData['phone_numbers_text'] = null;
                }
            }

            // Handle email addresses as newline-separated text (email_addresses_text column)
            if ($request->has('email_addresses')) {
                $emailAddresses = $request->input('email_addresses');
                
                if (is_string($emailAddresses)) {
                    $decoded = json_decode($emailAddresses, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $updateData['email_addresses_text'] = implode("\n", array_filter($decoded));
                    } else {
                        $updateData['email_addresses_text'] = $emailAddresses;
                    }
                } elseif (is_array($emailAddresses)) {
                    $updateData['email_addresses_text'] = implode("\n", array_filter($emailAddresses));
                } else {
                    $updateData['email_addresses_text'] = null;
                }
            }

            // Handle list items as newline-separated text (list_items_text column)
            if ($request->has('list_items')) {
                $listItems = $request->input('list_items');
                
                if (is_string($listItems)) {
                    $decoded = json_decode($listItems, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $updateData['list_items_text'] = implode("\n", array_filter($decoded));
                    } else {
                        $updateData['list_items_text'] = $listItems;
                    }
                } elseif (is_array($listItems)) {
                    $updateData['list_items_text'] = implode("\n", array_filter($listItems));
                } else {
                    $updateData['list_items_text'] = null;
                }
            }

            // Handle social links as JSON-encoded text (social_links_text column)
            if ($request->has('social_links')) {
                $socialLinks = $request->input('social_links');
                
                if (is_string($socialLinks)) {
                    $decoded = json_decode($socialLinks, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $updateData['social_links_text'] = json_encode(array_filter($decoded));
                    } else {
                        $updateData['social_links_text'] = $socialLinks;
                    }
                } elseif (is_array($socialLinks)) {
                    $updateData['social_links_text'] = json_encode(array_filter($socialLinks));
                } else {
                    $updateData['social_links_text'] = null;
                }
            }

            $content->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Contact page content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteContactPageContent($id)
    {
        $content = \App\Models\ContactUsPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact page content deleted successfully!'
        ]);
    }

    public function changeWarehousingSolutions()
    {
        $warehousingContent = \App\Models\WarehousingSolutionsPage::ordered()->get();
        return view('admin.change-warehousing-solutions', ['warehousingContent' => $warehousingContent]);
    }

    public function storeWarehousingSolutionsContent(Request $request)
    {
        try {
            $newContent = new \App\Models\WarehousingSolutionsPage();
            
            $storeData = [
                'section' => $request->section === 'features_header' ? 'features' : $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            $contentData = [];
            switch($request->section) {
                case 'hero':
                    $listItems = $request->input('content.list_items');
                    if (is_string($listItems)) {
                        $listItems = array_map('trim', explode(',', $listItems));
                        $listItems = array_filter($listItems);
                    }
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'badge_text' => $request->input('content.badge_text'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                        'image' => $request->input('content.image'),
                        'list_items' => $listItems,
                    ];
                    // Also populate normalized columns
                    $storeData['paragraphs'] = $request->input('content.paragraphs');
                    $storeData['subtitle'] = $request->input('content.subtitle');
                    break;
                case 'stats':
                    $contentData = [
                        'stat_number' => $request->input('content.stat_number'),
                        'stat_label' => $request->input('content.stat_label'),
                        'suffix' => $request->input('content.suffix'),
                    ];
                    break;
                case 'overview':
                    $listItems = $request->input('content.list_items');
                    if (is_string($listItems)) {
                        $listItems = array_map('trim', explode(',', $listItems));
                        $listItems = array_filter($listItems);
                    }
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'image' => $request->input('content.image'),
                        'list_items' => $listItems,
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    // Also populate normalized columns
                    $storeData['paragraphs'] = $request->input('content.paragraphs');
                    $storeData['subtitle'] = $request->input('content.subtitle');
                    break;
                case 'features_header':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'description' => $request->input('content.description'),
                        'paragraphs' => $request->input('content.paragraphs'),
                    ];
                    $storeData['paragraphs'] = $request->input('content.paragraphs');
                    $storeData['subtitle'] = $request->input('content.subtitle');
                    break;
                case 'features':
                    $contentData = [
                        'subtitle' => $request->input('content.subtitle'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'icon_svg' => $request->input('content.icon_svg'),
                        'icon_class' => $request->input('content.icon_class'),
                    ];
                    // Also populate normalized columns
                    $storeData['paragraphs'] = $request->input('content.paragraphs');
                    $storeData['subtitle'] = $request->input('content.subtitle');
                    break;
                case 'faq':
                    $contentData = [
                        'question' => $request->input('content.question'),
                        'answer' => $request->input('content.answer'),
                    ];
                    break;
                case 'cta':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    // Also populate normalized columns
                    $storeData['subtitle'] = $request->input('content.subtitle');
                    break;
                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $contentData = $parsed !== null ? $parsed : [];
                    break;
            }

            $storeData['content'] = $contentData;

            // Handle extra_content as a JSON string
            if ($request->filled('extra_content')) {
                $extraContentRaw = $request->input('extra_content');
                // Validate that it's parseable JSON (store as-is; model accessor decodes it)
                if (is_string($extraContentRaw)) {
                    $decoded = json_decode($extraContentRaw, true);
                    if ($decoded !== null || $extraContentRaw === 'null') {
                        $storeData['extra_content'] = $extraContentRaw;
                    } else {
                        // Not valid JSON, store as-is anyway (will be ignored by accessor)
                        $storeData['extra_content'] = $extraContentRaw;
                    }
                }
            }

            $newContent->fill($storeData);
            $newContent->save();

            return response()->json([
                'success' => true,
                'message' => 'Warehousing solutions content stored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWarehousingSolutionsContent(Request $request, $id)
    {
        try {
            $content = \App\Models\WarehousingSolutionsPage::findOrFail($id);
            
            $updateData = [
                'section' => $request->section === 'features_header' ? 'features' : $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            // Handle content data based on section
            $contentData = [];
            switch($request->section) {
                case 'hero':
                    $listItems = $request->input('content.list_items');
                    if (is_string($listItems)) {
                        $listItems = array_map('trim', explode(',', $listItems));
                        $listItems = array_filter($listItems);
                    }
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'badge_text' => $request->input('content.badge_text'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                        'image' => $request->input('content.image'),
                        'list_items' => $listItems,
                    ];
                    // Also populate normalized columns
                    $updateData['paragraphs'] = $request->input('content.paragraphs');
                    $updateData['subtitle'] = $request->input('content.subtitle');
                    break;
                    
                case 'stats':
                    $contentData = [
                        'stat_number' => $request->input('content.stat_number'),
                        'stat_label' => $request->input('content.stat_label'),
                        'suffix' => $request->input('content.suffix'),
                    ];
                    break;
                    
                case 'overview':
                    $listItems = $request->input('content.list_items');
                    if (is_string($listItems)) {
                        $listItems = array_map('trim', explode(',', $listItems));
                        $listItems = array_filter($listItems);
                    }
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'image' => $request->input('content.image'),
                        'list_items' => $listItems,
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    // Also populate normalized columns
                    $updateData['paragraphs'] = $request->input('content.paragraphs');
                    $updateData['subtitle'] = $request->input('content.subtitle');
                    break;
                    
                case 'features_header':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'description' => $request->input('content.description'),
                        'paragraphs' => $request->input('content.paragraphs'),
                    ];
                    $updateData['paragraphs'] = $request->input('content.paragraphs');
                    $updateData['subtitle'] = $request->input('content.subtitle');
                    break;
                case 'features':
                    $contentData = [
                        'subtitle' => $request->input('content.subtitle'),
                        'paragraphs' => $request->input('content.paragraphs'),
                        'icon_svg' => $request->input('content.icon_svg'),
                        'icon_class' => $request->input('content.icon_class'),
                    ];
                    // Also populate normalized columns
                    $updateData['paragraphs'] = $request->input('content.paragraphs');
                    $updateData['subtitle'] = $request->input('content.subtitle');
                    break;
                    
                case 'faq':
                    $contentData = [
                        'question' => $request->input('content.question'),
                        'answer' => $request->input('content.answer'),
                    ];
                    break;
                    
                case 'cta':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'subtitle' => $request->input('content.subtitle'),
                        'button_text' => $request->input('content.button_text'),
                        'button_url' => $request->input('content.button_url'),
                    ];
                    // Also populate normalized columns
                    $updateData['subtitle'] = $request->input('content.subtitle');
                    break;
                    
                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $contentData = $parsed !== null ? $parsed : [];
                    break;
            }
            
            $updateData['content'] = $contentData;

            // Handle extra_content as a JSON string
            if ($request->filled('extra_content')) {
                $extraContentRaw = $request->input('extra_content');
                if (is_string($extraContentRaw)) {
                    $decoded = json_decode($extraContentRaw, true);
                    if ($decoded !== null || $extraContentRaw === 'null') {
                        $updateData['extra_content'] = $extraContentRaw;
                    } else {
                        $updateData['extra_content'] = $extraContentRaw;
                    }
                }
            }

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Warehousing solutions content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWarehousingSolutionsContent($id)
    {
        $content = \App\Models\WarehousingSolutionsPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Warehousing solutions content deleted successfully!'
        ]);
    }

    public function changeEcommerceLogisticsSolutions()
    {
        $ecommerceContent = \App\Models\EcommerceLogisticsSolutionsPage::ordered()->get();
        return view('admin.change-e-commerce-logistics-solutions', ['ecommerceContent' => $ecommerceContent]);
    }

    public function getEcommerceLogisticsSolutionsContent($id)
    {
        try {
            $content = \App\Models\EcommerceLogisticsSolutionsPage::findOrFail($id);
            return response()->json($content);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Content not found'], 404);
        }
    }

    public function storeEcommerceLogisticsSolutionsContent(Request $request)
    {
        try {
            $newContent = new \App\Models\EcommerceLogisticsSolutionsPage();
            
            $storeData = [
                'section' => $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            // Build data distributed across normalized columns, content JSON, and extra_content
            // based on where getContentAttribute() reads from
            $columnData = [];
            $contentData = [];
            $extraContentData = [];

            switch($request->section) {
                case 'hero':
                    $columnData['badge_text'] = $request->input('content.badge_text');
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    $badges = $request->input('content.badges');
                    if (is_string($badges)) {
                        $badgesLines = array_map('trim', explode("\n", $badges));
                        $badges = [];
                        foreach ($badgesLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 2) {
                                $badges[] = ['icon' => $parts[0], 'text' => $parts[1]];
                            }
                        }
                    }
                    $statPills = $request->input('content.stat_pills');
                    if (is_string($statPills)) {
                        $statPillsLines = array_map('trim', explode("\n", $statPills));
                        $statPills = [];
                        foreach ($statPillsLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 5) {
                                $statPills[] = ['icon' => $parts[0], 'value' => $parts[1], 'label' => $parts[2], 'color' => $parts[3], 'text_color' => $parts[4]];
                            }
                        }
                    }
                    $extraContentData = [
                        'button_primary_text' => $request->input('content.button_primary_text'),
                        'button_primary_icon' => $request->input('content.button_primary_icon'),
                        'button_primary_url' => $request->input('content.button_primary_url'),
                        'button_secondary_text' => $request->input('content.button_secondary_text'),
                        'button_secondary_icon' => $request->input('content.button_secondary_icon'),
                        'button_secondary_url' => $request->input('content.button_secondary_url'),
                        'badges' => $badges,
                        'stat_pills' => $statPills,
                    ];
                    break;

                case 'stats':
                    if ($request->item_key === 'stats_header') {
                        // Header record: title lives in extra_content (seeded pattern)
                        $extraContentData['title'] = $request->input('content.title');
                    } else {
                        // Individual stat: mapped to normalized columns
                        $columnData['stat_value'] = $request->input('content.value');
                        $columnData['stat_label'] = $request->input('content.label');
                        $columnData['stat_suffix'] = $request->input('content.suffix');
                    }
                    break;

                case 'overview':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    $columnData['button_text'] = $request->input('content.button_text');
                    $columnData['button_url'] = $request->input('content.button_url');
                    $checkList = $request->input('content.check_list');
                    if (is_array($checkList)) {
                        $columnData['check_list_text'] = implode("\n", $checkList);
                    } elseif (is_string($checkList) && !empty($checkList)) {
                        $items = array_map('trim', explode("\n", $checkList));
                        $items = array_filter($items);
                        $columnData['check_list_text'] = implode("\n", $items);
                    }
                    break;

                case 'features_header':
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;

                case 'features':
                    $columnData['icon_svg'] = $request->input('content.icon');
                    $columnData['color_scheme'] = $request->input('content.color_class');
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;

                case 'testimonials_header':
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'google_review_image' => $request->input('content.google_review_image'),
                    ];
                    break;

                case 'testimonials':
                    $columnData['name'] = $request->input('content.name');
                    $columnData['avatar_url'] = $request->input('content.avatar');
                    $columnData['rating'] = $request->input('content.rating');
                    $columnData['text_content'] = $request->input('content.text');
                    break;

                case 'faq_header':
                    $extraContentData = [
                        'badge' => $request->input('content.badge'),
                        'title' => $request->input('content.title'),
                        'sidebar_image' => $request->input('content.sidebar_image'),
                        'sidebar_title' => $request->input('content.sidebar_title'),
                        'sidebar_description' => $request->input('content.sidebar_description'),
                        'contact_box_title' => $request->input('content.contact_box_title'),
                        'contact_box_description' => $request->input('content.contact_box_description'),
                        'contact_button_text' => $request->input('content.contact_button_text'),
                    ];
                    break;

                case 'faq':
                    $columnData['question'] = $request->input('content.question');
                    $columnData['answer'] = $request->input('content.answer');
                    break;

                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $contentData = $parsed !== null ? $parsed : [];
                    break;
            }

            // Filter out null/empty string values (preserve empty arrays like [] for badges)
            $contentData = array_filter($contentData, function($v) { return $v !== null && $v !== ''; });
            $extraContentData = array_filter($extraContentData, function($v) { return $v !== null && $v !== ''; });
            $columnData = array_filter($columnData, function($v) { return $v !== null && $v !== ''; });

            // Merge: columns go directly, content JSON if any, extra_content if any
            $storeData = array_merge($storeData, $columnData);
            if (!empty($contentData)) {
                $storeData['content'] = $contentData;
            }
            // Always set extra_content so stale seeder data gets overwritten
            $storeData['extra_content'] = !empty($extraContentData) ? json_encode($extraContentData) : json_encode(new \stdClass());

            $newContent->fill($storeData);
            $newContent->save();

            return response()->json([
                'success' => true,
                'message' => 'E-commerce logistics solutions content stored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateEcommerceLogisticsSolutionsContent(Request $request, $id)
    {
        try {
            $content = \App\Models\EcommerceLogisticsSolutionsPage::findOrFail($id);
            
            $updateData = [
                'section' => $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            // Build data distributed across normalized columns, content JSON, and extra_content
            // based on where getContentAttribute() reads from
            $columnData = [];
            $contentData = [];
            $extraContentData = [];

            switch($request->section) {
                case 'hero':
                    $columnData['badge_text'] = $request->input('content.badge_text');
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    $badges = $request->input('content.badges');
                    if (is_string($badges)) {
                        $badgesLines = array_map('trim', explode("\n", $badges));
                        $badges = [];
                        foreach ($badgesLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 2) {
                                $badges[] = ['icon' => $parts[0], 'text' => $parts[1]];
                            }
                        }
                    }
                    $statPills = $request->input('content.stat_pills');
                    if (is_string($statPills)) {
                        $statPillsLines = array_map('trim', explode("\n", $statPills));
                        $statPills = [];
                        foreach ($statPillsLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 5) {
                                $statPills[] = ['icon' => $parts[0], 'value' => $parts[1], 'label' => $parts[2], 'color' => $parts[3], 'text_color' => $parts[4]];
                            }
                        }
                    }
                    $extraContentData = [
                        'button_primary_text' => $request->input('content.button_primary_text'),
                        'button_primary_icon' => $request->input('content.button_primary_icon'),
                        'button_primary_url' => $request->input('content.button_primary_url'),
                        'button_secondary_text' => $request->input('content.button_secondary_text'),
                        'button_secondary_icon' => $request->input('content.button_secondary_icon'),
                        'button_secondary_url' => $request->input('content.button_secondary_url'),
                        'badges' => $badges,
                        'stat_pills' => $statPills,
                    ];
                    break;

                case 'stats':
                    if ($request->item_key === 'stats_header') {
                        $extraContentData['title'] = $request->input('content.title');
                    } else {
                        $columnData['stat_value'] = $request->input('content.value');
                        $columnData['stat_label'] = $request->input('content.label');
                        $columnData['stat_suffix'] = $request->input('content.suffix');
                    }
                    break;

                case 'overview':
                    $contentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    $columnData['button_text'] = $request->input('content.button_text');
                    $columnData['button_url'] = $request->input('content.button_url');
                    $checkList = $request->input('content.check_list');
                    if (is_array($checkList)) {
                        $columnData['check_list_text'] = implode("\n", $checkList);
                    } elseif (is_string($checkList) && !empty($checkList)) {
                        $items = array_map('trim', explode("\n", $checkList));
                        $items = array_filter($items);
                        $columnData['check_list_text'] = implode("\n", $items);
                    }
                    break;

                case 'features_header':
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;

                case 'features':
                    $columnData['icon_svg'] = $request->input('content.icon');
                    $columnData['color_scheme'] = $request->input('content.color_class');
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;

                case 'testimonials_header':
                    $extraContentData = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'google_review_image' => $request->input('content.google_review_image'),
                    ];
                    break;

                case 'testimonials':
                    $columnData['name'] = $request->input('content.name');
                    $columnData['avatar_url'] = $request->input('content.avatar');
                    $columnData['rating'] = $request->input('content.rating');
                    $columnData['text_content'] = $request->input('content.text');
                    break;

                case 'faq_header':
                    $extraContentData = [
                        'badge' => $request->input('content.badge'),
                        'title' => $request->input('content.title'),
                        'sidebar_image' => $request->input('content.sidebar_image'),
                        'sidebar_title' => $request->input('content.sidebar_title'),
                        'sidebar_description' => $request->input('content.sidebar_description'),
                        'contact_box_title' => $request->input('content.contact_box_title'),
                        'contact_box_description' => $request->input('content.contact_box_description'),
                        'contact_button_text' => $request->input('content.contact_button_text'),
                    ];
                    break;

                case 'faq':
                    $columnData['question'] = $request->input('content.question');
                    $columnData['answer'] = $request->input('content.answer');
                    break;

                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $contentData = $parsed !== null ? $parsed : [];
                    break;
            }

            // Filter out null/empty string values (preserve empty arrays like [] for badges)
            $contentData = array_filter($contentData, function($v) { return $v !== null && $v !== ''; });
            $extraContentData = array_filter($extraContentData, function($v) { return $v !== null && $v !== ''; });
            $columnData = array_filter($columnData, function($v) { return $v !== null && $v !== ''; });

            // Merge: columns go directly, content JSON if any, extra_content if any
            $updateData = array_merge($updateData, $columnData);
            if (!empty($contentData)) {
                $updateData['content'] = $contentData;
            }
            // Always set extra_content so stale seeder data gets overwritten
            $updateData['extra_content'] = !empty($extraContentData) ? json_encode($extraContentData) : json_encode(new \stdClass());

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'E-commerce logistics solutions content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteEcommerceLogisticsSolutionsContent($id)
    {
        $content = \App\Models\EcommerceLogisticsSolutionsPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'E-commerce logistics solutions content deleted successfully!'
        ]);
    }

    public function updateRefundAndCancellationPolicyContent(Request $request, $id)
    {
        try {
            $content = \App\Models\RefundAndCancellationPolicyPage::findOrFail($id);
            
            $updateData = [
                'title' => $request->title,
                'paragraphs' => $request->paragraphs,
                'sort_order' => $request->sort_order,
            ];

            // Handle page meta data
            if ($content->section_key === '_page_meta') {
                $updateData['effective_date'] = $request->effective_date;
                $updateData['footer_heading'] = $request->footer_heading;
                $updateData['footer_email'] = $request->footer_email;
            }

            $content->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Refund and cancellation policy content updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteRefundAndCancellationPolicyContent($id)
    {
        $content = \App\Models\RefundAndCancellationPolicyPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Refund and cancellation policy content deleted successfully!'
        ]);
    }

    // Network Page Management Methods
    public function changeNetwork()
    {
        $indiaOffices = NetworkOffice::india()->ordered()->get();
        $overseasOffices = NetworkOffice::overseas()->ordered()->get();
        $faqs = \App\Models\Faq::byPage('network')->ordered()->get();
        
        return view('admin.change-network', compact('indiaOffices', 'overseasOffices', 'faqs'));
    }

    public function storeNetworkOffice(Request $request)
    {
        try {
            $office = NetworkOffice::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Network office added successfully!',
                'office' => $office
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateNetworkOffice(Request $request, $id)
    {
        try {
            $office = NetworkOffice::findOrFail($id);
            $office->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Network office updated successfully!',
                'office' => $office
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteNetworkOffice($id)
    {
        try {
            $office = NetworkOffice::findOrFail($id);
            $office->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Network office deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // FAQ Management Methods
    public function faq()
    {
        $faqs = \App\Models\Faq::orderBy('page')->orderBy('sort_order')->orderBy('id')->get();
        $faqsByPage = $faqs->groupBy('page');

        $pageNames = [
            'home' => 'Home',
            'network' => 'Network',
            'about' => 'About Us',
            'service' => 'Service',
            'partnership' => 'Partnership',
            'warehousing' => 'Warehousing Solutions',
            'ecommerce' => 'E-Commerce Logistics Solutions',
            'express-air' => 'Express Air Freight Solutions',
            'track-order' => 'Track Order',
            'e-books' => 'E-Books',
            'volumetric-calculator' => 'Volumetric Calculator',
            'barcode-generator' => 'Barcode Generator',
            'shipping-rate-calculator' => 'Shipping Rate Calculator',
            'hsn-finder' => 'HSN Finder',
        ];

        return view('admin.faq', compact('faqs', 'faqsByPage', 'pageNames'));
    }

    public function storeFaq(Request $request)
    {
        try {
            $faq = \App\Models\Faq::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'FAQ added successfully!',
                'faq' => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateFaq(Request $request, $id)
    {
        try {
            $faq = \App\Models\Faq::findOrFail($id);
            $faq->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'FAQ updated successfully!',
                'faq' => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteFaq($id)
    {
        try {
            $faq = \App\Models\Faq::findOrFail($id);
            $faq->delete();
            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Blog Management Methods
    public function changeBlog()
    {
        $blogs = \App\Models\Blog::with('category')->orderBy('created_at', 'desc')->get();
        $categories = \App\Models\BlogCategory::active()->get();
        return view('admin.change-blog', compact('blogs', 'categories'));
    }

    public function createBlog()
    {
        $blog = new \App\Models\Blog();
        $categories = \App\Models\BlogCategory::active()->get();
        return view('admin.edit-blog', compact('blog', 'categories'));
    }

    public function editBlog($id)
    {
        try {
            $blog = \App\Models\Blog::with('category')->findOrFail($id);
            $categories = \App\Models\BlogCategory::active()->get();
            return view('admin.edit-blog', compact('blog', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-blog')
                ->with('error', 'Blog post not found.');
        }
    }

    public function getBlog($id)
    {
        try {
            $blog = \App\Models\Blog::with('category')->findOrFail($id);
            return response()->json([
                'success' => true,
                'blog' => $blog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeBlog(Request $request)
    {
        try {
            $request->validate([
                'blog_title' => 'required|string|max:255',
                'url_title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:blogs,slug',
                'category_id' => 'nullable|exists:blog_categories,id',
                'sub_heading' => 'nullable|string|max:255',
                'sub_content' => 'nullable|string',
                'seo_meta_title' => 'nullable|string|max:255',
                'image_alt' => 'nullable|string|max:255',
                'social_title' => 'nullable|string|max:255',
                'country_name' => 'nullable|string|max:100',
                'state_name' => 'nullable|string|max:100',
                'city_name' => 'nullable|string|max:100',
                'blog_description' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keyword' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_url' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image_url' => 'nullable|string|max:255',
                'twitter_card' => 'nullable|string|max:100',
                'master_image_alt_text' => 'nullable|string|max:255',
                'is_trending' => 'nullable|in:Yes,No',
                'status' => 'nullable|in:Active,Inactive',
                'author_name' => 'nullable|string|max:255',
                'author_description' => 'nullable|string',
                'feed' => 'nullable|string',
            ]);

            $blog = new \App\Models\Blog();
            $blog->fill($request->except(['master_image', 'author_image']));

            // Handle master image file upload
            if ($request->hasFile('master_image')) {
                $request->validate([
                    'master_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('master_image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $blog->master_image = $imagePath;
            }

            // Handle author image file upload
            if ($request->hasFile('author_image')) {
                $request->validate([
                    'author_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $avatar = $request->file('author_image');
                $avatarName = time() . '_' . str_replace(' ', '_', $avatar->getClientOriginalName());
                $avatarPath = 'public/website_images/' . $avatarName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $avatar->move($uploadPath, $avatarName);
                $blog->author_image = $avatarPath;
            }

            $blog->blog_description = $request->blog_description;
            $blog->status = $request->status ?? 'Active';
            $blog->is_trending = $request->is_trending ?? 'No';
            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully!',
                'blog_id' => $blog->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateBlog(Request $request, $id)
    {
        try {
            $request->validate([
                'blog_title' => 'required|string|max:255',
                'url_title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:blogs,slug,' . $id,
                'category_id' => 'nullable|exists:blog_categories,id',
                'sub_heading' => 'nullable|string|max:255',
                'sub_content' => 'nullable|string',
                'seo_meta_title' => 'nullable|string|max:255',
                'image_alt' => 'nullable|string|max:255',
                'social_title' => 'nullable|string|max:255',
                'country_name' => 'nullable|string|max:100',
                'state_name' => 'nullable|string|max:100',
                'city_name' => 'nullable|string|max:100',
                'blog_description' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keyword' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_url' => 'nullable|string|max:255',
                'og_description' => 'nullable|string',
                'og_image_url' => 'nullable|string|max:255',
                'twitter_card' => 'nullable|string|max:100',
                'master_image_alt_text' => 'nullable|string|max:255',
                'is_trending' => 'nullable|in:Yes,No',
                'status' => 'nullable|in:Active,Inactive',
                'author_name' => 'nullable|string|max:255',
                'author_description' => 'nullable|string',
                'feed' => 'nullable|string',
            ]);

            $blog = \App\Models\Blog::findOrFail($id);
            $blog->fill($request->except(['master_image', 'author_image']));

            // Handle master image file upload
            if ($request->hasFile('master_image')) {
                $request->validate([
                    'master_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('master_image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $blog->master_image = $imagePath;
            }

            // Handle author image file upload
            if ($request->hasFile('author_image')) {
                $request->validate([
                    'author_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $avatar = $request->file('author_image');
                $avatarName = time() . '_' . str_replace(' ', '_', $avatar->getClientOriginalName());
                $avatarPath = 'public/website_images/' . $avatarName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $avatar->move($uploadPath, $avatarName);
                $blog->author_image = $avatarPath;
            }

            $blog->blog_description = $request->blog_description;
            $blog->status = $request->status ?? 'Active';
            $blog->is_trending = $request->is_trending ?? 'No';
            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBlog($id)
    {
        try {
            $blog = \App\Models\Blog::findOrFail($id);
            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== E-Book Management ==========

    public function changeEbook()
    {
        $ebooks = \App\Models\Ebook::ordered()->get();
        return view('admin.change-ebook', compact('ebooks'));
    }

    public function createEbook()
    {
        $ebook = new \App\Models\Ebook();
        return view('admin.edit-ebook', compact('ebook'));
    }

    public function editEbook($id)
    {
        try {
            $ebook = \App\Models\Ebook::findOrFail($id);
            return view('admin.edit-ebook', compact('ebook'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-ebook')
                ->with('error', 'E-book not found.');
        }
    }

    public function getEbook($id)
    {
        try {
            $ebook = \App\Models\Ebook::findOrFail($id);
            return response()->json([
                'success' => true,
                'ebook' => $ebook
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'E-book not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeEbook(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'required|file|mimes:pdf|max:20480',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $ebook = new \App\Models\Ebook();
            $ebook->fill($request->except(['image', 'link']));

            // Explicitly set as an e-book item (not page content)
            $ebook->section = null;
            $ebook->item_key = null;
            $ebook->content = null;

            // Handle PDF file upload
            if ($request->hasFile('link')) {
                $pdf = $request->file('link');
                $pdfName = time() . '_' . str_replace(' ', '_', $pdf->getClientOriginalName());
                $pdfPath = 'ebook_pdf/' . $pdfName;
                $uploadPath = public_path('ebook_pdf');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $pdf->move($uploadPath, $pdfName);
                $ebook->link = $pdfPath;
            }

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $ebook->image = $imagePath;
            }

            $ebook->status = $request->status ?? 'Active';
            $ebook->sort_order = $request->sort_order ?? 0;
            $ebook->save();

            return response()->json([
                'success' => true,
                'message' => 'E-book created successfully!',
                'ebook_id' => $ebook->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateEbook(Request $request, $id)
    {
        try {
            $ebook = \App\Models\Ebook::findOrFail($id);

            if ($ebook->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $ebook->content = $request->json_fields ?? [];
                $ebook->status = $request->status ?? 'Active';
                $ebook->sort_order = $request->sort_order ?? 0;
                $ebook->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── E-book item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|file|mimes:pdf|max:20480',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            // Fill only non-file fields
            $fillable = $request->except(['image', 'link']);
            // Don't overwrite link if no new file uploaded
            unset($fillable['link']);
            $ebook->fill($fillable);

            // Handle PDF file upload
            if ($request->hasFile('link')) {
                $pdf = $request->file('link');
                $pdfName = time() . '_' . str_replace(' ', '_', $pdf->getClientOriginalName());
                $pdfPath = 'ebook_pdf/' . $pdfName;
                $uploadPath = public_path('ebook_pdf');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $pdf->move($uploadPath, $pdfName);
                $ebook->link = $pdfPath;
            }

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $ebook->image = $imagePath;
            }

            $ebook->status = $request->status ?? 'Active';
            $ebook->sort_order = $request->sort_order ?? 0;
            $ebook->save();

            return response()->json([
                'success' => true,
                'message' => 'E-book updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteEbook($id)
    {
        try {
            $ebook = \App\Models\Ebook::findOrFail($id);
            $ebook->delete();

            return response()->json([
                'success' => true,
                'message' => 'E-book deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Track Order Page Management ==========

    public function changeTrackOrder()
    {
        $trackOrders = \App\Models\TrackOrderPage::ordered()->get();
        return view('admin.change-track-order', compact('trackOrders'));
    }

    public function createTrackOrder()
    {
        $trackOrder = new \App\Models\TrackOrderPage();
        return view('admin.edit-track-order', compact('trackOrder'));
    }

    public function editTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            return view('admin.edit-track-order', compact('trackOrder'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-track-order')
                ->with('error', 'Track Order content not found.');
        }
    }

    public function getTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'trackOrder' => $trackOrder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Track Order content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeTrackOrder(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $trackOrder = new \App\Models\TrackOrderPage();
            $trackOrder->fill($request->except(['image']));

            // Explicitly set as a track order item (not page content)
            $trackOrder->section = null;
            $trackOrder->item_key = null;
            $trackOrder->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $trackOrder->image = $imagePath;
            }

            $trackOrder->status = $request->status ?? 'Active';
            $trackOrder->sort_order = $request->sort_order ?? 0;
            $trackOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content created successfully!',
                'track_order_id' => $trackOrder->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateTrackOrder(Request $request, $id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);

            if ($trackOrder->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $trackOrder->content = $request->json_fields ?? [];
                $trackOrder->status = $request->status ?? 'Active';
                $trackOrder->sort_order = $request->sort_order ?? 0;
                $trackOrder->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── Track Order item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $trackOrder->fill($request->except(['image']));

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $trackOrder->image = $imagePath;
            }

            $trackOrder->status = $request->status ?? 'Active';
            $trackOrder->sort_order = $request->sort_order ?? 0;
            $trackOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTrackOrder($id)
    {
        try {
            $trackOrder = \App\Models\TrackOrderPage::findOrFail($id);
            $trackOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Track Order content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Webinar Page Management ==========

    public function changeWebinar()
    {
        $webinars = \App\Models\WebinarPage::ordered()->get();
        return view('admin.change-webinar', compact('webinars'));
    }

    public function createWebinar()
    {
        $webinar = new \App\Models\WebinarPage();
        return view('admin.edit-webinar', compact('webinar'));
    }

    public function editWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            return view('admin.edit-webinar', compact('webinar'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-webinar')
                ->with('error', 'Webinar content not found.');
        }
    }

    public function getWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'webinar' => $webinar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webinar content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeWebinar(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $webinar = new \App\Models\WebinarPage();
            $webinar->fill($request->except(['image']));

            // Explicitly set as a webinar item (not page content)
            $webinar->section = null;
            $webinar->item_key = null;
            $webinar->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $webinar->image = $imagePath;
            }

            $webinar->status = $request->status ?? 'Active';
            $webinar->sort_order = $request->sort_order ?? 0;
            $webinar->save();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content created successfully!',
                'webinar_id' => $webinar->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWebinar(Request $request, $id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);

            if ($webinar->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $webinar->content = $request->json_fields ?? [];
                $webinar->status = $request->status ?? 'Active';
                $webinar->sort_order = $request->sort_order ?? 0;
                $webinar->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── Webinar item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'json_fields' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $webinar->fill($request->except(['image']));

            // Save content JSON fields (category_tag, read_time, author_name, etc.)
            if ($request->has('json_fields')) {
                $existingContent = $webinar->content ?? [];
                $webinar->content = array_merge($existingContent, $request->json_fields);
            }

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $webinar->image = $imagePath;
            }

            $webinar->status = $request->status ?? 'Active';
            $webinar->sort_order = $request->sort_order ?? 0;
            $webinar->save();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWebinar($id)
    {
        try {
            $webinar = \App\Models\WebinarPage::findOrFail($id);
            $webinar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Webinar content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Currency Calculator Page Management ==========

    public function changeCurrencyCalculator()
    {
        $currencyCalculators = \App\Models\CurrencyCalculatorPage::ordered()->get();
        return view('admin.change-currency-calculator', compact('currencyCalculators'));
    }

    public function createCurrencyCalculator()
    {
        $currencyCalculator = new \App\Models\CurrencyCalculatorPage();
        return view('admin.edit-currency-calculator', compact('currencyCalculator'));
    }

    public function editCurrencyCalculator($id)
    {
        try {
            $currencyCalculator = \App\Models\CurrencyCalculatorPage::findOrFail($id);
            return view('admin.edit-currency-calculator', compact('currencyCalculator'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-currency-calculator')
                ->with('error', 'Currency calculator content not found.');
        }
    }

    public function getCurrencyCalculator($id)
    {
        try {
            $currencyCalculator = \App\Models\CurrencyCalculatorPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'currencyCalculator' => $currencyCalculator
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Currency calculator content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeCurrencyCalculator(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $currencyCalculator = new \App\Models\CurrencyCalculatorPage();
            $currencyCalculator->fill($request->except(['image']));

            // Explicitly set as a currency calculator item (not page content)
            $currencyCalculator->section = null;
            $currencyCalculator->item_key = null;
            $currencyCalculator->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $currencyCalculator->image = $imagePath;
            }

            $currencyCalculator->status = $request->status ?? 'Active';
            $currencyCalculator->sort_order = $request->sort_order ?? 0;
            $currencyCalculator->save();

            return response()->json([
                'success' => true,
                'message' => 'Currency calculator content created successfully!',
                'currency_calculator_id' => $currencyCalculator->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateCurrencyCalculator(Request $request, $id)
    {
        try {
            $currencyCalculator = \App\Models\CurrencyCalculatorPage::findOrFail($id);

            if ($currencyCalculator->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'section' => 'nullable|string|max:100',
                    'item_key' => 'nullable|string|max:100',
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'link' => 'nullable|string|max:500',
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $currencyCalculator->section = $request->section;
                $currencyCalculator->item_key = $request->item_key;
                $currencyCalculator->title = $request->title;
                $currencyCalculator->description = $request->description;
                $currencyCalculator->link = $request->link;
                $currencyCalculator->content = $request->json_fields ?? [];
                $currencyCalculator->status = $request->status ?? 'Active';
                $currencyCalculator->sort_order = $request->sort_order ?? 0;
                $currencyCalculator->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── Currency calculator item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $currencyCalculator->fill($request->except(['image']));

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $currencyCalculator->image = $imagePath;
            }

            $currencyCalculator->status = $request->status ?? 'Active';
            $currencyCalculator->sort_order = $request->sort_order ?? 0;
            $currencyCalculator->save();

            return response()->json([
                'success' => true,
                'message' => 'Currency calculator content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteCurrencyCalculator($id)
    {
        try {
            $currencyCalculator = \App\Models\CurrencyCalculatorPage::findOrFail($id);
            $currencyCalculator->delete();

            return response()->json([
                'success' => true,
                'message' => 'Currency calculator content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== World Weather Page Management ==========

    public function changeWorldWeather()
    {
        $worldWeathers = \App\Models\WorldWeatherPage::ordered()->get();
        return view('admin.change-world-weather', compact('worldWeathers'));
    }

    public function createWorldWeather()
    {
        $worldWeather = new \App\Models\WorldWeatherPage();
        return view('admin.edit-world-weather', compact('worldWeather'));
    }

    public function editWorldWeather($id)
    {
        try {
            $worldWeather = \App\Models\WorldWeatherPage::findOrFail($id);
            return view('admin.edit-world-weather', compact('worldWeather'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-world-weather')
                ->with('error', 'World weather content not found.');
        }
    }

    public function getWorldWeather($id)
    {
        try {
            $worldWeather = \App\Models\WorldWeatherPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'worldWeather' => $worldWeather
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'World weather content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeWorldWeather(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $worldWeather = new \App\Models\WorldWeatherPage();
            $worldWeather->fill($request->except(['image']));

            // Explicitly set as a world weather item (not page content)
            $worldWeather->section = null;
            $worldWeather->item_key = null;
            $worldWeather->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $worldWeather->image = $imagePath;
            }

            $worldWeather->status = $request->status ?? 'Active';
            $worldWeather->sort_order = $request->sort_order ?? 0;
            $worldWeather->save();

            return response()->json([
                'success' => true,
                'message' => 'World weather content created successfully!',
                'world_weather_id' => $worldWeather->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWorldWeather(Request $request, $id)
    {
        try {
            $worldWeather = \App\Models\WorldWeatherPage::findOrFail($id);

            if ($worldWeather->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'section' => 'nullable|string|max:100',
                    'item_key' => 'nullable|string|max:100',
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'link' => 'nullable|string|max:500',
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $worldWeather->section = $request->section;
                $worldWeather->item_key = $request->item_key;
                $worldWeather->title = $request->title;
                $worldWeather->description = $request->description;
                $worldWeather->link = $request->link;
                $worldWeather->content = $request->json_fields ?? [];
                $worldWeather->status = $request->status ?? 'Active';
                $worldWeather->sort_order = $request->sort_order ?? 0;
                $worldWeather->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── World weather item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $worldWeather->fill($request->except(['image']));

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $worldWeather->image = $imagePath;
            }

            $worldWeather->status = $request->status ?? 'Active';
            $worldWeather->sort_order = $request->sort_order ?? 0;
            $worldWeather->save();

            return response()->json([
                'success' => true,
                'message' => 'World weather content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWorldWeather($id)
    {
        try {
            $worldWeather = \App\Models\WorldWeatherPage::findOrFail($id);
            $worldWeather->delete();

            return response()->json([
                'success' => true,
                'message' => 'World weather content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== World Time Page Management ==========

    public function changeWorldTime()
    {
        $worldTimes = \App\Models\WorldTimePage::ordered()->get();
        return view('admin.change-world-time', compact('worldTimes'));
    }

    public function createWorldTime()
    {
        $worldTime = new \App\Models\WorldTimePage();
        return view('admin.edit-world-time', compact('worldTime'));
    }

    public function editWorldTime($id)
    {
        try {
            $worldTime = \App\Models\WorldTimePage::findOrFail($id);
            return view('admin.edit-world-time', compact('worldTime'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-world-time')
                ->with('error', 'World time content not found.');
        }
    }

    public function getWorldTime($id)
    {
        try {
            $worldTime = \App\Models\WorldTimePage::findOrFail($id);
            return response()->json([
                'success' => true,
                'worldTime' => $worldTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'World time content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeWorldTime(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $worldTime = new \App\Models\WorldTimePage();
            $worldTime->fill($request->except(['image']));

            // Explicitly set as a world time item (not page content)
            $worldTime->section = null;
            $worldTime->item_key = null;
            $worldTime->content = null;

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $worldTime->image = $imagePath;
            }

            $worldTime->status = $request->status ?? 'Active';
            $worldTime->sort_order = $request->sort_order ?? 0;
            $worldTime->save();

            return response()->json([
                'success' => true,
                'message' => 'World time content created successfully!',
                'world_time_id' => $worldTime->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWorldTime(Request $request, $id)
    {
        try {
            $worldTime = \App\Models\WorldTimePage::findOrFail($id);

            if ($worldTime->section) {
                // ── Page content row: save individual JSON fields ──
                $request->validate([
                    'section' => 'nullable|string|max:100',
                    'item_key' => 'nullable|string|max:100',
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'link' => 'nullable|string|max:500',
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $worldTime->section = $request->section;
                $worldTime->item_key = $request->item_key;
                $worldTime->title = $request->title;
                $worldTime->description = $request->description;
                $worldTime->link = $request->link;
                $worldTime->content = $request->json_fields ?? [];
                $worldTime->status = $request->status ?? 'Active';
                $worldTime->sort_order = $request->sort_order ?? 0;
                $worldTime->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            // ── World time item row ──
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $worldTime->fill($request->except(['image']));

            // Handle image file upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $worldTime->image = $imagePath;
            }

            $worldTime->status = $request->status ?? 'Active';
            $worldTime->sort_order = $request->sort_order ?? 0;
            $worldTime->save();

            return response()->json([
                'success' => true,
                'message' => 'World time content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWorldTime($id)
    {
        try {
            $worldTime = \App\Models\WorldTimePage::findOrFail($id);
            $worldTime->delete();

            return response()->json([
                'success' => true,
                'message' => 'World time content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Express Air Freight Solutions Page Management ==========

    public function changeExpressAirFreightSolutions()
    {
        $expressAirContent = \App\Models\ExpressAirFreightSolutionsPage::ordered()->get();
        return view('admin.change-express-air-freight-solutions', ['expressAirContent' => $expressAirContent]);
    }

    public function storeExpressAirFreightSolutionsContent(Request $request)
    {
        try {
            $newContent = new \App\Models\ExpressAirFreightSolutionsPage();

            $storeData = [
                'section' => $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            $extraContent = [];
            switch($request->section) {
                case 'hero':
                    $badges = $request->input('content.badges');
                    if (is_string($badges)) {
                        $badgesLines = array_map('trim', explode("\n", $badges));
                        $badges = [];
                        foreach ($badgesLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 2) {
                                $badges[] = ['icon' => $parts[0], 'text' => $parts[1]];
                            }
                        }
                    }
                    $statPills = $request->input('content.stat_pills');
                    if (is_string($statPills)) {
                        $statPillsLines = array_map('trim', explode("\n", $statPills));
                        $statPills = [];
                        foreach ($statPillsLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 5) {
                                $statPills[] = ['icon' => $parts[0], 'value' => $parts[1], 'label' => $parts[2], 'color' => $parts[3], 'text_color' => $parts[4]];
                            }
                        }
                    }
                    $storeData['badge_text'] = $request->input('content.badge_text');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'button_primary_text' => $request->input('content.button_primary_text'),
                        'button_primary_icon' => $request->input('content.button_primary_icon'),
                        'button_primary_url' => $request->input('content.button_primary_url'),
                        'button_secondary_text' => $request->input('content.button_secondary_text'),
                        'button_secondary_icon' => $request->input('content.button_secondary_icon'),
                        'button_secondary_url' => $request->input('content.button_secondary_url'),
                        'image' => $request->input('content.image'),
                        'badges' => $badges,
                        'stat_pills' => $statPills,
                    ];
                    break;
                case 'stats':
                    $storeData['stat_value'] = $request->input('content.value');
                    $storeData['stat_label'] = $request->input('content.label');
                    $storeData['stat_suffix'] = $request->input('content.suffix');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                    ];
                    break;
                case 'overview':
                    $checkListInput = $request->input('content.check_list');
                    if (is_string($checkListInput) && trim($checkListInput) !== '') {
                        $checkListItems = array_map('trim', explode("\n", $checkListInput));
                        $checkListItems = array_filter($checkListItems, function ($v) { return $v !== ''; });
                        $storeData['check_list_text'] = implode("\n", $checkListItems);
                    }
                    $storeData['button_text'] = $request->input('content.button_text');
                    $storeData['button_url'] = $request->input('content.button_url');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    break;
                case 'features_header':
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;
                case 'features':
                    $storeData['icon_class'] = $request->input('content.icon');
                    $storeData['color_scheme'] = $request->input('content.color_class');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;
                case 'testimonials_header':
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'google_review_image' => $request->input('content.google_review_image'),
                    ];
                    break;
                case 'testimonials':
                    $storeData['name'] = $request->input('content.name');
                    $storeData['avatar_url'] = $request->input('content.avatar');
                    $storeData['rating'] = $request->input('content.rating');
                    $storeData['text_content'] = $request->input('content.text');
                    break;
                case 'faq_header':
                    $storeData['badge_text'] = $request->input('content.badge');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'sidebar_image' => $request->input('content.sidebar_image'),
                        'sidebar_title' => $request->input('content.sidebar_title'),
                        'sidebar_description' => $request->input('content.sidebar_description'),
                        'contact_box_title' => $request->input('content.contact_box_title'),
                        'contact_box_description' => $request->input('content.contact_box_description'),
                        'contact_button_text' => $request->input('content.contact_button_text'),
                    ];
                    break;
                case 'faq':
                    $storeData['question'] = $request->input('content.question');
                    $storeData['answer'] = $request->input('content.answer');
                    break;
                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $extraContent = $parsed !== null ? $parsed : [];
                    break;
            }

            // Store keys without DB columns in extra_content as JSON
            if (!empty($extraContent)) {
                $storeData['extra_content'] = json_encode($extraContent);
            }

            // Clear the legacy content column (accessor ignores it; data is in normalized columns + extra_content)
            $storeData['content'] = null;

            $newContent->fill($storeData);
            $newContent->save();

            return response()->json([
                'success' => true,
                'message' => 'Express air freight solutions content stored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateExpressAirFreightSolutionsContent(Request $request, $id)
    {
        try {
            $content = \App\Models\ExpressAirFreightSolutionsPage::findOrFail($id);

            $updateData = [
                'section' => $request->section,
                'item_key' => $request->item_key,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ? 1 : 0,
            ];

            $extraContent = [];
            switch($request->section) {
                case 'hero':
                    $badges = $request->input('content.badges');
                    if (is_string($badges)) {
                        $badgesLines = array_map('trim', explode("\n", $badges));
                        $badges = [];
                        foreach ($badgesLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 2) {
                                $badges[] = ['icon' => $parts[0], 'text' => $parts[1]];
                            }
                        }
                    }
                    $statPills = $request->input('content.stat_pills');
                    if (is_string($statPills)) {
                        $statPillsLines = array_map('trim', explode("\n", $statPills));
                        $statPills = [];
                        foreach ($statPillsLines as $line) {
                            $parts = array_map('trim', explode('|', $line));
                            if (count($parts) >= 5) {
                                $statPills[] = ['icon' => $parts[0], 'value' => $parts[1], 'label' => $parts[2], 'color' => $parts[3], 'text_color' => $parts[4]];
                            }
                        }
                    }
                    $updateData['badge_text'] = $request->input('content.badge_text');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'button_primary_text' => $request->input('content.button_primary_text'),
                        'button_primary_icon' => $request->input('content.button_primary_icon'),
                        'button_primary_url' => $request->input('content.button_primary_url'),
                        'button_secondary_text' => $request->input('content.button_secondary_text'),
                        'button_secondary_icon' => $request->input('content.button_secondary_icon'),
                        'button_secondary_url' => $request->input('content.button_secondary_url'),
                        'image' => $request->input('content.image'),
                        'badges' => $badges,
                        'stat_pills' => $statPills,
                    ];
                    break;
                case 'stats':
                    $updateData['stat_value'] = $request->input('content.value');
                    $updateData['stat_label'] = $request->input('content.label');
                    $updateData['stat_suffix'] = $request->input('content.suffix');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                    ];
                    break;
                case 'overview':
                    $checkListInput = $request->input('content.check_list');
                    if (is_string($checkListInput) && trim($checkListInput) !== '') {
                        $checkListItems = array_map('trim', explode("\n", $checkListInput));
                        $checkListItems = array_filter($checkListItems, function ($v) { return $v !== ''; });
                        $updateData['check_list_text'] = implode("\n", $checkListItems);
                    } else {
                        $updateData['check_list_text'] = null;
                    }
                    $updateData['button_text'] = $request->input('content.button_text');
                    $updateData['button_url'] = $request->input('content.button_url');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'image' => $request->input('content.image'),
                    ];
                    break;
                case 'features_header':
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;
                case 'features':
                    $updateData['icon_class'] = $request->input('content.icon');
                    $updateData['color_scheme'] = $request->input('content.color_class');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                    ];
                    break;
                case 'testimonials_header':
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'description' => $request->input('content.description'),
                        'google_review_image' => $request->input('content.google_review_image'),
                    ];
                    break;
                case 'testimonials':
                    $updateData['name'] = $request->input('content.name');
                    $updateData['avatar_url'] = $request->input('content.avatar');
                    $updateData['rating'] = $request->input('content.rating');
                    $updateData['text_content'] = $request->input('content.text');
                    break;
                case 'faq_header':
                    $updateData['badge_text'] = $request->input('content.badge');
                    $extraContent = [
                        'title' => $request->input('content.title'),
                        'sidebar_image' => $request->input('content.sidebar_image'),
                        'sidebar_title' => $request->input('content.sidebar_title'),
                        'sidebar_description' => $request->input('content.sidebar_description'),
                        'contact_box_title' => $request->input('content.contact_box_title'),
                        'contact_box_description' => $request->input('content.contact_box_description'),
                        'contact_button_text' => $request->input('content.contact_button_text'),
                    ];
                    break;
                case 'faq':
                    $updateData['question'] = $request->input('content.question');
                    $updateData['answer'] = $request->input('content.answer');
                    break;
                default:
                    $rawJson = $request->input('content.json');
                    $parsed = json_decode($rawJson, true);
                    $extraContent = $parsed !== null ? $parsed : [];
                    break;
            }

            // Store keys without DB columns in extra_content as JSON
            if (!empty($extraContent)) {
                $updateData['extra_content'] = json_encode($extraContent);
            } else {
                $updateData['extra_content'] = null;
            }

            // Clear legacy content column (accessor ignores it)
            $updateData['content'] = null;

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Express air freight solutions content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * CKEditor Image Upload - stores images in blog_image folder
     */
    public function uploadBlogImage(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $request->validate([
                    'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
                ]);

                $file = $request->file('upload');
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $uploadPath = public_path('blog_image');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);

                $url = asset('blog_image/' . $fileName);

                return response()->json([
                    'uploaded' => true,
                    'url' => $url
                ]);
            }

            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'No file uploaded.']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => 'Error uploading image: ' . $e->getMessage()]
            ]);
        }
    }

    public function uploadMultipleBlogImages(Request $request)
    {
        try {
            if ($request->hasFile('images')) {
                $request->validate([
                    'images' => 'required|array',
                    'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
                ]);

                $uploadedUrls = [];
                $uploadPath = public_path('blog_image');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $file) {
                    $fileName = time() . '_' . uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    $file->move($uploadPath, $fileName);
                    $uploadedUrls[] = asset('blog_image/' . $fileName);
                }

                return response()->json([
                    'success' => true,
                    'urls' => $uploadedUrls,
                    'message' => count($uploadedUrls) . ' image(s) uploaded successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No images uploaded.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteExpressAirFreightSolutionsContent($id)
    {
        $content = \App\Models\ExpressAirFreightSolutionsPage::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Express air freight solutions content deleted successfully!'
        ]);
    }

    // ========== Barcode Generator Page Management ==========

    public function changeBarcodeGenerator()
    {
        $barcodeContent = \App\Models\BarcodeGeneratorPage::orderBy('display_order')->get();
        return view('admin.change-barcode-generator', ['barcodeContent' => $barcodeContent]);
    }

    public function updateBarcodeGeneratorContent(Request $request, $id)
    {
        try {
            $content = \App\Models\BarcodeGeneratorPage::findOrFail($id);

            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'page_badge_text' => $request->page_badge_text,
                'page_button_text' => $request->page_button_text,
                'page_icon_class' => $request->page_icon_class,
                'page_tag' => $request->page_tag,
                'page_label' => $request->page_label,
                'page_placeholder' => $request->page_placeholder,
                'link' => $request->link,
                'display_order' => $request->display_order ?? 0,
                'status' => $request->has('status') ? true : false,
            ];

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Barcode generator content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBarcodeGeneratorContent($id)
    {
        try {
            $content = \App\Models\BarcodeGeneratorPage::findOrFail($id);
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barcode generator content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Shipping Rate Calculator Page Management ==========

    public function changeShippingRateCalculator()
    {
        $shippingRateContent = \App\Models\ShippingRateCalculatorPage::orderBy('display_order')->get();
        return view('admin.change-shipping-rate-calculator', ['shippingRateContent' => $shippingRateContent]);
    }

    public function updateShippingRateCalculatorContent(Request $request, $id)
    {
        try {
            $content = \App\Models\ShippingRateCalculatorPage::findOrFail($id);

            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'page_badge_text' => $request->page_badge_text,
                'page_button_text' => $request->page_button_text,
                'page_icon_class' => $request->page_icon_class,
                'page_tag' => $request->page_tag,
                'page_label' => $request->page_label,
                'page_placeholder' => $request->page_placeholder,
                'link' => $request->link,
                'display_order' => $request->display_order ?? 0,
                'status' => $request->has('status') ? true : false,
            ];

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Shipping rate calculator content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteShippingRateCalculatorContent($id)
    {
        try {
            $content = \App\Models\ShippingRateCalculatorPage::findOrFail($id);
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shipping rate calculator content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== HSN Finder Page Management ==========

    public function changeHsnFinder()
    {
        $hsnFinderContent = \App\Models\HsnFinderPage::orderBy('display_order')->get();
        return view('admin.change-hsn-finder', ['hsnFinderContent' => $hsnFinderContent]);
    }

    public function updateHsnFinderContent(Request $request, $id)
    {
        try {
            $content = \App\Models\HsnFinderPage::findOrFail($id);

            $updateData = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'page_badge_text' => $request->page_badge_text,
                'page_button_text' => $request->page_button_text,
                'page_icon_class' => $request->page_icon_class,
                'page_tag' => $request->page_tag,
                'page_label' => $request->page_label,
                'page_placeholder' => $request->page_placeholder,
                'link' => $request->link,
                'display_order' => $request->display_order ?? 0,
                'status' => $request->has('status') ? true : false,
            ];

            $content->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'HSN finder content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteHsnFinderContent($id)
    {
        try {
            $content = \App\Models\HsnFinderPage::findOrFail($id);
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'HSN finder content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Partnership Page Management ==========

    public function changePartnership()
    {
        $partnerships = \App\Models\PartnershipPage::ordered()->get();
        return view('admin.change-partnership', compact('partnerships'));
    }

    public function createPartnership()
    {
        $partner = new \App\Models\PartnershipPage();
        return view('admin.edit-partnership', compact('partner'));
    }

    public function editPartnership($id)
    {
        try {
            $partner = \App\Models\PartnershipPage::findOrFail($id);
            return view('admin.edit-partnership', compact('partner'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-partnership')
                ->with('error', 'Partnership content not found.');
        }
    }

    public function getPartnership($id)
    {
        try {
            $partner = \App\Models\PartnershipPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'partner' => $partner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Partnership content not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storePartnership(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $partner = new \App\Models\PartnershipPage();
            $partner->fill($request->except(['image']));

            // Explicitly set as a partnership item (not page content)
            $partner->section = null;
            $partner->item_key = null;
            $partner->content = null;

            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $partner->image = $imagePath;
            }

            $partner->status = $request->status ?? 'Active';
            $partner->sort_order = $request->sort_order ?? 0;
            $partner->save();

            return response()->json([
                'success' => true,
                'message' => 'Partnership content created successfully!',
                'partner_id' => $partner->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updatePartnership(Request $request, $id)
    {
        try {
            $partner = \App\Models\PartnershipPage::findOrFail($id);

            if ($partner->section) {
                $request->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'link' => 'nullable|string|max:500',
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);

                $partner->title = $request->title ?? $partner->title;
                $partner->description = $request->description ?? $partner->description;
                $partner->link = $request->link ?? $partner->link;
                $partner->content = $request->json_fields ?? [];
                $partner->status = $request->status ?? 'Active';
                $partner->sort_order = $request->sort_order ?? 0;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                    $imagePath = 'public/website_images/' . $imageName;
                    $uploadPath = public_path('website_images');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $image->move($uploadPath, $imageName);
                    $partner->image = $imagePath;
                }

                $partner->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Page content updated successfully!'
                ]);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $partner->fill($request->except(['image']));

            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff|max:10240',
                ]);
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                $imagePath = 'public/website_images/' . $imageName;
                $uploadPath = public_path('website_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $imageName);
                $partner->image = $imagePath;
            }

            $partner->status = $request->status ?? 'Active';
            $partner->sort_order = $request->sort_order ?? 0;
            $partner->save();

            return response()->json([
                'success' => true,
                'message' => 'Partnership content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePartnership($id)
    {
        try {
            $partner = \App\Models\PartnershipPage::findOrFail($id);
            $partner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Partnership content deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function editAllPartnership()
    {
        $hero = \App\Models\PartnershipPage::bySection('hero')->active()->first();
        $logos = \App\Models\PartnershipPage::bySection('logos')->active()->ordered()->get();
        $formSection = \App\Models\PartnershipPage::bySection('partner_form')->active()->first();
        $aboutSection = \App\Models\PartnershipPage::bySection('about')->active()->first();
        $features = \App\Models\PartnershipPage::bySection('features')->active()->ordered()->get();
        $ecosystemSection = \App\Models\PartnershipPage::bySection('ecosystem')->active()->first();
        $ecosystemGlobalCards = \App\Models\PartnershipPage::bySection('ecosystem_global')->active()->ordered()->get();
        $ecosystemPartnerCards = \App\Models\PartnershipPage::bySection('ecosystem_partner')->active()->ordered()->get();
        $faqSection = \App\Models\PartnershipPage::bySection('faq')->active()->first();
        $faqItems = \App\Models\Faq::byPage('partnership')->active()->ordered()->get();

        return view('admin.edit-partnership-all', compact(
            'hero', 'logos', 'formSection', 'aboutSection', 'features',
            'ecosystemSection', 'ecosystemGlobalCards', 'ecosystemPartnerCards',
            'faqSection', 'faqItems'
        ));
    }

    public function updateAllPartnership(Request $request)
    {
        try {
            $data = $request->all();

            // Helper to update a single record
            $updateRecord = function ($id, $updates) {
                $record = \App\Models\PartnershipPage::findOrFail($id);
                foreach ($updates as $key => $value) {
                    if ($key === 'content' && is_array($value)) {
                        $record->content = $value;
                    } elseif ($key === 'image' && !empty($value)) {
                        $record->image = $value;
                    } elseif ($key === 'title' || $key === 'description' || $key === 'link' || $key === 'item_key' || $key === 'status' || $key === 'sort_order') {
                        $record->$key = $value;
                    }
                }
                $record->save();
            };

            // Update Hero
            if (isset($data['hero'])) {
                $heroData = $data['hero'];
                $updateRecord($heroData['id'], [
                    'content' => $heroData['content'] ?? [],
                    'image' => $heroData['image'] ?? null,
                ]);
            }

            // Update Logos
            if (isset($data['logos']) && is_array($data['logos'])) {
                foreach ($data['logos'] as $logoData) {
                    $updateRecord($logoData['id'], [
                        'title' => $logoData['title'] ?? '',
                        'image' => $logoData['image'] ?? null,
                    ]);
                }
            }

            // Update Partner Form
            if (isset($data['partner_form'])) {
                $formData = $data['partner_form'];
                $updateRecord($formData['id'], [
                    'content' => $formData['content'] ?? [],
                ]);
            }

            // Update About
            if (isset($data['about'])) {
                $aboutData = $data['about'];
                $updateRecord($aboutData['id'], [
                    'content' => $aboutData['content'] ?? [],
                ]);
            }

            // Update Features
            if (isset($data['features']) && is_array($data['features'])) {
                foreach ($data['features'] as $featureData) {
                    $updateRecord($featureData['id'], [
                        'title' => $featureData['title'] ?? '',
                    ]);
                }
            }

            // Update Ecosystem Section
            if (isset($data['ecosystem'])) {
                $ecoData = $data['ecosystem'];
                $updateRecord($ecoData['id'], [
                    'content' => $ecoData['content'] ?? [],
                ]);
            }

            // Update Ecosystem Global Cards
            if (isset($data['ecosystem_global']) && is_array($data['ecosystem_global'])) {
                foreach ($data['ecosystem_global'] as $cardData) {
                    $updateRecord($cardData['id'], [
                        'image' => $cardData['image'] ?? null,
                    ]);
                }
            }

            // Update Ecosystem Partner Cards
            if (isset($data['ecosystem_partner']) && is_array($data['ecosystem_partner'])) {
                foreach ($data['ecosystem_partner'] as $cardData) {
                    $updateRecord($cardData['id'], [
                        'title' => $cardData['title'] ?? '',
                        'image' => $cardData['image'] ?? null,
                    ]);
                }
            }

            // Update FAQ Section
            if (isset($data['faq'])) {
                $faqData = $data['faq'];
                $updateRecord($faqData['id'], [
                    'content' => $faqData['content'] ?? [],
                ]);
            }

            // Update FAQ Items (unified faq table)
            if (isset($data['faq_items']) && is_array($data['faq_items'])) {
                foreach ($data['faq_items'] as $faqItemData) {
                    $faq = \App\Models\Faq::findOrFail($faqItemData['id']);
                    $faq->question = $faqItemData['question'] ?? $faq->question;
                    $faq->answer = $faqItemData['answer'] ?? $faq->answer;
                    $faq->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'All partnership content updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // =============================================
    // DOCUMENT DOWNLOAD PAGE MANAGEMENT
    // =============================================

    public function updateDocumentDownloadPageMeta(Request $request)
    {
        try {
            $pageMeta = \App\Models\DocumentDownloadPage::bySection('page_meta')->active()->first();
            if (!$pageMeta) {
                $pageMeta = new \App\Models\DocumentDownloadPage();
                $pageMeta->section = 'page_meta';
                $pageMeta->status = 'Active';
            }

            $pageMeta->content = [
                'badge' => $request->input('badge'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ];
            $pageMeta->save();

            return response()->json([
                'success' => true,
                'message' => 'Page content updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function changeDocumentDownload()
    {
        $documents = \App\Models\DocumentDownloadPage::ordered()->get();
        $pageMeta = \App\Models\DocumentDownloadPage::bySection('page_meta')->first();
        return view('admin.change-document-download', compact('documents', 'pageMeta'));
    }

    public function createDocumentDownload()
    {
        $document = new \App\Models\DocumentDownloadPage();
        return view('admin.edit-document-download', compact('document'));
    }

    public function editDocumentDownload($id)
    {
        try {
            $document = \App\Models\DocumentDownloadPage::findOrFail($id);
            return view('admin.edit-document-download', compact('document'));
        } catch (\Exception $e) {
            return redirect()->route('admin.change-document-download')
                ->with('error', 'Document not found.');
        }
    }

    public function getDocumentDownload($id)
    {
        try {
            $document = \App\Models\DocumentDownloadPage::findOrFail($id);
            return response()->json([
                'success' => true,
                'document' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found: ' . $e->getMessage()
            ]);
        }
    }

    public function storeDocumentDownload(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file_type' => 'nullable|string|max:50',
                'file_size' => 'nullable|string|max:50',
                'document_file' => 'nullable|file|max:51200', // max 50MB
                'category' => 'nullable|string|max:100',
                'status_badge' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $document = new \App\Models\DocumentDownloadPage();
            $document->title = $request->title;
            $document->file_type = $request->file_type;
            $document->file_size = $request->file_size;
            $document->category = $request->category;
            $document->status_badge = $request->status_badge;
            $document->description = $request->description;
            $document->sort_order = $request->sort_order ?? 0;
            $document->status = $request->status ?? 'Active';

            // Handle file upload
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');

                // Get file size BEFORE moving (after move, temp file is gone)
                $bytes = $file->getSize();
                if ($bytes < 1024) {
                    $document->file_size = $bytes . ' B';
                } elseif ($bytes < 1048576) {
                    $document->file_size = round($bytes / 1024, 1) . ' KB';
                } elseif ($bytes < 1073741824) {
                    $document->file_size = round($bytes / 1048576, 1) . ' MB';
                } else {
                    $document->file_size = round($bytes / 1073741824, 2) . ' GB';
                }

                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $uploadPath = public_path('uploads/documents');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $document->file_url = asset('uploads/documents/' . $fileName);
            } else {
                $document->file_url = $request->file_url ?? '#';
            }

            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document created successfully!',
                'document_id' => $document->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDocumentDownload(Request $request, $id)
    {
        try {
            $document = \App\Models\DocumentDownloadPage::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'file_type' => 'nullable|string|max:50',
                'file_size' => 'nullable|string|max:50',
                'document_file' => 'nullable|file|max:51200', // max 50MB
                'category' => 'nullable|string|max:100',
                'status_badge' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'nullable|in:Active,Inactive',
            ]);

            $document->title = $request->title;
            $document->file_type = $request->file_type;
            $document->category = $request->category;
            $document->status_badge = $request->status_badge;
            $document->description = $request->description;
            $document->sort_order = $request->sort_order ?? 0;
            $document->status = $request->status ?? 'Active';

            // Handle file upload
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');

                // Get file size BEFORE moving (after move, temp file is gone)
                $bytes = $file->getSize();
                if ($bytes < 1024) {
                    $document->file_size = $bytes . ' B';
                } elseif ($bytes < 1048576) {
                    $document->file_size = round($bytes / 1024, 1) . ' KB';
                } elseif ($bytes < 1073741824) {
                    $document->file_size = round($bytes / 1048576, 1) . ' MB';
                } else {
                    $document->file_size = round($bytes / 1073741824, 2) . ' GB';
                }

                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $uploadPath = public_path('uploads/documents');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $document->file_url = asset('uploads/documents/' . $fileName);
            } else {
                $document->file_size = $request->file_size ?? $document->file_size;
            }

            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteDocumentDownload($id)
    {
        try {
            $document = \App\Models\DocumentDownloadPage::findOrFail($id);
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function editAllDocumentDownload()
    {
        $documents = \App\Models\DocumentDownloadPage::ordered()->get();
        return view('admin.edit-document-download-all', compact('documents'));
    }

    public function updateAllDocumentDownload(Request $request)
    {
        try {
            $data = $request->all();

            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $docId => $docData) {
                    $record = \App\Models\DocumentDownloadPage::findOrFail($docId);
                    $record->title = $docData['title'] ?? $record->title;
                    $record->file_type = $docData['file_type'] ?? $record->file_type;
                    $record->category = $docData['category'] ?? $record->category;
                    $record->status_badge = $docData['status_badge'] ?? $record->status_badge;
                    $record->description = $docData['description'] ?? $record->description;
                    $record->sort_order = $docData['sort_order'] ?? $record->sort_order;
                    $record->status = $docData['status'] ?? $record->status;

                    // Handle file upload for this document
                    if ($request->hasFile("documents.{$docId}.document_file")) {
                        $file = $request->file("documents.{$docId}.document_file");

                        // Get file size BEFORE moving (after move, temp file is gone)
                        $bytes = $file->getSize();
                        if ($bytes < 1024) {
                            $record->file_size = $bytes . ' B';
                        } elseif ($bytes < 1048576) {
                            $record->file_size = round($bytes / 1024, 1) . ' KB';
                        } elseif ($bytes < 1073741824) {
                            $record->file_size = round($bytes / 1048576, 1) . ' MB';
                        } else {
                            $record->file_size = round($bytes / 1073741824, 2) . ' GB';
                        }

                        $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                        $uploadPath = public_path('uploads/documents');

                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }

                        $file->move($uploadPath, $fileName);
                        $record->file_url = asset('uploads/documents/' . $fileName);
                    } else {
                        $record->file_size = $docData['file_size'] ?? $record->file_size;
                        $record->file_url = $docData['file_url'] ?? $record->file_url;
                    }

                    $record->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'All documents updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Common Stats (Fact Number Section) Management ==========

    public function changeCommonStats()
    {
        $commonStats = \App\Models\FactNumberSectionCommonPage::orderBy('display_order')->get();
        return view('admin.change-common-stats', compact('commonStats'));
    }

    public function updateCommonStats(Request $request, $id)
    {
        try {
            $stat = \App\Models\FactNumberSectionCommonPage::findOrFail($id);

            $stat->update([
                'title'         => $request->title,
                'target_number' => $request->target_number,
                'suffix'        => $request->suffix,
                'display_order' => $request->display_order ?? 0,
                'status'        => $request->has('status') ? true : false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stat updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteCommonStats($id)
    {
        try {
            $stat = \App\Models\FactNumberSectionCommonPage::findOrFail($id);
            $stat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stat deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========== Partners Section (Logos) Management ==========

    public function changePartnerLogos()
    {
        $partnerLogos = \App\Models\PartnersSectionCommonPage::orderBy('display_order')->get();
        return view('admin.change-partner-logos', compact('partnerLogos'));
    }

    public function storePartnerLogo(Request $request)
    {
        try {
            $request->validate([
                'logo_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'alt_text' => 'nullable|string|max:255',
                'display_order' => 'nullable|integer|min:0',
            ]);

            // Handle file upload
            $image = $request->file('logo_image');
            $fileName = time() . '_partner_' . str_replace(' ', '_', $image->getClientOriginalName());
            $uploadPath = public_path('uploads/partner_logos');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $image->move($uploadPath, $fileName);
            $imageUrl = asset('uploads/partner_logos/' . $fileName);

            \App\Models\PartnersSectionCommonPage::create([
                'logo_image'    => $imageUrl,
                'alt_text'      => $request->alt_text,
                'display_order' => $request->display_order ?? 0,
                'status'        => $request->has('status') ? true : false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Partner logo added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function updatePartnerLogo(Request $request, $id)
    {
        try {
            $logo = \App\Models\PartnersSectionCommonPage::findOrFail($id);

            $data = [
                'alt_text'      => $request->alt_text,
                'display_order' => $request->display_order ?? 0,
                'status'        => $request->has('status') ? true : false,
            ];

            // Handle file upload if a new image is provided
            if ($request->hasFile('logo_image')) {
                $image = $request->file('logo_image');
                $fileName = time() . '_partner_' . str_replace(' ', '_', $image->getClientOriginalName());
                $uploadPath = public_path('uploads/partner_logos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $fileName);
                $data['logo_image'] = asset('uploads/partner_logos/' . $fileName);
            }

            $logo->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Partner logo updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePartnerLogo($id)
    {
        try {
            $logo = \App\Models\PartnersSectionCommonPage::findOrFail($id);
            $logo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Partner logo deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function changeSubscribers()
    {
        $subscribers = \App\Models\Subscriber::orderBy('id', 'desc')->get();
        return view('admin.change-subscribers', compact('subscribers'));
    }

    public function changeFaqQueries()
    {
        $queries = \App\Models\FaqQuery::orderBy('id', 'desc')->get();
        return view('admin.change-faq-queries', compact('queries'));
    }

    public function kycPending()
    {
        $kycDetails = \App\Models\KycDetail::with('customer')
            ->where('kyc_status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.kyc-pending', compact('kycDetails'));
    }

    public function kycApproved()
    {
        $approvedKycDetails = \App\Models\KycDetail::with('customer')
            ->where('kyc_status', 'approved')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.kyc-approved', compact('approvedKycDetails'));
    }

    public function approveKyc($id)
    {
        $kycDetail = \App\Models\KycDetail::findOrFail($id);
        $kycDetail->kyc_status = 'approved';
        $kycDetail->save();

        // Clone all default rates (customer_id = 0) from courier_rates for the approved customer
        $defaultRates = \App\Models\CourierRate::where('customer_id', 0)->get();

        foreach ($defaultRates as $rate) {
            $newRate = $rate->replicate();
            $newRate->customer_id = $kycDetail->customer_id;
            $newRate->save();
        }

        $ratesCount = $defaultRates->count();

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been approved successfully. ' . $ratesCount . ' courier rates copied.');
    }

    public function rejectKyc($id)
    {
        $kycDetail = \App\Models\KycDetail::findOrFail($id);
        $kycDetail->kyc_status = 'rejected';
        $kycDetail->save();

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been rejected.');
    }

    public function manageRate()
    {
        $defaultRates = \App\Models\CourierRate::with('service')
            ->where('customer_id', 0)
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start')
            ->get();

        $customers = \App\Models\Customer::orderBy('first_name')->get();

        $services = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

        return view('admin.manage-rate', compact('defaultRates', 'customers', 'services'));
    }

    public function getCustomerRates(Request $request)
    {
        $customerId = $request->customer_id;

        $rates = \App\Models\CourierRate::with('service')
            ->where('customer_id', $customerId)
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start')
            ->get();

        return response()->json(['rates' => $rates]);
    }

    public function updateRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);
        $rate->price = $request->price;
        $rate->save();

        return response()->json(['success' => true, 'message' => 'Rate updated successfully.']);
    }

    public function updateCustomerRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);
        $rate->price = $request->price;
        $rate->save();

        return response()->json(['success' => true, 'message' => 'Customer rate updated successfully.']);
    }

}
