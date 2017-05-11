<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInThreadsTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
             ->post('/some-channel/1/replies', [])
             ->assertRedirect('/login');
    }

    /** @test */
    function an_authenticated_user_may_participate_in_forum_threads()
    {
        // be() Sets the currently logged in user for the application
        $this->be($user = factory('App\User')->create());

        // Defined create() in test/utilities/functions.php
        $thread = create('App\Thread');
        $reply = make('App\Reply');

        // This will post even if there's no route defined
        // To fix this add :
        // if(app())->environment() === 'testing') throw $exception;
        // IN
        // App\Exceptions\Handler.php method render()
        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);
        $this->post("{$thread->path()}/replies/", $reply->toArray())
            ->assertSessionHasErrors('body');
    }

}
