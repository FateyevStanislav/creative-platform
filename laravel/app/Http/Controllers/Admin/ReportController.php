<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(int $id)
    {
        $report = Report::with(['user', 'reviewer'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, int $id)
    {
        $report = Report::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,reviewed,rejected,accepted',
        ]);

        $report->update([
            'status' => $data['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Жалоба обновлена.');
    }
}