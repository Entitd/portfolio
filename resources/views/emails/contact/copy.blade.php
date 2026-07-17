<x-mail::message>
# Introduction

Здравствуйте, {{ $contact->name}}
Получили Вашу заявку и скоро свяжемся.

Ваш коментарий: {{ $contact->comment }}
Номер заявки: {{ $contact->request_id }}
<x-mail::button :url="''">
Button Text
</x-mail::button>

Спасибо,<br>
{{ config('app.name') }}
</x-mail::message>
