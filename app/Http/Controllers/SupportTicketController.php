<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display all support tickets for the current user
     */
    public function index(Request $request)
    {
        $query = SupportTicket::query()
            ->with(['user', 'assignedAdmin']);

        // Filter keyword
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('subject', 'like', "%$q%")
                ->orWhere('ticket_number', 'like', "%$q%")
                ->orWhereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%$q%");
                });
            });
        }

        $query->when($request->filled('created_from'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->created_from);
        });

        $query->when($request->filled('created_to'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->created_to);
        });

        $tickets = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.support-tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new support ticket
     */
    public function create()
    {
        return view('support-tickets.create');
    }

    /**
     * Store a newly created support ticket in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10|max:5000',
            'category' => 'required|in:business_data,email_change,account_access,technical_issue,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = auth()->user()->supportTickets()->create([
            'ticket_number' => SupportTicket::generateNumber(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => SupportTicket::STATUS_OPEN,
        ]);

        return redirect()->route('support-tickets.show', $ticket)
            ->with('success', 'Tiket dukungan berhasil dibuat. Nomor tiket: ' . $ticket->ticket_number);
    }

    /**
     * Display the specified support ticket
     */
    public function show(SupportTicket $supportTicket)
    {
        // Ensure user can only view their own tickets
        if ($supportTicket->user_id !== auth()->id()) {
            abort(403);
        }

        return view('support-tickets.show', compact('supportTicket'));
    }
}
