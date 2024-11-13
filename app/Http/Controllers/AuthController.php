<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="User registration",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registered successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function register(Request $request, User $user)
    {
        try {
            // Validate data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Register user
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            return response()->json([
                'data' => $user,
                'message' => 'Registered successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */

     /**
      * @OA\Post(
      *     path="/auth/login",
      *     tags={"Auth"},
      *     summary="User login",
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\MediaType(
      *             mediaType="application/json",
      *             @OA\Schema(
      *                 @OA\Property(
      *                     property="email",
      *                     type="string",
      *                     format="email"
      *                 ),
      *                 @OA\Property(
      *                     property="password",
      *                     type="string"
      *                 )
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Logged in successfully"
      *     ),
      *     @OA\Response(
      *         response=404,
      *         description="Not found"
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Bad Request"
      *     ),
      *     @OA\Response(
      *         response=419,
      *         description="session token has expired"
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error"
      *     )
      * )
      */
    public function login(Request $request)
    {
        //login user
        try {
            // Validate data
            $validatedData = $request->validate([
                'email' =>'required|string|email|max:255',
                'password' => 'required|string',
            ]);

            // Attempt to authenticate user
            if (Auth::attempt($validatedData)) {
                // Generate JWT token
                $token = Auth::user()->createToken('authToken')->plainTextToken;

                return response()->json([
                    'data' => Auth::user(),
                    'token' => $token,
                   'message' => 'Logged in successfully'
                ], 200);
            } else {
                return response()->json([
                   'message' => 'Invalid credentials'
                ], 400);
            }

        } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to login user',
                    'error' => $e->getMessage()
                ], 500);
            }
        }


        /**
     * Logout the user from the application.
     */

     /**
      * @OA\Post(
      *     path="/auth/logout",
      *     tags={"Auth"},
      *     summary="User logout",
      *     @OA\Response(
      *         response=200,
      *         description="Logged out successfully"
      *     ),
      *     @OA\Response(
      *         response=404,
      *         description="Not found"
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Bad Request"
      *     ),
      *     @OA\Response(
      *         response=419,
      *         description="Session token missing"
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error"
      *     ),
      *     security={
      *         {"bearerAuth": {}}
      *     }
      * )
      */
    
      public function logout(Request $request)
      {
          try {
              // Check if the user is authenticated and has a token
              if ($request->user()) {
                  // Revoke all tokens for the user (or revoke a specific token)
                  $request->user()->tokens()->delete();
      
                  return response()->json([
                      'message' => 'Logged out successfully'
                  ], 200);
              } else {
                  return response()->json([
                      'message' => 'No authenticated user found'
                  ], 404);
              }
          } catch (\Exception $e) {
              // Handle any errors during the logout process
              return response()->json([
                  'message' => 'Failed to logout user',
                  'error' => $e->getMessage()
              ], 500);
          }
      }
    
    

    /**
     * Update the specified resource in storage.
     */

    /**
 /**
     * Redirect to Google for authentication.
     *
     * @OA\Get(
     *     path="/auth/google/redirect",
     *     tags={"Auth"},
     *     summary="Redirect to Google for login",
     *     description="Redirects the user to Google OAuth 2.0 authentication page",
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to Google login page"
     *     ),
     *     security={
     *         {"oauth2": {"google"}}
     *     },
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
public function googleredirect()
{
    // Redirect to Google for login
    return Socialite::driver('google')->redirect();
}

/**
     * Handle callback from Google after authentication.
     *
     * @OA\Get(
     *     path="/auth/google/callback",
     *     tags={"Auth"},
     *     summary="Callback from Google after login",
     *     description="Handles the callback from Google and authenticates the user",
     *     @OA\Response(
     *         response=200,
     *         description="Logged in successfully or user created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", description="User data"),
     *             @OA\Property(property="token", type="string", description="JWT token"),
     *             @OA\Property(property="message", type="string", description="Login message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
public function googleLogin(User $user)
{
    // Login or create user using Google
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user->token;
    $user = User::updateOrCreate([
        'google_id' => $googleUser->id,
    ], [
        'name' => $googleUser->name,
        'email' => $googleUser->email,
        'google_id' => $googleUser->id,
        'password' => Hash::make('12345'),
    ]);

    // Generate JWT token
    $token = $user->createToken('authToken')->plainTextToken;

    

    return redirect('/login')->with('token', $token);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
