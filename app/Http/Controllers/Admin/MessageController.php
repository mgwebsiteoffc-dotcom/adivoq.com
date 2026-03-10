<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(20)->appends($request->query());

        $unreadCount = ContactMessage::where('status', 'unread')->count();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function update(Request $request, ContactMessage $message)
    {
        $request->validate([
            'status' => 'required|in:read,replied',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $data = ['status' => $request->status, 'admin_notes' => $request->admin_notes];
        if ($request->status === 'replied') {
            $data['replied_at'] = now();
        }

        $message->update($data);

        return back()->with('success', 'Message updated.');
    }
}