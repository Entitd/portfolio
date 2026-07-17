<x-mail::message>

Здравствуйте, {{ $contact->name}}
Получили Вашу заявку и скоро свяжемся.

Ваш коментарий: {{ $contact->comment }}
Номер заявки: {{ $contact->request_id }}

@if ($contact->ai_answer)
Предварительный автоматический ответ:

{{ $contact->ai_answer }}
@endif

Спасибо,<br>
{{ config('app.name') }}
</x-mail::message>
