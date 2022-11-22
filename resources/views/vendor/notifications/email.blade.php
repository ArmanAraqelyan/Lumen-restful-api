<div style="padding: 20px">
    @if (! empty($greeting))
        <h3>{{ $greeting }}</h3>
    @endif

    @foreach ($introLines as $line)
        <p>{!! $line !!} </p>
    @endforeach

    @isset($token)
        <p>Token : <b>{{ $token }}</b></p>
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
        <p>{!! $line !!}</p>
    @endforeach

    {{-- Salutation --}}
    @if (! empty($salutation))
        <p>{{ $salutation }}</p>
    @else
        @lang('Regards'),<br>
        <p>{{ config('app.name') }}</p>
    @endif
</div>