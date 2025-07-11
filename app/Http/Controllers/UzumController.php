<?php

namespace App\Http\Controllers;

use App\Exceptions\UzumException;
use App\Services\Uzum\UzumService;
use Illuminate\Http\Request;
use Throwable;

class UzumController extends Controller
{
    public function __construct(protected UzumService $service)
    {
    }

    public function handle(Request $request, string $method)
    {
        $payload = $request->all();

        try {
            $result = $this->service->handle($method, $payload);

            return response()->json(
                $result
            );
        } catch (UzumException $e) {
            return response()->json($e->toResponse(), 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => '10000',
                    'message' => [
                        'uz' => 'Server xatolikka uchradi',
                        'en' => 'Internal server error',
                        'uk' => 'Внутрішня помилка сервера',
                        'ru' => 'Внутренняя ошибка сервера',
                    ],
                    'data' => $e->getMessage(),
                ],
            ], 200);
        }
    }
}
