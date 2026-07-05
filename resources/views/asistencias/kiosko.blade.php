<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ALTERMEC · Marcación de asistencia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0f4c5c; color: #fff; min-height: 100vh; margin: 0; }
        .kiosko-stage {
            min-height: 100vh; display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 24px; text-align: center;
        }
        .reader-shell { width: 360px; max-width: 90vw; aspect-ratio: 1; border-radius: 16px;
            overflow: hidden; background: #000; position: relative; box-shadow: 0 12px 40px rgba(0,0,0,.4); }
        #qr-video { width: 100%; height: 100%; object-fit: cover; }
        .feedback { font-size: 2.4rem; font-weight: 700; margin-top: 24px; }
        .feedback.ok { color: #6ee7b7; }
        .feedback.late { color: #fbbf24; }
        .feedback.err { color: #fda4af; }
        .dni-fallback input { font-size: 1.5rem; text-align: center; letter-spacing: 0.3em; }
        .modo-tabs button { background: transparent; border: 1px solid rgba(255,255,255,.4);
            color: #fff; padding: 10px 20px; border-radius: 999px; margin: 0 6px; }
        .modo-tabs button.active { background: #fff; color: #0f4c5c; }
    </style>
</head>
<body>
    <div class="kiosko-stage">
        <h1 class="display-6 fw-bold mb-3">ALTERMEC — Marcación</h1>
        <p class="text-light-emphasis mb-4 small">Escanea tu código QR o ingresa tu DNI.</p>

        <div class="modo-tabs mb-4">
            <button type="button" id="btn-modo-qr"  class="active">📷 Escanear QR</button>
            <button type="button" id="btn-modo-dni">⌨️ Ingresar DNI</button>
        </div>

        <div id="modo-qr">
            <div class="reader-shell">
                <video id="qr-video" autoplay muted playsinline></video>
            </div>
            <p class="small mt-2 text-light-emphasis">Centra el código QR dentro del recuadro.</p>
        </div>

        <div id="modo-dni" class="dni-fallback" style="display:none;">
            <form id="form-dni" class="d-flex flex-column gap-3 align-items-center">
                <input type="text" inputmode="numeric" pattern="\d{8}" maxlength="8" id="dni-input"
                       class="form-control" placeholder="DNI" required>
                <button class="btn btn-light btn-lg px-5">Marcar</button>
            </form>
        </div>

        <div id="feedback" class="feedback"></div>
        <div id="empleado-info" class="mt-2 small text-light-emphasis"></div>
    </div>

    <script type="module">
        const csrf  = document.querySelector('meta[name="csrf-token"]').content;
        const url   = "{{ route('api.asistencias.marcar', [], false) }}";
        const fb    = document.getElementById('feedback');
        const info  = document.getElementById('empleado-info');
        const video = document.getElementById('qr-video');

        const btnQr  = document.getElementById('btn-modo-qr');
        const btnDni = document.getElementById('btn-modo-dni');
        const modoQr  = document.getElementById('modo-qr');
        const modoDni = document.getElementById('modo-dni');

        btnQr.addEventListener('click',  () => { setModo('qr'); });
        btnDni.addEventListener('click', () => { setModo('dni'); });

        function setModo(modo) {
            modoQr.style.display  = modo === 'qr'  ? 'block' : 'none';
            modoDni.style.display = modo === 'dni' ? 'block' : 'none';
            btnQr.classList.toggle('active',  modo === 'qr');
            btnDni.classList.toggle('active', modo === 'dni');
        }

        async function marcar(identificador, metodo) {
            fb.className = 'feedback';
            fb.textContent = 'Procesando…';
            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ identificador, metodo }),
                });
                const data = await r.json();
                pintarResultado(data);
            } catch (e) {
                fb.className = 'feedback err';
                fb.textContent = '⚠️ Error de conexión';
                info.textContent = '';
            }
        }

        function pintarResultado(data) {
            const ok       = data.ok;
            const esTarde  = data.marcacion?.estado === 'tardanza';
            fb.className = 'feedback ' + (ok ? (esTarde ? 'late' : 'ok') : 'err');
            fb.textContent = ok
                ? (data.marcacion?.hora_salida ? '✓ Salida registrada' : '✓ Entrada registrada' + (esTarde ? ' (tardanza)' : ''))
                : '✗ ' + (data.mensaje ?? 'No se pudo marcar');
            info.textContent = data.empleado
                ? `${data.empleado.nombre} · ${data.empleado.area ?? 'Sin área'}`
                : '';
            setTimeout(() => { fb.textContent = ''; info.textContent = ''; fb.className = 'feedback'; }, 4500);
        }

        // QR scanner usando BarcodeDetector si disponible; en su defecto, fallback manual.
        async function iniciarCamara() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }, audio: false,
                });
                video.srcObject = stream;
                await video.play();
                if ('BarcodeDetector' in window) {
                    const detector = new BarcodeDetector({ formats: ['qr_code'] });
                    let bloqueado = false;
                    setInterval(async () => {
                        if (bloqueado || video.readyState < 2) return;
                        try {
                            const codes = await detector.detect(video);
                            if (codes.length > 0) {
                                bloqueado = true;
                                await marcar(codes[0].rawValue, 'QR');
                                setTimeout(() => { bloqueado = false; }, 4000);
                            }
                        } catch (_) {}
                    }, 600);
                } else {
                    info.textContent = 'Este navegador no detecta QR — usa la opción DNI.';
                }
            } catch (e) {
                info.textContent = 'No se pudo acceder a la cámara. Usa DNI.';
            }
        }

        document.getElementById('form-dni').addEventListener('submit', (ev) => {
            ev.preventDefault();
            const v = document.getElementById('dni-input').value.trim();
            if (v) {
                marcar(v, 'DNI');
                document.getElementById('dni-input').value = '';
            }
        });

        iniciarCamara();
    </script>
</body>
</html>
