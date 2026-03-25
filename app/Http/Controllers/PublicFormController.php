<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    /**
     * Handle a form submission from the frontend.
     */
    public function submit(Request $request, Form $form)
    {
        if ($form->status !== 'published') {
            return redirect()->back()->with('error', 'Form is not active.');
        }

        if ($request->filled('website')) {
            Log::info('Spam detected in form submission', ['form_id' => $form->id, 'ip' => $request->ip()]);
            return redirect()->back()->with('success', 'Thank you for your submission!');
        }

        $rules = [];
        $input = $request->except(['_token', '_method', 'website']);

        if (is_array($form->content)) {
            foreach ($form->content as $field) {
                $stableKey = $this->resolveFieldKey($field);
                if (!$stableKey) {
                    continue;
                }

                $legacyKey = $this->resolveLegacyLabelKey($field['label'] ?? null);
                if ($legacyKey && !array_key_exists($stableKey, $input) && array_key_exists($legacyKey, $input)) {
                    $input[$stableKey] = $input[$legacyKey];
                }

                $fieldRules = [!empty($field['required']) ? 'required' : 'nullable'];

                if (isset($field['type'])) {
                    if ($field['type'] === 'email') {
                        $fieldRules[] = 'email';
                    } elseif ($field['type'] === 'text') {
                        $fieldRules[] = 'string|max:500';
                    } elseif ($field['type'] === 'tel') {
                        $fieldRules[] = 'string|max:30';
                    }
                }

                $rules[$stableKey] = implode('|', $fieldRules);
            }
        }

        $data = count($rules) > 0
            ? Validator::make($input, $rules)->validate()
            : $input;

        $idempotencyKey = $this->resolveIdempotencyKey($request);

        if ($idempotencyKey !== null) {
            $duplicate = FormSubmission::query()
                ->where('form_id', $form->id)
                ->where('idempotency_key', $idempotencyKey)
                ->exists();

            if ($duplicate) {
                return redirect()->back()->with('success', $this->resolveSuccessMessage($form));
            }
        }

        try {
            FormSubmission::create([
                'form_id' => $form->id,
                'idempotency_key' => $idempotencyKey,
                'data' => $data,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (QueryException $e) {
            if (!$this->isIdempotencyConflict($e, $idempotencyKey)) {
                Log::error('Error saving form submission: ' . $e->getMessage());
                return redirect()->back()->with('error', 'There was an error sending your message. Please try again later.');
            }
        } catch (\Exception $e) {
            Log::error('Error saving form submission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error sending your message. Please try again later.');
        }

        return redirect()->back()->with('success', $this->resolveSuccessMessage($form));
    }

    private function resolveSuccessMessage(Form $form): string
    {
        $successMessage = $form->settings['success_message'] ?? 'Thank you! Your message has been sent.';

        if (is_array($successMessage)) {
            $locale = app()->getLocale();
            $fallbackLocale = (string) config('app.fallback_locale', $locale);
            $successMessage = $successMessage[$locale] ?? $successMessage[$fallbackLocale] ?? collect($successMessage)->first() ?? 'Sent!';
        }

        return (string) $successMessage;
    }

    private function resolveIdempotencyKey(Request $request): ?string
    {
        $raw = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');
        if (!is_string($raw)) {
            return null;
        }

        $trimmed = trim($raw);
        if ($trimmed === '') {
            return null;
        }

        return hash('sha256', $trimmed);
    }

    private function isIdempotencyConflict(QueryException $exception, ?string $idempotencyKey): bool
    {
        if ($idempotencyKey === null) {
            return false;
        }

        $sqlState = (string) ($exception->errorInfo[0] ?? '');
        return in_array($sqlState, ['23000', '23505'], true);
    }

    private function resolveFieldKey(array $field): ?string
    {
        if (($field['type'] ?? null) === 'radio' && !empty($field['group'])) {
            return 'group_' . Str::slug((string) $field['group'], '_');
        }

        if (!empty($field['id'])) {
            return 'field_' . Str::slug((string) $field['id'], '_');
        }

        if (!empty($field['name'])) {
            return 'field_' . Str::slug((string) $field['name'], '_');
        }

        $legacyLabel = $this->resolveLegacyLabelKey($field['label'] ?? null);
        if ($legacyLabel) {
            return 'field_' . Str::slug($legacyLabel, '_');
        }

        return null;
    }

    private function resolveLegacyLabelKey($label): ?string
    {
        if (!$label) {
            return null;
        }

        if (is_array($label)) {
            return $label[app()->getLocale()] ?? collect($label)->first();
        }

        return __(is_string($label) ? $label : '');
    }
}
