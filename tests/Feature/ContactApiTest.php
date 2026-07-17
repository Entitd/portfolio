<?php

use App\Contracts\AiAnalyzerInterface;
use App\Data\AiAnalysisResult;
use App\Mail\ContactOwnerNotification;
use App\Mail\ContactUserNotification;
use App\Models\Contact;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

beforeEach(function (): void {
    config()->set('contact.owner_email', 'owner@example.com');

    Mail::fake();

    $this->app->instance(AiAnalyzerInterface::class, new class implements AiAnalyzerInterface
    {
        public function analyze(string $comment): AiAnalysisResult
        {
            return new AiAnalysisResult(
                answer: 'Спасибо! Я получил описание проекта и скоро свяжусь с вами.',
                category: 'project',
                sentiment: 'positive',
                status: 'success',
            );
        }
    });
});

test('contact endpoint creates a request and sends both emails', function (): void {
    $response = $this->postJson('/api/contact', [
        'name' => '  Иван   Петров  ',
        'email' => 'IVAN@example.com',
        'phone' => '+7 (999) 123-45-67',
        'comment' => 'Хочу обсудить разработку нового проекта.',
    ]);

    $response
        ->assertCreated()
        ->assertHeader('X-Request-Id')
        ->assertJsonPath('message', 'Обращение успешно принято.')
        ->assertJsonPath('data.ai.category', 'project')
        ->assertJsonPath('data.ai.sentiment', 'positive')
        ->assertJsonPath('data.ai.status', 'success');

    $requestId = $response->json('data.request_id');

    expect(Str::isUuid($requestId))->toBeTrue();
    expect($response->headers->get('X-Request-Id'))->toBe($requestId);

    $this->assertDatabaseHas('contacts', [
        'request_id' => $requestId,
        'name' => 'Иван Петров',
        'email' => 'ivan@example.com',
        'ai_category' => 'project',
        'ai_sentiment' => 'positive',
        'ai_status' => 'success',
    ]);

    $contact = Contact::query()->where('request_id', $requestId)->firstOrFail();

    expect($contact->ai_processed_at)->not->toBeNull();

    Mail::assertSent(
        ContactOwnerNotification::class,
        fn (ContactOwnerNotification $mail): bool => $mail->hasTo('owner@example.com')
            && $mail->contact->is($contact),
    );

    Mail::assertSent(
        ContactUserNotification::class,
        fn (ContactUserNotification $mail): bool => $mail->hasTo('ivan@example.com')
            && $mail->contact->is($contact),
    );

    Mail::assertSentCount(2);
});

test('contact endpoint returns validation errors and creates nothing', function (): void {
    $response = $this->postJson('/api/contact', [
        'name' => 'A',
        'email' => 'not-an-email',
        'phone' => 'abc',
        'comment' => 'no',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Validation failed.')
        ->assertJsonValidationErrors(['name', 'email', 'phone', 'comment']);

    $this->assertDatabaseCount('contacts', 0);
    Mail::assertNothingSent();
});

test('contact endpoint continues with fallback when ai is unavailable', function (): void {
    $this->app->instance(AiAnalyzerInterface::class, new class implements AiAnalyzerInterface
    {
        public function analyze(string $comment): AiAnalysisResult
        {
            return AiAnalysisResult::fallback();
        }
    });

    $response = $this->postJson('/api/contact', [
        'name' => 'Анна Смирнова',
        'email' => 'anna@example.com',
        'phone' => '+79991234567',
        'comment' => 'Нужна консультация по интеграции API.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.ai.category', 'other')
        ->assertJsonPath('data.ai.sentiment', 'unknown')
        ->assertJsonPath('data.ai.status', 'fallback');

    $this->assertDatabaseHas('contacts', [
        'request_id' => $response->json('data.request_id'),
        'ai_status' => 'fallback',
    ]);

    Mail::assertSentCount(2);
});

test('contact endpoint is rate limited by client ip', function (): void {
    RateLimiter::for('contact', fn (Request $request): Limit => Limit::perMinute(3)->by($request->ip()));

    $payload = [
        'name' => 'Иван Петров',
        'email' => 'ivan@example.com',
        'phone' => '+79991234567',
        'comment' => 'Хочу обсудить разработку проекта.',
    ];

    foreach (range(1, 3) as $attempt) {
        $this->postJson('/api/contact', $payload)->assertCreated();
    }

    $this->postJson('/api/contact', $payload)
        ->assertTooManyRequests()
        ->assertHeader('Retry-After')
        ->assertJsonPath('message', 'Too many requests.');

    $this->assertDatabaseCount('contacts', 3);
    Mail::assertSentCount(6);
});

test('cors preflight allows a configured frontend origin', function (): void {
    $this->withHeaders([
        'Origin' => 'http://localhost:8000',
        'Access-Control-Request-Method' => 'POST',
        'Access-Control-Request-Headers' => 'content-type',
    ])->options('/api/contact')
        ->assertNoContent()
        ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
        ->assertHeader('Access-Control-Allow-Methods');
});
