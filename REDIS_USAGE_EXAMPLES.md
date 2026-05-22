<?php

/**
 * Redis Usage Examples for UWC Application
 * 
 * This file demonstrates common Redis patterns used in the application.
 * Redis is configured for caching, sessions, and job queues.
 */

// ============================================
// 1. BASIC CACHING
// ============================================

// Using Cache facade (recommended)
use Illuminate\Support\Facades\Cache;

// Store a value for 1 hour (3600 seconds)
Cache::put('user_profile_' . $userId, $userData, 3600);

// Retrieve a value
$userData = Cache::get('user_profile_' . $userId);

// Retrieve with default value
$userData = Cache::get('user_profile_' . $userId, []);

// Check if key exists
if (Cache::has('user_profile_' . $userId)) {
    // Do something
}

// Delete a key
Cache::forget('user_profile_' . $userId);

// Store with no expiration (~1 year)
Cache::put('persistent_key', $data, 3153600);


// ============================================
// 2. USING REDIS HELPER
// ============================================

use App\Helpers\RedisHelper;

// Simple put/get
RedisHelper::put('admin:' . $adminId . ':permissions', $permissions, 7200);
$permissions = RedisHelper::get('admin:' . $adminId . ':permissions');

// Check connection
if (RedisHelper::isConnected()) {
    echo "Redis is running!";
}

// Counters
RedisHelper::increment('total_logins');
RedisHelper::decrement('active_users');

// List operations (queues)
RedisHelper::push('user_actions', ['action' => 'login', 'user_id' => 1]);
$action = RedisHelper::pop('user_actions');

// Set operations
RedisHelper::sadd('admin_emails', ['admin1@example.com', 'admin2@example.com']);
$emails = RedisHelper::smembers('admin_emails');

// Hash operations (structured data)
RedisHelper::hset('shipment:123', [
    'status' => 'pending',
    'customer' => 'John Doe',
    'weight' => '5kg'
]);
$status = RedisHelper::hget('shipment:123', 'status');
$allData = RedisHelper::hgetall('shipment:123');


// ============================================
// 3. SESSION MANAGEMENT (Automatic with Redis)
// ============================================

// Sessions are automatically stored in Redis
session(['user_id' => 123]);
$userId = session('user_id');

// Admin sessions example
session(['admin_id' => 1, 'admin_type' => 'Super Admin']);
$adminType = session('admin_type');


// ============================================
// 4. QUEUE JOBS (Using Redis as backend)
// ============================================

use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessShipment;

// Dispatch job to Redis queue
ProcessShipment::dispatch($shipmentId)->onQueue('default');

// In your job class:
class ProcessShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle()
    {
        // Job logic here
    }
}

// Run queue worker:
// php artisan queue:work redis


// ============================================
// 5. REAL-TIME FEATURES
// ============================================

// Track active admin users
RedisHelper::sadd('active_admins', Auth::guard('admin')->id());

// Get all active admins
$activeAdmins = RedisHelper::smembers('active_admins');

// Remove when logout
RedisHelper::forget('admin:' . $adminId . ':session');


// ============================================
// 6. CACHING COMMON DATA
// ============================================

// Cache admin list
Cache::remember('all_admins', 3600, function () {
    return Admin::all();
});

// Cache shipment data
$shipments = Cache::remember('shipments_' . $date, 7200, function () use ($date) {
    return Shipment::whereDate('created_at', $date)->get();
});


// ============================================
// 7. EXAMPLE IN CONTROLLER
// ============================================

/*
namespace App\Http\Controllers;

use App\Helpers\RedisHelper;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function loginPost(Request $request)
    {
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = Auth::guard('admin')->user();
            
            // Cache admin data
            Cache::put('admin_' . $admin->id, $admin, 3600);
            
            // Track login
            RedisHelper::increment('admin_logins_' . date('Y-m-d'));
            
            // Add to active admins
            RedisHelper::sadd('active_admins', $admin->id);
            
            return redirect()->route('admin.dashboard');
        }
        
        return back()->with('error', 'Invalid credentials');
    }
    
    public function logout(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        
        // Remove from cache
        Cache::forget('admin_' . $adminId);
        
        // Remove from active admins
        RedisHelper::forget('active_admins');
        
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
    
    public function getDashboard()
    {
        // Get cached admin data
        $admin = Cache::get('admin_' . Auth::guard('admin')->id());
        
        // Get from Redis if not in cache
        if (!$admin) {
            $admin = RedisHelper::get('admin_' . Auth::guard('admin')->id());
        }
        
        return view('admin.dashboard', compact('admin'));
    }
}
*/


// ============================================
// 8. IMPORTANT NOTES
// ============================================

/*
Configuration:
- CACHE_STORE=redis
- SESSION_DRIVER=redis
- QUEUE_CONNECTION=redis

Prerequisites:
- Redis server must be running on 127.0.0.1:6379
- PHP Redis extension (pecl install redis)
- Or use PhpRedis client library

Database Separation:
- Database 0: General cache
- Database 1: Cache (as configured)
- Use different databases for different purposes

Naming Convention:
- user:id:data
- admin:id:permissions
- shipment:id:status
- active_users_count

Best Practices:
1. Always use meaningful key names
2. Set appropriate TTL values
3. Handle Redis connection failures gracefully
4. Use namespacing in key names
5. Monitor Redis memory usage
6. Clear old keys periodically

Monitoring:
- redis-cli to connect to Redis
- KEYS * to list all keys
- FLUSHDB to clear database
- INFO to get Redis stats
- MONITOR to watch commands
*/
?>
