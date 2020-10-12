<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConfigSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = \App\Models\User::factory()->active()
            ->create(['first' => 'admin', 'last' => 'user'])
            ->assignRole('Admin');
    }

    /** @test */
    public function feature_flag_config_settings_can_be_displayed_in_admin()
    {
        $response = $this->signIn($this->admin)
            ->get(route('admin-settings-edit'));

        $response->assertSee('Site Settings');
        $response->assertSee('Community Acronym');
        $response->assertSee(config('site.retreat_name_for_email_subject', 'FAILED'));
    }

    /** @test */
    public function an_edited_feature_flag_setting_is_persisted()
    {
        $settings = app()->get('config');
        $settings->save([
            // these must not be blank, so we set them to sane values before testing
            'email_general' => 'foo@example.com',
            'community_mailing_address' => 'demo address',
            'emergency_contact_text' => 'emergency contact',
            'emergency_contact_number' => 'number here',
            'retreat_name_for_email_subject' => 'Tres Dias Retreat', // this is the default value, repeated here just for clarity
        ]);

        $this->attributes = [];

        // getItems() and getValue() come from the Illuminatech/Config package
        $settings->getItems()->each(function ($setting) {
            if (!isset($this->attributes[$setting->id])) {
                $this->attributes[$setting->id] = $setting->getValue();
            }
        });

        // Set the value to be tested
        $new_value_to_test_for = 'changed for testing';
        $this->attributes['retreat_name_for_email_subject'] = $new_value_to_test_for;

        $response = $this->signIn($this->admin)
            ->patch(route('admin-settings-update', $this->attributes));

        $response->assertRedirect(route('admin'));
        $response->assertSessionHasNoErrors();

        $response = $this->signIn($this->admin)
            ->get(route('admin-settings-edit'));

        $response->assertSee('Site Settings');
        $response->assertSee('Community Acronym');
        $response->assertSee(config('site.retreat_name_for_email_subject', 'FAILED'));
        $response->assertSee($new_value_to_test_for);

        // ensure the value was changed in db
        $this->assertDatabaseHas('settings', [
            'key' => 'site.retreat_name_for_email_subject',
            'value' => $new_value_to_test_for,
            ]);

        // @TODO - test that the change was recorded in the ActivityLog table
    }

}
