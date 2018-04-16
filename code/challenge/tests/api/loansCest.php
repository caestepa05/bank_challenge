<?php

use Carbon\Carbon;

class loansCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function createLoans(ApiTester $I)
    {

        $data = [
            "amount" => 1000,
            "term" => 12,
            "rate" => 0.05,
            "date" => "2017-08-05 02:18Z",
        ];

        $I->sendPOST('/loans', $data);
        
    }
}
