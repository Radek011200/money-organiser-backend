<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['name', 'amount', 'category_id', 'date', 'user_id'];

    /**
     * Get the category that owns the deposit.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that owns the deposit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
