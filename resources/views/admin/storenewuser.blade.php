public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'password' => 'required|min:6',
        'balance' => 'required|numeric',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'balance' => $request->balance
    ]);

    return redirect()->route('admin.users')->with('success', 'User created successfully.');
}
