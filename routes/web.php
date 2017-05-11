<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', 'ThreadsController@index');
Route::post('/', 'ThreadsController@store');
Route::get('/create', 'ThreadsController@create');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');

Route::get('/{channel}/{thread}', 'ThreadsController@show');
Route::delete('/{channel}/{thread}', 'ThreadsController@destroy');

Route::post('/{channel}/{thread}/replies', 'RepliesController@store');
Route::get('/{channel}', 'ThreadsController@index');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store');



// Notes:

/*

ModelFactory.php is our Factory

$thread->load('replies');
$thread->withCount('replies');

make model function getReplyCountAttribute which return $this->replies()->count()
Then we can do $thread->replyCount();

boot() in model:
static::addGlobalScope('replyCount', function($builder) {
    $build->withCount('replies');
});

 MAKE PLURAL OR SINGULAR DEPENDING ON THE VALUE
 {{str_plural('reply', $thread->replies_count)}}

 Pagination:
 IN CONTROLLER:
 return view('threads.show', [
     'thread' => $thread,
     'replies' => $thread->replies()->paginate(10)
 ]);
 THEN IN VIEW:
 {{$replies->links()}}

dd($threads->toSql());

UNIQUE PROPERTY
$table->unique(['user_id', 'favorited_id', 'favorited_type']);



POLICIES:

php artisan make:policy ThreadPolicy --model=Thread

THEN

In AuthServiceProvider.php update $policies like so:
protected $policies = [
    'App\Thread' => 'App\Policies\ThreadPolicy',
];

IN CONTROLLER:

$this->authorize('update', $thread);

WE CAN IMPLEMENT POLICIES IN VIEWS LIKE SO:

@can('update', $thread)
@endcan

Also we can authorize just one user for everything in ThreadPolicy:
public function before($user)
{
    if ($user->name === 'Matas') {
        return true;
    }
}
OR we can do it globally using the Gate::class:
In AuthServiceProvider.php boot() function.



*/
