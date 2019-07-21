<table cellspacing="0" border="1" width="100%">
    <thead>
    <tr>
        <th>Дата
        </th>
        <th>Текст
        </th>
        <th>Всего слов
        </th>
        <th>Детали
        </th>
        <th>Скачать
        </th>
    </tr>
    </thead>
    <tbody>
<?php
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
$ip = getIp();
$sql = "SELECT *  FROM text_origin WHERE ip LIKE '$ip'";
foreach ($db->query($sql) as $row) {
    echo "<tr><td>" . $row['date'] . "</td>" .
        "<td>" . $row['text'] . "</td>" .
        "<td>" . $row['result'] . "</td>" .
        "<td>" . "<form target=\"_blank\" method=\"POST\" action=\"info.php\">    
    <input type=\"hidden\" name=\"id\" value=\"{$row['id']}\">
    <input type=\"submit\" value=\"Посмотреть детали\"/>
</form>" . "</td>" .
        "<td><a href=\"{$row['download']}\" download> Скачать файл</a></td></tr>";
}
echo "</tbody>";
