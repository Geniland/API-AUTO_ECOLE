<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use App\Models\Option;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

       // Vérifier les réponses soumises par l'utilisateur
       public function verifyAnswers(Request $request)
       {
           $answers = $request->input('answers'); // { question_id: option_id, ... }
   
           $results = [];
           foreach ($answers as $questionId => $optionId) {
               $option = Option::where('id', $optionId)->where('question_id', $questionId)->first();
               if ($option) {
                   $results[$questionId] = $option->is_correct;
               } else {
                   $results[$questionId] = false; // Option invalide
               }
           }
   
           return response()->json($results);
       }

       


    public function index($testId)
    {
        $test = Test::findOrFail($testId);
        return $test->questions()->with('options')->get();
    }

    public function store(Request $request, $testId)
    {
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $test = Test::findOrFail($testId);

        $question = $test->questions()->create([
            'question_text' => $request->question_text,
        ]);

        foreach ($request->options as $optionData) {
            $question->options()->create($optionData);
        }

        return response()->json($question->load('options'), 201);
    }

    public function show($testId, $questionId)
    {
        $test = Test::findOrFail($testId);
        $question = $test->questions()->with('options')->findOrFail($questionId);
        return response()->json($question);
    }

    public function update(Request $request, $testId, $questionId)
    {
        $question = Question::where('test_id', $testId)->findOrFail($questionId);

        $request->validate([
            'question_text' => 'sometimes|required|string',
            'options' => 'sometimes|array',
            'options.*.option_text' => 'sometimes|required|string',
            'options.*.is_correct' => 'sometimes|required|boolean',
        ]);

        if ($request->has('question_text')) {
            $question->question_text = $request->question_text;
        }

        $question->save();

        if ($request->has('options')) {
            $question->options()->delete(); // Supprimer les options existantes

            foreach ($request->options as $optionData) {
                $question->options()->create($optionData);
            }
        }

        return response()->json($question->load('options'), 200);
    }

    public function destroy($testId, $questionId)
    {
        $question = Question::where('test_id', $testId)->findOrFail($questionId);
        $question->delete();
        return response()->json(null, 204);
    }
}
