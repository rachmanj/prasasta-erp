<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserItemPreferencesService
{
    /**
     * Track item usage for a user
     */
    public function trackItemUsage($itemId)
    {
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();

        DB::table('user_item_preferences')
            ->updateOrInsert(
                ['user_id' => $userId, 'item_id' => $itemId],
                [
                    'usage_count' => DB::raw('usage_count + 1'),
                    'last_used_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }

    /**
     * Get recent items for a user
     */
    public function getRecentItems($userId, $limit = 10)
    {
        return DB::table('user_item_preferences')
            ->join('items', 'user_item_preferences.item_id', '=', 'items.id')
            ->where('user_item_preferences.user_id', $userId)
            ->where('items.is_active', 1)
            ->select([
                'items.id',
                'items.code',
                'items.name',
                'items.unit_of_measure',
                'user_item_preferences.usage_count',
                'user_item_preferences.last_used_at'
            ])
            ->orderBy('user_item_preferences.last_used_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get frequently used items for a user
     */
    public function getFrequentlyUsedItems($userId, $limit = 10)
    {
        return DB::table('user_item_preferences')
            ->join('items', 'user_item_preferences.item_id', '=', 'items.id')
            ->where('user_item_preferences.user_id', $userId)
            ->where('items.is_active', 1)
            ->select([
                'items.id',
                'items.code',
                'items.name',
                'items.unit_of_measure',
                'user_item_preferences.usage_count',
                'user_item_preferences.last_used_at'
            ])
            ->orderBy('user_item_preferences.usage_count', 'desc')
            ->orderBy('user_item_preferences.last_used_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's favorite items (combination of recent and frequent)
     */
    public function getFavoriteItems($userId, $limit = 10)
    {
        // Get items that are both recent and frequently used
        $recentItems = $this->getRecentItems($userId, $limit * 2);
        $frequentItems = $this->getFrequentlyUsedItems($userId, $limit * 2);

        // Combine and deduplicate
        $allItems = collect();

        foreach ($recentItems as $item) {
            $allItems->put($item->id, $item);
        }

        foreach ($frequentItems as $item) {
            if (!$allItems->has($item->id)) {
                $allItems->put($item->id, $item);
            }
        }

        return $allItems->take($limit)->values();
    }

    /**
     * Clear old preferences (older than specified days)
     */
    public function clearOldPreferences($days = 90)
    {
        return DB::table('user_item_preferences')
            ->where('last_used_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get user's item usage statistics
     */
    public function getUserStats($userId)
    {
        return DB::table('user_item_preferences')
            ->where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_items_used,
                SUM(usage_count) as total_usage_count,
                MAX(last_used_at) as last_activity,
                AVG(usage_count) as average_usage_per_item
            ')
            ->first();
    }
}
