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
})();
