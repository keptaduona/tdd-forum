<nav id="mainNav">
    <div class="container">

        <div class="login-form">
            @if(auth()->check())
                <button class="btn btn-default"><a href="/profiles/{{auth()->user()->name}}">My Profile</a></button>
                <button class="btn btn-default"><a href="/logout">Logout</a></button>
            @else
                <button class="btn btn-default"><span class="glyphicon glyphicon-user"></span><a href="/login">Sign in</a></button>
                <button class="btn btn-default"><a href="/register">Register</a></button>
            @endif
        </div>

        <div id="app-title">
            <h3><a href="/">Discuss</a></h3>
        </div>

        <div>
            <ul id="nav-list">
                @foreach($channels as $channel)
                    <li id="channel-{{$channel->name}}" class="page-scroll">
                        <a href="/{{$channel->slug}}">{{$channel->name}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
