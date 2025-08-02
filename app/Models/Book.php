<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'genre',
        'published_year',
        'total_copies',
        'available_copies',
    ];

    /**
     * Get all rentals for this book.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Check if the book is available for rent.
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }
}
