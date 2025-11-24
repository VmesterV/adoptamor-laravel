<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adoption;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;

class AdoptionController extends Controller
{
    /**
     * Procesa la solicitud de adopción
     */
    public function store(Request $request, $id)
    {
        // 1. Buscar la mascota
        $pet = Pet::findOrFail($id);

        // 2. Verificar si ya la adoptó antes (Opcional, para evitar duplicados)
        $exists = Adoption::where('pet_id', $pet->id)
                          ->where('user_id', Auth::id())
                          ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Ya has enviado una solicitud para esta mascota.');
        }

        // 3. Crear la solicitud
        Adoption::create([
            'pet_id' => $pet->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        // 4. Cambiar estado de la mascota (Opcional, o dejarla visible hasta que el dueño apruebe)
        // Por ahora solo notificamos.
        
        return redirect()->back()->with('success', '¡Solicitud enviada! El refugio se pondrá en contacto contigo.');
    }
}