<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function handle(Request $request)
    {
        return view('callback', [
            'data' => $request->all(),
        ]);
    }
}
