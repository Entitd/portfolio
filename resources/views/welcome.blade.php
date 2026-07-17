<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Резюме PHP / Backend-разработчика Утешева Амаля">

        <title>Утешев Амаль - PHP / Backend-разработчик</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portfolio-page">
        <main>
            <section class="hero-section">
                <div class="page-shell hero-grid">
                    <div class="hero-copy">
                        <p class="eyebrow">PHP / Backend-разработчик</p>
                        <h1>Утешев Амаль</h1>
                        <p class="hero-lead">
                            PHP-разработчик с коммерческим опытом поддержки и доработки проектов на OpenCart и SugarCRM.
                            Исправляю ошибки, развиваю серверную бизнес-логику, работаю с существующим кодом и интеграциями по API.
                        </p>

                        <div class="contact-strip" aria-label="Контакты">
                            <a href="tel:+79270707123">+7 927 070-71-23</a>
                            <a href="mailto:amaluteshev92@gmail.com">amaluteshev92@gmail.com</a>
                            <a href="https://t.me/ENTITD" target="_blank" rel="noreferrer">Telegram: @ENTITD</a>
                            <a href="https://github.com/Entitd" target="_blank" rel="noreferrer">GitHub</a>
                        </div>
                    </div>

                    <aside class="hero-panel" aria-label="Кратко">
                        <div class="terminal-card" aria-hidden="true">
                            <div class="terminal-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <pre><code>Developer::profile([
  'stack' => 'PHP, Laravel, REST API',
  'db' => 'MySQL, PostgreSQL, SQLite',
  'work' => 'legacy, integrations, backend',
]);</code></pre>
                        </div>

                        <dl class="quick-facts">
                            <div>
                                <dt>Локация</dt>
                                <dd>Волгоград, удалённо</dd>
                            </div>
                            <div>
                                <dt>Фокус</dt>
                                <dd>Backend, API, интеграции</dd>
                            </div>
                            <div>
                                <dt>Статус</dt>
                                <dd>Открыт к PHP / Backend позиции</dd>
                            </div>
                        </dl>
                    </aside>
                </div>
            </section>

            <section class="page-shell content-grid" aria-label="Резюме и форма связи">
                <div class="resume-column">
                    <section class="resume-section">
                        <div class="section-heading">
                            <span>01</span>
                            <h2>Ключевые навыки</h2>
                        </div>
                        <div class="skills-grid">
                            <article>
                                <h3>Backend</h3>
                                <p>PHP 7/8, Laravel, ООП, MVC, REST API, JSON, Eloquent ORM, миграции, Blade</p>
                            </article>
                            <article>
                                <h3>Базы данных</h3>
                                <p>MySQL, PostgreSQL, SQLite, SQL, реляционная модель данных</p>
                            </article>
                            <article>
                                <h3>Инфраструктура</h3>
                                <p>Git, Linux, SSH, работа с логами, базовое развёртывание Nginx</p>
                            </article>
                            <article>
                                <h3>Redis</h3>
                                <p>Строки, множества, sorted sets, TTL и кеширование</p>
                            </article>
                            <article>
                                <h3>Frontend</h3>
                                <p>HTML5, CSS3, JavaScript, React, TypeScript, Bootstrap</p>
                            </article>
                            <article>
                                <h3>CMS / CRM</h3>
                                <p>OpenCart, SugarCRM, WordPress, Telegram API, API ФНС, маркетплейсы и небольшие интеграции с 1С</p>
                            </article>
                        </div>
                    </section>

                    <section class="resume-section">
                        <div class="section-heading">
                            <span>02</span>
                            <h2>Опыт работы</h2>
                        </div>
                        <article class="timeline-item">
                            <div class="timeline-meta">
                                <span>03.2025 - настоящее время</span>
                                <span>Волгоград / удалённо</span>
                            </div>
                            <h3>FullStack-разработчик</h3>
                            <ul>
                                <li>Поддерживал и дорабатывал проекты на PHP, OpenCart и SugarCRM: исправлял ошибки, изменял существующий функционал и добавлял серверную бизнес-логику.</li>
                                <li>Разбирался в legacy-коде, диагностировал причины сбоев и вносил изменения без нарушения текущих бизнес-процессов.</li>
                                <li>Интегрировал внешние сервисы по API, обрабатывал запросы и ответы в JSON, настраивал передачу и обработку данных.</li>
                                <li>Работал с API маркетплейсов Ozon, Яндекс, Wildberries и AliExpress, API ФНС и небольшими интеграциями с 1С.</li>
                                <li>Использовал MySQL, Git и Linux в рабочем окружении; работал с файлами, логами и серверной частью приложений.</li>
                            </ul>
                        </article>
                    </section>

                    <section class="resume-section">
                        <div class="section-heading">
                            <span>03</span>
                            <h2>Проекты</h2>
                        </div>
                        <div class="project-list">
                            <article>
                                <h3>Сервисное веб-приложение</h3>
                                <p class="stack-line">Laravel, React, TypeScript, Inertia, MySQL</p>
                                <p>Разрабатывал backend- и frontend-части приложения: маршруты, контроллеры, формы, бизнес-логику и обмен данными между клиентом и сервером.</p>
                            </article>
                            <article>
                                <h3>Redis Lab</h3>
                                <p class="stack-line">PHP / Laravel, Redis</p>
                                <p>Создал учебный стенд для практики структур данных Redis, TTL, кеширования, уникальных значений и рейтингов на sorted sets.</p>
                            </article>
                            <article>
                                <h3>Анализатор bib-файлов</h3>
                                <p class="stack-line">Laravel, MySQL / SQLite</p>
                                <p>Реализовал обработку и проверку библиографических записей, серверную логику, работу со структурированными данными и вывод результатов.</p>
                            </article>
                        </div>
                    </section>

                    <section class="resume-section two-column-section">
                        <div>
                            <div class="section-heading">
                                <span>04</span>
                                <h2>Образование</h2>
                            </div>
                            <article class="compact-block">
                                <h3>Волгоградский государственный университет</h3>
                                <p>Магистратура, «Информатика и вычислительная техника» - 2025-2027, обучаюсь</p>
                                <p>Бакалавриат, «Программная инженерия» - 2021-2025</p>
                            </article>
                        </div>
                        <div>
                            <div class="section-heading">
                                <span>05</span>
                                <h2>Дополнительно</h2>
                            </div>
                            <article class="compact-block">
                                <p>Готов предоставить примеры кода и доступ к приватным репозиториям по запросу.</p>
                            </article>
                        </div>
                    </section>
                </div>

                <aside class="contact-column" id="contact">
                    <form class="contact-form" data-contact-form>
                        <div class="form-header">
                            <p class="eyebrow">Связаться</p>
                            <h2>Обсудим проект</h2>
                        </div>

                        <label>
                            <span>Имя</span>
                            <input type="text" name="name" autocomplete="name" required minlength="2" maxlength="100" placeholder="Иван Петров">
                        </label>

                        <label>
                            <span>Телефон</span>
                            <input type="tel" name="phone" autocomplete="tel" required minlength="7" maxlength="32" placeholder="+7 999 123-45-67">
                        </label>

                        <label>
                            <span>Email</span>
                            <input type="email" name="email" autocomplete="email" required maxlength="254" placeholder="mail@example.com">
                        </label>

                        <label>
                            <span>Комментарий</span>
                            <textarea name="comment" required minlength="5" maxlength="3000" rows="6" placeholder="Коротко опишите задачу"></textarea>
                        </label>

                        <button type="submit" class="submit-button">
                            <span>Отправить заявку</span>
                            <span aria-hidden="true">→</span>
                        </button>

                        <div class="form-status" data-form-status role="status" aria-live="polite"></div>
                    </form>
                </aside>
            </section>
        </main>
    </body>
</html>
