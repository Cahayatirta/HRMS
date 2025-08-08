<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class AbsenController extends Controller
{
    public function masuk(Request $request)
    {
        dd($request->location_type);
        $request->validate([
            'location_type' => 'required',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'account_id' => Auth::id(),
            'clock_in' => now(),
            'location' => $request->location_type,
        ];

        if ($request->location_type === 'anywhere' && $request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('absen_photos', 'public');
        }

        Attendance::create($data);

        return back()->with('message', 'Absen masuk berhasil.');
    }

    public function keluar(Request $request)
    {
        $attendance = Attendance::where('account_id', Auth::id())
            ->whereDate('clock_in', now()->toDateString())
            ->first();

        if (!$attendance) {
            return back()->with('message', 'Tidak ditemukan absen masuk hari ini.');
        }

        $attendance->update([
            'clock_out' => now(),
            'task_link' => $request->task_link,
            'completed_task_id' => $request->completed_task_id,
        ]);

        return back()->with('message', 'Absen keluar berhasil.');
    }
}
