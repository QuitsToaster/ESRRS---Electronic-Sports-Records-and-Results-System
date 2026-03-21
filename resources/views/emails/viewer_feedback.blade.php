@component('mail::message')
# {{ $data['subject'] }}

**Name:** {{ $data['name'] }}  
**Email:** {{ $data['email'] }}  

**Message:**  
{{ $data['message'] }}

@component('mail::button', ['url' => config('app.url')])
View eSRRS
@endcomponent

Thanks,<br>
{{ config('app.name') }} Support Team
@endcomponent
