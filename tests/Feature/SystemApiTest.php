<?php

use App\Models\Contact;
use Illuminate\Support\Str;

test('health endpoint reports application and database status', function (): void {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertHeader('X-Request-Id')
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('services.application', 'ok')
        ->assertJsonPath('services.database', 'ok')
        ->assertJsonStructure(['timestamp']);
});

test('metrics endpoint returns the number of contacts', function (): void {
    foreach (range(1, 2) as $index) {
        Contact::query()->create([
            'name' => "Пользователь {$index}",
            'email' => "user{$index}@example.com",
            'phone' => '+79991234567',
            'comment' => 'Тестовое обращение.',
            'request_id' => (string) Str::uuid(),
        ]);
    }

    $this->getJson('/api/metrics')
        ->assertOk()
        ->assertJsonPath('data.metrics.total', 2)
        ->assertJsonStructure(['timestamp']);
});

test('unknown api route returns a json 404 response', function (): void {
    $this->getJson('/api/missing')
        ->assertNotFound()
        ->assertJsonPath('message', 'Resource not found.')
        ->assertJsonPath('error.status', 404);
});
