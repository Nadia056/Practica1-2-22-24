<?php

namespace App\Jobs;

use App\Mail\SendMailActivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendActivationURL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $url;
    protected $user;
    protected $random;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $user, $random)
    {
        //
        $this->url = $url;
        $this->user = $user;
        $this->random = $random;
    }
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        Mail::to($this->user->email)->send(new SendMailActivation($this->url,$this->user,$this->random));
        
    }
}
