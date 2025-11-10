/* =========================================================
   Popup Modal + 3-Field Row Form (AJAX submit)
   - Row 1: orange text + image
   - Row 2: three inputs in one row + Submit
   - Inline success/error message
   ========================================================= */
(() => {
  const SELECTORS = {
    modal: '#image-popup-modal',
    closeBtn: '.modal-close-btn',
    form: '#popup-contact-form',
    message: '#popup-message',
    name: '#popup-name',
    mobile: '#popup-mobile',
    email: '#popup-email',
    submitBtn: '.popup-submit'
  };

  const STATE = {
    autoOpenDelay: 1500, // set to null to disable auto-open
    isSubmitting: false
  };

  // ---------- helpers ----------
  const $ = (sel, scope = document) => scope.querySelector(sel);

  function openModal() {
    const modal = $(SELECTORS.modal);
    if (!modal) return;
    modal.style.display = 'flex';
    $(SELECTORS.name)?.focus();
    lockScroll(true);
  }

  function closeModal() {
    const modal = $(SELECTORS.modal);
    if (!modal) return;
    modal.style.display = 'none';
    lockScroll(false);
  }

  function lockScroll(locked) {
    document.documentElement.style.overflow = locked ? 'hidden' : '';
    document.body.style.overflow = locked ? 'hidden' : '';
  }

  function setMessage(text, type = '') {
    const el = $(SELECTORS.message);
    if (!el) return;
    el.textContent = text || '';
    el.className = type ? type : '';
  }

  function setSubmitting(v) {
    STATE.isSubmitting = v;
    const btn = $(SELECTORS.submitBtn);
    if (btn) {
      btn.disabled = v;
      btn.textContent = v ? 'Submitting...' : 'Submit';
    }
  }

  function validateFields() {
    const name   = $(SELECTORS.name)?.value.trim() || '';
    const mobile = $(SELECTORS.mobile)?.value.trim() || '';
    const email  = $(SELECTORS.email)?.value.trim() || '';

    if (!name) return { ok: false, msg: 'Please enter your name.' };
    if (!/^\d{10}$/.test(mobile)) return { ok: false, msg: 'Please enter a valid 10-digit mobile number.' };
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return { ok: false, msg: 'Please enter a valid email address.' };
    return { ok: true };
  }

  // ---------- wire up ----------
  document.addEventListener('DOMContentLoaded', () => {
    const modal = $(SELECTORS.modal);
    const form  = $(SELECTORS.form);
    if (!modal || !form) return;

    // auto open (optional)
    if (typeof STATE.autoOpenDelay === 'number') {
      setTimeout(openModal, STATE.autoOpenDelay);
    }

    // close actions
    $(SELECTORS.closeBtn)?.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // AJAX submit
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (STATE.isSubmitting) return;

      const check = validateFields();
      if (!check.ok) { setMessage(check.msg, 'error'); return; }

      setMessage('Submitting...');
      setSubmitting(true);

      try {
        const fd = new FormData(form);
        const res = await fetch('popup_submit.php', { method: 'POST', body: fd });

        // safer parsing if server accidentally returns HTML
        const raw = await res.text();
        let data = null;
        try { data = JSON.parse(raw); } catch { /* ignore */ }

        if (!res.ok || !data) {
          console.error('Non-JSON or error response:', raw);
          setMessage('Server error. Please try again.', 'error');
        } else if (data.success) {
          setMessage('Thank you! Your details were submitted successfully.', 'success');
          form.reset();
        } else {
          setMessage(data.message || 'Error submitting. Please try again.', 'error');
        }
      } catch (err) {
        console.error(err);
        setMessage('Network error. Please try again.', 'error');
      } finally {
        setSubmitting(false);
      }
    });
  });

  // optional global access
  window.PopupModal = { open: () => openModal(), close: () => closeModal() };
})();
