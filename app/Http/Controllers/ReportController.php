<?php

namespace App\Http\Controllers;

use App\Enums\ReportType;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $reportedUser    = $request->user_id    ? User::findOrFail($request->user_id)    : null;
        $reportedProduct = $request->product_id ? Product::findOrFail($request->product_id) : null;
        $order           = $request->order_id   ? Order::findOrFail($request->order_id)  : null;

        return view('reports.create', compact('reportedUser', 'reportedProduct', 'order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id'    => 'nullable|exists:users,id',
            'reported_product_id' => 'nullable|exists:products,id',
            'order_id'            => 'nullable|exists:orders,id',
            'type'                => 'required|in:' . implode(',', array_column(ReportType::cases(), 'value')),
            'description'         => 'required|string|min:10|max:2000',
        ]);

        $validated['reporter_id'] = auth()->id();
        $validated['status']      = 'pending';

        Report::create($validated);

        return back()->with('success', 'Your report has been submitted. Our team will review it shortly.');
    }
}
