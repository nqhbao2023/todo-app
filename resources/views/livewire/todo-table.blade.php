namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;

class TodoTable extends Component
{
    public $todos = [];

    protected $listeners = ['todoAdded' => 'loadTodos'];

    public function mount()
    {
        $this->loadTodos();
    }

    public function loadTodos()
    {
        $this->todos = Todo::with(['assignedTo', 'createdBy'])->latest()->get();
    }

    public function render()
    {
        // Dùng lại file partial có sẵn
        return view('partials.todo_table', [
            'todos' => $this->todos,
        ]);
    }
}
