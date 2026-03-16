<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PaymentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => PaymentOrder::count(),
            'paid' => PaymentOrder::where('status', 'paid')->count(),
            'pending' => PaymentOrder::where('status', 'pending')->count(),
            'expired' => PaymentOrder::where('status', 'expired')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }

    public function payments(Request $request)
    {
        $query = PaymentOrder::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('reff', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $payments = $query->paginate(10)->withQueryString();

        return Inertia::render('payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'status', 'sort', 'direction'])
        ]);
    }

    public function toggleFlag(Request $request, $id)
    {
        $payment = PaymentOrder::findOrFail($id);

        if ($payment->flagged_at) {
            $payment->flagged_at = null;
            $payment->flagged_by_user_id = null;
        } else {
            $payment->flagged_at = now();
            $payment->flagged_by_user_id = Auth::id();
        }

        $payment->save();

        return redirect()->back()->with('success', 'Flag successfully updated.');
    }
}
