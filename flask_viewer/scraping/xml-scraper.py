import os 
from bs4 import BeautifulSoup 
import csv
#E:\maimai universe\SDEZ_1.20.00_20210803175334_0\Package\Sinmai_Data\StreamingAssets\A000\music\music000682
#E:\maimai universe\SDEZ_1.20.00_20210803175334_0\Package\Sinmai_Data\StreamingAssets\A000\music
xmlfile = "Music.xml"
musicxmlfiles = [os.path.join(root, name)
             for root, dirs, files in os.walk("E:\SIT\INF1002 Programming Fundamentals\maimai web scraping\data stuff\music")
             for name in files
             if name == xmlfile]


csvfile = open("test.csv","w",encoding='utf-8')

for i in musicxmlfiles:
    
    with open(i,"r",encoding="utf-8") as f:
        soup = BeautifulSoup(f, 'xml')

    name = soup.findAll('name')
    for tag in name:
        content = tag.contents
        songid = str(content[1]).replace('<id>','').replace('</id>','')
        songname = str(content[3]).replace('<str>','').replace('</str>','')

        
    genre = soup.find_all('genreName')
    for i in genre:
        contents = i.contents
        songgenre = str(contents[3]).replace('<str>','').replace('</str>','')
    
    version = soup.find_all('AddVersion')
    for j in version:
        versioncontent = j.contents
        songversion = str(versioncontent[3]).replace('<str>','').replace('</str>','')

    artist = soup.find_all('artistName')
    for k in artist:
        artistcontent = k.contents
        songartist = str(artistcontent[3]).replace('<str>','').replace('</str>','')

    bpm = soup.find("bpm")
    songbpm = str(bpm.contents[0])
    levels = []
    decimals = []
    constants = []
    level = soup.find_all("level")
    #rint(level)
    for l in level:
        formattedLevel = str(l).replace('<level>','').replace('</level>','')
        if formattedLevel != '0':
            levels.append(formattedLevel)
    
    decimal = soup.find_all("levelDecimal")
    #print(decimal)
    for d in range(len(decimal)):
        if d <5:
            decimals.append(str(decimal[d]).replace('<levelDecimal>','').replace('</levelDecimal>',''))

    if len(levels) == 4:
        levels.append("NIL")
        decimals[4] = ""
        for index in range(len(levels)):
            if index < 4:
                constants.append(levels[index]+"."+decimals[index])
            else:
               constants.append(levels[index]+decimals[index]) 
    else:
        for index in range(len(levels)):
            constants.append(levels[index]+"."+decimals[index])

    print(constants)

    if len(songid) == 5:
        songid = songid[1:]
    csvwriter = csv.writer(csvfile, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)
    diffs = ['00','01','02','03','04']
    for i in range(len(constants)):
        chartid = songid + "_"+diffs[i]
        songconstant = constants[i]
        csvwriter.writerow([chartid,songname,songconstant,songgenre,songversion,songartist,songbpm])


    # print(songid)
    # print(songname)
    # print(songgenre)
    # print(songversion)
    # print(songartist)
    # print(songbpm)



csvfile.close()
