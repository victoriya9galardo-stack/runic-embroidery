<?php
// Тестовий файл для перевірки підключення до Telegram

$botToken = "8305382969:AAGaGuRbVHVFQo6E1UYQ4gwxzw_EznySkQw";
$chatId = "1109352803";

echo "<h2>Тест підключення до Telegram Bot API</h2>";

// Проверка cURL
if (!function_exists('curl_init')) {
    echo "<p style='color:red;'>❌ cURL не встановлено на сервері!</p>";
    echo "<p>Зверніться до хостинг-провайдера для встановлення cURL.</p>";
    exit;
} else {
    echo "<p style='color:green;'>✅ cURL встановлено</p>";
}

// Тест getMe
echo "<h3>1. Перевірка токена бота (getMe):</h3>";
$url = "https://api.telegram.org/bot{$botToken}/getMe";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($result) {
    $data = json_decode($result, true);
    if ($data['ok']) {
        echo "<p style='color:green;'>✅ Токен правильний!</p>";
        echo "<pre>" . print_r($data['result'], true) . "</pre>";
    } else {
        echo "<p style='color:red;'>❌ Помилка: " . ($data['description'] ?? 'Unknown') . "</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Не вдалося підключитися до Telegram API</p>";
}

// Тест відправки повідомлення
echo "<h3>2. Тест відправки повідомлення:</h3>";
$testMessage = "🧪 Тестове повідомлення з RUNAVYSHYVANKA_bot\n\n" .
               "Час: " . date('d.m.Y H:i:s');

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $testMessage,
    'parse_mode' => 'HTML'
];

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

if ($result) {
    $data = json_decode($result, true);
    if ($data['ok']) {
        echo "<p style='color:green;'>✅ Повідомлення відправлено успішно!</p>";
        echo "<p>Перевірте ваш Telegram (ID: {$chatId})</p>";
    } else {
        echo "<p style='color:red;'>❌ Помилка відправки: " . ($data['description'] ?? 'Unknown') . "</p>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    }
} else {
    echo "<p style='color:red;'>❌ cURL помилка: " . $curlError . "</p>";
    echo "<p>HTTP код: " . $httpCode . "</p>";
}

echo "<hr>";
echo "<p><strong>Наступні кроки:</strong></p>";
echo "<ul>";
echo "<li>Якщо все OK - форма на сайті буде працювати</li>";
echo "<li>Якщо є помилки - зверніться до технічної підтримки хостингу</li>";
echo "<li>Після перевірки видаліть цей файл з сервера!</li>";
echo "</ul>";
?>
