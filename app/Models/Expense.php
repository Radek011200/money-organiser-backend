<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'amount', 'category_id', 'date'];

    /**
     * Get the category that owns the expense.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
