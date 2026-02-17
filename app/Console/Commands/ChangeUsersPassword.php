<?php

namespace App\Console\Commands;

use App\Models\User;
use Hash;
use Illuminate\Console\Command;

class ChangeUsersPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-pw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the password of a user';

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

        $newPassword = null;
        while (empty($newPassword)) {
            $newPassword = $this->ask('Which new password should be used for ' . $user->email . '?');
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->info('Password updated for ' . $user->email);
    }
}
