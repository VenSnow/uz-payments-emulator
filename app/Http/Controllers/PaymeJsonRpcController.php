<?php

namespace App\Http\Controllers;

use App\Services\Payme\PaymeRpcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymeJsonRpcController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $payload = $request->all();

        $id     = $payload['id'] ?? null;
        $method = $payload['method'] ?? null;
        $params = $payload['params'] ?? [];

        try {
            $result = app(PaymeRpcService::class)->handle($method, $params);

            return response()->json([
                'jsonrpc' => '2.0',
                'id'      => $id,
                'result'  => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id'      => $id,
                'error'   => [
                    'code'    => -31008,
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

