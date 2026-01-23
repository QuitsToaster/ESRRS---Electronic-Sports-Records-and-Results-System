@component('mail::message')
# {{ $data['subject'] }}

**Name:** {{ $data['name'] }}  
**Email:** {{ $data['email'] }}  

**Message:**  
{{ $data['message'] }}

@component('mail::button', ['url' => config('app.url')])
View CAVRAA Monitoring System
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
