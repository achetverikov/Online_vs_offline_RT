<?php
require 'cnf.php';

while (list ( $n, $v ) = each($_REQUEST)) {
    $$n = & $_REQUEST[$n];
}

if (!is_numeric($id))
    return;

$res = mysql_query("select * from ${tblPrefix}_options");
while ($row = mysql_fetch_assoc($res)) {
    $$row['optname'] = $row['optval'];
}
$group_type = mysql_get_list('select group_id from  ' . ${tblPrefix} . '_users where id=' . $id);
$q = 'select set_size, round(avg(correct)*100)  correct, round(avg(if(correct=1,rt,NULL))) rt from ' . ${tblPrefix} . '_answers a where  a.uid=' . $id . ' group by set_size  WITH ROLLUP';

$userCorrect = mysql_get_list($q);
$q = 'select set_size, round(avg(correct)*100) correct, round(avg(if(correct=1,rt,NULL))) rt from ' . ${tblPrefix} . '_answers a, ' . ${tblPrefix} . '_users u where u.id=uid&&  finished && included  group by set_size WITH ROLLUP';
$avgCorrect = mysql_get_list($q);

$data_for_chart = mysql_get_list('select uid, browser, set_size, avg(correct)*100 x, round(avg(if(correct=1,rt,NULL))) y from ' . ${tblPrefix} . '_answers a, ' . ${tblPrefix} . '_users u where u.id=uid group by uid');

$browser_colors = array('Firefox' => 'red', 'Safari' => 'blue', 'Opera' => 'brown', 'Chrome' => 'green', 'IE' => 'darkblue', 'Other' => 'gray');
$arr_for_json = array();

foreach ($data_for_chart as $key => $value) {
    if (in_array($value['browser'], array_keys($browser_colors)))
        $data_for_chart[$key]['key'] = $value['browser'];
    else
        $data_for_chart[$key]['key'] = 'Other';
    }
foreach (array_keys($browser_colors) as $v) {
    $filteredItems = array_filter($data_for_chart, function($elem) use($v) {
                return $elem['key'] == $v;
            });
    $arr_for_json[] = array('key' => $v, 'values' => array_values($filteredItems));
}


?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <title>Тест <?= $testTitle; ?></title>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <meta property="og:image" content="<?= $testImg ?>"/> 


        <meta property="og:title" content="Тест <?= $testTitle ?>" /> 


        <style type="text/css">

            #content,body,#mtbl, #mtbl tr,#main_form{vertical-align:middle;height:100%;padding:0;margin:0}
            #results table{width:800px;text-align:left; }
            #content {margin-top:40px; overflow:visible; text-align: left !important}

            * {
                font-family:Verdana, sans-serif;
                font-size-adjust:none;
                font-stretch:normal;
                font-variant:normal;
                line-height:1.25em; /* 16×1.125=18 */
            }
            #spaced_table td,#spaced_table th{
                padding:7px;
            }
            #spaced_table{
                border-bottom:1px solid black
            }
            #spaced_table th{
                border-top:1px solid black;
                border-bottom:1px solid black
            }
            #results {
                width:600px;
            }
            #results p, textarea{
                font-size:13px;
                text-align:justify
            }
            #results p{
                margin: 1.25em 0 1.25em 0;
            }
            .chart{
                border: 10px solid #EAEAEA;
                margin: 10px 0;
            }
            #vkb{
                margin-bottom:5px;
            }
            .atc_l{
                font-size:10px;
            }
            #chart{height: 400px}
        </style>
        <!--[if IE]>
        <style type="text/css">
        #results, #to_show{padding:15px;}
        </style>
        <![endif]-->

        <link href="style.css" rel="stylesheet" type="text/css" />
        <link href="nv.d3.css" rel="stylesheet" type="text/css" />
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <script src="nv.d3.min.js" charset="utf-8"></script>

        <script language="javascript">

            nv.addGraph(function() {
                var chart = nv.models.scatterChart()
                        .showDistX(true)
                        .showDistY(true)
                        .color(d3.scale.category10().range());

                chart.tooltipContent(function(key) {
                    return '<h3>' + key + '</h3>';
                });

                chart.xAxis.axisLabel('Точность ответов (%)').tickFormat(d3.format('.0f'));
                chart.yAxis.axisLabel('Время ответов (мс)').tickFormat(d3.format('.0f'));
                chart.forceY([300, 1200]);
                chart.forceX([75, 100]);
                var jsonCircles = <?= json_encode($arr_for_json); ?>;
                
                d3.select('#chart svg')
                        .datum(jsonCircles)
                        .transition().duration(500)
                        .call(chart);

                var yrange = chart.yAxis.scale().domain();
                nv.utils.windowResize(chart.update);
                return chart;
            });



        </script>

    </head>

    <body align=center>
        <table id='mtbl'>
            <tr>
                <td align=center>

                    <div id='content'>

                        <p><b>Тест закончен, большое спасибо за участие.</b></p>
                        <p>Ваша точность (в процентах от числа ответов) и время реакции (в миллисекундах) и средняя точность и среднее время реакции по всем участникам показаны в таблице ниже. </p>
                        <table id='spaced_table' cellspacing='0'><tr><th>Число квадратов</th><th>Ваша точность</th><th>Ваше время реакции</th><th>Средняя точность</th><th>Среднее время реакции</th></tr><?php
                            foreach ($avgCorrect as $k => $v) {
                                if ($k > 2) {
                                    $v['set_size'] = 'В среднем';
                                }
                                echo '<tr><td>' . $v['set_size'] . '</td><td>' . $userCorrect[$k]['correct'] . '</td><td>' . $userCorrect[$k]['rt'] . '</td><td>' . $v['correct'] . '</td><td>' . $v['rt'] . '</td></tr>';
                            }
                            ?> 
                        </table>


                        <p>Обычно с увеличением сложности задачи (чем больше число квадратов, тем сложнее) люди начинают реагировать медленнее. Вы также можете сравнить свои ответы со средними значениями, полученными по данным других участников. Чем больше точность и ниже время реакции, тем лучше вы справились с заданием. А на графике ниже вы можете сравнить данные по различным браузерам. Понятно, что браузер - только один из параметров, который для нас важен, когда мы проанализруем все данные, то обязательно их опубликуем. Пока же, вы можете попробовать на глаз оценить, есть ли какие-то систематические различия в оценке времени реакции при использовании различных браузеров.</p>
                        <div id='chart'>
                            <svg></svg>
                            <!-- /the chart goes here -->
                        </div>
                        <div id='vkb'></div>
                        



                    </div>
                    <div id='log'></div>
                </td>
            </tr>
        </table>

    </body>

</html>
