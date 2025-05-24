<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deposits = \App\Models\Deposit::all();
        return response()->json(['deposits' => $deposits]);
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

        // Zapis wpłaty w bazie danych
        $deposit = \App\Models\Deposit::create($validatedData);

        // Zwrot odpowiedzi JSON
        return response()->json([
            'message' => 'Wpłata dodana pomyślnie',
            'deposit' => $deposit,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $deposit = \App\Models\Deposit::findOrFail($id);
        return response()->json(['deposit' => $deposit]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deposit = \App\Models\Deposit::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'sometimes|required|date',
        ]);

        $deposit->update($validatedData);

        return response()->json([
            'message' => 'Wpłata zaktualizowana pomyślnie',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deposit = \App\Models\Deposit::findOrFail($id);
        $deposit->delete();

        return response()->json([
            'message' => 'Wpłata usunięta pomyślnie'
        ]);
    }
}
