# Modul formularzy (stan kodu)

## Co dziala

- CRUD formularzy w panelu admin (`/{locale}/admin/forms`).
- Edycja formularza przez Block Builder (JSON w `content`).
- Ustawienia formularza w `settings`.
- Preview publiczne: `/{locale}/forms/{id}/preview`.
- **System FormSubmission**: Automatyczne zapisywanie danych z formularzy runtime do bazy (`form_submissions`).
- **Form Block**: Specjalny kontener w builderze, który grupuje pola wejściowe i obsługuje wysyłkę.

## Schema

### Tabela `forms` (Definicja formularza)

- `status`: draft/published
- `title`: (json translatable)
- `content`: (json) Definicja bloków buildera
- `settings`: (json) Konfiguracja (np. email powiadomienia)

### Tabela `form_submissions` (Dane od użytkowników)

- `form_id`: FK do `forms`
- `data`: (json) Wszystkie pola przesłane przez użytkownika
- `ip_address`: string
- `user_agent`: text
- `is_read`: boolean
- `created_at`: data zgłoszenia

## Integracja Runtime

W `DynamicBlock.vue` blok typu `form` działa jako `provide('runtimeFormValues')`, a bloki wejściowe (np. `text_input`) używają `inject` do synchronizacji wartości. Dane są przesyłane do `POST /forms/{id}/submit`.