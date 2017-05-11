<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;


    protected $thread;


    function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }


    /** @test  */
    function a_user_can_view_all_threads()
    {
        $this->get('/')
            ->assertSee($this->thread->title);
    }

    /** @test  */
    function a_user_can_view_a_single_thread()
    {
        // Again, path() is defined in Thread.php model
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test  */
    function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        $reply = factory('App\Reply')
            ->create(['thread_id' => $this->thread->id]);

        $this->get($this->thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get("/{$channel->slug}")
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create(('App\User'), ['name' => 'John']));

        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get('/?by=John')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    function a_user_can_filter_thread_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        // Because we create threads with no replies in previous tests anyway
        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));

    }
}
