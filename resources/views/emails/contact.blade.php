<x-mail::message>
# New Contact Message

**Name**: {{ $data['name'] }}
**Email**: {{ $data['email'] }}

**Message**:
{{ $data['message'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
