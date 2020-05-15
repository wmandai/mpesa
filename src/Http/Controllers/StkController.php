<?php

namespace Wmandai\Mpesa\Http\Controllers;

use Illuminate\Http\Request;
use Wmandai\Mpesa\Facades\STK;
use Wmandai\Mpesa\Http\Requests\StkRequest;

class StkController extends Controller
{
    public function initiatePush(StkRequest $request)
    {
        try {
            $stk = STK::request($request->amount)
                ->from($request->phone)
                ->usingReference($request->reference, $request->description)
                ->push();
        } catch (\Exception $exception) {
            $stk = [
                'ResponseCode' => 900,
                'ResponseDescription' => 'Invalid request',
                'extra' => $exception->getMessage(),
            ];
        }
        return response()->json($stk);
    }
    /**
     * @param  $reference
     * @return \Illuminate\Http\JsonResponse
     */
    public function stkStatus($reference)
    {
        return response()->json(STK::validate($reference));
    }
}
