<?php

namespace App\Console\Commands;

use App\Models\User;
use Auth;
use Illuminate\Console\Command;

class CheckUsersPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check-pw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the password of a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $usersEmails = User::all()
            ->map(fn(User $user) => $user->email);

        $email = null;

        while (!User::where('email', '=', $email)->exists()) {
            $email = $this->anticipate('Enter the email of the user you want to edit.', $usersEmails->all());
        }

        $user = User::where('email', '=', $email)->firstOrFail();

        $password = $this->ask('Which password should be checked for ' . $user->email . '?');

        $valid = Auth::attempt([
            'email' => $user->email,
            'password' => $password
        ]);

        if ($valid) {
            $this->info('The password is valid for ' . $user->email);
        } else {
            $this->error('The password is invalid for ' . $user->email);
        }
    }
}
