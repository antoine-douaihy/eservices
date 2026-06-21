<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\FirstLoginOtpController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch all staff members (users with the 'office' role)
        $staffMembers = User::where('role', 'office')->get();

        return view('admin.dashboard', compact('staffMembers'));
    }

   public function create()
       {
           // 1. Get all the offices from the database
           $offices = \App\Models\Office::all();

           // 2. Return YOUR exact view file and pass the offices data to it
           return view('admin.create_staff', compact('offices'));
       }

   public function store(Request $request)
       {
           $request->validate([
               'first_name' => 'required|string|max:255',
               'last_name'  => 'required|string|max:255',
               'email'      => 'required|string|email|max:255|unique:users',
               'office_id'  => 'required|exists:offices,id',
           ]);

           // Generate a random temporary password — the admin never sees or
           // sets it. It's emailed directly to the new staff member, who
           // must use it to log in once and is then forced to choose their
           // own permanent password before they can do anything else.
           $temporaryPassword = Str::password(12);

           $user = User::create([
               'first_name'               => $request->first_name,
               'last_name'                => $request->last_name,
               'email'                    => $request->email,
               'password'                 => Hash::make($temporaryPassword),
               'role'                     => 'office',
               'status'                   => 'active',
               'office_id'                => $request->office_id,
               'requires_first_login_otp' => true,
           ]);

           FirstLoginOtpController::sendTemporaryPasswordEmail($user, $temporaryPassword);

           return redirect()->route('admin.dashboard')->with('success', "New staff member added! Their temporary login password was emailed to {$user->email}.");
       }
       public function edit($id)
           {
               // Find the specific staff member by their ID
               $staff = User::findOrFail($id);

               return view('admin.edit_staff', compact('staff'));
           }

           public function update(Request $request, $id)
           {
               $staff = User::findOrFail($id);

               // 1. Validate the data (Notice the email validation ignores this specific user's current email)
               $request->validate([
                   'first_name' => 'required|string|max:255',
                   'last_name'  => 'required|string|max:255',
                   'email'      => 'required|string|email|max:255|unique:users,email,' . $staff->id,
                   'status'     => 'required|string|in:active,inactive',
               ]);

               // 2. Update their basic info
               $staff->update([
                   'first_name' => $request->first_name,
                   'last_name'  => $request->last_name,
                   'email'      => $request->email,
                   'status'     => $request->status,
               ]);

               // 3. Optional: Only update password if they typed a new one
               if ($request->filled('password')) {
                   $request->validate(['password' => 'string|min:8']);
                   $staff->update(['password' => Hash::make($request->password)]);
               }

               return redirect()->route('admin.dashboard')->with('success', 'Staff member updated successfully.');
           }

           public function destroy($id)
           {
               $staff = User::findOrFail($id);
               $staff->delete();

               return redirect()->route('admin.dashboard')->with('success', 'Staff member removed successfully.');
           }
}
