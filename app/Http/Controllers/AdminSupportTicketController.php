<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminSupportTicketController extends Controller
{
    /**
     * Display all support tickets for admin
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with('user', 'assignedAdmin');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // Default: show open and in progress tickets
            $query->whereIn('status', [SupportTicket::STATUS_OPEN, SupportTicket::STATUS_IN_PROGRESS, SupportTicket::STATUS_PENDING_USER_ACTION]);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by assigned admin
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by ticket number or subject
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->paginate(20);
        $statuses = SupportTicket::getStatusLabels();
        $priorities = SupportTicket::getPriorityLabels();
        $categories = SupportTicket::getCategoryLabels();
        $admins = Admin::all();

        return view('admin.support-tickets.index', compact('tickets', 'statuses', 'priorities', 'categories', 'admins'));
    }

    /**
     * Display the specified support ticket
     */
    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load('user', 'assignedAdmin');
        $admins = Admin::all();
        $statuses = SupportTicket::getStatusLabels();
        $priorities = SupportTicket::getPriorityLabels();

        return view('admin.support-tickets.show', compact('supportTicket', 'admins', 'statuses', 'priorities'));
    }

    /**
     * Update the specified support ticket
     */
    public function update(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,pending_user_action,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:admins,id',
            'notes' => 'nullable|string|max:5000',
        ]);

        $supportTicket->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'assigned_to' => $validated['assigned_to'],
            'notes' => $validated['notes'],
            'resolved_at' => in_array($validated['status'], [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED]) 
                ? now() 
                : null,
        ]);

        return redirect()->route('admin.support-tickets.show', $supportTicket)
            ->with('success', 'Tiket dukungan berhasil diperbarui.');
    }

    /**
     * Close the specified support ticket
     */
    public function close(SupportTicket $supportTicket)
    {
        $supportTicket->update([
            'status' => SupportTicket::STATUS_CLOSED,
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.support-tickets.show', $supportTicket)
            ->with('success', 'Tiket dukungan berhasil ditutup.');
    }

    /**
     * Assign ticket to admin
     */
    public function assign(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:admins,id',
        ]);

        $supportTicket->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', 'Tiket berhasil ditugaskan ke admin.');
    }
}
