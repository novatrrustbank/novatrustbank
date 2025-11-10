<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Change email and password to what you want
        $email = 'collaomn@gmail.com';
        $password = 'freeon080';

        $user = User::where('email', $email)->first();
        if (!$user) {
            User::create([
                'name' => 'Owner',
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => 1,
                'role' => 'admin',
                'balance' => 0,
                'account_number' => '2031060001',
            ]);
            $this->command->info("Admin user created: {$email} / {$password}");
        } else {
            $this->command->info("Admin already exists: {$email}");
        }
    }
}
