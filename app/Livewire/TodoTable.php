<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;

class TodoTable extends Component
{
    public $todos = [];
    public $tab;
    public $statusList = []; // Nếu có truyền statusList từ ngoài thì nhận
    public $statusArr = [];

    // Nghe event từ form quick add 
    protected $listeners = ['todoAdded' => 'loadTodos'];

    public function mount($tab = null, $statusList = [], $statusArr = [])
    {
        $this->tab = $tab;
        $this->statusList = $statusList;
        $this->statusArr = $statusArr;
        $this->loadTodos();
    }

    public function loadTodos()
    {
        $this->todos = Todo::with(['assignedTo', 'createdBy'])->latest()->get();
    }

    public function render()
    {
        return view('livewire.todo-table');
    }
}
