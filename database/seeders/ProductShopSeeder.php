<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductShopSeeder extends Seeder
{
    public function run(): void
    {
        $productShops = [
            ['id' => 1, 'produit_id' => 1, 'shop_id' => 1, 'stock_particulier' => 8, 'created_at' => '2026-09-26 16:13:02', 'updated_at' => '2026-10-11 15:50:37'],
            ['id' => 2, 'produit_id' => 2, 'shop_id' => 1, 'stock_particulier' => 9, 'created_at' => '2026-10-03 18:14:18', 'updated_at' => '2026-10-08 13:06:07'],
            ['id' => 3, 'produit_id' => 3, 'shop_id' => 1, 'stock_particulier' => 10, 'created_at' => '2026-10-03 18:14:31', 'updated_at' => '2026-10-03 18:14:31'],
            ['id' => 4, 'produit_id' => 4, 'shop_id' => 1, 'stock_particulier' => 77, 'created_at' => '2026-10-03 18:14:59', 'updated_at' => '2026-10-10 20:02:08'],
            ['id' => 5, 'produit_id' => 5, 'shop_id' => 1, 'stock_particulier' => 5, 'created_at' => '2026-10-03 18:15:15', 'updated_at' => '2026-10-03 18:15:15'],
            ['id' => 6, 'produit_id' => 31, 'shop_id' => 1, 'stock_particulier' => 20, 'created_at' => '2026-10-08 18:10:23', 'updated_at' => '2026-10-08 18:10:23'],
            ['id' => 7, 'produit_id' => 14, 'shop_id' => 1, 'stock_particulier' => 32, 'created_at' => '2026-10-08 18:10:50', 'updated_at' => '2026-10-08 18:10:50'],
            ['id' => 8, 'produit_id' => 17, 'shop_id' => 1, 'stock_particulier' => 14, 'created_at' => '2026-10-08 18:12:20', 'updated_at' => '2026-10-08 18:12:20'],
            ['id' => 9, 'produit_id' => 45, 'shop_id' => 1, 'stock_particulier' => 66, 'created_at' => '2026-10-08 18:26:07', 'updated_at' => '2026-10-10 15:24:18'],
            ['id' => 10, 'produit_id' => 27, 'shop_id' => 1, 'stock_particulier' => 160, 'created_at' => '2026-10-10 14:38:54', 'updated_at' => '2026-10-10 14:38:54'],
            ['id' => 11, 'produit_id' => 58, 'shop_id' => 1, 'stock_particulier' => 44, 'created_at' => '2026-10-10 14:47:45', 'updated_at' => '2026-10-10 18:43:16'],
            ['id' => 12, 'produit_id' => 11, 'shop_id' => 1, 'stock_particulier' => 19, 'created_at' => '2026-10-10 14:54:43', 'updated_at' => '2026-10-10 14:54:43'],
            ['id' => 13, 'produit_id' => 10, 'shop_id' => 1, 'stock_particulier' => 38, 'created_at' => '2026-10-10 14:57:51', 'updated_at' => '2026-10-10 14:57:51'],
            ['id' => 14, 'produit_id' => 59, 'shop_id' => 1, 'stock_particulier' => 68, 'created_at' => '2026-10-10 15:09:04', 'updated_at' => '2026-10-10 15:09:04'],
            ['id' => 15, 'produit_id' => 47, 'shop_id' => 1, 'stock_particulier' => 51, 'created_at' => '2026-10-10 15:15:40', 'updated_at' => '2026-10-10 15:15:40'],
            ['id' => 16, 'produit_id' => 46, 'shop_id' => 1, 'stock_particulier' => 43, 'created_at' => '2026-10-10 15:26:28', 'updated_at' => '2026-10-10 15:26:28'],
            ['id' => 17, 'produit_id' => 60, 'shop_id' => 1, 'stock_particulier' => 3, 'created_at' => '2026-10-10 15:31:55', 'updated_at' => '2026-10-10 15:31:55'],
            ['id' => 18, 'produit_id' => 61, 'shop_id' => 1, 'stock_particulier' => 1, 'created_at' => '2026-10-10 15:34:38', 'updated_at' => '2026-10-10 15:34:38'],
            ['id' => 19, 'produit_id' => 66, 'shop_id' => 1, 'stock_particulier' => 100, 'created_at' => '2026-10-10 16:11:52', 'updated_at' => '2026-10-10 16:11:52'],
            ['id' => 20, 'produit_id' => 43, 'shop_id' => 1, 'stock_particulier' => 12, 'created_at' => '2026-10-10 16:13:45', 'updated_at' => '2026-10-10 16:13:45'],
            ['id' => 21, 'produit_id' => 62, 'shop_id' => 1, 'stock_particulier' => 32, 'created_at' => '2026-10-10 18:44:06', 'updated_at' => '2026-10-10 18:45:03'],
            ['id' => 22, 'produit_id' => 63, 'shop_id' => 1, 'stock_particulier' => 16, 'created_at' => '2026-10-10 18:46:35', 'updated_at' => '2026-10-10 18:46:35'],
            ['id' => 23, 'produit_id' => 34, 'shop_id' => 1, 'stock_particulier' => 45, 'created_at' => '2026-10-10 18:51:09', 'updated_at' => '2026-10-10 18:56:11'],
            ['id' => 24, 'produit_id' => 70, 'shop_id' => 1, 'stock_particulier' => 18, 'created_at' => '2026-10-10 19:19:34', 'updated_at' => '2026-10-10 19:25:13'],
            ['id' => 25, 'produit_id' => 22, 'shop_id' => 1, 'stock_particulier' => 14, 'created_at' => '2026-10-10 19:21:43', 'updated_at' => '2026-10-10 19:21:43'],
            ['id' => 26, 'produit_id' => 71, 'shop_id' => 1, 'stock_particulier' => 27, 'created_at' => '2026-10-10 19:24:43', 'updated_at' => '2026-10-10 19:24:43'],
            ['id' => 27, 'produit_id' => 73, 'shop_id' => 1, 'stock_particulier' => 4, 'created_at' => '2026-10-10 19:31:25', 'updated_at' => '2026-10-10 19:31:25'],
            ['id' => 28, 'produit_id' => 74, 'shop_id' => 1, 'stock_particulier' => 4, 'created_at' => '2026-10-10 19:35:52', 'updated_at' => '2026-10-10 19:35:52'],
            ['id' => 29, 'produit_id' => 76, 'shop_id' => 1, 'stock_particulier' => 8, 'created_at' => '2026-10-10 19:42:20', 'updated_at' => '2026-10-10 19:42:20'],
            ['id' => 30, 'produit_id' => 77, 'shop_id' => 1, 'stock_particulier' => 25, 'created_at' => '2026-10-10 19:45:40', 'updated_at' => '2026-10-10 19:45:40'],
            ['id' => 31, 'produit_id' => 83, 'shop_id' => 1, 'stock_particulier' => 12, 'created_at' => '2026-10-10 19:53:08', 'updated_at' => '2026-10-10 19:53:08'],
            ['id' => 32, 'produit_id' => 85, 'shop_id' => 1, 'stock_particulier' => 7, 'created_at' => '2026-10-10 19:57:10', 'updated_at' => '2026-10-10 19:57:10'],
            ['id' => 33, 'produit_id' => 25, 'shop_id' => 1, 'stock_particulier' => 31, 'created_at' => '2026-10-10 20:05:01', 'updated_at' => '2026-10-10 20:05:01'],
            ['id' => 34, 'produit_id' => 87, 'shop_id' => 1, 'stock_particulier' => 18, 'created_at' => '2026-10-10 20:08:19', 'updated_at' => '2026-10-10 20:08:19'],
            ['id' => 35, 'produit_id' => 88, 'shop_id' => 1, 'stock_particulier' => 30, 'created_at' => '2026-10-10 20:11:42', 'updated_at' => '2026-10-10 20:11:42']
        ];

        foreach ($productShops as $item) {
            DB::table('product_shop')->updateOrInsert(['id' => $item['id']], $item);
        }
    }
}