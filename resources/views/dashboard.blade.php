<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <title>Todo List</title>
        @vite('resources/css/app.css')

    </head>
    <body class ="bg-gray-100 font-mono" >



<h2> Todo List </h2>

<form action="/todos" method="POST">
    @csrf 
    <input type="text" name="title" placeholder="Việc cần làm"/>
    <button type="submit">Thêm</button>
</form>

<ul>
    @foreach ($todos as $todo)
    <li>
        @if($todo->completed)
        <del>{{ $todo->title }}</del>
        @else
        {{ $todo->title }}
        @endif

        <a href="/todos/{{ $todo->id }}/toggle">
            [@if($todo->completed) Huỷ @else Hoàn thành @endif]
        </a>

        <form method="POST" action="/todos/{{ $todo->id }}" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit">Xoá</button>
        </form>
    </li>
    @endforeach
</ul>

<form method="POST" action="/logout">
    @csrf 
    <button>Logout</button>
</form>

    </body>
</html>
