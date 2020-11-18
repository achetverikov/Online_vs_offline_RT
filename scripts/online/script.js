var img_num = 0;
var start = 0;
var react_dots = 0,timestart, timeend;
var trials = []

curTrial = {}
codesDots = {65: 'a', 83: 's', 68: 'd', 72: 'h', 74: 'j', 75: 'k'}
activeCodes=[]
labelsDots = []
elemSize = 90
full_answers = [];

$(document).ready(function() {
	
    paper.setup('main_canv');
    squares_group=new Group();
    info = new PointText({
        point: [view.center.x, view.center.y - 160],
        content: '',
        fontSize: 40,
        fontFamily: 'Arial',
        fontWeight: 'bold',
        justification: 'left',
        visible: 0
    });
    progress_bar_frame = new Shape.Rectangle({
        center:[view.center.x, 25],
        size:[view.getSize().width/2,8],
        fillColor:'white',
        strokeColor:'#aaaaaa',
        strokeWidth:0.5,
        visible:0
        })
    progress_bar = new Shape.Rectangle({
        center:[view.center.x, 25],
        size:[view.getSize().width/2,8],
        fillColor:'#aaaaaa',
        strokeWidth:0,
        visible:0
    })
    error_msg= new PointText({
        point: view.center,
        content: 'ОШИБКА',
        fontSize: 80,
        fontFamily: 'Arial',

        justification: 'center',
        color:'black',
        visible: 0
    });

    target_rectangle = new Shape.Rectangle({
        center: view.center,
        size: [elemSize / 2, elemSize / 2],
        visible: 0
    });
	generate_trial_list();
	$('#main_canv, #to_show').hide()
	
    $('#results').html(instr).show();
    
    $(document).bind(key_event, function(event) {
        var code = event.keyCode || event.which;

        if (code == 32) {
            $(document).unbind(key_event);
			$('#main_form').hide()
			$('#main_canv').show()
            start_trial();
        }
    });
});


function range(start, end)
{
    if (!end) {
        end = start
        start = 0
    }
    var array = new Array();
    for (var i = start; i < end; i++)
    {
        array.push(i);
    }
    return array;
}
function arr_diff(a1, a2)
{
    var a = [], diff = [];
    for (var i = 0; i < a1.length; i++)
        a[a1[i]] = true;
    for (var i = 0; i < a2.length; i++)
        if (a[a2[i]])
            delete a[a2[i]];
        else
            a[a2[i]] = true;
    for (var k in a)
        diff.push(parseInt(k));
    return diff;
}


function generate_trial_list() {
    testing = 0
    sets = [2, 4, 6]

    for (var set_i = 0; set_i < sets.length; set_i++) {
        var colors = []
        N = sets[set_i]
        for (var i = 0; i < 6; i++) {
            colors.push(360 * (parseInt(i) + 1) / 6)
        }
        if (N == 6) {
            keyList = ['a', 's', 'd', 'h', 'j', 'k']
            nReps = 4
        }
        else if (N == 4) {
            keyList = ['s', 'd', 'h', 'j']
            nReps = 6
        }
        else if (N == 2) {
            keyList = ['d', 'h']
            nReps = 12
        }
        if (testing) {
            nReps = 1
        }
        shapes = []
        helper_letters = []
        targetColors = shuffle(colors)
        targetPoss = range(N)

        nTotal = targetColors.length * nReps * targetPoss.length
        thisN = 0.0
        console.log(nTotal)
        blockN = 0.0

        for (var ti=0;ti<targetColors.length; ti++) {
            targetColor=targetColors[ti]
            blockN += 1
            nInBlock = 0;
            for (var j = 0; j < nReps; j++) {
                shuffle(targetPoss)
                for (var tp=0;tp<targetPoss.length; tp++) {
                    targetPos=targetPoss[tp]
                    thisN += 1
                    nInBlock += 1
                    curColors = arr_diff(colors, [targetColor])
                    shuffle(curColors)
                    
                    curColors.splice(targetPos, 0, targetColor)
                    curColors = curColors.slice(0,N)
                    trials.push({'thisN': thisN, 'set_size': N, 'curColors': curColors, 'targetColor': targetColor,
                        'targetPos': targetPos, 'nrep': j, 'blockN': blockN, 'nTotal': nTotal, 'nInBlock': nInBlock, 'keyList': keyList})
                }
            }
        }
    }
}
function start_trial() {
    if (img_num == 0) {

        $('#results').hide();
        //$('#to_show').show();
        view.draw()
        //$('#helper, #sub_helper').show();
        $(document).bind(key_event, function(event) {
            event.preventDefault();
            if (!react_dots)
                return;
            var code = event.keyCode || event.which;
            if (activeCodes.indexOf(code )!=-1) {
                squares_group.removeChildren();
                
                timeend = new Date();

                react_dots = 0;
                trials[img_num].rt=timeend.getTime()-timestart.getTime()
                trials[img_num].answer = codesDots[code];
                correct = labelsDots.indexOf(codesDots[code])==trials[img_num].targetPos?1:0;
                trials[img_num].correct=correct
                img_num++;
                if (correct)
                    start_trial()
                else
                    show_error_msg()
            }
        });
    }
    if (img_num == (trials.length)) {
        progress_bar.visible=0
        progress_bar_frame.visible=0
        view.draw()
        $('#to_show,#helper,#sub_helper').hide();
        $(document).unbind(key_event);
        img_num = -1;

        $('#to_show').hide().html('');
		$('#main_canv').hide()
		$('#main_form').show()
        $('#results').html('<input type="hidden" name="answers" value=\'' + $.toJSON(trials) + '\'>');
        $('#results').append('<p>Нажмите далее для продолжения</p>');
        $('#results').append('<p><input type="button" onclick="this.form.submit()"  value="Далее"></p>').show();
        return
    }
    error_msg.visible=0
    curTrial = trials[img_num]
    labelsDots=curTrial.keyList
    $.each(codesDots, function(key, val) {
        if (curTrial.keyList.indexOf(val) !== -1) {
            activeCodes.push(parseInt(key))

        }
    })

    if (curTrial.nInBlock == 1)
        show_block_info()
    else
        show_target()

}



function show_block_info() {
    progress_bar.visible=0
    progress_bar_frame.visible=0
    info.content = 'Число квадратов: ' + curTrial.set_size + '.\nБлок ' + curTrial.blockN + '. Цель: '
    info.point = [view.center.x - (info.getBounds().width / 2), view.center.y - 160]
    info.visible = 1
    target_rectangle.fillColor = {hue: curTrial.targetColor, saturation: 1, brightness: 1}
    target_rectangle.visible = 1
    view.draw()
    if (testing==0) setTimeout(show_target, 5000)
    else setTimeout(show_target, 50)
}
function show_target() {
    target_rectangle.visible = 1
    view.draw()
    info.visible = 0
    setTimeout(show_squares, 500)
}

function show_squares() {
    target_rectangle.visible=0
    progress_bar.bounds.width=progress_bar_frame.bounds.width*curTrial.thisN/curTrial.nTotal
    progress_bar.bounds.setCenterX(view.center.x)
    progress_bar_frame.bounds.setCenterX(view.center.x)
    progress_bar.visible=1
    progress_bar_frame.visible=1

    squares = []
    helpers = []

    for (var i = 0; i < curTrial.set_size; i++) {
        var rectangle = new Rectangle({
            center: [view.center.x + (parseInt(i) - curTrial.set_size / 2 + 0.5) * elemSize, view.center.y],
            size: [elemSize / 2, elemSize / 2]
        });

        squares[i] = new Path.Rectangle(rectangle);
        squares[i].fillColor = {hue: curTrial.curColors[i], saturation: 1, brightness: 1};
        helpers[i] = new PointText({
            point: [view.center.x + (parseInt(i) - curTrial.set_size / 2 + 0.5) * elemSize-10, view.center.y + 100],
            content: labelsDots[i],
            fontSize: 30,
            fontFamily: 'Arial',
            fontWeight: 'bold'
        });
    }

    squares_group.children=squares.concat(helpers)
    view.draw();
    react_dots = 1;
    timestart = new Date();
}

function show_error_msg(){
    error_msg.setPoint([view.center.x, view.center.y + error_msg.getBounds().height/2])
    error_msg.visible=1
    view.draw()
    setTimeout(start_trial,1000)
}