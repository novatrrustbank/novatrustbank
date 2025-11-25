public function updateUser(Request $request)
{
    $request->validate([
        'user_id' => 'required',
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'balance' => 'required|numeric',
    ]);

    $user = User::find($request->user_id);

    if (!$user) return back()->with('error', 'User not found.');

    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->balance = $request->balance;

    if ($request->password != null) {
        $request->validate(['password' => 'min:6']);
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'User updated successfully.');
}
