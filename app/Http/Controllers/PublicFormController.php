<?php
  
  namespace App\Http\Controllers;
  
  use App\Models\Form;
  use App\Models\FormSubmission;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Log;
  
  class PublicFormController extends Controller
  {
      /**
       * Handle a form submission from the frontend.
       */
      public function submit(Request $request, Form $form)
      {
          // 1. Basic check
          if ($form->status !== 'published') {
              return redirect()->back()->with('error', 'Form is not active.');
          }
  
          // 2. Data extraction
          // We expect data under 'data' key or as flat request
          $data = $request->except(['_token', '_method']);
  
          // 3. Optional: Extract honeypot (website field if present)
          if ($request->has('website') && !empty($request->website)) {
              // Potential spam
              Log::info('Spam detected in form submission', ['form_id' => $form->id, 'ip' => $request->ip()]);
              return redirect()->back()->with('success', 'Thank you for your submission!');
          }
  
          // 4. Save submission
          try {
              FormSubmission::create([
                  'form_id' => $form->id,
                  'data' => $data,
                  'ip_address' => $request->ip(),
                  'user_agent' => $request->userAgent(),
              ]);
  
              $successMessage = $form->settings['success_message'] ?? 'Thank you! Your message has been sent.';
              
              // Localize if it's a translation object
              if (is_array($successMessage)) {
                  $locale = app()->getLocale();
                  $successMessage = $successMessage[$locale] ?? $successMessage['pl'] ?? 'Sent!';
              }
  
              return redirect()->back()->with('success', $successMessage);
          } catch (\Exception $e) {
              Log::error('Error saving form submission: ' . $e->getMessage());
              return redirect()->back()->with('error', 'There was an error sending your message. Please try again later.');
          }
      }
  }
  
