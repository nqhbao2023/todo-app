<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoProgress extends Model
{
    protected $table = 'todo_progresses';

    protected $fillable = ['todo_id', 'progress_date', 'quantity'];

    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
}
