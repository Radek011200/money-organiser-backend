<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Expense;

class ExpenseApiTest extends TestCase
{
    use RefreshDatabase; // Automatyczne czyszczenie bazy danych dla każdego testu

    /**
     * Testowanie pobierania listy wydatków.
     *
     * @return void
     */
    public function test_it_lists_all_expenses(): void
    {
        // Tworzymy kilka przykładowych rekordów w bazie danych
        Expense::factory()->count(3)->create();

        // Wysyłamy żądanie GET do /api/expenses
        $response = $this->getJson('/api/expenses');

        // Sprawdzamy, czy odpowiedź HTTP ma kod 200 (OK)
        $response->assertStatus(200);

        // Sprawdzamy strukturę odpowiedzi
        $response->assertJsonStructure([
            'expenses' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'category',
                    'date',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

        // Sprawdzamy, czy odpowiedź zawiera 3 rekordy
        $response->assertJsonCount(3, 'expenses');
    }

    /**
     * Testowanie pobierania pojedynczego wydatku.
     *
     * @return void
     */
    public function test_it_shows_a_single_expense(): void
    {
        // Tworzymy przykładowy rekord w bazie danych
        $expense = Expense::factory()->create();

        // Wysyłamy żądanie GET do /api/expenses/{id}
        $response = $this->getJson("/api/expenses/{$expense->id}");

        // Sprawdzamy, czy odpowiedź HTTP ma kod 200 (OK)
        $response->assertStatus(200);

        // Sprawdzamy strukturę odpowiedzi
        $response->assertJsonStructure([
            'expense' => [
                'id',
                'name',
                'amount',
                'category',
                'date',
                'created_at',
                'updated_at',
            ]
        ]);

        // Sprawdzamy, czy odpowiedź zawiera dane zgodne z utworzonym rekordem
        $response->assertJsonPath('expense.id', $expense->id);
    }

    /**
     * Testowanie dodawania nowego wydatku.
     *
     * @return void
     */
    public function test_it_creates_a_new_expense(): void
    {
        // Dane, które będą przesłane w żądaniu POST
        $payload = [
            'name' => 'Pizza',
            'amount' => 45.50,
            'category' => 'Food',
            'date' => '2023-10-28',
        ];

        // Wysyłamy żądanie POST do /api/expenses
        $response = $this->postJson('/api/expenses', $payload);

        // Sprawdzamy, czy odpowiedź HTTP ma kod 201 (utworzono)
        $response->assertStatus(201);

        // Sprawdzamy strukturę odpowiedzi (czy zawiera klucze JSON)
        $response->assertJsonStructure([
            'message',
            'expense' => [
                'id',
                'name',
                'amount',
                'category',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);

        // Sprawdzamy, czy rekord został zapisany w bazie danych
        $this->assertDatabaseHas('expenses', $payload);
    }

    public function test_it_requires_all_fields(): void
    {
        // Wysyłamy żądanie z pustymi danymi
        $response = $this->postJson('/api/expenses', []);

        // Sprawdzamy, czy odpowiedź HTTP ma kod 422 (błąd walidacji)
        $response->assertStatus(422);

        // Sprawdzamy, czy odpowiedź zawiera błędy walidacji
        $response->assertJsonValidationErrors(['name', 'amount', 'date']);
    }


    public function test_it_updates_an_expense(): void
    {
        // Tworzymy przykładowy rekord w bazie danych
        $expense = Expense::factory()->create([
            'name' => 'Pizza',
            'amount' => 45.50,
            'category' => 'Food',
            'date' => '2023-10-28',
        ]);

        // Dane dla aktualizacji
        $updatedData = [
            'name' => 'Burger',
            'amount' => 55.00,
        ];

        // Wysyłamy żądanie PUT
        $response = $this->putJson("/api/expenses/{$expense->id}", $updatedData);

        // Sprawdzamy, czy odpowiedź HTTP ma kod 200 (OK)
        $response->assertStatus(200);

        // Sprawdzamy, czy rekord został zaktualizowany
        $this->assertDatabaseHas('expenses', array_merge(['id' => $expense->id], $updatedData));
    }

    /**
     * Testowanie walidacji przy aktualizacji wydatku.
     *
     * @return void
     */
    public function test_it_validates_expense_update(): void
    {
        // Tworzymy przykładowy rekord w bazie danych
        $expense = Expense::factory()->create();

        // Dane z nieprawidłową wartością (ujemna kwota)
        $invalidData = [
            'amount' => -10.00,
        ];

        // Wysyłamy żądanie PUT
        $response = $this->putJson("/api/expenses/{$expense->id}", $invalidData);

        // Sprawdzamy, czy odpowiedź HTTP ma kod 422 (błąd walidacji)
        $response->assertStatus(422);

        // Sprawdzamy, czy odpowiedź zawiera błędy walidacji dla pola amount
        $response->assertJsonValidationErrors(['amount']);
    }

    public function test_it_deletes_an_expense(): void
    {
        // Tworzymy przykładowy rekord w bazie danych
        $expense = Expense::factory()->create();

        // Wysyłamy żądanie DELETE
        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        // Sprawdzamy, czy odpowiedź HTTP ma kod 200 (OK)
        $response->assertStatus(200);

        // Sprawdzamy, czy rekord został usunięty z bazy
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    /**
     * Testowanie próby usunięcia nieistniejącego wydatku.
     *
     * @return void
     */
    public function test_it_returns_404_when_deleting_nonexistent_expense(): void
    {
        // Wysyłamy żądanie DELETE dla nieistniejącego ID
        $response = $this->deleteJson("/api/expenses/999");

        // Sprawdzamy, czy odpowiedź HTTP ma kod 404 (Not Found)
        $response->assertStatus(404);
    }
}
