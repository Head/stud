import io
import os
import PIL
import urllib2 as urllib
from PIL import Image


# source file of URLs within <URL>
ins = open( "img_url", "r" )

# destination folder
downloadFolder = '/Applications/MAMP/htdocs/stud/AAI/imgs'

# basewidth to scale image
basewidth = 300

for line in ins:
    # url out of <URL>
    url = line[line.find("<")+1:line.find(">")]
    # get the url prefix and file extension
    urlName, fileExtension = os.path.splitext(url)
    # get the filename by removing special chars
    fileName = ''.join(e for e in urlName if e.isalnum())

    try:
        # open new file and download
        oriIMG = open(downloadFolder+"/"+fileName+fileExtension,'wb')
        oriIMG.write(urllib.urlopen(url).read())
        oriIMG.close()

        #oriIMGUrl = urllib.urlopen(url)
        #oriIMGio = io.BytesIO(oriIMGUrl.read())
        oriIMG = Image.open(downloadFolder+"/"+fileName+fileExtension)

        #smallIMG = Image.open(downloadFolder+"/"+fileName+"_small"+fileExtension)
        wpercent = (basewidth/float(oriIMG.size[0]))
        hsize = int((float(oriIMG.size[1])*float(wpercent)))
        smallIMG = oriIMG.resize((basewidth,hsize), PIL.Image.ANTIALIAS)
        smallIMG.save(downloadFolder+"/"+fileName+"_small"+fileExtension)

        smallIMG.close()
        oriIMG.close()
    except:
        # ignore 404 and other errors
        pass

    # oriIMG = Image.open(downloadFolder+"/"+fileName+fileExtension)
    # oriIMG.close()
    # smallIMG.close()

    print urlName, fileExtension, fileName

ins.close()
