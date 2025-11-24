<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Consulta Base
        $query = Pet::where('is_approved', true)
                    ->where('status', 'available')
                    ->with('user'); // Necesario para filtrar por ubicación del dueño

        // 2. Filtros de Ubicación (Cascada Inteligente)
        $department = $request->input('department');
        $province   = $request->input('province');
        $district   = $request->input('district');

        // Si no hay filtro y el usuario está logueado, usar su ubicación por defecto
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

        // 3. Otros Filtros (Búsqueda, Tipo, Orden)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('sort')) {
            $direction = $request->sort === 'oldest' ? 'asc' : 'desc';
            $query->orderBy('published_at', $direction);
        } else {
            $query->orderBy('published_at', 'desc');
        }

        // 4. Lógica de Edad (Slider)
        // Obtenemos los resultados para procesar la edad (texto -> número) en PHP
        $petsCollection = $query->get();

        if ($request->filled('min_age') && $request->filled('max_age')) {
            $min = (int) $request->min_age;
            $max = (int) $request->max_age;

            $petsCollection = $petsCollection->filter(function ($pet) use ($min, $max) {
                // Limpiar string: "2 años" -> 2, "5 meses" -> 0
                $ageStr = strtolower($pet->age);
                $number = (int) filter_var($ageStr, FILTER_SANITIZE_NUMBER_INT);
                
                // Si es meses, cuenta como 0 años para el slider
                $years = (str_contains($ageStr, 'mes')) ? 0 : $number;

                return $years >= $min && $years <= $max;
            });
        }

        // Paginación Manual
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 12;
        $pets = new \Illuminate\Pagination\LengthAwarePaginator(
            $petsCollection->forPage($currentPage, $perPage),
            $petsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        if ($request->ajax()) {
            return view('components.pet-grid', compact('pets'))->render();
        }

        return view('pages.home', compact('pets'));
    }
}