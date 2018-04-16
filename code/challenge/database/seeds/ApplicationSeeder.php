<?php

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\User;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::beginTransaction();
        try {
            $clients = new ClientRepository();
            $client  = $clients->createPasswordGrantClient(
                null,
                'challenge',
                'http://localhost'
            );
            $client->secret = 'secret';
            $client->save();
            DB::commit();
            
            
          
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            DB::rollback();
        }


        $email = Config::get('app.su_email');
        $super = DB::table('users')->where('email', $email)->first();
        
        if ($super) {
            
            return;
        }
 
        $pass       = Config::get('app.su_pass');
        $username   = Config::get('app.su_name');
 
        if (is_null($pass) || is_null($username)) {
            
            return;
        }
 
        DB::beginTransaction();
        try {
            
            $user = User::create(
                [
                'name' => $username,
                'password' => $pass,
                'email' => $email
                ]
            );
 
 
            DB::commit();
            
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            
            DB::rollback();
        }

    }
}
