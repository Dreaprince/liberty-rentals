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
     * Retrieve a list of all books available in the library system.
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
     *     "available_copies": 7,
     *     "created_at": "2025-08-02T12:34:56.000000Z",
     *     "updated_at": "2025-08-02T13:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "title": "Atomic Habits",
     *     "author": "James Clear",
     *     "genre": "Self-help",
     *     "published_year": 2018,
     *     "total_copies": 5,
     *     "available_copies": 2,
     *     "created_at": "2025-08-02T14:20:15.000000Z",
     *     "updated_at": "2025-08-02T14:45:30.000000Z"
     *   }
     * ]
     */

    public function index()
    {
        $books = Book::all();

        if ($books->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No books found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Books retrieved successfully',
            'data' => $books
        ], 200);
    }

    /**
     * Show details for a specific book
     *
     * Returns detailed information for a given book by its ID.
     *
     * @authenticated
     *
     * @urlParam book int required The ID of the book. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "title": "Rich Dad Poor Dad",
     *   "author": "Robert Kiyosaki",
     *   "genre": "Finance",
     *   "published_year": 1997,
     *   "total_copies": 10,
     *   "available_copies": 10,
     *   "created_at": "2025-08-02T12:34:56.000000Z",
     *   "updated_at": "2025-08-02T12:34:56.000000Z"
     * }
     *
     * @response 404 {
     *   "message": "Book not found"
     * }
     */

    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Book retrieved successfully',
            'data' => $book
        ], 200);
    }

    /**
     * Create a new book (Admin only)
     *
     * Allows an admin to add a new book to the library.
     *
     * @authenticated
     *
     * @bodyParam title string required The title of the book. Example: The Alchemist
     * @bodyParam author string required The author of the book. Example: Paulo Coelho
     * @bodyParam genre string required The genre of the book. Example: Fiction
     * @bodyParam published_year integer required The year the book was published. Format: YYYY. Example: 1988
     * @bodyParam total_copies integer required Total number of copies. Minimum: 1. Example: 10
     * @bodyParam available_copies integer required Available number of copies. Must be >= 0. Example: 8
     *
     * @response 201 {
     *   "message": "Book created successfully",
     *   "book": {
     *     "id": 1,
     *     "title": "The Alchemist",
     *     "author": "Paulo Coelho",
     *     "genre": "Fiction",
     *     "published_year": 1988,
     *     "total_copies": 10,
     *     "available_copies": 8,
     *     "created_at": "2025-08-02T14:30:00.000000Z",
     *     "updated_at": "2025-08-02T14:30:00.000000Z"
     *   }
     * }
     *
     * @response 403 {
     *   "message": "Only admins allowed"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "title": ["The title field is required."],
     *     "author": ["The author field is required."]
     *   }
     * }
     */

    public function store(Request $request)
    {
        // Check if the authenticated user is an admin
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Only admins are authorized to perform this action.',
            ], 403);
        }

        // Validate request input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'published_year' => 'required|digits:4|integer',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
        ]);

        // Check if the same book (title & author) already exists
        $exists = Book::where('title', $validated['title'])
            ->where('author', $validated['author'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book already exists with the same title and author.',
            ], 409);
        }

        // Create and return new book
        $book = Book::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Book created successfully.',
            'data' => $book,
        ], 201);
    }


    /**
     * Update an existing book (Admin only)
     * 
     * @authenticated
     * @urlParam id int required The ID of the book to update.
     * 
     * @bodyParam title string The updated title.
     * @bodyParam author string The updated author.
     * @bodyParam genre string The updated genre.
     * @bodyParam published_year integer Format: YYYY.
     * @bodyParam total_copies integer Minimum: 1.
     * @bodyParam available_copies integer >= 0.
     * 
     * @response 200 {
     *   "message": "Book updated successfully",
     *   "book": {
     *     "id": 1,
     *     "title": "Updated Title",
     *     "author": "Updated Author",
     *     "genre": "Updated Genre",
     *     "published_year": 2022,
     *     "total_copies": 5,
     *     "available_copies": 3
     *   }
     * }
     * 
     * @response 403 {
     *   "message": "Only admins allowed"
     * }
     * 
     * @response 404 {
     *   "message": "Book not found"
     * }
     */

    public function update(Request $request, $id)
    {
        // Check admin role
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Only admins are authorized to perform this action.',
            ], 403);
        }

        // Find book by ID manually to handle not-found errors
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book not found.',
            ], 404);
        }

        // Validate input
        $validated = $request->validate([
            'title' => 'string|max:255',
            'author' => 'string|max:255',
            'genre' => 'string|max:100',
            'published_year' => 'digits:4|integer',
            'total_copies' => 'integer|min:1',
            'available_copies' => 'integer|min:0',
        ]);

        // Update book
        $book->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Book updated successfully.',
            'data' => $book,
        ], 200);
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
