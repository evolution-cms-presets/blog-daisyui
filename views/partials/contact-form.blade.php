@php
  $contact = $contact ?? [];
  $old = $old ?? [];
  $fieldErrors = $fieldErrors ?? [];
  $status = (string)($contact['status'] ?? '');
  $action = (string)($contact['action'] ?? '/contact-submit');
  $redirectTo = (string)($contact['pageUrl'] ?? request()->getRequestUri());

  if (empty($old)) {
      $old = [
          'name' => old('name', ''),
          'email' => old('email', ''),
          'message' => old('message', ''),
      ];
  }

  if (empty($fieldErrors) && isset($errors) && method_exists($errors, 'toArray')) {
      $fieldErrors = $errors->toArray();
  }

  $errorFor = static function (string $field) use ($fieldErrors): string {
      $messages = $fieldErrors[$field] ?? [];
      return is_array($messages) && !empty($messages) ? (string)$messages[0] : '';
  };

  $valueFor = static function (string $field) use ($old): string {
      return (string)($old[$field] ?? '');
  };
@endphp

@if($status === 'invalid' || !empty($fieldErrors))
  <div class="alert alert-error mb-5">
    <span>Check the form fields and try again.</span>
  </div>
@endif

<form data-contact-form class="space-y-5" action="{{ $action }}" method="post" novalidate>
  @csrf
  <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
  <div class="hidden" aria-hidden="true">
    <label>
      Website
      <input type="text" name="company_url" tabindex="-1" autocomplete="off">
    </label>
  </div>

  <div class="form-control w-full">
    <label class="label" for="contact-name">
      <span class="label-text font-medium">Name</span>
    </label>
    <input
      id="contact-name"
      class="input input-bordered w-full {{ $errorFor('name') !== '' ? 'input-error' : '' }}"
      type="text"
      name="name"
      value="{{ $valueFor('name') }}"
      autocomplete="name"
      aria-invalid="{{ $errorFor('name') !== '' ? 'true' : 'false' }}"
    >
    @if($errorFor('name') !== '')
      <div class="label">
        <span class="label-text-alt text-error">{{ $errorFor('name') }}</span>
      </div>
    @endif
  </div>

  <div class="form-control w-full">
    <label class="label" for="contact-email">
      <span class="label-text font-medium">Email</span>
    </label>
    <input
      id="contact-email"
      class="input input-bordered w-full {{ $errorFor('email') !== '' ? 'input-error' : '' }}"
      type="email"
      name="email"
      value="{{ $valueFor('email') }}"
      autocomplete="email"
      aria-invalid="{{ $errorFor('email') !== '' ? 'true' : 'false' }}"
    >
    @if($errorFor('email') !== '')
      <div class="label">
        <span class="label-text-alt text-error">{{ $errorFor('email') }}</span>
      </div>
    @endif
  </div>

  <div class="form-control w-full">
    <label class="label" for="contact-message">
      <span class="label-text font-medium">Message</span>
    </label>
    <textarea
      id="contact-message"
      class="textarea textarea-bordered min-h-36 w-full {{ $errorFor('message') !== '' ? 'textarea-error' : '' }}"
      name="message"
      aria-invalid="{{ $errorFor('message') !== '' ? 'true' : 'false' }}"
    >{{ $valueFor('message') }}</textarea>
    @if($errorFor('message') !== '')
      <div class="label">
        <span class="label-text-alt text-error">{{ $errorFor('message') }}</span>
      </div>
    @endif
  </div>

  <div class="pt-1">
    <button class="btn btn-primary w-full sm:w-auto" type="submit">Send message</button>
  </div>
</form>
