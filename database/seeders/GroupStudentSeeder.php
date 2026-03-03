<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupStudentSeeder extends Seeder
{
    public function run(): void
    {
        $groups   = Group::all();
        $students = Student::all();

        foreach ($groups as $group) {
            // كل مجموعة تاخد 4-8 طلاب عشوائيين
            $assigned = $students->random(min(rand(4, 8), $students->count()));

            foreach ($assigned as $student) {
                $status        = ['active', 'active', 'active', 'pending', 'expired'][rand(0, 4)];
                $paymentStatus = $status === 'active' ? 'paid' : ($status === 'pending' ? 'unpaid' : 'paid');

                DB::table('group_student')->insertOrIgnore([
                    'group_id'       => $group->id,
                    'student_id'     => $student->id,
                    'status'         => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentStatus === 'paid' ? ['online', 'cash'][rand(0, 1)] : null,
                    'enrolled_at'    => $status === 'active' ? now()->subDays(rand(5, 60)) : null,
                    'expires_at'     => $status === 'active' ? now()->addDays(rand(15, 45)) : null,
                    'amount_paid'    => $paymentStatus === 'paid' ? $group->price : 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // تحديث عدد الطلاب الفعليين
            $group->update([
                'current_students' => DB::table('group_student')
                    ->where('group_id', $group->id)
                    ->where('status', 'active')
                    ->count(),
            ]);
        }
    }
}
