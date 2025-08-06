<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Category::query();

        // Filter by user
        $query->where('user_id', Auth::id());

        // Filter by type if specified
        if ($request->has('type')) {
            $isIncome = $request->type === 'income';
            $query->where('is_income', $isIncome);
        }

        $categories = $query->get();
        return response()->json(['categories' => $categories]);
    }

    /**
     * Display a listing of income categories.
     */
    public function income()
    {
        $categories = \App\Models\Category::where('is_income', true)
            ->where('user_id', Auth::id())
            ->get();
        return response()->json(['categories' => $categories]);
    }

    /**
     * Display a listing of expense categories.
     */
    public function expense()
    {
        $categories = \App\Models\Category::where('is_income', false)
            ->where('user_id', Auth::id())
            ->get();
        return response()->json(['categories' => $categories]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'isMain' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'is_income' => 'boolean',
        ]);

        // Add user_id to the validated data
        $validatedData['user_id'] = Auth::id();

        // Zapis kategorii w bazie danych
        $category = \App\Models\Category::create($validatedData);

        // Zwrot odpowiedzi JSON
        return response()->json([
            'message' => 'Kategoria dodana pomyślnie',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(string $id)
    {
        $category = \App\Models\Category::with(['children'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        return response()->json(['category' => $category]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = \App\Models\Category::where('user_id', Auth::id())->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'isMain' => 'sometimes|boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'is_income' => 'sometimes|boolean',
        ]);

        // Prevent setting parent to self
        if (isset($validatedData['parent_id']) && $validatedData['parent_id'] == $id) {
            return response()->json([
                'message' => 'Kategoria nie może być swoim własnym rodzicem',
            ], 422);
        }

        $category->update($validatedData);

        return response()->json([
            'message' => 'Kategoria zaktualizowana pomyślnie',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(string $id)
    {
        $category = \App\Models\Category::where('user_id', Auth::id())->findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Kategoria usunięta pomyślnie'
        ]);
    }
}
