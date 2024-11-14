<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     /**
      * @OA\Get(
      *     path="/user/getUser",
      *     tags={"Users"},
      *     summary="Get all users",
      *     @OA\Response(
      *         response=200,
      *         description="Success",
      *         @OA\Schema(
      *             type="array",
      *             @OA\Items(ref="#/components/schemas/User")
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthorized"
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error"
      *     ),
      * )
      */
    public function getAllUsers()
    {
       Try {
        $users = User::all();
        return response()->json([
        'data' => $users,
        'message' => 'Users retrieved successfully'
        ], 200);
       } catch (\Exception $e) {
        return response()->json([
        'message' => 'Failed to retrieve users',
        'error' => $e->getMessage()
        ], 500);
       }
    }

    /**
     * update password User
     */

    /**
     * @OA\Post(
     *     path="/user/updatePassword",
     *     tags={"Users"},
     *     summary="Update user password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="current_password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="new_password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="confirm_password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Password updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Current password is incorrect"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="Failed to update password"),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     ),
     *     security={
      *         {"bearerAuth": {}}
      *     }
     * )
     */
    public function updatePassword(Request $request)
    {
        try {
            //update password
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // This returns validation errors as JSON
            ], 400);
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        $User = User::find(Auth::user()->id);
        if ($User) {
            $User->password = Hash::make($request->new_password);
            $User->save();
            return response()->json([
                'status' => 200,
                'message' => 'Password updated successfully',
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update password',
                'error' => $e->getMessage()
            ]);
        }

    }

    /**
     * update user profile
     */

     /**
      * @OA\Post(
      *     path="/user/updateProfile",
      *     tags={"Users"},
      *     summary="Update user profile",
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
      *                 )
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Success",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="status", type="integer", example=200),
      *             @OA\Property(property="message", type="string", example="Profile updated successfully")
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Bad Request",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="status", type="integer", example=400),
      *             @OA\Property(property="message", type="string", example="Validation failed"),     
      *             @OA\Property(property="errors", type="object")
      *         )     
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="Internal Server Error",
      *         @OA\JsonContent(
      *             type="object",
      *             @OA\Property(property="status", type="integer", example=500),
      *             @OA\Property(property="message", type="string", example="Failed to update profile"),
      *             @OA\Property(property="error", type="string", example="Detailed error message")
      *         )
      *     ),
      *     security={
      *         {"bearerAuth": {}}
      *     }
      * )
      */
    public function updateProfile(Request $request)
    {
        try {
            //update user profile
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|unique:users,email,' . Auth::user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(), // This returns validation errors as JSON
            ], 400);
        }
        $User = User::find(Auth::user()->id);
        if ($User) {
            $User->name = $request->name;
            $User->email = $request->email;
            $User->save();
            return response()->json([
                'status' => 200,
                'message' => 'Profile updated successfully',
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
