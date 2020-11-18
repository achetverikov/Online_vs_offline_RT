<?php
include_once 'cnf.php';


while (list ( $n, $v ) = each($_REQUEST)) {
    $$n = & $_REQUEST[$n];
}

if ((!$op || $op == 1) && !$test_mode) {
    if (!is_numeric($age) || ($sex != 1 && $sex != 0)) {
        header('Location: test0.php?status=1&day=' . $day);
        die();
    }
    $ip = getenv("HTTP_X_FORWARDED_FOR");
    if (empty($ip) || $ip == "unknown") {
        $ip = getenv("REMOTE_ADDR");
    }
    $fieldnames = 'age, sex, ip, ref, monitor, refresh_rate, screen_size_x, screen_size_y, browser, browser_version, browser_major, os, os_version, ua, keyboard, pc, cpu, gpu, ram';
    $fieldnames_arr = preg_split('%, %', $fieldnames);
    $fields = compact($fieldnames_arr);
    $sql = array_map(function($k, $v) {
                return "$k='" . addslashes($v) . "'";
            }, array_keys($fields), array_values($fields));
    $q = "insert into " . $tblPrefix . "_users set " . join(',', $sql);
    mysql_query($q);
    $id = mysql_insert_id();

} elseif ($op == 2) {
    if (!$id || !is_numeric($id))
        return;
    $full_answers = json_decode(stripslashes($_REQUEST['answers']));
    foreach ($full_answers as $k => $answers) {
        $answers = (array) $answers;
        if (empty($answers))
            continue;
        $answers['curColors'] = implode(';', $answers['curColors']);
        $q = "insert into " . $tblPrefix . "_answers
		   (uid,thisN, set_size, curColors, targetColor, targetPos, nrep, blockN, nTotal, nInBlock,  rt, answer, correct) 
		   values (" . $id . ",'";
        $q.= implode("','", array_intersect_key($answers, array_flip(array("thisN", "set_size", "curColors", "targetColor", "targetPos", "nrep", "blockN", "nTotal", "nInBlock", "rt", "answer", "correct"))));
        $q.= "')";
        q($q);
    }

    q("update " . $tblPrefix . "_users set finished=1 where id=" . addslashes($id));

    header("Location: test_results.php?id=" . $id);
    die;
}

$res = q("select * from ${tblPrefix}_options");
while ($row = mysql_fetch_assoc($res)) {
    $$row['optname'] = $row['optval'];
}

$instr = $instr_main;

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>Тест <?= $testTitle; ?></title>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <meta property="og:image" content="<?= $testImg ?>"/> 


        <meta property="og:title" content="Тест <?= $testTitle ?>" /> 
        <!--[if IE]>
            <style type="text/css">
            #results, #to_show{padding:15px;}
            </style>
        <![endif]-->
		
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>

        <script language="javascript" type="text/javascript" src="jquery.json-2.2.min.js"></script>
        <script language="javascript" type="text/javascript" src="hsv_to_rgb.js"></script>
        <script language="javascript" type="text/javascript" src="paper-full.min.js"></script>
        <link href="style.css" rel="stylesheet" type="text/css" />

        <script language="javascript" type="text/javascript">
            var key_event = 'keydown';
            var codesDots = {37: 'left', 39: 'right'};
            var sets = [2, 4, 6];

            var instr = '<?= nl2p($instr_both) ?>'
            paper.install(window);

        </script>
        <script type="text/javascript" cavas='main_canv' src="script.js"></script>

    </head>

    <body align=center><canvas id="main_canv" resize></canvas>
    <form id='main_form' action="test1.php" method="POST" onsubmit='return false;'>
        <input type='hidden' name='op' value=2> <input type='hidden' name='id' value='<?php echo $id; ?>'>

        <table id='mtbl'>
            <tr>
                <td align='center'>

                    <div id='content'>
                        <table id="content_tb" align=center>
                            <tr><td id="helper"></td></tr>
                            <tr>
                                <td id='to_show'>
                                </td>
                            </tr>
                            <tr>
                                <td id='results'>
                                </td>
                            </tr>
                            <tr><td id="sub_helper"></td></tr>
                        </table>
                    </div>
                    <div id='log'></div>
                </td>
            </tr>
        </table>
    </form>

</body>

</html>