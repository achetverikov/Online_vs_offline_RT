<?php
error_reporting(E_ALL&~E_NOTICE);

define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');


$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME, $db);
mysql_query("set names utf8");
mysql_query("set character_set_client='utf8'");
mysql_query("set character_set_results='utf8'");
mysql_query("set collation_connection='utf8_general_ci'");
setlocale(LC_ALL, 'ru_RU.UTF-8');
header("Content-type: text/html; charset=utf-8");

$tblPrefix = 'web_rt';
$testTitle = mysql_result(mysql_query('select optval from ' . $tblPrefix . '_options where optname="testTitle"'), 0, 0);

function mynl2br($text) {
    global $code1val, $code2val;
    return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />', "%code1val%" => $code1val, "%code2val%" => $code2val));
}

function nl2p($text) {
    if (!strstr($text, "\n")) {
        return $text;
    }
    $text = '<p>' .str_replace("\n", "</p><p>", $text). '</p>';

    $text = str_replace(array('<p' . $cssClass . '></p>', '<p></p>', "\r"), '', $text);

    return $text;
}

function add_quotes($str) {
    return "\"" . addslashes($str) . "\"";
}

function mysql_get_list($q, $field = "") {
    $res = q($q);
    $list = array();
    while ($row = mysql_fetch_assoc($res)) {
        if (!empty($field))
            $list[] = $row[$field];
        else
            $list[] = $row;
    }
    return $list;
}

function q($query) {
    $result = mysql_query($query);
    if (mysql_errno()) {
        $error = "MySQL error " . mysql_errno() . ": " . mysql_error() . "\n<br>When executing:<br>\n$query\n<br>";
        echo $error;
    }
    return $result;
}

?>