@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

<p>
    The user: {{ $group->owner->name }} has invited you to their group: {{ $group->name }}. Log in below to accept or reject their group invitation.
</p>

@component('mail::button', ['url' => env("APP_URL")])
    Login to Joystick Jury
@endcomponent

Thanks for using Joystick Jury!<br>
{{ config('app.name') }}
@endcomponent