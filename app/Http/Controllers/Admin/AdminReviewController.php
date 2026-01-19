<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Get pending reviews
     */
    public function getPending(Request $request)
    {
        $reviews = Review::with(['teacher.user', 'student.user'])
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($reviews);
    }

    /**
     * Approve review
     */
    public function approve(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->approve();

        return response()->json([
            'message' => 'تم الموافقة على التقييم',
            'review' => $review,
        ]);
    }

    /**
     * Reject review
     */
    public function reject(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->reject();

        return response()->json([
            'message' => 'تم رفض التقييم',
        ]);
    }

    /**
     * Delete review
     */
    public function delete(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();

        return response()->json([
            'message' => 'تم حذف التقييم',
        ]);
    }
}
