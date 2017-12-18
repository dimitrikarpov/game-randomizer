<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function canBrowseDashboard()
    {
        $this->get('/')->assertStatus(200);
        $this->get('/home')->assertStatus(200);
    }

    /**
     * @test
     */
    public function canSeeLatestApprovedGameList()
    {
        $this->actingAs(factory('App\User')->create());

        factory('App\Platform')->create();
        $approvedGame = factory('App\Game')->create();
        $unapprovedGame = factory('App\Game')->states('unapproved')->create();

        $this->get('/')
            ->assertSee($approvedGame->name)
            ->assertDontSee($unapprovedGame->name);

        $this->get('/home')
            ->assertSee($approvedGame->name)
            ->assertDontSee($unapprovedGame->name);
    }
}
