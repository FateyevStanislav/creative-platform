<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $targetType = $request->query('target_type');
        $targetId = $request->query('target_id');

        return view('reports.create', compact('targetType', 'targetId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type' => 'required|in:post,comment,user',
            'target_id' => 'required|integer',
            'reason' => 'required|in:spam,abuse,plagiarism,other',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($data['target_type'] === 'user' && $data['target_id'] === Auth::id()) {
            return back()->withErrors(['target_id' => 'Нельзя жаловаться на себя.']);
        }

        Report::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Жалоба отправлена.');
    }
}