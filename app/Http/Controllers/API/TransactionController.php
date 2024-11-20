<?php

namespace App\Http\Controllers\API;

use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Store;
use Illuminate\Http\Exceptions\HttpResponseException;


class TransactionController extends Controller
{
    public function index()
    {
        $transaction = Transaction::with('listing')->whereUserId(auth()->id())->paginate();
        return response()->json(
            [
                'success' => true,
                'message' => 'Get All Transactions',
                'data' => $transaction
            ]
        );
    }
    private function _fullyBookedChecker(Store $request)
    {
        $listing = Listing::find($request->listing_id);
        $runningtransactionCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'cenceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date,
                ])->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date,
                ])->orWhere(function ($subquery) use ($request) {
                    $subquery->where('start_date', '<', $request->start_date)
                        ->where('end_date', '>', $request->end_date);
                });
            })->count();

        if ($runningtransactionCount >= $listing->max_person) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Listing Is Fully Booking',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        return true;
    }
    public function isAvalilable(Store $request)
    {
        $this->_fullyBookedChecker($request);

        return response()->json(
            [
                'success' => true,
                'message' => 'Listing Is ready to book'
            ]
        );
    }

    public function store(Store $request)
    {
        $this->_fullyBookedChecker($request);

        $transaction = Transaction::create([
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'listing_id'=>$request->listing_id,
            'user_id'=>auth()->id()
        ]);
        $transaction->Listing;
        return response()->json([
            'success' => true,
            'message' => 'New Transaction Succes Created',
            'data'=>$transaction
        ]);

    }
    public function show(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $transaction->Listing;
        return response()->json([
            'success' => true,
            'message' => 'Get Transaction Details',
            'data' => $transaction
        ]);
    }
}