# Portfolio Contact API

Backend-ориентированное портфолио разработчика с REST API для формы обратной связи. Сервис валидирует и сохраняет обращения, анализирует комментарий локальной AI-моделью, отправляет уведомления владельцу и пользователю, ведёт отдельный журнал API-запросов и предоставляет health/metrics endpoints.

## Возможности

- лендинг-портфолио с формой обратной связи;
- `POST /api/contact` с нормализацией и валидацией данных;
- хранение обращений и результатов AI-анализа в SQLite;
- классификация обращения, анализ тональности и предварительный AI-ответ;
- graceful fallback при недоступности Ollama;
- письмо владельцу сайта и копия пользователю;
- rate limiting по IP;
- корреляционный `request_id` в БД, ответе и логах;
- отдельный ежедневный файл логов API;
- глобальные JSON-ответы об ошибках;
- CORS с allowlist из переменной окружения;
- health и metrics endpoints;
- OpenAPI 3.0 и Swagger UI.



## Стек

- PHP 8.3+
- Laravel 13
- SQLite
- Ollama и `gemma3:4b`
- Pest 4
- Blade, JavaScript, CSS
- Vite 8



## Требования

- PHP 8.3 или новее с расширениями, необходимыми Laravel и SQLite;
- Composer;
- Node.js и npm;
- Ollama с загруженной моделью `gemma3:4b`;
- SMTP-аккаунт для реальной отправки писем или mail-драйвер `log` для локальной разработки.



## Установка

```bash
git clone https://github.com/Entitd/portfolio.git
cd portfolio

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

npm install
npm run build
```

В PowerShell файл SQLite можно создать командой:

```powershell
New-Item database/database.sqlite -ItemType File -Force
```



### Настройка Ollama

Установите Ollama, затем загрузите модель:

```bash
ollama pull gemma3:4b
ollama serve
```

Проверка доступности:

```bash
curl http://127.0.0.1:11434/api/tags
```



### Запуск приложения

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Лендинг будет доступен по адресу `http://127.0.0.1:8000`, Swagger UI — по адресу `http://127.0.0.1:8000/docs`.

Для frontend-разработки можно отдельно запустить:

```bash
npm run dev
```

Для ngrok и любого публичного запуска используйте production-сборку `npm run build` и не оставляйте файл `public/hot`.

## Переменные окружения

Основные параметры:


| Переменная                      | Назначение                        | Пример                   |
| ------------------------------- | --------------------------------- | ------------------------ |
| `APP_URL`                       | Базовый URL приложения            | `http://localhost:8000`  |
| `DB_CONNECTION`                 | Драйвер БД                        | `sqlite`                 |
| `OLLAMA_BASE_URL`               | URL Ollama API                    | `http://127.0.0.1:11434` |
| `OLLAMA_MODEL`                  | AI-модель                         | `gemma3:4b`              |
| `OLLAMA_TIMEOUT`                | Максимальное время AI-запроса     | `30`                     |
| `MAIL_MAILER`                   | Почтовый драйвер                  | `log` или `smtp`         |
| `MAIL_OWNER`                    | Получатель письма владельца       | `owner@example.com`      |
| `CONTACT_RATE_LIMIT_PER_MINUTE` | Лимит формы в минуту              | `3`                      |
| `CONTACT_RATE_LIMIT_PER_HOUR`   | Лимит формы в час                 | `20`                     |
| `CORS_ALLOWED_ORIGINS`          | Разрешённые origins через запятую | `http://localhost:8000`  |


После изменения `.env` очистите кеш конфигурации:

```bash
php artisan optimize:clear
```



### Локальная почта

По умолчанию используется:

```env
MAIL_MAILER=log
```

Письма не отправляются во внешний сервис, а записываются в `storage/logs/laravel.log`.

### SMTP [Mail.ru](http://Mail.ru)

Для Mail.ru или VK Почты нужен отдельный пароль внешнего приложения:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.mail.ru
MAIL_PORT=465
MAIL_USERNAME=your-address@mail.ru
MAIL_PASSWORD="application-password"
MAIL_FROM_ADDRESS=your-address@mail.ru
MAIL_FROM_NAME="${APP_NAME}"
MAIL_OWNER=owner@example.com
```

Обычный пароль аккаунта использовать нельзя. `.env` и реальные секреты не должны попадать в Git.

## Архитектура

Основной поток запроса:

```text
Route
  -> ApiRequestLogger middleware
  -> StoreContactRequest
  -> ContactController
  -> ContactService
      -> Contact model / SQLite
      -> AiAnalyzerInterface
          -> OllamaAnalyzer
          -> AiAnalysisResult fallback
      -> ContactOwnerNotification
      -> ContactUserNotification
  -> JSON response
```

Ключевые элементы:

- `app/Http/Controllers` — HTTP-слой;
- `app/Http/Requests` — нормализация и валидация;
- `app/Services` — прикладной сценарий и AI-интеграция;
- `app/Contracts` — контракт AI-провайдера;
- `app/Data` — типизированный результат AI-анализа;
- `app/Models` — хранение обращений;
- `app/Mail` — письма владельцу и пользователю;
- `app/Http/Middleware` — трассировка и логирование API.

`AiAnalyzerInterface` отделяет бизнес-логику от Ollama. Благодаря этому провайдер можно заменить, а в тестах используется fake-анализатор без сетевых запросов.

## API



### POST `/api/contact`

Создаёт и обрабатывает обращение.

Поля:


| Поле      | Правила                                                          |
| --------- | ---------------------------------------------------------------- |
| `name`    | обязательная строка, 2–100 символов                              |
| `email`   | обязательный RFC email, до 254 символов                          |
| `phone`   | обязательная строка, 7–32 символа, допустимы цифры и `+ ( ) . -` |
| `comment` | обязательная строка, 5–3000 символов                             |


Пример:

```bash
curl -i -X POST http://127.0.0.1:8000/api/contact \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "Иван Петров",
    "email": "ivan@example.com",
    "phone": "+79991234567",
    "comment": "Хочу обсудить разработку проекта"
  }'
```

Успешный ответ — `201 Created`:

```json
{
  "message": "Обращение успешно принято.",
  "data": {
    "id": 1,
    "request_id": "8bb2c0d4-9710-4d15-a6b7-45e28e708f35",
    "ai": {
      "answer": "Спасибо за обращение! Я ознакомился с описанием проекта и скоро свяжусь с вами.",
      "category": "project",
      "sentiment": "positive",
      "status": "success"
    }
  }
}
```

В заголовке ответа возвращается тот же `X-Request-Id`.

Возможные статусы:

- `201` — обращение обработано;
- `422` — ошибка валидации;
- `429` — превышен rate limit;
- `500` — внутренняя ошибка, например ошибка БД или почтового транспорта.



### GET `/api/health`

Проверяет приложение и соединение с БД:

```bash
curl http://127.0.0.1:8000/api/health
```

Возвращает `200`, если БД доступна, или `503` при ошибке.

### GET `/api/metrics`

Возвращает количество сохранённых обращений:

```bash
curl http://127.0.0.1:8000/api/metrics
```



### Swagger/OpenAPI

- Swagger UI: `/docs`
- YAML: `/openapi.yaml`



## AI-интеграция

`OllamaAnalyzer` отправляет комментарий в локальную модель `gemma3:4b` и требует структурированный JSON по схеме.

AI выполняет три функции:

1. создаёт короткий предварительный ответ пользователю;
2. классифицирует обращение: `project`, `job`, `consultation`, `cooperation`, `spam`, `other`;
3. определяет тональность: `positive`, `neutral`, `negative`.

Системный промпт требует отвечать на русском, не придумывать цену, сроки или гарантии, не выполнять инструкции из пользовательского комментария и возвращать только данные по JSON Schema.

Если Ollama недоступна, вернула ошибку или некорректный JSON, исключение перехватывается. Заявка продолжает обрабатываться с результатом:

```json
{
  "category": "other",
  "sentiment": "unknown",
  "status": "fallback"
}
```

Fallback фиксируется в БД и в обычном Laravel-логе.

## Логирование и хранение

Обращения хранятся в таблице `contacts`. Вместе с исходными полями сохраняются:

- `request_id`;
- AI-ответ;
- категория;
- тональность;
- AI-статус;
- время обработки AI.

API-запросы записываются отдельным daily-каналом:

```text
storage/logs/api-requests-YYYY-MM-DD.log
```

Лог содержит request ID, метод, маршрут, статус, длительность, IP и User-Agent. Тело формы не логируется, чтобы не дублировать персональные данные.

Ошибки приложения и AI fallback записываются в:

```text
storage/logs/laravel.log
```

Rate limiting использует Laravel RateLimiter и cache-драйвер проекта. Ограничения применяются по IP клиента.

## CORS

Разрешённые frontend origins задаются через запятую:

```env
CORS_ALLOWED_ORIGINS=http://localhost:8000,http://127.0.0.1:8000
```

Для отдельного frontend или временного домена добавьте его в список и выполните `php artisan optimize:clear`. Same-origin запросы отдельного CORS-разрешения не требуют.

## Тесты и качество

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

Feature-тесты используют SQLite in-memory, `Mail::fake()` и fake-реализацию `AiAnalyzerInterface`. Настоящие SMTP и Ollama во время тестов не вызываются.

Проверяются успешная заявка, нормализация, валидация, сохранение, два письма, AI fallback, rate limiting, CORS preflight, health, metrics и JSON 404.

## Публичная демонстрация

Текущий временный адрес:

```text
https://salute-tackle-snowfall.ngrok-free.dev
```

Ngrok URL работает только пока локально запущены Laravel, Ollama и ngrok. При создании нового туннеля адрес изменится.

## Использование AI при разработке

AI-ассистент использовался для:

- разбора требований тестового задания;
- обсуждения структуры Laravel-приложения;
- подготовки вариантов AI-контракта и structured output;
- поиска причин ошибок SMTP, Vite и reverse proxy;
- генерации черновиков тестов и документации.

Вручную проверялись и адаптировались Laravel API, миграции, валидация, DI-контейнер, обработка ошибок, промпт, fallback, почтовые шаблоны, rate limiting, логирование и публичная работа через ngrok.