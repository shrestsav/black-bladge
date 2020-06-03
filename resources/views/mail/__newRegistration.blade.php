@component('mail::message')
Dear {{$mailData['name']}},

Welcome to Black Badge !
<br>
Start your first booking and enjoy the majestic ride of Rolls Royce. Experince the luxury at lowest cost possible. 
<br>
Click here to make your first booking !
<br>
<br>


Regards,<br>
{{ config('app.name') }}
@endcomponent