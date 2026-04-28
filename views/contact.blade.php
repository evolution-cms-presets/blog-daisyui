@extends('layouts.base')

@section('title', $documentObject['longtitle'] ?? $documentObject['pagetitle'] ?? evo()->getConfig('site_name'))

@section('content')
  <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(320px,420px)] lg:items-start">
    @if(!empty($documentObject['content']))
      <article class="content-flow">
        {!! $documentObject['content'] !!}
      </article>
    @endif

    <section id="contact-form" class="rounded-lg border border-base-300 bg-base-100 p-5 shadow-sm" aria-label="Contact form">
      <div id="contactFormFrame">
        @if(in_array(($contact['status'] ?? ''), ['sent', 'logged'], true))
          @include('partials.contact-thanks', [
            'mailSent' => ($contact['status'] ?? '') === 'sent',
            'contactUrl' => $contact['pageUrl'] ?? request()->getRequestUri(),
          ])
        @else
          @include('partials.contact-form', [
            'contact' => $contact ?? [],
            'old' => [],
            'fieldErrors' => [],
          ])
        @endif
      </div>
    </section>
  </div>

  <script>
    (function () {
      var container = document.getElementById('contactFormFrame');
      if (!container) return;
      var initialFormHtml = container.innerHTML;

      container.addEventListener('click', function (event) {
        var reset = event.target.closest('[data-contact-reset]');
        if (!reset) return;

        event.preventDefault();
        container.innerHTML = initialFormHtml;

        var firstField = container.querySelector('input[name="name"]');
        if (firstField) {
          firstField.focus();
        }
      });

      container.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-contact-form]');
        if (!form) return;

        event.preventDefault();

        var submitButton = form.querySelector('[type="submit"]');
        if (submitButton) {
          submitButton.disabled = true;
          submitButton.classList.add('btn-disabled');
        }

        fetch(form.getAttribute('action'), {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: new FormData(form)
        })
          .then(function (response) {
            return response.json().then(function (payload) {
              return {
                ok: response.ok,
                payload: payload
              };
            });
          })
          .then(function (result) {
            if (result.payload && result.payload.output) {
              container.innerHTML = result.payload.output;
            }

            if (!result.ok) {
              var firstInvalid = container.querySelector('[aria-invalid="true"]');
              if (firstInvalid) {
                firstInvalid.focus();
              }
            }
          })
          .catch(function () {
            container.insertAdjacentHTML('afterbegin', '<div class="alert alert-error mb-4">The request could not be sent. Please try again.</div>');
          })
          .finally(function () {
            var freshButton = container.querySelector('[type="submit"]');
            if (freshButton) {
              freshButton.disabled = false;
              freshButton.classList.remove('btn-disabled');
            }
          });
      });
    })();
  </script>
@endsection
