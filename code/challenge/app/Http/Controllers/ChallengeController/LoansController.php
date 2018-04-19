<?php

namespace App\Http\Controllers\ChallengeController;

use App\Http\Controllers\RestFullController\RestFullController;
use App\Services\LoansService;
use Illuminate\Http\Request;

class LoansController extends RestFullController
{

    /**
     *
     * @var LoansService
     */
    private $loansService;
     
    /**
     *
     * @param LoansService $service
     */
    public function __construct(LoansService $service)
    {
        $this->loansService = $service;
    }



 /**
     * @SWG\Swagger(
     *   @SWG\Info(
     *     title="Bank Challenge",
     *     version="1.0.0"
     *   )
     * )
     */

    /**
     * @SWG\Post(path="/api/loans",
     *   tags={"Loans"},
     *   summary="Create a new loan",
     *   description="",
     *   operationId="loans.create",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Data parameter",
     *     required=true,
     *     @SWG\Schema(
     *       @SWG\Property(
     *         property="amount",
     *         type="float",
     *         example=10000
     *       ),
     *     @SWG\Property(
     *         property="term",
     *         type="integer",
     *         example=12
     *       ),
     *     @SWG\Property(
     *         property="rate",
     *         type="float",
     *         example=0.05
     *       ),
     *     @SWG\Property(
     *         property="date",
     *         description="Format required is Y-m-d H:i\Z",
     *         type="string",
     *         example="2016-08-05 02:18Z"
     *       )
     *     )
     *   ),
     *   @SWG\Response(response="default", description="successful")
     * )
     */
    public function createLoan(Request $request)
    {
         
        $data = $this->loansService->processLoan($request->all());

        return $data;
         
    }

    public function getLoans(Request $request)
    {

        $from = null;
        $to = null;
        if ($request->has('from')) {
            $from = $request->input('from');
        }

        if ($request->has('to')) {
            $to = $request->input('to');
        }

        $data = $this->loansService->getLoans($from, $to);

        return $data;
         
    }

    public function createPayment(Request $request, $loan_id)
    {
        $data = $this->loansService->createPayment($request->all(), $loan_id);
        
        return $data;
    }

    public function getBalance(Request $request, $loan_id)
    {
        
        $date = null;
        if ($request->has('date')) {
            $date = $request->input('date');
        }

        $data = $this->loansService->getLoansBalance($loan_id, $date);
        
        return $data;
    }
     
}