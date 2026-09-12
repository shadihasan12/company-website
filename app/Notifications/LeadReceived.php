<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadReceived extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lead = $this->lead;

        $message = (new MailMessage)
            // Replying to the notification reaches the prospect directly,
            // which is the whole point of receiving it.
            ->replyTo($lead->email, $lead->name)
            ->subject("New {$lead->source} enquiry — {$lead->name}")
            ->greeting("New enquiry from {$lead->name}")
            ->line("**Email:** {$lead->email}");

        foreach ([
            'Phone' => $lead->phone,
            'Company' => $lead->company,
            'Service' => (string) ($lead->service?->title ?? ''),
            'Budget' => $lead->budget_range,
            'Timeline' => $lead->timeline,
        ] as $label => $value) {
            if (filled($value)) {
                $message->line("**{$label}:** {$value}");
            }
        }

        if (filled($lead->message)) {
            $message->line('---')->line($lead->message);
        }

        if (filled($lead->payload)) {
            $message->line('---')->line('**Scoping answers**');

            foreach ($lead->payload as $key => $value) {
                $message->line('**'.str($key)->headline().':** '.(is_array($value) ? implode(', ', $value) : $value));
            }
        }

        return $message->action('Open in admin', url("/admin/leads/{$lead->id}/edit"));
    }
}
