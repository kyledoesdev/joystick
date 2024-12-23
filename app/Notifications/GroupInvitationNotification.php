<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(private Group $group) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('emails.group_invitation', [
            'notifiable' => $notifiable,
            'group' => $this->group
        ])->subject('Joystick Jury - New Group Invitation');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
