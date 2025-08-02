<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    /**
     * Rent a book if available.
     *
     * @authenticated
     * 
     * @bodyParam book_id int required The ID of the book to rent.
     * 
     * @response 201 {
     *   "status": "success",
     *   "message": "Book rented successfully",
     *   "rental": {
     *     "id": 5,
     *     "user_id": 1,
     *     "book_id": 2,
     *     "rented_at": "2025-08-02T13:00:00Z",
     *     "returned_at": null
     *   }
     * }
     */
    public function rent(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $book = Book::find($validated['book_id']);

        if (! $book) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book not found.'
            ], 404);
        }

        if ($book->available_copies < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'This book is currently out of stock.'
            ], 400);
        }

        $alreadyRented = Rental::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($alreadyRented) {
            return response()->json([
                'status' => 'error',
                'message' => 'You already have this book rented. Please return it before renting again.'
            ], 409);
        }

        $rental = Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now(),
        ]);

        $book->decrement('available_copies');

        return response()->json([
            'status' => 'success',
            'message' => 'Book rented successfully.',
            'rental' => $rental
        ], 201);
    }


    /**
     * Return a rented book.
     * 
     * @authenticated
     * 
     * @urlParam id int required The ID of the rental to return.
     * 
     * @response 200 {
     *   "message": "Book returned successfully"
     * }
     */
    public function returnBook($id)
    {
        $rental = Rental::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->first();

        if (! $rental) {
            return response()->json(['message' => 'No active rental found.'], 404);
        }

        $rental->update(['returned_at' => now()]);
        $rental->book->increment('available_copies');

        return response()->json(['message' => 'Book returned successfully'], 200);
    }

    /**
     * View all my rentals.
     * 
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "book": {
     *       "id": 2,
     *       "title": "Book Title"
     *     },
     *     "rented_at": "2025-08-02T13:00:00Z",
     *     "returned_at": null
     *   }
     * ]
     */
    public function myRentals()
    {
        return Auth::user()
            ->rentals()
            ->with('book:id,title')
            ->latest()
            ->get();
    }
}
