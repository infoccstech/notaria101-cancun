<?php
// API para gestionar artículos del blog
// Endpoints: GET (listar), POST (crear/editar), DELETE (eliminar)

session_start();

header('Content-Type: application/json; charset=utf-8');

$DATA_FILE = __DIR__ . '/articulos.json';

// Verificar autenticación para operaciones de escritura
function isAuthenticated()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Leer artículos
function getArticulos($file)
{
    if (!file_exists($file)) {
        file_put_contents($file, '[]');
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

// Guardar artículos
function saveArticulos($file, $articulos)
{
    file_put_contents($file, json_encode($articulos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];

// GET — Listar artículos (público)
if ($method === 'GET') {
    $articulos = getArticulos($DATA_FILE);
    // Ordenar por fecha descendente
    usort($articulos, function ($a, $b) {
        return strcmp($b['fecha'], $a['fecha']);
    });
    echo json_encode($articulos);
    exit;
}

// Todas las demás operaciones requieren autenticación
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// POST — Crear o editar artículo
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['titulo']) || empty($input['contenido'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Título y contenido son obligatorios']);
        exit;
    }

    $articulos = getArticulos($DATA_FILE);

    $id = isset($input['id']) ? intval($input['id']) : 0;

    $articulo = [
        'titulo' => $input['titulo'],
        'fecha' => $input['fecha'] ?? date('Y-m-d'),
        'autor' => $input['autor'] ?? 'Notaría Pública 101',
        'resumen' => $input['resumen'] ?? '',
        'imagen' => $input['imagen'] ?? '',
        'contenido' => $input['contenido']
    ];

    if ($id > 0) {
        // Editar existente
        $found = false;
        foreach ($articulos as &$a) {
            if ($a['id'] === $id) {
                $a = array_merge($a, $articulo);
                $found = true;
                break;
            }
        }
        if (!$found) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Artículo no encontrado']);
            exit;
        }
    } else {
        // Crear nuevo
        $maxId = 0;
        foreach ($articulos as $a) {
            if ($a['id'] > $maxId)
                $maxId = $a['id'];
        }
        $articulo['id'] = $maxId + 1;
        $articulos[] = $articulo;
    }

    saveArticulos($DATA_FILE, $articulos);
    echo json_encode(['success' => true, 'message' => 'Artículo guardado']);
    exit;
}

// DELETE — Eliminar artículo
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? intval($input['id']) : 0;

    if ($id === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        exit;
    }

    $articulos = getArticulos($DATA_FILE);
    $articulos = array_values(array_filter($articulos, function ($a) use ($id) {
        return $a['id'] !== $id;
    }));

    saveArticulos($DATA_FILE, $articulos);
    echo json_encode(['success' => true, 'message' => 'Artículo eliminado']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>