@component('mail::message')
# New Viewer Feedback

**Name:** {{ $data['name'] }}  
**Email:** {{ $data['email'] }}  
**Subject:** {{ $data['subject'] }}

**Message:**  
{{ $data['message'] }}

@component('mail::button', ['url' => config('app.url')])
View CAVRAA Monitoring System
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
