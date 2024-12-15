<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'categoryId',
        'title',
        'description',
        'status',
        'dueDate',
        'userId',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }
    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->toDateString() : null;
    }
}
