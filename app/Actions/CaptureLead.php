<?php

namespace App\Actions;

use App\Models\Lead;
use App\Models\Service;
use App\Notifications\LeadReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CaptureLead
{
    /**
     * Persist a lead and notify the team.
     *
     * The record is written before anything else is attempted. Mail is the
     * part most likely to fail — bad credentials, a provider outage — and
     * losing a genuine enquiry to a mail error is unacceptable, so a
     * notification failure is logged rather than thrown.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, Request $request, string $source = 'contact'): Lead
    {
        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'service_id' => isset($data['service'])
                ? Service::where('slug', $data['service'])->value('id')
                : null,
            'budget_range' => $data['budget_range'] ?? null,
            'timeline' => $data['timeline'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => $source,
            'payload' => $data['payload'] ?? null,
            'locale' => app()->getLocale(),
            'referrer' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notify($lead);

        // Read once by the analytics component on the page the visitor
        // lands on, so a refresh cannot report the same conversion twice.
        session()->flash('conversion', [
            'event' => 'lead_submitted',
            'properties' => array_filter([
                'source' => $lead->source,
                'service' => (string) ($lead->service?->title ?? ''),
                'budget' => $lead->budget_range,
            ]),
        ]);

        return $lead;
    }

    protected function notify(Lead $lead): void
    {
        $recipient = config('site.leads.notify');

        if (blank($recipient)) {
            return;
        }

        try {
            Notification::route('mail', $recipient)->notify(new LeadReceived($lead));
        } catch (\Throwable $exception) {
            Log::error('Lead notification failed', [
                'lead_id' => $lead->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
