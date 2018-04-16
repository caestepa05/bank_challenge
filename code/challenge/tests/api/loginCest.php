<?php


class loginCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function loginAsAdmin(ApiTester $I)
    {
        $data = [
            'username' => 'andres@challenge.com',
            'password' => '123123',
        ];

        $I->loginAuth2($data);
        
    }
}
