<?php

namespace App\Jobs;

use App\Enums\Language;
use App\Enums\UserRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailForgotPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $token;
    protected $name;
    protected $languageId;
    protected $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $token, $name)
    {
        $this->email = $email;
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->subject = 'パスワード再設定';
        $sent = $this->sendEmail('emails.forgot_password');
        if ($sent) {
            Log::warning('Send forgot password job Successful');
        } else {
            Log::warning('Send forgot password job failed');
        }
    }

    public function sendEmail($template)
    {
        Mail::send(
            $template,
            [
                'token' => $this->token,
                'email' => $this->email,
                'name' => $this->name,
                'role' => UserRole::USER,
            ],
            function ($message) {
                $message->to($this->email);
                $message->subject($this->subject);
            }
        );

        Log::info('Send email to ' . $this->email . ' was successful, please check your email.');
    }
}
