<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    /**
     * Rent a book if available
     *
     * @authenticated
     * 
     * @bodyParam book_id int required The ID of the book to rent.
     * 
     * @response 200 {
     *   "message": "Book rented successfully"
     * }
     */
    public function rent(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_copies < 1) {
            return response()->json(['message' => 'Book not available'], 400);
        }

        $alreadyRented = Rental::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($alreadyRented) {
            return response()->json(['message' => 'You already rented this book'], 400);
        }

        Rental::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ]);

        $book->decrement('available_copies');

        return response()->json(['message' => 'Book rented successfully']);
    }

    /**
     * Return a rented book
     * 
     * @authenticated
     * 
     * @urlParam id int required The ID of the rental.
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
            return response()->json(['message' => 'No active rental found'], 404);
        }

        $rental->update(['returned_at' => now()]);
        $rental->book->increment('available_copies');

        return response()->json(['message' => 'Book returned successfully']);
    }

    /**
     * View all my rentals
     * 
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "book_id": 2,
     *     "rented_at": "2025-08-02T13:00:00Z",
     *     "returned_at": null
     *   }
     * ]
     */
    public function myRentals()
    {
        return Auth::user()->rentals()->with('book')->get();
    }
}
