+  1 <?php
+  2 
+  3 // Конфигурация
+  4 $botToken = "8305382969:AAGaGuRbVHVFQo6E1UYQ4gwxzw_EznySkQw";
+  5 $chatId = "1109352803";
+  6 
+  7 /**
+  8  * Отправка сообщения в Telegram
+  9  * 
+ 10  * @param string $botToken Токен бота
+ 11  * @param string $chatId ID чата
+ 12  * @param string $message Текст сообщения
+ 13  * @param string $parseMode Режим парсинга (HTML, Markdown, MarkdownV2)
+ 14  * @return array Ответ от Telegram API
+ 15  */
+ 16 function sendTelegramMessage($botToken, $chatId, $message, $parseMode = 'HTML') {
+ 17     $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
+ 18     
+ 19     $data = [
+ 20         'chat_id' => $chatId,
+ 21         'text' => $message,
+ 22         'parse_mode' => $parseMode
+ 23     ];
+ 24     
+ 25     $options = [
+ 26         'http' => [
+ 27             'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
+ 28             'method'  => 'POST',
+ 29             'content' => http_build_query($data)
+ 30         ]
+ 31     ];
+ 32     
+ 33     $context  = stream_context_create($options);
+ 34     $result = file_get_contents($url, false, $context);
+ 35     
+ 36     return json_decode($result, true);
+ 37 }
+ 38 
+ 39 // Пример использования
+ 40 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
+ 41     // Получение сообщения из POST-запроса
+ 42     $message = $_POST['message'] ?? 'Тестовое сообщение от бота RUNAVYSHYVANKA_bot';
+ 43     
+ 44     $response = sendTelegramMessage($botToken, $chatId, $message);
+ 45     
+ 46     if ($response['ok']) {
+ 47         echo json_encode([
+ 48             'status' => 'success',
+ 49             'message' => 'Сообщение успешно отправлено',
+ 50             'response' => $response
+ 51         ]);
+ 52     } else {
+ 53         echo json_encode([
+ 54             'status' => 'error',
+ 55             'message' => 'Ошибка отправки сообщения',
+ 56             'response' => $response
+ 57         ]);
+ 58     }
+ 59 } else {
+ 60     // GET-запрос - отправка тестового сообщения
+ 61     $testMessage = "Привет! Это тестовое сообщение от бота RUNAVYSHYVANKA_bot\n\n" .
+ 62                    "Время: " . date('Y-m-d H:i:s');
+ 63     
+ 64     $response = sendTelegramMessage($botToken, $chatId, $testMessage);
+ 65     
+ 66     if ($response['ok']) {
+ 67         echo "✅ Сообщение успешно отправлено!";
+ 68     } else {
+ 69         echo "❌ Ошибка: " . ($response['description'] ?? 'Неизвестная ошибка');
+ 70     }
+ 71 }
+ 72 
+ 73 ?>
