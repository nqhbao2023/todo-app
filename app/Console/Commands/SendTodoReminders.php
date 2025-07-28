<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoReminderNotification;
use Carbon\Carbon;

class SendTodoReminders extends Command
{
    protected $signature = 'todos:send-reminders';
    protected $description = 'Gửi nhắc nhở công việc đến hạn, sắp đến hạn, quá hạn';

    public function handle()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();

        // Nhắc công việc hết hạn hôm nay
        $todosToday = Todo::whereDate('deadline', $today)->where('completed', false)->get();
        foreach ($todosToday as $todo) {
            $user = $todo->assigned_to ? User::find($todo->assigned_to) : $todo->user;
            if ($user) $user->notify(new TodoReminderNotification($todo, 'today'));
        }

        // Nhắc công việc sắp đến hạn (ngày mai)
        $todosSoon = Todo::whereDate('deadline', $tomorrow)->where('completed', false)->get();
        foreach ($todosSoon as $todo) {
            $user = $todo->assigned_to ? User::find($todo->assigned_to) : $todo->user;
            if ($user) $user->notify(new TodoReminderNotification($todo, 'soon'));
        }

        // Nhắc công việc quá hạn
        $todosOverdue = Todo::where('completed', false)->whereDate('deadline', '<', $today)->get();
        foreach ($todosOverdue as $todo) {
            $user = $todo->assigned_to ? User::find($todo->assigned_to) : $todo->user;
            if ($user) $user->notify(new TodoReminderNotification($todo, 'overdue'));
        }

        $this->info('Đã gửi nhắc nhở công việc!');
    }
}
