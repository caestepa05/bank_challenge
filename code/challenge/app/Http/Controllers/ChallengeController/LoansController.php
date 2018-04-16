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
     * @SWG\Post(path="/{model}/archive",
     *   tags={"CommonActions"},
     *   summary="Archive objects",
     *   description="Archive one or more objects",
     *   operationId="",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="model",
     *     type="string",
     *     description="Entity name to archived (users, venues, etc.)",
     *     required=true,
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="JSON Object. Structure",
     *     required=true,
     *     @SWG\Schema (
     *              @SWG\Property(
     *                            property="objects",
     *                            type="array",
     *                            @SWG\Items(
     *                                type="object",
     *                                @SWG\Property(property="id", type="number",default=1),
     *                            ),
     *              ),
     *      ),
     *   ),
     *   @SWG\Response(response=200, description="successfull operation")),
     *   @SWG\Response(response=500, description="server error"),
     *   @SWG\Response(response=422, description="validation failed")
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