<?php
// Configuración
$destinatario = "legal@notaria101.com";
$asunto_prefix = "Nuevo mensaje desde la web - ";

// Headers CORS para permitir peticiones desde el frontend
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'])) : '';
$email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
$telefono = isset($_POST['telefono']) ? trim(htmlspecialchars($_POST['telefono'])) : '';
$servicio = isset($_POST['servicio']) ? trim(htmlspecialchars($_POST['servicio'])) : '';
$mensaje = isset($_POST['mensaje']) ? trim(htmlspecialchars($_POST['mensaje'])) : '';

// Validación
if (empty($nombre) || empty($email) || empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Por favor completa los campos obligatorios (nombre, email y mensaje).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

// Construir el cuerpo del correo
$asunto = $asunto_prefix . $servicio;

$cuerpo = "=== Nuevo mensaje desde notaria101.com ===\n\n";
$cuerpo .= "Nombre: $nombre\n";
$cuerpo .= "Email: $email\n";
$cuerpo .= "Teléfono: $telefono\n";
$cuerpo .= "Servicio de interés: $servicio\n\n";
$cuerpo .= "Mensaje:\n$mensaje\n";
$cuerpo .= "\n===================================================\n";

// Headers del correo
$headers = "From: $nombre <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Enviar correo
$enviado = mail($destinatario, $asunto, $cuerpo, $headers);

if ($enviado) {
    echo json_encode(['success' => true, 'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje. Por favor intenta de nuevo o contáctanos por WhatsApp.']);
}
?>