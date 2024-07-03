<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;

class OptionController extends Controller
{
 

    public function index($questionId)
    {
        $question = Question::findOrFail($questionId);
        return $question->options;
    }

    public function store(Request $request, $questionId)
    {
        $request->validate([
            'option_text' => 'required|string',
            'is_correct' => 'required|boolean',
        ]);

        $question = Question::findOrFail($questionId);

        $option = $question->options()->create([
            'option_text' => $request->option_text,
            'is_correct' => $request->is_correct,
        ]);

        return response()->json($option, 201);
    }

    public function show($questionId, $optionId)
    {
        $question = Question::findOrFail($questionId);
        $option = $question->options()->findOrFail($optionId);
        return response()->json($option);
    }

    public function update(Request $request, $questionId, $optionId)
    {
        $option = Option::where('question_id', $questionId)->findOrFail($optionId);

        $request->validate([
            'option_text' => 'sometimes|required|string',
            'is_correct' => 'sometimes|required|boolean',
        ]);

        if ($request->has('option_text')) {
            $option->option_text = $request->option_text;
        }

        if ($request->has('is_correct')) {
            $option->is_correct = $request->is_correct;
        }

        $option->save();

        return response()->json($option, 200);
    }

    public function destroy($questionId, $optionId)
    {
        $option = Option::where('question_id', $questionId)->findOrFail($optionId);
        $option->delete();
        return response()->json(null, 204);
    }
}
