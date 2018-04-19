<?php

namespace App\Services;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ChallengeException;
use App\Models\Loans;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;

use Log;

/**
 * @author Andres Estepa <caestepa05@gmail.com> 
 * @package App
 * @subpackage Services
 */
class LoansService
{

   

    public function processLoan($data)
    {
        try{
            $validator = Validator::make($data, Loans::$createLoans);
            if ($validator->fails()) {
                throw new ChallengeException('Validation fails', 422, $validator->errors());
            }

            $data['user_id'] = 1;

            $newLoan = Loans::create($data);
            

            $response = [
                'loan_id' => $newLoan->id,
                'installment' => $newLoan->installment()
            ];

            return $response;
            

        }catch(\Exception $e){
            throw $e;
        }
    }

    public function getLoans($from= null, $to = null)
    {
        try{

            $filter = Loans::whereBetween('date', [$from, $to])->get(['id', 'amount', 'term', 'rate', 'date']);

            return $filter;
            
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function createPayment($data, $loan_id)
    {
        try{
            $validator = Validator::make($data, Payments::$newPayment);
            if ($validator->fails()) {
                throw new ChallengeException('Validation fails', 422, $validator->errors());
            }

            $loan = Loans::find($loan_id);

            if(!$loan) {
                throw new ChallengeException('Loan not found', 422, ['The loan id :' . $loan_id . ' was not found']);
            }
            
            //Validate payment date
            $paymentDate = date_create_from_format('Y-m-d H:i\Z', $data['date']);
            
            if($paymentDate <= $loan->getRawDate()) {
                throw new ChallengeException('The date is invalid', 422, ['The date must be after the date ' . $loan->date]);
            }
            
            //Validate payment balance
            if($loan->getCurrentBalance() <= 0) {
                throw new ChallengeException('The loan already was canceled.', 422, []);
            }

            if($loan->thereIsPaymentForDate($paymentDate)) {
                throw new ChallengeException('A payment has already been registered for the date.', 422, []);
            }
          
            //Register payment
            $data['loan_id'] = $loan_id;
            $newPayment = Payments::create($data);
                        

            $response = [
                'message' => 'Payment registered successfully',
            ];

            return $response;

        }catch(\Exception $e){
            throw $e;
        }
    }

    public function getLoansBalance($loan_id, $date = null)
    {
        $loan = Loans::find($loan_id);
        
        if(!$loan) {
            throw new ChallengeException('Loan not found', 422, ['The loan id :' . $loan_id . ' was not found']);
        }

        if(!$date) {
            $balance = $loan->getCurrentBalance();
        }else{
            $balance = $loan->getBalanceUntilDate($date);
        }

        return ['balance' => $balance];

    }
}