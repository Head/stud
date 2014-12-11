#!/usr/bin/python
# -*- coding: utf-8 -*-


import random
import math
import numpy as np
import matplotlib.pyplot as plt
from scipy import stats
import md5



# INIT OF GENERATORS
# gauss for age
mu_age, sigma_age = 45, 20
# degree, art interested
xk = np.arange(2)
pk = (0.3, 0.7)
custm = stats.rv_discrete(name='custm', values=(xk, pk))

# Read aritst csv file
artist_array = [map(lambda x: x.replace("\r\n",""),line.split(',')) for line in open('query.csv')]


def clamp(minval,maxval,value):
    return max(minval, min(value, maxval))

# Open file for writing example user data writing
f = open('query_poll_md5.csv','w')
# Write csv header
f.write("age, gender, art, degree, artisturi, answer \r\n")

DUMMY_ARTISTS = 10000
REALARTIST_P_DARTIST = 10

counter = 0.

for e in range(DUMMY_ARTISTS):


    if e % 100 == 0:
        print (counter / (DUMMY_ARTISTS * REALARTIST_P_DARTIST)) * 100


    # calculate random age
    age = int(random.gauss(mu_age, sigma_age))
    # clamp the age
    age = clamp(8,100,age)

    # generate gender
    gender = "m" if (random.uniform(0,1)>0.5) else "w"
    # degree - 30% Berufsausbildung / 70% Hochschulabschluss
    degree = "b" if (custm.rvs()==0) else "h"
    # art - interessiert in Kunst: 30% ja / 70% nein
    art = "ja" if (custm.rvs()==0) else "nein"

    # pick a random artist
    for a in range(REALARTIST_P_DARTIST):
        counter += 1.
        artist_index = random.randint(1,len(artist_array)-1)
        artist_picked = artist_array[artist_index]
        artist_uri = artist_picked[0]
        artist_paints = int(artist_picked[-1])
        artist_influenced = int(artist_picked[-2])
        artist_abstract = int(int("0"+artist_picked[-3])/100)
        
        # Wahrscheinlichkeit die Antwort richtig zu beantworten
        prob = 0
        # prob for age
        # ältere Leute werden als gebildeter angesehen
        prob_age = (age/10)
        # prob for art nerd
        if art == "ja":
            prob_nerd = (age/10)
        else:
            prob_nerd = 0
        # prob degree
        # muss mindestens 20 Jahre alt sein
        if degree == "h" and age > 20:
            prob_deg = (age/15)
        else:
            prob_deg = (age/25)
            degree = "b"

        artist_paints_prob = clamp(1,20,artist_paints)
        artist_inf_prob = clamp(1,20,artist_influenced)
        artist_abs_prob = clamp(1,20,artist_abstract*age/100)


        # break
        prob = sum([prob_deg, prob_age, prob_nerd, artist_paints_prob, artist_inf_prob, artist_abs_prob])
        prob = clamp(0, 100, prob+20)/100.
        prob_neg = 1 - prob

        # generate true/false for question
        xp = np.arange(2)
        pkx = (prob_neg, prob)
        custmp = stats.rv_discrete(name='custmp', values=(xp, pkx))

        # probs = [age, gender, art, degree, artist_uri, prob].join(",")
        # print age, gender, art, degree, artist_uri, prob_age, prob_nerd, prob_deg, artist_paints_prob, artist_inf_prob, artist_abs_prob
        # +str(prob)+"/"+str(prob_neg)+","
        #print str(age)+","+str(gender)+","+str(art)+","+str(degree)+","+str(artist_uri)+","+str(custmp.rvs())
        f.write(str(age)+","+str(gender)+","+str(art)+","+str(degree)+","+md5.new(str(artist_uri)).hexdigest()+","+str(custmp.rvs())+"\r\n")

# Close file for example user data writing
f.close()
