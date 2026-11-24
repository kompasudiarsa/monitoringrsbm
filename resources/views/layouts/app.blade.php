<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Monitoring BPJS' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        a { color: inherit; }
        /* Jhon | 2026-11-23 21:01 WITA | UBAH: header akun bersama, brand dan logout proporsional. */
        .monitor-topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:16px 28px;background:#fff;border-bottom:1px solid #e2e8f0}
        .monitor-brand{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;text-decoration:none;color:#0f172a}
        .monitor-brand-icon{display:grid;place-items:center;width:36px;height:36px;border-radius:11px;background:#e0f2fe;color:#0369a1;font-size:23px}
        .monitor-account{display:flex;align-items:center;gap:14px;min-width:0}.monitor-user{font-size:13px;color:#475569;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .monitor-logout{padding:9px 14px;border:1px solid #fecaca;border-radius:10px;background:#fff1f2;color:#b91c1c;font-weight:700;cursor:pointer}.monitor-logout:hover{background:#fee2e2}.monitor-logout:focus-visible,.monitor-brand:focus-visible{outline:3px solid #7dd3fc;outline-offset:3px}
        @media(max-width:600px){.monitor-topbar{padding:12px 16px;gap:10px}.monitor-brand{font-size:13px}.monitor-user{max-width:90px}.monitor-account{gap:8px}.monitor-logout{padding:8px 10px}}
        /* Jhon | 2026-11-24 11:45 WITA | UBAH: padatkan bar navigasi akun tanpa menghapus akses Logout. */
        .monitor-topbar { padding: 10px 24px; gap: 12px; }
        .monitor-brand { font-size: 14px; gap: 8px; }
        .monitor-brand-icon { width: 29px; height: 29px; border-radius: 8px; font-size: 21px; }
        .monitor-logout { padding: 7px 12px; }
        @media(max-width:600px) { .monitor-topbar { padding: 8px 14px; } .monitor-brand { font-size: 12px; } }
        /* Jhon | 2026-11-24 14:20 WITA | TAMBAH: modal status bersama, header drag dan isi scroll terpisah. */
        [data-draggable-modal] [data-drag-handle]{cursor:grab;touch-action:none;user-select:none}
        [data-draggable-modal] [data-drag-handle].is-dragging{cursor:grabbing}
        .shared-status-modal{width:min(760px,calc(100vw - 32px));max-height:85vh;padding:0;border:0;border-radius:18px;box-shadow:0 24px 80px #0f172a50}
        .shared-status-modal::backdrop{background:#0f172a80}.shared-status-header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid #e2e8f0}.shared-status-header h2{font-size:18px;margin:0}.shared-status-header button{border:0;border-radius:8px;padding:8px 12px;cursor:pointer}.shared-status-body{padding:18px;max-height:65vh;overflow:auto;overflow-wrap:anywhere;white-space:normal}.shared-status-body .kontrol-explanation{font-size:14px;line-height:1.6}
    </style>
</head>
<body>
    {{-- Jhon | 2026-11-23 20:45 WITA | TAMBAH: identitas sesi dan keluar tersedia pada layout bersama. --}}
    @if (session('monitoring_auth.expires_at', 0) > time())
    {{-- Jhon | 2026-11-23 21:01 WITA | UBAH: brand dashboard, identitas akun, dan logout; hook sesi tetap sama. --}}
    <header class="monitor-topbar">
        <a href="{{ route('menu') }}" class="monitor-brand"><span class="monitor-brand-icon" aria-hidden="true">+</span>Dashboard Monitoring</a>
        <div class="monitor-account">
            <span class="monitor-user" title="{{ session('monitoring_auth.name') }}">{{ session('monitoring_auth.name') }}</span>
            <form id="monitoring-logout" method="POST" action="{{ route('monitoring.logout') }}">@csrf<button type="submit" class="monitor-logout">Logout</button></form>
        </div>
    </header>
    @endif
    @yield('content')
    {{-- Jhon | 2026-11-23 20:48 WITA | TAMBAH: idle logout dan heartbeat hanya setelah interaksi, bukan polling. --}}
    @if (session('monitoring_auth.expires_at', 0) > time())
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('monitoring-logout');
        if (!form) return;
        let last = Date.now(), active = false, pending = false;
        const timeout = 30 * 60 * 1000;
        function logout() { form.submit(); }
        ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach(name => {
            document.addEventListener(name, function () {
                if (Date.now() - last >= timeout) { logout(); return; }
                last = Date.now(); active = true;
            }, {passive: true});
        });
        setInterval(async function () {
            if (Date.now() - last >= timeout) { logout(); return; }
            if (!active || pending) return;
            active = false; pending = true;
            try {
                const response = await fetch("{{ route('monitoring.activity') }}", {
                    method: 'POST', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value}
                });
                if (response.status === 401 || response.status === 419) window.location.assign("{{ route('login') }}");
            } catch (error) { active = true; }
            finally { pending = false; }
        }, 30000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && Date.now() - last >= timeout) logout();
        });
    });
    </script>
    @endif
<script>

// Jhon | 2026-11-24 14:20 WITA | TAMBAH: pointer drag untuk kedua modal status; reset saat tutup/resize, tombol tidak menjadi pegangan.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-draggable-modal]').forEach(modal => {
        const handle = modal.querySelector('[data-drag-handle]');
        if (!handle) return;
        let drag = null, x = 0, y = 0;
        function reset() { drag = null; x = 0; y = 0; modal.style.removeProperty('transform'); handle.classList.remove('is-dragging'); }
        modal.addEventListener('close', reset);
        window.addEventListener('resize', reset);
        const close = modal.querySelector('[data-modal-close]');
        if (close) close.addEventListener('click', () => modal.close());
        handle.addEventListener('pointerdown', event => {
            if (event.button !== 0 || event.target.closest('button,a,input,select,textarea')) return;
            drag = {id:event.pointerId, px:event.clientX, py:event.clientY, x, y, rect:modal.getBoundingClientRect()};
            handle.setPointerCapture(event.pointerId); handle.classList.add('is-dragging'); event.preventDefault();
        });
        handle.addEventListener('pointermove', event => {
            if (!drag || drag.id !== event.pointerId) return;
            const dx = Math.max(8-drag.rect.left,Math.min(event.clientX-drag.px,window.innerWidth-8-drag.rect.right));
            const dy = Math.max(8-drag.rect.top,Math.min(event.clientY-drag.py,window.innerHeight-8-drag.rect.bottom));
            x=drag.x+dx; y=drag.y+dy; modal.style.transform=`translate(${x}px, ${y}px)`;
        });
        function stop(event) {
            if (!drag || drag.id !== event.pointerId) return;
            drag=null; handle.classList.remove('is-dragging');
            if (handle.hasPointerCapture(event.pointerId)) handle.releasePointerCapture(event.pointerId);
        }
        ['pointerup','pointercancel','lostpointercapture'].forEach(name=>handle.addEventListener(name,stop));
    });
});
</script>
</body>
</html>