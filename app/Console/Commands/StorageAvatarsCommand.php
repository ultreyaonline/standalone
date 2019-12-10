<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageAvatarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:avatars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "public/avatars" to "storage/app/avatars"';

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
     * @return mixed
     */
    public function handle()
    {
        if (file_exists(public_path('avatars'))) {
            return $this->error('The "public/avatars" directory already exists.');
        }

        $this->laravel->make('files')->link(storage_path('app/avatars'), public_path('avatars'));

        $this->info('The [public/avatars] directory has been linked.');
    }
}
