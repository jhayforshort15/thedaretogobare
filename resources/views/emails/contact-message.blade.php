<x-mail::message>
# New Contact Message

**From:** {{ $senderName }} ({{ $senderEmail }})

{{ $messageBody }}

<x-mail::button :url="'mailto:' . $senderEmail">
Reply to {{ $senderName }}
</x-mail::button>

**{{ config('app.name') }}** — contact form
</x-mail::message>
