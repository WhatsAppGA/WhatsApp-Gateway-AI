<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $user_with_active_subscription = User::whereActiveSubscription('active')->where('subscription_expired', '<', date('Y-m-d'))->get();
        Log::info('Checking subscription');
        foreach ($user_with_active_subscription as $user) {
           $numbers = Device::whereUserId($user->id)->whereStatus('Connected')->get();
              foreach ($numbers as $number) {
              // delete folder in credentials
              $path = base_path('credentials/'.$number->body);
              if(file_exists($path)){
               // delete all file from path
                $files = glob($path.'/*'); // get all file names
                foreach($files as $file){ // iterate files
                  if(is_file($file))
                    unlink($file); // delete file
                }
                rmdir($path); // delete folder
              }
              $number->status = 'Disconnected';
                $number->save();
               
               
              }
                $user->active_subscription = 'inactive';
                $user->subscription_expired = null;
                $user->save();
        }
    }
}
