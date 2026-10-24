<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterestRequestAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.requests.index', [
            'requests' => InterestRequest::with(['user', 'venue.regency.province'])
                ->orderByRaw("FIELD(status, 'pending', 'contacted', 'done')")
                ->orderByDesc('created_at')
                ->paginate(20),
        ]);
    }

    public function update(Request $request, InterestRequest $interestRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,contacted,done'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $interestRequest->update([
            ...$validated,
            'responded_at' => $validated['status'] !== 'pending' ? now() : null,
        ]);

        return back()->with('success', 'Permintaan diperbarui.');
    }
}
