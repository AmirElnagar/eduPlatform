<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * Get all payments
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $query = Payment::with(['enrollment.student.user', 'enrollment.group.teacher.user']);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['from_date'])) {
            $query->whereDate('created_at', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->whereDate('created_at', '<=', $validated['to_date']);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($payments);
    }

    /**
     * Get payment statistics
     */
    public function getStats(Request $request)
    {
        $stats = [
            'total_revenue' => Payment::completed()->sum('amount'),
            'pending_amount' => Payment::pending()->sum('amount'),
            'total_transactions' => Payment::count(),
            'successful_transactions' => Payment::completed()->count(),
            'failed_transactions' => Payment::byStatus('failed')->count(),
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
