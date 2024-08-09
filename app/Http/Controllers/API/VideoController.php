<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;

class VideoController extends Controller
{
    /**
     * Handle the video upload request.
     */
    public function upload(Request $request)
    {
        // Validation des champs
        $request->validate([
            'video' => 'required|mimes:mp4,mov,ogg,qt|max:90000000', // Limite à 20MB
            'description' => 'required|string|max:255',
        ]);

        // Stockage de la vidéo
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('videos', 'public');

            // Création de l'entrée en base de données
            $video = new Video();
            $video->path = $path;
            $video->description = $request->input('description');
            $video->save();

            return response()->json(['message' => 'Vidéo téléchargée avec succès', 'video' => $video], 201);
        }

        return response()->json(['error' => 'Erreur lors du téléchargement de la vidéo'], 400);
    }

    public function index()
    {
        $videos = Video::all();
        return response()->json($videos);
    }


}