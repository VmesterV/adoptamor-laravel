<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ELIMINAMOS EL __CONSTRUCT QUE DABA ERROR

    public function index()
    {
        // SEGURIDAD MANUAL: Verificamos aquí si es admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        // 1. Estadísticas Generales
        $stats = [
            'total_users' => User::count(),
            'total_pets' => Pet::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'income' => Order::where('status', 'paid')->orWhere('status', 'completed')->sum('total'),
        ];

        // 2. Elementos Pendientes de Aprobación
        $pendingPets = Pet::where('is_approved', false)
                          ->with('user')
                          ->orderBy('created_at', 'asc')
                          ->get();

        $pendingProducts = Product::where('is_approved', false)
                                  ->with('user')
                                  ->orderBy('created_at', 'asc')
                                  ->get();

        return view('dashboard.index', compact('stats', 'pendingPets', 'pendingProducts'));
    }

    public function approvePet($id)
    {
        if (Auth::user()->role !== 'admin') abort(403); // Seguridad extra

        $pet = Pet::findOrFail($id);
        $pet->update(['is_approved' => true, 'published_at' => now()]);

        return redirect()->back()->with('success', 'Mascota aprobada.');
    }

    public function rejectPet($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $pet = Pet::findOrFail($id);
        $pet->delete(); // SoftDelete

        return redirect()->back()->with('error', 'Publicación rechazada.');
    }

    public function approveProduct($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $product = Product::findOrFail($id);
        $product->update(['is_approved' => true, 'published_at' => now()]);

        return redirect()->back()->with('success', 'Producto aprobado.');
    }

    public function rejectProduct($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('error', 'Producto rechazado.');
    }
}