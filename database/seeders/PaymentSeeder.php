<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = DB::table('group_student')
            ->where('payment_status', 'paid')
            ->get();

        foreach ($enrollments as $enrollment) {
            $group  = Group::find($enrollment->group_id);
            if (!$group) continue;

            $method = $enrollment->payment_method ?? 'cash';
            $isOnline = $method === 'online';

            DB::table('payments')->insert([
                'student_id'      => $enrollment->student_id,
                'group_id'        => $enrollment->group_id,
                'teacher_id'      => $group->teacher_id,
                'amount'          => $group->price,
                'payment_method'  => $method,
                'status'          => 'completed',
                'transaction_id'  => $isOnline ? 'TXN-' . strtoupper(Str::random(12)) : null,
                'payment_gateway' => $isOnline ? ['paymob', 'fawry', 'vodafone_cash'][rand(0, 2)] : null,
                'payment_details' => $isOnline ? json_encode(['ref' => rand(100000, 999999), 'currency' => 'EGP']) : null,
                'notes'           => !$isOnline ? 'تم استلام المبلغ نقداً' : null,
                'paid_at'         => now()->subDays(rand(1, 30)),
                'period_start'    => now()->startOfMonth(),
                'period_end'      => now()->endOfMonth(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
