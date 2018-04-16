<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Config;
use App\User;

use Log;

class Challenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user for testing porpuse';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createFrontendAdminKey();
        
        $this->createSuperUserAdminKey();
   }

   public function createFrontendAdminKey()
   {

       $username      = 'challenge';
       $oauth_clients = DB::table('oauth_clients')->where('name', '=', $username)->get(['*']);
       
       if (count($oauth_clients) > 0) {
           $this->info('The frontend app already is created');
           return;
       }

       $redirect = Config::get('app.url');
      
       if (is_null($redirect)) {
           $this->info('The url is not set');
           return;
       }

       DB::beginTransaction();
       try {
           $clients = new ClientRepository();
           $client  = $clients->createPasswordGrantClient(
               1,
               $username,
               $redirect
           );

           DB::commit();
           
           $this->info('Frontend secret: ' . $client->secret);
       } catch (\Exception $e) {
           Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
           $this->info('Error' . $e->getMessage());
           DB::rollback();
       }
   }

   public function createSuperUserAdminKey()
   {
       $email = Config::get('app.su_email');
       $super = DB::table('users')->where('email', $email)->first();
       
       if ($super) {
           $this->info('The super user already exists');
           return;
       }

       $pass       = Config::get('app.su_pass');
       $username   = Config::get('app.su_name');

       if (is_null($pass) || is_null($username)) {
           $this->info('Name and password are not set');
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
           $this->info('Super User created');
       } catch (\Exception $e) {
           Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
           $this->info('Error' . $e->getMessage());
           DB::rollback();
       }
   }
    
}
