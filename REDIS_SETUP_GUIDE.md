# Redis Setup Guide for UWC Application

## ✅ What's Configured

Your Laravel application is now fully configured to use Redis for:
- **Caching** - Fast data retrieval
- **Sessions** - User session management  
- **Queues** - Background job processing

## 📋 Configuration Files Updated

### `.env` File
```
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### Config Files (Already configured)
- `config/cache.php` - Redis cache store
- `config/session.php` - Redis session driver
- `config/queue.php` - Redis queue connection
- `config/database.php` - Redis connection settings

## 🚀 Getting Started

### 1. **Ensure Redis is Running**
```bash
# Windows (using Redis from WSL or installed service)
redis-server

# Or check if service is running
redis-cli ping
# Should return: PONG
```

### 2. **Test Redis Connection**
```bash
php artisan redis:test
```

This will verify:
- ✓ Redis Connection
- ✓ Cache Store functionality
- ✓ Session Driver configuration
- ✓ Queue Connection setup
- ✓ Redis Helper functionality
- ✓ Redis server info

### 3. **Use Redis in Your Code**

#### **Option A: Using Cache Facade (Recommended for simple caching)**
```php
use Illuminate\Support\Facades\Cache;

// Store for 1 hour
Cache::put('user_' . $id, $userData, 3600);

// Retrieve
$user = Cache::get('user_' . $id);

// Check existence
if (Cache::has('user_' . $id)) { }

// Delete
Cache::forget('user_' . $id);
```

#### **Option B: Using RedisHelper (For advanced operations)**
```php
use App\Helpers\RedisHelper;

// Simple operations
RedisHelper::put('key', $value, 3600);
RedisHelper::get('key');

// Counter operations
RedisHelper::increment('login_count');
RedisHelper::decrement('active_users');

// List operations
RedisHelper::push('queue_name', ['task' => 'process']);
$task = RedisHelper::pop('queue_name');

// Set operations
RedisHelper::sadd('admin_emails', ['admin@example.com']);
$emails = RedisHelper::smembers('admin_emails');

// Hash operations
RedisHelper::hset('shipment:123', ['status' => 'pending', 'weight' => '5kg']);
$status = RedisHelper::hget('shipment:123', 'status');
$all = RedisHelper::hgetall('shipment:123');

// Check connection
if (RedisHelper::isConnected()) { }

// Clear all
RedisHelper::flush();
```

## 📝 Common Use Cases

### Admin Login Session Caching
```php
// In AdminController loginPost
Cache::put('admin_' . $admin->id, $admin->toArray(), 3600);

// In controller methods
$admin = Cache::get('admin_' . Auth::guard('admin')->id());
```

### Tracking Active Admins
```php
// Track login
RedisHelper::sadd('active_admins', Auth::guard('admin')->id());

// Get all active
$activeAdmins = RedisHelper::smembers('active_admins');

// Remove on logout
RedisHelper::forget('admin_' . $adminId);
```

### Caching Shipment Data
```php
$shipments = Cache::remember('shipments_' . date('Y-m-d'), 7200, function () {
    return Shipment::whereDate('created_at', date('Y-m-d'))->get();
});
```

### Real-time Counters
```php
RedisHelper::increment('total_shipments');
RedisHelper::increment('logins_' . date('Y-m-d'));
```

## 🔧 Queue Management

### Run Queue Worker (Process jobs)
```bash
# Run in foreground (for development)
php artisan queue:work redis

# Run in background (production)
php artisan queue:work redis --daemon

# With specific settings
php artisan queue:work redis --tries=3 --timeout=90
```

### Dispatch Job to Queue
```php
use App\Jobs\ProcessShipment;

ProcessShipment::dispatch($shipmentId)->onQueue('default');
```

## 📊 Redis CLI Commands

Open Redis CLI to inspect/manage data:
```bash
redis-cli
```

### Common Commands
```redis
# Check connection
PING
# Output: PONG

# View all keys
KEYS *

# Get value
GET key_name

# Delete key
DEL key_name

# Clear database
FLUSHDB

# View database info
INFO

# Monitor all commands
MONITOR

# Clear all databases
FLUSHALL
```

## ⚙️ Performance Tips

1. **Use Appropriate TTL (Time To Live)**
   - Session data: 3600-7200 seconds
   - Cache data: 1800-86400 seconds
   - Temporary data: 60-300 seconds

2. **Key Naming Convention**
   - Use meaningful names: `user:123:data`
   - Namespace by entity: `admin:`, `shipment:`, `customer:`
   - Use colons as separators

3. **Error Handling**
   - All RedisHelper methods handle exceptions gracefully
   - Errors are logged to `storage/logs/laravel.log`
   - Check connectivity with `RedisHelper::isConnected()`

4. **Memory Management**
   - Monitor Redis memory with `redis-cli > INFO memory`
   - Set maxmemory policy in Redis config
   - Periodically clean old keys

## 🐛 Troubleshooting

### Redis Connection Failed
```
Error: Could not connect to Redis at 127.0.0.1:6379
```
**Solution:**
- Ensure Redis server is running: `redis-cli ping`
- Check REDIS_HOST and REDIS_PORT in .env
- Windows: Install Redis or run in WSL

### Sessions Not Persisting
```
Sessions stored in files instead of Redis
```
**Solution:**
- Verify `SESSION_DRIVER=redis` in .env
- Run `php artisan redis:test`
- Restart application/PHP

### Cache Not Working
```
Cache returning null values
```
**Solution:**
- Check Redis connection: `redis-cli ping`
- Verify key exists: `redis-cli > GET key_name`
- Check TTL: `redis-cli > TTL key_name` (should be > 0)

## 📚 Documentation Links

- [Laravel Cache Documentation](https://laravel.com/docs/cache)
- [Laravel Sessions](https://laravel.com/docs/session)
- [Laravel Queues](https://laravel.com/docs/queues)
- [Redis CLI Documentation](https://redis.io/commands)

## ✨ Next Steps

1. Run `php artisan redis:test` to verify setup
2. Review `REDIS_USAGE_EXAMPLES.md` for detailed examples
3. Test caching in your controllers
4. Set up queue workers for background jobs
5. Monitor Redis performance in production

---

**Questions?** Check logs at: `storage/logs/laravel.log`
