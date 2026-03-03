<?php
session_start();

// ======================================
// CONFIGURACIÓN - Cambiar estas credenciales
// ======================================
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'Notaria101!';
// ======================================

// Login
if (isset($_POST['login'])) {
    if ($_POST['usuario'] === $ADMIN_USER && $_POST['password'] === $ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Credenciales incorrectas';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Blog | Notaría Pública 101</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Quill Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0f0f13;
            --bg-card: #1a1a22;
            --border: rgba(255, 255, 255, 0.08);
            --gold: #cfb53b;
            --gold-dim: rgba(207, 181, 59, 0.2);
            --text: #fafafa;
            --muted: #a0a0ab;
            --danger: #ef4444;
            --success: #22c55e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Login */
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .login-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-box h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--gold);
        }

        .login-box p {
            color: var(--muted);
            margin-bottom: 2rem;
        }

        .login-box input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            font-size: 1rem;
            margin-bottom: 1rem;
            outline: none;
        }

        .login-box input:focus {
            border-color: var(--gold);
        }

        .login-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 2rem;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-gold {
            background: var(--gold);
            color: #000;
            width: 100%;
            justify-content: center;
        }

        .btn-gold:hover {
            opacity: 0.9;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Admin Layout */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 2rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
        }

        .admin-logo {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--gold);
        }

        .admin-nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .admin-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Article List */
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .panel-header h2 {
            font-size: 1.8rem;
            font-weight: 500;
        }

        .article-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .article-row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.2rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .article-row:hover {
            border-color: var(--gold-dim);
        }

        .article-row-img {
            width: 80px;
            height: 56px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .article-row-img.placeholder {
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
        }

        .article-row-info {
            flex: 1;
        }

        .article-row-title {
            font-weight: 500;
            margin-bottom: 0.3rem;
            font-size: 1.05rem;
        }

        .article-row-meta {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .article-row-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Editor View */
        .editor-view {
            display: none;
        }

        .editor-view.active {
            display: block;
        }

        .list-view.hidden {
            display: none;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            font-size: 1rem;
            outline: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--gold);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        /* Quill Overrides */
        .ql-toolbar.ql-snow {
            border: 1px solid var(--border) !important;
            border-radius: 8px 8px 0 0;
            background: var(--bg-card);
        }

        .ql-container.ql-snow {
            border: 1px solid var(--border) !important;
            border-top: none !important;
            border-radius: 0 0 8px 8px;
            background: var(--bg);
            min-height: 300px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            color: var(--text);
        }

        .ql-editor {
            min-height: 300px;
            line-height: 1.7;
        }

        .ql-editor.ql-blank::before {
            color: var(--muted);
            font-style: normal;
        }

        .ql-snow .ql-stroke {
            stroke: var(--muted) !important;
        }

        .ql-snow .ql-fill {
            fill: var(--muted) !important;
        }

        .ql-snow .ql-picker-label {
            color: var(--muted) !important;
        }

        .ql-snow .ql-picker-options {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
        }

        .ql-snow .ql-picker-item {
            color: var(--text) !important;
        }

        .ql-snow button:hover .ql-stroke {
            stroke: var(--gold) !important;
        }

        .ql-snow button:hover .ql-fill {
            fill: var(--gold) !important;
        }

        .ql-snow button.ql-active .ql-stroke {
            stroke: var(--gold) !important;
        }

        .ql-snow button.ql-active .ql-fill {
            fill: var(--gold) !important;
        }

        .editor-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-size: 0.95rem;
            z-index: 999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s ease;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: var(--success);
        }

        .toast.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            color: var(--border);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .admin-main {
                padding: 1rem;
            }

            .article-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .article-row-img {
                width: 100%;
                height: 120px;
            }
        }
    </style>
</head>

<body>

    <?php if (!$isLoggedIn): ?>
        <!-- Login Screen -->
        <div class="login-page">
            <div class="login-box">
                <h1><i class="ri-quill-pen-line"></i> Blog Admin</h1>
                <p>Notaría Pública 101</p>
                <?php if (isset($login_error)): ?>
                    <div class="login-error">
                        <?= $login_error ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <input type="text" name="usuario" placeholder="Usuario" required>
                    <input type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit" name="login" class="btn btn-gold">Iniciar Sesión</button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- Admin Panel -->
        <header class="admin-header">
            <span class="admin-logo"><i class="ri-quill-pen-line"></i> Blog Admin</span>
            <nav class="admin-nav">
                <a href="/" class="btn btn-outline btn-sm"><i class="ri-external-link-line"></i> Ver sitio</a>
                <a href="/blog.html" class="btn btn-outline btn-sm"><i class="ri-article-line"></i> Ver blog</a>
                <a href="?logout" class="btn btn-outline btn-sm"><i class="ri-logout-box-r-line"></i> Salir</a>
            </nav>
        </header>

        <main class="admin-main">
            <!-- List View -->
            <div id="listView" class="list-view">
                <div class="panel-header">
                    <h2>Artículos</h2>
                    <button class="btn btn-gold" onclick="showEditor()">
                        <i class="ri-add-line"></i> Nuevo Artículo
                    </button>
                </div>
                <div id="articleList" class="article-list">
                    <div class="empty-state">
                        <i class="ri-loader-4-line ri-spin"></i>
                        <p>Cargando artículos...</p>
                    </div>
                </div>
            </div>

            <!-- Editor View -->
            <div id="editorView" class="editor-view">
                <div class="panel-header">
                    <h2 id="editorTitle">Nuevo Artículo</h2>
                    <button class="btn btn-outline" onclick="showList()">
                        <i class="ri-arrow-left-line"></i> Volver
                    </button>
                </div>

                <input type="hidden" id="artId" value="0">

                <div class="form-row">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" id="artTitulo" placeholder="Título del artículo">
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" id="artFecha">
                    </div>
                </div>

                <div class="form-group">
                    <label>Resumen</label>
                    <textarea id="artResumen" rows="2" placeholder="Breve descripción del artículo..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>URL de Imagen</label>
                        <input type="url" id="artImagen" placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                    <div class="form-group">
                        <label>Autor</label>
                        <input type="text" id="artAutor" placeholder="Notaría Pública 101" value="Notaría Pública 101">
                    </div>
                </div>

                <div class="form-group">
                    <label>Contenido *</label>
                    <div id="quill-editor"></div>
                </div>

                <div class="editor-actions">
                    <button class="btn btn-gold" onclick="saveArticle()">
                        <i class="ri-save-line"></i> Guardar Artículo
                    </button>
                    <button class="btn btn-outline" onclick="showList()">Cancelar</button>
                </div>
            </div>
        </main>

        <div id="toast" class="toast"></div>

        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            let quill;
            let articles = [];

            // Init Quill
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Escribe el contenido del artículo aquí...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link'],
                        ['blockquote'],
                        ['clean']
                    ]
                }
            });

            // Toast
            function showToast(msg, type = 'success') {
                const t = document.getElementById('toast');
                t.textContent = msg;
                t.className = 'toast ' + type + ' show';
                setTimeout(() => t.classList.remove('show'), 3000);
            }

            // Load articles
            async function loadArticles() {
                try {
                    const res = await fetch('/api.php');
                    articles = await res.json();
                    renderList();
                } catch (e) {
                    showToast('Error al cargar artículos', 'error');
                }
            }

            // Render list
            function renderList() {
                const list = document.getElementById('articleList');
                if (articles.length === 0) {
                    list.innerHTML = '<div class="empty-state"><i class="ri-article-line"></i><p>No hay artículos aún. ¡Crea el primero!</p></div>';
                    return;
                }
                list.innerHTML = articles.map(a => `
      <div class="article-row">
        ${a.imagen
                    ? `<img src="${a.imagen}" class="article-row-img" alt="">`
                    : `<div class="article-row-img placeholder"><i class="ri-image-line"></i></div>`}
        <div class="article-row-info">
          <div class="article-row-title">${a.titulo}</div>
          <div class="article-row-meta">${formatDate(a.fecha)} · ${a.autor || 'Sin autor'}</div>
        </div>
        <div class="article-row-actions">
          <button class="btn btn-outline btn-sm" onclick="editArticle(${a.id})">
            <i class="ri-edit-line"></i> Editar
          </button>
          <button class="btn btn-danger btn-sm" onclick="deleteArticle(${a.id}, '${a.titulo.replace(/'/g, "\\'")}')">
            <i class="ri-delete-bin-line"></i>
          </button>
        </div>
      </div>
    `).join('');
        }

        function formatDate(d) {
            return new Date(d + 'T00:00:00').toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        // Views
        function showEditor(clearForm = true) {
            if (clearForm) {
                document.getElementById('artId').value = '0';
                document.getElementById('artTitulo').value = '';
                document.getElementById('artFecha').value = new Date().toISOString().split('T')[0];
                document.getElementById('artResumen').value = '';
                document.getElementById('artImagen').value = '';
                document.getElementById('artAutor').value = 'Notaría Pública 101';
                quill.setContents([]);
                document.getElementById('editorTitle').textContent = 'Nuevo Artículo';
            }
            document.getElementById('listView').classList.add('hidden');
            document.getElementById('editorView').classList.add('active');
        }

        function showList() {
            document.getElementById('listView').classList.remove('hidden');
            document.getElementById('editorView').classList.remove('active');
        }

        // Edit
        function editArticle(id) {
            const a = articles.find(x => x.id === id);
            if (!a) return;
            document.getElementById('artId').value = a.id;
            document.getElementById('artTitulo').value = a.titulo;
            document.getElementById('artFecha').value = a.fecha;
            document.getElementById('artResumen').value = a.resumen || '';
            document.getElementById('artImagen').value = a.imagen || '';
            document.getElementById('artAutor').value = a.autor || '';
            quill.root.innerHTML = a.contenido;
            document.getElementById('editorTitle').textContent = 'Editar Artículo';
            showEditor(false);
        }

        // Save
        async function saveArticle() {
            const titulo = document.getElementById('artTitulo').value.trim();
            const contenido = quill.root.innerHTML;

            if (!titulo) { showToast('El título es obligatorio', 'error'); return; }
            if (!contenido || contenido === '<p><br></p>') { showToast('El contenido es obligatorio', 'error'); return; }

            const data = {
                id: parseInt(document.getElementById('artId').value),
                titulo,
                fecha: document.getElementById('artFecha').value || new Date().toISOString().split('T')[0],
                autor: document.getElementById('artAutor').value || 'Notaría Pública 101',
                resumen: document.getElementById('artResumen').value,
                imagen: document.getElementById('artImagen').value,
                contenido
            };

            try {
                const res = await fetch('/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    showToast('¡Artículo guardado!');
                    await loadArticles();
                    showList();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (e) {
                showToast('Error al guardar', 'error');
            }
        }

        // Delete
        async function deleteArticle(id, titulo) {
            if (!confirm(`¿Eliminar "${titulo}"? Esta acción no se puede deshacer.`)) return;
            try {
                const res = await fetch('/api.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await res.json();
                if (result.success) {
                    showToast('Artículo eliminado');
                    await loadArticles();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (e) {
                showToast('Error al eliminar', 'error');
            }
        }

        // Init
        loadArticles();
    </script>

    <?php endif; ?>
</body>

</html>