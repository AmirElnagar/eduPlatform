<?php

namespace Database\Seeders;

use App\Models\ParentModel; // أو ParentModel حسب اسم الموديل عندك
use App\Models\User;
use Illuminate\Database\Seeder;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentUsers = User::where('role', 'parent')->orderBy('id')->get();

        foreach ($parentUsers as $user) {
            // تأكد من اسم الموديل الصحيح عندك (Parent محجوز في PHP)
            ParentModel::create([
                'user_id'    => $user->id,
                'address'    => ['القاهرة', 'الإسكندرية', 'الجيزة'][rand(0, 2)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
