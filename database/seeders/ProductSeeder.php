<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    // Chargement des articles initiaux pour Tsena Kely
    public function run(): void
    {
        $products = [
            [
                'name'        => 'Mofo baolina (Lot de 5)',
                'description' => 'Beignets traditionnels malgaches sucres et moelleux.',
                'price'       => 2500,
                'stock'       => 45,
                'category'    => 'Gouter & Fritures',
            ],
            [
                'name'        => 'Sambos viande (Lot de 3)',
                'description' => 'Samoussas croustillants a la viande de boeuf parfume.',
                'price'       => 3000,
                'stock'       => 30,
                'category'    => 'Gouter & Fritures',
            ],
            [
                'name'        => 'Kafe Gasy torrefie 250g',
                'description' => 'Cafe moulu artisanal de la cote est de Madagascar.',
                'price'       => 7500,
                'stock'       => 20,
                'category'    => 'Boissons',
            ],
            [
                'name'        => 'The a la citronnelle 100g',
                'description' => 'Infusion naturelle de citronnelle sechee parfumee.',
                'price'       => 3500,
                'stock'       => 25,
                'category'    => 'Boissons',
            ],
            [
                'name'        => 'Vary gasy mena (Sac 5kg)',
                'description' => 'Riz rouge traditionnel riche en mineraux et fibres.',
                'price'       => 18000,
                'stock'       => 15,
                'category'    => 'Epicerie',
            ],
            [
                'name'        => 'Miel pur de Manakara 500g',
                'description' => 'Miel d eucalyptus 100% naturel recolte artisanalement.',
                'price'       => 14000,
                'stock'       => 12,
                'category'    => 'Epicerie',
            ],
            [
                'name'        => 'Mofo akondro (Lot de 4)',
                'description' => 'Beignets a la banane douce frits a la minute.',
                'price'       => 2000,
                'stock'       => 35,
                'category'    => 'Gouter & Fritures',
            ],
            [
                'name'        => 'Lasary karaoty & sakay 200g',
                'description' => 'Condiment de carottes pimentees pour accompagner le riz.',
                'price'       => 4000,
                'stock'       => 18,
                'category'    => 'Epicerie',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'name'        => $p['name'],
                    'description' => $p['description'],
                    'price'       => $p['price'],
                    'stock'       => $p['stock'],
                    'category'    => $p['category'],
                ]
            );
        }

        // Initialisation des parametres d injection de panne
        SystemSetting::updateOrCreate(['key' => 'chaos_latency_ms'], ['value' => '0']);
        SystemSetting::updateOrCreate(['key' => 'chaos_error_rate'], ['value' => '0']);
        SystemSetting::updateOrCreate(['key' => 'chaos_stock_down'], ['value' => '0']);
        SystemSetting::updateOrCreate(['key' => 'chaos_orders_down'], ['value' => '0']);
    }
}
