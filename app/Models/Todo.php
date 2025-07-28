<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $kpi_target
 * @property int|null $total_progress
 * @property int|null $assigned_to
 * @property string|null $title
 * @property \Carbon\Carbon|null $deadline
 * @property string|null $priority
 * @property string|null $status
 * @property string|null $detail
 * @property bool $completed
 * @property int $id
  * @property string|null $attachment_link
    * @property string|null $repeat
    * @property bool $important

 */
class Todo extends Model
{
    use HasFactory;

    // ĐÚNG: assigned_to (không phải 'assigned to')
    protected $fillable = [
        'user_id',
        'assigned_to',
        'completed',
        'title',
        'kpi_target',
        'attachment_link',
        'deadline',
        'priority',
        'status',
        'detail',
        'repeat',
        'important',
        'repeat_custom',
        'flagged',
        'attachment_file',

    ];

    protected $casts = [
        'deadline'  => 'datetime',
        'completed' => 'boolean',
    ];

    // Quan hệ: mỗi công việc (Todo) thuộc về một người dùng (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progresses()
    {
        return $this->hasMany(TodoProgress::class);
    }

    // Người được giao việc (assignee)
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Tổng số đã làm được cho todo này (property ảo: total_progress)
    public function getTotalProgressAttribute()
    {
        return $this->progresses->sum('quantity');
    }

    // Tính % tiến độ so với mục tiêu (property ảo: percent_progress)
    public function getPercentProgressAttribute()
    {
        if ($this->kpi_target > 0) {
            return round($this->total_progress / $this->kpi_target * 100, 1);
        }
        return 0;
    }

    // Kiểm tra đã hoàn thành chưa (property ảo: is_completed_kpi)
    public function getIsCompletedKpiAttribute()
    {
        return $this->total_progress >= $this->kpi_target;
    }
}
