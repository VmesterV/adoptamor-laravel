<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Adoption;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PublishController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'person') {
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        // --- 1. SECCIÓN: PUBLICACIONES ACTIVAS ---
        // Buscador: 'search_active'
        // Paginación: 'page_active'
        $searchActive = $request->input('search_active');
        $statusActive = $request->input('status_active');

        $activeItems = collect();

        // Si es Refugio/Admin buscamos Mascotas
        if ($user->role === 'shelter' || $user->role === 'admin') {
            $petsQuery = Pet::where('user_id', $user->id)->where('status', 'available');
            
            if ($searchActive) {
                $petsQuery->where(function($q) use ($searchActive) {
                    $q->where('name', 'like', "%{$searchActive}%")
                      ->orWhere('breed', 'like', "%{$searchActive}%")
                      ->orWhere('description', 'like', "%{$searchActive}%");
                });
            }
            // Unimos resultados con productos abajo para paginar juntos o separados.
            // Para simplificar la vista unificada, traeremos colecciones separadas pero paginadas.
            // NOTA: Para no complicar la mezcla de dos modelos en un paginador, 
            // mostramos productos y mascotas en listas separadas si eres refugio, 
            // o usamos la lógica anterior. Aquí mantendré colecciones separadas para orden.
        }

        // Lógica simplificada: Traemos Productos Paginados
        $prodQuery = Product::where('user_id', $user->id)->where('is_active', true);
        if ($searchActive) {
            $prodQuery->where(function($q) use ($searchActive) {
                $q->where('name', 'like', "%{$searchActive}%")
                  ->orWhere('description', 'like', "%{$searchActive}%")
                  ->orWhere('category', 'like', "%{$searchActive}%");
            });
        }
        $myProducts = $prodQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page_products');

        // Traemos Mascotas Paginadas (Solo si corresponde)
        $myPets = collect();
        if ($user->role === 'shelter' || $user->role === 'admin') {
            $petsQuery = Pet::where('user_id', $user->id)->where('status', 'available');
            if ($searchActive) {
                $petsQuery->where(function($q) use ($searchActive) {
                    $q->where('name', 'like', "%{$searchActive}%")
                      ->orWhere('breed', 'like', "%{$searchActive}%");
                });
            }
            $myPets = $petsQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page_pets');
        }


        // --- 2. SECCIÓN: VENTAS REALIZADAS ---
        // Buscador: 'search_sales'
        // Paginación: 'page_sales'
        $searchSales = $request->input('search_sales');
        
        $salesQuery = OrderDetail::whereHas('product', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->with(['product', 'order.user']);

        if ($searchSales) {
            $salesQuery->where(function($q) use ($searchSales) {
                // Buscar por nombre de producto
                $q->whereHas('product', function($sq) use ($searchSales) {
                    $sq->where('name', 'like', "%{$searchSales}%");
                })
                // O buscar por nombre del comprador
                ->orWhereHas('order.user', function($sq) use ($searchSales) {
                    $sq->where('name', 'like', "%{$searchSales}%");
                });
            });
        }

        $salesHistory = $salesQuery->latest()->paginate(10, ['*'], 'page_sales');


        // --- 3. SECCIÓN: SOLICITUDES DE ADOPCIÓN (Finales Felices / Por Aprobar) ---
        // Buscador: 'search_adoptions'
        // Paginación: 'page_adoptions'
        $searchAdoptions = $request->input('search_adoptions');
        $adoptionRequests = collect(); // Vacío para tiendas

        if ($user->role === 'shelter' || $user->role === 'admin') {
            $adoptQuery = Adoption::whereHas('pet', function($q) use ($user) {
                                $q->where('user_id', $user->id);
                            })
                            ->with(['pet', 'user']);

            if ($searchAdoptions) {
                $adoptQuery->where(function($q) use ($searchAdoptions) {
                    // Buscar por nombre de mascota
                    $q->whereHas('pet', function($sq) use ($searchAdoptions) {
                        $sq->where('name', 'like', "%{$searchAdoptions}%");
                    })
                    // O nombre de adoptante
                    ->orWhereHas('user', function($sq) use ($searchAdoptions) {
                        $sq->where('name', 'like', "%{$searchAdoptions}%");
                    });
                });
            }

            $adoptionRequests = $adoptQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page_adoptions');
        }

        return view('pages.publish', compact('myProducts', 'myPets', 'salesHistory', 'adoptionRequests'));
    }

    // --- ACCIONES DE ESTADO ---

    /**
     * Concretar Venta (Cambiar estado del pedido a 'completed' o similar)
     * Nota: Como un pedido puede tener varios productos de varias tiendas,
     * aquí simularemos que "Marcamos como entregado" este item específico o la orden completa.
     * Para simplificar, marcaremos la Orden completa como 'completed' si el usuario lo decide.
     */
    public function markSaleAsDelivered($id)
    {
        // $id es el ID del OrderDetail (el item vendido)
        $detail = OrderDetail::findOrFail($id);
        
        // Verificamos que el producto sea mío
        if ($detail->product->user_id !== Auth::id()) abort(403);

        // Actualizamos la orden padre a 'completed' (Entregado)
        // Ojo: Esto afecta a toda la orden. En un sistema complejo sería por linea.
        $detail->order->update(['status' => 'completed']);

        return back()->with('success', 'Venta marcada como entregada/concretada.');
    }

    public function cancelSale($id)
    {
        $detail = OrderDetail::findOrFail($id);
        
        if ($detail->product->user_id !== Auth::id()) abort(403);

        // 1. Restaurar el stock
        $detail->product->increment('stock', $detail->quantity);

        // 2. Marcar orden como cancelada
        $detail->order->update(['status' => 'cancelled']);

        return back()->with('error', 'Venta cancelada y stock restaurado.');
    }

    /**
     * Aprobar Adopción
     */
    public function approveAdoption($id)
    {
        $adoption = Adoption::findOrFail($id);
        
        // Verificar que la mascota sea mía
        if ($adoption->pet->user_id !== Auth::id()) abort(403);

        // 1. Marcar adopción como aprobada
        $adoption->update(['status' => 'approved']);

        // 2. Marcar mascota como adoptada
        $adoption->pet->update(['status' => 'adopted']);

        // 3. (Opcional) Rechazar otras solicitudes para la misma mascota
        Adoption::where('pet_id', $adoption->pet_id)
                ->where('id', '!=', $id)
                ->update(['status' => 'rejected']);

        return back()->with('success', '¡Felicidades! Adopción aprobada correctamente.');
    }

    public function rejectAdoption($id)
    {
        $adoption = Adoption::findOrFail($id);
        
        if ($adoption->pet->user_id !== Auth::id()) abort(403);

        $adoption->update(['status' => 'rejected']);

        return back()->with('error', 'Solicitud de adopción rechazada.');
    }

    // Mantener storePet y storeProduct igual...
    public function storePet(Request $request) {
        $request->validate(['name'=>'required','type'=>'required','age'=>'required','description'=>'required','image_url'=>'required']);
        Pet::create(array_merge($request->all(), ['user_id'=>Auth::id(), 'status'=>'available']));
        return back()->with('success', 'Mascota enviada.');
    }
    public function storeProduct(Request $request) {
        $request->validate(['name'=>'required','category'=>'required','price'=>'required','stock'=>'required','image_url'=>'required']);
        Product::create(array_merge($request->all(), ['user_id'=>Auth::id(), 'is_active'=>true, 'is_approved'=>false]));
        return back()->with('success', 'Producto enviado.');
    }
}