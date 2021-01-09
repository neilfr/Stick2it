<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert(" INSERT INTO `foodgroups` (`id`, `description`, `created_at`) VALUES
            (1,'Dairy and Egg Products', now()),
            (2,'Spices and Herbs', now()),
            (3,'Babyfoods', now()),
            (4,'Fats and Oils', now()),
            (5,'Poultry Products', now()),
            (6,'Soups, Sauces and Gravies', now()),
            (7,'Sausages and Luncheon meats', now()),
            (8,'Breakfast cereals', now()),
            (9,'Fruits and fruit juices', now()),
            (10,'Pork Products', now()),
            (11,'Vegetables and Vegetable Products', now()),
            (12,'Nuts and Seeds', now()),
            (13,'Beef Products', now()),
            (14,'Beverages', now()),
            (15,'Finfish and Shellfish Products', now()),
            (16,'Legumes and Legume Products', now()),
            (17,'Lamb, Veal and Game', now()),
            (18,'Baked Products', now()),
            (19,'Sweets', now()),
            (20,'Cereals, Grains and Pasta', now()),
            (21,'Fast Foods', now()),
            (22,'Mixed Dishes', now()),
            (25,'Snacks', now()),
            (26,'Meals', now());
        ");
    }
}
