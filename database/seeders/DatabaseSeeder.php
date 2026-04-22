<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Always ensure the admin account exists with the correct role.
        User::updateOrCreate(
            ['email' => 'admin@tabuan.com'],
            ['name' => 'Admin TABUAN', 'role' => 'admin', 'password' => Hash::make('password'), 'account_status' => 'approved', 'is_active' => true, 'is_verified' => true, 'location' => 'Cantilan, Surigao del Sur']
        );

        if (User::where('role', 'farmer')->exists()) {
            $this->command->info('Already seeded — skipping demo data.');
            return;
        }

        $farmersData = [
            ['name'=>'Juan dela Cruz','email'=>'juan@tabuan.com','farm_name'=>"Juan's Organic Farm",'barangay'=>'Bunawan','purok'=>'3','latitude'=>9.3139,'longitude'=>125.9897,'bio'=>'Third-generation farmer specializing in organic vegetables and herbs.'],
            ['name'=>'Maria Santos','email'=>'maria@tabuan.com','farm_name'=>"Maria's Fruit Garden",'barangay'=>'Poblacion','purok'=>'5','latitude'=>9.3155,'longitude'=>125.9912,'bio'=>'Expert in growing tropical fruits. Supplying fresh produce for 10+ years.'],
            ['name'=>'Pedro Reyes','email'=>'pedro@tabuan.com','farm_name'=>"Pedro's Rice & Grains",'barangay'=>'Panikian','purok'=>'1','latitude'=>9.3108,'longitude'=>125.9875,'bio'=>'Certified organic rice farmer with 20 acres of paddy fields.'],
        ];

        $farmerUsers = [];
        foreach ($farmersData as $f) {
            $farmerUsers[] = User::create(array_merge($f, ['role'=>'farmer','password'=>Hash::make('password'),'account_status'=>'approved','is_active'=>true,'is_verified'=>true,'verified_at'=>now(),'location'=>'Cantilan, Surigao del Sur','phone'=>'09'.rand(100000000,999999999)]));
        }

        User::create(['name'=>'Ana Garcia','email'=>'buyer@tabuan.com','role'=>'buyer','password'=>Hash::make('password'),'account_status'=>'approved','location'=>'Davao City','is_active'=>true,'phone'=>'09171234567']);

        $cats = [];
        foreach ([['name'=>'Vegetables','slug'=>'vegetables','icon'=>'🥬','color'=>'#16a34a','description'=>'Fresh farm vegetables'],['name'=>'Fruits','slug'=>'fruits','icon'=>'🍎','color'=>'#ea580c','description'=>'Sweet tropical fruits'],['name'=>'Grains','slug'=>'grains','icon'=>'🌾','color'=>'#ca8a04','description'=>'Rice, corn, and grains'],['name'=>'Livestock','slug'=>'livestock','icon'=>'🐔','color'=>'#9333ea','description'=>'Poultry and livestock'],['name'=>'Dairy & Eggs','slug'=>'dairy-eggs','icon'=>'🥚','color'=>'#0891b2','description'=>'Fresh eggs and dairy'],['name'=>'Herbs','slug'=>'herbs','icon'=>'🌿','color'=>'#16a34a','description'=>'Aromatic herbs and spices']] as $c) {
            $cats[$c['slug']] = Category::create($c);
        }

        $products = [
            [0,'Fresh Tomatoes','vegetables',45,60,'kg',100,true,true,4.8,24,156,'Vine-ripened red tomatoes picked fresh every morning. No pesticides, grown organically in Cantilan soil.'],
            [0,'Kangkong (Water Spinach)','vegetables',25,null,'bundle',80,true,false,4.5,12,88,'Fresh kangkong harvested this morning. Tender and crispy, great for sautéing with garlic.'],
            [0,'Lemongrass','herbs',20,null,'bundle',120,true,false,4.7,8,45,'Freshly cut lemongrass. Highly aromatic, perfect for cooking and herbal tea.'],
            [0,'Pechay (Bok Choy)','vegetables',30,null,'bundle',60,false,false,4.3,6,72,'Crisp and tender pechay. Ideal for soups, stir-fry, and steamed dishes.'],
            [1,'Sweet Carabao Mangoes','fruits',120,150,'kg',50,false,true,4.9,45,230,'Premium Philippine carabao mangoes — the sweetest in the world. Freshly harvested at peak ripeness.'],
            [1,'Pineapple','fruits',80,null,'pc',40,false,true,4.6,19,95,'Freshly harvested pineapples. Sweet, juicy and full of flavor.'],
            [1,'Calamansi','fruits',60,75,'kg',60,true,false,4.4,11,67,'Locally grown calamansi, bursting with citrus flavor. Rich in Vitamin C.'],
            [1,'Free Range Eggs','dairy-eggs',12,null,'pc',300,true,true,4.7,33,412,'Eggs from free-range native chickens. Deep yellow yolk, rich flavor, no antibiotics.'],
            [2,'Brown Rice','grains',55,65,'kg',200,true,true,4.8,28,350,'Freshly milled organic brown rice. Rich in fiber and nutrients, grown without chemicals.'],
            [2,'Sweet Potato (Camote)','vegetables',35,null,'kg',150,false,false,4.2,9,118,'Orange-fleshed sweet potato. Naturally sweet, high in beta-carotene and great for roasting.'],
        ];

        foreach ($products as [$fi,$name,$cat,$price,$orig,$unit,$stock,$organic,$featured,$rating,$reviews,$sold,$desc]) {
            Product::create(['user_id'=>$farmerUsers[$fi]->id,'category_id'=>$cats[$cat]->id,'name'=>$name,'slug'=>Str::slug($name).'-'.Str::random(6),'description'=>$desc,'price'=>$price,'original_price'=>$orig,'unit'=>$unit,'stock'=>$stock,'location'=>'Cantilan, Surigao del Sur','is_organic'=>$organic,'is_featured'=>$featured,'status'=>'active','avg_rating'=>$rating,'total_reviews'=>$reviews,'total_sold'=>$sold]);
        }

        $this->command->info('TABUAN seeded! Accounts: admin@tabuan.com | juan@tabuan.com | buyer@tabuan.com (password: password)');
    }
}
