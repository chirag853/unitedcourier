<?php

namespace App\Console\Commands;

use App\Helpers\RedisHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class TestRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connection and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Redis Connection...');
        $this->newLine();

        // Test 1: Connection
        try {
            Redis::ping();
            $this->line('<fg=green>✓ Redis Connection: SUCCESS</fg=green>');
            $this->line('   Location: ' . config('database.redis.default.host') . ':' . config('database.redis.default.port'));
        } catch (\Exception $e) {
            $this->line('<fg=red>✗ Redis Connection: FAILED</fg=red>');
            $this->line('   Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Cache Store
        try {
            Cache::put('redis_test', 'success', 60);
            $value = Cache::get('redis_test');
            
            if ($value === 'success') {
                $this->line('<fg=green>✓ Cache Store (Redis): SUCCESS</fg=green>');
                $this->line('   CACHE_STORE=' . config('cache.default'));
                Cache::forget('redis_test');
            } else {
                $this->line('<fg=red>✗ Cache Store: FAILED</fg=red>');
                return 1;
            }
        } catch (\Exception $e) {
            $this->line('<fg=red>✗ Cache Store: FAILED</fg=red>');
            $this->line('   Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 3: Session Driver
        try {
            $this->line('<fg=green>✓ Session Driver: Configured</fg=green>');
            $this->line('   SESSION_DRIVER=' . config('session.driver'));
        } catch (\Exception $e) {
            $this->line('<fg=yellow>⚠ Session Driver: Warning</fg=yellow>');
            $this->line('   Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 4: Queue Connection
        try {
            $this->line('<fg=green>✓ Queue Connection: Configured</fg=green>');
            $this->line('   QUEUE_CONNECTION=' . config('queue.default'));
        } catch (\Exception $e) {
            $this->line('<fg=yellow>⚠ Queue Connection: Warning</fg=yellow>');
            $this->line('   Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 5: Helper Functions
        try {
            RedisHelper::put('helper_test', ['test' => 'data'], 60);
            $value = RedisHelper::get('helper_test');
            
            if ($value['test'] === 'data') {
                $this->line('<fg=green>✓ RedisHelper: SUCCESS</fg=green>');
                RedisHelper::forget('helper_test');
            } else {
                $this->line('<fg=red>✗ RedisHelper: FAILED</fg=red>');
                return 1;
            }
        } catch (\Exception $e) {
            $this->line('<fg=red>✗ RedisHelper: FAILED</fg=red>');
            $this->line('   Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 6: Get Redis Info
        try {
            $info = Redis::info();
            $this->line('<fg=green>✓ Redis Server Information:</fg=green>');
            $this->line('   Version: ' . $info['Server']['redis_version'] ?? 'Unknown');
            $this->line('   Connected Clients: ' . $info['Clients']['connected_clients'] ?? 'Unknown');
            $this->line('   Used Memory: ' . $info['Memory']['used_memory_human'] ?? 'Unknown');
        } catch (\Exception $e) {
            $this->line('<fg=yellow>⚠ Could not fetch Redis info</fg=yellow>');
        }

        $this->newLine();
        $this->info('✅ All Redis tests completed successfully!');
        $this->newLine();

        $this->line('📚 Usage Examples:');
        $this->line('   Cache::put("key", "value", 3600);');
        $this->line('   RedisHelper::put("key", $data, 3600);');
        $this->line('   RedisHelper::get("key");');
        $this->line('   RedisHelper::increment("counter");');
        $this->newLine();

        return 0;
    }
}
