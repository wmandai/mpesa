<?php

namespace Wmandai\Mpesa\Http\Controllers;

use Illuminate\Http\Request;
use Wmandai\Mpesa\Daraja;
use Wmandai\Mpesa\Http\Requests\StkRequest;

class StkController extends Controller
{
    public $request;
    public $daraja;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->daraja = new Daraja();
    }

    /**
     * Receives a request from a form and sends STK to specified number
     */
    public function push(StkRequest $request)
    {
        try {
            $stk = $this->daraja->stkPush(
                $request->phone,
                $request->amount,
                $request->reference,
                $request->type,
                $request->description
            );
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
     * @param $reference
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($reference)
    {
        return response()->json($this->daraja->stkQuery($reference));
    }
}
