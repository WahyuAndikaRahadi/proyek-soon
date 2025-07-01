<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\NotificationState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function markAsDismissed(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $notificationsToDismiss = $request->input('notifications');

        if (empty($notificationsToDismiss)) {
            return response()->json(['success' => false, 'message' => 'No notifications provided.'], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($notificationsToDismiss as $notification) {
                $date = Carbon::parse($notification['date'])->format('Y-m-d');

                // KUNCI: Gunakan updateOrCreate.
                // Ini akan mencari entri berdasarkan user_id, schedule_id, date, dan type.
                // Jika ditemukan, is_dismissed akan diupdate menjadi TRUE.
                // Jika tidak ditemukan, entri baru akan dibuat dengan is_dismissed TRUE.
                NotificationState::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'schedule_id' => $notification['schedule_id'],
                        'type' => $notification['type'],
                        'date' => $date,
                    ],
                    [
                        'is_dismissed' => true, // Selalu set ke TRUE saat ditandai selesai dibaca
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Notifications marked as dismissed.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error marking notifications as dismissed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to mark notifications as dismissed: ' . $e->getMessage()], 500);
        }
    }
}