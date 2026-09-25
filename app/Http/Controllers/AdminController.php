<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;
use App\Models\BusinessCategory;
use App\Models\ConsigneeInfo;
use App\Models\CourierRate;
use App\Models\CourierService;
use App\Models\CreateShipment;
use App\Models\CsbForm;
use App\Models\CsbInformation;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\ExporterCustomer;
use App\Models\KycDetail;
use App\Models\NetworkOffice;
use App\Models\PackageDimension;
use App\Models\ShipmentDispute;
use App\Models\ShipmentInvoice;
use App\Models\ShipmentInvoiceItem;
use App\Models\ShipmentLog;
use App\Models\ShipperInfo;
use App\Models\SurCharge;
use App\Models\Tracking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Zone;
use App\Notifications\DeliveryAssignedNotification;
use App\Services\AdomantraApiClient;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class AdminController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Please login first');
        }
        if (!$admin->canAccessDashboard()) {
            return redirect()->route('admin.my-profile')
                ->with('error', 'You do not have permission to access the dashboard.');
        }

        // Date helpers (used by the stat card month-over-month calculations)
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        // Customer Summary
        $totalRegistrations = Customer::count();
        $kycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->count();
        $onboardedCustomers = KycDetail::where('kyc_status', 'approved')->count();
        $csb5Enabled = Customer::where('csb_status', 2)->count();

        // Shipment Summary (all statuses)
        $shipmentStatusCounts = ShipperInfo::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalShipments = array_sum($shipmentStatusCounts);

        // In-transit shipments (any shipment that has left the draft/pending stage
        // and has not reached a terminal state)
        $inTransitStatuses = ['assigned_for_pickup', 'confirm_pickup', 'packed', 'manifested', 'dispatched', 'ready_to_dispatch', 'received'];
        $inTransit = collect($inTransitStatuses)->sum(fn ($status) => $shipmentStatusCounts[$status] ?? 0);

        // Delivery success rate: delivered / (total - cancelled - disputed)
        $terminalExcluded = $totalShipments - ($shipmentStatusCounts['cancelled'] ?? 0) - ($shipmentStatusCounts['disputed'] ?? 0);
        $deliveredCount = $shipmentStatusCounts['delivered'] ?? 0;
        $deliverySuccessRate = $terminalExcluded > 0 ? round($deliveredCount / $terminalExcluded * 100, 1) : 0;

        // Revenue from shipment invoices for this month vs last month
        $thisMonthRevenue = ShipmentInvoice::whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])
            ->where('invoice_amount', '>', 0)
            ->sum('invoice_amount');
        $lastMonthRevenue = ShipmentInvoice::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->where('invoice_amount', '>', 0)
            ->sum('invoice_amount');
        $revenueChangePercent = $lastMonthRevenue > 0 ? round(($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100, 1) : ($thisMonthRevenue > 0 ? 100 : 0);

        // Wallet top-ups (credit transactions) for this month
        $thisMonthWalletTopups = WalletTransaction::where('type', 'credit')
            ->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])
            ->sum('amount');
        $lastMonthWalletTopups = WalletTransaction::where('type', 'credit')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $walletTopupsChangePercent = $lastMonthWalletTopups > 0 ? round(($thisMonthWalletTopups - $lastMonthWalletTopups) / $lastMonthWalletTopups * 100, 1) : ($thisMonthWalletTopups > 0 ? 100 : 0);

        // Total customer wallet balance held by the company
        $walletBalanceTotal = Wallet::sum('balance');

        // Today's shipments (for the quick glance strip)
        $todayShipments = ShipperInfo::whereDate('created_at', now()->toDateString())->count();
        $todayRegistrations = Customer::whereDate('created_at', now()->toDateString())->count();

        // Delivery/Network Summary - group by shipping_method
        $networkCounts = ShipperInfo::select('shipping_method', DB::raw('count(*) as count'))
            ->whereNotNull('shipping_method')
            ->where('shipping_method', '!=', '')
            ->groupBy('shipping_method')
            ->pluck('count', 'shipping_method')
            ->toArray();

        // Categorize networks: ShipRocket, Self/Delhivery/UPS/etc
        $shipRocketCount = 0;
        $selfCount = 0;
        $otherNetworkCount = 0;
        foreach ($networkCounts as $method => $count) {
            $methodLower = strtolower($method);
            if (str_contains($methodLower, 'shiprocket') || str_contains($methodLower, 'ship_rocket')) {
                $shipRocketCount += $count;
            } elseif (str_contains($methodLower, 'self') || str_contains($methodLower, 'own')) {
                $selfCount += $count;
            } else {
                $otherNetworkCount += $count;
            }
        }
        // Delivered shipments count
        $deliveredCount = $shipmentStatusCounts['delivered'] ?? 0;

        // Pickup & Dispatch funnel (display-only section on admin dashboard).
        // Mapping mirrors the companies page tabs:
        //   Ready for Pickup    = ready_for_pickup
        //   Assigned for Pickup = assigned_for_pickup
        //   Print Label         = received + dispatched
        //   Ready to Dispatch   = ready_to_dispatch
        $pickupDispatchCounts = [
            'ready_for_pickup'    => (int) ($shipmentStatusCounts['ready_for_pickup'] ?? 0),
            'assigned_for_pickup' => (int) ($shipmentStatusCounts['assigned_for_pickup'] ?? 0),
            'print_label'         => (int) ($shipmentStatusCounts['received'] ?? 0) + (int) ($shipmentStatusCounts['dispatched'] ?? 0),
            'ready_to_dispatch'   => (int) ($shipmentStatusCounts['ready_to_dispatch'] ?? 0),
        ];

        // Latest shipments sitting in the pickup/dispatch funnel (for the dashboard table).
        $pickupDispatchRows = DB::table('shipper_info')
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
            ->whereIn('shipper_info.status', ['ready_for_pickup', 'assigned_for_pickup', 'received', 'dispatched', 'ready_to_dispatch'])
            ->select(
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.city as pickup_city',
                'shipper_info.status',
                'shipper_info.created_at',
                'consignee_info.consignee_name',
                'consignee_info.city as destination_city',
                'customers.first_name',
                'customers.last_name'
            )
            ->orderByDesc('shipper_info.created_at')
            ->limit(10)
            ->get();

        // COD / Prepaid / General split (order-type analytics).
        // COD = shipment_type 2, Prepaid = 4 or 5 (codebase uses both),
        // General = everything else (incl. type 1 / null).
        $orderTypeCounts = ShipperInfo::select('shipment_type', DB::raw('count(*) as count'))
            ->groupBy('shipment_type')
            ->pluck('count', 'shipment_type')
            ->toArray();
        $codCount = (int) ($orderTypeCounts[2] ?? 0);
        $prepaidCount = (int) ($orderTypeCounts[4] ?? 0) + (int) ($orderTypeCounts[5] ?? 0);
        $generalCount = $totalShipments - $codCount - $prepaidCount;

        $thisMonthRegistrations = Customer::whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthRegistrations = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $registrationsChangePercent = $lastMonthRegistrations > 0 ? round(($thisMonthRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations * 100, 1) : ($thisMonthRegistrations > 0 ? 100 : 0);

        $thisMonthKycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthKycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $kycPendingChangePercent = $lastMonthKycPending > 0 ? round(($thisMonthKycPending - $lastMonthKycPending) / $lastMonthKycPending * 100, 1) : ($thisMonthKycPending > 0 ? 100 : 0);

        $thisMonthOnboarded = KycDetail::where('kyc_status', 'approved')->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthOnboarded = KycDetail::where('kyc_status', 'approved')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $onboardedChangePercent = $lastMonthOnboarded > 0 ? round(($thisMonthOnboarded - $lastMonthOnboarded) / $lastMonthOnboarded * 100, 1) : ($thisMonthOnboarded > 0 ? 100 : 0);

        $thisMonthCsb5 = Customer::where('csb_status', 2)->whereBetween('created_at', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $lastMonthCsb5 = Customer::where('csb_status', 2)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $csb5ChangePercent = $lastMonthCsb5 > 0 ? round(($thisMonthCsb5 - $lastMonthCsb5) / $lastMonthCsb5 * 100, 1) : ($thisMonthCsb5 > 0 ? 100 : 0);

        // KYC Pending list for the dashboard table
        $kycPendingList = KycDetail::with('customer')
            ->whereIn('kyc_status', ['pending', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent shipments for the activity feed
        $recentShipments = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
            ->select(
                'shipment_invoice.invoice_number',
                'shipment_invoice.invoice_amount',
                'shipment_invoice.invoice_currency',
                'shipment_invoice.created_at',
                'shipper_info.id as shipper_id',
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.city as pickup_city',
                'shipper_info.status',
                'consignee_info.consignee_name',
                'consignee_info.city as destination_city',
                'customers.first_name',
                'customers.last_name'
            )
            ->orderByDesc('shipment_invoice.created_at')
            ->limit(8)
            ->get();

        // Recent customer registrations for the activity feed
        $recentRegistrations = Customer::select('id', 'first_name', 'last_name', 'email', 'phone_number', 'created_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // Greeting for the dashboard header
        $adminName = explode(' ', (string) $admin->name)[0] ?: $admin->name;
        $currentHour = (int) $now->format('H');
        $greeting = $currentHour < 12 ? 'Good morning' : ($currentHour < 17 ? 'Good afternoon' : 'Good evening');

        return view('admin.dashboard', compact(
            'adminName', 'greeting',
            'totalRegistrations', 'kycPending', 'onboardedCustomers', 'csb5Enabled',
            'shipmentStatusCounts', 'shipRocketCount', 'selfCount', 'otherNetworkCount', 'deliveredCount',
            'networkCounts',
            'codCount', 'prepaidCount', 'generalCount',
            'registrationsChangePercent', 'kycPendingChangePercent', 'onboardedChangePercent', 'csb5ChangePercent',
            'kycPendingList',
            'totalShipments', 'inTransit', 'deliverySuccessRate',
            'thisMonthRevenue', 'revenueChangePercent',
            'thisMonthWalletTopups', 'walletTopupsChangePercent', 'walletBalanceTotal',
            'todayShipments', 'todayRegistrations',
            'recentShipments', 'recentRegistrations',
            'pickupDispatchCounts', 'pickupDispatchRows'
        ));
    }

    /**
     * Return chart data for the admin dashboard via AJAX.
     * Supports date filters: today, yesterday, this_month, last_month, last_year
     */
    public function dashboardChartData(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDashboard()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $filter = $request->input('filter', 'this_month');
        $now = now();

        // Determine date range based on filter
        switch ($filter) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last_year':
                $startDate = $now->copy()->subYearNoOverflow()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
        }

        // Customer Summary — cumulative (all-time), not date-filtered
        // These metrics represent total counts regardless of date period
        $totalRegistrations = Customer::count();
        $kycPending = KycDetail::whereIn('kyc_status', ['pending', 'under_review'])->count();
        $onboardedCustomers = KycDetail::where('kyc_status', 'approved')->count();
        $csb5Enabled = Customer::where('csb_status', 2)->count();

        // Shipment Summary for filtered period
        $shipmentStatusCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Network/Delivery Summary for filtered period
        $networkCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('shipping_method')
            ->where('shipping_method', '!=', '')
            ->select('shipping_method', DB::raw('count(*) as count'))
            ->groupBy('shipping_method')
            ->pluck('count', 'shipping_method')
            ->toArray();

        $shipRocketCount = 0;
        $selfCount = 0;
        $otherNetworks = [];
        foreach ($networkCounts as $method => $count) {
            $methodLower = strtolower($method);
            if (str_contains($methodLower, 'shiprocket') || str_contains($methodLower, 'ship_rocket')) {
                $shipRocketCount += $count;
            } elseif (str_contains($methodLower, 'self') || str_contains($methodLower, 'own')) {
                $selfCount += $count;
            } else {
                $otherNetworks[$method] = $count;
            }
        }
        $deliveredCount = $shipmentStatusCounts['delivered'] ?? 0;

        // Extra period-based business metrics for the stat tiles
        $periodRevenue = ShipmentInvoice::whereBetween('created_at', [$startDate, $endDate])
            ->where('invoice_amount', '>', 0)
            ->sum('invoice_amount');

        $periodWalletTopups = WalletTransaction::where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $inTransitStatuses = ['ready_for_pickup', 'assigned_for_pickup', 'confirm_pickup', 'packed', 'manifested', 'dispatched', 'ready_to_dispatch', 'received'];
        $periodInTransit = collect($inTransitStatuses)->sum(fn ($status) => $shipmentStatusCounts[$status] ?? 0);

        $totalPeriodShipments = array_sum($shipmentStatusCounts);
        $periodEligible = $totalPeriodShipments - ($shipmentStatusCounts['cancelled'] ?? 0) - ($shipmentStatusCounts['disputed'] ?? 0);
        $periodSuccessRate = $periodEligible > 0 ? round($deliveredCount / $periodEligible * 100, 1) : 0;

        // Date-wise shipment counts for trend chart
        if ($filter === 'last_year') {
            $dateWiseCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"), DB::raw('count(*) as count'))
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('count', 'period')
                ->toArray();
        } else {
            $dateWiseCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as period"), DB::raw('count(*) as count'))
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('count', 'period')
                ->toArray();
        }

        // COD / Prepaid / General split for the selected period.
        // COD = shipment_type 2, Prepaid = 4 or 5, General = rest.
        $periodTypeCounts = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->select('shipment_type', DB::raw('count(*) as count'))
            ->groupBy('shipment_type')
            ->pluck('count', 'shipment_type')
            ->toArray();
        $periodCod = (int) ($periodTypeCounts[2] ?? 0);
        $periodPrepaid = (int) ($periodTypeCounts[4] ?? 0) + (int) ($periodTypeCounts[5] ?? 0);
        $periodGeneral = $totalPeriodShipments - $periodCod - $periodPrepaid;

        // Date-wise COD vs Prepaid trend for the selected period.
        $periodFormat = $filter === 'last_year' ? '%Y-%m' : '%Y-%m-%d';
        $trendRows = ShipperInfo::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$periodFormat}') as period"),
                DB::raw("SUM(CASE WHEN shipment_type = 2 THEN 1 ELSE 0 END) as cod"),
                DB::raw("SUM(CASE WHEN shipment_type IN (4, 5) THEN 1 ELSE 0 END) as prepaid")
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        $orderTypeTrend = [
            'labels' => $trendRows->pluck('period')->all(),
            'cod' => $trendRows->pluck('cod')->map(fn ($v) => (int) $v)->all(),
            'prepaid' => $trendRows->pluck('prepaid')->map(fn ($v) => (int) $v)->all(),
        ];

        // Pickup & Dispatch funnel for the selected period (same mapping as index()).
        $pickupDispatchSummary = [
            'ready_for_pickup'    => (int) ($shipmentStatusCounts['ready_for_pickup'] ?? 0),
            'assigned_for_pickup' => (int) ($shipmentStatusCounts['assigned_for_pickup'] ?? 0),
            'print_label'         => (int) ($shipmentStatusCounts['received'] ?? 0) + (int) ($shipmentStatusCounts['dispatched'] ?? 0),
            'ready_to_dispatch'   => (int) ($shipmentStatusCounts['ready_to_dispatch'] ?? 0),
        ];

        $pickupDispatchRows = ShipperInfo::whereBetween('shipper_info.created_at', [$startDate, $endDate])
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
            ->whereIn('shipper_info.status', ['ready_for_pickup', 'assigned_for_pickup', 'received', 'dispatched', 'ready_to_dispatch'])
            ->select(
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.city as pickup_city',
                'shipper_info.status',
                'shipper_info.created_at',
                'consignee_info.consignee_name',
                'consignee_info.city as destination_city',
                'customers.first_name',
                'customers.last_name'
            )
            ->orderByDesc('shipper_info.created_at')
            ->limit(10)
            ->get();

        $statusMap = Tracking::getStatusTitleMap();

        return response()->json([
            'success' => true,
            'filter' => $filter,
            'customerSummary' => [
                'totalRegistrations' => $totalRegistrations,
                'kycPending' => $kycPending,
                'onboardedCustomers' => $onboardedCustomers,
                'csb5Enabled' => $csb5Enabled,
            ],
            'shipmentStatusCounts' => $shipmentStatusCounts,
            'statusMap' => $statusMap,
            'deliverySummary' => [
                'delivered' => $deliveredCount,
                'shipRocket' => $shipRocketCount,
                'self' => $selfCount,
                'otherNetworks' => $otherNetworks,
            ],
            'businessSummary' => [
                'totalShipments' => $totalPeriodShipments,
                'revenue' => $periodRevenue,
                'walletTopups' => $periodWalletTopups,
                'inTransit' => $periodInTransit,
                'successRate' => $periodSuccessRate,
            ],
            'orderTypeSummary' => [
                'cod' => $periodCod,
                'prepaid' => $periodPrepaid,
                'general' => $periodGeneral,
            ],
            'orderTypeTrend' => $orderTypeTrend,
            'dateWiseCounts' => $dateWiseCounts,
            'pickupDispatchSummary' => $pickupDispatchSummary,
            'pickupDispatchRows' => $pickupDispatchRows,
        ]);
    }

    /**
     * Show delivery statistics scoped to the authenticated delivery person.
     */
    public function deliveryDashboard()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDeliveryDashboard()) {
            return redirect()->route('admin.my-profile')
                ->with('error', 'You do not have permission to access the delivery dashboard.');
        }

        $assignedShipments = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->where('shipment_invoice.assigned_delivery_person', $admin->id);

        $statusCounts = (clone $assignedShipments)
            ->select('shipper_info.status', DB::raw('count(*) as count'))
            ->groupBy('shipper_info.status')
            ->pluck('count', 'status')
            ->toArray();

        $totalAssigned = array_sum($statusCounts);
        $completed = $statusCounts['delivered'] ?? 0;
        $pendingPickup = $statusCounts['assigned_for_pickup'] ?? 0;
        $inProgressStatuses = ['received', 'confirm_pickup', 'ready_to_dispatch', 'dispatched'];
        $performedStatuses = array_merge($inProgressStatuses, ['delivered']);
        $inProgress = collect($inProgressStatuses)->sum(fn ($status) => $statusCounts[$status] ?? 0);
        $performed = collect($performedStatuses)->sum(fn ($status) => $statusCounts[$status] ?? 0);
        $completionPercentage = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100, 1) : 0;

        $recentDeliveries = (clone $assignedShipments)
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->select(
                'shipment_invoice.id',
                'shipment_invoice.invoice_number',
                'shipment_invoice.updated_at as assigned_at',
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.contact_person as pickup_name',
                'shipper_info.address_line1 as pickup_address_line1',
                'shipper_info.address_line2 as pickup_address_line2',
                'shipper_info.address_line3 as pickup_address_line3',
                'shipper_info.pincode as pickup_pincode',
                'shipper_info.city as pickup_city',
                'shipper_info.state as pickup_state',
                'shipper_info.phone_number as pickup_phone',
                'shipper_info.status',
                'consignee_info.consignee_name',
                'consignee_info.address_line1 as destination_address_line1',
                'consignee_info.address_line2 as destination_address_line2',
                'consignee_info.address_line3 as destination_address_line3',
                'consignee_info.zip_code as destination_pincode',
                'consignee_info.city as destination_city',
                'consignee_info.state as destination_state',
                'consignee_info.phone_number as destination_phone'
            )
            ->orderByDesc('shipment_invoice.updated_at')
            ->limit(10)
            ->get();

        $statusMap = Tracking::getStatusTitleMap();

        return view('admin.delivery-dashboard', compact(
            'admin',
            'totalAssigned',
            'completed',
            'pendingPickup',
            'inProgress',
            'performed',
            'completionPercentage',
            'statusCounts',
            'statusMap',
            'recentDeliveries'
        ));
    }

    /**
     * Show deliveries assigned to the authenticated delivery person.
     */
    public function deliveryOrders(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDeliveryDashboard()) {
            return redirect()->route('admin.my-profile')
                ->with('error', 'You do not have permission to access delivery records.');
        }

        $view = $request->input('view', 'pending');
        if (!in_array($view, ['pending', 'process_pickup', 'completed', 'history'], true)) {
            $view = 'pending';
        }

        $baseQuery = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
            ->where('shipment_invoice.assigned_delivery_person', $admin->id);

        $pendingStatuses = [
            'received',
            'confirm_pickup',
            'ready_to_dispatch',
            'dispatched',
            'delivered',
            'cancelled',
            'disputed',
        ];
        // Confirmed pickup rows remain in Process Pickup until the hub confirms receipt.
        $processPickupStatuses = ['confirm_pickup'];
        $completedStatuses = ['received', 'delivered'];
        $pendingCount = (clone $baseQuery)->whereNotIn('shipper_info.status', $pendingStatuses)->count();
        $processPickupCount = (clone $baseQuery)->whereIn('shipper_info.status', $processPickupStatuses)->count();
        $completedCount = (clone $baseQuery)->whereIn('shipper_info.status', $completedStatuses)->count();
        $historyCount = (clone $baseQuery)->count();

        if ($view === 'pending') {
            $baseQuery->whereNotIn('shipper_info.status', $pendingStatuses);
        } elseif ($view === 'process_pickup') {
            $baseQuery->whereIn('shipper_info.status', $processPickupStatuses);
        } elseif ($view === 'completed') {
            $baseQuery->whereIn('shipper_info.status', $completedStatuses);
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('shipper_info.awb_number', 'like', '%' . $search . '%')
                    ->orWhere('shipment_invoice.invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('shipper_info.company_name', 'like', '%' . $search . '%')
                    ->orWhere('shipper_info.contact_person', 'like', '%' . $search . '%')
                    ->orWhere('shipper_info.address_line1', 'like', '%' . $search . '%')
                    ->orWhere('consignee_info.consignee_name', 'like', '%' . $search . '%')
                    ->orWhere('consignee_info.city', 'like', '%' . $search . '%')
                    ->orWhere('consignee_info.address_line1', 'like', '%' . $search . '%');
            });
        }

        $deliveries = $baseQuery
            ->select(
                'shipment_invoice.id',
                'shipment_invoice.invoice_number',
                'shipment_invoice.delivery_type',
                'shipment_invoice.updated_at as assigned_at',
                'shipper_info.id as shipper_id',
                'shipper_info.awb_number',
                'shipper_info.company_name',
                'shipper_info.contact_person as pickup_name',
                'shipper_info.address_line1 as pickup_address_line1',
                'shipper_info.address_line2 as pickup_address_line2',
                'shipper_info.address_line3 as pickup_address_line3',
                'shipper_info.pincode as pickup_pincode',
                'shipper_info.city as pickup_city',
                'shipper_info.state as pickup_state',
                'shipper_info.phone_number as pickup_phone',
                'shipper_info.status',
                'consignee_info.consignee_name',
                'consignee_info.contact_person as consignee_contact',
                'consignee_info.address_line1 as destination_address_line1',
                'consignee_info.address_line2 as destination_address_line2',
                'consignee_info.address_line3 as destination_address_line3',
                'consignee_info.zip_code as destination_pincode',
                'consignee_info.city as destination_city',
                'consignee_info.state as destination_state',
                'consignee_info.phone_number as destination_phone'
            )
            ->orderByDesc('shipment_invoice.updated_at')
            ->paginate(15)
            ->withQueryString();

        $statusMap = Tracking::getStatusTitleMap();

        return view('admin.delivery-orders', compact(
            'admin',
            'view',
            'search',
            'deliveries',
            'pendingCount',
            'processPickupCount',
            'completedCount',
            'historyCount',
            'statusMap'
        ));
    }

    /**
     * Confirm pickup for an assigned delivery and move it into process.
     */
    public function pickupDelivery(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDeliveryDashboard()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['shipment_id' => 'required|integer']);

        $shipment = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->where('shipment_invoice.id', $request->integer('shipment_id'))
            ->where('shipment_invoice.assigned_delivery_person', $admin->id)
            ->select('shipment_invoice.id', 'shipper_info.id as shipper_id', 'shipper_info.awb_number', 'shipper_info.status')
            ->first();

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'This delivery is not assigned to you.'], 403);
        }

        if (in_array($shipment->status, ['delivered', 'cancelled', 'disputed', 'received', 'confirm_pickup', 'ready_to_dispatch', 'dispatched'], true)) {
            return response()->json(['success' => false, 'message' => 'This delivery is already in process or completed.'], 422);
        }

        DB::transaction(function () use ($shipment) {
            Tracking::create([
                'awb_number' => $shipment->awb_number,
                'status' => 'confirm_pickup',
                'title' => 'Pickup Confirmed - In Process',
                'shipper_id' => $shipment->shipper_id,
                'uwc_id' => $shipment->awb_number,
            ]);

            DB::table('shipper_info')->where('id', $shipment->shipper_id)->update([
                'status' => 'confirm_pickup',
                'updated_at' => now(),
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Pickup confirmed. Delivery moved to In Process.']);
    }

    /**
     * Mark a picked-up shipment as received in the hub. The Complete Delivery
     * view includes received shipments, while the No option remains non-destructive.
     */
    public function receivedInHub(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDeliveryDashboard()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['shipment_id' => 'required|integer']);

        $shipment = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->where('shipment_invoice.id', $request->integer('shipment_id'))
            ->where('shipment_invoice.assigned_delivery_person', $admin->id)
            ->select(
                'shipment_invoice.id',
                'shipper_info.id as shipper_id',
                'shipper_info.awb_number',
                'shipper_info.status'
            )
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'This delivery is not assigned to you.',
            ], 403);
        }

        if ($shipment->status === 'received') {
            return response()->json([
                'success' => true,
                'message' => 'Shipment is already in Complete Delivery.',
            ]);
        }

        if ($shipment->status !== 'confirm_pickup') {
            return response()->json([
                'success' => false,
                'message' => 'Only Process Pickup shipments can be received in hub.',
            ], 422);
        }

        DB::transaction(function () use ($shipment) {
            Tracking::create([
                'awb_number' => $shipment->awb_number,
                'status' => 'received',
                'title' => 'Shipment Received in Hub',
                'shipper_id' => $shipment->shipper_id,
                'uwc_id' => $shipment->awb_number,
            ]);

            DB::table('shipper_info')
                ->where('id', $shipment->shipper_id)
                ->where('status', 'confirm_pickup')
                ->update([
                    'status' => 'received',
                    'updated_at' => now(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Shipment received in hub and moved to Complete Delivery.',
        ]);
    }

    /**
     * Return filtered chart data for the authenticated delivery person.
     */
    public function deliveryDashboardChartData(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->canAccessDeliveryDashboard()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $filter = $request->input('filter', 'this_month');
        $now = now();

        switch ($filter) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'last_year':
                $startDate = $now->copy()->subYearNoOverflow()->startOfYear();
                $endDate = $now->copy()->subYearNoOverflow()->endOfYear();
                break;
            case 'this_month':
            default:
                $filter = 'this_month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        $baseQuery = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->where('shipment_invoice.assigned_delivery_person', $admin->id)
            ->whereBetween('shipment_invoice.updated_at', [$startDate, $endDate]);

        $statusCounts = (clone $baseQuery)
            ->select('shipper_info.status', DB::raw('count(*) as count'))
            ->groupBy('shipper_info.status')
            ->pluck('count', 'status')
            ->toArray();

        $periodExpression = $filter === 'last_year'
            ? "DATE_FORMAT(shipment_invoice.updated_at, '%Y-%m')"
            : "DATE_FORMAT(shipment_invoice.updated_at, '%Y-%m-%d')";

        $assignmentTrend = (clone $baseQuery)
            ->select(DB::raw($periodExpression . ' as period'), DB::raw('count(*) as count'))
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        $trackingPeriodExpression = $filter === 'last_year'
            ? "DATE_FORMAT(tracking.created_at, '%Y-%m')"
            : "DATE_FORMAT(tracking.created_at, '%Y-%m-%d')";

        $completionTrend = DB::table('tracking')
            ->join('shipment_invoice', 'tracking.shipper_id', '=', 'shipment_invoice.shipper_id')
            ->where('shipment_invoice.assigned_delivery_person', $admin->id)
            ->where('tracking.status', 'delivered')
            ->whereBetween('tracking.created_at', [$startDate, $endDate])
            ->select(DB::raw($trackingPeriodExpression . ' as period'), DB::raw('count(distinct tracking.shipper_id) as count'))
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period')
            ->toArray();

        return response()->json([
            'success' => true,
            'filter' => $filter,
            'statusCounts' => $statusCounts,
            'statusMap' => Tracking::getStatusTitleMap(),
            'assignmentTrend' => $assignmentTrend,
            'completionTrend' => $completionTrend,
        ]);
    }

    public function login()
    {
        // Send already-authenticated users to a destination they can access.
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            $landingRoute = $admin->canAccessDashboard()
                ? 'admin.dashboard'
                : ($admin->canAccessDeliveryDashboard() ? 'admin.delivery-dashboard' : 'admin.my-profile');

            return redirect()->route($landingRoute);
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

        // Look up the admin by email first (so we can give a clear inactive message)
        $admin = Admin::where('email', $request->email)->first();

        // Block login for deactivated users
        if ($admin && $admin->status != 1) {
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated. Please contact the Super Admin.')
                ->withInput($request->only('email'));
        }

        // Attempt to login with admin guard (also enforce status at credential level)
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1], $request->has('remember'))) {
            $authenticatedAdmin = Auth::guard('admin')->user();
            $landingRoute = $authenticatedAdmin->canAccessDashboard()
                ? 'admin.dashboard'
                : ($authenticatedAdmin->canAccessDeliveryDashboard() ? 'admin.delivery-dashboard' : 'admin.my-profile');

            \App\Support\SystemLogger::log(
                'admin.login',
                'Admin logged in: ' . $authenticatedAdmin->email,
                'admin'
            );

            return redirect()->route($landingRoute)->with('success', 'Login successful!');
        }

        // Login failed
        return redirect()->route('admin.login')->with('error', 'Invalid email or password');
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        \App\Support\SystemLogger::log(
            'admin.logout',
            'Admin logged out: ' . ($admin->email ?? 'unknown'),
            'admin'
        );

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }

    public function companies()
    {
        // Base query builder for shipments with all related data, filtered by shipper status
        $baseQuery = function ($status) {
            return DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->leftJoin('manifests', 'manifests.shipper_id', '=', 'shipper_info.id')
                ->leftJoin('admin_user', 'shipment_invoice.assigned_delivery_person', '=', 'admin_user.id')
                ->whereIn('shipper_info.status', (array) $status)
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
                    'shipper_info.status as shipper_status',
                    'shipper_info.company_name as shipper_company',
                    'shipper_info.contact_person as shipper_contact',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.pincode as shipper_pincode',
                    'shipper_info.awb_number',
                    'shipper_info.total_price as shipper_total_price',
                    'shipper_info.total_base_price as shipper_total_base_price',
                    'shipper_info.total_fuel_price as shipper_total_fuel_price',
                    'shipper_info.total_surcharge as shipper_total_surcharge',
                    'manifests.manifest_number',
                    'manifests.created_at as manifest_created_at',
                    'manifests.pickup_date',
                    'customers.id as customer_id',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.email as customer_email',
                    'customers.phone_number as customer_phone',
                    'consignee_info.consignee_name',
                    'consignee_info.contact_person as consignee_contact',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'consignee_info.zip_code as consignee_zip',
                    'consignee_info.email as consignee_email',
                    'consignee_info.phone_number as consignee_phone',
                    'consignee_info.delivery_destination as consignee_destination',
                    'admin_user.name as delivery_person_name'
                )
                ->orderBy('shipment_invoice.created_at', 'desc')
                ->get();
        };

        // Fetch shipments by status for each tab
        $manifestedShipments = $baseQuery('manifested');

        // Group manifested shipments by manifest number so each manifest shows
        // its code, order date, shipment count, total value and total cost.
        $manifestGroups = collect($manifestedShipments)
            ->filter(function ($s) {
                return ! empty($s->manifest_number);
            })
            ->groupBy('manifest_number')
            ->map(function ($shipments, $manifestNumber) {
                $first = $shipments->first();

                return (object) [
                    'manifest_number' => $manifestNumber,
                    'manifest_created_at' => $first->manifest_created_at ?? null,
                    'shipment_count' => $shipments->count(),
                    'total_value' => (float) $shipments->sum('shipper_total_price'),
                    'total_cost' => (float) $shipments->sum(function ($s) {
                        return (float) $s->shipper_total_base_price
                            + (float) $s->shipper_total_fuel_price
                            + (float) $s->shipper_total_surcharge;
                    }),
                    'shipments' => $shipments->map(function ($s) {
                        $customerName = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''));
                        $from = trim(($s->shipper_city ?? '-') . ', ' . ($s->shipper_state ?? '-'));
                        $to = trim(($s->consignee_city ?? '-') . ', ' . ($s->consignee_state ?? '-'));

                        return [
                            'awb_number' => $s->awb_number ?? 'N/A',
                            'invoice_number' => $s->invoice_number ?? 'N/A',
                            'customer_name' => $customerName ?: 'N/A',
                            'consignee_name' => $s->consignee_name ?: ($s->consignee_contact ?: 'N/A'),
                            'from' => $from,
                            'to' => $to,
                            'amount' => (float) ($s->shipper_total_price ?? 0),
                            'amount_formatted' => $s->shipper_total_price
                                ? number_format((float) $s->shipper_total_price, 2) . ' ' . ($s->invoice_currency ?? '')
                                : 'N/A',
                        ];
                    })->values()->all(),
                ];
            })
            ->sortByDesc('manifest_created_at')
            ->values();

        $readyForPickupShipments = $baseQuery('ready_for_pickup');
        $assignedForPickupShipments = $baseQuery('assigned_for_pickup');
        $printLabelShipments = $baseQuery(['dispatched', 'received']);
        $readyToDispatchShipments = $baseQuery('ready_to_dispatch');

        // Package Details column (draft-style) ke liye packages, shipper_id se grouped.
        // Print Label + Ready to Dispatch tabs ke shippers load hote hain, plus
        // Ready for Pickup + Assigned for Pickup tabs ke Total Weight column ke liye.
        $packagesByShipper = collect();
        $pkgShipperIds = $printLabelShipments->pluck('shipper_id')
            ->merge($readyToDispatchShipments->pluck('shipper_id'))
            ->merge($readyForPickupShipments->pluck('shipper_id'))
            ->merge($assignedForPickupShipments->pluck('shipper_id'))
            ->filter()->unique()->values()->all();
        if (! empty($pkgShipperIds)) {
            $packagesByShipper = \App\Models\PackageDimension::whereIn('shipper_id', $pkgShipperIds)
                ->orderBy('id')
                ->get()
                ->groupBy('shipper_id');
        }

        // Manifest ke saare shippers ke saare packages ka total chargeable weight.
        $manifestTotalWeight = function ($shipments) use ($packagesByShipper): float {
            $total = 0.0;
            foreach ($shipments as $s) {
                foreach ($packagesByShipper->get($s->shipper_id, collect()) as $pkg) {
                    if ($pkg->chargeable_weight !== null && $pkg->chargeable_weight !== '') {
                        $total += (float) $pkg->chargeable_weight;
                    }
                }
            }

            return round($total, 2);
        };

        // Group ready-for-pickup shipments by manifest number so the tab shows
        // one row per manifest (same as the Manifested tab). Each child keeps
        // the fields needed by the Assign Pickup modal.
        $readyForPickupManifestGroups = collect($readyForPickupShipments)
            ->filter(function ($s) {
                return ! empty($s->manifest_number);
            })
            ->groupBy('manifest_number')
            ->map(function ($shipments, $manifestNumber) use ($manifestTotalWeight, $packagesByShipper) {
                $first = $shipments->first();

                return (object) [
                    'manifest_number' => $manifestNumber,
                    'manifest_created_at' => $first->manifest_created_at ?? null,
                    'pickup_date' => $first->pickup_date ?? null,
                    'shipment_count' => $shipments->count(),
                    'total_value' => (float) $shipments->sum('shipper_total_price'),
                    'total_weight' => $manifestTotalWeight($shipments),
                    'total_cost' => (float) $shipments->sum(function ($s) {
                        return (float) $s->shipper_total_base_price
                            + (float) $s->shipper_total_fuel_price
                            + (float) $s->shipper_total_surcharge;
                    }),
                    'shipments' => $shipments->map(function ($s) use ($packagesByShipper) {
                        $customerName = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''));
                        $from = trim(($s->shipper_city ?? '-') . ', ' . ($s->shipper_state ?? '-'));
                        $to = trim(($s->consignee_city ?? '-') . ', ' . ($s->consignee_state ?? '-'));
                        $weight = 0.0;
                        foreach ($packagesByShipper->get($s->shipper_id, collect()) as $pkg) {
                            if ($pkg->chargeable_weight !== null && $pkg->chargeable_weight !== '') {
                                $weight += (float) $pkg->chargeable_weight;
                            }
                        }

                        return [
                            'id' => $s->id,
                            'delivery_type' => $s->delivery_type,
                            'assigned_delivery_person' => $s->assigned_delivery_person,
                            'awb_number' => $s->awb_number ?? 'N/A',
                            'pickup_date' => $s->pickup_date,
                            'invoice_number' => $s->invoice_number ?? 'N/A',
                            'customer_name' => $customerName ?: 'N/A',
                            'consignee_name' => $s->consignee_name ?: ($s->consignee_contact ?: 'N/A'),
                            'from' => $from,
                            'to' => $to,
                            'amount' => (float) ($s->shipper_total_price ?? 0),
                            'amount_formatted' => $s->shipper_total_price
                                ? number_format((float) $s->shipper_total_price, 2) . ' ' . ($s->invoice_currency ?? '')
                                : 'N/A',
                            'weight' => round($weight, 2),
                        ];
                    })->values()->all(),
                ];
            })
            ->sortByDesc('manifest_created_at')
            ->values();

        // Group assigned-for-pickup shipments by manifest number so the tab shows
        // one row per manifest (same as the Ready for Pickup tab). Each child
        // keeps the fields needed by the Receive Shipment modal. Shipments
        // without a manifest number fall back to single-shipment groups so no
        // row is ever lost.
        $assignedForPickupManifestGroups = collect($assignedForPickupShipments)
            ->groupBy(function ($s) {
                return ! empty($s->manifest_number) ? $s->manifest_number : 'SINGLE-'.$s->id;
            })
            ->map(function ($shipments, $manifestNumber) use ($manifestTotalWeight, $packagesByShipper) {
                $first = $shipments->first();

                return (object) [
                    'manifest_number' => $first->manifest_number ?? null,
                    'manifest_created_at' => $first->manifest_created_at ?? null,
                    'pickup_date' => $first->pickup_date ?? null,
                    'shipment_count' => $shipments->count(),
                    'total_value' => (float) $shipments->sum('shipper_total_price'),
                    'total_weight' => $manifestTotalWeight($shipments),
                    'total_cost' => (float) $shipments->sum(function ($s) {
                        return (float) $s->shipper_total_base_price
                            + (float) $s->shipper_total_fuel_price
                            + (float) $s->shipper_total_surcharge;
                    }),
                    'shipments' => $shipments->map(function ($s) use ($packagesByShipper) {
                        $customerName = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''));
                        $from = trim(($s->shipper_city ?? '-') . ', ' . ($s->shipper_state ?? '-'));
                        $to = trim(($s->consignee_city ?? '-') . ', ' . ($s->consignee_state ?? '-'));
                        $weight = 0.0;
                        foreach ($packagesByShipper->get($s->shipper_id, collect()) as $pkg) {
                            if ($pkg->chargeable_weight !== null && $pkg->chargeable_weight !== '') {
                                $weight += (float) $pkg->chargeable_weight;
                            }
                        }

                        return [
                            'id' => $s->id,
                            'delivery_type' => $s->delivery_type,
                            'assigned_delivery_person' => $s->assigned_delivery_person,
                            'awb_number' => $s->awb_number ?? 'N/A',
                            'pickup_date' => $s->pickup_date,
                            'invoice_number' => $s->invoice_number ?? 'N/A',
                            'customer_name' => $customerName ?: 'N/A',
                            'consignee_name' => $s->consignee_name ?: ($s->consignee_contact ?: 'N/A'),
                            'from' => $from,
                            'to' => $to,
                            'amount' => (float) ($s->shipper_total_price ?? 0),
                            'amount_formatted' => $s->shipper_total_price
                                ? number_format((float) $s->shipper_total_price, 2) . ' ' . ($s->invoice_currency ?? '')
                                : 'N/A',
                            'weight' => round($weight, 2),
                        ];
                    })->values()->all(),
                ];
            })
            ->sortByDesc('manifest_created_at')
            ->values();

        // Fetch delivery persons where type = 'Delivery_person'
        $deliveryPersons = Admin::where('type', 'Delivery_person')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'mobile']);

        return view('admin.companies', compact('manifestGroups', 'manifestedShipments', 'readyForPickupManifestGroups', 'readyForPickupShipments', 'assignedForPickupShipments', 'assignedForPickupManifestGroups', 'printLabelShipments', 'readyToDispatchShipments', 'deliveryPersons', 'packagesByShipper'));
    }

    /**
     * Assign delivery type and/or delivery person to a shipment.
     * When DDU (Delhivery) is selected, also calls the Delhivery API to create a pickup.
     */
    public function assignDelivery(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'delivery_type' => 'required|string|in:DDU,DDP,Self',
                'delivery_person_id' => 'nullable|integer|exists:admin_user,id',
            ]);

            // If Self is selected, assign delivery person; otherwise set to null
            if ($request->delivery_type === 'Self' && !$request->delivery_person_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a delivery person for Self delivery type.'
                ]);
            }

            $result = $this->applyPickupAssignment(
                (int) $request->shipment_id,
                $request->delivery_type,
                $request->delivery_person_id ? (int) $request->delivery_person_id : null
            );

            $this->notifyDeliveryPersonIfChanged(
                $request->delivery_person_id ? (int) $request->delivery_person_id : null,
                $result['previous_delivery_person_id'],
                $result['shipment_invoice'],
                $result['shipper']
            );

            // If DDU (Delhivery) is selected, call the Delhivery API
            $delhiveryResponse = null;
            if ($request->delivery_type === 'DDU') {
                $delhiveryResponse = $this->callDelhiveryApi((int) $request->shipment_id);
            }

            $response = [
                'success' => true,
                'message' => 'Delivery assignment saved successfully.'
            ];

            // Include Delhivery API response if applicable
            if ($delhiveryResponse !== null) {
                $response['delhivery'] = $delhiveryResponse;
                if (!$delhiveryResponse['success']) {
                    $response['message'] = 'Delivery assignment saved, but Delhivery API call failed: ' . $delhiveryResponse['message'];
                } else {
                    $response['message'] = 'Delivery assignment saved and Delhivery pickup created successfully.';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Assign a whole manifest for pickup in one go.
     *
     * All shipments of the manifest that are still in "ready_for_pickup"
     * move to "assigned_for_pickup" together. For DDU, Delhivery gets ONE
     * bulk request with all shipments instead of one call per shipment.
     */
    public function assignBulkDelivery(Request $request)
    {
        try {
            $request->validate([
                'manifest_number' => 'required|string|exists:manifests,manifest_number',
                'delivery_type' => 'required|string|in:DDU,DDP,Self',
                'delivery_person_id' => 'nullable|integer|exists:admin_user,id',
            ]);

            $manifestNumber = trim((string) $request->manifest_number);

            if ($request->delivery_type === 'Self' && !$request->delivery_person_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a delivery person for Self delivery type.'
                ]);
            }

            // All invoice ids of this manifest whose shipper is still ready for pickup.
            $shipmentIds = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->join('manifests', 'manifests.shipper_id', '=', 'shipper_info.id')
                ->where('manifests.manifest_number', $manifestNumber)
                ->where('shipper_info.status', 'ready_for_pickup')
                ->distinct()
                ->pluck('shipment_invoice.id')
                ->map(fn($id) => (int) $id)
                ->all();

            if (empty($shipmentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Is manifest me koi Ready for Pickup shipment nahi mili (shayad sab already assigned hain).'
                ]);
            }

            $deliveryPersonId = $request->delivery_person_id ? (int) $request->delivery_person_id : null;
            $assigned = [];
            DB::transaction(function () use ($shipmentIds, $request, $deliveryPersonId, &$assigned) {
                foreach ($shipmentIds as $sid) {
                    $assigned[] = $this->applyPickupAssignment($sid, $request->delivery_type, $deliveryPersonId)
                        + ['shipment_id' => $sid];
                }
            });

            // Self: ONE consolidated notification to the pickup person
            // (instead of one notification per shipment).
            if ($request->delivery_type === 'Self' && $deliveryPersonId) {
                $deliveryPerson = Admin::where('id', $deliveryPersonId)
                    ->where('type', 'Delivery_person')
                    ->where('status', 1)
                    ->first();

                if ($deliveryPerson) {
                    $first = $assigned[0];
                    $deliveryPerson->notify(new DeliveryAssignedNotification(
                        shipmentInvoiceId: (int) $first['shipment_id'],
                        shipperId: $first['shipment_invoice']?->shipper_id,
                        awbNumber: $first['shipper']?->awb_number,
                        invoiceNumber: $first['shipment_invoice']?->invoice_number,
                        shipperCompany: $first['shipper']?->company_name,
                        destination: null,
                        assignedBy: Auth::guard('admin')->user()?->name,
                        manifestNumber: $manifestNumber,
                        shipmentCount: count($assigned),
                    ));
                }
            }

            // DDU: ONE bulk Delhivery call for all shipments of the manifest.
            $delhiveryResponse = null;
            if ($request->delivery_type === 'DDU') {
                $delhiveryResponse = $this->callDelhiveryBulkApi(
                    array_map(fn($a) => (int) $a['shipment_id'], $assigned)
                );
            }

            $count = count($assigned);
            $response = [
                'success' => true,
                'message' => $count . ' shipment(s) assigned for pickup successfully (Manifest ' . $manifestNumber . ').',
                'manifest_number' => $manifestNumber,
                'assigned_count' => $count,
            ];

            if ($delhiveryResponse !== null) {
                $response['delhivery'] = $delhiveryResponse;
                if (!$delhiveryResponse['success']) {
                    $response['message'] = $count . ' shipment(s) assigned, but Delhivery API call failed: ' . $delhiveryResponse['message'];
                } else {
                    $response['message'] = $count . ' shipment(s) assigned and Delhivery pickup created successfully (Manifest ' . $manifestNumber . ').';
                }
                if (!empty($delhiveryResponse['failed_orders'])) {
                    $response['failed'] = $delhiveryResponse['failed_orders'];
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Bulk assign for pickup failed for manifest ' . ($request->manifest_number ?? '-') . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Shared per-shipment DB work for pickup assignment (single + bulk flows).
     *
     * Updates the invoice row, moves the shipper to "assigned_for_pickup"
     * (unless already past pickup) and writes the tracking record.
     *
     * @return array{shipment_invoice: \App\Models\ShipmentInvoice|null, shipper: \App\Models\ShipperInfo|null, previous_delivery_person_id: mixed}
     */
    private function applyPickupAssignment($shipmentId, $deliveryType, $deliveryPersonId)
    {
        $shipmentBeforeUpdate = ShipmentInvoice::findOrFail($shipmentId);
        $previousDeliveryPersonId = $shipmentBeforeUpdate->assigned_delivery_person;

        $updateData = [
            'delivery_type' => $deliveryType,
            'assigned_delivery_person' => $deliveryType === 'Self' ? $deliveryPersonId : null,
        ];

        ShipmentInvoice::where('id', $shipmentId)->update($updateData);

        // Create tracking record for pickup assignment and update shipper status
        $shipmentInvoice = ShipmentInvoice::find($shipmentId);
        $shipper = null;
        if ($shipmentInvoice && $shipmentInvoice->shipper_id) {
            $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
            if ($shipper) {
                // The tracking record needs an AWB, so it is only created
                // once the AWB exists. The pickup assignment itself always
                // moves the shipment to "Assigned for Pickup" so customers
                // see it under "In-Transit to Hub" right away.
                if ($shipper->awb_number) {
                    $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();
                    \App\Models\Tracking::create([
                        'awb_number' => $shipper->awb_number,
                        'status'     => 'assigned_for_pickup',
                        'title'      => 'Assigned for Pickup',
                        'shipper_id' => $shipper->id,
                        'shipping_id' => $createShipment ? $createShipment->id : null,
                        'uwc_id'     => $shipper->awb_number,
                    ]);
                }
                // Never downgrade shipments that already moved past pickup
                // (e.g. reassigning a delivered order must not reset it).
                $pastPickupStatuses = ['received', 'confirm_pickup', 'ready_to_dispatch', 'dispatched', 'delivered', 'cancelled', 'disputed'];
                if (!in_array($shipper->status, $pastPickupStatuses, true)) {
                    $shipper->status = 'assigned_for_pickup';
                    $shipper->save();
                }
            }
        }

        return [
            'shipment_invoice' => $shipmentInvoice,
            'shipper' => $shipper,
            'previous_delivery_person_id' => $previousDeliveryPersonId,
        ];
    }

    /**
     * Notify the pickup person when a single assignment changes the person.
     */
    private function notifyDeliveryPersonIfChanged($newDeliveryPersonId, $previousDeliveryPersonId, $shipmentInvoice, $shipper)
    {
        if (!$newDeliveryPersonId || (string) $previousDeliveryPersonId === (string) $newDeliveryPersonId) {
            return;
        }

        $deliveryPerson = Admin::where('id', $newDeliveryPersonId)
            ->where('type', 'Delivery_person')
            ->where('status', 1)
            ->first();

        if (!$deliveryPerson) {
            return;
        }

        $deliveryPerson->notify(new DeliveryAssignedNotification(
            shipmentInvoiceId: (int) ($shipmentInvoice?->id ?? 0),
            shipperId: $shipmentInvoice?->shipper_id,
            awbNumber: $shipper?->awb_number,
            invoiceNumber: $shipmentInvoice?->invoice_number,
            shipperCompany: $shipper?->company_name,
            destination: null,
            assignedBy: Auth::guard('admin')->user()?->name
        ));
    }

    /**
     * Return recent notifications for the authenticated admin user.
     */
    public function notificationsData()
    {
        $admin = Auth::guard('admin')->user();
        $notifications = $admin->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'url' => $data['url'] ?? route('admin.notifications'),
                    'read' => !is_null($notification->read_at),
                    'created_at' => optional($notification->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'unread_count' => $admin->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationRead(string $id)
    {
        $admin = Auth::guard('admin')->user();
        $notification = $admin->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        Auth::guard('admin')->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Permanently clear all notifications of the authenticated admin user.
     */
    public function clearNotifications()
    {
        Auth::guard('admin')->user()->notifications()->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Full notifications listing page for the authenticated admin user.
     */
    public function notifications()
    {
        $admin = Auth::guard('admin')->user();
        $notifications = $admin->notifications()->latest()->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Receive a shipment - mark as received in tracking table.
     * When "Yes" is selected, creates a tracking record with status "received"
     * and updates shipper status to "received" (moves shipment to Print Label tab).
     * When "No" is selected, creates a tracking record with status "on_hold"
     * and updates shipper status to "on_hold".
     */
    public function receiveShipment(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'received'    => 'required|string|in:yes,no',
            ]);

            $result = $this->applyReceiveShipment((int) $request->shipment_id, $request->received);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Receive a whole manifest in one go.
     *
     * All shipments of the manifest that are still in "assigned_for_pickup"
     * move to "received" (Yes) or "on_hold" (No) together.
     */
    public function receiveBulkShipment(Request $request)
    {
        try {
            $request->validate([
                'manifest_number' => 'required|string|exists:manifests,manifest_number',
                'received'        => 'required|string|in:yes,no',
            ]);

            $manifestNumber = trim((string) $request->manifest_number);

            // All invoice ids of this manifest whose shipper is still assigned for pickup.
            $shipmentIds = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->join('manifests', 'manifests.shipper_id', '=', 'shipper_info.id')
                ->where('manifests.manifest_number', $manifestNumber)
                ->where('shipper_info.status', 'assigned_for_pickup')
                ->distinct()
                ->pluck('shipment_invoice.id')
                ->map(fn($id) => (int) $id)
                ->all();

            if (empty($shipmentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Is manifest me koi Assigned for Pickup shipment nahi mili (shayad sab already received/hold hain).'
                ]);
            }

            $received = $request->received;
            $done = 0;
            $skipped = 0;
            $cmsFailed = 0;
            DB::transaction(function () use ($shipmentIds, $received, &$done, &$skipped, &$cmsFailed) {
                foreach ($shipmentIds as $sid) {
                    $result = $this->applyReceiveShipment($sid, $received);
                    if ($result['success']) {
                        $done++;
                        if ($received === 'yes' && array_key_exists('cms_success', $result) && $result['cms_success'] === false) {
                            $cmsFailed++;
                        }
                    } else {
                        $skipped++;
                    }
                }
            });

            if ($done === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koi shipment update nahi ho payi (shipper/AWB data missing).'
                ]);
            }

            $message = $received === 'yes'
                ? $done . ' shipment(s) received successfully, Print Label tab me chali gayi hain (Manifest ' . $manifestNumber . ').'
                : $done . ' shipment(s) on hold mark ho gayi hain (Manifest ' . $manifestNumber . ').';
            if ($skipped > 0) {
                $message .= ' ' . $skipped . ' skipped (data missing).';
            }
            if ($cmsFailed > 0) {
                $message .= ' ' . $cmsFailed . ' me CMS sync fail hua (shipment received hai, retry ho sakta hai).';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'manifest_number' => $manifestNumber,
                'received_count' => $done,
                'skipped_count' => $skipped,
                'cms_failed_count' => $cmsFailed,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk receive failed for manifest ' . ($request->manifest_number ?? '-') . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Shared per-shipment receive work (single + bulk flows).
     *
     * Yes → tracking "received" + shipper "received" (Print Label tab) + CMS
     *       order_create (non-blocking; CMS failure does not roll back receive).
     * No  → tracking "on_hold"   + shipper "on_hold" (no CMS call).
     *
     * @return array{success: bool, message: string, cms_success?: bool|null, cms_message?: string|null}
     */
    private function applyReceiveShipment($shipmentId, $received)
    {
        $shipmentInvoice = ShipmentInvoice::find($shipmentId);
        if (!$shipmentInvoice || !$shipmentInvoice->shipper_id) {
            return [
                'success' => false,
                'message' => 'Shipment not found or no shipper associated.'
            ];
        }

        $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
        if (!$shipper || !$shipper->awb_number) {
            return [
                'success' => false,
                'message' => 'Shipper info not found or AWB number missing.'
            ];
        }

        $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();

        if ($received === 'yes') {
            // Mark as received in tracking and set shipper status to 'received'
            // so the shipment moves to the Print Label tab
            \App\Models\Tracking::create([
                'awb_number'  => $shipper->awb_number,
                'status'      => 'received',
                'title'       => 'Shipment Received',
                'shipper_id'  => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id'      => $shipper->awb_number,
            ]);
            $shipper->status = 'received';
            $shipper->save();

            // CMS (Adomantra order_create) now fires here instead of at
            // create-shipment/create-order time. Non-blocking: a CMS failure
            // must not move the shipment back — it stays received and the
            // failure is only logged + surfaced as a warning.
            $cms = $this->syncCmsOrderForShipper($shipper);

            $message = 'Shipment received successfully. It has been moved to Print Label tab.';
            if ($cms['skipped']) {
                $message .= ' (CMS order was already synced earlier.)';
            } elseif ($cms['success'] === false) {
                $message .= ' (Warning: CMS order sync failed — '.$cms['message'].')';
            } else {
                $message .= ' (CMS order synced.)';
            }

            return [
                'success' => true,
                'message' => $message,
                'cms_success' => $cms['success'],
                'cms_message' => $cms['message'],
            ];
        }

        // Mark as on hold (not received)
        \App\Models\Tracking::create([
            'awb_number'  => $shipper->awb_number,
            'status'      => 'on_hold',
            'title'       => 'Shipment On Hold',
            'shipper_id'  => $shipper->id,
            'shipping_id' => $createShipment ? $createShipment->id : null,
            'uwc_id'      => $shipper->awb_number,
        ]);
        $shipper->status = 'on_hold';
        $shipper->save();

        return [
            'success' => true,
            'message' => 'Shipment marked as on hold (not received).'
        ];
    }

    /**
     * Fire the CMS (Adomantra order_create) call for a received shipment.
     *
     * Non-blocking by design: the shipment is already physically received, so
     * a CMS failure must never roll the status back. Idempotent: if a CMS
     * sync was already logged for this shipper, the call is skipped.
     *
     * @return array{success: bool|null, skipped: bool, message: string|null}
     */
    private function syncCmsOrderForShipper(ShipperInfo $shipper): array
    {
        if ($this->isCmsOrderAlreadySynced((int) $shipper->id)) {
            return ['success' => true, 'skipped' => true, 'message' => 'CMS order already synced.'];
        }

        try {
            $payload = $this->buildAdomantraOrderPayloadFromShipment($shipper);

            Log::info('Adomantra receive-time order payload generated.', [
                'shipper_id' => $shipper->id,
                'awb_number' => $shipper->awb_number,
                'payload' => $payload,
            ]);

            $response = app(AdomantraApiClient::class)->createOrder($payload);

            Log::info('Adomantra receive-time order created.', [
                'shipper_id' => $shipper->id,
                'awb_number' => $shipper->awb_number,
            ]);

            ShipmentLog::logStatus(
                (int) $shipper->id,
                (string) $shipper->awb_number,
                'received',
                'assigned_for_pickup',
                'CMS order synced via Adomantra order_create.',
                $shipper->customer_id,
                'admin'
            );

            return ['success' => true, 'skipped' => false, 'message' => 'CMS order synced.'];
        } catch (\Throwable $e) {
            Log::error('Adomantra receive-time order failed.', [
                'shipper_id' => $shipper->id,
                'awb_number' => $shipper->awb_number,
                'exception' => $e->getMessage(),
            ]);

            return ['success' => false, 'skipped' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Check whether the CMS order was already synced for this shipper.
     */
    private function isCmsOrderAlreadySynced(int $shipperId): bool
    {
        return ShipmentLog::where('shipper_id', $shipperId)
            ->where(function ($query) {
                $query->where('description', 'like', '%CMS order synced%')
                    ->orWhere('description', 'like', '%Adomantra order%');
            })
            ->exists();
    }

    /**
     * Build the exact nested request contract expected by Adomantra
     * order_create, sourced from the stored shipment rows (not request input).
     */
    private function buildAdomantraOrderPayloadFromShipment(ShipperInfo $shipper): array
    {
        $shipper->loadMissing(['consigneeInfo', 'packageDimensions', 'invoices.invoiceItems', 'csbInformation', 'serviceRate.service']);

        $consignee = $shipper->consigneeInfo;
        $packages = $shipper->packageDimensions ?? collect();
        $invoice = $shipper->invoices->sortByDesc('id')->first();
        $items = $invoice ? $invoice->invoiceItems : collect();
        $csb = $shipper->csbInformation;
        $createShipment = CreateShipment::where('shipper_id', $shipper->id)->first();

        $rate = $shipper->serviceRate;
        $service = $rate?->service;
        if (! $service && ! empty($shipper->service_id)) {
            $service = CourierService::find($shipper->service_id);
        }

        $customer = null;
        if (! empty($shipper->customer_id)) {
            $customer = Customer::find($shipper->customer_id);
        }
        if (! $customer && $createShipment?->customer_id) {
            $customer = Customer::find($createShipment->customer_id);
        }

        $baseAmount = (float) ($rate?->price ?? 0);
        $fuelPercentage = (float) ($rate?->fuel_percentage ?? 0);
        $fuel = (float) ($rate?->fuel_charge ?? 0);
        if ($fuel <= 0) {
            $fuel = $baseAmount * $fuelPercentage / 100;
        }
        $gstPercentage = (float) ($rate?->gst_percentage ?? 0);
        $gst = (float) ($rate?->gst_amount ?? 0);
        $surcharge = (float) ($rate?->surcharge_amount ?? 0);
        if ($gst <= 0) {
            $gst = ($baseAmount + $fuel + $surcharge) * $gstPercentage / 100;
        }

        $oversizeCharge = (float) ($createShipment?->oversize_charge ?? 0);
        $handlingCharge = (float) ($createShipment?->handling_charge ?? 0);
        $miscellaneous = $oversizeCharge + $handlingCharge;

        $destination = (string) ($consignee?->delivery_destination ?? $createShipment?->delivery_destination ?? '');
        $destinationRecord = $destination !== '' ? Destination::where('name', $destination)->first() : null;
        $countryCode = strtoupper((string) ($destinationRecord?->country_code ?? ''));
        $originType = (string) ($consignee?->origin_type ?? $createShipment?->origin_type ?? 'CSB IV');
        $csbType = strtoupper($originType) === 'CSB V' ? 'CSB 5' : 'CSB 4';

        $customerName = $customer
            ? trim((string) ($customer->first_name.' '.$customer->last_name))
            : trim((string) ($shipper->company_name ?: $shipper->contact_person ?: 'Customer'));
        $accountCode = $customer
            ? (string) ($customer->customer_code ?: 'UWC'.str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT))
            : 'UWC000000';

        $awbNumber = (string) $shipper->awb_number;

        $productDetails = $items->map(function ($item): array {
            return [
                'BoxNo' => (string) ($item->box_no ?? ''),
                'Description' => (string) ($item->description ?? ''),
                'HSNCode' => (string) ($item->hs_code ?? ''),
                'HTSCode' => (string) ($item->hts_code ?? ''),
                'UnitType' => (string) ($item->unit_type ?? 'PCS'),
                'Qty' => (float) ($item->qty ?? 0),
                'UnitRate' => (float) ($item->unit_rate ?? 0),
                'ShipPieceIGST' => (float) ($item->igst_amount ?? 0),
                'PieceWt' => 0,
            ];
        })->values()->all();

        $packageDetails = $packages->map(function ($package): array {
            return [
                'Length' => (float) ($package->length_cm ?? 0),
                'Width' => (float) ($package->width_cm ?? 0),
                'Height' => (float) ($package->height_cm ?? 0),
                'ActualWeight' => (float) ($package->actual_weight_kg ?? 0),
            ];
        })->values()->all();

        $invoiceDate = 'now';
        if ($invoice?->invoice_date) {
            $invoiceDate = $invoice->invoice_date instanceof \DateTimeInterface
                ? $invoice->invoice_date->format('Y-m-d')
                : (string) $invoice->invoice_date;
        } elseif ($createShipment?->invoice_date) {
            $invoiceDate = $createShipment->invoice_date instanceof \DateTimeInterface
                ? $createShipment->invoice_date->format('Y-m-d')
                : (string) $createShipment->invoice_date;
        }

        return [
            'Awbno' => $awbNumber,
            'AccountCode' => $accountCode,
            'AccountName' => $customerName,
            'Origin' => 'DEL',
            'PaymentType' => 'Credit',
            'ShipDate' => now()->format('Y-m-d\\TH:i:s'),
            'Sender' => [
                'SenderName' => (string) ($shipper->company_name ?: $customerName),
                'SenderContactPerson' => (string) ($shipper->contact_person ?: $customerName),
                'SenderAddressLine1' => (string) ($shipper->address_line1 ?? ''),
                'SenderAddressLine2' => (string) ($shipper->address_line2 ?? ''),
                'SenderAddressLine3' => (string) ($shipper->address_line3 ?? ''),
                'SenderPincode' => (string) ($shipper->pincode ?? ''),
                'SenderCity' => (string) ($shipper->city ?? ''),
                'SenderState' => (string) ($shipper->state ?? ''),
                'SenderTelephone' => (string) ($shipper->phone_number ?? ''),
                'SenderEmailId' => (string) ($shipper->email ?? ''),
                'KYCType' => (string) ($shipper->kyc_type ?? ''),
                'KYCNo' => (string) ($shipper->kyc_number ?? ''),
            ],
            'Receiver' => [
                'ReceiverName' => (string) ($consignee?->consignee_name ?? ''),
                'ReceiverContactPerson' => (string) ($consignee?->contact_person ?? ''),
                'ReceiverAddressLine1' => (string) ($consignee?->address_line1 ?? ''),
                'ReceiverAddressLine2' => (string) ($consignee?->address_line2 ?? ''),
                'ReceiverAddressLine3' => (string) ($consignee?->address_line3 ?? ''),
                'ReceiverZipcode' => (string) ($consignee?->zip_code ?? ''),
                'ReceiverCity' => (string) ($consignee?->city ?? ''),
                'ReceiverState' => (string) ($consignee?->state ?? ''),
                'ReceiverCountry' => $countryCode !== '' ? $countryCode : $destination,
                'ReceiverTelephone' => (string) ($consignee?->phone_number ?? ''),
                'ReceiverEmailid' => (string) ($consignee?->email ?? ''),
                'VatId' => '',
            ],
            'ServiceDetails' => [
                'ServiceCode' => (string) ($service?->service_code ?? $service?->scode ?? ''),
                'ServiceName' => (string) ($service?->method ?? $shipper->shipping_method ?? ''),
                'Forwarder' => (string) ($service?->shipper_code ?? $service?->network ?? ''),
                'NetworkCode' => (string) ($service?->network ?? ''),
                'NetworkName' => (string) ($service?->description ?? $service?->network ?? ''),
                'NetworkNo' => (string) ($service?->method_code ?? ''),
                'GoodsType' => 'NDOX',
                'PackageType' => 'PACKAGE',
            ],
            'PackageDetails' => ['PackageDetail' => $packageDetails],
            'AdditionalDetails' => [
                'IsThirdParty' => false,
                'ProductDetails' => $productDetails,
                'InvoiceCurrency' => (string) ($invoice?->invoice_currency ?? $createShipment?->invoice_currency ?? ''),
                'InvoiceNo' => (string) ($invoice?->invoice_number ?? $createShipment?->invoice_number ?? ''),
                'InvoiceDate' => date('Y-m-d\\T00:00:00', strtotime((string) $invoiceDate)),
                'TermsOfSale' => (string) ($invoice?->incoterms ?? $createShipment?->incoterms ?? ''),
                'ReasonForExport' => 'Sale',
                'FreightCharge' => round($baseAmount, 2),
                'InsuranceCharge' => 0,
                'CSB_Type' => $csbType,
                'CustomerRefNo' => (string) ($invoice?->reference_number ?? $createShipment?->reference_number ?? ''),
                'DeliveryConfirmation' => '',
                'DutyTax' => '',
                'DutiesAccountNo' => '',
                'TransactionId' => $awbNumber,
                'IECNo' => (string) ($csb?->iec_code ?? $createShipment?->iec_code ?? ''),
                'ADCode' => (string) ($csb?->ad_code ?? $createShipment?->ad_code ?? ''),
                'BankType' => '',
                'NFEI' => false,
                'Ecom' => strtoupper((string) ($csb?->ecommerce ?? $createShipment?->ecommerce ?? 'No')) === 'YES',
                'MEIS' => false,
                'BankAccount' => (string) ($csb?->bank_account_number ?? $createShipment?->bank_account_number ?? ''),
                'ProductType' => 'Commercial',
                'BoundUT' => (string) ($csb?->bond_ut_igst ?? $createShipment?->bond_ut_igst ?? ''),
                'IGSTAmount' => round((float) $items->sum('igst_amount'), 2),
                'IGSTPaid' => strtoupper((string) ($csb?->bond_ut_igst ?? $createShipment?->bond_ut_igst ?? '')) === 'IGST' ? 'Yes' : 'No',
                'ShipperImage' => '',
                'ShipperKYC' => '',
                'FileName' => '',
            ],
            'FreightDetails' => [
                'BasicAmount' => round($baseAmount, 2),
                'FuelPercentage' => round($fuelPercentage, 2),
                'Fuel' => round($fuel, 2),
                'MisFuel' => 0,
                'Misc' => round($miscellaneous, 2),
                'Demand' => 0,
                'GreenSuch' => 0,
                'Taxable' => round($baseAmount + $fuel + $surcharge + $miscellaneous, 2),
                'SGST' => 0,
                'CGST' => 0,
                'IGST' => round($gst, 2),
                'NTaxable' => 0,
                'NetTotal' => round($baseAmount + $fuel + $gst + $surcharge + $miscellaneous, 2),
            ],
            'MiscDetailsTable' => array_values(array_filter([
                $oversizeCharge > 0 ? ['MiscCode' => 'OVERSIZE', 'MiscName' => 'Oversize Charge', 'MisAmt' => round($oversizeCharge, 2), 'MisNTax' => 0, 'MisFuel' => 0] : null,
                $handlingCharge > 0 ? ['MiscCode' => 'HANDLING', 'MiscName' => 'Handling Charge', 'MisAmt' => round($handlingCharge, 2), 'MisNTax' => 0, 'MisFuel' => 0] : null,
            ])),
        ];
    }

    /**
     * Mark a shipment as Ready to Dispatch.
     * Creates a tracking record with status 'ready_to_dispatch' and updates
     * the shipper status so the shipment moves to the Ready to Dispatch tab.
     */
    public function readyToDispatch(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
            ]);

            $shipmentInvoice = ShipmentInvoice::find($request->shipment_id);
            if (!$shipmentInvoice || !$shipmentInvoice->shipper_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or no shipper associated.'
                ]);
            }

            $shipper = \App\Models\ShipperInfo::find($shipmentInvoice->shipper_id);
            if (!$shipper || !$shipper->awb_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipper info not found or AWB number missing.'
                ]);
            }

            $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();

            // Create tracking record for ready to dispatch
            \App\Models\Tracking::create([
                'awb_number'  => $shipper->awb_number,
                'status'      => 'ready_to_dispatch',
                'title'       => 'Ready to Dispatch',
                'shipper_id'  => $shipper->id,
                'shipping_id' => $createShipment ? $createShipment->id : null,
                'uwc_id'      => $shipper->awb_number,
            ]);

            // Update shipper status so it moves to "Ready to Dispatch" tab
            $shipper->status = 'ready_to_dispatch';
            $shipper->save();

            return response()->json([
                'success' => true,
                'message' => 'Shipment marked as Ready to Dispatch successfully. It has been moved to the Ready to Dispatch tab.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Dispute charges dropdown list for the admin/companies Dispute popup.
     * shipment_id mile to us shipment ke destination + shipping method ke
     * hisaab se filter karke bhejta hai (USA-only rows CA shipment me nahi
     * dikhengi). place_of_apply mile (e.g. Ready to Dispatch se 'After
     * Dispatched') to sirf us stage ke rules; warna dono stages ke rules.
     */
    public function disputeChargesList(Request $request)
    {
        try {
            $places = ['weighing at first scan', 'After Dispatched'];
            if ($request->filled('place_of_apply') && in_array($request->place_of_apply, $places, true)) {
                $places = [$request->place_of_apply];
            }
            $charges = \App\Models\DisputeCharge::whereIn('place_of_apply', $places)
                ->where('status', 1)
                ->orderBy('id')
                ->get([
                    'id',
                    'additional_charges',
                    'conditions',
                    'destination',
                    'service_id',
                    'calculation_type',
                    'values',
                    'gst_percentage',
                    'place_of_apply',
                ]);

            if ($request->filled('shipment_id')) {
                $invoice = \App\Models\ShipmentInvoice::find($request->shipment_id);
                $shipper = $invoice ? \App\Models\ShipperInfo::find($invoice->shipper_id) : null;
                if ($shipper) {
                    $consignee = \App\Models\ConsigneeInfo::where('shipper_id', $shipper->id)->first();
                    $country = app(\App\Http\Controllers\CustomerController::class)
                        ->resolveDestinationCountry($consignee->delivery_destination ?? '');
                    $destIn = strtoupper(trim((string) ($country ?? '')));
                    if ($destIn === '') {
                        $destIn = 'ALL';
                    }
                    $service = (string) ($shipper->shipping_method ?? '');
                    if (! empty($shipper->service_id)) {
                        $courierService = \App\Models\CourierService::find($shipper->service_id);
                        if ($courierService) {
                            // Method + network + api_provider teeno jodo taaki
                            // carrier-specific rows (UPS/DPD) match ho sakein.
                            $service .= ' ' . (string) ($courierService->method ?? '')
                                . ' ' . (string) ($courierService->network ?? '')
                                . ' ' . (string) ($courierService->api_provider ?? '');
                        }
                    }
                    $charges = $charges->filter(function ($c) use ($destIn, $service) {
                        return \App\Services\ShipmentChargeService::matchDestination($c->destination, $destIn)
                            && \App\Services\ShipmentChargeService::matchService($c->service_id, $service);
                    })->values();
                }
            }

            return response()->json([
                'success' => true,
                'charges' => $charges,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Dispute Orders listing page (sidebar: Manage Orders > Orders > Dispute Orders).
     * Apply Dispute Charge modal se save hui saari rows yahan table me dikhti hain.
     */
    public function disputeOrders()
    {
        $disputes = DB::table('shipment_disputes as sd')
            ->leftJoin('shipment_invoice as si', 'si.id', '=', 'sd.shipment_invoice_id')
            ->leftJoin('shipper_info as shp', 'shp.id', '=', 'sd.shipper_id')
            ->leftJoin('customers as c', 'c.id', '=', 'shp.customer_id')
            ->leftJoin('consignee_info as con', 'con.shipper_id', '=', 'shp.id')
            ->leftJoin('admin_user as au', 'au.id', '=', 'sd.applied_by')
            ->where('sd.status', '!=', 'cancelled')
            ->select(
                'sd.*',
                'si.invoice_number',
                'si.invoice_currency',
                'shp.company_name as shipper_company',
                'shp.contact_person as shipper_contact',
                'shp.city as shipper_city',
                'shp.state as shipper_state',
                'c.first_name',
                'c.last_name',
                'c.email as customer_email',
                'con.consignee_name',
                'con.city as consignee_city',
                'con.state as consignee_state',
                'con.delivery_destination as consignee_destination',
                'au.name as applied_by_name'
            )
            ->orderByDesc('sd.created_at')
            ->get();

        $totalBase = (float) $disputes->sum('base_amount');
        $totalGst = (float) $disputes->sum('gst_amount');
        $totalIncl = (float) $disputes->sum('total_incl_gst');

        return view('admin.dispute-orders', compact('disputes', 'totalBase', 'totalGst', 'totalIncl'));
    }

    /**
     * Cancel Orders listing page (sidebar: Manage Orders > Orders > Cancel Orders).
     * Saare cancelled shipments (shipper ya invoice status cancelled) yahan
     * table me dikhte hain — customer self-cancel + dispute-cancel dono.
     */
    public function cancelOrders()
    {
        $orders = DB::table('shipment_invoice as si')
            ->join('shipper_info as shp', 'shp.id', '=', 'si.shipper_id')
            ->leftJoin('customers as c', 'c.id', '=', 'shp.customer_id')
            ->leftJoin('consignee_info as con', 'con.shipper_id', '=', 'shp.id')
            ->where(function ($query) {
                $query->where('shp.status', 'cancelled')
                    ->orWhere('si.status', 'cancelled');
            })
            ->select(
                'si.id',
                'si.invoice_number',
                'si.invoice_currency',
                'si.created_at as order_date',
                'shp.id as shipper_id',
                'shp.awb_number',
                'shp.company_name as shipper_company',
                'shp.contact_person as shipper_contact',
                'shp.city as shipper_city',
                'shp.state as shipper_state',
                'shp.status as shipper_status',
                'shp.total_price',
                'c.first_name',
                'c.last_name',
                'c.email as customer_email',
                'con.consignee_name',
                'con.city as consignee_city',
                'con.state as consignee_state',
                'con.delivery_destination as consignee_destination'
            )
            ->orderByDesc('si.created_at')
            ->get();

        // Latest 'cancelled' log per shipper: kab + kisne cancel kiya.
        $cancelLogs = [];
        if ($orders->isNotEmpty()) {
            $logs = ShipmentLog::whereIn('shipper_id', $orders->pluck('shipper_id')->all())
                ->where('status', 'cancelled')
                ->orderBy('created_at')
                ->get(['shipper_id', 'created_at', 'performed_by', 'description']);
            foreach ($logs as $log) {
                $cancelLogs[$log->shipper_id] = $log;
            }
        }

        $totalAmount = (float) $orders->sum('total_price');

        return view('admin.cancel-orders', compact('orders', 'cancelLogs', 'totalAmount'));
    }

    /**
     * Wallet Transactions page (sidebar: Account > Wallet Transaction).
     * Customer select karne par uski wallet_transactions rows AJAX se aati hain.
     */
    public function walletTransactions()
    {
        $customers = Customer::select('id', 'first_name', 'last_name', 'email', 'phone_number')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.wallet-transactions', compact('customers'));
    }

    /**
     * Ek customer ki saari wallet transactions (JSON for the Wallet Transaction page).
     */
    public function walletTransactionsData(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        $wallet = Wallet::where('customer_id', $request->customer_id)->first();
        $transactions = WalletTransaction::where('customer_id', $request->customer_id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'has_wallet' => (bool) $wallet,
            'balance' => $wallet ? (float) $wallet->balance : 0,
            'total_credit' => (float) $transactions->where('type', 'credit')->sum('amount'),
            'total_debit' => (float) $transactions->where('type', 'debit')->sum('amount'),
            'transactions' => $transactions,
        ]);
    }

    /**
     * Raise a Dispute Charge with a remark (companies page modal).
     * flat/box calculation me boxes mandatory hai; Weight dispute ya
     * Custom-valued rule me custom amount mandatory hai (rule rate ki
     * jagah wahi charge hota hai). Remark mandatory hai.
     *
     * Raise par wallet se kuch deduct NAHI hota — dispute 'applied' me
     * save hota hai aur shipment 'disputed' me chala jata hai. Customer
     * accept kare tabhi admin deduct kar sakta hai (deductDisputeCharge).
     * Total GST-inclusive server par compute hota hai.
     */
    public function applyDisputeCharge(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
                'dispute_charge_id' => 'required|integer|exists:dispute_surcharge_charges,id',
                'boxes' => 'nullable|integer|min:1|max:10000',
                'custom_amount' => 'nullable|numeric|min:0|max:10000000',
                'remark' => 'required|string|max:1000',
            ]);

            $charge = \App\Models\DisputeCharge::findOrFail($request->dispute_charge_id);
            $isBox = stripos((string) $charge->calculation_type, 'box') !== false;
            $isCustom = stripos((string) $charge->additional_charges, 'weight') !== false
                || stripos((string) $charge->values, 'custom') !== false;

            if ($isBox && ! $request->filled('boxes')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter number of boxes.',
                ], 422);
            }

            if ($isCustom && ! ($request->filled('custom_amount') && (float) $request->custom_amount > 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter custom amount for this dispute.',
                ], 422);
            }

            $invoice = ShipmentInvoice::findOrFail($request->shipment_id);
            $shipper = $invoice->shipper_id ? ShipperInfo::find($invoice->shipper_id) : null;

            // Custom-amount rule me admin ka custom amount hi rate hai, warna
            // rate values string se nikalo ("Rs 1900 + 18% GST" -> 1900, "$45" -> 45).
            $rate = null;
            if ($isCustom) {
                $rate = round((float) $request->custom_amount, 2);
            } elseif (preg_match('/(\d+(?:\.\d+)?)/', str_replace(',', '', (string) $charge->values), $m)) {
                $rate = (float) $m[1];
            }
            $currency = str_starts_with(trim((string) $charge->values), '$') ? '$' : 'Rs';
            $gstPct = (float) ($charge->gst_percentage ?? 0);
            $boxes = $isBox ? (int) $request->boxes : null;
            $qty = $isBox ? $boxes : 1;

            $base = $rate !== null ? round($rate * $qty, 2) : null;
            $gstAmt = $base !== null ? round($base * $gstPct / 100, 2) : 0;
            $total = $base !== null ? round($base + $gstAmt, 2) : null;

            $remark = trim((string) $request->remark);

            $adminId = Auth::guard('admin')->id();

            DB::transaction(function () use ($invoice, $shipper, $charge, $rate, $boxes, $base, $gstPct, $gstAmt, $total, $currency, $adminId, $remark, &$dispute) {
                // Same shipment + same rule ka purana row already deducted
                // hai to use overwrite mat karo — naya raise row banao. Pending
                // (applied/accepted) row hai to wahi update hogi.
                $existing = ShipmentDispute::where('shipment_invoice_id', $invoice->id)
                    ->where('dispute_charge_id', $charge->id)
                    ->orderByDesc('id')
                    ->first();

                $attributes = [
                    'shipper_id' => $invoice->shipper_id,
                    'awb_number' => $shipper->awb_number ?? null,
                    'charge_type' => $charge->additional_charges,
                    'conditions' => $charge->conditions,
                    'destination' => $charge->destination,
                    'service_id' => $charge->service_id,
                    'calculation_type' => $charge->calculation_type,
                    'values' => $charge->values,
                    'rate' => $rate,
                    'boxes' => $boxes,
                    'base_amount' => $base,
                    'gst_percentage' => $gstPct,
                    'gst_amount' => $gstAmt,
                    'total_incl_gst' => $total,
                    'currency' => $currency,
                    'remark' => $remark,
                    'applied_by' => $adminId,
                    'accepted_at' => null,
                    'accepted_by' => null,
                    'deducted_at' => null,
                    'deducted_by' => null,
                    'status' => 'applied',
                ];

                if ($existing && in_array($existing->status, ['deducted', 'applied'], true) && (bool) $existing->deducted_at) {
                    $dispute = ShipmentDispute::create(array_merge(
                        ['shipment_invoice_id' => $invoice->id, 'dispute_charge_id' => $charge->id],
                        $attributes
                    ));
                } else {
                    $dispute = ShipmentDispute::updateOrCreate(
                        [
                            'shipment_invoice_id' => $invoice->id,
                            'dispute_charge_id' => $charge->id,
                        ],
                        $attributes
                    );
                }

                // Shipment ko 'disputed' karo: tracking entry + shipper status
                // update taaki shipment Disputed state me chala jaye.
                if ($shipper) {
                    $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();
                    if (! empty($shipper->awb_number)) {
                        \App\Models\Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'status' => 'disputed',
                            'title' => 'Shipment Disputed - Charge Applied ('.(string) $charge->additional_charges.')',
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                        ]);
                    }
                    $shipper->status = 'disputed';
                    $shipper->save();
                }
            });

            $message = 'Dispute applied with remark. Shipment marked as Disputed. Amount will be deducted from wallet only after the customer accepts.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'dispute' => $dispute,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deduct an accepted dispute charge from the customer wallet.
     *
     * Sirf 'accepted' (customer ne accept kiya hua) dispute deduct ho sakta
     * hai. Deduct ke baad resolved dispute row table se delete ho jati hai
     * aur shipment 'ready_to_dispatch' me chala jata hai.
     */
    public function deductDisputeCharge(Request $request)
    {
        try {
            $request->validate([
                'dispute_id' => 'required|integer|exists:shipment_disputes,id',
            ]);

            $dispute = ShipmentDispute::findOrFail($request->dispute_id);

            if ($dispute->status !== 'accepted') {
                $state = $dispute->status === 'applied'
                    ? 'Customer ne abhi accept nahi kiya hai.'
                    : 'Ye dispute already deduct ho chuka hai.';
                return response()->json([
                    'success' => false,
                    'message' => 'Sirf accepted dispute deduct ho sakta hai. '.$state,
                ], 422);
            }

            $deductAmount = round((float) ($dispute->total_incl_gst ?? 0), 2);
            $currency = $dispute->currency ?: 'Rs';

            $shipper = $dispute->shipper_id ? ShipperInfo::find($dispute->shipper_id) : null;
            $customerId = $shipper ? (int) $shipper->customer_id : 0;
            $wallet = $customerId > 0 ? Wallet::where('customer_id', $customerId)->first() : null;

            if ($deductAmount > 0) {
                if (! $wallet) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer wallet not found. Please contact support.',
                    ], 422);
                }
                if ((float) $wallet->balance < $deductAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient wallet balance to deduct this dispute charge. Current balance is ₹'.number_format((float) $wallet->balance, 2).', required ₹'.number_format($deductAmount, 2).'.',
                    ], 422);
                }
            }

            $adminId = Auth::guard('admin')->id();
            $newBalance = $wallet ? (float) $wallet->balance : 0;
            $disputeId = (int) $dispute->id;

            DB::transaction(function () use ($dispute, $shipper, $wallet, $deductAmount, $currency, $customerId, $adminId, &$newBalance) {
                if ($wallet && $deductAmount > 0) {
                    $wallet->decrement('balance', $deductAmount);
                    $wallet->refresh();
                    $newBalance = (float) $wallet->balance;

                    WalletTransaction::create([
                        'customer_id' => $customerId,
                        'type' => 'debit',
                        'reason' => 'dispute_charge',
                        'user_id' => $adminId,
                        'user_type' => 'admin',
                        'amount' => $deductAmount,
                        'balance_after' => $wallet->balance,
                        'reference' => $dispute->awb_number ?? ('DISPUTE-'.$dispute->id),
                        'description' => 'Dispute charge ('.$dispute->charge_type.') of '.$currency.' '.number_format($deductAmount, 2).' for shipment '.($dispute->awb_number ?? '#'.$dispute->shipper_id).' (accepted by customer)',
                    ]);
                }

                // Deduct ho gaya — resolved dispute row table se delete.
                $dispute->delete();

                if ($shipper) {
                    $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();
                    if (! empty($shipper->awb_number)) {
                        \App\Models\Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'status' => 'ready_to_dispatch',
                            'title' => 'Ready to Dispatch - Dispute Deducted ('.(string) $dispute->charge_type.')',
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                        ]);
                    }
                    $shipper->status = 'ready_to_dispatch';
                    $shipper->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Dispute charge deducted from customer wallet. Shipment marked as Ready to Dispatch.',
                'dispute_id' => $disputeId,
                'dispute_deleted' => true,
                'deducted' => $deductAmount,
                'new_balance' => $newBalance,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a pending dispute's amount with a custom value (Dispute Orders page).
     *
     * Custom amount naya BASE (pre-GST) amount hota hai; GST dispute ke apne
     * gst_percentage se recompute hota hai aur total wahi se banta hai.
     * Sirf pending (applied/accepted) disputes edit ho sakti hain. Agar dispute
     * 'accepted' thi to amount badalne par wapas 'applied' ho jati hai taaki
     * customer naya amount phir se accept kare. Customer panel (My Disputes +
     * Disputed tab) me wahi updated amount dikhta hai.
     */
    public function updateDisputeAmount(Request $request)
    {
        try {
            $request->validate([
                'dispute_id' => 'required|integer|exists:shipment_disputes,id',
                'custom_amount' => 'required|numeric|min:0.01|max:10000000',
            ]);

            $dispute = ShipmentDispute::findOrFail($request->dispute_id);

            if (! in_array($dispute->status, ['applied', 'accepted'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sirf pending disputes ka amount change ho sakta hai. Ye dispute already deduct ho chuka hai.',
                ], 422);
            }

            $base = round((float) $request->custom_amount, 2);
            $gstPct = (float) ($dispute->gst_percentage ?? 0);
            $gstAmt = round($base * $gstPct / 100, 2);
            $total = round($base + $gstAmt, 2);
            $wasAccepted = $dispute->status === 'accepted';

            DB::transaction(function () use ($dispute, $base, $gstAmt, $total, $wasAccepted) {
                $dispute->rate = $base;
                $dispute->boxes = null;
                $dispute->base_amount = $base;
                $dispute->gst_amount = $gstAmt;
                $dispute->total_incl_gst = $total;
                if ($wasAccepted) {
                    $dispute->status = 'applied';
                    $dispute->accepted_at = null;
                    $dispute->accepted_by = null;
                }
                $dispute->save();
            });

            $message = 'Dispute amount updated. New total: '.$dispute->currency.' '.number_format($total, 2).' (incl. GST).';
            if ($wasAccepted) {
                $message .= ' Customer ne purana amount accept kiya tha, isliye dispute wapas Applied hua — customer ko naya amount phir se accept karna hoga.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'dispute' => $dispute->fresh(),
                'new_total' => $total,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a pending dispute (Dispute Orders page).
     *
     * Sirf pending (applied/accepted) disputes cancel ho sakti hain. Cancel par
     * koi wallet refund NAHI hota — dispute row table se delete ho jati hai
     * aur shipper 'cancelled' ho jata hai taaki shipment customer panel ke
     * Cancelled section me dikhe. shipment_invoice ka status NAHI badalta.
     */
    public function cancelDisputeCharge(Request $request)
    {
        try {
            $request->validate([
                'dispute_id' => 'required|integer|exists:shipment_disputes,id',
            ]);

            $dispute = ShipmentDispute::findOrFail($request->dispute_id);

            if (! in_array($dispute->status, ['applied', 'accepted'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sirf pending disputes cancel ho sakti hain.',
                ], 422);
            }

            $shipper = $dispute->shipper_id ? ShipperInfo::find($dispute->shipper_id) : null;
            $adminId = Auth::guard('admin')->id();
            $previousStatus = $shipper?->status;

            DB::transaction(function () use ($dispute, $shipper, $adminId, $previousStatus) {
                // Resolved dispute row table se delete.
                $dispute->delete();

                // NOTE: shipment_invoice ka status NAHI badalta — sirf shipper
                // 'cancelled' hota hai. Customer Cancelled tab shipper status
                // se match karta hai (viewAllShipments cancelled filter).
                if ($shipper) {
                    $shipper->status = 'cancelled';
                    $shipper->save();

                    $createShipment = \App\Models\CreateShipment::where('shipper_id', $shipper->id)->first();
                    if (! empty($shipper->awb_number)) {
                        \App\Models\Tracking::create([
                            'awb_number' => $shipper->awb_number,
                            'status' => 'cancelled',
                            'title' => 'Shipment Cancelled - Dispute Cancelled by Admin',
                            'shipper_id' => $shipper->id,
                            'shipping_id' => $createShipment ? $createShipment->id : null,
                            'uwc_id' => $shipper->awb_number,
                        ]);
                    }

                    ShipmentLog::logStatus(
                        $shipper->id,
                        (string) ($shipper->awb_number ?? ''),
                        'cancelled',
                        $previousStatus,
                        'Dispute cancelled by admin. No wallet refund.',
                        $shipper->customer_id,
                        'admin'
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Dispute cancelled and removed. Shipment moved to Cancelled (no wallet refund).',
                'dispute_id' => (int) $request->dispute_id,
                'dispute_deleted' => true,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validation failed.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a shipping label PDF as base64 string.
     * Priority: Use shipment_tracking.raw_response for existing label data.
     * - UPS (response_status_description = "success"): Extract GraphicImage base64 → PDF
     * - Ship Global (response_status_description = "Ship Global order created"): Extract pdf_base64 → PDF
     * Fallback: Generate label via Dompdf if no tracking record exists.
     */
    /**
     * Resolve a stored carrier label into base64 PDF bytes.
     *
     * Accepts either a carrier-hosted label URL (downloaded server-side) or
     * an already-base64-encoded payload. Images are wrapped into a PDF so
     * the print modal always receives a PDF. Returns null when the value
     * cannot be turned into a PDF (caller falls back to the Dompdf label).
     */
    private function fetchCarrierLabelPdf($value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || strlen($value) > 6 * 1024 * 1024) {
            return null;
        }

        // Carrier-hosted URL: download it (http/https only, 30s cap, 5MB cap).
        if (preg_match('#^https?://#i', $value)) {
            try {
                $response = Http::timeout(30)->get($value);
                if (! $response->successful()) {
                    return null;
                }
                $body = (string) $response->body();
                if ($body === '' || strlen($body) > 5 * 1024 * 1024) {
                    return null;
                }
            } catch (\Throwable $e) {
                Log::warning('Carrier label download failed: '.$e->getMessage());

                return null;
            }
        } else {
            // Assume base64 payload.
            $body = base64_decode($value, true);
            if ($body === false || $body === '') {
                return null;
            }
        }

        // Already a PDF — return base64 as-is.
        if (str_starts_with($body, '%PDF-')) {
            return base64_encode($body);
        }

        // Common image formats — embed into a PDF like the UPS GIF path.
        $mimeType = null;
        if (str_starts_with($body, "\x89PNG")) {
            $mimeType = 'image/png';
        } elseif (str_starts_with($body, 'GIF8')) {
            $mimeType = 'image/gif';
        } elseif (str_starts_with($body, "\xFF\xD8\xFF")) {
            $mimeType = 'image/jpeg';
        }

        if ($mimeType === null) {
            return null;
        }

        $html = '<html><body style="margin:0;padding:0;"><img src="data:'.$mimeType.';base64,'.base64_encode($body).'" style="width:100%;height:auto;"></body></html>';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper([0, 0, 400, 600], 'portrait');

        return base64_encode($pdf->output());
    }

    public function generateLabel(Request $request)
    {
        try {
            $request->validate([
                'shipment_id' => 'required|integer|exists:shipment_invoice,id',
            ]);

            // Get shipper_id from shipment_invoice
            $shipperId = DB::table('shipment_invoice')
                ->where('id', $request->shipment_id)
                ->value('shipper_id');

            if (!$shipperId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found.'
                ]);
            }

            // Look up shipment_tracking record for this shipper
            $trackingRecord = \App\Models\ShipmentTracking::where('shipper_id', $shipperId)->first();

            // Try to extract label from shipment_tracking raw_response.
            // Every stored carrier format is attempted in order, so the print
            // button always returns the actual carrier label saved in
            // shipment_tracking (Dompdf fallback only when none is stored).
            if ($trackingRecord && $trackingRecord->raw_response) {
                $rawResponse = $trackingRecord->raw_response;
                $statusDescription = $trackingRecord->response_status_description;
                $pdfBase64 = null;

                $packageResults = null;

                // Try ShipmentResults.PackageResults path in raw_response
                if (isset($rawResponse['ShipmentResults']['PackageResults'])) {
                    $packageResults = $rawResponse['ShipmentResults']['PackageResults'];
                } elseif (isset($rawResponse['PackageResults'])) {
                    $packageResults = $rawResponse['PackageResults'];
                } elseif ($trackingRecord->package_results) {
                    $packageResults = $trackingRecord->package_results;
                }

                $firstPkg = is_array($packageResults) && isset($packageResults[0]) ? $packageResults[0] : $packageResults;

                // Attempt 1: UPS GraphicImage (newer UPS Ship API + older formats).
                if ($firstPkg) {
                    $graphicImage = null;
                    $labelFormat = null;

                    // Try ShippingLabel key (newer UPS Ship API format)
                    if (isset($firstPkg['ShippingLabel'])) {
                        $labelFormat = $firstPkg['ShippingLabel']['ImageFormat']['Code'] ?? 'GIF';
                        $graphicImage = $firstPkg['ShippingLabel']['GraphicImage'] ?? null;
                    } elseif (isset($firstPkg['LabelImage'])) {
                        // Older/different UPS response format
                        $labelFormat = $firstPkg['LabelImage']['LabelImageFormat']['Code'] ?? 'PDF';
                        $graphicImage = $firstPkg['LabelImage']['GraphicImage'] ?? null;
                    }

                    if ($graphicImage) {
                        if ($labelFormat === 'PDF') {
                            // GraphicImage is already base64-encoded PDF — return directly
                            $pdfBase64 = $graphicImage;
                        } else {
                            // GraphicImage is base64-encoded image (GIF/SPL/EPL etc.)
                            // Convert to PDF by embedding the image in a Dompdf HTML template
                            $mimeType = strtolower($labelFormat);
                            // Map common UPS format codes to MIME types
                            $mimeMap = [
                                'gif'  => 'image/gif',
                                'png'  => 'image/png',
                                'jpg'  => 'image/jpeg',
                                'jpeg' => 'image/jpeg',
                                'pdf'  => 'application/pdf',
                                'spl'  => 'application/pdf',
                                'epl'  => 'application/pdf',
                                'zpl'  => 'application/pdf',
                            ];
                            $mimeType = $mimeMap[$mimeType] ?? 'image/gif';

                            $imageBase64Src = 'data:' . $mimeType . ';base64,' . $graphicImage;

                            $html = '<html><body style="margin:0;padding:0;"><img src="' . $imageBase64Src . '" style="width:100%;height:auto;"></body></html>';
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                            $pdf->setPaper([0, 0, 400, 600], 'portrait');
                            $pdfBase64 = base64_encode($pdf->output());
                        }
                    }
                }

                // Attempt 2: Ship Global pdf_base64.
                if (empty($pdfBase64)) {
                    if (isset($rawResponse['data']['pdf_base64'])) {
                        $pdfBase64 = $rawResponse['data']['pdf_base64'];
                    } elseif (isset($rawResponse['pdf_base64'])) {
                        $pdfBase64 = $rawResponse['pdf_base64'];
                    }
                }

                // Attempt 3: Direct carrier label URL
                // (Overseas / PostShipping / Flying Tigers store LabelURL;
                // Overseas also stores the 4x6 BoxLabelURL, preferred first).
                // BoxLabelURL is also read straight from raw_response so
                // orders created before BoxLabelURL storage (or with an
                // unreadable package_results value) still print 4x6.
                if (empty($pdfBase64)) {
                    $labelUrl = null;
                    if (is_array($firstPkg)) {
                        $labelUrl = $firstPkg['BoxLabelURL'] ?? null;
                    }
                    if (empty($labelUrl) && is_array($rawResponse)) {
                        foreach (['Data', 'data'] as $dataKey) {
                            $awbBlock = $rawResponse[$dataKey]['Airwaybill'] ?? null;
                            if (is_array($awbBlock)) {
                                $labelUrl = $awbBlock['BoxlabelUrl'] ?? $awbBlock['BoxLabelURL'] ?? null;
                                if (! empty($labelUrl)) {
                                    break;
                                }
                            }
                        }
                    }
                    if (empty($labelUrl) && is_array($firstPkg)) {
                        $labelUrl = $firstPkg['LabelURL'] ?? null;
                    }
                    if ($labelUrl) {
                        $pdfBase64 = $this->fetchCarrierLabelPdf($labelUrl);
                    }
                }

                // Attempt 4: Base64 PDF stored directly (Primus stores PDF).
                if (empty($pdfBase64)) {
                    $storedPdf = is_array($firstPkg) ? ($firstPkg['PDF'] ?? null) : null;
                    if (is_string($storedPdf) && $storedPdf !== '') {
                        $pdfBase64 = $this->fetchCarrierLabelPdf($storedPdf);
                    }
                }

                // If we found a label from shipment_tracking, return it
                if ($pdfBase64) {
                    $awbNumber = DB::table('shipper_info')->where('id', $shipperId)->value('awb_number');
                    return response()->json([
                        'success'    => true,
                        'pdf_base64' => $pdfBase64,
                        'awb_number' => $awbNumber,
                        'source'     => 'shipment_tracking',
                    ]);
                }
            }

            // Fallback: Generate label using Dompdf from label-pdf template
            $shipment = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                ->leftJoin('customers', 'shipper_info.customer_id', '=', 'customers.id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->where('shipment_invoice.id', $request->shipment_id)
                ->select(
                    'shipment_invoice.id',
                    'shipment_invoice.invoice_number',
                    'shipment_invoice.invoice_date',
                    'shipment_invoice.invoice_amount',
                    'shipment_invoice.invoice_currency',
                    'shipper_info.awb_number',
                    'shipper_info.company_name as shipper_company',
                    'shipper_info.contact_person as shipper_contact',
                    'shipper_info.address_line1 as shipper_address_line1',
                    'shipper_info.address_line2 as shipper_address_line2',
                    'shipper_info.address_line3 as shipper_address_line3',
                    'shipper_info.pincode as shipper_pincode',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.phone_number as shipper_phone',
                    'customers.first_name',
                    'customers.last_name',
                    'consignee_info.consignee_name',
                    'consignee_info.address_line1 as consignee_address_line1',
                    'consignee_info.address_line2 as consignee_address_line2',
                    'consignee_info.address_line3 as consignee_address_line3',
                    'consignee_info.zip_code as consignee_zip_code',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'consignee_info.phone_number as consignee_phone'
                )
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found.'
                ]);
            }

            $labelHtml = view('admin.label-pdf', compact('shipment'))->render();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($labelHtml);
            $pdf->setPaper([0, 0, 280, 400], 'portrait');
            $pdfBase64 = base64_encode($pdf->output());

            return response()->json([
                'success'    => true,
                'pdf_base64' => $pdfBase64,
                'awb_number' => $shipment->awb_number,
                'source'     => 'dompdf_fallback',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Call the Delhivery API to create a pickup/shipment.
     *
     * @param int $shipmentId
     * @return array
     */
    /**
     * Build ONE Delhivery CMU "shipments" entry for a shipment invoice.
     *
     * Returns null when the shipment data is not found.
     */
    private function buildDelhiveryShipmentPayload($shipmentId)
    {
        // Fetch shipment with all related data
        $shipment = DB::table('shipment_invoice')
                ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
                // i want to join shipment_invoice_items
                ->join('shipment_invoice_items', 'shipment_invoice.id', '=', 'shipment_invoice_items.invoice_id')
                ->leftJoin('consignee_info', 'shipper_info.id', '=', 'consignee_info.shipper_id')
                ->leftJoin('package_dimension', 'shipper_info.id', '=', 'package_dimension.shipper_id')
                ->where('shipment_invoice.id', $shipmentId)
                ->select(
                    'shipment_invoice.id',
                    'shipment_invoice.invoice_number',
                    'shipment_invoice.invoice_amount',
                    'shipment_invoice.invoice_currency',
                    'shipment_invoice.reference_number',
                    'shipper_info.id as shipper_id',
                    'shipper_info.company_name',
                    'shipper_info.contact_person',
                    'shipper_info.address_line1',
                    'shipper_info.address_line2',
                    'shipper_info.address_line3',
                    'shipper_info.pincode',
                    'shipper_info.city as shipper_city',
                    'shipper_info.state as shipper_state',
                    'shipper_info.phone_number as shipper_phone',
                    'shipper_info.email as shipper_email',
                    'shipper_info.awb_number',
                    'consignee_info.consignee_name',
                    'consignee_info.contact_person as consignee_contact',
                    'consignee_info.address_line1 as consignee_add1',
                    'consignee_info.address_line2 as consignee_add2',
                    'consignee_info.address_line3 as consignee_add3',
                    'consignee_info.zip_code as consignee_pin',
                    'consignee_info.city as consignee_city',
                    'consignee_info.state as consignee_state',
                    'consignee_info.phone_number as consignee_phone',
                    'consignee_info.email as consignee_email',
                    'package_dimension.actual_weight_kg',
                    'package_dimension.length_cm as pkg_length',
                    'package_dimension.width_cm as pkg_width',
                    'package_dimension.height_cm as pkg_height',
                    'shipment_invoice_items.description'
                )
                ->first();

            if (!$shipment) {
                return null;
            }

            // Build the full address string for shipper (origin)
            $shipperAddress = trim(
                ($shipment->address_line1 ?? '') . ' ' .
                ($shipment->address_line2 ?? '') . ' ' .
                ($shipment->address_line3 ?? '')
            );

            // Determine payment mode based on incoterms or default to prepaid
            $paymentMode = 'prepaid';

            // Build ONE shipment entry for the Delhivery API with shipper_info details
            return [
                'name' => $shipment->company_name ?? $shipment->contact_person ?? 'Shipper',
                'add' => $shipperAddress ?: 'Address not provided',
                'pin' => $shipment->pincode ?? '',
                'city' => $shipment->shipper_city ?? '',
                'state' => $shipment->shipper_state ?? '',
                'country' => 'India',
                'phone' => $shipment->shipper_phone ?? '',
                'order' => $shipment->reference_number ?? $shipment->invoice_number ?? '',
                'payment_mode' => $paymentMode,
                'quantity' => 1,
                'weight' => $shipment->actual_weight_kg ?? 0,
                'total_amount' => $shipment->invoice_amount ?? 0,
                'products_desc' => $shipment->description ?? '',
                'cod_amount' => $paymentMode === 'COD' ? ($shipment->invoice_amount ?? 0) : 0,
                'shipping_mode' => 'Surface',
                'shipment_width' => $shipment->pkg_width ?? 0,
                'shipment_length' => $shipment->pkg_length ?? 0,
                'shipment_height' => $shipment->pkg_height ?? 0,
                'end_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            ];
    }

    /**
     * Call the Delhivery API to create a pickup/shipment (single shipment).
     *
     * @param int $shipmentId
     * @return array
     */
    private function callDelhiveryApi($shipmentId)
    {
        $payload = $this->buildDelhiveryShipmentPayload($shipmentId);
        if (!$payload) {
            return ['success' => false, 'message' => 'Shipment data not found for Delhivery API call.'];
        }

        // Explicit waybill avoids Delhivery auto-consume failures
        // ("Unable to consume <waybill> for <pickup_location>").
        $waybills = $this->fetchDelhiveryWaybills(1);
        if (!empty($waybills)) {
            $payload['waybill'] = $waybills[0];
        }

        return $this->postDelhiveryShipments([$payload], 'shipment #' . $shipmentId);
    }

    /**
     * Call the Delhivery API ONCE for a whole manifest.
     *
     * All shipment entries go into a single CMU request's "shipments" array
     * instead of one HTTP call per shipment. Per-package failures are mapped
     * back to order ids in the "failed_orders" key of the response.
     *
     * @param int[] $shipmentIds
     * @return array
     */
    private function callDelhiveryBulkApi(array $shipmentIds)
    {
        $shipmentsData = [];
        $orderToAwb = [];
        $missing = 0;

        // Order key must match the payload builder exactly:
        // reference_number ?? invoice_number ?? ''.
        $rows = DB::table('shipment_invoice')
            ->join('shipper_info', 'shipment_invoice.shipper_id', '=', 'shipper_info.id')
            ->whereIn('shipment_invoice.id', $shipmentIds)
            ->select('shipment_invoice.id', 'shipment_invoice.reference_number', 'shipment_invoice.invoice_number', 'shipper_info.awb_number')
            ->get()
            ->keyBy('id');

        foreach ($shipmentIds as $sid) {
            $payload = $this->buildDelhiveryShipmentPayload($sid);
            if (!$payload) {
                $missing++;
                continue;
            }
            $shipmentsData[] = $payload;
            $row = $rows->get($sid);
            $orderKey = (string) ($payload['order'] ?? '');
            $orderToAwb[$orderKey] = $row?->awb_number;
        }

        if (empty($shipmentsData)) {
            return ['success' => false, 'message' => 'Shipment data not found for Delhivery API call.'];
        }

        // One explicit waybill per shipment (same auto-consume fix as single).
        // Shipments without a pre-fetched waybill fall back to auto-assign.
        $waybills = $this->fetchDelhiveryWaybills(count($shipmentsData));
        foreach ($shipmentsData as $i => $item) {
            if (isset($waybills[$i]) && $waybills[$i] !== '') {
                $shipmentsData[$i]['waybill'] = $waybills[$i];
            }
        }

        $result = $this->postDelhiveryShipments(
            $shipmentsData,
            count($shipmentsData) . ' shipment(s)' . ($missing > 0 ? ' (' . $missing . ' skipped, data missing)' : '')
        );

        // Map per-package failures back to our order ids / AWBs.
        $failed = [];
        $packages = $result['data']['packages'] ?? null;
        if (is_array($packages)) {
            foreach ($packages as $pkg) {
                if (($pkg['status'] ?? '') !== 'Fail') {
                    continue;
                }
                $orderKey = (string) ($pkg['order'] ?? $pkg['refnum'] ?? $pkg['reference_number'] ?? '');
                $remarks = isset($pkg['remarks']) && is_array($pkg['remarks'])
                    ? implode(', ', array_filter($pkg['remarks']))
                    : '';
                $failed[] = [
                    'order' => $orderKey !== '' ? $orderKey : null,
                    'awb' => ($orderKey !== '' && array_key_exists($orderKey, $orderToAwb)) ? $orderToAwb[$orderKey] : null,
                    'remarks' => $remarks,
                ];
            }
        }
        if (!empty($failed)) {
            $result['failed_orders'] = $failed;
        }

        return $result;
    }

    /**
     * Pre-fetch unused Delhivery waybills for explicit use in the CMU payload.
     *
     * Official Bulk Waybill endpoint:
     *   GET {waybill_url}?cl={client}&token={token}&count={n}
     * Some accounts fail Delhivery-side auto-consume ("Unable to consume
     * <waybill> for <pickup_location>"); sending an explicit pre-fetched
     * waybill per shipment avoids that path.
     *
     * Returns an array of waybill strings — possibly fewer than requested,
     * or empty when pre-fetch is disabled or fails. Callers then fall back
     * to Delhivery auto-assign, so this never blocks the pickup creation.
     *
     * @param int $count
     * @return string[]
     */
    private function fetchDelhiveryWaybills($count)
    {
        $count = max(1, (int) $count);
        $cfg = config('services.delhivery', []);

        if (empty($cfg['waybill_prefetch'])) {
            return [];
        }

        try {
            $token = $cfg['token'] ?? '';
            if ($token === '') {
                return [];
            }
            $url = rtrim($cfg['waybill_url'] ?? 'https://track.delhivery.com/waybill/api/bulk/json/', '/') . '/';
            $timeout = (int) ($cfg['waybill_timeout'] ?? 15);

            $query = ['token' => $token, 'count' => $count];
            if (!empty($cfg['client'])) {
                $query['cl'] = $cfg['client'];
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $token,
            ])->timeout($timeout)
                ->connectTimeout(min(10, $timeout))
                ->get($url, $query);

            if (!$response->successful()) {
                Log::warning('Delhivery waybill pre-fetch failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return [];
            }

            $body = $response->json();

            // Tolerant parsing: Delhivery returns a bare JSON string with
            // COMMA-SEPARATED waybills ("wb1,wb2,..."), a plain list, or an
            // object wrapped under a known key.
            $splitWaybills = function ($s) {
                return preg_split('/[\s,;]+/', trim((string) $s), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            };

            $list = [];
            if (is_scalar($body) && trim((string) $body) !== '') {
                $list = $splitWaybills($body);
            } elseif (is_array($body)) {
                if (array_is_list($body)) {
                    $list = $body;
                } else {
                    foreach (['waybills', 'wbns', 'data', 'waybill'] as $key) {
                        if (isset($body[$key]) && is_array($body[$key])) {
                            $list = array_is_list($body[$key]) ? $body[$key] : [$body[$key]];
                            break;
                        }
                    }
                }
            }

            $waybills = [];
            foreach ($list as $w) {
                if (is_array($w)) {
                    $w = $w['waybill'] ?? $w['wbn'] ?? '';
                }
                // Each element may itself be comma-joined; keep digits only
                // so an error sentence never becomes a fake waybill.
                foreach ($splitWaybills($w) as $one) {
                    if (ctype_digit($one)) {
                        $waybills[] = $one;
                    }
                }
            }
            $waybills = array_values(array_unique($waybills));

            Log::info('Delhivery waybill pre-fetch: requested ' . $count . ', received ' . count($waybills));

            return $waybills;
        } catch (\Exception $e) {
            Log::warning('Delhivery waybill pre-fetch exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * POST a "shipments" array to Delhivery CMU create.json and parse the reply.
     *
     * @param array[] $shipmentsData
     * @param string $logContext Free text for log lines (e.g. "shipment #5" or "3 shipment(s)")
     * @return array
     */
    private function postDelhiveryShipments(array $shipmentsData, $logContext)
    {
        try {
            // Build pickup_location object - name must remain unchanged as specified
            $delhiveryCfg = config('services.delhivery', []);
            $pickupLocation = [
                'name' => $delhiveryCfg['pickup_location'] ?? 'ac549e-UNITEDWORLDWIDECOURI-do',
            ];

            // Build the full data structure
            $data = [
                'shipments' => $shipmentsData,
                'pickup_location' => $pickupLocation,
            ];

            // Make the API call to Delhivery
            // Note: asForm() sets Content-Type to application/x-www-form-urlencoded automatically
            // The Delhivery API expects form-encoded body with Accept: application/json header.
            // Explicit timeouts keep the admin UI from hanging ~30s when the
            // server cannot reach Delhivery (firewall/DNS/proxy issue); one
            // retry absorbs transient network blips. Delhivery dedupes on the
            // order id ("Duplicate order id"), so a single retry is safe.
            $createUrl = $delhiveryCfg['create_url'] ?? 'https://track.delhivery.com/api/cmu/create.json';
            $token = $delhiveryCfg['token'] ?? '462d4dd4644874ba774fa599aef160a97ed3fa7f';
            $timeout = (int) ($delhiveryCfg['timeout'] ?? 25);
            $connectTimeout = (int) ($delhiveryCfg['connect_timeout'] ?? 10);
            $retries = (int) ($delhiveryCfg['retries'] ?? 1);
            $retryDelay = (int) ($delhiveryCfg['retry_delay'] ?? 1000);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $token,
            ])->asForm()
                ->timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->retry($retries, $retryDelay)
                ->post($createUrl, [
                    'format' => 'json',
                    'data' => json_encode($data),
                ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                // Check the inner data.success field — Delhivery can return HTTP 200
                // but with data.success = false (e.g. "Duplicate order id")
                $innerSuccess = true;
                if (isset($apiResponse['success']) && $apiResponse['success'] === false) {
                    $innerSuccess = false;
                }

                if ($innerSuccess) {
                    return [
                        'success' => true,
                        'message' => 'Delhivery pickup created successfully.',
                        'data' => $apiResponse,
                    ];
                } else {
                    // Extract error details from the Delhivery response
                    $errorMessage = 'Delhivery pickup creation failed.';
                    if (isset($apiResponse['rmk']) && !empty($apiResponse['rmk'])) {
                        $errorMessage = is_array($apiResponse['rmk']) ? implode(', ', $apiResponse['rmk']) : $apiResponse['rmk'];
                    }
                    // Also check packages for per-package error remarks
                    if (isset($apiResponse['packages']) && is_array($apiResponse['packages'])) {
                        foreach ($apiResponse['packages'] as $pkg) {
                            if (isset($pkg['remarks']) && is_array($pkg['remarks']) && !empty($pkg['remarks'])) {
                                $errorMessage .= ' - ' . implode(', ', $pkg['remarks']);
                            }
                            if (isset($pkg['status']) && $pkg['status'] === 'Fail') {
                                $errorMessage .= ' (Status: Fail)';
                            }
                        }
                    }
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'data' => $apiResponse,
                    ];
                }
            } else {
                $apiResponse = $response->json();
                Log::warning('Delhivery API error for ' . $logContext, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $errorMessage = 'Delhivery API returned error.';
                if (is_array($apiResponse)) {
                    // Delhivery sometimes returns errors in various formats
                    if (isset($apiResponse['error'])) {
                        $errorMessage = $apiResponse['error'];
                    } elseif (isset($apiResponse['message'])) {
                        $errorMessage = $apiResponse['message'];
                    } elseif (isset($apiResponse['rmk'])) {
                        $errorMessage = $apiResponse['rmk'];
                    }
                }
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $apiResponse,
                    'status_code' => $response->status(),
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // cURL error 28 (timeout) / DNS / connection refused: the app
            // server itself cannot reach Delhivery. Surface an actionable
            // message instead of the raw cURL dump.
            Log::warning('Delhivery API unreachable for ' . $logContext . ': ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Delhivery server tak pahunch nahi ho paya (connection timeout). '
                    . 'Server ka outbound HTTPS/firewall check karein — track.delhivery.com:443 open hona chahiye. '
                    . 'Delivery assignment save ho gayi hai; Delhivery pickup baad me retry karein.',
            ];
        } catch (\Exception $e) {
            Log::error('Delhivery API call failed for ' . $logContext . ': ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Delhivery API call failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Display delivery persons management page.
     */
    public function deliveryPersons()
    {
        $deliveryPersons = Admin::where('type', 'Delivery_person')
            ->orderBy('name')
            ->get();

        return view('admin.delivery-persons', compact('deliveryPersons'));
    }

    /**
     * Store a new delivery person.
     */
    public function storeDeliveryPerson(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email',
                'mobile' => 'nullable|string|max:20|unique:admin_user,mobile',
                'password' => 'required|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
            ], [
                'email.unique' => 'This email address is already in use.',
                'mobile.unique' => 'This mobile number is already in use.',
            ]);

            Admin::create([
                'type' => 'Delivery_person',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'password' => bcrypt($validated['password']),
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
            ]);

            return redirect()->route('admin.delivery-persons')
                ->with('success', 'Delivery person added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.delivery-persons')
                ->withErrors($e->validator)
                ->withInput()
                ->with('open_delivery_person_modal', 'add');
        } catch (\Exception $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing delivery person.
     */
    public function updateDeliveryPerson(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email,' . $id,
                'mobile' => 'nullable|string|max:20|unique:admin_user,mobile,' . $id,
                'password' => 'nullable|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
            ], [
                'email.unique' => 'This email address is already in use.',
                'mobile.unique' => 'This mobile number is already in use.',
            ]);

            $deliveryPerson = Admin::where('type', 'Delivery_person')->findOrFail($id);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $deliveryPerson->update($updateData);

            return redirect()->route('admin.delivery-persons')
                ->with('success', 'Delivery person updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.delivery-persons')
                ->withErrors($e->validator)
                ->withInput()
                ->with('open_delivery_person_modal', 'edit')
                ->with('edit_delivery_person_id', $id);
        } catch (\Exception $e) {
            return redirect()->route('admin.delivery-persons')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the Create User management page.
     * Lists all admin users (except Super Admin) with their module access.
     */
    public function createUser()
    {
        $users = Admin::where('type', '!=', 'Delivery_person')
            ->orderByDesc('id')
            ->get();

        $modules = Admin::getModules();

        return view('admin.create-user', compact('users', 'modules'));
    }

    /**
     * Store a newly created admin user with module-wise access.
     */
    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email',
                'mobile' => 'nullable|string|max:20',
                'password' => 'required|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
                'type' => 'required|in:Admin,Super Admin',
                'module_access' => 'nullable|array',
                'module_access.*' => 'string|in:' . implode(',', Admin::getModuleKeys()),
            ]);

            // Super Admin gets all modules; Admin gets only selected ones
            $moduleAccess = $validated['type'] === 'Super Admin'
                ? Admin::getModuleKeys()
                : ($validated['module_access'] ?? []);

            Admin::create([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'password' => bcrypt($validated['password']),
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
                'module_access' => $moduleAccess,
            ]);

            return redirect()->route('admin.create-user')
                ->with('success', 'User created successfully with module access!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing admin user and their module access.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = Admin::where('type', '!=', 'Delivery_person')->findOrFail($id);

            // Prevent editing a Super Admin account
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'Super Admin account cannot be modified.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admin_user,email,' . $id,
                'mobile' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                'designation' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1',
                'type' => 'required|in:Admin,Super Admin',
                'module_access' => 'nullable|array',
                'module_access.*' => 'string|in:' . implode(',', Admin::getModuleKeys()),
            ]);

            // Super Admin gets all modules; Admin gets only selected ones
            $moduleAccess = $validated['type'] === 'Super Admin'
                ? Admin::getModuleKeys()
                : ($validated['module_access'] ?? []);

            $updateData = [
                'type' => $validated['type'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'status' => $validated['status'] ?? 1,
                'module_access' => $moduleAccess,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $user->update($updateData);

            return redirect()->route('admin.create-user')
                ->with('success', 'User updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete an admin user.
     */
    public function deleteUser($id)
    {
        try {
            $user = Admin::where('type', '!=', 'Delivery_person')->findOrFail($id);

            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'Super Admin account cannot be deleted.');
            }

            // Prevent self-deletion
            if (Auth::guard('admin')->check() && Auth::guard('admin')->id() == $id) {
                return redirect()->route('admin.create-user')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.create-user')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.create-user')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function createShipment()
    {
        return view('admin.create-shipment');
    }

    public function csb5Form()
    {
        $csbForms = CsbForm::with(['customer' => function ($query) {
                $query->with('businessCategory');
            }])
            ->latest()
            ->get();

        return view('admin.csb5-form', compact('csbForms'));
    }

    public function approveCsb5($id)
    {
        $csbForm = CsbForm::findOrFail($id);
        $csbForm->is_csb_v = true;
        $csbForm->save();

        \App\Support\SystemLogger::log(
            'csb5.approve',
            'CSB5 approved for Customer #' . $csbForm->customer_id,
            'csb_form',
            0,
            1
        );

        return redirect()->to(route('admin.csb5-form') . '#tab-pending')
            ->with('success', 'CSB5 form approved successfully. It has been moved out of pending.');
    }

    public function formKyc()
    {
        return view('admin.form-kyc');
    }
    


    // Network Page Management Methods


    // FAQ Management Methods


    // Testimonials / Reviews Management Methods


    // Blog Management Methods


    // ========== E-Book Management ==========


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

                $jsonFields = $request->json_fields ?? [];

                // Reverse mapping of getContentAttribute(): json_fields key => db column.
                // The accessor rebuilds the "content" array from these individual columns
                // (it ignores the raw content JSON), and the frontend reads the columns
                // directly. So we must persist edits back to the columns — otherwise the
                // DB content JSON changes but the frontend never reflects the update.
                $fieldColumnMap = [
                    'title'       => 'title',
                    'description' => 'description',
                    'image'       => 'image',
                    'link'        => 'link',
                    'icon_svg'    => 'icon_svg',
                    'icon_class'  => 'icon_class',
                    'color_class' => 'color_scheme',
                    'badge_text'  => 'badge_text',
                    'button_text' => 'button_text',
                    'button_url'  => 'button_url',
                    'btn_text'    => 'btn_text',
                    'subtitle'    => 'subtitle',
                    'paragraphs'  => 'paragraphs',
                    'question'    => 'question',
                    'answer'      => 'answer',
                    'name'        => 'name',
                    'avatar'      => 'avatar_url',
                    'rating'      => 'rating',
                    'text'        => 'text_content',
                    'value'       => 'stat_value',
                    'label'       => 'stat_label',
                    'suffix'      => 'stat_suffix',
                    'logo_url'    => 'logo_url',
                    'alt'         => 'alt_text',
                ];

                // Array fields are stored as newline-separated text.
                // Both "check_list" (frontend key) and "checklist" (legacy about-row key)
                // map to the same check_list_text column so the frontend's
                // $aboutData['check_list'] lookup resolves correctly.
                $arrayFieldMap = [
                    'list_items' => 'list_items_text',
                    'check_list' => 'check_list_text',
                    'checklist'  => 'check_list_text',
                ];

                // Preserve existing extra_content keys, then update with submitted extras
                $extraContent = [];
                if (!empty($trackOrder->extra_content)) {
                    $decoded = json_decode($trackOrder->extra_content, true);
                    if (is_array($decoded)) {
                        $extraContent = $decoded;
                    }
                }

                foreach ($jsonFields as $key => $value) {
                    if (array_key_exists($key, $fieldColumnMap)) {
                        $column = $fieldColumnMap[$key];
                        $trackOrder->{$column} = ($value === '' ? null : $value);
                    } elseif (array_key_exists($key, $arrayFieldMap)) {
                        $column = $arrayFieldMap[$key];
                        $trackOrder->{$column} = is_array($value)
                            ? implode("\n", $value)
                            : ($value === '' ? null : $value);
                        // Remove any legacy copy of this array key from extra_content
                        // so the accessor's array_merge doesn't reintroduce a stale
                        // value (e.g. legacy "checklist" vs frontend "check_list").
                        unset($extraContent[$key]);
                    } else {
                        // Unmapped keys live in extra_content JSON
                        if ($value === '' || $value === null) {
                            unset($extraContent[$key]);
                        } else {
                            $extraContent[$key] = $value;
                        }
                    }
                }

                // Also drop legacy array keys that may already exist in extra_content
                // from older saves but are now backed by their own text columns.
                foreach (array_keys($arrayFieldMap) as $legacyKey) {
                    unset($extraContent[$legacyKey]);
                }

                $trackOrder->extra_content = !empty($extraContent) ? json_encode($extraContent) : null;

                // Keep the content column in sync as JSON (backward compatibility)
                $trackOrder->content = json_encode($jsonFields);

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
                // ── Page content row: save fields into normalized DB columns ──
                $request->validate([
                    'json_fields' => 'nullable|array',
                    'sort_order' => 'nullable|integer|min:0',
                    'status' => 'nullable|in:Active,Inactive',
                ]);

                $fields = $request->json_fields ?? [];
                $fieldColumnMap = [
                    'title' => 'title',
                    'description' => 'description',
                    'image' => 'image',
                    'link' => 'link',
                    'badge' => 'badge_text',
                    'badge_text' => 'badge_text',
                    'icon_svg' => 'icon_svg',
                    'icon_class' => 'icon_class',
                    'color_class' => 'color_scheme',
                    'button_text' => 'button_text',
                    'button_url' => 'button_url',
                    'btn_text' => 'btn_text',
                    'subtitle' => 'subtitle',
                    'paragraphs' => 'paragraphs',
                    'question' => 'question',
                    'answer' => 'answer',
                    'name' => 'name',
                    'avatar' => 'avatar_url',
                    'rating' => 'rating',
                    'text' => 'text_content',
                    'value' => 'stat_value',
                    'label' => 'stat_label',
                    'suffix' => 'stat_suffix',
                    'logo_url' => 'logo_url',
                    'alt' => 'alt_text',
                ];
                $extraContent = [];

                foreach ($fields as $key => $value) {
                    if (isset($fieldColumnMap[$key])) {
                        $webinar->{$fieldColumnMap[$key]} = $value === '' ? null : $value;
                    } elseif ($value !== '' && $value !== null) {
                        $extraContent[$key] = $value;
                    }
                }

                // The legacy content column is no longer used for editable data.
                $webinar->content = null;
                $webinar->extra_content = $extraContent ? json_encode($extraContent) : null;
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

            $webinar->fill($request->except(['image', 'json_fields', 'content']));

            // Store item metadata outside the legacy content column.
            if ($request->has('json_fields')) {
                $itemFields = array_filter(
                    $request->json_fields,
                    static fn ($value) => $value !== '' && $value !== null
                );
                $webinar->extra_content = $itemFields ? json_encode($itemFields) : null;
            }
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


    // ========== World Weather Page Management ==========


    // ========== World Time Page Management ==========


    // ========== Express Air Freight Solutions Page Management ==========


    /**
     * CKEditor Image Upload - stores images in blog_image folder
     */


    // ========== Barcode Generator Page Management ==========


    // ========== Shipping Rate Calculator Page Management ==========


    // ========== HSN Finder Page Management ==========


    // ========== Partnership Page Management ==========


    // =============================================
    // DOCUMENT DOWNLOAD PAGE MANAGEMENT
    // =============================================


    // ========== Common Stats (Fact Number Section) Management ==========


    // ========== Partners Section (Logos) Management ==========


    public function kycPending()
    {
        // ===== "Complete KYC" tile =====
        // Full submissions (KycDetail) that are still waiting for admin review.
        $kycDetails = \App\Models\KycDetail::with(['customer.csbForm'])
            ->whereIn('kyc_status', ['pending', 'under_review'])
            ->orderBy('id', 'desc')
            ->get();

        // ===== "Incomplete KYC" tile =====
        // Every registered customer who does NOT currently hold an approved /
        // pending / under-review submission is considered incomplete. That covers:
        //   1. Customers who started the KYC wizard (have a KycDraft row)
        //   2. Customers who only registered and never submitted/started KYC at all
        //      (no row in kyc_details and no row in kyc_draft)
        $activeKycCustomerIds = \App\Models\KycDetail::whereIn('kyc_status', ['pending', 'under_review', 'approved'])
            ->pluck('customer_id')
            ->unique()
            ->values();

        $incompleteCustomers = \App\Models\Customer::with('businessCategory')
            ->whereNotIn('id', $activeKycCustomerIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Latest draft per incomplete customer (a customer may rarely hold more than
        // one draft row, e.g. one per KYC flow).
        $draftsByCustomer = \App\Models\KycDraft::whereIn('customer_id', $incompleteCustomers->pluck('id'))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('customer_id');

        // Customers whose last submission was rejected are waiting on a re-submission.
        $rejectedCustomerIds = \App\Models\KycDetail::where('kyc_status', 'rejected')
            ->pluck('customer_id')
            ->unique()
            ->values();

        $incompleteKycItems = $incompleteCustomers->map(function ($customer) use ($draftsByCustomer, $rejectedCustomerIds) {
            $draft = $draftsByCustomer->get($customer->id)?->first();
            $data = $draft && is_array($draft->form_data) ? $draft->form_data : [];

            // Resolve the KYC flow: prefer the draft's type, otherwise fall back to
            // the customer's business category (Personal / Business).
            if ($draft) {
                $kycType = ($draft->kyc_type ?? 'personal') === 'business' ? 'business' : 'personal';
            } else {
                $userType = $customer->businessCategory->user_type ?? 'Personal';
                $kycType = strcasecmp(trim((string) $userType), 'Business') === 0 ? 'business' : 'personal';
            }

            // eCommerce and Exporter customers may complete Business KYC without
            // CSB-V (the step is optional for them and is skipped by default), so
            // CSB-V must never count towards their KYC progress. Exporter
            // customers may additionally complete Business KYC without Aadhaar,
            // so the Aadhar step is also excluded from their progress.
            $isCsbVOptional = false;
            $isAadhaarOptional = false;
            if ($customer->relationLoaded('businessCategory') && $customer->businessCategory) {
                $category = $customer->businessCategory;
                $categoryKey = strtolower(trim((string) $category->category_slug));
                $categoryName = strtolower(trim((string) $category->category_name));
                $isExporter = in_array($categoryKey, ['exporter', 'exporters'], true)
                    || in_array($categoryName, ['exporter', 'exporters'], true);
                $isCsbVOptional = $isExporter
                    || in_array($categoryKey, ['ecommerce', 'e-commerce', 'e commerce'], true)
                    || in_array($categoryName, ['ecommerce', 'e-commerce', 'e commerce'], true);
                $isAadhaarOptional = $isExporter;
            }

            $steps = $kycType === 'business' ? [
                'GST' => ! empty($data['gst_number']),
                'Aadhar' => ! empty($data['aadhar_number']),
                'PAN' => ! empty($data['pan_number']),
                'CSB-V' => ! empty($data['gst_certificate_number'])
                    || ! empty($data['iec_number'])
                    || ! empty($data['bank_account_number']),
                'Signature' => ! empty($data['signature_document']) || ! empty($data['signature']),
                'Agreement' => ! empty($data['terms_accepted']),
            ] : [
                'Aadhar' => ! empty($data['aadhar_number']),
                'PAN' => ! empty($data['pan_number']),
                'Signature' => ! empty($data['signature_document']) || ! empty($data['signature']),
                'Agreement' => ! empty($data['terms_accepted']),
            ];

            if ($kycType === 'business' && $isCsbVOptional) {
                unset($steps['CSB-V']);
            }
            if ($kycType === 'business' && $isAadhaarOptional) {
                unset($steps['Aadhar']);
            }

            $completed = collect($steps)->filter()->keys();

            return [
                'customer' => $customer,
                'kyc_type' => $kycType,
                'has_draft' => (bool) $draft,
                'form_data' => $data,
                'updated_at' => $draft ? $draft->updated_at : $customer->created_at,
                'progress_done' => $completed->count(),
                'progress_total' => count($steps),
                'progress_labels' => $completed->implode(', '),
                'is_rejected' => $rejectedCustomerIds->contains($customer->id),
            ];
        });

        return view('admin.kyc-pending', compact('kycDetails', 'incompleteKycItems'));
    }

    public function kycApproved()
    {
        $approvedKycDetails = \App\Models\KycDetail::with(['customer.csbForm'])
            ->where('kyc_status', 'approved')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.kyc-approved', compact('approvedKycDetails'));
    }

    public function kycRejected()
    {
        $rejectedKycDetails = \App\Models\KycDetail::with(['customer.csbForm'])
            ->where('kyc_status', 'rejected')
            ->orderBy('id', 'desc')
            ->get();

        $rejectedRemarks = \App\Models\KycDraft::whereIn(
            'customer_id',
            $rejectedKycDetails->pluck('customer_id')->unique()
        )
            ->get(['customer_id', 'kyc_type', 'form_data'])
            ->mapWithKeys(function ($draft) {
                $formData = is_array($draft->form_data) ? $draft->form_data : [];
                $remark = trim((string) ($formData['reject_remark'] ?? ''));

                return [$draft->customer_id . ':' . ($draft->kyc_type ?? 'personal') => $remark];
            });

        return view('admin.kyc-rejected', compact('rejectedKycDetails', 'rejectedRemarks'));
    }

    /**
     * Reset a customer's password (admin action).
     */
    public function resetCustomerPassword(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $customer->password_hash = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $customer->setRememberToken(\Illuminate\Support\Str::random(60));
        $customer->save();

        \App\Support\SystemLogger::log(
            'customer.password_reset',
            'Password reset for customer #' . $customer->id . ' (' . $customer->first_name . ' ' . $customer->last_name . ') by admin',
            'customer',
            null,
            ['performed_by_admin' => true]
        );

        return redirect()->back()
            ->with('success', 'Password for ' . $customer->first_name . ' ' . $customer->last_name . ' has been reset successfully.');
    }

    public function approveKyc($id)
    {
        $kycDetail = \App\Models\KycDetail::findOrFail($id);
        $previousStatus = $kycDetail->kyc_status;

        $ratesCount = DB::transaction(function () use ($kycDetail) {
            $kycDetail->kyc_status = 'approved';
            $kycDetail->save();

            $customer = Customer::lockForUpdate()->findOrFail($kycDetail->customer_id);
            $customer->can_create_shipment = true;
            $customer->save();

            // Create the customer's zero-balance wallet as soon as KYC is approved.
            // firstOrCreate keeps repeated approval requests idempotent.
            Wallet::firstOrCreate(
                ['customer_id' => $kycDetail->customer_id],
                ['balance' => 0.00]
            );

            // Copy only missing default rates so repeated approval requests cannot
            // create duplicate rates or overwrite customer-specific pricing.
            $defaultRates = \App\Models\CourierRate::where('customer_id', 0)->get();
            $existingRateKeys = \App\Models\CourierRate::where('customer_id', $kycDetail->customer_id)
                ->get(['service_id', 'wt_range_start', 'wt_range_end', 'zone_no'])
                ->mapWithKeys(function ($rate) {
                    $key = implode('|', [
                        $rate->service_id,
                        (string) $rate->wt_range_start,
                        (string) $rate->wt_range_end,
                        (int) ($rate->zone_no ?? 0),
                    ]);

                    return [$key => true];
                });

            $copiedRates = 0;
            foreach ($defaultRates as $rate) {
                $key = implode('|', [
                    $rate->service_id,
                    (string) $rate->wt_range_start,
                    (string) $rate->wt_range_end,
                    (int) ($rate->zone_no ?? 0),
                ]);

                if ($existingRateKeys->has($key)) {
                    continue;
                }

                $newRate = $rate->replicate();
                $newRate->customer_id = $kycDetail->customer_id;
                $newRate->save();
                $existingRateKeys->put($key, true);
                $copiedRates++;
            }

            return $copiedRates;
        });

        if ($previousStatus !== 'approved') {
            try {
                $customer = Customer::findOrFail($kycDetail->customer_id);

                Mail::send('emails.kyc-approval', [
                    'customer' => $customer,
                    'kyc' => $kycDetail->fresh(),
                ], function ($mail) use ($customer) {
                    $mail->to(
                        $customer->email,
                        trim($customer->first_name . ' ' . $customer->last_name)
                    )
                        ->replyTo(config('mail.support_address'), config('mail.from.name'))
                        ->subject('Your KYC Has Been Approved - United Worldwide Couriers');
                });
            } catch (\Throwable $mailException) {
                report($mailException);
                \Log::error('KYC approval email error for customer ' . $kycDetail->customer_id . ': ' . $mailException->getMessage());
            }

            try {
                $customer = Customer::findOrFail($kycDetail->customer_id);
                $csbForm = \App\Models\CsbForm::where('customer_id', $customer->id)->latest()->first();
                $client = new \App\Services\AdomantraApiClient();
                $response = $client->createCustomer(
                    $client->buildPayload($customer, $kycDetail->fresh(), $csbForm)
                );
                \Log::info('Adomantra customer sync succeeded for customer ' . $customer->id, [
                    'response' => $response,
                ]);
            } catch (\Throwable $adomantraException) {
                report($adomantraException);
                \Log::error('Adomantra customer sync error for customer ' . $kycDetail->customer_id . ': ' . $adomantraException->getMessage());
            }
        }

        \App\Support\SystemLogger::log(
            'kyc.approve',
            'KYC approved: ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id),
            'kyc_detail',
            $previousStatus,
            'approved'
        );

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been approved successfully. Shipment creation enabled, wallet created with ₹0 balance, and ' . $ratesCount . ' courier rates assigned.');
    }

    public function rejectKyc(Request $request, $id)
    {
        $validated = $request->validate([
            'reject_remark' => 'required|string|max:1000',
        ]);

        $remark = trim($validated['reject_remark']);
        if ($remark === '') {
            return back()
                ->withErrors(['reject_remark' => 'The remark field is required.'])
                ->withInput();
        }

        $kycDetail = \App\Models\KycDetail::findOrFail($id);

        $previousStatus = $kycDetail->kyc_status;

        // Preserve all submitted KYC details as a draft so the customer can
        // re-submit without re-entering everything.
        $formData = collect($kycDetail->getAttributes())
            ->except(['id', 'created_at', 'updated_at'])
            ->map(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
            })
            ->toArray();

        $customer = $kycDetail->customer;
        $csb = $customer ? $customer->csbForm : null;
        if ($csb) {
            $csbData = collect($csb->getAttributes())
                ->except(['id', 'customer_id', 'created_at', 'updated_at'])
                ->map(function ($value) {
                    return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
                })
                ->toArray();
            $formData = array_merge($formData, $csbData);
        }

        $formData['reject_remark'] = $remark;

        // The frontend KYC form reads the business name from gst_business_name,
        // which lives on the customer's submitted record as organization_name.
        if (empty($formData['gst_business_name'])) {
            $formData['gst_business_name'] = $kycDetail->organization_name ?? null;
        }

        \App\Models\KycDraft::updateOrCreate(
            [
                'customer_id' => $kycDetail->customer_id,
                'kyc_type' => $kycDetail->kyc_type ?? 'personal',
            ],
            [
                'current_step' => 1,
                'form_data' => $formData,
            ]
        );

        $kycDetail->kyc_status = 'rejected';
        $kycDetail->save();

        \App\Support\SystemLogger::log(
            'kyc.reject',
            'KYC rejected: ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id),
            'kyc_detail',
            $previousStatus,
            ['status' => 'rejected', 'remark' => $remark]
        );

        if ($customer) {
            $this->sendKycRejectionEmail($customer, $kycDetail, $remark);
        }

        return redirect()->route('admin.kyc-pending')
            ->with('success', 'KYC for ' . ($kycDetail->organization_name ?? 'Customer #' . $kycDetail->customer_id) . ' has been rejected.');
    }

    /**
     * Notify the customer that their KYC application was rejected, including
     * the admin's remark. A copy is BCC'd to the internal KYC mailbox.
     */
    private function sendKycRejectionEmail(Customer $customer, KycDetail $kyc, string $remark): void
    {
        try {
            Mail::send('emails.kyc-rejected', [
                'customer' => $customer,
                'kyc' => $kyc,
                'remark' => $remark,
            ], function ($mail) use ($customer) {
                $mail->to(
                    $customer->email,
                    trim($customer->first_name . ' ' . $customer->last_name)
                )
                    ->bcc('sidhantk@unitedcouriers.biz')
                    ->replyTo(config('mail.support_address'), config('mail.from.name'))
                    ->subject('KYC Application Rejected - United Worldwide Couriers');
            });
        } catch (\Throwable $mailException) {
            report($mailException);
            \Log::error('KYC rejection email error for customer ' . $customer->id . ': ' . $mailException->getMessage());
        }
    }

    /**
     * Admin recharges a customer's wallet by a given amount.
     */
    public function rechargeCustomerWallet(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $amount = (float) $validated['amount'];

            // Find or create wallet for the customer
            $wallet = Wallet::firstOrCreate(
                ['customer_id' => $customer->id],
                ['balance' => 0]
            );

            $balanceBefore = (float) $wallet->balance;
            $mode = $request->input('mode', 'credit');
            $adminId = Auth::guard('admin')->id();

            DB::transaction(function () use ($wallet, $amount, $customer, $mode, $adminId) {
                $wallet->increment('balance', $amount);
                $wallet->refresh();

                WalletTransaction::create([
                    'customer_id'       => $customer->id,
                    'type'              => 'credit',
                    'reason'            => 'recharge',
                    'recharge_type'     => $mode,
                    'user_id'           => $adminId,
                    'user_type'         => 'admin',
                    'amount'            => $amount,
                    'balance_after'     => $wallet->balance,
                    'reference'         => 'ADMIN-' . now()->format('ymd'),
                    'description'       => 'Wallet recharge of ₹' . number_format($amount, 2) . ' by Admin',
                ]);
            });

            $wallet->refresh();

            \App\Support\SystemLogger::log(
                'wallet.recharge',
                'Wallet recharged ₹' . number_format($amount, 2) . ' for ' . $customer->first_name . ' ' . $customer->last_name,
                'wallet',
                ['balance_before' => $balanceBefore],
                ['amount' => $amount, 'mode' => $mode, 'balance_after' => (float) $wallet->balance]
            );

            return response()->json([
                'success'     => true,
                'message'     => 'Wallet recharged successfully! ₹' . number_format($amount, 2) . ' has been added to ' . $customer->first_name . ' ' . $customer->last_name . "'s wallet.",
                'new_balance' => (float) $wallet->balance,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admin wallet recharge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing recharge: ' . $e->getMessage(),
            ], 500);
        }
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

        $surcharges = \App\Models\SurCharge::orderBy('name')->get();

        // ------------------------------------------------------------------
        // Zone lookup map for the "Zone Name" / "Zone Category" columns.
        //
        // courier_rates.zone_no  matches  zone.zone_number_testing  (0-13).
        // Multiple zones can share the same zone_number_testing, so for each
        // (destination_id, zone_number_testing) pair we pre-compute:
        //   - category : the zone_category ('state' or 'zipcode')
        //   - names    : comma-separated zone names (for the table columns)
        //   - nameList : array of individual zone names (for the Add Rate
        //                modal's Select2 dropdown, where each name becomes a
        //                separate searchable option)
        //   - count    : number of zones in this group
        //
        // This is built once server-side and passed to the view so the
        // Default Rate table can render the columns without extra queries,
        // and the Customer Rate tab can look them up in JS. The Add Rate
        // modal's Select2 zone dropdown shows each zone name as a separate
        // option so the admin can search for a specific state/postal code.
        // ------------------------------------------------------------------
        $zoneLookup = [];
        // Increase GROUP_CONCAT limit so the full list of zone names (which
        // can be hundreds of postal codes for zipcode-category zones) is
        // not truncated by MySQL's default 1024-byte limit.
        \DB::statement('SET SESSION group_concat_max_len = 1000000');
        // A single zone_name may now have multiple zone_codes, so we
        // GROUP_CONCAT DISTINCT zone_name to avoid the same name appearing
        // several times in the comma-separated display / Select2 dropdown.
        $zones = \App\Models\Zone::selectRaw('destination_id, zone_number_testing, zone_category, COUNT(*) as cnt, GROUP_CONCAT(DISTINCT zone_name SEPARATOR ", ") as names')
            ->groupBy('destination_id', 'zone_number_testing', 'zone_category')
            ->get();

        foreach ($zones as $z) {
            $zoneNo = (int) $z->zone_number_testing;
            $category = $z->zone_category ?: 'state';
            $count = (int) $z->cnt;
            // Comma-separated string for the table columns.
            $nameDisplay = $z->names ?: ('Zone ' . $zoneNo);
            // Array of individual names for the Add Rate modal dropdown,
            // where each name becomes a separate searchable Select2 option.
            $nameList = $z->names
                ? array_map('trim', explode(',', $z->names))
                : ['Zone ' . $zoneNo];
            $zoneLookup[$z->destination_id][$zoneNo] = [
                'category' => $category,
                'names'    => $nameDisplay,
                'nameList' => $nameList,
                'count'    => $count,
            ];
        }

        // Map CourierService.country -> Destination.id so we can look up
        // zones for a given rate's service. courier_services.country now
        // stores the same short code as destinations.country_code
        // (e.g. "US", "UK", "CA", "AUS"). We build a lookup that tries several
        // match strategies (code, country_code, name, plus legacy friendly
        // names) so the mapping stays correct regardless of which format is
        // used.
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $countryToDestId = [];
        foreach ($destinations as $dest) {
            $countryToDestId[$dest->id] = [
                'name'         => $dest->name,
                'code'         => $dest->code,
                'country_code' => $dest->country_code,
            ];
        }

        // Helper: given a service's country string, find the matching
        // destination_id. Tries (in order): exact code, exact country_code,
        // case-insensitive name contains, code prefix.
        $countryToDestinationId = [];
        foreach ($destinations as $dest) {
            $countryToDestinationId[strtolower($dest->code)] = $dest->id;
            $countryToDestinationId[strtolower($dest->country_code)] = $dest->id;
            $countryToDestinationId[strtolower($dest->name)] = $dest->id;
        }
        // Also map the friendly country names used by courier_services.
        $friendlyMap = [
            'us'        => 1,
            'usa'       => 1,
            'uk'        => 2,
            'united kingdom' => 2,
            'canada'    => 3,
            'ca'        => 3,
            'australia' => 4,
            'aus'       => 4,
            'au'        => 4,
        ];
        foreach ($friendlyMap as $k => $v) {
            if (!isset($countryToDestinationId[$k])) {
                $countryToDestinationId[$k] = $v;
            }
        }

        // ------------------------------------------------------------------
        // Map each destination NAME to the matching courier_services.country
        // value (the short code, e.g. "US", "UK", "CA", "AUS"). Kept for
        // backward compatibility — all country <select> dropdowns in the
        // manage-rate view now use the destination country_code as the
        // option value (matching the courier_services.country short code),
        // so this name-based lookup is rarely needed. It still lets the
        // service dropdown be filtered by the selected country.
        //
        // We reuse countryToDestinationId (which already resolves every
        // friendly-name/code variant to a destination_id) by inverting it:
        // for each distinct courier_services.country, look up its
        // destination_id, then map every destination's name -> that
        // service-country string.
        // ------------------------------------------------------------------
        $serviceCountries = \App\Models\CourierService::distinct()
            ->pluck('country')
            ->filter()
            ->unique()
            ->values();
        $destIdToServiceCountry = [];
        foreach ($serviceCountries as $sc) {
            $key = strtolower(trim((string) $sc));
            $destId = $countryToDestinationId[$key] ?? null;
            if ($destId) {
                $destIdToServiceCountry[$destId] = $sc;
            }
        }
        $destNameToServiceCountry = [];
        foreach ($destinations as $dest) {
            if (isset($destIdToServiceCountry[$dest->id])) {
                $destNameToServiceCountry[$dest->name] = $destIdToServiceCountry[$dest->id];
            }
        }

        // ------------------------------------------------------------------
        // Per-service zone lookup for the Bulk Upload modal.
        //
        // Zones are either service-specific (zone.service_id is set) or
        // shared across every service of a destination (service_id IS NULL).
        // For each courier service we pre-compute the sorted list of zone
        // numbers that apply to it (shared zones + its own service-specific
        // zones for the service's destination). The bulk modal uses this to
        // decide whether the selected service has any zones for the chosen
        // country: if it does, the zone section is shown with the applicable
        // zone checkboxes; otherwise the without-zone format is used.
        // ------------------------------------------------------------------
        $sharedZonesByDest = [];
        $serviceZonesByDest = [];
        $zoneRows = \App\Models\Zone::select('destination_id', 'service_id', 'zone_number_testing')
            ->whereNotNull('destination_id')
            ->get();
        foreach ($zoneRows as $z) {
            $destId = (int) $z->destination_id;
            $zoneNo = (int) $z->zone_number_testing;
            if ($z->service_id) {
                $serviceZonesByDest[(int) $z->service_id][$destId][$zoneNo] = true;
            } else {
                $sharedZonesByDest[$destId][$zoneNo] = true;
            }
        }

        $serviceZoneNumbers = [];
        foreach ($services as $svc) {
            if (!$svc->country) {
                continue;
            }
            $destId = $countryToDestinationId[strtolower(trim((string) $svc->country))] ?? null;
            if (!$destId) {
                continue;
            }
            $merged = $sharedZonesByDest[$destId] ?? [];
            foreach ($serviceZonesByDest[$svc->id][$destId] ?? [] as $zoneNo => $_) {
                $merged[$zoneNo] = true;
            }
            if (!empty($merged)) {
                $zonesList = array_map('intval', array_keys($merged));
                sort($zonesList);
                $serviceZoneNumbers[$svc->id] = $zonesList;
            }
        }

        return view('admin.manage-rate', compact('defaultRates', 'customers', 'services', 'zoneLookup', 'countryToDestId', 'countryToDestinationId', 'destinations', 'destNameToServiceCountry', 'surcharges', 'serviceZoneNumbers'));
    }

    /**
     * Surcharge management page.
     *
     * Lists every surcharge (name, code, price) so the admin can edit
     * prices and add/delete surcharges.
     */
    public function manageSurcharges()
    {
        $surcharges = \App\Models\SurCharge::orderBy('name')->get();

        return view('admin.manage-surcharges', compact('surcharges'));
    }

    /**
     * Store a new surcharge.
     */
    public function storeSurcharge(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        \App\Models\SurCharge::create([
            'name'  => $validated['name'],
            'code'  => $validated['code'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.manage-surcharges')->with('success', 'Surcharge added successfully.');
    }

    /**
     * Update an existing surcharge (name / code / price).
     */
    public function updateSurcharge(Request $request, $id)
    {
        $surcharge = \App\Models\SurCharge::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        $surcharge->update([
            'name'  => $validated['name'],
            'code'  => $validated['code'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.manage-surcharges')->with('success', 'Surcharge updated successfully.');
    }

    /**
     * Delete a surcharge.
     */
    public function deleteSurcharge(Request $request, $id)
    {
        \App\Models\SurCharge::findOrFail($id)->delete();

        return redirect()->route('admin.manage-surcharges')->with('success', 'Surcharge deleted successfully.');
    }

    /**
     * Service page content management page.
     *
     * Backs the admin/change-service route and the change-service.blade.php
     * view. Loads every ServicePage record (active AND inactive) ordered by
     * sort_order so the admin can view and edit all service-page content
     * sections (services, faq, stats, partners, testimonials, ...).
     *
     * NOTE: This is distinct from services() below, which manages the
     * CourierService catalogue (enable/disable rate services). The route
     * /change-service maps here; /services maps to services().
     */
    public function service()
    {
        $serviceContent = \App\Models\ServicePage::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.change-service', compact('serviceContent'));
    }

    /**
     * Courier Services management page.
     *
     * Lists every courier service (enabled AND disabled) so the admin can
     * see the full catalogue and toggle each service's status. Only enabled
     * services (status = 1) show rates to customers on the create-shipment
     * page, bulk upload and bulk rate calculation.
     */
    public function services()
    {
        // Show all services so disabled ones can be re-enabled from this page.
        $services = \App\Models\CourierService::orderBy('network')->orderBy('method')->get();

        return view('admin.services', compact('services'));
    }

    /**
     * Toggle the enabled/disabled status of a courier service.
     *
     *  - status = 1  -> service shows rates to customers
     *  - status = 0  -> service is hidden from rate calculations
     *
     * Mirrors toggleCustomerStatus(): a plain form POST that redirects back
     * with a flash message. This is the proven, reliable pattern used across
     * the admin panel (no AJAX/CSRF-token juggling required).
     */
    public function toggleServiceStatus($id)
    {
        $service = \App\Models\CourierService::findOrFail($id);

        $service->status = $service->status ? 0 : 1;
        $service->save();

        $action = $service->status ? 'ENABLED' : 'DISABLED';
        $message = 'Service "' . $service->method . '" has been ' . $action . '.';

        return redirect()->back()->with('success', $message);
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

        // Include customer details (name, email, phone) and the current
        // end_date (taken from the first rate, since all rates for a
        // customer share the same end_date) so the manage-rate page can
        // show them in the end_date popup.
        $customer = \App\Models\Customer::find($customerId);
        $customerInfo = null;
        $currentEndDate = null;
        if ($customer) {
            $customerInfo = [
                'id'            => $customer->id,
                'first_name'    => $customer->first_name,
                'last_name'     => $customer->last_name,
                'email'         => $customer->email,
                'phone_number'  => $customer->phone_number,
                'full_name'     => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
            ];
            $firstRate = $rates->first();
            if ($firstRate && $firstRate->end_date) {
                $currentEndDate = $firstRate->end_date instanceof \DateTime
                    ? $firstRate->end_date->format('Y-m-d')
                    : (string) $firstRate->end_date;
            }
        }

        return response()->json([
            'rates'           => $rates,
            'customer'        => $customerInfo,
            'current_end_date' => $currentEndDate,
        ]);
    }

    /**
     * Export rates for one or more selected customers in the upload format.
     */
    public function exportCustomerRates(Request $request)
    {
        $customerIds = array_values(array_filter(array_map('intval', (array) $request->input('customer_ids', []))));
        if (empty($customerIds)) {
            abort(422, 'Please select at least one customer.');
        }

        $serviceId = $request->input('service_id');
        $serviceKey = trim((string) $request->input('service_key', ''));
        $country = trim((string) $request->input('country', ''));
        $query = \App\Models\CourierRate::with(['service', 'customer'])
            ->whereIn('customer_id', $customerIds)
            ->orderBy('customer_id')
            ->orderBy('service_id')
            ->orderBy('zone_no')
            ->orderBy('wt_range_start');
        if ($serviceId !== null && $serviceId !== '') {
            $query->where('service_id', (int) $serviceId);
        } elseif ($serviceKey !== '' && str_contains($serviceKey, '||')) {
            // DISTINCT service group from the service-first dropdown
            // (api_provider||service_code, same as Bulk Upload).
            [$keyApi, $keyCode] = explode('||', $serviceKey, 2);
            $keyApi = trim((string) $keyApi);
            $keyCode = trim((string) $keyCode);
            $query->whereHas('service', function ($serviceQuery) use ($keyApi, $keyCode) {
                $serviceQuery->whereRaw('LOWER(api_provider) = ?', [strtolower($keyApi)])
                    ->whereRaw('LOWER(service_code) = ?', [strtolower($keyCode)]);
            });
        }
        if ($country !== '') {
            $query->whereHas('service', function ($serviceQuery) use ($country) {
                $serviceQuery->where('country', $country);
            });
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Customer Code', 'Customer Name', 'Network', 'Country', 'Service Code', 'Method', 'TAT', 'Weight Start (gm)', 'Weight End (gm)', 'Zone No', 'Zone Category', 'Price', 'Default', 'Start Date', 'End Date'];

        // PhpSpreadsheet 2.x+ removed setCellValueByColumnAndRow(), so we
        // build column letters (A, B, C, ...) and use coordinate-based addressing.
        $columnLetters = [];
        for ($i = 0; $i < count($headers); $i++) {
            $columnLetters[$i] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetters[$i] . '1', $headers[$i]);
        }

        $rowNumber = 2;
        foreach ($query->get() as $rate) {
            $service = $rate->service;
            $customer = $rate->customer;
            $startDate = $rate->start_date
                ? ($rate->start_date instanceof \DateTime ? $rate->start_date->format('Y-m-d') : (string) $rate->start_date)
                : '';
            $endDate = $rate->end_date
                ? ($rate->end_date instanceof \DateTime ? $rate->end_date->format('Y-m-d') : (string) $rate->end_date)
                : '';
            $values = [
                $customer->customer_code ?? '',
                trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                $service->network ?? '',
                $service->country ?? '',
                $service->service_code ?? '',
                $service->method ?? '',
                $service->tat ?? '',
                $rate->wt_range_start,
                $rate->wt_range_end,
                $rate->zone_no,
                '',
                $rate->price,
                $rate->is_default ? 'Yes' : 'No',
                $startDate,
                $endDate,
            ];
            foreach ($values as $index => $value) {
                $sheet->setCellValue($columnLetters[$index] . $rowNumber, $value);
            }
            $rowNumber++;
        }

        $lastColumn = $columnLetters[count($columnLetters) - 1];
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        foreach ($columnLetters as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $fileName = 'customer-rates-' . date('Y-m-d-His') . '.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function updateRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);

        // Only default rates (is_default = 1) can be changed from the admin panel
        if (!$rate->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'This rate is marked as non-default and cannot be edited from the admin panel.',
            ], 403);
        }

        $rate->price = $request->price;
        $rate->save();

        // Propagate the default rate change to all customers who still use the default rate (is_default = 1).
        // Customer-specific rates that have been customized (is_default = 0) are left untouched.
        $updatedCustomers = \App\Models\CourierRate::where('customer_id', '!=', 0)
            ->where('is_default', 1)
            ->where('service_id', $rate->service_id)
            ->where('wt_range_start', $rate->wt_range_start)
            ->where('wt_range_end', $rate->wt_range_end)
            ->where('zone_no', $rate->zone_no)
            ->update(['price' => $request->price]);

        $message = 'Rate updated successfully.';
        if ($updatedCustomers > 0) {
            $message .= ' The same rate was also updated for ' . $updatedCustomers . ' customer(s) using the default rate.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function updateCustomerRate(Request $request, $id)
    {
        $rate = \App\Models\CourierRate::findOrFail($id);

        $rate->price = $request->price;
        // Once a customer rate is customized, mark it as non-default so it is no longer
        // overwritten when the corresponding default rate is changed from the Default Rates table.
        $rate->is_default = false;
        $rate->save();

        return response()->json(['success' => true, 'message' => 'Customer rate updated successfully.']);
    }

    /**
     * Update the end_date for ALL courier_rates rows belonging to a given
     * customer at once.
     *
     * Called from the manage-rate page when the admin clicks on the
     * end_date cell of any customer rate row — a popup shows the customer
     * details and an editable end_date input; on save this endpoint
     * bulk-updates every rate row for that customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerEndDate(Request $request, $customerId)
    {
        $validated = $request->validate([
            'end_date' => ['required', 'date'],
        ]);

        // Enforce that the end_date is at least tomorrow (today + 1 day).
        // The admin must never be able to set an end_date of today or earlier.
        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
        if ($validated['end_date'] < $tomorrow) {
            return response()->json([
                'success' => false,
                'message' => 'The end date must be tomorrow (' . $tomorrow . ') or a later date.',
            ], 422);
        }

        $customer = \App\Models\Customer::find($customerId);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $updated = \App\Models\CourierRate::where('customer_id', $customerId)
            ->update(['end_date' => $validated['end_date']]);

        return response()->json([
            'success'  => true,
            'message'  => 'End date updated successfully for ' . $updated . ' rate(s) of ' . trim($customer->first_name . ' ' . $customer->last_name) . '.',
            'updated'  => $updated,
            'end_date' => $validated['end_date'],
        ]);
    }

    /**
     * Bulk-update the end_date for ALL courier_rates rows belonging to
     * MULTIPLE customers at once.
     *
     * Called from the manage-rate page when the admin has selected several
     * customers in the dropdown and clicks the "End Date" button — a popup
     * shows how many customers are selected and an editable end_date input;
     * on save this endpoint bulk-updates every rate row for every selected
     * customer to the same end_date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMultipleCustomersEndDate(Request $request)
    {
        $validated = $request->validate([
            'customer_ids'   => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['required', 'integer'],
            'end_date'       => ['required', 'date'],
        ]);

        // Enforce that the end_date is at least tomorrow (today + 1 day).
        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
        if ($validated['end_date'] < $tomorrow) {
            return response()->json([
                'success' => false,
                'message' => 'The end date must be tomorrow (' . $tomorrow . ') or a later date.',
            ], 422);
        }

        // Cast all submitted IDs to integers (HTTP requests send strings).
        $customerIds = array_map('intval', $validated['customer_ids']);

        // Bulk-update every rate row for every selected customer in one query.
        $updated = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
            ->update(['end_date' => $validated['end_date']]);

        $customerCount = count($customerIds);

        return response()->json([
            'success'        => true,
            'message'        => 'End date updated successfully for ' . $updated . ' rate(s) across ' . $customerCount . ' customer(s).',
            'updated'        => $updated,
            'customer_count' => $customerCount,
            'end_date'       => $validated['end_date'],
        ]);
    }

    /**
     * Apply a downloaded customer-rate Excel sheet as a new dated rate set.
     * Rows are matched by service, weight range and zone number.
     */
    public function updateNewCustomerRate(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'integer|exists:customers,id',
            'service_id'  => 'nullable|integer|exists:courier_services,id',
            // DISTINCT service group from the service-first dropdown
            // (api_provider||service_code, same as Bulk Upload / Add Country).
            'service_key' => 'nullable|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'rate_file'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // Resolve DISTINCT service group (if picked) to api_provider + service_code.
        $filterApiProvider = null;
        $filterServiceCode = null;
        if (!empty($validated['service_key']) && str_contains($validated['service_key'], '||')) {
            [$filterApiProvider, $filterServiceCode] = explode('||', $validated['service_key'], 2);
            // Normalize the same way the JS group key does (lower/trim); match
            // case-insensitively so "OVerseas||aramex_ppx" still resolves.
            $filterApiProvider = trim((string) $filterApiProvider);
            $filterServiceCode = trim((string) $filterServiceCode);
        }

        $filePath = $request->file('rate_file')->getRealPath();

        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not read the uploaded file. Please upload the Excel file downloaded from customer rates.',
            ], 422);
        }

        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        if (count($rows) < 2) {
            return response()->json(['success' => false, 'message' => 'The uploaded file has no data rows.'], 422);
        }

        $normalizeHeader = function ($value) {
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $value)));
        };

        // DataTables Excel exports can contain a title, customer details and
        // blank rows before the actual column header. Find that header row
        // instead of assuming it is always the first row.
        $headerRowIndex = null;
        $header = [];
        foreach (array_slice($rows, 0, 10, true) as $rowIndex => $candidateRow) {
            $candidateHeader = array_map($normalizeHeader, $candidateRow);
            $hasWeightStart = in_array('weight start', $candidateHeader, true)
                || in_array('weight start (gm)', $candidateHeader, true)
                || in_array('wt start', $candidateHeader, true)
                || in_array('wt_range_start', $candidateHeader, true);
            $hasWeightEnd = in_array('weight end', $candidateHeader, true)
                || in_array('weight end (gm)', $candidateHeader, true)
                || in_array('wt end', $candidateHeader, true)
                || in_array('wt_range_end', $candidateHeader, true);
            $hasZoneNo = in_array('zone no', $candidateHeader, true)
                || in_array('zone_no', $candidateHeader, true)
                || in_array('zone number', $candidateHeader, true)
                || in_array('zone', $candidateHeader, true);
            $hasPrice = in_array('price', $candidateHeader, true)
                || in_array('rate', $candidateHeader, true)
                || in_array('amount', $candidateHeader, true)
                || in_array('cost', $candidateHeader, true);

            if ($hasWeightStart && $hasWeightEnd && $hasZoneNo && $hasPrice) {
                $headerRowIndex = $rowIndex;
                $header = $candidateHeader;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find the rate table header. Please upload the downloaded customer rates Excel file.',
            ], 422);
        }

        $findColumn = function (array $names) use ($header) {
            foreach ($header as $index => $value) {
                if (in_array($value, $names, true)) {
                    return $index;
                }
            }
            return null;
        };

        $wtStartCol = $findColumn(['weight start', 'weight start (gm)', 'wt start', 'wt_range_start']);
        $wtEndCol   = $findColumn(['weight end', 'weight end (gm)', 'wt end', 'wt_range_end']);
        $zoneNoCol  = $findColumn(['zone no', 'zone_no', 'zone number', 'zone']);
        $priceCol   = $findColumn(['price', 'rate', 'amount', 'cost']);
        $customerIdCol = $findColumn(['customer id', 'customer_id', 'customerid']);
        $customerCodeCol = $findColumn(['customer code', 'customer_code', 'customercode', 'cust code', 'cust_code']);
        $serviceCodeCol = $findColumn(['service code', 'service_code', 'servicecode']);
        $networkCol = $findColumn(['network']);
        $methodCol = $findColumn(['method']);
        // Country column (present in current exports). When filled, each row
        // is matched to its exact country clone so same-code rows for
        // different countries (e.g. AF vs AL) never collide.
        $fileCountryCol = $findColumn(['country', 'country_code', 'country code', 'destination']);

        // Export now contains Customer Code (no Customer ID) + Country.
        // Accept EITHER identifier for backward compatibility with old files.
        // NOTE: service_id is no longer posted by the service-first modal
        // (it posts service_key), so use ?? null to avoid undefined-key 500s.
        $filterServiceId = $validated['service_id'] ?? null;
        if ($wtStartCol === null || $wtEndCol === null || $zoneNoCol === null || $priceCol === null
            || ($customerIdCol === null && $customerCodeCol === null)
            || (!$filterServiceId && $serviceCodeCol === null && $networkCol === null && $methodCol === null)) {
            return response()->json([
                'success' => false,
                'message' => 'The file must contain Customer Code (or Customer ID), Weight Start, Weight End, Zone No and Price. When All Services is selected, it must also contain Service Code, Network or Method.',
            ], 422);
        }

        // ---- Customer Code guard: the file must belong to the selected customer(s) ----
        // Selected IDs -> codes.
        $selectedCustomers = \App\Models\Customer::whereIn('id', $validated['customer_ids'])->get(['id', 'customer_code']);
        $validatedCustomerIds = array_map('intval', (array) $validated['customer_ids']);
        $selectedCodeSet = [];
        foreach ($selectedCustomers as $sc) {
            $code = strtoupper(trim((string) $sc->customer_code));
            if ($code !== '') {
                $selectedCodeSet[$code] = true;
            }
        }
        // All customer codes in DB (for resolving file codes -> ids).
        $codeToIdAll = [];
        foreach (\App\Models\Customer::pluck('customer_code', 'id')->toArray() as $id => $code) {
            $code = strtoupper(trim((string) $code));
            if ($code !== '') {
                $codeToIdAll[$code] = (int) $id;
            }
        }
        // Scan file identifiers; abort if any row belongs to another customer.
        $mismatched = [];
        foreach (array_slice($rows, $headerRowIndex + 1) as $row) {
            $wtStart = isset($row[$wtStartCol]) ? trim((string) $row[$wtStartCol]) : '';
            $wtEnd   = isset($row[$wtEndCol]) ? trim((string) $row[$wtEndCol]) : '';
            $zoneNo  = isset($row[$zoneNoCol]) ? trim((string) $row[$zoneNoCol]) : '';
            $price   = isset($row[$priceCol]) ? trim((string) $row[$priceCol]) : '';
            if ($wtStart === '' && $wtEnd === '' && $zoneNo === '' && $price === '') {
                continue;
            }
            if ($customerCodeCol !== null && isset($row[$customerCodeCol]) && trim((string) $row[$customerCodeCol]) !== '') {
                $fileCode = strtoupper(trim((string) $row[$customerCodeCol]));
                if (!isset($selectedCodeSet[$fileCode])) {
                    $mismatched[$fileCode] = true;
                }
            } elseif ($customerIdCol !== null && isset($row[$customerIdCol]) && trim((string) $row[$customerIdCol]) !== '') {
                $fileId = (int) trim((string) $row[$customerIdCol]);
                if (!in_array($fileId, $validatedCustomerIds, true)) {
                    $mismatched['ID:' . $fileId] = true;
                }
            }
        }
        if (!empty($mismatched)) {
            return response()->json([
                'success' => false,
                'message' => 'This file belongs to other customer(s): ' . implode(', ', array_keys($mismatched)) . '. Please upload only the selected customer(s) file (Customer Code must match).',
            ], 422);
        }

        $updated = 0;
        $skipped = 0;
        $notFound = 0;

        \DB::transaction(function () use ($rows, $headerRowIndex, $wtStartCol, $wtEndCol, $zoneNoCol, $priceCol, $customerIdCol, $customerCodeCol, $fileCountryCol, $codeToIdAll, $serviceCodeCol, $networkCol, $methodCol, $validated, $filterApiProvider, $filterServiceCode, $filterServiceId, &$updated, &$skipped, &$notFound) {
            $normalizeValue = function ($value) {
                return strtolower(preg_replace('/\s+/', ' ', trim((string) $value)));
            };
            $normalizeNumber = function ($value) {
                return rtrim(rtrim(number_format((float) $value, 6, '.', ''), '0'), '.');
            };

            // Load the customer's rates once. Querying the database inside
            // the Excel-row loop makes large exports hit the request timeout.
            // Scope: single service_id (legacy) OR DISTINCT api_provider +
            // service_code group (service-first dropdown) OR all services.
            $existingRates = \App\Models\CourierRate::with('service')
                ->whereIn('customer_id', $validated['customer_ids'])
                ->when($filterServiceId, function ($query) use ($filterServiceId) {
                    $query->where('service_id', $filterServiceId);
                })
                ->when($filterApiProvider !== null && empty($filterServiceId), function ($query) use ($filterApiProvider, $filterServiceCode) {
                    $query->whereHas('service', function ($sq) use ($filterApiProvider, $filterServiceCode) {
                        $sq->whereRaw('LOWER(api_provider) = ?', [strtolower($filterApiProvider)])
                           ->whereRaw('LOWER(service_code) = ?', [strtolower($filterServiceCode)]);
                    });
                })
                ->get();
            $rateLookup = [];
            foreach ($existingRates as $existingRate) {
                $weightKey = $normalizeNumber($existingRate->wt_range_start) . '|'
                    . $normalizeNumber($existingRate->wt_range_end) . '|'
                    . (int) $existingRate->zone_no;
                $rateLookup['id:' . $existingRate->customer_id . '|' . $existingRate->service_id . '|' . $weightKey] = $existingRate;

                if (!$filterServiceId && $existingRate->service) {
                    $service = $existingRate->service;
                    foreach ([$service->service_code, $service->scode] as $code) {
                        $code = $normalizeValue($code);
                        if ($code !== '') {
                            $rateLookup['code:' . $existingRate->customer_id . '|' . $code . '|' . $weightKey] = $existingRate;
                        }
                    }
                    $network = $normalizeValue($service->network);
                    $method = $normalizeValue($service->method);
                    if ($network !== '' && $method !== '') {
                        $rateLookup['method:' . $existingRate->customer_id . '|' . $network . '|' . $method . '|' . $weightKey] = $existingRate;
                    }
                    // Country-scoped keys so same-code rows for different
                    // countries (e.g. AF vs AL clones) resolve to their own
                    // exact rate record instead of colliding on one key.
                    $svcCountry = strtoupper(trim((string) ($service->country ?? '')));
                    if ($svcCountry !== '') {
                        foreach ([$service->service_code, $service->scode] as $code) {
                            $code = $normalizeValue($code);
                            if ($code !== '') {
                                $rateLookup['ccode:' . $existingRate->customer_id . '|' . $svcCountry . '|' . $code . '|' . $weightKey] = $existingRate;
                            }
                        }
                        if ($network !== '' && $method !== '') {
                            $rateLookup['cmethod:' . $existingRate->customer_id . '|' . $svcCountry . '|' . $network . '|' . $method . '|' . $weightKey] = $existingRate;
                        }
                    }
                }
            }
            $pendingUpdates = [];

            foreach (array_slice($rows, $headerRowIndex + 1) as $row) {
                $wtStart = isset($row[$wtStartCol]) ? trim((string) $row[$wtStartCol]) : '';
                $wtEnd   = isset($row[$wtEndCol]) ? trim((string) $row[$wtEndCol]) : '';
                $zoneNo  = isset($row[$zoneNoCol]) ? trim((string) $row[$zoneNoCol]) : '';
                $price   = isset($row[$priceCol]) ? trim((string) $row[$priceCol]) : '';
                // Resolve customer via Customer Code first (new export format),
                // falling back to Customer ID (old files).
                $customerId = 0;
                if ($customerCodeCol !== null && isset($row[$customerCodeCol]) && trim((string) $row[$customerCodeCol]) !== '') {
                    $fileCode = strtoupper(trim((string) $row[$customerCodeCol]));
                    $customerId = $codeToIdAll[$fileCode] ?? 0;
                } elseif ($customerIdCol !== null && isset($row[$customerIdCol]) && trim((string) $row[$customerIdCol]) !== '') {
                    $customerId = (int) trim((string) $row[$customerIdCol]);
                }
                $serviceCode = $serviceCodeCol !== null && isset($row[$serviceCodeCol]) ? trim((string) $row[$serviceCodeCol]) : '';
                $network = $networkCol !== null && isset($row[$networkCol]) ? trim((string) $row[$networkCol]) : '';
                $method = $methodCol !== null && isset($row[$methodCol]) ? trim((string) $row[$methodCol]) : '';

                if ($wtStart === '' && $wtEnd === '' && $zoneNo === '' && $price === '') {
                    continue;
                }
                if (!is_numeric($wtStart) || !is_numeric($wtEnd) || !is_numeric($zoneNo) || !is_numeric($price)
                    || (float) $wtEnd <= (float) $wtStart || (int) $zoneNo < 0 || (int) $zoneNo > 13) {
                    $skipped++;
                    continue;
                }
                // NOTE: $validated['customer_ids'] comes from the request as
                // an array of STRINGS (e.g. ["3","17"]), while $customerId is
                // cast to int above. Using strict comparison (true) here made
                // in_array(3, ["3","17"], true) return false, causing EVERY
                // row to be skipped with "No matching customer rates found".
                // Cast the validated IDs to int so the comparison is reliable.
                // (Mismatch against other customers was already rejected in the
                // pre-pass above; this is a second guard incl. unknown codes.)
                $validatedCustomerIds = array_map('intval', $validated['customer_ids']);
                if ($customerId === 0 || !in_array($customerId, $validatedCustomerIds, true)
                    || (!$filterServiceId && $serviceCode === '' && ($network === '' || $method === ''))) {
                    $skipped++;
                    continue;
                }

                $weightKey = $normalizeNumber($wtStart) . '|'
                    . $normalizeNumber($wtEnd) . '|' . (int) $zoneNo;
                // File country (upper-trimmed). When filled, the row matches
                // its exact country clone; otherwise legacy behavior applies.
                $fileCountry = '';
                if ($fileCountryCol !== null && isset($row[$fileCountryCol])) {
                    $fileCountry = strtoupper(trim((string) $row[$fileCountryCol]));
                }
                $rate = null;
                if ($filterServiceId) {
                    $rate = $rateLookup['id:' . $customerId . '|' . $filterServiceId . '|' . $weightKey] ?? null;
                } elseif ($fileCountry !== '') {
                    $uploadedCode = $normalizeValue($serviceCode);
                    $rate = $uploadedCode !== ''
                        ? ($rateLookup['ccode:' . $customerId . '|' . $fileCountry . '|' . $uploadedCode . '|' . $weightKey] ?? null)
                        : null;
                    if (!$rate && $network !== '' && $method !== '') {
                        $rate = $rateLookup['cmethod:' . $customerId . '|' . $fileCountry . '|' . $normalizeValue($network) . '|' . $normalizeValue($method) . '|' . $weightKey] ?? null;
                    }
                } else {
                    $uploadedCode = $normalizeValue($serviceCode);
                    $rate = $uploadedCode !== ''
                        ? ($rateLookup['code:' . $customerId . '|' . $uploadedCode . '|' . $weightKey] ?? null)
                        : null;
                    if (!$rate && $network !== '' && $method !== '') {
                        $rate = $rateLookup['method:' . $customerId . '|' . $normalizeValue($network) . '|' . $normalizeValue($method) . '|' . $weightKey] ?? null;
                    }
                }

                if (!$rate) {
                    $notFound++;
                    continue;
                }

                $pendingUpdates[$rate->id] = [
                    'id'         => $rate->id,
                    'price'      => $price,
                    'start_date' => $validated['start_date'],
                    'end_date'   => $validated['end_date'],
                    'updated_at' => now(),
                ];
            }

            $updated = count($pendingUpdates);

            foreach (array_chunk(array_values($pendingUpdates), 500) as $chunk) {
                \DB::table('courier_rates')->upsert(
                    $chunk,
                    ['id'],
                    ['price', 'start_date', 'end_date', 'updated_at']
                );
            }
        });

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No matching customer rates were found in the uploaded file.',
                'skipped' => $skipped,
                'not_found' => $notFound,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $updated . ' rate(s) updated successfully.'
                . ($notFound ? ' ' . $notFound . ' row(s) did not match an existing rate.' : '')
                . ($skipped ? ' ' . $skipped . ' invalid row(s) skipped.' : ''),
            'updated' => $updated,
            'skipped' => $skipped,
            'not_found' => $notFound,
        ]);
    }

    /**
     * Add a new default rate (customer_id = 0) for a given service.
     *
     * The admin selects a country (which filters the service list) and a
     * service, then provides the weight range, zone number and price.
     * Optional fuel/GST fields default to 0 when left blank.
     *
     * A duplicate guard prevents creating two default rates for the exact
     * same (service_id, wt_range_start, wt_range_end, zone_no) combination.
     */
    public function addRate(Request $request)
    {
        $validated = $request->validate([
            'service_id'      => 'required|integer|exists:courier_services,id',
            'wt_range_start'  => 'required|numeric|min:0',
            'wt_range_end'    => 'required|numeric|min:0|gt:wt_range_start',
            'zone_no'         => 'nullable|integer|min:0|max:13',
            'price'           => 'required|numeric|min:0',
            'fuel_charge'     => 'nullable|numeric|min:0',
            'fuel_percentage' => 'nullable|numeric|min:0',
            'gst_percentage'  => 'nullable|numeric|min:0',
            'surcharge_id'    => 'nullable|array',
            'surcharge_id.*'  => 'integer|exists:sur_charges,id',
        ]);

        // Normalize surcharge selections into a JSON-friendly list of ids.
        $surchargeIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) ($validated['surcharge_id'] ?? [])
        ))));

        // Zone-less countries submit an empty zone_no. Normalize it to null
        // so the rate is stored as a zone-independent default rate.
        $zoneNo = $validated['zone_no'] ?? null;
        if ($zoneNo === '' || $zoneNo === null) {
            $zoneNo = null;
        } else {
            $zoneNo = (int) $zoneNo;
        }

        // Guard against duplicate default rates for the same service+weight+zone.
        $exists = \App\Models\CourierRate::where('customer_id', 0)
            ->where('service_id', $validated['service_id'])
            ->where('wt_range_start', $validated['wt_range_start'])
            ->where('wt_range_end', $validated['wt_range_end'])
            ->where(function ($q) use ($zoneNo) {
                if ($zoneNo === null) {
                    $q->whereNull('zone_no');
                } else {
                    $q->where('zone_no', $zoneNo);
                }
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A default rate already exists for this service, weight range and zone.',
            ], 409);
        }

        $customerCount = 0;

        DB::transaction(function () use ($validated, $zoneNo, $surchargeIds, &$rate, &$customerCount) {
            $rate = \App\Models\CourierRate::create([
                'customer_id'     => 0,
                'service_id'      => $validated['service_id'],
                'wt_range_start'  => $validated['wt_range_start'],
                'wt_range_end'    => $validated['wt_range_end'],
                'zone_no'         => $zoneNo,
                'price'           => $validated['price'],
                'fuel_charge'     => $validated['fuel_charge'] ?? 0,
                'fuel_percentage' => $validated['fuel_percentage'] ?? 0,
                'gst_percentage'  => $validated['gst_percentage'] ?? 0,
                'gst_amount'      => 0,
                'surcharge_id'    => $surchargeIds,
                'is_default'      => true,
                'start_date'      => '2026-01-01',
                'end_date'        => '2026-12-31',
            ]);

            // Propagate the new default rate to every customer so they inherit
            // it (is_default = 1) until an admin customizes their own rate.
            // Customers who already have a rate for this service + weight +
            // zone (inherited or customized) are skipped so we never clobber
            // a customized rate.
            $customerIds = \App\Models\Customer::pluck('id')->toArray();

            if (!empty($customerIds)) {
                $customersWithRate = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
                    ->where('service_id', $validated['service_id'])
                    ->where('wt_range_start', $validated['wt_range_start'])
                    ->where('wt_range_end', $validated['wt_range_end'])
                    ->where(function ($q) use ($zoneNo) {
                        if ($zoneNo === null) {
                            $q->whereNull('zone_no');
                        } else {
                            $q->where('zone_no', $zoneNo);
                        }
                    })
                    ->pluck('customer_id')
                    ->toArray();

                $customersNeedingRate = array_values(array_diff($customerIds, $customersWithRate));
                $customerCount = count($customersNeedingRate);

                if (!empty($customersNeedingRate)) {
                    $now = now();
                    $rows = array_map(function ($customerId) use ($validated, $zoneNo, $now, $surchargeIds) {
                        return [
                            'customer_id'     => $customerId,
                            'service_id'      => $validated['service_id'],
                            'wt_range_start'  => $validated['wt_range_start'],
                            'wt_range_end'    => $validated['wt_range_end'],
                            'zone_no'         => $zoneNo,
                            'price'           => $validated['price'],
                            'fuel_charge'     => $validated['fuel_charge'] ?? 0,
                            'fuel_percentage' => $validated['fuel_percentage'] ?? 0,
                            'gst_percentage'  => $validated['gst_percentage'] ?? 0,
                            'gst_amount'      => 0,
                            'surcharge_id'    => json_encode($surchargeIds),
                            'is_default'      => true,
                            'start_date'      => '2026-01-01',
                            'end_date'        => '2026-12-31',
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }, $customersNeedingRate);

                    foreach (array_chunk($rows, 500) as $chunk) {
                        \App\Models\CourierRate::insert($chunk);
                    }
                }
            }
        });

        return response()->json([
            'success'        => true,
            'message'        => 'Default rate added successfully' . ($customerCount > 0 ? ' and propagated to ' . $customerCount . ' customer(s).' : '.'),
            'rate_id'        => $rate->id,
            'propagated_to'  => $customerCount,
        ]);
    }

    /**
     * Download a vertical sample (Weight Start, Weight End, Zone No, Price,
     * Fuel Charge, Fuel %, GST %) or a plain sample for a no-zone country.
     *
     * Existing default rates are pre-filled where available.
     */
    public function downloadRateSample(Request $request)
    {
        // NOTE: input() reads query string + POST body, so both the legacy
        // GET link and the bulk-modal POST form (used for 100+ countries to
        // avoid Apache "414 Request-URI Too Long") work here.
        $serviceId = $request->input('service_id');
        $country = $request->input('country');
        $withoutZone = $request->boolean('without_zone');

        // ---- Multi-country sample: all checked (service, country) targets ----
        // Frontend sends service_ids[] + countries[] (aligned pairs, same as
        // upload). When present, the sample contains a Country first column
        // with one row-block per selected country.
        $multiServiceIds = array_values(array_filter(array_map('intval', (array) $request->input('service_ids', []))));
        $multiCountries = array_values(array_filter(array_map(function ($c) { return trim((string) $c); }, (array) $request->input('countries', []))));
        $multiTargets = [];
        if (!empty($multiServiceIds) || !empty($multiCountries)) {
            $paired = (count($multiServiceIds) === count($multiCountries));
            foreach ($multiServiceIds as $idx => $sid) {
                $cc = $paired ? ($multiCountries[$idx] ?? null) : null;
                if (!$cc) {
                    $cc = \App\Models\CourierService::whereKey($sid)->value('country');
                }
                if (!$cc) {
                    continue;
                }
                $multiTargets[] = ['service_id' => (int) $sid, 'country' => $cc];
            }
            if (!empty($multiTargets)) {
                return $this->downloadMultiCountryRateSample($multiTargets, $request);
            }
        }

        $destinationId = $this->destinationIdForCountry($country);

        // Many courier_services.country values (e.g. "France", "China") have
        // no matching destinations row. For those, fall back to a
        // destination-less sample as long as the sent country matches the
        // service's own country string (case-insensitive). Zones are then
        // treated as unconfigured (without-zone format works).
        if ($country && !$destinationId) {
            if (!$serviceId) {
                abort(422, 'The selected country "' . $country . '" is not recognized.');
            }
            $svcCountry = \App\Models\CourierService::whereKey($serviceId)->value('country');
            if (strtolower(trim((string) $svcCountry)) !== strtolower(trim((string) $country))) {
                abort(422, 'The selected country "' . $country . '" is not recognized.');
            }
            $destinationId = null;
        }

        if ($serviceId && $destinationId && !$this->serviceBelongsToDestination($serviceId, $destinationId)) {
            abort(422, 'The selected service does not belong to the selected country.');
        }

        if ($withoutZone) {
            $hasConfiguredZones = $destinationId
                ? ($serviceId
                    ? $this->serviceHasConfiguredZones((int) $serviceId, $destinationId)
                    : $this->destinationHasConfiguredZones($destinationId))
                : false;
            if ($hasConfiguredZones) {
                abort(422, 'The without-zone sample is available only for a selected country (and service) that has no configured zones.');
            }
        }

        $zoneNos = $request->input('zone_nos', []);
        $zoneNos = is_array($zoneNos) ? $zoneNos : [$zoneNos];
        $zoneNos = array_values(array_unique(array_filter(array_map('intval', $zoneNos), function ($zone) {
            return $zone >= 0 && $zone <= 13;
        })));

        // Retain the historical sample only when no explicit no-zone mode was requested.
        if (empty($zoneNos) && !$withoutZone) {
            $zoneNos = [1, 2];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Vertical format: one row per (weight, zone) with a Zone No column.
        $headers = ['Weight Start', 'Weight End', 'Zone No', 'Price', 'Fuel Charge', 'Fuel %', 'GST %'];
        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        $row = 2;
        $sampleZoneNos = $withoutZone ? [0] : $zoneNos;
        foreach ($sampleZoneNos as $zoneNo) {
            $rateRows = [];
            if ($serviceId) {
                $rates = \App\Models\CourierRate::where('customer_id', 0)
                    ->where('service_id', $serviceId)
                    ->where('zone_no', $zoneNo)
                    ->orderBy('wt_range_start')
                    ->get();
                foreach ($rates as $rate) {
                    $rateRows[] = [
                        'start' => $rate->wt_range_start,
                        'end'   => $rate->wt_range_end,
                        'rate'  => $rate,
                    ];
                }
            }
            if (empty($rateRows)) {
                foreach ([[0.5, 1.0], [1.0, 2.0], [2.0, 3.0]] as $range) {
                    $rateRows[] = ['start' => $range[0], 'end' => $range[1], 'rate' => null];
                }
            }
            foreach ($rateRows as $rateRow) {
                $rate = $rateRow['rate'];
                $values = [$rateRow['start'], $rateRow['end'], $zoneNo];
                $values = array_merge($values, $rate
                    ? [$rate->price, $rate->fuel_charge, $rate->fuel_percentage, $rate->gst_percentage]
                    : ['', '', '', '']);
                foreach ($values as $colIdx => $value) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                    $sheet->setCellValue($columnLetter . $row, $value);
                }
                $row++;
            }
        }

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        foreach (range(1, count($headers)) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $fileName = $withoutZone
            ? 'rate-upload-sample-without-zone.xlsx'
            : 'rate-upload-sample.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Multi-country sample in vertical format: Country, Weight Start,
     * Weight End, Zone No, Price, Fuel Charge, Fuel %, GST %. One row per
     * (country, weight, zone) with example rows pre-filled per zone.
     * Countries without any configured zones get example rows with a null
     * (empty) Zone No instead of per-zone rows. Existing default rates are
     * pre-filled where available. The upload parser reads the Country +
     * Zone No columns and applies each row only to its matching target
     * (files without the Country column are still replicated to every
     * target as before).
     */
    private function downloadMultiCountryRateSample(array $targets, Request $request)
    {
        $withoutZone = $request->boolean('without_zone');
        $zoneNos = $request->input('zone_nos', []);
        $zoneNos = is_array($zoneNos) ? $zoneNos : [$zoneNos];
        $zoneNos = array_values(array_unique(array_filter(array_map('intval', $zoneNos), function ($zone) {
            return $zone >= 0 && $zone <= 13;
        })));
        if (empty($zoneNos) && !$withoutZone) {
            $zoneNos = [1, 2];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Vertical format: one row per (country, weight, zone) with a Zone No
        // column. Zones are listed vertically with example rows pre-filled.
        $headers = ['Country', 'Weight Start', 'Weight End', 'Zone No', 'Price', 'Fuel Charge', 'Fuel %', 'GST %'];
        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        $row = 2;
        foreach ($targets as $target) {
            $sid = (int) $target['service_id'];
            $cc = trim((string) $target['country']);
            $sampleZoneNos = $withoutZone ? [0] : $zoneNos;
            // Countries without any configured zones (in the selected zone
            // set) get example rows with a null (empty) Zone No instead of
            // per-zone rows. On upload those rows import as zone-free (0).
            $sampleDestId = $this->destinationIdForCountry($cc);
            $sampleHasZones = $withoutZone ? true : false;
            if (!$withoutZone && $sampleDestId) {
                $sampleHasZones = \App\Models\Zone::where('destination_id', $sampleDestId)
                    ->whereIn('zone_number_testing', $zoneNos)
                    ->where(function ($q) use ($sid) {
                        $q->whereNull('service_id')->orWhere('service_id', $sid);
                    })
                    ->exists();
            }
            if (!$withoutZone && !$sampleHasZones) {
                foreach ([[0.5, 1.0], [1.0, 2.0], [2.0, 3.0]] as $range) {
                    $values = [$cc, $range[0], $range[1], '', '', '', '', ''];
                    foreach ($values as $colIdx => $value) {
                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $sheet->setCellValue($columnLetter . $row, $value);
                    }
                    $row++;
                }
                continue;
            }
            foreach ($sampleZoneNos as $zoneNo) {
                $rates = \App\Models\CourierRate::where('customer_id', 0)
                    ->where('service_id', $sid)
                    ->where('zone_no', $zoneNo)
                    ->orderBy('wt_range_start')
                    ->get();
                $rateRows = [];
                foreach ($rates as $rate) {
                    $rateRows[] = [
                        'start' => $rate->wt_range_start,
                        'end'   => $rate->wt_range_end,
                        'rate'  => $rate,
                    ];
                }
                if (empty($rateRows)) {
                    foreach ([[0.5, 1.0], [1.0, 2.0], [2.0, 3.0]] as $range) {
                        $rateRows[] = ['start' => $range[0], 'end' => $range[1], 'rate' => null];
                    }
                }
                foreach ($rateRows as $rateRow) {
                    $rate = $rateRow['rate'];
                    $values = [$cc, $rateRow['start'], $rateRow['end'], $zoneNo];
                    $values = array_merge($values, $rate
                        ? [$rate->price, $rate->fuel_charge, $rate->fuel_percentage, $rate->gst_percentage]
                        : ['', '', '', '']);
                    foreach ($values as $colIdx => $value) {
                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $sheet->setCellValue($columnLetter . $row, $value);
                    }
                    $row++;
                }
            }
        }

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        foreach (range(1, count($headers)) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $fileName = $withoutZone
            ? 'rate-upload-sample-without-zone.xlsx'
            : 'rate-upload-sample.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Bulk-import default rates from zoned, zone-free, or legacy vertical files.
     *
     * Zone-free rows use zone 0. Duplicate detection remains based on service,
     * weight range, and zone number.
     */
    public function uploadRateExcel(Request $request)
    {
        // Supports BOTH legacy single-target (service_id + country) and the
        // new service-first multi-country flow (service_ids[] + countries[]).
        // The frontend sends one service_ids[] + countries[] pair per checked
        // country. Rows with a Country column go only to their matching
        // target (empty Zone No on a zone-less target imports as zone-free);
        // rows without it are replicated to every target.
        $validated = $request->validate([
            'service_id'   => 'nullable|integer|exists:courier_services,id',
            'service_ids'  => 'nullable|array|min:1',
            'service_ids.*' => 'integer|exists:courier_services,id',
            'country'      => 'nullable|string|max:100',
            'countries'    => 'nullable|array|min:1',
            'countries.*'  => 'string|max:100',
            'service_key'  => 'nullable|string|max:255',
            'without_zone' => 'nullable|boolean',
            'zone_nos'     => 'nullable|array',
            'zone_nos.*'   => 'integer|min:0|max:13',
            'zone_no'      => 'nullable|integer|min:0|max:13',
            'rate_file'    => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $withoutZone = $request->boolean('without_zone');

        // ---- Build the list of (service_id, country, destination_id) targets ----
        $targets = [];
        $multiServiceIds = array_values(array_filter(array_map('intval', (array) $request->input('service_ids', []))));
        $multiCountries = array_values(array_filter(array_map(function ($c) { return trim((string) $c); }, (array) $request->input('countries', []))));

        if (!empty($multiServiceIds) || !empty($multiCountries)) {
            if (empty($multiServiceIds) || empty($multiCountries)) {
                return redirect()->route('admin.manage-rate')
                    ->with('error', 'Please select a service and at least one country.');
            }
            // Pair by index when both arrays are given (frontend sends them
            // aligned). If counts differ, fall back to resolving each service
            // ID's own country.
            // NOTE: many services (e.g. "France", "China") have no destinations
            // row — those targets get destination_id = null and are treated as
            // zone-less (proven by frontend union logic). They are allowed as
            // long as the sent country matches the service's own country.
            $paired = (count($multiServiceIds) === count($multiCountries));
            foreach ($multiServiceIds as $idx => $sid) {
                $countryCode = $paired ? $multiCountries[$idx] : null;
                if (!$countryCode) {
                    $countryCode = \App\Models\CourierService::whereKey($sid)->value('country');
                }
                if (!$countryCode) {
                    return redirect()->route('admin.manage-rate')
                        ->with('error', 'Could not determine the country for a selected service.');
                }
                $destId = $this->destinationIdForCountry($countryCode);
                if (!$destId) {
                    $svcCountry = \App\Models\CourierService::whereKey($sid)->value('country');
                    if (strtolower(trim((string) $svcCountry)) !== strtolower(trim((string) $countryCode))) {
                        return redirect()->route('admin.manage-rate')
                            ->with('error', 'The selected country "' . $countryCode . '" is not recognized.');
                    }
                    $targets[] = ['service_id' => (int) $sid, 'country' => $countryCode, 'destination_id' => null];
                    continue;
                }
                if (!$this->serviceBelongsToDestination($sid, $destId)) {
                    return redirect()->route('admin.manage-rate')
                        ->with('error', 'The selected service does not belong to country "' . $countryCode . '". Please re-select the service and countries.');
                }
                $targets[] = ['service_id' => (int) $sid, 'country' => $countryCode, 'destination_id' => $destId];
            }
            // De-duplicate identical targets (same service twice).
            $seen = [];
            $targets = array_values(array_filter($targets, function ($t) use (&$seen) {
                $k = $t['service_id'] . '|' . strtolower($t['country']);
                if (isset($seen[$k])) return false;
                $seen[$k] = true;
                return true;
            }));
        } else {
            // Legacy single-target flow.
            if (empty($validated['service_id']) || empty($validated['country'])) {
                return redirect()->route('admin.manage-rate')
                    ->with('error', 'Please select a service and at least one country.');
            }
            $destinationId = $this->destinationIdForCountry($validated['country']);
            if (!$destinationId) {
                $svcCountry = \App\Models\CourierService::whereKey($validated['service_id'])->value('country');
                if (strtolower(trim((string) $svcCountry)) !== strtolower(trim((string) $validated['country']))) {
                    return redirect()
                        ->route('admin.manage-rate')
                        ->with('error', 'The selected country "' . $validated['country'] . '" is not recognized.');
                }
                $targets[] = ['service_id' => (int) $validated['service_id'], 'country' => $validated['country'], 'destination_id' => null];
            } else {
                if (!$this->serviceBelongsToDestination($validated['service_id'], $destinationId)) {
                    return redirect()
                        ->route('admin.manage-rate')
                        ->with('error', 'The selected service does not belong to the selected country.');
                }
                $targets[] = ['service_id' => (int) $validated['service_id'], 'country' => $validated['country'], 'destination_id' => $destinationId];
            }
        }

        if ($withoutZone) {
            foreach ($targets as $t) {
                if ($t['destination_id'] && $this->serviceHasConfiguredZones($t['service_id'], $t['destination_id'])) {
                    return redirect()
                        ->route('admin.manage-rate')
                        ->with('error', 'Country "' . $t['country'] . '" has configured zones for this service. Select at least one zone and use the zoned sample file.');
                }
            }
        }

        $file = $request->file('rate_file');
        $filePath = $file->getRealPath();

        try {
            // Detect the file type and load the spreadsheet.
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'Could not read the uploaded file. Please ensure it is a valid Excel/CSV file.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'The file is empty or has no data rows (only a header was found).');
        }

        // The first row is the header. Find each column by matching header
        // text (case-insensitive) so the admin does not have to worry about
        // exact column order.
        $header = array_map(function ($h) {
            // Normalize: lowercase, trim and collapse internal whitespace.
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $h)));
        }, $rows[0]);

        $wtStartHeaders    = ['weight start', 'wt start', 'wt_range_start', 'weightstart', 'start weight', 'from weight', 'min weight', 'weight from'];
        $wtEndHeaders      = ['weight end', 'wt end', 'wt_range_end', 'weightend', 'end weight', 'to weight', 'max weight', 'weight to'];
        $zoneNoHeaders     = ['zone no', 'zone_no', 'zoneno', 'zone number', 'zone'];
        $priceHeaders      = ['price', 'rate', 'amount', 'cost'];
        $fuelChargeHeaders = ['fuel charge', 'fuel_charge', 'fuelcharge', 'fuel'];
        $fuelPctHeaders    = ['fuel %', 'fuel percentage', 'fuel_percentage', 'fuelpercentage', 'fuel pct', 'fuel_pct'];
        $gstPctHeaders     = ['gst %', 'gst percentage', 'gst_percentage', 'gstpercentage', 'gst pct', 'gst_pct', 'gst'];

        $wtStartCol    = null;
        $wtEndCol      = null;
        $zoneNoCol     = null;
        $priceCol      = null;
        $fuelChargeCol = null;
        $fuelPctCol    = null;
        $gstPctCol     = null;
        // Multi-country samples carry a Country first column. When present,
        // each row is applied only to its matching (service, country) target
        // instead of being replicated to every target.
        $countryHeaders = ['country', 'country_code', 'country code', 'destination'];
        $countryCol = null;
        $horizontalGroups = [];

        foreach ($header as $idx => $h) {
            if ($countryCol === null && in_array($h, $countryHeaders, true)) {
                $countryCol = $idx;
            }
            if ($wtStartCol === null && in_array($h, $wtStartHeaders, true)) {
                $wtStartCol = $idx;
            }
            if ($wtEndCol === null && in_array($h, $wtEndHeaders, true)) {
                $wtEndCol = $idx;
            }
            if ($zoneNoCol === null && in_array($h, $zoneNoHeaders, true)) {
                $zoneNoCol = $idx;
            }
            if ($priceCol === null && in_array($h, $priceHeaders, true)) {
                $priceCol = $idx;
            }
            if ($fuelChargeCol === null && in_array($h, $fuelChargeHeaders, true)) {
                $fuelChargeCol = $idx;
            }
            if ($fuelPctCol === null && in_array($h, $fuelPctHeaders, true)) {
                $fuelPctCol = $idx;
            }
            if ($gstPctCol === null && in_array($h, $gstPctHeaders, true)) {
                $gstPctCol = $idx;
            }

            // Horizontal sample columns are normalized below into the legacy
            // seven-column row format, so the existing import/propagation code
            // remains shared by both formats.
            if (preg_match('/^zone\s*(\d+)\s+(price|fuel charge|fuel %|gst %)$/', $h, $matches)) {
                $horizontalGroups[(int) $matches[1]][$matches[2]] = $idx;
            }
        }

        $selectedZoneNos = array_values(array_unique(array_map('intval', $validated['zone_nos'] ?? [])));
        if ($withoutZone) {
            $selectedZoneNos = [0];
        } elseif (empty($selectedZoneNos) && isset($validated['zone_no']) && $validated['zone_no'] !== null) {
            $selectedZoneNos = [(int) $validated['zone_no']];
        }

        $isHorizontal = $wtStartCol !== null && $wtEndCol !== null && !empty($horizontalGroups);
        if ($withoutZone && $isHorizontal) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'A country without zones requires the without-zone sample file with plain Price, Fuel Charge, Fuel %, and GST % columns.');
        }
        if ($isHorizontal) {
            $normalizedRows = [$rows[0]];
            foreach (array_slice($rows, 1) as $sourceRow) {
                $sourceCountry = $countryCol !== null ? ($sourceRow[$countryCol] ?? '') : '';
                foreach ($horizontalGroups as $zoneNumber => $columns) {
                    // If zones were selected during upload, ignore every other
                    // horizontal zone group, including Zone 3 in a Zone 1/2 upload.
                    if (!empty($selectedZoneNos) && !in_array($zoneNumber, $selectedZoneNos, true)) {
                        continue;
                    }
                    $normalizedRows[] = [
                        $sourceRow[$wtStartCol] ?? '',
                        $sourceRow[$wtEndCol] ?? '',
                        $zoneNumber,
                        $sourceRow[$columns['price'] ?? -1] ?? '',
                        $sourceRow[$columns['fuel charge'] ?? -1] ?? '',
                        $sourceRow[$columns['fuel %'] ?? -1] ?? '',
                        $sourceRow[$columns['gst %'] ?? -1] ?? '',
                        $sourceCountry,
                    ];
                }
            }
            $rows = $normalizedRows;
            $wtStartCol = 0;
            $wtEndCol = 1;
            $zoneNoCol = 2;
            $priceCol = 3;
            $fuelChargeCol = 4;
            $fuelPctCol = 5;
            $gstPctCol = 6;
            $countryCol = 7;
        }

        // Weight Start, Weight End and Price are required for vertical files;
        // horizontal files have one Price column inside each zone group.
        if ($wtStartCol === null || $wtEndCol === null || (!$isHorizontal && $priceCol === null)) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'The file must contain Weight Start and Weight End plus either Price or horizontal Zone N Price columns. Please download the sample file for the correct format.');
        }

        if (!$withoutZone && !$isHorizontal && $zoneNoCol === null && empty($selectedZoneNos)) {
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', 'Select at least one zone, or choose a country without configured zones and use its without-zone sample file.');
        }

        $created = 0;
        $skipped = 0;
        $duplicates = 0;
        $dataRows = array_slice($rows, 1);

        // Pre-fetch all customer IDs once (shared across every target).
        $customerIds = \App\Models\Customer::pluck('id')->toArray();
        $propagated = 0;

        // Replicate the same file to EVERY selected (service, country)
        // target. Invalid rows are counted once (first target only);
        // duplicates/created are aggregated across all targets.
        $isFirstTarget = true;
        foreach ($targets as $target) {
            $currentServiceId = (int) $target['service_id'];

            // Pre-fetch all existing default rates for this service so we can
            // skip duplicates without running a query per row. Keys are
            // "wtStart|wtEnd|zoneNo" for fast lookup.
            $existingRates = \App\Models\CourierRate::where('customer_id', 0)
                ->where('service_id', $currentServiceId)
                ->get(['wt_range_start', 'wt_range_end', 'zone_no']);

        $existingKeys = [];
        foreach ($existingRates as $r) {
            $existingZoneNo = (int) ($r->zone_no ?? 0);
            $key = $r->wt_range_start . '|' . $r->wt_range_end . '|' . $existingZoneNo;
            $existingKeys[$key] = true;
        }

        // Track keys seen within this upload too, so the same combination
        // appearing twice in the same file is only inserted once.
        $seenInUpload = [];

        $formZoneNo = $withoutZone ? 0 : ($validated['zone_no'] ?? null);

        // Map of "wtStart|wtEnd|zoneNo" => [customerId => true] for every
        // customer rate that already exists for THIS target service.
        $existingCustomerKeys = [];
        if (!empty($customerIds)) {
            $existingCustomerRates = \App\Models\CourierRate::whereIn('customer_id', $customerIds)
                ->where('service_id', $currentServiceId)
                ->get(['customer_id', 'wt_range_start', 'wt_range_end', 'zone_no']);
            foreach ($existingCustomerRates as $cr) {
                $existingZoneNo = (int) ($cr->zone_no ?? 0);
                $k = $cr->wt_range_start . '|' . $cr->wt_range_end . '|' . $existingZoneNo;
                $existingCustomerKeys[$k][$cr->customer_id] = true;
            }
        }

        // Track customer rates we create during this upload so we don't
        // duplicate-propagate when the same combination appears twice in
        // the file.
        $propagatedInUpload = [];

        // Multi-country file: when THIS target has no configured zones (in
        // the selected zone set), rows with an empty Zone No import as
        // zone-free (0) instead of being skipped. Zoned targets keep the
        // strict behavior (empty zone = invalid row).
        $targetNoZone = false;
        if (!$withoutZone && $countryCol !== null) {
            $targetDestId = $target['destination_id'] ?? null;
            if (!$targetDestId) {
                $targetNoZone = true;
            } else {
                $zoneScope = !empty($selectedZoneNos) ? $selectedZoneNos : null;
                $zoneCheck = \App\Models\Zone::where('destination_id', $targetDestId);
                if ($zoneScope) {
                    $zoneCheck->whereIn('zone_number_testing', $zoneScope);
                }
                $zoneCheck->where(function ($qq) use ($currentServiceId) {
                    $qq->whereNull('service_id')->orWhere('service_id', $currentServiceId);
                });
                $targetNoZone = !$zoneCheck->exists();
            }
        }

        foreach ($dataRows as $row) {
            $wtStart = isset($row[$wtStartCol]) ? trim((string) $row[$wtStartCol]) : '';
            $wtEnd   = isset($row[$wtEndCol]) ? trim((string) $row[$wtEndCol]) : '';
            $price   = isset($row[$priceCol]) ? trim((string) $row[$priceCol]) : '';

            // Zone-free workbooks always map to zone 0. Zoned formats prefer
            // the file column and fall back to the modal's legacy zone value.
            $zoneNo = $withoutZone
                ? '0'
                : (($zoneNoCol !== null && isset($row[$zoneNoCol]) && trim((string) $row[$zoneNoCol]) !== '')
                    ? trim((string) $row[$zoneNoCol])
                    : ($formZoneNo !== null ? (string) $formZoneNo : ''));

            $fuelCharge = ($fuelChargeCol !== null && isset($row[$fuelChargeCol])) ? trim((string) $row[$fuelChargeCol]) : '';
            $fuelPct    = ($fuelPctCol !== null && isset($row[$fuelPctCol])) ? trim((string) $row[$fuelPctCol]) : '';
            $gstPct     = ($gstPctCol !== null && isset($row[$gstPctCol])) ? trim((string) $row[$gstPctCol]) : '';

            // Multi-country file: this row belongs to a specific country —
            // process it only for its matching target, silently skipping the
            // other targets (without counting it as skipped).
            if ($countryCol !== null) {
                $rowCountry = isset($row[$countryCol]) ? strtoupper(trim((string) $row[$countryCol])) : '';
                $targetCountry = strtoupper(trim((string) $target['country']));
                if ($rowCountry !== '' && $rowCountry !== $targetCountry) {
                    continue;
                }
            }

            // No-zone target: an empty (null) Zone No means zone-free (0).
            // Zoned targets keep the strict behavior below (empty = invalid).
            if ($zoneNo === '' && $targetNoZone) {
                $zoneNo = '0';
            }

            // Skip completely empty rows.
            // $skipped is counted on the first target only so multi-country
            // uploads don't multiply the same invalid rows.
            if ($wtStart === '' && $wtEnd === '' && $price === '' && $zoneNo === '') {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }

            // Validate required numeric fields.
            if ($wtStart === '' || $wtEnd === '' || $price === '') {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }
            if (!is_numeric($wtStart) || !is_numeric($wtEnd) || !is_numeric($price)) {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }
            if ((float) $wtEnd <= (float) $wtStart) {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }

            // Zone No must be valid and must be one of the zones explicitly
            // checked in the modal. This also filters legacy vertical files.
            if ($zoneNo === '' || !is_numeric($zoneNo) || (int) $zoneNo < 0 || (int) $zoneNo > 13) {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }
            $zoneNoInt = (int) $zoneNo;

            // Zone must be one of the zones explicitly checked in the modal.
            // Exception: zone 0 rows of a zone-less target (empty Zone No in
            // the sample) are always allowed — they import as zone-free.
            if (
                !empty($selectedZoneNos)
                && !in_array($zoneNoInt, $selectedZoneNos, true)
                && !($zoneNoInt === 0 && $targetNoZone)
            ) {
                if ($countryCol !== null || $isFirstTarget) $skipped++;
                continue;
            }

            $key = $wtStart . '|' . $wtEnd . '|' . $zoneNoInt;

            // Skip duplicates (already in DB or already in this upload).
            if (isset($existingKeys[$key]) || isset($seenInUpload[$key])) {
                $duplicates++;
                continue;
            }

            \App\Models\CourierRate::create([
                'customer_id'     => 0,
                'service_id'      => $currentServiceId,
                'wt_range_start'  => $wtStart,
                'wt_range_end'    => $wtEnd,
                'zone_no'         => $zoneNoInt,
                'price'           => $price,
                'fuel_charge'     => ($fuelCharge !== '' && is_numeric($fuelCharge)) ? $fuelCharge : 0,
                'fuel_percentage' => ($fuelPct !== '' && is_numeric($fuelPct)) ? $fuelPct : 0,
                'gst_percentage'  => ($gstPct !== '' && is_numeric($gstPct)) ? $gstPct : 0,
                'gst_amount'      => 0,
                'is_default'      => true,
                'start_date'      => '2026-01-01',
                'end_date'        => '2026-12-31',
            ]);
            $seenInUpload[$key] = true;
            $created++;

            // Propagate this new default rate to every customer that does
            // not already have a rate for this service + weight + zone
            // combination (inherited or customized).
            if (!empty($customerIds)) {
                $alreadyHave = $existingCustomerKeys[$key] ?? [];
                $propagatedHave = $propagatedInUpload[$key] ?? [];
                $customersNeedingRate = array_values(array_filter($customerIds, function ($cid) use ($alreadyHave, $propagatedHave) {
                    return !isset($alreadyHave[$cid]) && !isset($propagatedHave[$cid]);
                }));

                if (!empty($customersNeedingRate)) {
                    $now = now();
                    $rows = array_map(function ($customerId) use ($currentServiceId, $wtStart, $wtEnd, $zoneNoInt, $price, $fuelCharge, $fuelPct, $gstPct, $now) {
                        return [
                            'customer_id'     => $customerId,
                            'service_id'      => $currentServiceId,
                            'wt_range_start'  => $wtStart,
                            'wt_range_end'    => $wtEnd,
                            'zone_no'         => $zoneNoInt,
                            'price'           => $price,
                            'fuel_charge'     => ($fuelCharge !== '' && is_numeric($fuelCharge)) ? $fuelCharge : 0,
                            'fuel_percentage' => ($fuelPct !== '' && is_numeric($fuelPct)) ? $fuelPct : 0,
                            'gst_percentage'  => ($gstPct !== '' && is_numeric($gstPct)) ? $gstPct : 0,
                            'gst_amount'      => 0,
                            'is_default'      => true,
                            'start_date'      => '2026-01-01',
                            'end_date'        => '2026-12-31',
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }, $customersNeedingRate);

                    foreach (array_chunk($rows, 500) as $chunk) {
                        \App\Models\CourierRate::insert($chunk);
                    }

                    foreach ($customersNeedingRate as $cid) {
                        $propagatedInUpload[$key][$cid] = true;
                    }
                    $propagated += count($customersNeedingRate);
                }
            }
        }
        $isFirstTarget = false;
        } // end foreach ($targets as $target)

        $targetCount = count($targets);
        $targetSuffix = $targetCount > 1 ? ' across ' . $targetCount . ' countries' : '';

        if ($created === 0) {
            $msg = 'No new rates were imported' . $targetSuffix . '.';
            if ($duplicates > 0) {
                $msg .= ' ' . $duplicates . ' duplicate rate(s) already exist and were skipped.';
            }
            if ($skipped > 0) {
                $msg .= ' ' . $skipped . ' invalid/empty row(s) skipped.';
            }
            return redirect()
                ->route('admin.manage-rate')
                ->with('error', $msg);
        }

        $msg = $created . ' rate(s) imported successfully' . $targetSuffix . '.';
        if ($duplicates > 0) {
            $msg .= ' ' . $duplicates . ' duplicate rate(s) already existed and were skipped.';
        }
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' invalid/empty row(s) skipped.';
        }
        if ($propagated > 0) {
            $msg .= ' ' . $propagated . ' customer rate(s) propagated.';
        }

        return redirect()
            ->route('admin.manage-rate')
            ->with('success', $msg);
    }

    private function destinationIdForCountry(?string $country): ?int
    {
        $key = strtolower(trim((string) $country));
        if ($key === '') {
            return null;
        }

        $destinationId = \App\Models\Destination::where(function ($query) use ($key) {
            $query->whereRaw('LOWER(country_code) = ?', [$key])
                ->orWhereRaw('LOWER(code) = ?', [$key])
                ->orWhereRaw('LOWER(name) = ?', [$key]);
        })->value('id');

        if ($destinationId) {
            return (int) $destinationId;
        }

        // Fallback for common code/name variants: courier_services.country
        // stores short codes that don't always equal destinations.country_code
        // (e.g. service "USA" vs destination "US", "AUS" vs "AU", "UK" vs
        // "GB"/"United Kingdom"). Resolve via alias groups so the bulk
        // sample/upload flow (which sends service-country strings) keeps
        // working for multi-country selections.
        $aliasGroups = [
            ['us', 'usa', 'united states', 'united states of america', 'america'],
            ['uk', 'gb', 'gbr', 'united kingdom', 'great britain', 'britain', 'england'],
            ['ca', 'can', 'canada'],
            ['au', 'aus', 'australia'],
            ['de', 'deu', 'germany'],
            ['fr', 'fra', 'france'],
            ['in', 'ind', 'india'],
            ['ae', 'are', 'uae', 'united arab emirates', 'dubai'],
            ['sg', 'sgp', 'singapore'],
            ['nz', 'nzl', 'new zealand'],
            ['za', 'zaf', 'south africa'],
        ];

        foreach ($aliasGroups as $group) {
            if (!in_array($key, $group, true)) {
                continue;
            }
            $found = \App\Models\Destination::where(function ($query) use ($group) {
                foreach ($group as $alias) {
                    $query->orWhereRaw('LOWER(country_code) = ?', [$alias])
                        ->orWhereRaw('LOWER(code) = ?', [$alias])
                        ->orWhereRaw('LOWER(name) = ?', [$alias]);
                }
            })->value('id');
            if ($found) {
                return (int) $found;
            }
        }

        return null;
    }

    private function destinationHasConfiguredZones(int $destinationId): bool
    {
        return \App\Models\Zone::where('destination_id', $destinationId)->exists();
    }

    /**
     * Determine whether a service has configured zones for a destination.
     *
     * Zones are either service-specific (zone.service_id = $serviceId) or
     * shared across every service of the destination (service_id IS NULL).
     * The service therefore has zones if either kind exists.
     */
    private function serviceHasConfiguredZones(int $serviceId, int $destinationId): bool
    {
        return \App\Models\Zone::where('destination_id', $destinationId)
            ->where(function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId)
                    ->orWhereNull('service_id');
            })
            ->exists();
    }

    private function serviceBelongsToDestination(int $serviceId, int $destinationId): bool
    {
        $serviceCountry = \App\Models\CourierService::whereKey($serviceId)->value('country');

        return $this->destinationIdForCountry($serviceCountry) === $destinationId;
    }

    /**
     * Show the "Add Zone" page.
     *
     * Loads all destinations (countries) so the admin can pick one, then
     * choose a zone category (state / zipcode / city), and finally enter one
     * or more zone entries (zone name, zone code, zone number) for that
     * country + category combination.
     */
    public function addZone()
    {
        $destinations = \App\Models\Destination::orderBy('name')->get();
        $services = \App\Models\CourierService::orderBy('api_provider')
            ->orderBy('service_code')
            ->orderBy('country')
            ->get();

        // Service dropdown source (same query as manage-rate / add-country):
        //   SELECT DISTINCT api_provider, service_code FROM `courier_services`
        $serviceOptions = \Illuminate\Support\Facades\DB::select(
            'SELECT DISTINCT api_provider, service_code FROM `courier_services` ORDER BY api_provider, service_code'
        );

        // Build a service_id => destination_id map so the view can filter the
        // Service dropdown by the selected Country without extra DB queries.
        // A service is matched to a destination by its `country` string against
        // the destination's country_code / code / name (same logic as
        // destinationIdForCountry()).
        $destinationIdByCountryKey = collect();
        foreach ($destinations as $dest) {
            foreach ([$dest->country_code, $dest->code, $dest->name] as $key) {
                $destinationIdByCountryKey[strtolower(trim((string) $key))] = $dest->id;
            }
        }

        $serviceDestMap = [];
        foreach ($services as $svc) {
            $serviceDestMap[$svc->id] = $destinationIdByCountryKey[strtolower(trim((string) $svc->country))] ?? null;
        }

        // Coverage map for the DISTINCT dropdown: "api||code" (lower-cased)
        // => [destination_ids...]. The frontend shows only the pairs that
        // exist for the selected country.
        $serviceCoverage = [];
        foreach ($services as $svc) {
            $key = strtolower(trim((string) $svc->api_provider)) . '||' . strtolower(trim((string) $svc->service_code));
            if ($key === '||') {
                continue;
            }
            $destId = $destinationIdByCountryKey[strtolower(trim((string) $svc->country))] ?? null;
            if ($destId && !in_array($destId, $serviceCoverage[$key] ?? [], true)) {
                $serviceCoverage[$key][] = $destId;
            }
        }

        // Hero stats for the Add Zone page header (cheap aggregate queries).
        $zoneStats = [
            'total'     => \App\Models\Zone::count(),
            'countries' => \App\Models\Destination::count(),
            'services'  => \App\Models\CourierService::selectRaw('COUNT(DISTINCT api_provider, service_code) as cnt')->value('cnt') ?? 0,
        ];

        return view('admin.add-zone', compact('destinations', 'services', 'serviceDestMap', 'serviceOptions', 'serviceCoverage', 'zoneStats'));
    }

    /**
     * Return the list of zones (as JSON) for a selected country and an
     * optional service.
     *
     * Used by the "Zone List" tab on the Add Zone page. When a service is
     * selected, both the service-specific zones (zone.service_id matches)
     * and the shared zones (service_id IS NULL) are returned, matching the
     * semantics used everywhere else in the rate system.
     */
    /**
     * Resolve a DISTINCT service group ("api_provider||service_code") to the
     * exact courier_services clone for a destination.
     *
     * The add-zone dropdowns list DISTINCT pairs (same query as manage-rate),
     * while zones/rates reference per-country clone rows. Since the country
     * is always selected first on this page, the pair maps to exactly one
     * clone (first by id when duplicates exist). A legacy single service_id
     * is accepted as-is for backward compatibility.
     *
     * @return int|null The clone service id, or null when unavailable.
     */
    private function resolveZoneServiceId(?string $serviceKey, $serviceIdFallback, int $destinationId): ?int
    {
        if (!empty($serviceIdFallback)
            && \App\Models\CourierService::whereKey((int) $serviceIdFallback)->exists()) {
            return (int) $serviceIdFallback;
        }
        if (empty($serviceKey) || !str_contains($serviceKey, '||')) {
            return null;
        }
        [$api, $code] = explode('||', $serviceKey, 2);
        $candidates = \App\Models\CourierService::whereRaw('LOWER(api_provider) = ?', [strtolower(trim((string) $api))])
            ->whereRaw('LOWER(service_code) = ?', [strtolower(trim((string) $code))])
            ->orderBy('id')
            ->get();
        foreach ($candidates as $svc) {
            if ($this->destinationIdForCountry($svc->country) === $destinationId) {
                return (int) $svc->id;
            }
        }
        return null;
    }

    /**
     * Expand a DISTINCT service group ("api_provider||service_code") to every
     * clone service id that belongs to a destination (for the Zone List tab).
     *
     * @return int[]
     */
    private function expandZoneServiceIds(?string $serviceKey, int $destinationId): array
    {
        if (empty($serviceKey) || !str_contains($serviceKey, '||')) {
            return [];
        }
        [$api, $code] = explode('||', $serviceKey, 2);
        $ids = [];
        $candidates = \App\Models\CourierService::whereRaw('LOWER(api_provider) = ?', [strtolower(trim((string) $api))])
            ->whereRaw('LOWER(service_code) = ?', [strtolower(trim((string) $code))])
            ->orderBy('id')
            ->get(['id', 'country']);
        foreach ($candidates as $svc) {
            if ($this->destinationIdForCountry($svc->country) === $destinationId) {
                $ids[] = (int) $svc->id;
            }
        }
        return $ids;
    }

    public function listZones(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'service_id'     => 'nullable|integer|exists:courier_services,id',
            'service_key'    => 'nullable|string|max:255',
            'search'         => 'nullable|string|max:100',
            'category'       => 'nullable|in:all,state,zipcode,city',
            'per_page'       => 'nullable|integer|min:10|max:200',
            'all'            => 'nullable|boolean',
        ]);

        // DISTINCT group (service_key) expands to every clone of that pair
        // for the destination; a legacy single service_id still works.
        $serviceIds = [];
        if (!empty($validated['service_id'])) {
            $serviceIds[] = (int) $validated['service_id'];
        }
        if (!empty($validated['service_key'])) {
            $serviceIds = array_merge(
                $serviceIds,
                $this->expandZoneServiceIds($validated['service_key'], (int) $validated['destination_id'])
            );
        }
        $serviceIds = array_values(array_unique($serviceIds));

        // Base query: this destination + (service-specific OR shared) zones.
        // Cloned per use so counts, pagination and export share one scope.
        $baseQuery = function () use ($validated, $serviceIds) {
            return \App\Models\Zone::where('destination_id', $validated['destination_id'])
                ->when(!empty($serviceIds), function ($q) use ($serviceIds) {
                    $q->where(function ($query) use ($serviceIds) {
                        $query->whereIn('service_id', $serviceIds)
                            ->orWhereNull('service_id');
                    });
                });
        };

        // Per-category totals for the chips (ignores search + category filter).
        $categoryCounts = ['all' => 0, 'state' => 0, 'zipcode' => 0, 'city' => 0];
        foreach ((clone $baseQuery())->selectRaw('zone_category, COUNT(*) as cnt')->groupBy('zone_category')->get() as $row) {
            $cat = strtolower((string) $row->zone_category);
            $categoryCounts['all'] += (int) $row->cnt;
            if (isset($categoryCounts[$cat])) {
                $categoryCounts[$cat] += (int) $row->cnt;
            }
        }

        $query = $baseQuery()->with('service:id,method,service_code');

        // Server-side text search (zone name / code, plus exact zone number).
        $search = trim((string) ($validated['search'] ?? ''));
        if ($search !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
            $query->where(function ($q) use ($like, $search) {
                $q->where('zone_name', 'LIKE', $like)
                    ->orWhere('zone_code', 'LIKE', $like);
                if (is_numeric($search)) {
                    $q->orWhere('zone_number_testing', (int) $search);
                }
            });
        }

        // Server-side category filter.
        $category = strtolower((string) ($validated['category'] ?? 'all'));
        if ($category !== '' && $category !== 'all') {
            $query->where('zone_category', $category);
        }

        $query->orderBy('zone_category')->orderBy('zone_name');

        $mapZone = function ($z) {
            $serviceName = 'All Services';
            if ($z->service) {
                $serviceName = $z->service->method ?? ('Service #' . $z->service->id);
                if (!empty($z->service->service_code)) {
                    $serviceName .= ' (' . $z->service->service_code . ')';
                }
            }

            return [
                'id'            => $z->id,
                'zone_name'     => $z->zone_name,
                'zone_code'     => $z->zone_code,
                'zone_category' => ucfirst((string) $z->zone_category),
                'zone_number'   => $z->zone_number_testing,
                'service_name'  => $serviceName,
            ];
        };

        // Export mode (CSV button): full filtered list, no pagination.
        if ($request->boolean('all')) {
            return response()->json([
                'zones'           => $query->get()->map($mapZone)->values(),
                'total'           => $query->count(),
                'category_counts' => $categoryCounts,
            ]);
        }

        $perPage = (int) ($validated['per_page'] ?? 50);
        $page = max(1, (int) $request->input('page', 1));
        $paginator = $query->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        return response()->json([
            'zones'           => collect($paginator->items())->map($mapZone)->values(),
            'total'           => $paginator->total(),
            'per_page'        => $paginator->perPage(),
            'current_page'    => $paginator->currentPage(),
            'last_page'       => $paginator->lastPage(),
            'from'            => $paginator->firstItem(),
            'to'              => $paginator->lastItem(),
            'category_counts' => $categoryCounts,
        ]);
    }

    /**
     * Store one or more new zone entries submitted from the Add Zone page.
     *
     * The form submits:
     *   - destination_id : the selected country
     *   - zone_category  : state | zipcode | city
     *   - zone_number    : the zone number (0-13) that rates reference
     *   - entries        : array of { zone_name, zone_code } pairs
     *
     * Each entry creates a new row in the `zone` table linked to the chosen
     * destination and category. zone_number_testing is set to the supplied
     * zone_number so the new zones are immediately usable by the rate system.
     *
     * A single zone_name is allowed to have MULTIPLE zone_codes (e.g. a state
     * can be referenced by several codes). Therefore a repeated zone_name is
     * NOT treated as a duplicate. A row is only skipped when its zone_code
     * already exists or when the exact (zone_name + zone_code) pair already
     * exists.
     *
     * CODE UNIQUENESS SCOPE:
     *   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for every
     *   category (state, zipcode, city). The same code may legitimately exist
     *   in different countries (e.g. "AL" for Alabama (US) and Albania, or the
     *   postcode prefix "SW1" in UK and another country). A code is only a
     *   duplicate when it already exists within the same selected country +
     *   category.
     */
    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'zone_category'  => 'required|in:state,zipcode,city',
            'zone_number'    => 'required|integer|min:0|max:13',
            'service_id'     => 'nullable|integer|exists:courier_services,id',
            // DISTINCT pair from the service-first dropdown
            // (api_provider||service_code, same query as manage-rate).
            'service_key'    => 'nullable|string|max:255',
            'entries'        => 'required|array|min:1',
            'entries.*.zone_name' => 'required|string|max:100',
            'entries.*.zone_code' => 'nullable|string|max:10',
        ]);

        // The dropdown posts a DISTINCT pair; resolve it to the exact clone
        // for the selected country (a legacy single service_id still works).
        $serviceId = $this->resolveZoneServiceId(
            $validated['service_key'] ?? null,
            $validated['service_id'] ?? null,
            (int) $validated['destination_id']
        );
        if (!$serviceId) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'The selected service is not available for the selected country. Please re-select the service and country.');
        }

        $created = 0;
        $skipped = 0;

        // Pre-fetch existing zone codes AND (zone_name + zone_code) pairs for
        // this country + category (lower-cased keys for case-insensitive
        // duplicate detection) so we can skip duplicates without running a
        // query per entry.
        //
        // A single zone_name may have multiple zone_codes, so we no longer
        // build a name-only duplicate map. We track codes (which must stay
        // unique) and exact (name + code) pairs (to avoid identical rows).
        //
        // CODE UNIQUENESS SCOPE:
        //   zone_code uniqueness is scoped to the SELECTED COUNTRY +
        //   SELECTED SERVICE only, for every category (state, zipcode, city).
        //   The same code may exist in different countries or under different
        //   services; it is only a duplicate within the same selected country
        //   + category + service.
        $existingZones = \App\Models\Zone::where('destination_id', $validated['destination_id'])
            ->where('zone_category', $validated['zone_category'])
            ->when(!empty($serviceId), function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            }, function ($q) {
                $q->whereNull('service_id');
            })
            ->get(['zone_name', 'zone_code']);

        $existingCodes = [];
        $existingPairs = [];
        foreach ($existingZones as $z) {
            $n = strtolower(trim((string) $z->zone_name));
            $c = strtolower(trim((string) $z->zone_code));
            if ($c !== '') {
                $existingCodes[$c] = true;
            }
            $existingPairs[$n . '|' . $c] = true;
        }

        // Track codes AND (name + code) pairs added within this same
        // submission too, so the same value appearing twice in the form is
        // only inserted once.
        $seenCodes = [];
        $seenPairs = [];

        // Normalize the case of a zone name so that "DELHI", "Delhi" and
        // "delhi" (or "MANGAWHAI", "Mangawhai", "mangawai") all collapse to a
        // single consistent Title Case form ("Delhi", "Mangawhai"). This
        // applies to EVERY category (state, zipcode, city) because the
        // zone_name column always holds a place name, never a raw postcode.
        // The actual postcode is stored in zone_code (handled below) and is
        // always upper-cased. The `zone` table uses a case-insensitive
        // collation (utf8mb4_general_ci) so the rate-system lookup still
        // matches regardless of stored case.
        $normalizeName = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        };
        $normalizeCode = function ($value) {
            $value = trim((string) $value);
            return $value !== '' ? mb_strtoupper($value, 'UTF-8') : '';
        };

        foreach ($validated['entries'] as $entry) {
            $name = $normalizeName($entry['zone_name']);
            $code = $normalizeCode($entry['zone_code'] ?? '');

            $nameKey = strtolower($name);
            $codeKey = $code !== '' ? strtolower($code) : '';
            $pairKey = $nameKey . '|' . $codeKey;

            // A zone_name may have multiple zone_codes, so a repeated name is
            // NOT a duplicate. Skip only when the exact (name + code) pair
            // already exists, or when a non-empty zone_code already exists
            // (codes must stay unique because the rate system looks them up).
            if (isset($existingPairs[$pairKey]) || isset($seenPairs[$pairKey])) {
                $skipped++;
                continue;
            }
            if ($codeKey !== '' && (isset($existingCodes[$codeKey]) || isset($seenCodes[$codeKey]))) {
                $skipped++;
                continue;
            }

            \App\Models\Zone::create([
                'destination_id'      => $validated['destination_id'],
                'service_id'          => $serviceId,
                'zone_category'       => $validated['zone_category'],
                'zone_number'         => $validated['zone_number'],
                'zone_number_testing' => $validated['zone_number'],
                'zone_name'           => $name,
                'zone_code'           => $code,
            ]);
            $seenPairs[$pairKey] = true;
            if ($codeKey !== '') {
                $seenCodes[$codeKey] = true;
            }
            $created++;
        }

        $msg = $created . ' zone(s) added successfully.';
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' duplicate zone(s) (name or code already existed) were skipped.';
        }

        return redirect()
            ->route('admin.add-zone')
            ->with($created > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Download a sample Excel file showing the expected format for bulk
     * zone uploads.
     *
     * The sample contains a header row (Zone Name, Zone Code). If a
     * destination_id (and optionally zone_category) is passed as a query
     * parameter, the existing zones for that country/category are also
     * included so the admin can see which zones are already present (and
     * avoid duplicating them on re-upload). The file is generated on the fly
     * with PhpSpreadsheet and streamed back as a download (.xlsx).
     */
    public function downloadZoneSample(Request $request)
    {
        $destinationId = $request->query('destination_id');
        $zoneCategory  = $request->query('zone_category', 'state');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Zone Name');
        $sheet->setCellValue('B1', 'Zone Code');

        $row = 2;

        // If a country is selected, include the zones that already exist for
        // that country + category so the admin can see what's already there.
        if ($destinationId) {
            $existingZones = \App\Models\Zone::where('destination_id', $destinationId)
                ->where('zone_category', $zoneCategory)
                ->orderBy('zone_name')
                ->get();

            foreach ($existingZones as $z) {
                $sheet->setCellValue('A' . $row, $z->zone_name);
                $sheet->setCellValue('B' . $row, $z->zone_code ?: '');
                $row++;
            }
        }

        // If no existing zones were found (or no country was selected), fall
        // back to a few example rows so the file is not empty.
        if ($row === 2) {
            $samples = [
                ['New South Wales', 'NSW'],
                ['Victoria',        'VIC'],
                ['Queensland',      'QLD'],
                ['Western Australia', 'WA'],
                ['South Australia', 'SA'],
            ];
            foreach ($samples as $s) {
                $sheet->setCellValue('A' . $row, $s[0]);
                $sheet->setCellValue('B' . $row, $s[1]);
                $row++;
            }
        }

        // Bold the header row and auto-size columns
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $fileName = 'zone-upload-sample.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Bulk-import zones from an uploaded Excel file.
     *
     * The admin first selects a Country, Zone Category and Zone Number on
     * the Add Zone page, then uploads an .xlsx/.xls/.csv file. The file must
     * have a header row with "Zone Name" (required) and "Zone Code"
     * (optional). Each data row creates a new zone entry linked to the
     * chosen country + category + zone number.
     *
     * A single zone_name is allowed to have MULTIPLE zone_codes (e.g. a state
     * can be referenced by several codes). Therefore a repeated zone_name is
     * NOT treated as a duplicate. A row is only skipped when its zone_code
     * already exists or when the exact (zone_name + zone_code) pair already
     * exists.
     *
     * CODE UNIQUENESS SCOPE:
     *   zone_code uniqueness is scoped to the SELECTED COUNTRY only, for every
     *   category (state, zipcode, city). The same code may legitimately exist
     *   in different countries (e.g. "AL" for Alabama (US) and Albania, or the
     *   postcode prefix "SW1" in UK and another country). A code is only a
     *   duplicate when it already exists within the same selected country +
     *   category.
     */
    public function uploadZoneExcel(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'zone_category'  => 'required|in:state,zipcode,city',
            'zone_number'    => 'required|integer|min:0|max:13',
            'service_id'     => 'nullable|integer|exists:courier_services,id',
            // DISTINCT pair from the service-first dropdown
            // (api_provider||service_code, same query as manage-rate).
            'service_key'    => 'nullable|string|max:255',
            'zone_file'      => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // The dropdown posts a DISTINCT pair; resolve it to the exact clone
        // for the selected country (a legacy single service_id still works).
        $serviceId = $this->resolveZoneServiceId(
            $validated['service_key'] ?? null,
            $validated['service_id'] ?? null,
            (int) $validated['destination_id']
        );
        if (!$serviceId) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'The selected service is not available for the selected country. Please re-select the service and country.');
        }

        $file = $request->file('zone_file');
        $filePath = $file->getRealPath();

        try {
            // Detect the file type and load the spreadsheet.
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'Could not read the uploaded file. Please ensure it is a valid Excel/CSV file.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'The file is empty or has no data rows (only a header was found).');
        }

        // The first row is the header. Find the Zone Name and Zone Code
        // columns by matching header text (case-insensitive) so the admin
        // does not have to worry about exact column order. Each column is
        // detected INDEPENDENTLY so that a file which only has a "Zone Name"
        // or only a "Zone Code" column is mapped to the correct database
        // column (zone_name / zone_code) instead of being forced into the
        // first two positions.
        $header = array_map(function ($h) {
            // Normalize: lowercase, trim and collapse internal whitespace so
            // "Zone  Name", "ZONE NAME" and "Zone_Name" all match the same key.
            return preg_replace('/\s+/', ' ', strtolower(trim((string) $h)));
        }, $rows[0]);

        $nameHeaders = ['zone name', 'zone_name', 'zonename', 'name', 'state', 'zipcode', 'zip', 'city', 'zone'];
        $codeHeaders = ['zone code', 'zone_code', 'zonecode', 'code'];

        $nameCol = null;
        $codeCol = null;
        foreach ($header as $idx => $h) {
            if ($nameCol === null && in_array($h, $nameHeaders, true)) {
                $nameCol = $idx;
            }
            if ($codeCol === null && in_array($h, $codeHeaders, true)) {
                $codeCol = $idx;
            }
        }

        // Fallback ONLY when NEITHER column was recognized by its header:
        // assume the first column is the zone name and the second (if
        // present) is the zone code. This keeps backward compatibility for
        // headerless files while no longer clobbering a correctly-detected
        // "Zone Code" column.
        if ($nameCol === null && $codeCol === null) {
            $nameCol = 0;
            $codeCol = isset($header[1]) ? 1 : null;
        } elseif ($nameCol === null) {
            // Only the code column was recognized. Use the first available
            // column that is not the code column as the name column.
            $nameCol = ($codeCol === 0 && isset($header[1])) ? 1 : 0;
        }
        // If only the name column was recognized, codeCol stays null (no code).

        $created = 0;
        $skipped = 0;
        $duplicates = 0;
        $dataRows = array_slice($rows, 1);

        // Collect every row that is NOT inserted (empty, duplicate, or
        // duplicate code) so the admin can download them afterwards and see
        // exactly which rows were skipped and why. Each entry stores the
        // original (raw) name + code from the file plus a human-readable
        // reason. This array is flashed to the session at the end.
        $skippedRows = [];

        // Pre-fetch existing zone codes AND (zone_name + zone_code) pairs for
        // this country + category so we can skip duplicates without running a
        // query per row. Keys are lower-cased values for case-insensitive
        // comparison.
        //
        // A single zone_name may have multiple zone_codes, so we no longer
        // build a name-only duplicate map. We track codes (which must stay
        // unique) and exact (name + code) pairs (to avoid identical rows).
        //
        // CODE UNIQUENESS SCOPE:
        //   zone_code uniqueness is scoped to the SELECTED COUNTRY +
        //   SELECTED SERVICE only, for every category (state,zipcode, city).
        //   The same code may exist in different countries or under different
        //   services; it is only a duplicate within the same selected country
        //   + category + service.
        $existingZones = \App\Models\Zone::where('destination_id', $validated['destination_id'])
            ->where('zone_category', $validated['zone_category'])
            ->when(!empty($serviceId), function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            }, function ($q) {
                $q->whereNull('service_id');
            })
            ->get(['zone_name', 'zone_code']);

        $existingCodes = [];
        $existingPairs = [];
        foreach ($existingZones as $z) {
            $n = strtolower(trim((string) $z->zone_name));
            $c = strtolower(trim((string) $z->zone_code));
            if ($c !== '') {
                $existingCodes[$c] = true;
            }
            $existingPairs[$n . '|' . $c] = true;
        }

        // Track codes AND (name + code) pairs seen within this upload too,
        // so the same value appearing twice in the same file is only inserted
        // once.
        $seenCodesInUpload = [];
        $seenPairsInUpload = [];

        // Normalize the case of a zone name so that "DELHI", "Delhi" and
        // "delhi" (or "MANGAWHAI", "Mangawhai", "mangawai") all collapse to a
        // single consistent Title Case form ("Delhi", "Mangawhai"). This
        // applies to EVERY category (state, zipcode, city) because the
        // zone_name column always holds a place name, never a raw postcode.
        // The actual postcode is stored in zone_code (handled below) and is
        // always upper-cased. The `zone` table uses a case-insensitive
        // collation (utf8mb4_general_ci) so the rate-system lookup still
        // matches regardless of stored case.
        $normalizeName = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        };
        $normalizeCode = function ($value) {
            $value = trim((string) $value);
            return $value !== '' ? mb_strtoupper($value, 'UTF-8') : '';
        };

        foreach ($dataRows as $row) {
            $rawName = ($nameCol !== null && isset($row[$nameCol])) ? trim((string) $row[$nameCol]) : '';
            $rawCode = ($codeCol !== null && isset($row[$codeCol])) ? trim((string) $row[$codeCol]) : '';

            // Skip completely empty rows (neither a name nor a code present).
            if ($rawName === '' && $rawCode === '') {
                $skipped++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Empty row (no zone name and no zone code)',
                ];
                continue;
            }

            // The `zone` table requires a zone_name. If the file only had a
            // "Zone Code" column (no Zone Name), fall back to using the code
            // as the name so the row can still be imported. The fallback name
            // is normalized the same way as a regular name.
            $effectiveName = $rawName !== '' ? $normalizeName($rawName) : $normalizeName($rawCode);
            $zoneCode      = $normalizeCode($rawCode);

            $nameKey = strtolower($effectiveName);
            $codeKey = $zoneCode !== '' ? strtolower($zoneCode) : '';
            $pairKey = $nameKey . '|' . $codeKey;

            // A zone_name may have multiple zone_codes, so a repeated name is
            // NOT a duplicate. Skip only when the exact (name + code) pair
            // already exists, or when a non-empty zone_code already exists
            // (codes must stay unique because the rate system looks them up).
            if (isset($existingPairs[$pairKey]) || isset($seenPairsInUpload[$pairKey])) {
                $duplicates++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Duplicate zone name + zone code pair already exists',
                ];
                continue;
            }
            if ($codeKey !== '' && (isset($existingCodes[$codeKey]) || isset($seenCodesInUpload[$codeKey]))) {
                $duplicates++;
                $skippedRows[] = [
                    'zone_name' => $rawName,
                    'zone_code' => $rawCode,
                    'reason'    => 'Duplicate zone code already exists in this country',
                ];
                continue;
            }

            \App\Models\Zone::create([
                'destination_id'      => $validated['destination_id'],
                'service_id'          => $serviceId,
                'zone_category'       => $validated['zone_category'],
                'zone_number'         => $validated['zone_number'],
                'zone_number_testing' => $validated['zone_number'],
                'zone_name'           => mb_substr($effectiveName, 0, 100),
                'zone_code'           => mb_substr($zoneCode, 0, 10),
            ]);
            $seenPairsInUpload[$pairKey] = true;
            if ($codeKey !== '') {
                $seenCodesInUpload[$codeKey] = true;
            }
            $created++;
        }

        // Flash the skipped rows to the session so the admin can download
        // them as an Excel file from the Add Zone page. Only flash when there
        // is at least one skipped row, otherwise the download button is not
        // shown.
        if (!empty($skippedRows)) {
            session()->flash('skipped_zone_rows', $skippedRows);
        }

        if ($created === 0) {
            $msg = 'No new zones were imported.';
            if ($duplicates > 0) {
                $msg .= ' ' . $duplicates . ' duplicate zone(s) already exist and were skipped.';
            }
            if ($skipped > 0) {
                $msg .= ' ' . $skipped . ' empty row(s) skipped.';
            }
            if (!empty($skippedRows)) {
                $msg .= ' You can download the skipped records below.';
            }
            return redirect()
                ->route('admin.add-zone')
                ->with('error', $msg);
        }

        $msg = $created . ' zone(s) imported successfully.';
        if ($duplicates > 0) {
            $msg .= ' ' . $duplicates . ' duplicate zone(s) already existed and were skipped.';
        }
        if ($skipped > 0) {
            $msg .= ' ' . $skipped . ' empty row(s) skipped.';
        }
        if (!empty($skippedRows)) {
            $msg .= ' You can download the skipped records below.';
        }

        return redirect()
            ->route('admin.add-zone')
            ->with('success', $msg);
    }

    /**
     * Download the zones that were skipped during the last bulk Excel
     * upload as an .xlsx file.
     *
     * When uploadZoneExcel() skips rows (empty rows, duplicate name+code
     * pairs, or duplicate zone codes within the selected country), it
     * flashes them to the session under 'skipped_zone_rows'. This method
     * reads that flash data, builds an Excel with three columns
     * (Zone Name, Zone Code, Reason) and streams it back as a download.
     * The session key is forgotten afterwards so the same file is not
     * downloaded again on a later visit.
     */
    public function downloadSkippedZones(Request $request)
    {
        $skippedRows = session('skipped_zone_rows');

        if (empty($skippedRows)) {
            // There is nothing to download. This happens when the user hits
            // the URL/button without a fresh upload, or after the file was
            // already downloaded once (the session key is cleared below).
            // Show a helpful message instead of a confusing one.
            return redirect()
                ->route('admin.add-zone')
                ->with('error', 'There are no skipped records available to download right now. Please upload a zone Excel file that contains some skipped (e.g. duplicate) rows first.');
        }

        // Capture the data and clear the flash key so it cannot be
        // downloaded again on a later visit.
        session()->forget('skipped_zone_rows');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Zone Name');
        $sheet->setCellValue('B1', 'Zone Code');
        $sheet->setCellValue('C1', 'Reason');

        $row = 2;
        foreach ($skippedRows as $entry) {
            $sheet->setCellValue('A' . $row, $entry['zone_name'] ?? '');
            $sheet->setCellValue('B' . $row, $entry['zone_code'] ?? '');
            $sheet->setCellValue('C' . $row, $entry['reason'] ?? '');
            $row++;
        }

        // Bold the header row and auto-size columns
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $fileName = 'skipped-zones.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Show the "Country & Services" page (service-first flow).
     *
     * New flow (reversed): the admin FIRST picks a courier service
     * (template row from courier_services), THEN picks one or more
     * countries from a dropdown. On submit each selected country is
     * "added into" that service — i.e. the template service row is
     * cloned with its `country` column set to the selected country's
     * code, so the service becomes available for rate calculation
     * against that destination. The original service row is untouched.
     *
     * Missing destinations are auto-created so zones/rates keep working.
     */
    public function addCountry()
    {
        $destinations = \App\Models\Destination::orderBy('name')->get();

        // Step 1 dropdown source (as requested):
        //   SELECT DISTINCT api_provider, service_code FROM `courier_services`
        // 23 grouped services instead of 127 per-country rows.
        $serviceOptions = \Illuminate\Support\Facades\DB::select(
            'SELECT DISTINCT api_provider, service_code FROM `courier_services` ORDER BY api_provider, service_code'
        );

        // Coverage map keyed by "api_provider||service_code" => [country codes...].
        // Plus representative meta per key so the UI can preview without AJAX.
        $coverageMap = [];
        $serviceMeta = [];
        foreach ($serviceOptions as $opt) {
            $api = $opt->api_provider ?? '';
            $scode = $opt->service_code ?? '';
            $key = $api . '||' . $scode;

            $rows = \App\Models\CourierService::where('api_provider', $api)
                ->where('service_code', $scode)
                ->orderBy('id')
                ->get();

            $countries = [];
            foreach ($rows as $r) {
                $c = strtoupper(trim((string) $r->country));
                if ($c !== '') {
                    $countries[] = $c;
                }
            }
            $countries = array_values(array_unique($countries));
            sort($countries);
            $coverageMap[$key] = $countries;

            $rep = $rows->first();
            $methods = $rows->pluck('method')->filter()->unique()->values()->take(3)->toArray();
            $serviceMeta[$key] = [
                'api_provider' => $api,
                'service_code' => $scode,
                'method'       => $rep ? $rep->method : null,
                'network'      => $rep ? $rep->network : null,
                'status'       => $rep ? (int) $rep->status : 1,
                'total_rows'   => $rows->count(),
                'methods'      => $methods,
            ];
        }

        // Full per-country rows, only for the overview table count/context.
        $courierServices = \App\Models\CourierService::orderBy('api_provider')
            ->orderBy('service_code')
            ->orderBy('country')
            ->get();

        return view('admin.add-country', compact('destinations', 'courierServices', 'coverageMap', 'serviceMeta', 'serviceOptions'));
    }

    /**
     * Store countries INTO a service (service-first flow).
     *
     * New flow: `service_id` (template) + `country_codes[]` (one or more
     * destination codes). For each code we ensure the destination exists
     * (auto-create when missing) and clone the template service row with
     * `country` set to that code. Duplicates (same method + service_code +
     * country) are skipped.
     *
     * Legacy fallback: old form posted `name` + `code` + `service_ids[]`
     * (country-first). Still supported so old tabs/bookmarks keep working.
     */
    public function storeCountry(Request $request)
    {
        // ---- Service-first flow (DISTINCT api_provider + service_code) ----
        // Step 1 posts `service_key` = "api_provider||service_code".
        // (Old `service_id` single-row flow still accepted as fallback.)
        if ($request->filled('service_key') || $request->filled('service_id') || $request->has('country_codes')) {
            $validated = $request->validate([
                'service_key'     => 'nullable|string|max:255',
                'service_id'      => 'nullable|integer|exists:courier_services,id',
                'country_codes'   => 'required|array|min:1|max:500',
                'country_codes.*' => 'required|string|max:10',
            ]);
            if (empty($validated['service_key']) && empty($validated['service_id'])) {
                return redirect()->route('admin.add-country')
                    ->with('error', 'Please select a service first.');
            }

            // Resolve template + canonical api_provider/service_code pair.
            if (!empty($validated['service_key']) && str_contains($validated['service_key'], '||')) {
                [$apiProvider, $serviceCode] = explode('||', $validated['service_key'], 2);
                $template = \App\Models\CourierService::where('api_provider', $apiProvider)
                    ->where('service_code', $serviceCode)
                    ->orderBy('id')
                    ->first();
                if (!$template) {
                    return redirect()->route('admin.add-country')
                        ->with('error', 'Selected service not found.');
                }
            } else {
                $template = \App\Models\CourierService::findOrFail($validated['service_id']);
                $apiProvider = $template->api_provider;
                $serviceCode = $template->service_code;
            }

            // Normalise codes: upper-case, trimmed, de-duplicated.
            $codes = [];
            foreach ((array) $validated['country_codes'] as $c) {
                $c = strtoupper(trim((string) $c));
                if ($c !== '' && !in_array($c, $codes, true)) {
                    $codes[] = $c;
                }
            }
            if (empty($codes)) {
                return redirect()->route('admin.add-country')
                    ->with('error', 'Please select at least one country.');
            }

            $created = 0;
            $skipped = [];
            $destCreated = 0;

            foreach ($codes as $code) {
                // Ensure the destination exists (match code / ISO / name).
                $dest = \App\Models\Destination::whereRaw('UPPER(code) = ?', [$code])
                    ->orWhereRaw('UPPER(country_code) = ?', [$code])
                    ->orWhereRaw('UPPER(name) = ?', [$code])
                    ->first();
                if (!$dest) {
                    // Auto-create a minimal destination so zones/rates work.
                    $dest = \App\Models\Destination::create([
                        'name'         => ucwords(strtolower($code)),
                        'code'         => $code,
                        'country_code' => $code,
                        'is_active'    => true,
                    ]);
                    $destCreated++;
                }
                $targetCountry = strtoupper(trim((string) ($dest->country_code ?: $dest->code)));

                // Duplicate check on the DISTINCT key: same api_provider +
                // service_code already live for this country? Skip it.
                $exists = \App\Models\CourierService::where('api_provider', $apiProvider)
                    ->where('service_code', $serviceCode)
                    ->whereRaw('UPPER(country) = ?', [$targetCountry])
                    ->exists();
                if ($exists) {
                    $skipped[] = $dest->name . ' (' . $targetCountry . ')';
                    continue;
                }

                $data = $template->getAttributes();
                unset($data['id']);
                $data['api_provider'] = $apiProvider;
                $data['service_code'] = $serviceCode;
                $data['country'] = $targetCountry;
                \App\Models\CourierService::create($data);
                $created++;
            }

            $svcLabel = ($apiProvider ? $apiProvider . ' — ' : '') . $serviceCode;
            if ($created === 0) {
                $msg = 'No new countries added — "' . $svcLabel . '" already covers: ' . implode(', ', $skipped) . '.';
                return redirect()->route('admin.add-country')->with('error', $msg);
            }
            $msg = $created . ' countr' . ($created === 1 ? 'y' : 'ies') . ' added to "' . $svcLabel . '" successfully.';
            if (!empty($skipped)) {
                $msg .= ' Skipped (already added): ' . implode(', ', $skipped) . '.';
            }
            if ($destCreated > 0) {
                $msg .= ' ' . $destCreated . ' new destination(s) auto-created.';
            }
            return redirect()->route('admin.add-country')->with('success', $msg);
        }

        // ---- Legacy country-first fallback (old form) ----
        $validated = $request->validate([
            'name'           => 'required|string|max:150|unique:destinations,name',
            'code'           => 'nullable|string|max:10|unique:destinations,code',
            'country_code'   => 'nullable|string|max:5',
            'is_active'      => 'nullable|boolean',
            'service_ids'    => 'nullable|array',
            'service_ids.*'  => 'integer|exists:courier_services,id',
        ]);

        $code = $validated['code'] ?? '';
        if ($code === '') {
            $words = preg_split('/\s+/', trim($validated['name']));
            if (count($words) === 1) {
                $code = strtoupper(substr($words[0], 0, 3));
            } else {
                $code = '';
                foreach ($words as $w) {
                    $code .= strtoupper(substr($w, 0, 1));
                }
                $code = substr($code, 0, 10);
            }
            $base = $code;
            $i = 1;
            while (\App\Models\Destination::where('code', $code)->exists()) {
                $code = $base . $i;
                $i++;
            }
        }

        \App\Models\Destination::create([
            'name'         => $validated['name'],
            'code'         => $code,
            'country_code' => $validated['country_code'] ?? null,
            'is_active'    => $validated['is_active'] ?? true,
        ]);

        $clonedCount = 0;
        $serviceIds = $validated['service_ids'] ?? [];
        if (!empty($serviceIds)) {
            $services = \App\Models\CourierService::whereIn('id', $serviceIds)->get();
            $serviceCountry = $validated['country_code'] !== null && $validated['country_code'] !== ''
                ? $validated['country_code']
                : $code;
            foreach ($services as $service) {
                $data = $service->getAttributes();
                unset($data['id']);
                $data['country'] = $serviceCountry;
                \App\Models\CourierService::create($data);
                $clonedCount++;
            }
        }

        $msg = 'Country "' . $validated['name'] . '" added successfully (code: ' . $code . ').';
        if ($clonedCount > 0) {
            $msg .= ' ' . $clonedCount . ' service(s) cloned for this country.';
        }

        return redirect()
            ->route('admin.add-country')
            ->with('success', $msg);
    }

    public function myProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.my-profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_user,email,' . $admin->id,
            'mobile' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ];

        // If user is trying to change password, add password validation rules
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        // Check current password if changing password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $admin->password = $request->new_password;
        }

        // Update profile fields
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->mobile = $validated['mobile'];
        $admin->designation = $validated['designation'];
        $admin->state = $validated['state'];
        $admin->city = $validated['city'];
        $admin->save();

        return redirect()->route('admin.my-profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * View a customer's full profile (personal info, KYC, business details, wallet).
     */
    public function customerProfile($id)
    {
        $customer = Customer::with(['kycDetail', 'csbForm', 'wallet', 'businessCategory'])
            ->findOrFail($id);

        $personalKyc = $customer->kycDetail;
        $businessKyc = $customer->csbForm;
        $businessCategory = $customer->businessCategory;
        $userType = $businessCategory ? $businessCategory->user_type : 'Personal';
        $wallet = $customer->wallet;

        // Courier / Aggregator customers complete Business KYC without the
        // CSB-V (export) flow, so all CSB-V references are hidden for them.
        $isCourierOrAggregator = $businessCategory
            && $this->courierAggregatorCategoryIds()->contains($businessCategory->id);

        if ($businessKyc) {
            $documentDiagnostics = [];
            $documentPaths = [
                'gst_certificate_document' => $businessKyc->gst_certificate_document,
                'iec_document' => $businessKyc->iec_document,
                'ad_code_document' => $businessKyc->ad_code_document,
                'lut_document' => $businessKyc->lut_document,
                'gst_document' => $businessKyc->gst_document,
                'aadhar_document' => $businessKyc->aadhar_document,
                'signature_document' => $businessKyc->signature_document,
                'merchant_agreement' => $businessKyc->merchant_agreement,
            ];

            foreach (array_filter($documentPaths) as $field => $documentPath) {
                $storedPath = ltrim(str_replace('\\', '/', (string) $documentPath), '/');
                $currentUrlPath = (string) parse_url(
                    asset('uploads/') . '/' . $storedPath,
                    PHP_URL_PATH
                );

                $documentDiagnostics[$field] = [
                    'stored_path_starts_with_uploads' => str_starts_with($storedPath, 'uploads/'),
                    'current_url_has_duplicate_uploads' => str_contains($currentUrlPath, '/uploads/uploads/'),
                ];
            }

            $hasDuplicateUploadPath = collect($documentDiagnostics)->contains(
                fn (array $diagnostic): bool => $diagnostic['current_url_has_duplicate_uploads']
            );

            if ($hasDuplicateUploadPath) {
                Log::warning('Duplicate uploads segment detected in admin customer profile document URLs.', [
                    'customer_id' => $customer->id,
                    'asset_base_path' => parse_url(asset('uploads/'), PHP_URL_PATH),
                    'documents' => $documentDiagnostics,
                ]);
            }
        }

        return view('admin.customer-profile', compact(
            'customer',
            'personalKyc',
            'businessKyc',
            'businessCategory',
            'userType',
            'wallet',
            'isCourierOrAggregator'
        ));
    }

    /**
     * List all United customers (users) with a count of their exporter customers.
     */
    public function exportCustomers()
    {
        // User Type is derived from the customer's business_category_id by joining
        // the business_categories table so we can read that category's user_type.
        // NOTE: withCount must run AFTER the explicit select() so the
        // exporter_customers_count alias is appended instead of being overwritten.
        $customers = Customer::with('businessCategory')
            ->leftJoin('business_categories', 'business_categories.id', '=', 'customers.business_category_id')
            ->select(
                'customers.*',
                'business_categories.user_type as category_user_type'
            )
            ->withCount('exporterCustomers')
            ->orderByDesc('exporter_customers_count')
            ->orderBy('customers.id')
            ->get();

        return view('admin.export-customers', compact('customers'));
    }

    /**
     * Show the list of exporter customers belonging to a single United customer.
     */
    public function exportCustomersDetail($id)
    {
        $customer = Customer::with('businessCategory')
            ->withCount('exporterCustomers')
            ->findOrFail($id);

        $exporterCustomers = $customer->exporterCustomers()
            ->with(['businessCategory', 'addresses'])
            ->orderByDesc('id')
            ->get();

        return view('admin.export-customers-detail', compact('customer', 'exporterCustomers'));
    }

    /**
     * Show the full profile of a single exporter customer including every
     * uploaded document (KYC, GST, PAN, IEC, AD Code, LUT, merchant agreement)
     * with an in-page document preview.
     */
    public function exportCustomerView($id)
    {
        $exporterCustomer = ExporterCustomer::with(['businessCategory', 'addresses'])
            ->with('exporter')
            ->findOrFail($id);

        $parentCustomer = $exporterCustomer->exporter;

        return view('admin.export-customer-view', compact('exporterCustomer', 'parentCustomer'));
    }

    /**
     * Customer Report (read-only, admin side).
     *
     * Sidebar > Reports > Customer Report. Customer-wise saari shipment
     * details — kaunsi shipment kis step (draft/ready/packed/manifested/
     * ready_for_pickup/assigned_for_pickup/received/dispatched/delivered/
     * cancelled/disputed/on_hold) par hai — bilkul view-all-shipments jaisa
     * table, lekin yahan koi action (pay/cancel/print/manifest/assign)
     * available nahi hai. Sirf dekhne + filter karne ke liye.
     */
    /**
     * Customer Report listing + Excel export dono ke liye shared filtered query.
     * Saare filters (customer, awb, invoice, dates, status) yahin apply hote
     * hain taaki jo screen par dikhe wahi Excel me export ho.
     */
    private function baseCustomerReportQuery(Request $request)
    {
        $status = $request->input('status', 'all');
        $customerId = $request->input('customer_id');
        $awbNumber = $request->input('awb_number');
        $invoiceNumber = $request->input('invoice_number');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        return ShipmentInvoice::query()
            ->with([
                'shipperInfo' => function ($q) {
                    $q->select('id', 'customer_id', 'awb_number', 'company_name', 'contact_person', 'city', 'state', 'pincode', 'status', 'total_price', 'created_at');
                },
                'shipperInfo.manifest' => function ($q) {
                    $q->select('id', 'shipper_id', 'manifest_number', 'status', 'created_at');
                },
                'shipperInfo.consigneeInfo' => function ($q) {
                    $q->select('id', 'shipper_id', 'consignee_name', 'contact_person', 'city', 'state', 'zip_code', 'delivery_destination');
                },
            ])
            ->when($customerId, function ($q) use ($customerId) {
                $q->whereHas('shipperInfo', function ($sq) use ($customerId) {
                    $sq->where('customer_id', $customerId);
                });
            })
            ->when($awbNumber, function ($q) use ($awbNumber) {
                $q->whereHas('shipperInfo', function ($sq) use ($awbNumber) {
                    $sq->where('awb_number', 'like', '%'.$awbNumber.'%');
                });
            })
            ->when($invoiceNumber, function ($q) use ($invoiceNumber) {
                $q->where('invoice_number', 'like', '%'.$invoiceNumber.'%');
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->when($status && $status !== 'all', function ($q) use ($status) {
                if ($status === 'cancelled') {
                    $q->where('status', 'cancelled');
                } elseif ($status === 'dispatched') {
                    $q->whereHas('shipperInfo', function ($sq) {
                        $sq->whereIn('status', ['dispatched', 'ready_to_dispatch']);
                    });
                } elseif ($status === 'assigned_for_pickup') {
                    $q->whereHas('shipperInfo', function ($sq) {
                        $sq->whereIn('status', ['assigned_for_pickup', 'confirm_pickup']);
                    });
                } else {
                    $q->whereHas('shipperInfo', function ($sq) use ($status) {
                        $sq->where('status', $status);
                    });
                }
            });
    }

    public function customerReport(Request $request)
    {
        $status = $request->input('status', 'all');
        $customerId = $request->input('customer_id');

        $invoices = $this->baseCustomerReportQuery($request)
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Status-wise counts (customer filter ko respect karte hue) — tabs ke liye.
        $countBase = ShipmentInvoice::query()
            ->when($customerId, function ($q) use ($customerId) {
                $q->whereHas('shipperInfo', function ($sq) use ($customerId) {
                    $sq->where('customer_id', $customerId);
                });
            })
            ->with('shipperInfo:id,status')
            ->get(['id', 'shipper_id', 'status']);

        $statusCounts = [
            'all' => $countBase->count(),
            'draft' => 0, 'ready' => 0, 'packed' => 0, 'manifested' => 0,
            'ready_for_pickup' => 0, 'assigned_for_pickup' => 0, 'received' => 0,
            'dispatched' => 0, 'cancelled' => 0, 'delivered' => 0,
            'disputed' => 0, 'on_hold' => 0,
        ];
        foreach ($countBase as $row) {
            $rowStatus = ($row->status === 'cancelled' || ($row->shipperInfo?->status ?? '') === 'cancelled')
                ? 'cancelled'
                : ($row->shipperInfo?->status ?: 'draft');
            if ($rowStatus === 'ready_to_dispatch' || $rowStatus === 'confirm_pickup') {
                $rowStatus = $rowStatus === 'confirm_pickup' ? 'assigned_for_pickup' : 'dispatched';
            }
            if (isset($statusCounts[$rowStatus])) {
                $statusCounts[$rowStatus]++;
            }
        }

        // Customer dropdown ke liye — sirf wahi customers jinki koi shipment hai + saare active customers.
        $customers = Customer::orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'customer_code']);

        $statusOptions = [
            'all' => 'All Orders',
            'draft' => 'Drafts',
            'ready' => 'Ready',
            'packed' => 'Packed',
            'manifested' => 'Manifested',
            'ready_for_pickup' => 'Ready for Pickup',
            'assigned_for_pickup' => 'In-Transit to Hub',
            'received' => 'Received',
            'dispatched' => 'Dispatched',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'disputed' => 'Disputed',
            'on_hold' => 'On Hold',
        ];

        return view('admin.customer-report', compact('invoices', 'statusCounts', 'statusOptions', 'customers', 'status', 'customerId'));
    }

    /**
     * Customer Report ka Excel (.xlsx) export.
     *
     * Wahi filters apply hote hain jo listing page par lage hain, taaki
     * screen wali filtered list hi download ho. Table wale saare columns
     * (HAWB, date, customer, from/to, consignee, invoice, amount,
     * manifest, status) Excel me aate hain.
     */
    public function exportCustomerReport(Request $request)
    {
        $invoices = $this->baseCustomerReportQuery($request)
            ->orderBy('created_at', 'desc')
            ->get();

        $customerIds = $invoices
            ->map(function ($invoice) {
                return $invoice->shipperInfo ? $invoice->shipperInfo->customer_id : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $customerMap = Customer::whereIn('id', $customerIds)
            ->get(['id', 'first_name', 'last_name', 'email', 'customer_code'])
            ->keyBy('id');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Report');
        $headers = [
            '#', 'HAWB Number', 'Order Date', 'Customer Name', 'Customer Email',
            'Customer Code', 'Shipper Company', 'From City', 'From State', 'From Pincode',
            'To City', 'To State', 'To Pincode', 'Destination', 'Consignee',
            'Invoice No.', 'Amount', 'Manifest No.', 'Status',
        ];

        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter.'1', $header);
        }

        $rowNumber = 2;
        foreach ($invoices as $serial => $invoice) {
            $shipper = $invoice->shipperInfo;
            $consignee = $shipper ? $shipper->consigneeInfo : null;
            $manifest = $shipper ? $shipper->manifest : null;
            $rowStatus = 'draft';
            if (($invoice->status ?? '') === 'cancelled') {
                $rowStatus = 'cancelled';
            } elseif ($shipper && $shipper->status) {
                $rowStatus = $shipper->status;
            }
            if ($rowStatus === 'ready_to_dispatch') {
                $rowStatus = 'dispatched';
            } elseif ($rowStatus === 'confirm_pickup') {
                $rowStatus = 'assigned_for_pickup';
            }
            $cust = $shipper && $shipper->customer_id ? ($customerMap->get($shipper->customer_id)) : null;

            $values = [
                $serial + 1,
                $shipper->awb_number ?? '-',
                $invoice->created_at ? $invoice->created_at->format('d-m-Y H:i') : '-',
                $cust ? trim(($cust->first_name ?? '').' '.($cust->last_name ?? '')) : '-',
                $cust->email ?? '-',
                $cust->customer_code ?? '-',
                $shipper->company_name ?? '-',
                $shipper->city ?? '-',
                $shipper->state ?? '-',
                $shipper->pincode ?? '-',
                $consignee->city ?? '-',
                $consignee->state ?? '-',
                $consignee->zip_code ?? '-',
                $consignee->delivery_destination ?? '-',
                $consignee->consignee_name ?? ($consignee->contact_person ?? '-'),
                $invoice->invoice_number ?? '-',
                $shipper && $shipper->total_price ? (float) $shipper->total_price : 0,
                $manifest->manifest_number ?? '-',
                str_replace('_', ' ', (string) $rowStatus),
            ];
            foreach ($values as $index => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter.$rowNumber, $value);
            }
            $rowNumber++;
        }

        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        if ($rowNumber > 2) {
            $sheet->setAutoFilter('A1:'.$lastColumn.'1');
        }
        foreach (range(1, count($headers)) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $fileName = 'customer-report-'.date('Y-m-d-His').'.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /**
     * Resolve the business category ids that are treated as Courier / Aggregator.
     * Matching mirrors the tolerant slug/name checks used across the codebase.
     */
    private function courierAggregatorCategoryIds()
    {
        $allowedCategories = [
            'courier-or-aggregator',
            'courier-aggregator',
            'courier or aggregator',
            'courier / aggregator',
            'courier/aggregator',
        ];

        return BusinessCategory::query()
            ->where(function ($query) use ($allowedCategories) {
                foreach ($allowedCategories as $category) {
                    $query->orWhereRaw('LOWER(TRIM(category_name)) = ?', [$category])
                        ->orWhereRaw('LOWER(TRIM(category_slug)) = ?', [$category]);
                }
            })
            ->pluck('id');
    }

    /**
     * Download all available KYC documents for a customer as a ZIP archive.
     */
    public function downloadCustomerKycDocuments($id)
    {
        $customer = Customer::with(['kycDetail', 'csbForm'])->findOrFail($id);
        $uploadsRoot = realpath(public_path('uploads'));

        if ($uploadsRoot === false || !class_exists('ZipArchive')) {
            abort(500, 'Document download is not available.');
        }

        $documents = [
            'Personal GST Certificate' => $customer->kycDetail?->gst_certificate_document,
            'Personal PAN Card' => $customer->kycDetail?->pan_document,
            'Personal Aadhaar Front' => $customer->kycDetail?->aadhar_front_document,
            'Personal Aadhaar Back' => $customer->kycDetail?->aadhar_back_document,
            'Personal Aadhaar Document' => $customer->kycDetail?->aadhar_document,
            'Personal Signature' => $customer->kycDetail?->signature_document ?: $customer->kycDetail?->signature,
            'Personal Merchant Agreement' => $customer->kycDetail?->merchant_agreement,
            'Business GST Certificate' => $customer->csbForm?->gst_certificate_document,
            'Business GST Document' => $customer->csbForm?->gst_document,
            'Business IEC Certificate' => $customer->csbForm?->iec_document,
            'Business AD Code Document' => $customer->csbForm?->ad_code_document,
            'Business LUT Document' => $customer->csbForm?->lut_document,
            'Business Aadhaar Document' => $customer->csbForm?->aadhar_document,
            'Business Signature' => $customer->csbForm?->signature_document,
            'Business Merchant Agreement' => $customer->csbForm?->merchant_agreement,
        ];

        $zipPath = tempnam(sys_get_temp_dir(), 'kyc_');
        $zip = new \ZipArchive();
        if ($zipPath === false || $zip->open($zipPath, \ZipArchive::OVERWRITE) !== true) {
            if ($zipPath !== false) {
                @unlink($zipPath);
            }
            abort(500, 'Unable to create document archive.');
        }

        $added = [];
        $rootPrefix = rtrim($uploadsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        foreach ($documents as $label => $storedPath) {
            $path = trim((string) $storedPath);
            if ($path === '' || filter_var($path, FILTER_VALIDATE_URL)) {
                continue;
            }

            $path = ltrim(str_replace('\\', '/', $path), '/');
            $path = preg_replace('#^(?:(?:public|uploads)/)+#i', '', $path) ?? $path;
            if ($path === '' || str_contains($path, '..')) {
                continue;
            }

            $absolutePath = realpath($uploadsRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
            if ($absolutePath === false || !is_file($absolutePath) || !str_starts_with($absolutePath, $rootPrefix) || isset($added[$absolutePath])) {
                continue;
            }

            $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
            $archiveName = preg_replace('/[^A-Za-z0-9._-]+/', '_', $label) . ($extension ? '.' . $extension : '');
            $zip->addFile($absolutePath, $archiveName);
            $added[$absolutePath] = true;
        }

        $zip->close();
        if (!$added) {
            @unlink($zipPath);
            abort(404, 'No KYC documents were found for this customer.');
        }

        $customerName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'customer';
        $downloadName = 'kyc-documents-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($customerName)) . '-' . $customer->id . '.zip';

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Download one KYC document, or generate the electronically signed agreement.
     */
    public function downloadCustomerKycDocument($id, string $document)
    {
        $customer = Customer::with(['kycDetail', 'csbForm'])->findOrFail($id);
        $uploadsRoot = realpath(public_path('uploads'));
        if ($uploadsRoot === false) {
            abort(500, 'Document download is not available.');
        }

        if ($document === 'signed_merchant_agreement') {
            return $this->downloadSignedMerchantAgreement($customer, $uploadsRoot);
        }

        $documents = [
            'gst_certificate' => ['GST Certificate', $customer->csbForm?->gst_certificate_document ?: $customer->csbForm?->gst_document ?: $customer->kycDetail?->gst_certificate_document],
            'pan_card' => ['PAN Card', $customer->kycDetail?->pan_document],
            'aadhar_front' => ['Aadhaar Front', $customer->kycDetail?->aadhar_front_document ?: $customer->csbForm?->aadhar_document],
            'aadhar_back' => ['Aadhaar Back', $customer->kycDetail?->aadhar_back_document],
            'iec_certificate' => ['IEC Certificate', $customer->csbForm?->iec_document],
            'ad_code_document' => ['AD Code Document', $customer->csbForm?->ad_code_document],
            'lut_document' => ['LUT Document', $customer->csbForm?->lut_document],
            'signature' => ['Signature', $customer->csbForm?->signature_document ?: $customer->kycDetail?->signature_document ?: $customer->kycDetail?->signature],
            'merchant_agreement' => ['Merchant Agreement', $customer->csbForm?->merchant_agreement ?: $customer->kycDetail?->merchant_agreement],
        ];

        if (!isset($documents[$document])) {
            abort(404, 'Unknown KYC document.');
        }

        [$label, $storedPath] = $documents[$document];
        $sourcePath = $this->resolveKycUploadedFile($storedPath, $uploadsRoot);
        if ($sourcePath === null) {
            abort(404, 'The requested KYC document was not found.');
        }

        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $customerCode = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($customer->customer_code ?: $customer->id));
        $downloadName = $customerCode . '-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($label))
            . ($extension !== '' ? '.' . $extension : '');

        return response()->download($sourcePath, $downloadName);
    }

    private function downloadSignedMerchantAgreement(Customer $customer, string $uploadsRoot)
    {
        // Signature priority: kyc_details.signature_document -> csb_form.signature_document
        // -> legacy kyc_details.signature (whichever path exists gets used).
        $signaturePath = $this->resolveKycUploadedFile(
            $customer->kycDetail?->signature_document
                ?: $customer->csbForm?->signature_document
                ?: $customer->kycDetail?->signature,
            $uploadsRoot
        );
        // The signed merchant agreement PDF is rendered from the static agreement
        // text plus the customer's signature image, so only the signature is
        // strictly required. Some older KYC flows (personal kycSubmit) never store
        // the merchant_agreement upload, but the generated PDF does not need it.
        if ($signaturePath === null) {
            abort(404, 'A signature is required to generate the signed agreement.');
        }

        $mimeType = mime_content_type($signaturePath) ?: 'image/png';
        $signatureDataUri = 'data:' . $mimeType . ';base64,' . base64_encode((string) file_get_contents($signaturePath));
        $acceptedAt = $customer->csbForm?->merchant_agreement_accepted_at
            ?: $customer->kycDetail?->merchant_agreement_accepted_at
            ?: $customer->kycDetail?->terms_accepted_at;

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.signed-merchant-agreement', compact(
                'customer',
                'acceptedAt',
                'signatureDataUri'
            ))->setPaper('a4');
        } catch (\Throwable $exception) {
            Log::error('Unable to generate signed merchant agreement.', [
                'customer_id' => $customer->id,
                'error' => $exception->getMessage(),
            ]);
            abort(500, 'Unable to generate the signed merchant agreement.');
        }

        $customerCode = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($customer->customer_code ?: $customer->id));
        return $pdf->download($customerCode . '-signed-merchant-agreement.pdf');
    }

    private function resolveKycUploadedFile($storedPath, string $uploadsRoot): ?string
    {
        $path = trim((string) $storedPath);
        if ($path === '' || filter_var($path, FILTER_VALIDATE_URL)) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $path = preg_replace('#^(?:(?:public|uploads)/)+#i', '', $path) ?? $path;
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        $absolutePath = realpath($uploadsRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
        $rootPrefix = rtrim($uploadsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $absolutePath !== false
            && is_file($absolutePath)
            && str_starts_with($absolutePath, $rootPrefix)
                ? $absolutePath
                : null;
    }

    /**
     * Activate or deactivate a customer account.
     */
    public function toggleCustomerStatus($id)
    {
        $customer = Customer::findOrFail($id);

        $oldStatus = $customer->status;

        $customer->status = !$customer->status;
        $customer->save();

        $action = $customer->status ? 'activated' : 'deactivated';
        $customerName = $customer->first_name . ' ' . $customer->last_name;

        \App\Support\SystemLogger::log(
            'customer.status_toggle',
            "Account for {$customerName} has been {$action}",
            'customer',
            (int) $oldStatus,
            (int) $customer->status
        );

        return redirect()->back()
            ->with('success', "Account for {$customerName} has been {$action} successfully.");
    }

    /**
     * Enable or disable a customer's ability to create shipments.
     *
     * This is INDEPENDENT from the account `status` toggle:
     *  - status               -> can the customer log in at all
     *  - can_create_shipment  -> can the customer create new shipments
     *
     * When disabled, the customer sees a warning banner on the create-shipment
     * page and the storeShipment endpoint rejects the request.
     */
    public function toggleShipmentAccess($id)
    {
        $customer = Customer::findOrFail($id);

        $oldAccess = $customer->can_create_shipment;

        $customer->can_create_shipment = !$customer->can_create_shipment;
        $customer->save();

        $action = $customer->can_create_shipment ? 'enabled' : 'disabled';
        $customerName = $customer->first_name . ' ' . $customer->last_name;

        \App\Support\SystemLogger::log(
            'customer.shipment_access_toggle',
            "Shipment creation for {$customerName} has been {$action}",
            'customer',
            (bool) $oldAccess,
            (bool) $customer->can_create_shipment
        );

        return redirect()->back()
            ->with('success', "Shipment creation for {$customerName} has been {$action} successfully.");
    }

    /**
     * Export KYC records to an Excel (.xlsx) file using PhpSpreadsheet.
     * Accepts an optional ?status=pending|approved|rejected|all filter.
     */
    public function exportKycExcel(Request $request)
    {
        $status = $request->query('status', 'all');
        $customerId = $request->query('customer_id');

        $query = KycDetail::with(['customer.csbForm', 'customer.businessCategory']);

        // When a specific customer is provided, limit the export to that customer only.
        if (! empty($customerId)) {
            $query->where('customer_id', (int) $customerId);
        }

        if ($status === 'pending') {
            $query->whereIn('kyc_status', ['pending', 'under_review']);
        } elseif ($status === 'approved') {
            $query->where('kyc_status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('kyc_status', 'rejected');
        }

        $kycRecords = $query->orderBy('id', 'desc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('KYC Records');

        $headers = [
            'KYC ID', 'KYC Type', 'KYC Status', 'Customer Name', 'Email', 'Phone',
            'Alternate Phone', 'Organization', 'Authorized Signatory', 'GST Number',
            'GST Verified', 'Aadhar Number', 'Aadhar Verified', 'PAN Number',
            'PAN Holder Name', 'PAN DOB', 'PAN Verified', 'OTP Verified', 'Terms Accepted',
            'Billing Address', 'Billing GST', 'Billing Contact', 'Billing Email',
            'IEC Number', 'AD Code', 'GST Certificate Number', 'LUT Expiry Date',
            'LUT Bond Year', 'Bank Account Number', 'Bank Type', 'Account Status',
            'Submitted At', 'Updated At',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $row = 2;
        foreach ($kycRecords as $kyc) {
            $customer = $kyc->customer;
            $csb = $customer ? $customer->csbForm : null;

            $data = [
                $kyc->id,
                ucfirst($kyc->kyc_type ?? 'personal'),
                ucfirst($kyc->kyc_status ?? 'pending'),
                $customer ? ($customer->first_name . ' ' . $customer->last_name) : '',
                $customer->email ?? '',
                $customer->phone_number ?? '',
                $customer->alternate_phone_number ?? '',
                $kyc->organization_name ?? '',
                $kyc->authorized_signatory ?? '',
                $kyc->gst_number ?? '',
                $kyc->gst_verified ? 'Yes' : 'No',
                $kyc->aadhar_number ?? '',
                $kyc->aadhar_verified ? 'Yes' : 'No',
                $kyc->pan_number ?? '',
                $kyc->pan_holder_name ?? '',
                $kyc->pan_dob ? $kyc->pan_dob->format('Y-m-d') : '',
                $kyc->pan_verified ? 'Yes' : 'No',
                $kyc->otp_verified ? 'Yes' : 'No',
                $kyc->terms_accepted ? 'Yes' : 'No',
                $kyc->billing_address ?? '',
                $kyc->billing_gst ?? '',
                $kyc->billing_contact ?? '',
                $kyc->billing_email ?? '',
                $csb->iec_number ?? '',
                $csb->ad_code ?? '',
                $csb->gst_certificate_number ?? '',
                $csb && $csb->lut_expiry_date ? $csb->lut_expiry_date->format('Y-m-d') : '',
                $csb->lut_bond_year ?? '',
                $csb->bank_account_number ?? '',
                $csb->bank_type ?? '',
                $customer && isset($customer->status) ? ($customer->status ? 'Active' : 'Deactivated') : 'Active',
                $kyc->created_at ? $kyc->created_at->format('Y-m-d H:i:s') : '',
                $kyc->updated_at ? $kyc->updated_at->format('Y-m-d H:i:s') : '',
            ];

            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // foreach (range('A', 'AG') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(
                Coordinate::stringFromColumnIndex($i)
            )->setAutoSize(true);
        }

        $fileName = 'kyc_records_' . ($customerId ? 'customer_' . (int) $customerId . '_' : '') . $status . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

}
