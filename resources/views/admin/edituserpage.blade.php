public function editUserPage($id)
{
    $user = User::find($id);

    if (!$user) return back()->with('error', 'User not found.');

    return view('admin.edit-user', compact('user'));
}
