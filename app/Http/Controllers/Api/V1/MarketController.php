<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $markets = Cache::remember('markets', now()->addMinutes(30), function () {
            return Market::query()
                ->get(['id as value', 'name as label']);
        });

        return response()->json($markets);
    }
}
