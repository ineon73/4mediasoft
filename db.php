<html>
<form method="POST" action="" enctype="multipart/form-data">
    <textarea name="text"></textarea>
    <input type="file" name="filename">
    <input type="submit"/>
</form>
</html>
<?php
echo "<form target=\"_blank\" method=\"POST\" action=\"detail.php\">    
    <input type=\"hidden\" name=\"id\" value=\"{$ip}\">
    <input type=\"submit\" value=\"Ранее загруженные тексты\"/>
</form>";
$text = isset($_POST['text']) ? $_POST['text'] : "";
$textFile = isset($_FILES['filename']['tmp_name']) ? $_FILES['filename']['tmp_name'] : "";
try {
    $host = 'localhost';
    $username = 'admin';
    $password = '0000';
    $dbname = 'text_info';
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
$sql = "INSERT INTO text_origin (id, text, result, date, ip, download) VALUES (NULL, :text, :result, NOW(), :ip, :download)";
$table1 = $db->prepare($sql);
$table1->bindParam(':text', $text);
$table1->bindParam(':download', $download);
$table1->bindParam(':result', $result);
$table1->bindParam(':ip', $ip);
$ip = getIp();
$sq1 = "INSERT INTO text_result (word, count, text_id) VALUES (:word, :count, :text_id)";
$table2 = $db->prepare($sq1);
$table2->bindParam(':text_id', $textId);
$table2->bindParam(':word', $word);
$table2->bindParam(':count', $count);
function getIp()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
    elseif (filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
    else $ip = $remote;

    return $ip;
}

function cleanText($text)
{
    $a = explode(' ', $text); //разбиваем строку
    $symbol = [',', '.', '!', ':', ';', '?', '...', '"', '—', ')', '('];
    $format = str_replace($symbol, '', $a); // очищаем слова в массиве от символов
    $format = array_diff($format, array('')); // удаляем пустые значения в массиве (лишние пробелы)
    foreach ($format as &$value) {
        $value = trim(mb_strtolower($value));
    }
    return $format;
}

function writeText($text)
{
    $result = count($text);
    $array1 = (array_count_values($text));
    $i = 0;
    while (file_exists("csv/file{$i}.csv")) {
        $i++;
    }
    $fp = fopen("csv/file{$i}.csv", 'w');
    foreach ($array1 as $key => $line) {
        $line = (array)$line;
        $key = (array)$key;
        $res = array_merge($key, $line);
        fputcsv($fp, $res);
    }
    file_put_contents("csv/file{$i}.csv", "Всего слов,{$result}", FILE_APPEND);
    fclose($fp);
    $arr = array("$result", "csv/file{$i}.csv", $array1);
    return $arr;
}

if (!empty($text)) {
    $link = writeText(cleanText($text));
    $result = $link[0];
    $download = $link[1];
    $array1 = $link[2];
    $table1->execute();
    $textId = $db->lastInsertId();
    foreach ($array1 as $key => &$line) {
        $word = $key;
        $count = $line;
        $table2->execute();
    }
}
if (!empty($textFile)) {
    $text = file_get_contents($textFile);
    $text = trim($text); //нужен для правильной работы двух следующих функций
    $a = mb_detect_encoding($text, "auto");
    $b = iconv("{$a}", "UTF-8//IGNORE", $text);
    $text = $b;
    $link = writeText(cleanText($text));
    $result = $link[0];
    $download = $link[1];
    $array1 = $link[2];
    $table1->execute();
    $textId = $db->lastInsertId();
    foreach ($array1 as $key => $line) {
        $word = $key;
        $count = $line;
        $table2->execute();
    }
}
?>

