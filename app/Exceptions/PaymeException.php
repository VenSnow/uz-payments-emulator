<?php

namespace App\Exceptions;

use Exception;

class PaymeException extends Exception
{
    /**
     * @param string $message
     * @param int $paymeCode
     */
    public function __construct(string $message, public readonly int $paymeCode = -31000)
    {
        parent::__construct($message);
    }

    /**
     * Convert exception to Payme error response.
     *
     * @return array<string, mixed>
     */
    public function toResponse(): array
    {
        return [
            'error' => [
                'code'    => $this->paymeCode,
                'message' => [
                    'en' => $this->message,
                    'uz' => $this->translateToUz(),
                    'ua' => $this->translateToUa(),
                    'ru' => $this->translateToRu(),
                ],
                'data'    => strtoupper(str_replace(' ', '_', $this->message)),
            ],
        ];
    }

    /**
     * @return string
     */
    private function translateToUz(): string
    {
        return match ($this->message) {
            'Transaction not found' => 'Tranzaksiya topilmadi',
            'Invalid input parameters' => 'Noto‘g‘ri kiritilgan parametrlar',
            'Transaction already performed and cannot be canceled' => 'Tranzaksiya allaqachon bajarilgan va bekor qilinishi mumkin emas',
            'Method not supported' => 'Usul qo‘llab-quvvatlanmaydi',
            default => $this->message,
        };
    }

    /**
     * @return string
     */
    private function translateToUa(): string
    {
        return match ($this->message) {
            'Transaction not found' => 'Транзакцію не знайдено',
            'Invalid input parameters' => 'Невірні вхідні параметри',
            'Transaction already performed and cannot be canceled' => 'Транзакція вже виконана і не може бути скасована',
            'Method not supported' => 'Метод не підтримується',
            default => $this->message,
        };
    }

    /**
     * @return string
     */
    private function translateToRu(): string
    {
        return match ($this->message) {
            'Transaction not found' => 'Транзакция не найдена',
            'Invalid input parameters' => 'Неверные входные параметры',
            'Transaction already performed and cannot be canceled' => 'Транзакция уже выполнена и не может быть отменена',
            'Method not supported' => 'Метод не поддерживается',
            default => $this->message,
        };
    }
}
