{{-- App-wide modal dialogs replacing the browser's alert()/confirm().
     JS API:  uiAlert(message, {title, tone})            -> Promise<void>
              uiConfirm(message, {title, confirmText, cancelText, tone}) -> Promise<boolean>
     Markup:  data-confirm="Message" on a link, button or form (optional data-confirm-title, data-confirm-ok, data-confirm-tone="danger").
     window.alert is routed to uiAlert as a safety net. Include once per layout, before page scripts. --}}
@once
<style>
  .ui-dlg-wrap{position:fixed;inset:0;z-index:9999;display:flex;align-items:flex-end;justify-content:center;padding:16px;background:rgba(11,16,32,.45);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);opacity:0;transition:opacity .16s ease}
  .ui-dlg-wrap.is-in{opacity:1}
  @media (min-width:640px){.ui-dlg-wrap{align-items:center}}
  .ui-dlg{width:100%;max-width:420px;border-radius:16px;background:#ffffff;border:1px solid #e3ddcf;box-shadow:0 30px 80px -20px rgba(11,16,32,.35);color:#0b1020;font-family:Inter,system-ui,sans-serif;transform:translateY(12px);transition:transform .18s ease}
  .ui-dlg-wrap.is-in .ui-dlg{transform:none}
  .ui-dlg-body{display:flex;gap:14px;padding:22px 22px 8px}
  .ui-dlg-ico{flex:none;width:40px;height:40px;border-radius:12px;display:grid;place-items:center}
  .ui-dlg-ico svg{width:20px;height:20px}
  .ui-dlg-ico.info{background:#f6f0e1;color:#8f6f2f}
  .ui-dlg-ico.danger{background:#fbe9e9;color:#a32a2a}
  .ui-dlg-ico.success{background:#e6f4ec;color:#1f7a4d}
  .ui-dlg h3{margin:0;font:600 17px Inter,sans-serif;letter-spacing:-.01em;color:#0b1020}
  .ui-dlg p{margin:6px 0 0;font-size:14px;line-height:1.55;color:#3b4357;white-space:pre-line;word-break:break-word}
  .ui-dlg-actions{display:flex;gap:10px;justify-content:flex-end;padding:16px 22px 20px}
  .ui-dlg-actions button{min-width:96px;padding:10px 16px;border-radius:10px;font:600 14px Inter,sans-serif;cursor:pointer;border:1px solid transparent}
  .ui-dlg-cancel{background:#f8f7f3;color:#3b4357;border-color:#e3ddcf!important}
  .ui-dlg-cancel:hover{color:#0b1020}
  .ui-dlg-ok{background:#0b1020;color:#fff}
  .ui-dlg-ok:hover{background:#141c33}
  .ui-dlg-ok.danger{background:#a32a2a}.ui-dlg-ok.danger:hover{background:#8a2222}
  @media (max-width:639px){.ui-dlg-actions button{flex:1}}
  @media (prefers-reduced-motion:reduce){.ui-dlg-wrap,.ui-dlg{transition:none}}
</style>
<script>
(function () {
  if (window.uiConfirm) return;
  var ICONS = {
    info: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>',
    danger: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>',
    success: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>'
  };
  var queue = Promise.resolve();

  function open(opts) {
    return new Promise(function (resolve) {
      var lastFocus = document.activeElement;
      var tone = opts.tone || (opts.confirm ? 'info' : 'info');
      var wrap = document.createElement('div');
      wrap.className = 'ui-dlg-wrap';
      wrap.innerHTML =
        '<div class="ui-dlg" role="' + (opts.confirm ? 'alertdialog' : 'dialog') + '" aria-modal="true" aria-labelledby="uiDlgT" aria-describedby="uiDlgM">' +
          '<div class="ui-dlg-body"><span class="ui-dlg-ico ' + tone + '">' + (ICONS[tone] || ICONS.info) + '</span>' +
          '<div><h3 id="uiDlgT"></h3><p id="uiDlgM"></p></div></div>' +
          '<div class="ui-dlg-actions">' +
            (opts.confirm ? '<button type="button" class="ui-dlg-cancel"></button>' : '') +
            '<button type="button" class="ui-dlg-ok ' + (tone === 'danger' ? 'danger' : '') + '"></button>' +
          '</div></div>';
      wrap.querySelector('#uiDlgT').textContent = opts.title || (opts.confirm ? 'Please confirm' : 'Notice');
      wrap.querySelector('#uiDlgM').textContent = opts.message == null ? '' : String(opts.message);
      var ok = wrap.querySelector('.ui-dlg-ok'), cancel = wrap.querySelector('.ui-dlg-cancel');
      ok.textContent = opts.confirmText || (opts.confirm ? 'Confirm' : 'OK');
      if (cancel) cancel.textContent = opts.cancelText || 'Cancel';
      document.body.appendChild(wrap);
      requestAnimationFrame(function () { wrap.classList.add('is-in'); });
      (cancel || ok).focus();

      function done(val) {
        document.removeEventListener('keydown', onKey, true);
        wrap.classList.remove('is-in');
        setTimeout(function () { wrap.remove(); if (lastFocus && lastFocus.focus) lastFocus.focus(); }, 160);
        resolve(val);
      }
      function onKey(e) {
        if (e.key === 'Escape') { e.preventDefault(); done(false); }
        else if (e.key === 'Tab') { // keep focus inside the dialog
          var f = Array.prototype.slice.call(wrap.querySelectorAll('button'));
          var i = f.indexOf(document.activeElement);
          e.preventDefault(); f[(i + (e.shiftKey ? -1 : 1) + f.length) % f.length].focus();
        }
      }
      document.addEventListener('keydown', onKey, true);
      ok.addEventListener('click', function () { done(true); });
      if (cancel) cancel.addEventListener('click', function () { done(false); });
      wrap.addEventListener('click', function (e) { if (e.target === wrap) done(false); });
    });
  }
  // One dialog at a time.
  function enqueue(opts) { var p = queue.then(function () { return open(opts); }); queue = p.catch(function () {}); return p; }

  window.uiAlert = function (message, o) { o = o || {}; return enqueue({ message: message, title: o.title, tone: o.tone, confirmText: o.confirmText }).then(function () {}); };
  window.uiConfirm = function (message, o) { o = o || {}; return enqueue({ confirm: true, message: message, title: o.title, tone: o.tone || 'danger', confirmText: o.confirmText, cancelText: o.cancelText }); };
  window.alert = function (message) { window.uiAlert(message); };

  // data-confirm on links, buttons and forms
  function ask(el) {
    return window.uiConfirm(el.getAttribute('data-confirm'), {
      title: el.getAttribute('data-confirm-title') || undefined,
      confirmText: el.getAttribute('data-confirm-ok') || undefined,
      tone: el.getAttribute('data-confirm-tone') || 'danger'
    });
  }
  document.addEventListener('click', function (e) {
    var el = e.target.closest('a[data-confirm], button[data-confirm], input[type=submit][data-confirm]');
    if (!el || el.dataset.confirmed === '1') return;
    e.preventDefault(); e.stopImmediatePropagation();
    ask(el).then(function (yes) {
      if (!yes) return;
      el.dataset.confirmed = '1';
      if (el.tagName === 'A') { window.location.href = el.href; }
      else { el.click(); }
      setTimeout(function () { delete el.dataset.confirmed; }, 0);
    });
  }, true);
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.matches('form[data-confirm]') || form.dataset.confirmed === '1') return;
    e.preventDefault(); e.stopImmediatePropagation();
    var submitter = e.submitter;
    ask(form).then(function (yes) {
      if (!yes) return;
      form.dataset.confirmed = '1';
      if (form.requestSubmit) form.requestSubmit(submitter || undefined); else form.submit();
      setTimeout(function () { delete form.dataset.confirmed; }, 0);
    });
  }, true);
})();
</script>
@endonce
