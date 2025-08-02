<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * List all books
     * 
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "title": "Rich Dad Poor Dad",
     *     "author": "Robert Kiyosaki",
     *     "genre": "Finance",
     *     "published_year": 1997,
     *     "total_copies": 10,
     *     "available_copies": 10,
     *     "created_at": "2025-08-02T12:34:56.000000Z",
     *     "updated_at": "2025-08-02T12:34:56.000000Z"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(Book::all(), 200);
    }

    /**
     * Show details for a specific book
     * 
     * @authenticated
     * @urlParam book int required The ID of the book.
     * 
     * @response 200 {
     *   "id": 1,
     *   "title": "Rich Dad Poor Dad",
     *   "author": "Robert Kiyosaki",
     *   "genre": "Finance",
     *   "published_year": 1997,
     *   "total_copies": 10,
     *   "available_copies": 10
     * }
     */
    public function show(Book $book)
    {
        return response()->json($book, 200);
    }

    /**
     * Create a new book (Admin only)
     * 
     * @authenticated
     * @bodyParam title string required Title of the book.
     * @bodyParam author string required Author of the book.
     * @bodyParam genre string required Genre of the book.
     * @bodyParam published_year integer required Format: YYYY
     * @bodyParam total_copies integer required Minimum: 1
     * @bodyParam available_copies integer required >= 0
     * 
     * @response 201 {
     *   "id": 1,
     *   "title": "New Book",
     *   "author": "Someone",
     *   ...
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Only admins allowed'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'published_year' => 'required|digits:4|integer',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
        ]);

        $book = Book::create($data);

        return response()->json($book, 201);
    }

    /**
     * Update an existing book (Admin only)
     * 
     * @authenticated
     * @urlParam book int required ID of the book to update.
     * @bodyParam title string The updated title.
     * @bodyParam author string The updated author.
     * @bodyParam genre string The updated genre.
     * @bodyParam published_year integer Format: YYYY
     * @bodyParam total_copies integer Minimum: 1
     * @bodyParam available_copies integer >= 0
     * 
     * @response 200 {
     *   "id": 1,
     *   "title": "Updated Title",
     *   ...
     * }
     */
    public function update(Request $request, Book $book)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Only admins allowed'], 403);
        }

        $data = $request->validate([
            'title' => 'string|max:255',
            'author' => 'string|max:255',
            'genre' => 'string|max:100',
            'published_year' => 'digits:4|integer',
            'total_copies' => 'integer|min:1',
            'available_copies' => 'integer|min:0',
        ]);

        $book->update($data);

        return response()->json($book, 200);
    }

    /**
     * Delete a book (Admin only)
     * 
     * @authenticated
     * @urlParam book int required ID of the book to delete.
     * 
     * @response 200 {
     *   "message": "Book deleted"
     * }
     */
    public function destroy(Book $book)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Only admins allowed'], 403);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted'], 200);
    }
}
