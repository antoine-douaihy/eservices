<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class HomeController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth');
    }

    /**

     * Show the application dashboard.

     *

     * @return \Illuminate\Contracts\Support\Renderable

     */

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'office') {
            return redirect()->route('office.dashboard');
        }

        return redirect()->route('citizen.home');
    }

    public function howItWorks()
    {
        $services = Service::with(['office', 'requiredDocuments'])
            ->where('is_active', true)
            ->orderBy('name_en')
            ->get();

        return view('citizen.how-it-works', compact('services'));
    }
}
