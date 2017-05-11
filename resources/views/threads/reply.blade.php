<div id="reply-{{$reply->id}}" class="panel panel-default">
    <div class="panel-heading">
        {{-- Made a relationship where user_id is owner() --}}
        <div class="level">
            <h5 class="flex">
                <a href="{{route('profile', $thread->owner)}}">
                    {{$reply->owner->name}}
                </a>
                    {{$reply->created_at->diffForHumans()}}
            </h5>

            <div class="form-group">
                <form class="" action="/replies/{{$reply->id}}/favorites" method="POST">
                    {{csrf_field()}}
                    <button {{$reply->isFavorited() ? 'disabled' : ''}} type="submit" class="btn btn-default" name="button">
                        {{$reply->favorites_count}} {{str_plural('Favorite', $reply->favorites_count)}}
                    </button>
                </form>
            </div>

        </div>
    </div>
    <div class="panel-body">
        {{$reply->body}}
    </div>
</div>
