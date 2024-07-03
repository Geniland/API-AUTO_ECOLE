<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return Test::all();
    }

    public function indexTest()
    {
        return Test::with('cours')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'cours_id' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);

        $test = Test::create($request->all());

        return response()->json($test, 201);
    }

    public function show($id)
    {
        return Test::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);

        $request->validate([
            'cours_id' => 'sometimes|required|integer',
            'title' => 'sometimes|required|string|max:255',
        ]);

        $test->update($request->all());

        return response()->json($test, 200);
    }

    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        $test->delete();

        return response()->json(null, 204);
    }
}
