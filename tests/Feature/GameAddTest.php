<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GameAddTest extends TestCase
{
    use DatabaseMigrations;

    protected $platform;
    protected $suggester;
    protected $user;
    protected $game;

    public function setUp()
    {
        parent::setUp();

        $role = factory('App\Role')->states('game-management')->create();

        $this->suggester = factory('App\Suggester')->create();
        $this->platform = factory('App\Platform')->create();
        $this->user = factory('App\User')->create();
        $this->user->roles()->attach($role);
        $this->game = factory('App\Game')->create();

        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function canSeeUnapprovedGameInSuggestedGameList()
    {
        $game = factory('App\Game')->states('unapproved')->create();

        $this->get('/games/suggested')->assertSee($game->name);
    }

    /**
     * @test
     */
    public function whenGuestCreateGameItIsNotApproved()
    {
        auth()->logout();

        $this->post('games', [
            'name' => 'foo',
            'platform_id' => $this->platform->id
        ]);

        $this->assertDatabaseHas(
            'games',
            [
                'name' => 'foo',
                'platform_id' => $this->platform->id,
                'user_id' => null
            ]
        );
    }

    /**
     * @test
     */
    public function whenUserCreateGameItIsApproved()
    {
        $this->withoutExceptionHandling();

        $this->post('/games', [
            'name' => 'foo',
            'platform_id' => $this->platform->id,
            'suggester_id' => null,
        ]);

        $this->assertDatabaseHas(
            'games',
            [
                'name' => 'foo',
                'platform_id' => $this->platform->id,
                'user_id' => $this->user->id,
                'suggester_id' => null,
            ]
        );
    }

    /**
     * @test
     */
    public function whenUserApproveGameItHasUserId()
    {
        $game = factory('App\Game')->states('unapproved')->create();

        $this->get("/games/{$game->id}/approve");

        $this->assertDatabaseHas(
            'games',
            [
                'name' => $game->name,
                'platform_id' => $this->platform->id,
                'user_id' => $this->user->id
            ]
        );
    }

    /**
     * test
     */
    // public function whenUserAddGameItHasSuggestedByAsUserName()
    // {
    //     $this->post(
    //         '/games',
    //         [
    //             'name' => 'foo',
    //             'platform_id' => $this->platform->id,
    //             'user_id' => null,
    //             'suggester_id' => null
    //         ]
    //     );
    //
    //     $this->assertDatabaseHas(
    //         'games',
    //         [
    //             'name' => 'foo',
    //             'platform_id' => $this->platform->id,
    //             'user_id' => $this->user->id,
    //             'suggested' => $this->user->name
    //         ]
    //     );
    // }

    /**
     * @test
     */
    public function guestCanBrowseAddGameForm()
    {
        $this->get('/games/create')->assertStatus(200);
    }

    /**
     * @test
     */
    public function guestCanStoreGame()
    {
        $platform = factory('App\Platform')->create();

        $game = [
            'name' => 'foo',
            'platform_id' => $platform->id
        ];

        $this->post('/games', $game);

        $this->assertDatabaseHas('games', $game);
    }

    /**
     *
     */
    // public function guestAddedGameWithEmptySuggesedFieldHasEmptySuggestedField()
    // {
    //     auth()->logout();
    //
    //     $game = factory('App\Game')->states('unapproved')->create();
    //
    //     $this->post(
    //         '/games',
    //         [
    //             'name' => 'foo',
    //             'platform_id' => $this->platform->id,
    //             'user_id' => null,
    //             'suggested' => null
    //         ]
    //     );
    //
    //     $this->assertDatabaseHas(
    //         'games',
    //         [
    //             'name' => 'foo',
    //             'platform_id' => $this->platform->id,
    //             'suggested' => null
    //         ]
    //     );
    // }
}
