<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Todo;

class TodoReminderNotification extends Notification
{
    use Queueable;
    protected $todo;
    protected $type; // Loại nhắc nhở (hôm nay, sắp tới, quá hạn)

    public function __construct(Todo $todo, $type = 'today')
    {
        $this->todo = $todo;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = '⏰ Nhắc nhở công việc';
        $line = '';
        if ($this->type === 'today') $line = 'Công việc này có hạn chót hôm nay!';
        if ($this->type === 'soon') $line = 'Công việc này sẽ đến hạn trong 1 ngày nữa!';
        if ($this->type === 'overdue') $line = 'Công việc này đã quá hạn!';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Chào ' . $notifiable->name . ',')
            ->line($line)
            ->line('Tên công việc: ' . $this->todo->title)
            ->line('Deadline: ' . ($this->todo->deadline ?? 'Không có'))
            ->line('Trạng thái: ' . ($this->todo->status ?? 'Chưa cập nhật'))
            ->line('Mô tả: ' . ($this->todo->description ?? '(Không có mô tả)'))
            ->line('Hãy kiểm tra ứng dụng để cập nhật tiến độ nhé!');
    }
}
