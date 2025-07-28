<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Todo;

class TodoCreatedNotification extends Notification
{
    use Queueable;

    protected $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🆕 Bạn có công việc mới được giao')
            ->greeting('Chào ' . $notifiable->name . ',')
            ->line('Bạn vừa được giao một công việc mới:')
            ->line('-----------------------------------')
            ->line('📝 **Tên công việc:** ' . $this->todo->title)
            ->line('📅 **Deadline:** ' . ($this->todo->deadline ?? 'Chưa có'))
            ->line('🔖 **KPI:** ' . ($this->todo->kpi_target ?? 'Chưa cập nhật'))

            ->line('-----------------------------------')
            ->line('Hãy hoàn thành đúng hạn nhé 💪');
    }
}
