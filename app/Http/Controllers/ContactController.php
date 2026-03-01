<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Simple Honeypot
        if ($request->filled('website')) {
            return response()->json(['message' => 'Spam detected.'], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
            'subject' => 'nullable|string|max:255',
        ]);

        $message = ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Optional: Send email to admin
        // Mail::to(config('mail.from.address'))->send(new \App\Mail\NewContactMessage($message));

        return back()->with('success', 'Thank you! Your message has been sent.');
    }
}
