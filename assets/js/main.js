/* Cordillera - main.js
 * Countdown, FAQ-Akkordeon, Hero-Variantenwechsel (Vorschau fuer Eingeloggte),
 * mobiles Menue und Boersen-Formular (AJAX).
 */
(function () {
	'use strict';
	var MYM = window.MYM || {};

	/* ---------- Mobiles Menue ---------- */
	var burger = document.querySelector('.mym-burger');
	var nav = document.querySelector('.mym-nav');
	if (burger && nav) {
		burger.addEventListener('click', function () {
			var open = nav.classList.toggle('open');
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		nav.addEventListener('click', function (e) {
			if (e.target.tagName === 'A') { nav.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }
		});
	}

	/* ---------- Countdown ---------- */
	var cd = document.getElementById('mym-countdown');
	if (cd && cd.getAttribute('data-date')) {
		var dateStr = cd.getAttribute('data-date');
		var timeStr = cd.getAttribute('data-time') || '11:00';
		var target = new Date(dateStr + 'T' + (timeStr.length === 5 ? timeStr : '11:00') + ':00').getTime();
		var els = {
			d: cd.querySelector('[data-cd="d"]'), h: cd.querySelector('[data-cd="h"]'),
			m: cd.querySelector('[data-cd="m"]'), s: cd.querySelector('[data-cd="s"]')
		};
		var pad = function (n) { return (n < 10 ? '0' : '') + n; };
		var tick = function () {
			var diff = Math.max(0, target - Date.now());
			var d = Math.floor(diff / 86400000); diff -= d * 86400000;
			var h = Math.floor(diff / 3600000); diff -= h * 3600000;
			var m = Math.floor(diff / 60000); diff -= m * 60000;
			var s = Math.floor(diff / 1000);
			if (els.d) els.d.textContent = String(d);
			if (els.h) els.h.textContent = pad(h);
			if (els.m) els.m.textContent = pad(m);
			if (els.s) els.s.textContent = pad(s);
		};
		tick();
		setInterval(tick, 1000);
	}

	/* FAQ-Akkordeon laeuft jetzt nativ ueber <details>/<summary> — kein JS noetig. */

	/* ---------- Hero-Variantenwechsel (nur Vorschau, Eingeloggte) ---------- */
	var switcher = document.querySelector('.mym-variant-switch');
	if (switcher) {
		switcher.addEventListener('click', function (e) {
			var btn = e.target.closest('button[data-variant]');
			if (!btn) return;
			var v = btn.getAttribute('data-variant');
			document.querySelectorAll('.mym-hero-pane').forEach(function (p) {
				p.style.display = (p.getAttribute('data-pane') === v) ? '' : 'none';
			});
			switcher.querySelectorAll('button').forEach(function (b) { b.classList.toggle('active', b === btn); });
		});
	}

	/* ---------- Boersen-Formular ---------- */
	var form = document.getElementById('mym-board-form');
	var msg = document.getElementById('mym-board-msg');
	if (form && MYM.ajaxUrl) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var data = new FormData(form);
			var name = (data.get('name') || '').trim();
			msg.classList.remove('error');
			if (!name) { msg.textContent = (MYM.i18n && MYM.i18n.errName) || 'Name?'; msg.classList.add('error'); return; }
			data.append('action', 'mym_board_submit');
			data.append('nonce', MYM.nonce);
			msg.textContent = (MYM.i18n && MYM.i18n.sending) || '...';
			var btn = form.querySelector('button[type="submit"]');
			if (btn) btn.disabled = true;

			fetch(MYM.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (btn) btn.disabled = false;
					if (res && res.success) {
						if (res.data && res.data.moderated) {
							msg.textContent = (MYM.i18n && MYM.i18n.thanks) || 'Danke!';
						} else if (res.data && res.data.entry) {
							addEntryToList(res.data.entry);
							msg.textContent = (MYM.i18n && MYM.i18n.thanks) || 'Danke!';
						}
						form.reset();
					} else {
						msg.textContent = (res && res.data && res.data.message) || (MYM.i18n && MYM.i18n.error) || 'Fehler';
						msg.classList.add('error');
					}
				})
				.catch(function () {
					if (btn) btn.disabled = false;
					msg.textContent = (MYM.i18n && MYM.i18n.error) || 'Fehler';
					msg.classList.add('error');
				});
		});
	}

	function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

	function addEntryToList(entry) {
		var list = document.querySelector('.mym-board-list[data-list="' + entry.type + '"]');
		if (!list) return;
		var empty = list.querySelector('[data-empty]');
		if (empty) empty.remove();

		var langLabels = { de: 'DE', es: 'ES', fr: 'FR', en: 'EN' };
		var langColors = { de: '#7fae8a', es: '#d9b873', fr: '#a8bde8', en: '#88c0d0' };

		var div = document.createElement('div');
		div.className = 'mym-board-item ' + (entry.type === 'seek' ? 'seek' : 'offer');

		var html = '<div class="mym-board-item-head">'
			+ '<span class="name">' + esc(entry.name) + '</span>'
			+ (entry.places ? '<span class="pl">' + esc(entry.places) + ' Pl.</span>' : '')
			+ '</div>';

		if (entry.location || entry.date_from || entry.date_to) {
			html += '<div class="mym-board-item-meta">';
			if (entry.location) html += '<span class="meta-loc">📍 ' + esc(entry.location) + '</span>';
			if (entry.date_from || entry.date_to) {
				html += '<span class="meta-dates">📅 ' + esc(entry.date_from || '') + (entry.date_to ? ' – ' + esc(entry.date_to) : '') + '</span>';
			}
			html += '</div>';
		}

		if (entry.langs && entry.langs.length) {
			html += '<div class="mym-board-item-langs">';
			entry.langs.forEach(function(l) {
				html += '<span class="mym-lang-badge ' + esc(l) + '">' + esc(langLabels[l] || l.toUpperCase()) + '</span>';
			});
			html += '</div>';
		}

		if (entry.note) html += '<div class="note">' + esc(entry.note) + '</div>';

		div.innerHTML = html;
		list.appendChild(div);
	}

	/* ---------- RSVP-Formular ---------- */
	var rsvpRoot = document.getElementById('mym-rsvp-box');
	var rsvpForm = document.getElementById('mym-rsvp-form');
	if (rsvpRoot && rsvpForm && MYM.ajaxUrl) {
		var rsvpMsg = document.getElementById('mym-rsvp-msg');
		var guestList = document.getElementById('mym-rsvp-guests');
		var guestTpl = document.getElementById('mym-rsvp-guest-tpl');
		var addGuestBtn = document.getElementById('mym-rsvp-add-guest');
		var guestsWrap = rsvpForm.querySelector('[data-guests-wrap]');
		var declinedNameWrap = rsvpForm.querySelector('[data-declined-name-wrap]');
		var statusRadios = rsvpForm.querySelectorAll('input[name="status"]');
		var i18n = MYM.rsvpI18n || {};

		function addGuestRow(data) {
			data = data || {};
			var frag = guestTpl.content.cloneNode(true);
			var row = frag.querySelector('.mym-rsvp-guest-row');
			row.querySelector('[data-g="name"]').value = data.name || '';
			row.querySelector('[data-g="child"]').checked = !!data.child;
			row.querySelector('[data-g="veggie"]').checked = !!data.veggie;
			row.querySelector('[data-g="allergies"]').value = data.allergies || '';
			var langs = data.langs || [];
			row.querySelectorAll('[data-g="langs"]').forEach(function (cb) {
				cb.checked = langs.indexOf(cb.value) !== -1;
			});
			row.querySelector('[data-remove-guest]').addEventListener('click', function () {
				row.remove();
			});
			guestList.appendChild(row);
		}

		function updateGuestsVisibility() {
			var yes = rsvpForm.querySelector('input[name="status"]:checked');
			var isYes = !yes || yes.value === 'yes';
			guestsWrap.style.display = isYes ? '' : 'none';
			if (declinedNameWrap) { declinedNameWrap.style.display = isYes ? 'none' : ''; }
		}
		statusRadios.forEach(function (r) { r.addEventListener('change', updateGuestsVisibility); });
		updateGuestsVisibility();

		if (addGuestBtn) {
			addGuestBtn.addEventListener('click', function () { addGuestRow(); });
		}

		/* Vorbefuellung bei Bearbeiten via Token, sonst eine leere Zeile zum Start */
		var prefillEl = document.getElementById('mym-rsvp-prefill');
		if (prefillEl) {
			try {
				var guests = JSON.parse(prefillEl.textContent || '[]');
				guests.forEach(function (g) { addGuestRow(g); });
			} catch (err) { /* ignore malformed prefill */ }
		} else if (guestList.children.length === 0) {
			addGuestRow();
		}

		rsvpForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var email = rsvpForm.querySelector('[name="email"]').value.trim();
			var phone = rsvpForm.querySelector('[name="phone"]').value.trim();
			var status = (rsvpForm.querySelector('input[name="status"]:checked') || {}).value || 'yes';

			rsvpMsg.classList.remove('error');
			if (!email || email.indexOf('@') === -1) { rsvpMsg.textContent = i18n.errEmail || 'Email?'; rsvpMsg.classList.add('error'); return; }
			if (!/^\+[0-9 ()-]{7,20}$/.test(phone)) { rsvpMsg.textContent = i18n.errPhone || 'Phone?'; rsvpMsg.classList.add('error'); return; }
			if (status === 'no') {
				var declinedName = rsvpForm.querySelector('[name="name"]').value.trim();
				if (!declinedName) { rsvpMsg.textContent = i18n.errName || 'Name?'; rsvpMsg.classList.add('error'); return; }
			}

			var guestsData = [];
			if (status === 'yes') {
				guestList.querySelectorAll('.mym-rsvp-guest-row').forEach(function (row) {
					var gName = row.querySelector('[data-g="name"]').value.trim();
					if (!gName) { return; }
					var langs = [];
					row.querySelectorAll('[data-g="langs"]:checked').forEach(function (cb) { langs.push(cb.value); });
					guestsData.push({
						name: gName,
						child: row.querySelector('[data-g="child"]').checked,
						veggie: row.querySelector('[data-g="veggie"]').checked,
						allergies: row.querySelector('[data-g="allergies"]').value.trim(),
						langs: langs
					});
				});
				if (!guestsData.length) { rsvpMsg.textContent = i18n.errGuest || 'Guests?'; rsvpMsg.classList.add('error'); return; }
			}

			var data = new FormData(rsvpForm);
			data.append('action', 'mym_rsvp_submit');
			data.append('nonce', MYM.rsvpNonce);
			data.append('token', rsvpRoot.getAttribute('data-token') || '');
			data.append('guests', JSON.stringify(guestsData));

			rsvpMsg.textContent = i18n.sending || '...';
			var btn = rsvpForm.querySelector('button[type="submit"]');
			if (btn) btn.disabled = true;

			fetch(MYM.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (btn) btn.disabled = false;
					if (res && res.success) {
						rsvpMsg.textContent = res.data.updated ? (i18n.updated || 'Updated!') : (i18n.thanks || 'Thanks!');
						if (res.data.token) { rsvpRoot.setAttribute('data-token', res.data.token); }
					} else {
						rsvpMsg.textContent = (res && res.data && res.data.message) || i18n.error || 'Error';
						rsvpMsg.classList.add('error');
					}
				})
				.catch(function () {
					if (btn) btn.disabled = false;
					rsvpMsg.textContent = i18n.error || 'Error';
					rsvpMsg.classList.add('error');
				});
		});
	}

	/* ---------- Foto-Slider ---------- */
	document.querySelectorAll('.mym-slider').forEach(function (slider) {
		var track = slider.querySelector('.mym-slider-track');
		var slides = Array.prototype.slice.call(slider.querySelectorAll('.mym-slide'));
		if (!track || slides.length < 2) { return; }

		var dotsWrap = slider.querySelector('.mym-slider-dots');
		var prevBtn = slider.querySelector('.mym-slider-prev');
		var nextBtn = slider.querySelector('.mym-slider-next');
		var index = 0;
		var dots = [];

		if (dotsWrap) {
			slides.forEach(function (_, i) {
				var dot = document.createElement('button');
				dot.type = 'button';
				dot.setAttribute('aria-label', 'Bild ' + (i + 1));
				dot.addEventListener('click', function () { goTo(i); });
				dotsWrap.appendChild(dot);
				dots.push(dot);
			});
		}

		function goTo(i) {
			index = (i + slides.length) % slides.length;
			track.style.transform = 'translateX(-' + (index * 100) + '%)';
			dots.forEach(function (d, di) { d.classList.toggle('active', di === index); });
		}
		goTo(0);

		if (prevBtn) { prevBtn.addEventListener('click', function () { goTo(index - 1); stopAutoplay(); }); }
		if (nextBtn) { nextBtn.addEventListener('click', function () { goTo(index + 1); stopAutoplay(); }); }

		slider.setAttribute('tabindex', '0');
		slider.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { goTo(index - 1); stopAutoplay(); }
			if (e.key === 'ArrowRight') { goTo(index + 1); stopAutoplay(); }
		});

		/* Touch-Wisch-Geste */
		var touchStartX = null;
		slider.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
		slider.addEventListener('touchend', function (e) {
			if (touchStartX === null) { return; }
			var dx = e.changedTouches[0].clientX - touchStartX;
			if (Math.abs(dx) > 40) { goTo(dx < 0 ? index + 1 : index - 1); stopAutoplay(); }
			touchStartX = null;
		}, { passive: true });

		/* Autoplay, per data-autoplay="true" am .mym-slider-Element aktivierbar */
		var timer = null;
		function startAutoplay() {
			if (slider.dataset.autoplay !== 'true') { return; }
			timer = setInterval(function () { goTo(index + 1); }, 5000);
		}
		function stopAutoplay() { if (timer) { clearInterval(timer); timer = null; } }
		startAutoplay();
	});
})();
