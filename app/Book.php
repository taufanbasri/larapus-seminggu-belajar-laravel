<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;
use App\BorrowLog;

class Book extends Model
{
    protected $fillable = ['title', 'author_id', 'amount'];

    public function author()
    {
      return $this->belongsTo(Author::class);
    }

    public function borrowLogs()
    {
      return $this->hasMany(BorrowLog::class);
    }

    /**
     * Get the description's.
     */
    public function getStockAttribute()
    {
        $borrowed = $this->borrowLogs()->borrowed()->count();
        $stock = $this->amount -$borrowed;

        return $stock;
    }
}
