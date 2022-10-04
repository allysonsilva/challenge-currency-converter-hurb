@component('mail::message')
# Currency conversion information

- **Origin Symbol:** {{ $details->fromSymbol() }}
- **Origin Currency Exchange Rate:** 1 {{ $details->baseCurrency }} = {{ $details->fromExchangeRate() . ' ' . $details->fromSymbol() }}
- **Target Symbol:** {{ $details->toSymbol() }}
- **Target Currency Exchange Rate:** 1 {{ $details->baseCurrency }} = {{ $details->toExchangeRate() . ' ' . $details->toSymbol() }}
- **Value to be converted:** {{ $details->getValueToBeConverted() }}
- **Converted value:** {{ $details->getValueOfConverted() }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
