<?php

namespace App\Jobs;

use App\Enums\Language;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $subject;
    protected $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->data) {
            $this->subject = 'Please complete your registration';
            $this->time = getEnglishDatetime();
            $this->sendEmail('emails.register');
        } else {
            Log::warning('Register job:' . $this->data['email']);
        }
    }

    public function sendEmail($template)
    {
        Mail::send($template, [
            'token' => $this->data['token'],
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'timeNow' => $this->time,
        ], function ($message) {
            $message->to($this->data['email']);
            $message->subject($this->subject);
        });

        Log::info('Send email to ' . $this->data['email'] . ' was successful, please check your email.');
    }
}
