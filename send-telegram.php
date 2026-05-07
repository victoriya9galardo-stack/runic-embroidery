<?php
header('Content-Type: application/json; charset=utf-8');

// Конфигурация
$botToken = "8305382969:AAGaGuRbVHVFQo6E1UYQ4gwxzw_EznySkQw";
$chatId = "1109352803";

/**
 * Отправка сообщения в Telegram
 */
function sendTelegramMessage($botToken, $chatId, $message, $parseMode = 'HTML') {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => $parseMode
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        return ['ok' => false, 'description' => 'Помилка з\'єднання з Telegram'];
    }

    return json_decode($result, true);
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные формы
    $interest = $_POST['interest'] ?? '';
    $timing = $_POST['timing'] ?? '';
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $comment = $_POST['comment'] ?? '';

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
    $message .= "👤 <b>Ім'я:</b> " . htmlspecialchars($name) . "\n";
    $message .= "📞 <b>Контакт:</b> " . htmlspecialchars($contact) . "\n";
    $message .= "🎯 <b>Інтерес:</b> " . htmlspecialchars($interest) . "\n";
    $message .= "⏰ <b>Термін:</b> " . htmlspecialchars($timing) . "\n";

    if (!empty($comment)) {
        $message .= "💬 <b>Коментар:</b> " . htmlspecialchars($comment) . "\n";
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
        echo json_encode([
            'success' => false,
            'message' => 'Помилка відправки. Напишіть нам у Telegram: @Telegram'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Невірний метод запиту'
    ]);
}
?>
