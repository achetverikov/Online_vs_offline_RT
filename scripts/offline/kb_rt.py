#!/usr/bin/env python2
# -*- coding: utf-8 -*-

from psychopy import visual, core, event, data, logging, gui,misc, monitors
from psychopy.tools.coordinatetools import cart2pol
import copy, codecs
import numpy #for maths on arrays
from numpy.random import random, shuffle #we only need these two commands from this lib

win = visual.Window([1024,768], units='deg', fullscr=True, useFBO=True, monitor='testMonitor',color=(1, 1, 1))

elemSize = 3

expName='kb_rt'

expInfo = {'participant':'', 'age':'', 'gender':''}
dlg = gui.DlgFromDict(dictionary=expInfo, title=expName)
if dlg.OK == False: core.quit()	 # user pressed cancel

expInfo['date'] = data.getDateStr()	 # add a simple timestamp
expInfo['expName'] = expName

# Setup filename for saving
filename = 'data/%s_%s_%s' %(expInfo['participant'], expName, expInfo['date'])

# An ExperimentHandler isn't essential but helps with data saving
thisExp = data.ExperimentHandler(name=expName, version='',
	extraInfo=expInfo, runtimeInfo=None,
	originPath=None,
	savePickle=True, saveWideText=True,
	dataFileName=filename)
#save a log file for detail verbose info
logFile = logging.LogFile(filename+'.log', level=logging.EXP)
logging.console.setLevel(logging.WARNING)  # this outputs to the screen, not a file

endExpNow = False  # flag for 'escape' or other condition => quit the exp

#trialList=data.createFactorialTrialList({'targetColor':colors, 'targetPos':range(N)})
cross = visual.TextStim(win=win, text='+', height=1, color=(-1,-1,-1))
target = visual.Rect(win=win, units='deg',lineWidth=0,fillColor=[180,0,0],
						  width=elemSize/2,height=elemSize/2, pos=[0,0], fillColorSpace='hsv')
error = visual.TextStim(win=win, text=u'ОШИБКА', height=3, color='black')
progress_bar_frame = visual.Rect(win, width=1, height=0.02, units=u'norm', pos=[0, 0.95], fillColor=None, lineColor='#aaaaaa',autoLog=False)
progress_bar = visual.Rect(win, width=1, height=0.02, units='norm', pos=[0, 0.95], fillColor='#aaaaaa',lineColor='#aaaaaa',autoLog=False)
info = visual.TextStim(win=win, text=u'Блок 1', height=1,pos=[0,5], color='black')
wait_msg= visual.TextStim(win=win, text=u'Подождите 5 секунд', height=1,pos=[0,-5], color='black')

f = codecs.open("instr.txt", "r", encoding='utf-8')

try:
	f = codecs.open("instr.txt", "r", encoding='utf-8')
	try:
		instrContent = [f.read()]		 
	finally:
		f.close()
except IOError:
	pass

instr_text = visual.TextStim(win=win, name='instr_text',
							 text=instrContent[0], color='black',
							 units=u'norm', pos=[0,0], height=0.05, wrapWidth=1.8)

instr_text.draw()
win.flip()

keys=event.waitKeys(keyList=['space'])
timer=core.Clock()
sets=[2,4,6]
for N in sets:

	colors=[360*(i+1)/6 for i in range(6)]
	if N==6:
		keyList=['a', 's', 'd', 'h', 'j', 'k']
		nReps=4
	elif N==4:
		keyList=['s', 'd', 'h', 'j']
		nReps=6
	elif N==2:
		keyList=[ 'd', 'h']
		nReps=12

	#globForm = visual.ElementArrayStim(win, nElements=N,sizes=elemSize/2,sfs=0,elementTex='sqr',elementMask='none',
	#													 xys = [[(i-N/2+0.5)*elemSize,0] for i in range(N)],
	#													 colors=[[i,1,1] for i in colors],colorSpace='hsv')
	
	shapes=[]
	helper_letters=[]
	for i in range(N):
		shapes.append(visual.Rect(win=win, units='deg',lineWidth=0,fillColor=colors[i],
							width=elemSize/2,height=elemSize/2, pos=[(i-N/2+0.5)*elemSize,0], fillColorSpace='hsv'))
		helper_letters.append(visual.TextStim(win=win, text=keyList[i],pos=[(i-N/2+0.5)*elemSize,-2], units='deg', height=1, color=(-1,-1,-1)))
	
	targetColors=copy.deepcopy(colors)
	shuffle(targetColors)
	targetPoss=range(N)

	nTotal=len(targetColors)*nReps*len(targetPoss)
	thisN=0.0
	print nTotal
	blockN=0.0
	
	for targetColor in targetColors:
		blockN+=1
		info.setText(u'Число квадратов: %i.\nБлок %i. Цель: ' % (N, blockN))
		info.draw()
		target.setFillColor([targetColor,1,1])
		target.draw()
		
		if blockN==1:
			win.flip()
			core.wait(5)
		else:
			wait_msg.draw()
			win.flip()
			core.wait(5)
		for j in range(nReps):
			timer.reset()
			shuffle(targetPoss)
			for targetPos in targetPoss:
				thisN+=1
				progress_bar.setWidth(thisN/nTotal)
				progress_bar_frame.draw()
				progress_bar.draw()
				
				curColors=list(set(colors)-set([targetColor]))
				shuffle(curColors)
				thisExp.addData('distractor_colors',curColors)
				curColors=curColors[:N]
				curColors=curColors[:targetPos]+[targetColor]+curColors[targetPos:]
				for i in range(N):
					shapes[i].setFillColor([curColors[i],1,1])
				#globForm.setColors([[i,1,1] for i in curColors])
				
				target.setFillColor([targetColor,1,1])
				target.draw()
				win.flip()
				core.wait(0.5)
				progress_bar_frame.draw()
				progress_bar.draw()
				for i in range(N):
					shapes[i].draw()
					helper_letters[i].draw()
				win.flip()

				fTime=timer.getTime()
				resp = event.waitKeys(keyList=keyList+['escape'], timeStamped=timer)
				
				key,rTime = resp[0]
				rt=rTime-fTime

				if key=="escape":
					endExpNow = True
				else:
					if keyList.index(key)==targetPos:
						correct=1
					else:
						correct=0
					if correct==0:
						error.draw()
						win.flip()
						core.wait(1)
					
					thisExp.addData('target_pos',targetPos)
					thisExp.addData('thisN',thisN)
					thisExp.addData('set_size',N)
					thisExp.addData('target_color',targetColor)
					thisExp.addData('nrep',j)
					
					thisExp.addData('correct',correct)
					thisExp.addData('answer',key)
					thisExp.addData('rt',rt)
					
				
				if endExpNow or event.getKeys(keyList=["escape"]):
					core.quit()
				thisExp.nextEntry()
		

#-------Ending Routine "trial"-------
info.setText(u'Спасибо за участие!')

info.draw()
win.flip()
core.wait(5)
win.close()
core.quit()
