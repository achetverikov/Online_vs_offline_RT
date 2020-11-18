#!/usr/bin/env python2
# -*- coding: utf-8 -*-

from psychopy import visual, core, event, data, logging, gui
from psychopy.tools.coordinatetools import cart2pol
import copy, codecs
import numpy #for maths on arrays
from numpy.random import random, shuffle #we only need these two commands from this lib

win = visual.Window([1024,768], units='deg', fullscr=True, useFBO=True, monitor='testMonitor',color=(1, 1, 1))

import pyxid
pyxid.use_response_pad_timer = True

devices = pyxid.get_xid_devices()
try:
	dev = devices[0]
	if not dev.is_response_device():
		raise
except:
	print 'Response box not connected'
	exit()
	
elemSize = 3

expName='cedrus_rt'

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
dev.reset_base_timer()
testing=0
sets=[2,4,6]
for N in sets:

	colors=[360*(i+1)/6 for i in range(6)]
	if N==6:
		keyList=['a', 's', 'd', 'h', 'j', 'k']
		cedrusKeyList=[0,1,2,5,6,7]
		nReps=4
	elif N==4:
		keyList=['s', 'd', 'h', 'j']
		cedrusKeyList=[1,2,5,6]
		nReps=6
	elif N==2:
		keyList=[ 'd', 'h']
		cedrusKeyList=[2,5]
		nReps=12
	if testing:
		nReps=1

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
			shuffle(targetPoss)
			dev.reset_base_timer()

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
				
				target.setFillColor([targetColor,1,1])
				target.draw()
				win.flip()
				
				core.wait(0.5)
				progress_bar_frame.draw()
				progress_bar.draw()
				
				dev.poll_for_response()
				while dev.response_queue_size()>0:
					dev.poll_for_response()
					dev.get_next_response()
					dev.clear_response_queue()
					
				for i in range(N):
					shapes[i].draw()
					helper_letters[i].draw()
				win.flip()
				
				dev.reset_rt_timer()
				dev.clear_response_queue()

				while True:
					dev.poll_for_response()
					if dev.response_queue_size() > 0:
						response = dev.get_next_response()
						if response['pressed'] and response['key'] in cedrusKeyList:
							rTimeCed=response['time']
							key=response['key']
							break
				
				resp = event.getKeys(keyList=['escape'], timeStamped=False)
				if len(resp)>0:
					endExpNow = True
				
				if cedrusKeyList.index(key)==targetPos:
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
				thisExp.addData('rt',rTimeCed)
				
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
