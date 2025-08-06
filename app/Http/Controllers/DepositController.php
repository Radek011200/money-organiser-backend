<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;
use App\Models\Category;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deposits = Deposit::where('user_id', Auth::id())->get();
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

        // Sprawdzenie, czy kategoria jest kategorią przychodów
        if (!empty($validatedData['category_id'])) {
            $category = Category::findOrFail($validatedData['category_id']);
            if (!$category->is_income) {
                return response()->json([
                    'message' => 'Wybrana kategoria nie jest kategorią przychodów',
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

        // Zapis wpłaty w bazie danych
        $deposit = Deposit::create($validatedData);

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
        $deposit = Deposit::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['deposit' => $deposit]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deposit = Deposit::where('user_id', Auth::id())->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'sometimes|required|date',
        ]);

        // Sprawdzenie, czy kategoria jest kategorią przychodów
        if (!empty($validatedData['category_id'])) {
            $category = Category::findOrFail($validatedData['category_id']);
            if (!$category->is_income) {
                return response()->json([
                    'message' => 'Wybrana kategoria nie jest kategorią przychodów',
                ], 422);
            }

            // Check if the category belongs to the authenticated user
            if ($category->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Nie masz dostępu do tej kategorii',
                ], 403);
            }
        }

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
        $deposit = Deposit::where('user_id', Auth::id())->findOrFail($id);
        $deposit->delete();

        return response()->json([
            'message' => 'Wpłata usunięta pomyślnie'
        ]);
    }
}
