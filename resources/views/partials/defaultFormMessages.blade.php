@if(isset($errors) && count($errors))
    <div class="msg-error full content-writable">
        <span class="title">{{$title ?? 'Le formulaire contient des erreurs'}}</span>
        <div class="content">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@if(isset($success))
    <div class="msg-success">
        <span class="title">{{ $success }}</span>
    </div>
@endif
