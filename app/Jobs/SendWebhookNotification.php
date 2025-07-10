<?php

namespace App\Jobs;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\WebhookLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendWebhookNotification implements ShouldQueue
{
    use Queueable;
    protected array $data;
    protected Transaction $transaction;
    protected bool $debug;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, Transaction $transaction, bool $debug = false)
    {
        $this->data = $data;
        $this->transaction = $transaction;
        $this->debug = $debug;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $errorMessage = 'Webhook error: ';
        $notifyUrl = config('app.debug_notify_url');

        $payload = [
            'transaction_id' => $this->transaction->transaction_id,
            'order_id'       => $this->transaction->order_id,
            'amount'         => $this->transaction->amount,
            'status'         => $this->transaction->status->value,
            'message'        => $this->getMessage(),
        ];

        try {
            $response = Http::post($notifyUrl, $payload);

            WebhookLog::create([
                'transaction_id'   => $this->transaction->id,
                'provider'         => $this->transaction->provider->value,
                'url'              => $notifyUrl,
                'payload'          => $payload,
                'is_debug'         => str_contains($payload['message'], '[DEBUG]'),
                'response_status'  => $response->status(),
                'response_body'    => $response->body(),
            ]);

        } catch (\Throwable $e) {
            logger()->error($errorMessage . $e->getMessage());

            WebhookLog::create([
                'transaction_id'   => $this->transaction->id,
                'provider'         => $this->transaction->provider->value,
                'url'              => $notifyUrl,
                'payload'          => $payload,
                'is_debug'         => str_contains($payload['message'], '[DEBUG]'),
                'error_message'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return string
     */
    private function getMessage(): string
    {
        $prefix = $this->debug ? '[DEBUG]: ' : '';
        return match ($this->transaction->status) {
            TransactionStatus::SUCCESS => $prefix . 'Transaction completed successfully',
            TransactionStatus::FAILED  => $prefix . 'Transaction failed',
            TransactionStatus::PENDING => $prefix . 'Transaction is pending',
            default                    => $prefix . 'Unknown status',
        };
    }
}
