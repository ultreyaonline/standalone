<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // first create a few generic users, which are separately assigned as rectors to demo weekends
        $m = factory(\App\Models\User::class)->states('male')->create();
        $m->assignRole('Member');
        $m = factory(\App\Models\User::class)->states('male')->create();
        $m->assignRole('Member');
        $w = factory(\App\Models\User::class)->states('female')->create();
        $w->assignRole('Member');
        $w = factory(\App\Models\User::class)->states('female')->create();
        $w->assignRole('Member');

        // now create some admin/dev dummy users
        $m = factory(\App\Models\User::class)->states('male')->create([
            'email'=>'john@example.com',
            'username'=>'john@example.com',
            'first' => 'John',
            'last' => 'Smith',
            'sponsorID' => $this->getRandomSponsorID(),
        ]);
        $m->assignRole('Member');
        $m->assignRole('Admin');
        $m->assignRole('Super-Admin');

        $u = factory(\App\Models\User::class)->states('male')->create([
            'email'=>'james@example.com',
            'username'=>'james@example.com',
            'first' => 'James',
            'last'=> 'Smith',
            'sponsorID' => $m->id,
        ]);
        $u->assignRole('Member');
        $u->assignRole('Admin');

        // create a wife record from shared properties of the man's record
        $clone = \App\Models\User::find($m->id);
        $w = factory(\App\Models\User::class)->states('female')->create([
            'email'=>'jane@example.com',
            'username'=>'jane@example.com',
            'gender' => 'F',
            'first' => 'Jane',
            'last' => 'Smith',
            'spouseID' => $m->id,
            'church'     => $m->church,
            'weekend'    => $m->weekend,
            'sponsorID'  => $m->sponsorID,
            'address1'   => $m->address1,
            'address2'   => $m->address2,
            'city'       => $m->city,
            'state'      => $m->state,
            'postalcode' => $m->postalcode,
            'homephone'  => $m->homephone,
        ]);
        $w->assignRole('Member');
        $w->assignRole('Admin');
        $w->assignRole('Super-Admin');
        // link spouse
        $m->spouseID = $w->id;
        $m->save();

        // build 10 random couples
        for ($i=0; $i < 10; $i++) {
            $sponsorID = $this->getRandomSponsorID();

            // build a couple
            $userM           = factory(\App\Models\User::class)->states('male')->create([
                'sponsorID' => $sponsorID,
            ]);
            $userF           = factory(\App\Models\User::class)->states('female')->create([
                'spouseID'   => $userM->id,
                'church'     => $userM->church,
                'weekend'    => $userM->weekend,
                'sponsorID'  => $sponsorID,
                'address1'   => $userM->address1,
                'address2'   => $userM->address2,
                'city'       => $userM->city,
                'state'      => $userM->state,
                'postalcode' => $userM->postalcode,
                'homephone'  => $userM->homephone,
            ]);
            $userM->spouseID = $userF->id;
            $userM->save();

            $userM->assignRole('Member');
            $userF->assignRole('Member');
        }

        // a few more non-couples
        factory(\App\Models\User::class, 3)->states('generic')->create(['sponsorID' => $this->getRandomSponsorID(), 'active'=>false]);
        factory(\App\Models\User::class, 3)->states('generic')->create(['sponsorID' => $this->getRandomSponsorID()]);

        // Make some couples be from another Community
        $sponsorID1 = \App\Models\User::select('sponsorID')->where('sponsorID', '>', 1)->get()->random()['sponsorID'];
        $sponsorID2 = \App\Models\User::select('sponsorID')->where('sponsorID', '>', 1)->get()->random()['sponsorID'];
        \App\Models\User::whereIn('sponsorID', [$sponsorID1, $sponsorID2])
            ->get()
            ->each(function($user) {
                $user->community='HOLA';
                $user->weekend = str_replace(config('site.community_acronym'), 'HOLA', $user->weekend);
                $user->save();
            });


    }

    private function getRandomSponsorID()
    {
        $sponsorID = 0;
        // pick a random existing user to be the sponsor
        $collection = \App\Models\User::select('id')->where('active', 1)->take(20)->get();
        if ($collection) {
            $sponsorID = $collection->random()['id'];
        }
        return $sponsorID;
    }
}
