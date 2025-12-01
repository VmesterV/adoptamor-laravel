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
        $pet = Pet::findOrFail($id);
        $user = Auth::user();

        // 1. Verificar duplicados (Igual que antes)
        $exists = Adoption::where('pet_id', $pet->id)->where('user_id', $user->id)->exists();
        if ($exists) {
            return redirect()->back()->with('toast_error', 'Ya has enviado una solicitud para esta mascota.');
        }

        // 2. Crear solicitud
        Adoption::create([
            'pet_id' => $pet->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // 3. Preparar mensaje de WhatsApp
        $phoneRefugio = $pet->user->phone; // Teléfono del refugio
        
        // Crear mensaje detallado
        $mensaje = "Hola {$pet->user->name}, soy {$user->name}.\n\n";
        $mensaje .= "Acabo de enviar una solicitud formal por la web para adoptar a: {$pet->name}.\n";
        $mensaje .= "Quedo atento a su respuesta para la entrevista.";

        $urlWhatsapp = "https://wa.me/51{$phoneRefugio}?text=" . urlencode($mensaje);

        // 4. Redirigir a WhatsApp usando 'redirect()->away()'
        // Esto abre la URL externa inmediatamente
        return redirect()->away($urlWhatsapp);
    }
}