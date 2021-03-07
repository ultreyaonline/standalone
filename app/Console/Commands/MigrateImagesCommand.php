<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Weekend;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl;

class MigrateImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'one-time migration of local banner/team/profile photos to medialibrary';

    /**
     * Load all images from local storage into media library
     */
    public function handle()
    {
            $this->processWeekendBanners();
            $this->processWeekendTeamPhotos();
            $this->processUserAvatars();
    }

    protected function processWeekendBanners()
    {
        $weekends = Weekend::all();
//        $weekends = Weekend::where('id', '<', 10)->get();
        foreach ($weekends as $weekend) {
            echo 'Banners: ' . $weekend->weekend_full_name . ' (' . $weekend->id . ') ' . "\n";

            // skip if this one is already in the media library
            if ($weekend->getMedia('banner')->count() > 0) {
                continue;
            }

            $local_file = $weekend->getAttributes()['banner_url'];
            if (empty($local_file)) continue;

            $path = storage_path('app/' . $local_file);

            try {
            $img = $weekend
                ->addMedia($path)
                ->toMediaCollection('banner');
            } catch(\Exception $e) {
                echo ' Not found: ' . $path . "\n";
            }
        }
        echo "\n";
    }

    protected function processWeekendTeamPhotos()
    {
        $weekends = Weekend::all();
//        $weekends = Weekend::where('id', '<', 10)->get();
        foreach ($weekends as $weekend) {
            echo 'Team Photos: ' . $weekend->weekend_full_name . ' (' . $weekend->id . ') ' . "\n";

            // skip if this one is already in the media library
            if ($weekend->getMedia('teamphoto')->count() > 0) {
                continue;
            }

            $local_file = $weekend->getAttributes()['teamphoto'];
            if (empty($local_file)) continue;

            $path = storage_path('app/' . $local_file);

            try {
            $img = $weekend
                ->addMedia($path)
                ->toMediaCollection('teamphoto');
            } catch(\Exception $e) {
                echo ' Not found: ' . $path . "\n";
            }
        }
        echo "\n";
    }

    protected function processUserAvatars()
    {
        $users = User::all();
//        $users = User::where('id', '<', 10)->get();
        foreach ($users as $user) {
            echo 'Member Avatars: ' . $user->full_name . ' (' . $user->id . ') ' . "\n";

            // skip if this one is already in the media library
            if ($user->getMedia('avatar')->count() > 0) {
                continue;
            }

            $local_file = $user->getAttributes()['avatar'];
            if (empty($local_file)) continue;

            $path = storage_path('app/' . $local_file);

            try {
            $img = $user
                ->addMedia($path)
                ->toMediaCollection('avatar');
            } catch(\Exception $e) {
                echo ' Not found: ' . $path . "\n";
            }
        }
        echo "\n";
    }
}
