import axios from 'axios';

// --- axios базовая настройка (Laravel + CSRF) ---
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
if (csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
axios.defaults.withCredentials = true;

// --- хранилище выбранных id по таблице ---
const selectedMap = new WeakMap(); // tableEl -> Set(ids)
function getSelectedSet(tableEl) {
    if (!selectedMap.has(tableEl)) selectedMap.set(tableEl, new Set());
    return selectedMap.get(tableEl);
}

// поиск чекбокса в заголовке (thead может перерисовываться)
function findHeaderCheckbox(tableEl) {
    return tableEl.querySelector(
        'thead input[type="checkbox"][data-select-all], thead input#select-all, thead input[type="checkbox"]'
    );
}

// синхронизация текущей страницы по нашему Set
function syncPage(tableEl) {
    const tbody = tableEl.querySelector('tbody');
    if (!tbody) return;
    const selected = getSelectedSet(tableEl);

    tbody.querySelectorAll('tr').forEach(tr => {
        const cb = tr.querySelector('.row-checkbox');
        if (!cb) return;
        const on = selected.has(cb.value);
        cb.checked = on;
        tr.classList.toggle('table-active', on);
    });

    const head = findHeaderCheckbox(tableEl);
    if (!head) return;
    const pageCbs = tbody.querySelectorAll('.row-checkbox');
    const pageOn = tbody.querySelectorAll('.row-checkbox:checked');

    if (pageCbs.length === 0) {
        head.checked = false; head.indeterminate = false;
    } else if (pageOn.length === pageCbs.length) {
        head.checked = true; head.indeterminate = false;
    } else if (pageOn.length > 0) {
        head.checked = false; head.indeterminate = true;
    } else {
        head.checked = false; head.indeterminate = false;
    }
}

// навешиваем обработчики (без jQuery, переживает redraw)
function attach(tableEl) {
    const tbody = tableEl.querySelector('tbody');
    if (!tbody) return false;
    const selected = getSelectedSet(tableEl);

    // делегирование change: и thead, и tbody
    if (!tableEl.__dtSelectBound) {
        tableEl.addEventListener('change', (e) => {
            const target = e.target;
            if (!(target instanceof HTMLInputElement)) return;

            // клик по чекбоксу в заголовке → отметить/снять ТЕКУЩЕ ВИДИМЫЕ строки
            if (target.closest('thead') && target.type === 'checkbox') {
                const check = target.checked;
                tbody.querySelectorAll('.row-checkbox').forEach(cb => {
                    if (check) selected.add(cb.value);
                    else selected.delete(cb.value);
                    cb.checked = check;
                    cb.closest('tr')?.classList.toggle('table-active', check);
                });
                target.indeterminate = false; // дальше sync выставит правильное состояние
                syncPage(tableEl);
                return;
            }

            // клик по чекбоксу строки
            if (target.classList.contains('row-checkbox')) {
                if (target.checked) selected.add(target.value);
                else selected.delete(target.value);
                target.closest('tr')?.classList.toggle('table-active', target.checked);
                syncPage(tableEl);
            }
        }, true);

        // публичное API для получения id
        tableEl.__getSelectedIds = () => Array.from(getSelectedSet(tableEl));
        tableEl.__clearSelected = () => { getSelectedSet(tableEl).clear(); syncPage(tableEl); };

        tableEl.__dtSelectBound = true;
    }

    // redraw (пагинация/поиск/сортировка) — пересинхронизировать страницу
    if (!tbody.__dtObserver) {
        const obs = new MutationObserver(() => syncPage(tableEl));
        obs.observe(tbody, { childList: true, subtree: true });
        tbody.__dtObserver = obs;
    }

    // первый прогон
    syncPage(tableEl);
    return true;
}

// ждём готовности таблицы (tbody + чекбокс в thead)
function waitReady(tableEl) {
    if (tableEl.querySelector('tbody') && findHeaderCheckbox(tableEl)) {
        attach(tableEl); return;
    }
    const obs = new MutationObserver(() => {
        if (tableEl.querySelector('tbody') && findHeaderCheckbox(tableEl)) {
            obs.disconnect(); attach(tableEl);
        }
    });
    obs.observe(tableEl, { childList: true, subtree: true });
}

// авто‑инициализация конкретной таблицы (или всех с data-select-all в thead)
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('companies-table');
    if (table) {
        waitReady(table);
    } else {
        document.querySelectorAll('table').forEach(t => {
            if (findHeaderCheckbox(t)) waitReady(t);
        });
    }
});

// --- Универсальный “bulk” без jQuery ---
// Кнопка: <button type="button" data-bulk data-table="companies-table" data-url="/route" data-method="post">...</button>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-bulk]');
    if (!btn) return;

    const tableId = btn.getAttribute('data-table');
    const url = btn.getAttribute('data-url');
    const method = (btn.getAttribute('data-method') || 'post').toLowerCase();

    if (!tableId || !url) return console.warn('[bulk] missing data-table or data-url');

    const table = document.getElementById(tableId);
    const ids = table?.__getSelectedIds?.() || [];
    if (!ids.length) {
        // нет выбора — решай сам: alert/тост/ничего
        return console.warn('[bulk] no selected ids');
    }

    try {
        btn.disabled = true;
        await axios({ method, url, data: { ids } });
        // по желанию: очистить выбор/перезагрузить
        // table.__clearSelected?.();
        // location.reload();
    } catch (err) {
        console.error('[bulk] request failed', err);
    } finally {
        btn.disabled = false;
    }
});
