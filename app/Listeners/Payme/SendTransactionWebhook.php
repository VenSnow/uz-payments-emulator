<?php

namespace App\Listeners\Payme;

use App\Events\Payme\TransactionUpdated;
use App\Jobs\SendWebhookNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionWebhook implements ShouldQueue
{
    /**
     * Handle the TransactionUpdated event.
     *
     * @param TransactionUpdated $event
     * @return void
     */
    public function handle(TransactionUpdated $event): void
    {
        if ($event->notify) {
            dispatch(new SendWebhookNotification($event->params, $event->transaction, true));
        }
    }
}
