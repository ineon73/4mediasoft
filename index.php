<html>
<form method="POST" action="" enctype="multipart/form-data">
    <textarea name="text"></textarea>
    <input type="file" name="filename">
    <input type="submit"/>
</form>
</html>

<?php
$textArea = $_POST[text];
$textFile = file_get_contents($_FILES['filename']['tmp_name']);
if (!empty($textArea)) {
    writeText(cleanText($textArea));
}
if (!empty($textFile)) {
    writeText(cleanText($textFile));
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

function writeText($format)
{
    $result = count($format);
    $list = (array_count_values($format));
    $i = 0;
    while (file_exists("csv/file{$i}.csv")) {
        $i++;

    }

    $fp = fopen("csv/file{$i}.csv", 'w');
    foreach ($list as $key => $line) {
        $line = (array)$line;
        $key = (array)$key;
        $res = array_merge($key, $line);
        fputcsv($fp, $res);
    }
    file_put_contents("csv/file{$i}.csv", "Всего слов,{$result}", FILE_APPEND);
    fclose($fp);
}

?>