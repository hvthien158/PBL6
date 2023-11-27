<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendUserAccount;
use Mail;
class MailInfomationAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $account;
    /**
     * @param mixed $account
     */
    public function __construct($account)
    {
        // $this->onQueue('emails');
        $this->account = $account;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->account['email'])->send(new SendUserAccount($this->account));
    }
}
