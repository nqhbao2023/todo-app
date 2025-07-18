<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['user_id','assigned to', 'completed', 'title','kpi_target','deadline','priority','status','detail'];
        protected $casts = [
        'deadline'  => 'datetime', 
        'completed' => 'boolean',
    ];
    // app/Models/Todo.php
    public function user(){
        
        return $this->belongsTo(User::class);//Xác định quan hệ: mỗi công việc (Todo) thuộc về một người dùng (User).
    }
    use HasFactory;
    
 

    public function progresses()
    {
        return $this->hasMany(TodoProgress::class);
    }
       public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    } 
    // Tổng số đã làm được cho todo này
    public function getTotalProgressAttribute()
    {
        return $this->progresses->sum('quantity');
    }
    
    // Tính % tiến độ so với mục tiêu
    public function getPercentProgressAttribute()
    {
        if ($this->kpi_target > 0) {
            return round($this->total_progress / $this->kpi_target * 100, 1);
        }
        return 0;
    }
    
    // Kiểm tra đã hoàn thành chưa
    public function getIsCompletedKpiAttribute()
    {
        return $this->total_progress >= $this->kpi_target;
    }
    
    
}
