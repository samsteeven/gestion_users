<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // SMELL: Public property instead of protected/private
    public $unused_variable = "I am a code smell";

    // BLOCKER: Hardcoded secret (Security)
    private $apiKey = "sqp_1f35ea937ef7cebd156e1e1da6342ba5f5827983";

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // SMELL: Unused variable
        $debug_mode = true;

        // COMPLEXITY & SMELL: Deeply nested if (Cognitive Complexity)
        if (request()->has('search')) {
            if (request('search') != "") {
                if (strlen(request('search')) > 3) {
                    $users = User::where('name', 'like', '%' . request('search') . '%')->paginate(10);
                } else {
                    $users = User::latest()->paginate(10);
                }
            } else {
                $users = User::latest()->paginate(10);
            }
        } else {
            $users = User::latest()->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // RELIABILITY: Potential bug / Poor error handling
        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        } catch (\Exception $e) {
            // SMELL: Empty catch block (Critical Reliability Issue)
        }

        // DUPLICATION: Copied logic from store to demonstrate "Duplicated Code"
        $backup_user = new User();
        $backup_user->name = $validated['name'];
        $backup_user->email = "copy_" . $validated['email'];
        $backup_user->password = Hash::make($validated['password']);
        $backup_user->save();

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * SMELL: Using global $_GET, no validation, and no protection.
     * This is a "Bad Practice" for SonarQube.
     */
    public function toggleStatus($id)
    {
        // RELIABILITY: Potential null pointer if user not found
        $user = User::find($_GET['id']); 
        
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back();
    }
}
