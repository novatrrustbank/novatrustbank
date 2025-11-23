namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function index()
    {
        // Dashboard data
        $uploads = Upload::latest()->take(20)->get();
        $transactions = Transaction::latest()->take(10)->get();
        $users = User::all();

        return view('admin.dashboard', compact('uploads', 'transactions', 'users'));
    }

    // ✅ Add this method to handle /admin/users page
    public function users()
    {
        $users = User::orderBy('id', 'DESC')->get();

        return view('admin.users', compact('users'));
    }
}