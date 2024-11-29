<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class BookController extends Controller
{
    /**
     * Display all books.
     */

     /**
      * @OA\Get(
      *     path="/admin/books/getBook",
      *     tags={"Admin"},
      *     summary="Get all books",
      *     @OA\Response(
      *         response=200,
      *         description="Success",
      *         @OA\Schema(
      *             type="array",
      *             @OA\Items(ref="#/components/schemas/Book")
      *         )
      *     ),
      *        
      *     @OA\Response(
      *         response=401,
      *         description="Unauthorized",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="message", type="string", description="Unauthorized message"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="message", type="string", description="Internal Server Error message"),
      *         ),
      *     ),
      * )
      */
    public function getAllBooks()
    {
        //
        $books = Book::all();
        return response()->json([
            'message' => 'success',
            'data' => $books
        ]);
    }

    /**
     * add book to database.
     */

     /**
      * @OA\Post(
      *     path="/admin/books/createBook",
      *     tags={"Admin"},
      *     summary="Add a book",
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\MediaType(
      *             mediaType="multipart/form-data",
      *             @OA\Schema(
      *                 required={"title", "author", "publication_year", "price", "description", "image"},
      *                 @OA\Property(property="title", type="string", description="Title of the book"),
      *                 @OA\Property(property="author", type="string", description="Author of the book"),
      *                 @OA\Property(property="publication_year", type="date", description="Publication year of the book"),
      *                 @OA\Property(property="price", type="integer", description="Price of the book"),
      *                 @OA\Property(property="description", type="string", description="Description of the book"),
      *                 @OA\Property(property="image", type="string", format="binary", description="Image of the book"),
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Success",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="status", type="integer", example=200),
      *             @OA\Property(property="message", type="string", example="Book added successfully"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthorized",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="message", type="string", description="Unauthorized message"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="message", type="string", description="Internal Server Error message"),
      *         ),
      *     ),
      *     security={
      *         {"bearerAuth": {}}
      *     },
      * )
      */

    public function addBook(StoreBookRequest $request)
    {
        try {
            //add books validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required',
            'publication_year' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'validation error',
                'errors' => $validator->errors()
            ]);
        }

        // Process the uploaded image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/books');
            $imagePath = str_replace('public/', 'storage/', $imagePath);
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'publication_year' => $request->publication_year,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath ?? null,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'book added successfully',
            'data' => $book,

        ],200);
        } catch (\Throwable $th) {
            if (isset($imagePath) && Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
            return response()->json([
                'status' => 500,
                'message' => 'internal error',
                'error' => $th->getMessage(),
            ],500);
        }
    }

    /**
 * Update Book.
 */

/**
 * @OA\Put(
 *     path="/admin/books/updateBook/{id}",
 *     tags={"Admin"},
 *     summary="Update a book",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the book to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"title", "author", "publication_year", "price", "description"},
 *                 @OA\Property(property="title", type="string", description="Title of the book", example="The Great Gatsby"),
 *                 @OA\Property(property="author", type="string", description="Author of the book", example="F. Scott Fitzgerald"),
 *                 @OA\Property(property="publication_year", type="string", format="date", description="Publication year of the book", example="1925-04-10"),
 *                 @OA\Property(property="price", type="integer", description="Price of the book", example=10),
 *                 @OA\Property(property="description", type="string", description="Description of the book", example="A novel about the Roaring Twenties."),
 *                 @OA\Property(property="image", type="string", format="binary", description="Image of the book (optional)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Book updated successfully"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Validation error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthorized access")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Internal Server Error")
 *         )
 *     ),
 *     
 * )
 */

 public function updateBook(Request $request, $id)
 {
     try {
        
         $book = Book::findOrFail($id);

        \Log::info('Update Book Request Data:', $request->all());
        \Log::info('Request Parameters:', [
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'publication_year' => $request->input('publication_year'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $request->file('image'),
        ]);

 
        
         $validator = Validator::make($request->all(), [
             'title' => 'required',
             'author' => 'required',
             'publication_year' => 'required',
             'price' => 'required',
             'description' => 'required',
             'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'message' => 'validation error',
                 'errors' => $validator->errors()
             ], 400);
         }
 
         
         if ($request->hasFile('image')) {
             
             if ($book->image) {
                 $oldImagePath = str_replace('storage/', 'public/', $book->image);
                 if (Storage::exists($oldImagePath)) {
                     Storage::delete($oldImagePath);
                 }
             }
 
           
             $imagePath = $request->file('image')->store('public/books');
             $imagePath = str_replace('public/', 'storage/', $imagePath);
         }
 
      
         $book->update([
             'title' => $request->title,
             'author' => $request->author,
             'publication_year' => $request->publication_year,
             'price' => $request->price,
             'description' => $request->description,
             'image' => isset($imagePath) ? $imagePath : $book->image,
         ]);
 
         return response()->json([
             'status' => 200,
             'message' => 'book updated successfully',
             'data' => $book
         ], 200);
 
     } catch (\Throwable $th) {
         // Delete newly uploaded image if exists and error occurs
         if (isset($imagePath) && Storage::exists($imagePath)) {
             Storage::delete($imagePath);
         }
 
         return response()->json([
             'status' => 500,
             'message' => 'internal error',
             'error' => $th->getMessage()
         ], 500);
     }
 }
      

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */

    /**
    * @OA\Delete(
    *     path="/admin/books/deleteBook/{id}",
    *     tags={"Admin"},
    *     summary="Delete a book",
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="ID of the book to delete",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="integer", example=200),
    *             @OA\Property(property="message", type="string", example="Book deleted successfully"),
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="Unauthorized access")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not Found",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="Book not found")
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal Server Error",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="Internal Server Error")
    *         )
    *     ),
    *     security={
    *         {"bearerAuth": {}}
    *     },
    * )
    */
    public function deleteBook(Book $book, $id)
    {
        try {
            $book = Book::findOrFail($id);
            if ($book->image) {
                $imagePath = str_replace('storage/', 'public/', $book->image);
                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                }
            }
            $book->delete();
            return response()->json([
                'status' => 200,
                'message' => 'book deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'internal error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
