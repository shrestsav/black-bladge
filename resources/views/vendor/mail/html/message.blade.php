@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{-- {{ config('app.name') }}  --}}
            <img height="70px" src="{{asset('system/img/company-logo.png')}}">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <a href="https://play.google.com/store/apps/details?id=com.blackbadge"><img height="45px" src="{{asset('system/img/appstore.png')}}"></a>
            <a href="https://play.google.com/store/apps/details?id=com.blackbadge"><img height="45px" src="{{asset('system/img/googlestore.png')}}"></a>
            <br><br>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
