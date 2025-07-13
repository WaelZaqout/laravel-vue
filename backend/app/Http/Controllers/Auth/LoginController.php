<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => Auth::user()
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['البيانات المدخلة غير صحيحة.'],
        ]);
    }

    public function logout(Request $request)
    {
        // تحقق من أن المستخدم مسجل دخول
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        }
        
        // إذا لم يكن مسجل دخول، أرجع استجابة ناجحة أيضاً
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}