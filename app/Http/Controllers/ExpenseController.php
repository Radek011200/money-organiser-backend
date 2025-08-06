<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())->get();
        return response()->json(['expenses' => $expenses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
        ]);

        // Sprawdzenie, czy kategoria jest kategorią wydatków
        if (!empty($validatedData['category_id'])) {
            $category = \App\Models\Category::findOrFail($validatedData['category_id']);
            if ($category->is_income) {
                return response()->json([
                    'message' => 'Wybrana kategoria nie jest kategorią wydatków',
                ], 422);
            }

            // Check if the category belongs to the authenticated user
            if ($category->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Nie masz dostępu do tej kategorii',
                ], 403);
            }
        }

        // Add user_id to the validated data
        $validatedData['user_id'] = Auth::id();

        // Zapis wydatku w bazie danych
        $expense = Expense::create($validatedData);

        // Zwrot odpowiedzi JSON
        return response()->json([
            'message' => 'Wydatek dodany pomyślnie',
            'expense' => $expense,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['expense' => $expense]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'sometimes|required|date',
        ]);

        // Sprawdzenie, czy kategoria jest kategorią wydatków
        if (!empty($validatedData['category_id'])) {
            $category = \App\Models\Category::findOrFail($validatedData['category_id']);
            if ($category->is_income) {
                return response()->json([
                    'message' => 'Wybrana kategoria nie jest kategorią wydatków',
                ], 422);
            }

            // Check if the category belongs to the authenticated user
            if ($category->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Nie masz dostępu do tej kategorii',
                ], 403);
            }
        }

        $expense->update($validatedData);

        return response()->json([
            'message' => 'Wydatek zaktualizowany pomyślnie',
            'expense' => $expense,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return response()->json([
            'message' => 'Wydatek usunięty pomyślnie'
        ]);
    }
}
