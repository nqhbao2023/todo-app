<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['user_id', 'completed', 'title','deadline','priority','status','detail'];
        protected $casts = [
        'deadline'  => 'datetime', 
        'completed' => 'boolean',
    ];
    
    public function user(){
        
        return $this->belongsTo(User::class);//Xác định quan hệ: mỗi công việc (Todo) thuộc về một người dùng (User).
    }
    use HasFactory;
}
