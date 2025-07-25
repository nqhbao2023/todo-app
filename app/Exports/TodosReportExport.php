<?php
namespace App\Exports;

use App\Models\Todo;
use App\Models\TodoProgress;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TodosReportExport implements FromView
{
    protected $todos;

    public function __construct($todos)
    {
        $this->todos = $todos;
    }

    public function view(): View
    {
        return view('exports.todos_report', [
            'todos' => $this->todos
        ]);
    }
}
