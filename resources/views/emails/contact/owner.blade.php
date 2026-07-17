<x-mail::message>

Заявка № {{ $contact->request_id }}
Имя {{ $contact->name }}
Телефон {{ $contact->phone }}
Email {{ $contact->email }}
Коментарий {{ $contact->comment }}

Категория: {{ $contact->ai_category }}
Тональность: {{ $contact->ai_sentiment }}
Статус AI: {{ $contact->ai_status }}
Ответ AI: {{ $contact->ai_answer }}

Спасибо,<br>
{{ config('app.name') }}
</x-mail::message>
