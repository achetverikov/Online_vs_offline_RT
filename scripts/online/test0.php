<?php
error_reporting(E_ALL & ~E_NOTICE);

require ('cnf.php');

while (list($n, $v) = each($_REQUEST)) {
    $$n = trim($v);
}
?>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <script language="javascript" type="text/javascript" src="http://code.jquery.com/jquery-1.4.1.min.js">
    </script>
    <script language="javascript" type="text/javascript" src="jquery-validate/jquery.validate.pack.js"></script>
    <script type="text/javascript" src="ua-parser.min.js"></script>

    <script language="javascript" type="text/javascript">

        var isArray = function(obj) {
            return Object.prototype.toString.call(obj) === "[object Array]";
        },
                getNumWithSetDec = function(num, numOfDec) {
            var pow10s = Math.pow(10, numOfDec || 0);
            return (numOfDec) ? Math.round(pow10s * num) / pow10s : num;
        },
                getAverageFromNumArr = function(numArr, numOfDec) {
            if (!isArray(numArr)) {
                return false;
            }
            var i = numArr.length,
                    sum = 0;
            while (i--) {
                sum += numArr[ i ];
            }
            return getNumWithSetDec((sum / numArr.length), numOfDec);
        },
                getVariance = function(numArr, numOfDec) {
            if (!isArray(numArr)) {
                return false;
            }
            var avg = getAverageFromNumArr(numArr, numOfDec),
                    i = numArr.length,
                    v = 0;

            while (i--) {
                v += Math.pow((numArr[ i ] - avg), 2);
            }
            v /= numArr.length;
            return getNumWithSetDec(v, numOfDec);
        },
                getStandardDeviation = function(numArr, numOfDec) {
            if (!isArray(numArr)) {
                return false;
            }
            var stdDev = Math.sqrt(getVariance(numArr, numOfDec));
            return getNumWithSetDec(stdDev, numOfDec);
        };

        (function() {
            var lastTime = 0;
            var vendors = ['webkit', 'moz'];
            for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
                window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
                window.cancelAnimationFrame =
                        window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
            }

            if (!window.requestAnimationFrame)
                window.requestAnimationFrame = function(callback, element) {
                    var currTime = new Date().getTime();
                    var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                    var id = window.setTimeout(function() {
                        callback(currTime + timeToCall);
                    },
                            timeToCall);
                    lastTime = currTime + timeToCall;
                    return id;
                };

            if (!window.cancelAnimationFrame)
                window.cancelAnimationFrame = function(id) {
                    clearTimeout(id);
                };
        }());



        var t = [];
        var fps_glob;
        function animate(now) {
            n = 100;
            t.push(now);
            if (t.length > n) {
                var t0 = t[0];
                tdiffs = [];
                for (i = 1; i < t.length; i++) {
                    tdiffs.unshift(t[i] - t[i - 1])
                }
                var fps = Math.floor(1000 * n / (now - t0));
                fps_glob = fps;
                var fps2 = getAverageFromNumArr(tdiffs, 1)
                var fps_sd = getStandardDeviation(tdiffs, 1)
                var low_ci = fps2 - 1.96 * fps_sd
                var high_ci = fps2 + 1.96 * fps_sd
                tdiffs2 = [];
                for (i = 0; i < tdiffs.length; i++) {
                    if (tdiffs[i] > low_ci && tdiffs[i] < high_ci) {
                        tdiffs2.push(tdiffs[i])
                    }
                }
                var fps3 = 1000 / getAverageFromNumArr(tdiffs2, 1)
                console.log(fps)
                console.log(fps2)
                console.log(fps3)
                $('#refresh_rate').val(Math.floor(fps3));

                t = [];
                return;
            }
            window.requestAnimationFrame(animate);
        }
        ;
        //window.requestAnimationFrame(animate);

        $(document).ready(function() {
            $('#browser').val($.ua.browser.name);
            $('#browser_version').val($.ua.browser.version);
            $('#browser_major').val($.ua.browser.major);
            $('#os').val($.ua.os.name);
            $('#os_version').val($.ua.os.version);
            $('#ua').val($.ua.get());
            $('#screen_size_x').val(screen.width);
            $('#screen_size_y').val(screen.height);

            //$('#refresh_rate').val(fps_glob);
            setTimeout(window.requestAnimationFrame, 10, animate)
            $('.error#right').html('<img src="../lib/errorArrow_right.png">');
            $('.error#bottom').html('<img src="../lib/errorArrow_bottom.png">');
            $('#form1').validate({
                submitHandler: function(form) {
                    $('#form1').attr("action", "test1.php");
                    form.submit();
                }
            }
            );
        });

        var parser = new UAParser();
        console.log(parser.getResult());


    </script>
    <link href="style.css" rel="stylesheet" type="text/css" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

    <meta property="og:description" content="В этом эксперименте мы хотим понять, насколько большой разброс в данные по времени реакции в онлайн-исследованиях вносят технические различия: браузер, операционная система, частота обновления монитора, процессор, и т.п. Вам нужно будет находить квадрат определенного цвета среди других квадратов. По итогам вы получите информацию о том, как точность и скорость ваших ответов соотносятся с ответами других участников."/> 


    <!-- Latest compiled and minified JavaScript -->
    <title>Эксперимент "<?= $testTitle; ?>"</title>
    <style>
        label {
            font-weight: normal !important;
        }
        label.col-sm-4 {
            text-align: right;
        }
    </style>
</head>
<body>


    <table cellpadding=0 cellspacing=0 width=100% height=100%>
        <tr><td align=center valign=middle>
                <table cellpadding=0 cellspacing=0 width=600 height=100%>
                    <tr><td align=left valign=middle>
                            <form id="form1" method="post" class="form-horizontal"  action="test0.php">
                                <input type="hidden" name="op" value="1">
                                <?php
                                if ($status == 1)
                                    echo '<font color="red">Необходимо заполнить все поля.</font><br>';
                                ?>
                                <div class="form-group">
                                    <p>Здравствуйте, уважаемый участник исследования!</p>
                                    <p>В этом эксперименте мы хотим понять, насколько большой разброс в данные по времени реакции в онлайн-исследованиях вносят технические различия: браузер, операционная система, частота обновления монитора, процессор, и т.п. </p>

                                    <p>Вам нужно будет находить квадрат определенного цвета среди других квадратов. По итогам вы получите информацию о том, как точность и скорость ваших ответов соотносятся с ответами других участников. А когда мы соберем достаточную статистику, то опубликуем результаты исследования в сообществе <a href='https://vk.com/cognitive_u'>Cognitive U - популярная когнитивная психология</a>. На участие в исследовании вам потребуется примерно 10 минут. Пожалуйста, переведите ваш браузер на время исследования в полноэкранный режим, нажав клавишу <b>F11</b>.</p>

                                    <p>Участие в данном исследовании приветствуется, но является абсолютно добровольным. Если по каким-то причинам вы почувствуете себя некомфортно во время исследования, вы можете прекратить свое участие в нем в любой момент.</p>
                                    <p>
                                        <input type='hidden' name="ref" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>">
                                        Для участия в исследовании укажите, пожалуйста, свои данные (если вы не знаете что-то, например, частоту обновления экрана или модель процессора - пропустите этот пункт):
                                    </p>

                                </div>
                                <div class="form-group">
                                    <label class='col-sm-4 ' for="age">Возраст </label>
                                    <div class='col-sm-8'><input name="age" class="required" id="age" size=6> <label for="age" class="error" id="right"></label> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sex" class='col-sm-4 control-label'>Пол </label>
                                    <div class='col-sm-8'>
                                        <label class="radio-inline"> <input class="required" type=radio name="sex" value=0>М</label>
                                        <label class="radio-inline"><input type=radio name="sex" value=1>Ж </label>
                                        <label for="sex" class="error" id="right"></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class='col-sm-4 control-label'>Монитор</label>
                                    <div class='col-sm-8'>
                                        <label class="radio-inline">
                                            <input type='radio' class="required" name='monitor' value='LCD'>ЖК (LCD)
                                        </label>
                                        <label class="radio-inline">
                                            <input type='radio' value='CRT' name='monitor'>ЭЛТ (CRT)
                                        </label>
                                        <label class="radio-inline">
                                            <input type='radio' value='other' name='monitor'>Другое
                                        </label>
                                        <label for="monitor" class="error" id="right"></div>
                                </div>
                                <div class="form-group">

                                    <label for="refresh_rate" class='col-sm-4'>Частота обновления экрана (Hz)</label>
                                    <div class='col-sm-8'> <input type='text' name='refresh_rate' id='refresh_rate' size=3> </div>
                                </div>
                                <div class="form-group">
                                    <label for="screen_size_x" class='col-sm-4'>Ширина экрана, в пикселях</label>
                                    <div class='col-sm-8'> <input type='text' id='screen_size_x' name='screen_size_x' size=6/><br></div>
                                </div>
                                <div class="form-group">
                                    <label for="screen_size_y" class='col-sm-4'>Высота экрана, в пикселях</label>
                                    <div class='col-sm-8'> <input type='text' id='screen_size_y' name='screen_size_y' size=6/>
                                        <input type='hidden' id='browser' name='browser'/>
                                        <input type='hidden' id='browser_version' name='browser_version'/>
                                        <input type='hidden' id='browser_major' name='browser_major'/>
                                        <input type='hidden' id='os' name='os'/>
                                        <input type='hidden' id='os_version' name='os_version'/>

                                        <input type='hidden' id='ua' name='ua' size=250/><br>
                                        <small>(эти данные были взяты автоматически из данных, сообщаемых браузером, пожалуйста, перепроверьте их)</small></div>
                                </div>

                                </fieldset>

                                <div class="form-group">

                                    <label class='col-sm-4 control-label'>Компьютер</label>

                                    <div class='col-sm-8'><label class="radio-inline">
                                            <input type='radio' class="required" name='pc' value='desktop'> Настольный
                                        </label>
                                        <label class="radio-inline">
                                            <input type='radio' value='laptop' name='pc'> Ноутбук
                                        </label>
                                        <label for="pc" class="error" id="right"></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for='cpu' class='col-sm-4'>Процессор</label>
                                    <div class='col-sm-8'> <input type='text' name='cpu' id='cpu' size=30> <br><small>(например, Intel Core i3 4330 или AMD A4 Richland)</small></div>
                                </div>
                                <div class="form-group"><label  class='col-sm-4' for='ram'>Объем оперативной памяти (GB)</label>
                                    <div class='col-sm-8'><input type='text' name='ram' id='ram' size=30> <br><small> (название процессора и объем оперативной памяти под Windows можно узнать, щелкнув правой кнопкой на "Мой компьютер" и выбрав "Свойства") </small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for='gpu' class='col-sm-4'>Видеокарта</label>
                                    <div class='col-sm-8'><input type='text' name='gpu' id='gpu' size=30> <br><small>(например, ATI Radeon 4650 или NVIDIA GeForce GTX 560 Ti)</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for='keyboard' class='col-sm-4'>Клавиатура</label>
                                    <div class='col-sm-8'><input type='text' name='keyboard' id='keyboard' size=30> <br><small>(например, Logitech K270 или "встроенная")</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input  type="checkbox" id="ua_agree" name="ua_agree" checked value="1"> Я согласен с тем, что данные о моем браузере и операционной системе будут взяты из поля User Agent, передаваемого браузером.</label>
                                </div>

                                <div id="errorCont"></div>

                                <div style='text-align:center'><input class="button" type="submit" value="Продолжить"><br><br><br></div>

                            </form>
                        </td></tr></table>
            </td></tr></table>

</body>

