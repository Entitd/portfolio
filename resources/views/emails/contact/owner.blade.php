<x-mail::message>

Заявка № {{ $contact->request_id }}
Имя {{ $contact->name }}
Телефон {{ $contact->phone }}
Email {{ $contact->email }}
Коментарий {{ $contact->comment }}

Спасибо,<br>
{{ config('app.name') }}
</x-mail::message>
