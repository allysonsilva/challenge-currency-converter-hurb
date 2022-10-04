<?php

namespace App\API\Currency\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use CurrencyDomain\DTO\ConvertedCurrencyResultDTO;

class ConvertedCurrency extends Mailable implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    /**
     * The DTO instance.
     */
    public ConvertedCurrencyResultDTO $details;

    /**
     * Create a new message instance.
     *
     * @param \CurrencyDomain\DTO\ConvertedCurrencyResultDTO $details
     *
     * @return void
     */
    public function __construct(ConvertedCurrencyResultDTO $details)
    {
        $this->details = $details;

        $this->onQueue('emails');
        $this->subject('Valor convertido de ' . $details->fromSymbol() . ' => ' . $details->toSymbol());
    }

    /**
     * Build the message.
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function build()
    {
        return $this->markdown('currency-mail::conversion-details');
    }
}
