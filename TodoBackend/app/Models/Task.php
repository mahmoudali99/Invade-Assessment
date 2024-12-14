<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'categoryId',
        'title',
        'description',
        'status',
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
}