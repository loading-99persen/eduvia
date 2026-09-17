<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Daftar laporan user
    public function index()
    {
        $reports = Report::where('id_user', Auth::id())
            ->latest()
            ->get();

        return view('report.index', compact('reports'));
    }

    // Kirim laporan
    public function store(Request $request)
    {
        $request->validate([
            'tipe_target' => 'required',
            'id_target' => 'required',
            'alasan' => 'required'
        ]);

        Report::create([
            'id_user' => Auth::id(),
            'tipe_target' => $request->tipe_target,
            'id_target' => $request->id_target,
            'alasan' => $request->alasan,
            'status' => 'menunggu'
        ]);

        return back()
            ->with('success', 'Laporan berhasil dikirim.');
    }

    // Semua laporan admin
    public function admin()
    {
        $reports = Report::with('user')
            ->latest()
            ->get();

        return view('admin.report.index', compact('reports'));
    }

    // Update status laporan
    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'status' => 'required'
        ]);

        $report->update([
            'status' => $request->status
        ]);

        return back()
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}