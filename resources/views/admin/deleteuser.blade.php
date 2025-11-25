public function deleteUser(Request $request)
{
    $user = User::find($request->user_id);

    if (!$user) return back()->with('error', 'User not found.');

    $user->delete();

    return back()->with('success', 'User deleted.');
}
