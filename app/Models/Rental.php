<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rented_at',
        'returned_at',
    ];

    protected $casts = [
        'rented_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the user who rented the book.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rented book.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
