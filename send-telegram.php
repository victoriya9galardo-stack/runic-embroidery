<?php
// Разрешаем CORS (если сайт на другом домене)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Логирование ошибок (для отладки)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Конфигурация
$botToken = "8305382969:AAGaGuRbVHVFQo6E1UYQ4gwxzw_EznySkQw";
$chatId = "1109352803";

/**
 * Отправка сообщения в Telegram через cURL
 */
function sendTelegramMessage($botToken, $chatId, $message, $parseMode = 'HTML') {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => $parseMode
    ];

    // Используем cURL вместо file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($result === false) {
        return [
            'ok' => false,
            'description' => 'cURL Error: ' . $curlError
        ];
    }

    $response = json_decode($result, true);

    if ($httpCode !== 200 || !$response) {
        return [
            'ok' => false,
            'description' => 'HTTP ' . $httpCode . ': ' . ($response['description'] ?? 'Unknown error')
        ];
    }

    return $response;
}

// Обработка только POST-запросов
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Невірний метод запиту'
    ]);
    exit;
}

// Получаем данные формы
$interest = isset($_POST['interest']) ? trim($_POST['interest']) : '';
$timing = isset($_POST['timing']) ? trim($_POST['timing']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Проверка обязательных полей
if (empty($interest) || empty($timing) || empty($name) || empty($contact)) {
    echo json_encode([
        'success' => false,
        'message' => 'Заповніть усі обов\'язкові поля'
    ]);
    exit;
}

// Формируем сообщение
$message = "🔔 <b>Нова заявка з сайту RUNAVYSHYVANKA</b>\n\n";
$message .= "👤 <b>Ім'я:</b> " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n";
$message .= "📞 <b>Контакт:</b> " . htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') . "\n";
$message .= "🎯 <b>Інтерес:</b> " . htmlspecialchars($interest, ENT_QUOTES, 'UTF-8') . "\n";
$message .= "⏰ <b>Термін:</b> " . htmlspecialchars($timing, ENT_QUOTES, 'UTF-8') . "\n";

if (!empty($comment)) {
    $message .= "💬 <b>Коментар:</b> " . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . "\n";
}

$message .= "\n📅 <b>Дата:</b> " . date('d.m.Y H:i');

// Отправляем в Telegram
$response = sendTelegramMessage($botToken, $chatId, $message);

if ($response['ok']) {
    echo json_encode([
        'success' => true,
        'message' => '✅ Дякуємо! Вашу заявку отримано. Зв\'яжемося найближчим часом.'
    ]);
} else {
    // Логируем ошибку
    error_log('Telegram API Error: ' . json_encode($response));

    echo json_encode([
        'success' => false,
        'message' => 'Помилка відправки: ' . ($response['description'] ?? 'Невідома помилка')
    ]);
}
?>
