from pony.orm import *
import csv
from scraping.packages import wikiscraper
import traceback

db = Database()


   

class UserData(db.Entity):
    username = Optional(str)
    userid = PrimaryKey(str)
    photoicon = Optional(str)
    rating = Optional(int)
    title = Optional(str)
    playcount = Optional(int)
    scores = Set("Scores")


class Songs(db.Entity):
    songid = PrimaryKey(str)
    internalid = Optional(str)
    wikiid = Optional(str)
    name = Optional(str)
    type = Optional(str)
    version = Optional(str)
    genre = Optional(str)
    artist = Optional(str)
    bpm = Optional(float)
    jacket = Optional(str)
    charts = Set("Charts")
    alias = Set("Alias")

class Charts(db.Entity):
    chartid = PrimaryKey(str)
    internalid = Optional(str)
    level = Optional(str)
    difficulty = Optional(str)
    constant = Optional(float)
    totalnotecount = Optional(int)
    tapcount = Optional(int)
    slidecount = Optional(int)
    holdcount = Optional(int)
    breakcount = Optional(int)
    touchcount = Optional(int)
    songlength = Optional(float)
    density = Optional(float)
    excount = Optional(int)
    radartap = Optional(float)
    radarslide = Optional(float)
    radartouch = Optional(float)
    radarhold = Optional(float)
    radarbreak = Optional(float)
    radarex = Optional(float)
    radartotal = Optional(float)
    scores = Set("Scores")
    parentsong = Required(Songs)
    

class Scores(db.Entity):
    rowid = PrimaryKey(int, auto=True)
    userid = Required(UserData)
    score = Optional(float)
    dxscore = Optional(str)
    scoregrade = Optional(str)
    combograde = Optional(str)
    syncgrade = Optional(str)
    chartid = Optional(Charts)
    playcount = Optional(int)
    lastplayed = Optional(str)
    rating = Optional(int)

class Alias(db.Entity):
    rowid = PrimaryKey(int, auto=True)
    songid = Optional(Songs)
    alias = Required(str)



def setup(debug=False):
    '''
    This function creates a sqlite database to be used for the application if it doesn't exist. Else it establishes a connection to the database.

    Debugging: set debug to True to see SQL code
    '''
    
    set_sql_debug(debug)
    db.bind(provider='sqlite', filename='../../database.sqlite', create_db=True)
    db.generate_mapping(create_tables=True)
   
@db_session
def updateXMLValues():
    
    set_sql_debug(True)
    csvfile = open("E:\\Code\\maimai-statviewer\\scraping\\packages\\csv\\xmlData.csv","r",encoding='utf-8')
    csvreader = csv.reader(csvfile,delimiter=',')
    for row in csvreader:
        
        name = row[1]
        constant = row[2]
        artist = row[5]
        version = row[4]
        if constant == "NIL":
            continue
        
        if name == "Link":
                
                
                if version == "maimai PLUS":
                    name = "Link (JOYPOLIS)"
                    
                else:
                    name = "Link (Circle of friends)"  
        bpm = row[6]
        internalid = row[0]
        diffid = row[0][-2:]
        print(diffid)
        diff =''
        if diffid == "00":
            diff = "Basic"
        if diffid == "01":
            diff = "Advanced"
        if diffid == "02":
            diff = "Expert"
        if diffid == "03":
            diff = "Master"
        if diffid == "04":
            diff = "Remaster"
         
        song = Songs.get(name = name, version = version, artist = artist)
        if song!= None:
            
            song.internalid = internalid[:-3]
        
            song.bpm = float(bpm)
            
            chart = Charts.get(difficulty=diff, parentsong = song)
            
            if chart != None:
                chart.constant = float(constant)
                chart.internalid = internalid
            
            commit()

@db_session
def insertNoteData():
    set_sql_debug(True)
    NoteDataList = []
    with open('E:\\Code\\maimai-statviewer\\scraping\\packages\\csv\\parsedData.csv', 'r', encoding='UTF8', newline='') as f:
        reader = csv.reader(f,delimiter=',')
        index = 0
        for row in reader:
            if(index == 0):
                print('row', row)
            else:
                NoteDataList.append(row)
            index +=1
    
    result = select(chart for chart in Charts)[:]
    for chart in result:
        if chart.internalid:
            NoteDataListFiltered = [x for x in NoteDataList if  checkIfInternalIdMatches(x, chart.internalid)]
            if(len(NoteDataListFiltered) > 0):
                # print('NoteDataListFiltered', NoteDataListFiltered)
                chart.tapcount = NoteDataListFiltered[0][1]
                chart.holdcount = NoteDataListFiltered[0][2]
                chart.slidecount = NoteDataListFiltered[0][3]
                chart.touchcount = NoteDataListFiltered[0][4]
                chart.breakcount = NoteDataListFiltered[0][5]
                chart.excount = NoteDataListFiltered[0][6]
                chart.totalnotecount = NoteDataListFiltered[0][7] 
                chart.songlength = NoteDataListFiltered[0][10]
                if chart.songlength != None:
                    chart.density = chart.totalnotecount / chart.songlength
                else:
                    chart.density = None
                commit()
    # print('NoteDataList', NoteDataList)

# test()

def checkIfInternalIdMatches(data, internalId):
    filename = data[0]
    dataId = filename.split('.txt')[0]
    chartId = 0
    if(dataId[:3] == "000"):
        chartId = dataId[3:9]
        # print('c1', chartId)
        # print('c2', '0'+internalId)
        return chartId == '0'*(6-len(internalId))+internalId
    elif(dataId[:2] in ("01", "00")):
        # print('c1', chartId)
        # print('c2', '00'+internalId)
        chartId = dataId[2:9]
        return chartId == '0'*(7-len(internalId))+internalId
    # print('chartId', chartId)
    else:
        return False
    
@db_session
def tryexcept2(song,urlDict):
    try:
        name = song.name.replace("”",'"')
        song.wikiid = urlDict[name]
    except:
        try:
            name = song.name.replace("＝",'=')
            song.wikiid = urlDict[name]
        except:
            try:
                name = song.name.replace('=',"＝")
                song.wikiid = urlDict[name]
            except:
                
                try:
                    name = song.name.replace('＋',"+")
                    song.wikiid = urlDict[name]
                except:
                    try:
                        name = song.name.replace("+",'＋')
                        song.wikiid = urlDict[name] 
                    except:
                        try:
                            name = song.name.replace("（",'(').replace("）",")")
                            song.wikiid = urlDict[name]
                        except:
                            name = song.name.replace('(',"（").replace(")","）")
                            song.wikiid = urlDict[name]
@db_session
def tryexcept(song,urlDict):
    try:
        name = song.name.replace("’","'")
        song.wikiid = urlDict[name]
    except:
        try:
            name = song.name.replace("'","’")
            song.wikiid = urlDict[name]
        except:
            try:
                name = song.name.replace("，",",")
                song.wikiid = urlDict[name]

            except:
                try:
                    name = song.name.replace(",","，")
                    song.wikiid = urlDict[name]
                except:
                    try:
                        name = song.name.replace("?","？")
                        song.wikiid = urlDict[name]
                    except:
                        try:
                            name = song.name.replace("？","?")
                            song.wikiid = urlDict[name]
                        except:
                            try:
                                name = song.name.replace("＞",">")
                                song.wikiid = urlDict[name]
                            except:
                                try:
                                    name = song.name.replace(">","＞")
                                    song.wikiid = urlDict[name]
                                except:
                                    try:
                                        name = song.name.replace('"',"”")
                                        song.wikiid = urlDict[name]
                                    
                                    except:
                                        tryexcept2(song,urlDict)
@db_session   
def getWikiID():
    set_sql_debug(True)
    urlDict = wikiscraper.getSongURLs()
    print(urlDict)
    songs = select(s for s in Songs)[:]
    for song in songs:
        
        try:
            
            song.wikiid = urlDict[song.name]
            
            
        except:
            
            try:
                
                name = song.name.replace("：", ":")
                song.wikiid = urlDict[name]
            except:

                try:
                    name = song.name.replace("［", "[").replace("］","]")
                    song.wikiid = urlDict[name]
                except:
                    try:
                        name = song.name.replace("！", "!")
                        song.wikiid = urlDict[name]
                    except:
                        try:
                            name = song.name.replace(":","：")
                            song.wikiid = urlDict[name]
                        except:
                            try:
                                name = song.name.replace("[","［").replace("]","］")
                                song.wikiid = urlDict[name]
                            except:
                                try:
                                    name = song.name.replace("!","！")
                                    song.wikiid = urlDict[name]
                                except:
                                    try:
                                        name = song.name.replace("＆","&")
                                        song.wikiid = urlDict[name]
                                    except:
                                        try:
                                            name = song.name.replace("&","＆")
                                            song.wikiid = urlDict[name]
                                        except:
                                            tryexcept(song,urlDict)

                                            



@db_session
def updateChartValues(charts,songinfo=None,chartOnly=False):

    set_sql_debug(True)

    

    for chart in charts:
        
        if chartOnly == True:
            songinfo = wikiscraper.scrapeSong("https://gamerch.com/maimai/entry/" + chart.parentsong.wikiid)

        if chart.parentsong.name == "全世界共通リズム感テスト":
            continue

        if chart.parentsong.type == "DX":
            chartType = "dx"
        else:
            chartType = "std"
        
        if chart.constant == None:
            if songinfo["".join([chartType,chart.difficulty,"Constant"])] != "":
                chart.constant = float(songinfo["".join([chartType,chart.difficulty,"Constant"])])
        
        if chart.totalnotecount == None:
            if songinfo["".join([chartType,chart.difficulty,"Total"])] != "":
                chart.totalnotecount = int(songinfo["".join([chartType,chart.difficulty,"Total"])]) 
        
        if chart.tapcount == None:
            if songinfo["".join([chartType,chart.difficulty,"Tap"])] != "":
                chart.tapcount = int(songinfo["".join([chartType,chart.difficulty,"Tap"])])
        
        if chart.slidecount == None:
            if songinfo["".join([chartType,chart.difficulty,"Slide"])] != "":
                chart.slidecount = int(songinfo["".join([chartType,chart.difficulty,"Slide"])])
        
        if chart.holdcount == None:
            if songinfo["".join([chartType,chart.difficulty,"Hold"])] != "":
                chart.holdcount = int(songinfo["".join([chartType,chart.difficulty,"Hold"])])

        if chart.breakcount == None:
            if songinfo["".join([chartType,chart.difficulty,"Break"])] != "":
                chart.breakcount = int(songinfo["".join([chartType,chart.difficulty,"Break"])])
        
        if chart.touchcount == None:
            if chartType == "dx":
                if songinfo["".join([chartType,chart.difficulty,"Touch"])] != "":
                    chart.touchcount = int(songinfo["".join([chartType,chart.difficulty,"Touch"])])
            else:
                chart.touchcount = 0




@db_session                                         
def getMissingValues(versions):

    set_sql_debug(True)
    baseurl = "https://gamerch.com/maimai/entry/"
    songs = select(s for s in Songs if s.bpm is None)[:]
    for song in songs:

        newurl = baseurl + song.wikiid
        songinfo = wikiscraper.scrapeSong(newurl)
        if song.name == "全世界共通リズム感テスト":
            song.bpm = 120.0
            charts = song.charts
            for chart in charts:
                chart.holdcount = 0
                chart.slidecount = 0
                chart.touchcount = 0
                if chart.difficulty == "Basic":
                    chart.constant = 6.0
                    chart.totalnotecount = 65
                    chart.tapcount = 48
                    chart.breakcount = 17
                
                if chart.difficulty == "Advanced":
                    chart.constant = 8.0
                    chart.totalnotecount = 129
                    chart.tapcount = 96
                    chart.breakcount = 33

                if chart.difficulty == "Expert":
                    chart.constant = 10.0
                    chart.totalnotecount = 129
                    chart.tapcount = 0
                    chart.breakcount = 129
                
                if chart.difficulty == "Expert":
                    chart.constant = 12.0
                    chart.totalnotecount = 129
                    chart.tapcount = 0
                    chart.breakcount = 129
            continue
                    
        
        song.bpm = float(songinfo['bpm'])
        charts = song.charts
        updateChartValues(charts,songinfo)
    #or (c.touchcount == None and versions[c.parentsong.version] > 12)
    charts = select(c for c in Charts if c.constant == None  or c.totalnotecount == None or c.tapcount == None  or c.slidecount == None or  c.holdcount == None  or c.breakcount == None  )
    updateChartValues(charts,chartOnly=True)

@db_session
def constant_validator():
    charts = select(c for c in Charts)[:]
    for chart in charts:
        level = chart.level
        const = chart.constant
        print(chart.parentsong.name)
        
        if chart.constant == None:
            if level[-1] == "+":
                chart.constant = float(level[:-1]) + 0.7
            
            else:
                chart.constant = float(level)
            
            continue
        
        if level[-1] == '+':
            floor = float(level[:-1]) + 0.7
            ceiling = float(level[:-1]) + 0.9
            if (const >= floor) and (const <= ceiling):
                pass
            else:
                chart.constant = floor
        else:
            ceiling = float(level) + 0.6
            if (const >= float(level)) and (const <= ceiling):
                pass
            else:
                chart.constant = float(level)

@db_session
def getAlias():
    set_sql_debug(True)
   
    
    
    
    with open("E:\\Code\\maimai-statviewer\\scraping\\packages\\csv\\alias.tsv",encoding='utf-8') as f:
        tsv = csv.reader(f,delimiter="\t")
        for row in tsv:
            name = row[0]
            if name == "　":
                aliases = ['x0o0x_','empty', 'blank', 'kisaragi', 'nameless']
                nameless = Songs.get(artist = "x0o0x_", internalid = "1422")
                id = nameless.songid
                for a in aliases:
                    Alias(songid = id, alias = a)
                continue
            songs = select(s for s in Songs if s.name == name)[:]
            for song in songs:
                Alias(songid = song.songid, alias = song.name)
                for i in range(1,len(row)):
                    
                    Alias(songid = song.songid, alias = row[i])
        

@db_session
def calculateRadarValues():
    set_sql_debug(True)

    totalnoteMax = max(c.totalnotecount for c in Charts)
    totalnoteMin = min(c.totalnotecount for c in Charts)
    tapMax = max(c.tapcount for c in Charts)
    tapMin = min(c.tapcount for c in Charts)
    slideMax = max(c.slidecount for c in Charts)
    slideMin = min(c.slidecount for c in Charts)
    holdMax = max(c.holdcount for c in Charts)
    holdMin = min(c.holdcount for c in Charts)
    breakMax = max(c.breakcount for c in Charts)
    breakMin = min(c.breakcount for c in Charts)
    touchMax = max(c.touchcount for c in Charts)
    touchMin = min(c.touchcount for c in Charts)
    exMax = max(c.excount for c in Charts)
    exMin = min(c.excount for c in Charts)
    # densityMax = max(c.density for c in Charts)
    # densityMin = min(c.density for c in Charts)
    
    charts = select(c for c in Charts)
    for chart in charts:
        try:
            
            chart.radartap = (chart.tapcount - tapMin) / (tapMax - tapMin)
        except Exception:
            traceback.print_exc()
            chart.radartap = None
        try:
            chart.radarslide = (chart.slidecount - slideMin) / (slideMax -slideMin)
        except Exception:
            chart.radarslide = None
            traceback.print_exc()
            
        try:

            chart.radartouch = (chart.touchcount - touchMin) / (touchMax - touchMin)
        except Exception:
            chart.radartouch = None
            traceback.print_exc()
            
        
        try:
            chart.radarhold = (chart.holdcount - holdMin) / (holdMax - holdMin)
        except Exception:
            chart.radarhold = None
            traceback.print_exc()
            

        try:
            chart.radarbreak = (chart.breakcount - breakMin) / (breakMax - breakMin)
        except Exception:
            chart.radarbreak = None
            traceback.print_exc()
            
        
        try:
            chart.radarex = (chart.excount - exMin) / (exMax - exMin)
        except Exception:
            chart.radarex = None
            traceback.print_exc()
            

        try:
            chart.radartotal = (chart.totalnotecount - totalnoteMin) / (totalnoteMax - totalnoteMin)
        
        except Exception:
            chart.radartotal = None
            traceback.print_exc()
            
        
    
    




setup()
#calculateRadarValues()
# getMissingValues()
# insertNoteData()
# updateXMLValues()