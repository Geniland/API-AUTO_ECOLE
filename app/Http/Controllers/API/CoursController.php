<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cours; // Assurez-vous que le modèle Cours est importé
use Illuminate\Support\Facades\Storage; // Assurez-vous que Storage est importé

class CoursController extends Controller
{
    public function index()
    {
        return Cours::all();
    }

    public function show($id)
    {
        return Cours::find($id);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('pdf')) {
            \Log::info('PDF file is present');
            $path = $request->file('pdf')->store('cours', 'public');
            \Log::info('PDF stored at: ' . $path);
        } else {
            \Log::info('No PDF file present');
        }
    
        $cours = Cours::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'pdf_path' => $path,
        ]);

        return response()->json($cours, 201);
    }

    public function update(Request $request, $id)
    {
        $cours = Cours::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('pdf')) {
            // Supprimer l'ancien fichier PDF s'il existe
            if ($cours->pdf_path) {
                Storage::disk('public')->delete($cours->pdf_path);
            }
            $path = $request->file('pdf')->store('cours', 'public');
            $cours->pdf_path = $path;
        }

        $cours->update($validatedData);

        return response()->json($cours, 200);
    }

    public function destroy($id)
    {
        $cours = Cours::findOrFail($id);
        if ($cours->pdf_path) {
            Storage::disk('public')->delete($cours->pdf_path);
        }
        $cours->delete();
        return response()->json(null, 204);
    }
}
