<table cellspacing="0" border="1" width="100%">
    <thead>
    <tr>
        <th>Текст
        </th>
        <th>Слово
        </th>
        <th>Повторений
        </th>
    </tr>
    </thead>
    <tbody>

<?php
$id = isset($_POST['id']) ? $_POST['id'] : "";
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
$sql = "SELECT word, count FROM text_result WHERE text_id=$id";
$sq = "SELECT text FROM text_origin WHERE id=$id";
$tt = $db->query($sq)->fetch(PDO::FETCH_ASSOC);
$tt = implode(" ", $tt);
echo "<td rowspan='65534'>" . "{$tt}" . "</td>";
foreach ($db->query($sql) as $row) {
    echo "<tr><td>" . $row['word'] . "</td>" .
        "<td>" . $row['count'] . "</td>" .
        "</tr>";
}
echo "</tbody>";