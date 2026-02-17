<?php

namespace Database\Seeders;

use App\Models\User;
use App\Utils\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Str;
use function array_merge;
use function collect;
use function config;
use function explode;
use function trim;

class CreateAdminUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $novaAdmins = config('nova.admin_users');
        $telescopeAdmins = config('telescope.admin_users', []);
        $horizonAdmins = config('horizon.admin_users', []);

        $adminEmails = collect(array_merge($novaAdmins, $telescopeAdmins, $horizonAdmins))
            ->map(fn($email) => trim($email))
            ->filter(fn($email) => !empty($email))
            ->unique();

        foreach ($adminEmails as $adminEmail) {
            $user = User::where('email', '=', $adminEmail)->first();
            if ($user != null) continue;

            User::create([
                'email' => $adminEmail,
                'name' => explode('@', $adminEmail)[0],
                'password' => Hash::make(Str::random(45)),
                'email_verified_at' => Carbon::now(),
                'role' => UserRole::ADMIN,
            ]);
        }
    }
}
