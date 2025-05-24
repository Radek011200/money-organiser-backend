<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::all();
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
        $expense = Expense::findOrFail($id);
        return response()->json(['expense' => $expense]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'sometimes|required|date',
        ]);

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
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'message' => 'Wydatek usunięty pomyślnie'
        ]);
    }
}
