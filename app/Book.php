<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;
use App\BorrowLog;
use Session;

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

    public static function boot()
    {
      parent::boot();

      self::updating(function($book)
      {
        if ($book->amount < $book->borrowed) {
          Session::flash("flash_notification", [
            "level" => "danger",
            "message" => "Jumlah buku $book->title harus >= " . $book->borrowed
          ]);

          return false;
        }
      });
    }

    public function getBorrowedAttribute()
    {
      return $this->borrowLogs()->borrowed()->count();
    }
}
