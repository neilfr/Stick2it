<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

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
        $name = $this->ask('Name?');
        $email = $this->ask('Email?');
        $password = $this->ask('Password?');

        if ($this->confirm("Name: {$name}, Email: {$email}, Password: {$password}. Correct?")) {
            $user = User::factory()->create([
                'name' => $name,
                'password' => Hash::make($password),
                'email' => $email,
            ]);
            $this->info('User creation successful');
        } else {
            $this->info('User creation cancelled');
        }
    }
}
