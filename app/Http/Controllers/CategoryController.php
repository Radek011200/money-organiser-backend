<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = \App\Models\Category::all();
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
        ]);

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
        $category = \App\Models\Category::with(['children'])->findOrFail($id);
        return response()->json(['category' => $category]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = \App\Models\Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'isMain' => 'sometimes|boolean',
            'parent_id' => 'nullable|exists:categories,id',
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
        $category = \App\Models\Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Kategoria usunięta pomyślnie'
        ]);
    }
}
