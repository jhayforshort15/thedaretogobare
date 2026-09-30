<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('pages/Contact', [
            'contact' => [
                'address' => '600 N Washington Ave, Suite C203',
                'phone' => '+1 (615) 796-0610',
                'email' => 'support@thedaretogobare.com',
                'hours' => '24 hours (Monday – Sunday)',
            ],
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $recipient = config('services.store.order_notification_email');

        try {
            if (! empty($recipient)) {
                Mail::to($recipient)->send(new ContactMessageMail($data['name'], $data['email'], $data['message']));
            }
        } catch (\Throwable $e) {
            Log::warning('Contact message email failed', ['error' => $e->getMessage()]);
        }

        return back()->with('success', "Thanks for reaching out, {$data['name']}! We'll be in touch soon.");
    }
}
