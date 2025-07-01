<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance;
use App\Models\NotificationState;
use Carbon\Carbon;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('teacher.layouts.app', function ($view) {
            $pendingNotifications = [];

            if (Auth::check() && Auth::user()->role === 'guru') {
                $user = Auth::user();
                $today = Carbon::now('Asia/Jakarta');
                $todayDateString = $today->toDateString();
                $dayOfWeek = $today->isoFormat('dddd');

                $schedulesToday = Schedule::where('user_id', $user->id)
                                          ->where('day_of_week', $dayOfWeek)
                                          ->with(['class', 'subject'])
                                          ->orderBy('start_time')
                                          ->get();

                foreach ($schedulesToday as $schedule) {
                    // --- LOGIKA UNTUK JURNAL ---
                    $journalExists = Journal::where('user_id', $user->id)
                                            ->where('class_id', $schedule->class_id)
                                            ->where('subject_id', $schedule->subject_id)
                                            ->whereDate('date', $todayDateString)
                                            ->whereTime('start_time', Carbon::parse($schedule->start_time)->format('H:i:s'))
                                            ->whereTime('end_time', Carbon::parse($schedule->end_time)->format('H:i:s'))
                                            ->exists();

                    // Cek apakah notifikasi jurnal untuk jadwal ini sudah diabaikan di database.
                    $journalIsDismissed = NotificationState::where('user_id', $user->id)
                                                            ->where('schedule_id', $schedule->id)
                                                            ->where('date', $todayDateString)
                                                            ->where('type', 'journal')
                                                            ->where('is_dismissed', true) // <--- Cari yang sudah di-dismissed
                                                            ->exists();

                    // Tampilkan notifikasi jurnal jika jurnal belum ada DAN notifikasi ini BELUM di-dismissed.
                    if (!$journalExists && !$journalIsDismissed) { // <--- KONDISI KUNCI
                        $pendingNotifications[] = (object) [
                            'type' => 'journal',
                            'schedule_id' => $schedule->id,
                            'date' => $todayDateString,
                            'class_name' => $schedule->class->name ?? 'N/A',
                            'subject_name' => $schedule->subject->name ?? 'N/A',
                            'at_time' => $schedule->at_time ?? null, // Tambahkan at_time
                            'time_slot' => Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . Carbon::parse($schedule->end_time)->format('H:i'),
                            'action_url' => route('teacher.journals.create', ['schedule_id' => $schedule->id]),
                            'message' => 'Jurnal untuk kelas <strong>' . ($schedule->class->name ?? 'N/A') . '</strong> mata pelajaran <strong>' . ($schedule->subject->name ?? 'N/A') . '</strong> Jam ke-<strong>' . ($schedule->at_time ?? '-') . '</strong> pada waktu ' . Carbon::parse($schedule->start_time)->format('H:i') . ' belum diisi.',
                            'icon' => 'fas fa-book',
                            'bg_color' => 'bg-red-100',
                            'text_color' => 'text-red-600',
                        ];
                    }

                    // --- LOGIKA UNTUK ABSENSI ---
                    $attendanceExists = Attendance::where('schedule_id', $schedule->id)
                                                    ->whereDate('date', $todayDateString)
                                                    ->exists();

                    // Cek apakah notifikasi absensi untuk jadwal ini sudah diabaikan di database.
                    $attendanceIsDismissed = NotificationState::where('user_id', $user->id)
                                                                ->where('schedule_id', $schedule->id)
                                                                ->where('date', $todayDateString)
                                                                ->where('type', 'attendance')
                                                                ->where('is_dismissed', true) // <--- Cari yang sudah di-dismissed
                                                                ->exists();

                    // Tampilkan notifikasi absensi jika absensi belum ada DAN notifikasi ini BELUM di-dismissed.
                    if (!$attendanceExists && !$attendanceIsDismissed) { // <--- KONDISI KUNCI
                        $pendingNotifications[] = (object) [
                            'type' => 'attendance',
                            'schedule_id' => $schedule->id,
                            'date' => $todayDateString,
                            'class_name' => $schedule->class->name ?? 'N/A',
                            'subject_name' => $schedule->subject->name ?? 'N/A',
                            'at_time' => $schedule->at_time ?? null, // Tambahkan at_time
                            'time_slot' => Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . Carbon::parse($schedule->end_time)->format('H:i'),
                            'action_url' => route('teacher.attendances.create', ['schedule_id' => $schedule->id]),
                            'message' => 'Absensi untuk kelas <strong>' . ($schedule->class->name ?? 'N/A') . '</strong> mata pelajaran <strong>' . ($schedule->subject->name ?? 'N/A') . '</strong> Jam ke-<strong>' . ($schedule->at_time ?? '-') . '</strong> pada waktu ' . Carbon::parse($schedule->start_time)->format('H:i') . ' belum dicatat.',
                            'icon' => 'fas fa-user-check',
                            'bg_color' => 'bg-orange-100',
                            'text_color' => 'text-orange-600',
                        ];
                    }
                }
            }
            $view->with('pendingNotifications', $pendingNotifications);
        });
    }
}
