<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }
        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 720px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 8px 20px rgba(31, 41, 55, 0.08);
        }
        h1 {
            margin: 0 0 12px 0;
            font-size: 30px;
            line-height: 1.2;
        }
        p {
            margin: 0;
            font-size: 16px;
            line-height: 1.55;
            color: #4b5563;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 18px;
        }
        .full {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 6px;
            color: #6b7280;
        }
        input, select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .actions {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }
        button {
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            font-weight: 700;
            padding: 10px 14px;
            cursor: pointer;
        }
        .alert {
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 14px;
            font-size: 14px;
        }
        .alert.success {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
        }
        .alert.error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        .muted {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="card" role="main" aria-labelledby="install-heading">
            <h1 id="install-heading">{{ $headline }}</h1>
            <p>{{ $description }}</p>

            <x-install-alert type="success" :message="session('success')" />
            <x-install-alert type="error" :message="$errors->first('database')" />
            <x-install-alert type="error" :message="$errors->first('language')" />
            <x-install-alert type="error" :message="$errors->first('finalize')" />

            <form method="post" action="{{ route('install.database.validate', ['locale' => app()->getLocale()]) }}">
                @csrf

                @php
                    $connection = old('connection', $database['connection'] ?? 'sqlite');
                @endphp

                <div class="grid">
                    <div class="full">
                        <label for="connection">Database Driver</label>
                        <select id="connection" name="connection">
                            <option value="sqlite" @selected($connection === 'sqlite')>SQLite</option>
                            <option value="mysql" @selected($connection === 'mysql')>MySQL</option>
                            <option value="pgsql" @selected($connection === 'pgsql')>PostgreSQL</option>
                        </select>
                    </div>

                    <div id="row-host">
                        <label for="host">Host</label>
                        <input id="host" name="host" type="text" value="{{ old('host', $database['host'] ?? '127.0.0.1') }}">
                    </div>
                    <div id="row-port">
                        <label for="port">Port</label>
                        <input id="port" name="port" type="text" value="{{ old('port', $database['port'] ?? '3306') }}">
                    </div>
                    <div>
                        <label for="database">Database Name</label>
                        <input id="database" name="database" type="text" value="{{ old('database', $database['database'] ?? '') }}">
                    </div>
                    <div>
                        <label for="username">Database User</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $database['username'] ?? '') }}">
                    </div>
                    <div class="full">
                        <label for="password">Database Password</label>
                        <input id="password" name="password" type="password" value="{{ old('password', $database['password'] ?? '') }}">
                    </div>

                    <div class="full" id="row-sqlite-path">
                        <label for="sqlite_path">SQLite File Path</label>
                        <input id="sqlite_path" name="sqlite_path" type="text" value="{{ old('sqlite_path', $database['sqlite_path'] ?? database_path('database.sqlite')) }}">
                        <p class="muted">Used only when driver is SQLite.</p>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit">Validate Database Connection</button>
                </div>
            </form>

            @if ($databaseValidated)
                <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">
                <h2 style="margin: 0 0 6px; font-size: 22px;">Step 2: Default Language</h2>
                <p class="muted">Choose the initial locale for admin and content defaults.</p>

                <form method="post" action="{{ route('install.language.store', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <div class="grid">
                        <div class="full">
                            <label for="language">Default Language</label>
                            <select id="language" name="language" required>
                                @foreach ($availableLocales as $locale)
                                    <option value="{{ $locale }}" @selected(old('language', $selectedLocale) === $locale)>
                                        {{ strtoupper($locale) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit">Save Language</button>
                    </div>
                </form>

                @if ($languageSelected)
                    <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">
                    <h2 style="margin: 0 0 6px; font-size: 22px;">Step 3: Finalize Installation</h2>
                    <p class="muted">Run migrations, seeds and lock installer.</p>

                    <form method="post" action="{{ route('install.finalize', ['locale' => app()->getLocale()]) }}">
                        @csrf
                        <div class="actions">
                            <button type="submit">Finalize Installation</button>
                        </div>
                    </form>
                @endif
            @endif
        </section>
    </main>
    <script>
        (function () {
            const connection = document.getElementById('connection');
            const rows = {
                host: document.getElementById('row-host'),
                port: document.getElementById('row-port'),
                sqlitePath: document.getElementById('row-sqlite-path'),
                database: document.getElementById('database').closest('div'),
                username: document.getElementById('username').closest('div'),
                password: document.getElementById('password').closest('div'),
            };

            const toggleFields = () => {
                const isSqlite = connection.value === 'sqlite';
                rows.host.style.display = isSqlite ? 'none' : 'block';
                rows.port.style.display = isSqlite ? 'none' : 'block';
                rows.database.style.display = isSqlite ? 'none' : 'block';
                rows.username.style.display = isSqlite ? 'none' : 'block';
                rows.password.style.display = isSqlite ? 'none' : 'block';
                rows.sqlitePath.style.display = isSqlite ? 'block' : 'none';
            };

            connection.addEventListener('change', toggleFields);
            toggleFields();
        })();
    </script>
</body>
</html>
