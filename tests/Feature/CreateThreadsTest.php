<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Activity;

class CreateThreadsTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function guests_may_not_create_threads()
    {

        $this->withExceptionHandling();

        $this->get('/create')
            ->assertRedirect('login');

        $this->post('/')
            ->assertRedirect('login');

    }

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();

        // make() returns a thread instance but doesn't create it in the database
        // raw() return an array which we need for POST

        /** We need to make() here because If we use create(),
            the thread would be created - there wouldn't be
            any sense to POST it again.

            We use $response to get the ID that was assigned to the thread,
            since make() doesn't assign an ID
        */

        // Defined make() in test/utilities/functions.php
        $thread = make('App\Thread');

        $response = $this->post('/', $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        // publishThread() is a custom function
        $this->publishThread(['title' => null])
            /** assertSessionHasErrors()
                Laravel has this because it has an $errors variable that is available
                So we basically check that $errors contains 'title'
            */
            ->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);

    }

    /** @test */
    function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread)
        ]);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $reply->id,
            'subject_type' => get_class($reply)
        ]);

        $this->assertEquals(0, Activity::count());
    }

    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/', $thread->toArray());
    }
}
