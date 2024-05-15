<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'driving_license' => [
                'required',
                Rule::in(['None', 'B', 'BE', 'C', 'B, BE', 'B, C', 'B, BE, C']),
            ],
            'role' => 'sometimes|required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'driving_license' => $request->driving_license,
        ]);
        if($request->role)
        {
            $user->assignRole($request->role);
        }
        else {
            $user->assignRole('user');
        }
        $validatedQualifications = $request->validate([
           'qualification_ids'=> 'nullable',
        ]);

        if(count($validatedQualifications) > 0) {
            $user->qualifications()->attach(Arr::collapse($validatedQualifications));
        }

        event(new Registered($user));

        return response()->noContent();
    }

    public function show(string $id){
        return User::where('id', $id)->with('roles')->with('qualifications')->get();
    }

    public function index(Request $request)
    {
        return User::with('roles')->with('qualifications')->get();
    }
    public function names(Request $request){
        return DB::table('users')->select('name')->where('id', '!=', $request->user()->id)->get();
    }
    public function update(Request $request, User $user){
        $updateData = $request->validate([
            'email' => 'sometimes|required',
            'driving_license' => ['sometimes','required',
            Rule::in(['None', 'B', 'BE', 'C', 'B, BE', 'B, C', 'B, BE, C']),
            ],
        ]);
        $validatedQualifications = $request->validate([
            'qualification_ids'=> 'nullable',
        ]);
        if(count($validatedQualifications) > 0) {
            $user->qualifications()->sync(Arr::collapse($validatedQualifications));
        }
        $user->update($updateData);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function changeUserRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => ['required',
                Rule::in(['admin', 'warehouse_worker', 'organizer', 'user'])]
        ]);

        $user->syncRoles($request->roles);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
