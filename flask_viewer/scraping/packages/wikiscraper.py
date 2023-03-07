import mechanize
from bs4 import BeautifulSoup
import http.cookiejar 
import re

def scrapeSong(url):
    
    print("Function Started")
    songInfoDict = {}
    versionDict = {
        "maimai" : 0, 
        "maimai PLUS" : 1, 
        "GreeN" : 2, 
        "GreeN PLUS" : 3 , 
        "ORANGE" : 4, 
        "ORANGE PLUS" : 5, 
        "PiNK" : 6, 
        "PiNK PLUS" : 7, 
        "MURASAKi" : 8, 
        "MURASAKi PLUS" : 9, 
        "MiLK" : 10, 
        "MiLK PLUS" : 11, 
        "FiNALE" : 12, 
        "でらっくす" : 13, 
        "でらっくす PLUS" : 14, 
        "Splash" : 15, 
        "Splash PLUS" : 16, 
        "UNiVERSE" : 17, 
        "UNiVERSE PLUS" : 18, 
        "FESTiVAL" : 19  
        }
    dxChart = False
    stdChart = False
    

    br.open(url)
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")

    print("Getting Song Info")
    songInfoTable = Soup.find_all("div",class_="mu__table")
    songInfoSoup = BeautifulSoup(str(songInfoTable[0]),"html.parser")
    songInfo = songInfoSoup.find_all("td")
    #imageURL = songInfo[0].img['data-src']
    songGenre = songInfo[1].text.replace("*1","").replace("*2","").replace("*3","")
    songInfoDict.update({"genre" : songGenre})
    songTitle = songInfo[2].text.replace("*1","").replace("*2","").replace("*3","")
    songInfoDict.update({"title" : songTitle})
    songArtist = songInfo[3].text
    songInfoDict.update({"artist" : songArtist})
    songBPM = songInfo[4].text
    substring = re.search('(\d+)', songBPM)
    if substring:
        songBPM = substring.group(1)
    songInfoDict.update({"bpm" : songBPM})
    songVersion = songInfo[6].text
    if songVersion == "無印":
        songVersion = "maimai"
    if songTitle == "全世界共通リズム感テスト":
        songVersion = "Splash"
    if ("DX譜面" in songVersion or "ST譜面" in songVersion):
        a,b = songVersion.split(')',1)
        if ("DX譜面") in a:
            dxVersion = a.replace("DX譜面","").replace("(","").replace(")","")
            songInfoDict.update({"dxVersion" : dxVersion})
            dxChart = True
        else:
            stdVersion = a.replace("ST譜面","").replace("(","").replace(")","")
            songInfoDict.update({"stdVersion" : stdVersion})
            stdChart = True
        if ("DX譜面") in b:
            dxVersion = b.replace("DX譜面","").replace("(","").replace(")","")
            songInfoDict.update({"dxVersion" : dxVersion})
            dxChart = True
        else:
            stdVersion = b.replace("ST譜面","").replace("(","").replace(")","")
            songInfoDict.update({"stdVersion" : stdVersion})
            stdChart = True
    else:
        songInfoDict.update({"version" : songVersion})
        print(songVersion)
        print(songTitle)
        if versionDict[songVersion] < 13:
            stdChart = True
        else:
            dxChart = True
    print("Song Info Done")
    # print(imageURL)
    #print(songGenre)
    # print(songTitle)
    # print(songArtist)
    # print(songBPM)
    # print(songVersion)
    #print(dxVersion)
    #print(stdVersion)
    if (dxChart == True):
        print("Getting dx chart info")
        dxInfoSoup = BeautifulSoup(str(songInfoTable[1]),"html.parser")
        dxInfo = dxInfoSoup.find_all('tr') #get all table header elements
        #print(type(dxInfo))
        wanted = ["background-color:#98fb98", "background-color:#ffa500","background-color:#fa8080", "background-color:#ee82ee","background-color:#ffceff" ]
        diffInfo = []
        for i in dxInfo:
            content = i.contents
            #print(type(content))
            #print(content)
            for item in content:
                #print(type(item))
                #print(item)
                if any(x in str(item) for x in wanted):
                    diffInfo.append(i)
        
        for index,ele in enumerate(diffInfo):
            
            if index == 0:
                diff = "Basic"
            
            elif index == 1:
                diff = "Advanced"
            
            elif index == 2:
                diff = "Expert"

            elif index == 3:
                diff = "Master"
            
            elif index == 4:
                diff ="Remaster"
            
            else:
                break

            
            level = getattr(ele.find("th",class_="mu__table--col1"),"text",None)
            print(level)
            infolist = ele.find_all("td")
            constant = getattr(infolist[0],"text",None)
            totalnotecount = getattr(infolist[1],"text",None)
            tapcount = getattr(infolist[2],"text",None)
            holdcount = getattr(infolist[3],"text",None)
            slidecount = getattr(infolist[4],"text",None)
            touchcount = getattr(infolist[5],"text",None)
            breakcount = getattr(infolist[6],"text",None)
            songInfoDict.update({"dx"+diff+"Level" : level})
            songInfoDict.update({"dx"+diff+"Constant" : constant})
            songInfoDict.update({"dx"+diff+"Total" : totalnotecount})
            songInfoDict.update({"dx"+diff+"Tap" : tapcount})
            songInfoDict.update({"dx"+diff+"Hold" : holdcount})
            songInfoDict.update({"dx"+diff+"Slide" : slidecount})
            songInfoDict.update({"dx"+diff+"Touch" : touchcount})
            songInfoDict.update({"dx"+diff+"Break" : breakcount})
        
        

            
        
            
        print("dx chart info done")


    if (stdChart == True and dxChart == True):

        print("Getting standard chart info")
        stdInfoSoup = BeautifulSoup(str(songInfoTable[2]),"html.parser")
        stdInfo = stdInfoSoup.find_all('tr') #get all table header elements
        #print(type(dxInfo))
        wanted = ["background-color:#98fb98", "background-color:#ffa500","background-color:#fa8080", "background-color:#ee82ee","background-color:#ffceff" ]
        diffInfo = []
        for i in stdInfo:
            content = i.contents
            #print(type(content))
            #print(content)
            for item in content:
                #print(type(item))
                #print(item)
                if any(x in str(item) for x in wanted):
                    diffInfo.append(i)
        
        for index,ele in enumerate(diffInfo):
            
            if index == 0:
                diff = "Basic"
            
            elif index == 1:
                diff = "Advanced"
            
            elif index == 2:
                diff = "Expert"

            elif index == 3:
                diff = "Master"
            
            elif index == 4:
                diff ="Remaster"
            
            else:
                break

            
            level = getattr(ele.find("th",class_="mu__table--col1"),"text",None)
            print(level)
            infolist = ele.find_all("td")
            constant = getattr(infolist[0],"text",None)
            totalnotecount = getattr(infolist[1],"text",None)
            tapcount = getattr(infolist[2],"text",None)
            holdcount = getattr(infolist[3],"text",None)
            slidecount = getattr(infolist[4],"text",None)
            breakcount = getattr(infolist[5],"text",None)
            songInfoDict.update({"std"+diff+"Level" : level})
            songInfoDict.update({"std"+diff+"Constant" : constant})
            songInfoDict.update({"std"+diff+"Total" : totalnotecount})
            songInfoDict.update({"std"+diff+"Tap" : tapcount})
            songInfoDict.update({"std"+diff+"Hold" : holdcount})
            songInfoDict.update({"std"+diff+"Slide" : slidecount})
            songInfoDict.update({"std"+diff+"Break" : breakcount})

        print("standard chart info done")

    elif (stdChart == True and dxChart == False):

        print("Getting standard chart info")
        stdInfoSoup = BeautifulSoup(str(songInfoTable[1]),"html.parser")
        stdInfo = stdInfoSoup.find_all('tr') #get all table header elements
        #print(type(dxInfo))
        wanted = ["background-color:#98fb98", "background-color:#ffa500","background-color:#fa8080", "background-color:#ee82ee","background-color:#ffceff" ]
        diffInfo = []
        for i in stdInfo:
            content = i.contents
            #print(type(content))
            #print(content)
            for item in content:
                #print(type(item))
                #print(item)
                if any(x in str(item) for x in wanted):
                    diffInfo.append(i)
        
        for index,ele in enumerate(diffInfo):
            
            if index == 0:
                diff = "Basic"
            
            elif index == 1:
                diff = "Advanced"
            
            elif index == 2:
                diff = "Expert"

            elif index == 3:
                diff = "Master"
            
            elif index == 4:
                diff ="Remaster"
            
            else:
                break

            
            level = getattr(ele.find("th",class_="mu__table--col1"),"text",None)
            print(level)
            infolist = ele.find_all("td")
            constant = getattr(infolist[0],"text",None)
            totalnotecount = getattr(infolist[1],"text",None)
            tapcount = getattr(infolist[2],"text",None)
            holdcount = getattr(infolist[3],"text",None)
            slidecount = getattr(infolist[4],"text",None)
            breakcount = getattr(infolist[5],"text",None)
            songInfoDict.update({"std"+diff+"Level" : level})
            songInfoDict.update({"std"+diff+"Constant" : constant})
            songInfoDict.update({"std"+diff+"Total" : totalnotecount})
            songInfoDict.update({"std"+diff+"Tap" : tapcount})
            songInfoDict.update({"std"+diff+"Hold" : holdcount})
            songInfoDict.update({"std"+diff+"Slide" : slidecount})
            songInfoDict.update({"std"+diff+"Break" : breakcount})

        print("standard chart info done")
    

    return songInfoDict

    
    
def getSongURLs():
    
    songlist = [
      'AMAZING MIGHTYYYY!!!!',
      'Alea jacta est!',
      'BREAK YOU!!',
      'BREaK! BREaK! BREaK!',
      'BaBan!! －甘い罠－',
      'Backyun! －悪い女－',
      'Bad Apple!! feat nomico',
      'Bang Babang Bang!!!',
      'CALL HEAVEN!!',
      'CHOCOLATE BOMB!!!!',
      'Endless, Sleepless Night',
      'FREEDOM DiVE (tpz Overcute Remix)',
      'GET!! 夢&DREAM',
      'H-A-J-I-M-A-R-I-U-T-A-!!',
      'Help me, ERINNNNNN!!',
      'Help me, あーりん！',
      'I\'m with you',
      'JUMPIN\' JUMPIN\'',
      'Jump!! Jump!! Jump!!',
      'Jumping!!',
      'KING is BACK!!',
      'Let\'s Go Away',
      'Never Give Up!',
      'Now Loading!!!!',
      'Oshama Scramble!',
      'Scream out! -maimai SONIC WASHER Edit-',
      'Signs Of Love (“Never More” ver.)',
      'Splash Dance!!',
      'Time To Make History (AKIRA YAMAOKA Remix)',
      'TwisteD! XD',
      'WORLD\'S END UMBRELLA',
      'YATTA!',
      'air\'s gravity',
      'magician\'s operation',
      'shake it!',
      'specialist (“Never More” ver.)',
      'welcome to maimai!! with マイマイマー',
      'あ・え・い・う・え・お・あお!!',
      'おジャ魔女カーニバル!!',
      'ちがう!!!',
      'でらっくmaimai♪てんてこまい!',
      'オパ! オパ! RACER -GMT mashup-',
      'ドキドキDREAM!!!',
      'ナイト・オブ・ナイツ (Cranky Remix)',
      'ファンタジーゾーン OPA-OPA! -GMT remix-',
      'ラブリー☆えんじぇる!!',
      'リッジでリッジでGO!GO!GO! -GMT mashup-',
      '全力☆Summer!',
      '教えて!! 魔法のLyric',
      '最強 the サマータイム!!!!!',
      '泣き虫O\'clock',
      '無敵We are one!!',
      '電車で電車でGO!GO!GO!GC! -GMT remix-',
      '電車で電車でOPA!OPA!OPA! -GMT mashup-',
      '響け！CHIREI MY WAY!',
    ]
    
    moddict = {}
    for i in songlist:
        x = re.sub(r"[!,'()]","",i)
        moddict.update({x : i})
    
    
    
    
    SONG_LIST_URL = "https://gamerch.com/maimai/entry/545589"
    br.open(SONG_LIST_URL)
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    links = Soup.find_all("div", class_="markup mu")
    songEntryDict = {}
    linksSoup = BeautifulSoup(str(links),"html.parser")
    listsList = linksSoup.find_all('a',href=True)
    for list in listsList:
        href = str(list['href']).replace("https://gamerch.com/maimai/entry/","")
        name = str(list.contents[0])

        if name == "Link（Circle of friends）":
            name = "Link (Circle of friends)"
        
        if name == "Link":
            name = "Link (JOYPOLIS)"
        
        if name == "YA・DA・YO ［Reborn］":
            name = "YA･DA･YO [Reborn]"
        
        if name == "D✪N’T ST✪P R✪CKIN’":
            name = "D✪N’T  ST✪P  R✪CKIN’"
        
        if name == "ウッーウッーウマウマ":
            name = "ウッーウッーウマウマ(ﾟ∀ﾟ)"

        if name == "トルコ行進曲 - オワタ":
            name = "トルコ行進曲 - オワタ＼(^o^)／"
        
        if name == "♂":
            name = "+♂"

        if name == "‎":
            name = ""
        if name == "檄！帝国華撃団（改）":
            name = "檄！帝国華撃団(改)"

        if name == "JACKY ［Remix］":
            name = "JACKY [Remix]"

        if name == "City Escape： Act1":
            name = "City Escape: Act1"

        if name == "'Rooftop Run： Act1":
            name = "Rooftop Run: Act1"

        if name == "Grip ＆ Break down ！！":
            name = "Grip & Break down !!"

        if name == "L’épilogue":
            name = "L'épilogue"

        if name == "L4TS：2018 （feat. あひる ＆ KTA）":
            name = "L4TS:2018 (feat. あひる & KTA)"
        if name == "GET 夢＆DREAM":
            name = "GET!! 夢&DREAM"
        
        if name == "Sqlupp （Camellia’s ”Sqleipd＊Hiytex” Remix）":
            name = """Sqlupp (Camellia's "Sqleipd*Hiytex" Remix)"""
        if name == "Yakumo ＞＞JOINT STRUGGLE （2019 Update）":
            name = "Yakumo >>JOINT STRUGGLE (2019 Update)"

        if name == "Bad Apple！！ feat.nomico （REDALiCE Remix）":
            name = "Bad Apple!! feat.nomico (REDALiCE Remix)"

        if name == "≠彡”／了→":
            name = '≠彡"/了→'

        if name == '大輪の魂 （feat. AO， 司芭扶）':
            name = "大輪の魂 (feat. AO, 司芭扶)"
        if name in moddict:
            name = moddict[name]

            
       
        songEntryDict.update({name:href})
    
    return songEntryDict





cj = http.cookiejar.CookieJar()
br = mechanize.Browser()
br.set_cookiejar(cj)
#print(scrapeSong("https://gamerch.com/maimai/entry/534687"))
#songURLs = getSongURLs()
#print(songURLs['‎'])

#print(scrapeSong("https://gamerch.com/maimai/entry/533458")) #glorious crown std only
#print(scrapeSong("https://gamerch.com/maimai/entry/533969")) #vii bit explorer dx + std in dx ver
#print(scrapeSong("https://gamerch.com/maimai/entry/533844")) #atropos dx only
#print(scrapeSong("https://gamerch.com/maimai/entry/533599")) #starlight disco std with remaster + dx 
#print(scrapeSong("https://gamerch.com/maimai/entry/533459")) #garakuta remaster + utage


