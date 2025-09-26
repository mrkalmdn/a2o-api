<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $markets = Cache::remember("markets.{$user->getKey()}", now()->addMinutes(30), function () use ($user) {
            return Market::query()
                ->when($user->hasRole('regular'), fn ($query) => $query->whereIn('id', $user->markets()->pluck('market_id')))
                ->get(['id as value', 'name as label']);
        });

        return response()->json($markets);
    }
}
