<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Mail\DeactivationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::query()
            ->paginate();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user->load('profile', 'products', 'organisations');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deactivate(Request $request)
    {
        $user = $request->user();

        if (!$request->confirmation) {
            return response()->json([
                "message" => "Confirmation needs to be true for deactivation to proceed"
            ]);
        }

        if ($user->is_active == 1) {
            $user->is_active = 0;
            $user->save();

            try {
                Mail::to($user->email)->send(new DeactivationMail($user->name));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error: Mail not sent successfully',
                    'error' => $e->getMessage()
                ], 500);
            }

            return response()->json([
                'message' => 'Account deactivated and email sent successfully'
            ],200);
        }

        return response()->json([
            'message' => 'User is already deactivated'
        ], 400);
    }

}
