<h2>Todo List của bạn</h2>

<form action="/todos" method="POST">
    @csrf
    <input type="text" name="title" placeholder="Việc cần làm">
    <button type="submit">Thêm</button>
</form>

<ul>
    @foreach($todos as $todo)
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
                <button type="submit">Xóa</button>
            </form>
        </li>
    @endforeach
</ul>

<form method="POST" action="/logout">
    @csrf
    <button>Đăng xuất</button>
</form>

