<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class NotifyTestController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        Log::channel('webhook')->info('Received webhook', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        return response()->json(['status' => 'received']);
    }
}

