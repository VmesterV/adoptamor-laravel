<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Muestra el catálogo de productos con filtros.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
                        ->where('is_approved', true)
                        ->where('stock', '>', 0)
                        ->with('user');

        // --- FILTRO UBICACIÓN ---
        $department = $request->input('department');
        $province   = $request->input('province');
        $district   = $request->input('district');

        // Default al usuario
        if (empty($department) && empty($province) && empty($district) && auth()->check() && !$request->filled('search')) {
            $department = auth()->user()->department;
        }

        if ($department) {
            $query->whereHas('user', function($q) use ($department) {
                $q->where('department', 'like', "%{$department}%");
            });
        }
        if ($province) {
            $query->whereHas('user', function($q) use ($province) {
                $q->where('province', 'like', "%{$province}%");
            });
        }
        if ($district) {
            $query->whereHas('user', function($q) use ($district) {
                $q->where('district', 'like', "%{$district}%");
            });
        }

        // --- OTROS FILTROS ---
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc': $query->orderBy('price', 'asc'); break;
                case 'price_desc': $query->orderBy('price', 'desc'); break;
                default: $query->orderBy('published_at', 'desc'); break;
            }
        } else {
            $query->orderBy('published_at', 'desc');
        }

        $products = $query->paginate(12);

        if ($request->ajax()) {
            return view('components.product-grid', compact('products'))->render();
        }

        return view('pages.store', compact('products'));
    }

    /**
     * Muestra el detalle de un solo producto.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.product-detail', compact('product'));
    }

    /**
     * Procesa la compra (Checkout simple).
     * Recibe un JSON con el carrito desde el frontend.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'delivery_type' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:card,yape',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            
            // Crear la Orden Cabecera
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => 0, // Lo calculamos abajo
                'delivery_type' => $request->delivery_type,
                'shipping_address' => $request->address ?? null,
                'payment_method' => $request->payment_method,
                'status' => 'pending' // O 'paid' si simulas pago exitoso
            ]);

            foreach ($request->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                // Validar Stock
                if ($product->stock < $item['qty']) {
                    throw new \Exception("No hay suficiente stock de {$product->name}");
                }

                // Crear Detalle
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price, // Precio congelado
                ]);

                // Actualizar Stock y Total
                $product->decrement('stock', $item['qty']);
                $total += $product->price * $item['qty'];
            }

            // Guardar total final
            $order->update(['total' => $total]);

            DB::commit();

            return response()->json(['success' => true, 'message' => '¡Compra realizada con éxito!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->with(['orderDetails.product.user'])
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json($orders);
    }

}