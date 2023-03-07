import mechanize
from bs4 import BeautifulSoup
import http.cookiejar 
import os
import requests
import urllib.parse
from urllib.error import HTTPError
from concurrent.futures import ThreadPoolExecutor
import time
import re
from scraping.packages import database, rating
from pony.orm import *







#TODO add async to every scraping function
class Song:
    def __init__(self, name=None, type=None, basicLevel=None, advancedLevel=None, expertLevel=None, masterLevel=None, remasterLevel=None, id=None, jacket=None, genre=None, artist=None, version=None):
        self.name = name
        self.type = type
        self.basicLevel = basicLevel
        self.advancedLevel = advancedLevel
        self.expertLevel = expertLevel
        self.masterLevel = masterLevel
        self.remasterLevel = remasterLevel
        self.id = id
        self.jacket = jacket
        self.genre = genre
        self.artist = artist
        self.version = version

class User:
    
    def __init__(self,name=None,title=None,rating=None,photoIcon=None,playcount=None,friendcode=None):
        self.name = name
        self.title = title
        self.rating = rating
        self.photoIcon = photoIcon
        self.playcount = playcount
        self.friendcode = friendcode

class Score:

    def __init__(self, name=None, score=None, dxscore = None, type=None, difficulty=None, combograde=None, syncgrade=None, scoregrade=None, rating=None):
        self.name = name
        self.score = score
        self.dxscore = dxscore
        self.type = type
        self.difficulty = difficulty
        self.combograde = combograde
        self.syncgrade = syncgrade
        self.scoregrade = scoregrade
        self.rating = rating




def getScores(br,cj,userid,urllist,debug=False):
    '''
    This functions scrapes mainet for the user's score values
    '''
    #trying threading to see if it helps
    with ThreadPoolExecutor() as executor:
        for i in urllist:
            if i[-1] == '0':
                diff = "Basic"
            if i[-1] == '1':
                diff = "Advanced"
            if i[-1] == '2':
                diff = "Expert"
            if i[-1] == '3':
                diff = "Master"
            if i[-1] == '4':
                diff = "Remaster"
            
            executor.submit(getScoresByDifficulty(br,cj,i,userid,diff,debug))
    # for i in urllist:
    #         if i[-1] == '0':
    #             diff = "Basic"
    #         if i[-1] == '1':
    #             diff = "Advanced"
    #         if i[-1] == '2':
    #             diff = "Expert"
    #         if i[-1] == '3':
    #             diff = "Master"
    #         if i[-1] == '4':
    #             diff = "Remaster"
            
    #         getScoresByDifficulty(br,cj,i,userid,diff,debug)



@db_session
def getScoresByDifficulty(br,cj,url,userid,diff,debug = False):
    '''
    This function scrapes the user's scores from mainet for a specific difficulty

    Set debug to True to enable SQL debugging
    
    '''


    set_sql_debug(debug)
    
    

    br.open(url)
    
    #parse html response
    scoreSoup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    #find all div elements with class w_450 m_15 p_r f_0
    scores = scoreSoup.find_all("div", class_="w_450 m_15 p_r f_0")
    
    #iterate through results
    for item in scores:
        #create instance of Score object
        score = Score()
        score.difficulty = diff
        #take content[1] for score data and content[3] or content[5] for dx/std chart type
        #content[3] if chart only has 1 type, else content[5] if chart has both dx/std
        content = item.contents
        
        name = getattr(content[1].find("div", class_="music_name_block t_l f_13 break"),'text',None)
        score.name = name
        
        try:
            if (content[5]['src']) == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png":
                chartType = "DX"
                score.type = chartType
            else:
                chartType = "Standard"
                score.type = chartType
        except:
        
    
            if (content[3]['src']) == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png":
                chartType = "DX"
                score.type = chartType
            else:
                chartType = "Standard"
                score.type = chartType
           


        
        
        
        #handling for songs with same name Link
        if name == "Link":
            
            id = content[1].find('input', {'name': 'idx'}).get('value')
            br.open("https://maimaidx-eng.com/maimai-mobile/record/musicDetail/?idx=" + urllib.parse.quote(id.encode('utf-8')))
            Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
            artist = Soup.find("div",class_="m_5 f_12 break").text.strip()
            
            if artist == "Circle of friends(天月-あまつき-・un:c・伊東歌詞太郎・コニー・はしやん)":
                score.name = "Link (Circle of friends)"
            else:
                score.name = "Link (JOYPOLIS)"

        parentsong = database.Songs.get(name = score.name, type= score.type)
        #check for new songs
        if parentsong == None:
            print(f"Song: {score.name}  not in database, update manually")
            #getSongs(br,cj,{"FESTiVAL" : 19},True,True)
            continue
            
        
        songscore = getattr(content[1].find("div", class_="music_score_block w_120 t_r f_l f_12"),'text',None)
        
        if songscore != None:
            #remove % from score
            score.score = songscore.replace("%","")
        else:
            #this should be None
            score.score = songscore
        dxscore = getattr(content[1].find("div", class_="music_score_block w_180 t_r f_l f_12"),'text',None)
        if dxscore is not None:
            #remove whitespace
            dxscore = dxscore.strip()
            score.dxscore = dxscore
        else:
            score.dxscore = ""

        
        
        grades = item.find_all("img")
        
        #if player hasn't played the chart, skip
        if songscore == None:
            continue
        
        else:

            
            
            combograde = grades[3]['src'].replace("https://maimaidx-eng.com/maimai-mobile/img/music_icon_","")
            #get value of combograde
            if "app" in combograde:
                combograde = "AP+"
            elif "ap" in combograde:
                combograde = "AP"
            elif "fcp" in combograde:
                combograde = "FC+"
            elif "fc" in combograde:
                combograde = "FC"
            else:
                combograde = ""
            
            score.combograde = combograde
            #get value of syncgrade
            syncgrade = grades[2]['src'].replace("https://maimaidx-eng.com/maimai-mobile/img/music_icon_","")
            
            if "fsdp" in syncgrade:
                syncgrade = "FSD+"
            elif "fsd" in syncgrade:
                syncgrade = "FSD"
            elif "fsp" in syncgrade:
                syncgrade = "FS+"
            elif "fs" in syncgrade:
                syncgrade = "FS"
            else:
                syncgrade = ""

            score.syncgrade = syncgrade


            if float(score.score) > 100.5000:
                score.scoregrade = "SSS+"
            
            elif float(score.score) > 100.0000:
                score.scoregrade = "SSS"
            
            elif float(score.score) > 99.50:
                score.scoregrade = "SS+"
            
            elif float(score.score) > 99:
                score.scoregrade = "SS"
            
            elif float(score.score) > 98:
                score.scoregrade = "S+"
            
            elif float(score.score) > 97:
                score.scoregrade = "S"
            
            elif float(score.score) > 94:
                score.scoregrade = "AAA"
            
            elif float(score.score) > 90:
                score.scoregrade = "AA"
            
            elif float(score.score) > 80:
                score.scoregrade = "A"
            
            elif float(score.score) > 75:
                score.scoregrade = "BBB"
            
            elif float(score.score) > 70:
                score.scoregrade = "BB"
            
            elif float(score.score) > 60:
                score.scoregrade = "B"
            
            elif float(score.score) > 50:
                score.scoregrade = "C"
            
            else:
                score.scoregrade = "D"

        
        
        chartid = parentsong.songid + diff
        constant = select(c.constant for c in database.Charts if c.chartid == chartid)[:]
        score.rating = rating.CalculateRating(score.score,constant[0])
        
        
        result = select(s for s in database.Scores if s.userid.userid == userid and s.chartid.chartid == chartid)[:]
        if len(result) == 0:

            database.Scores(userid = userid, score = score.score, dxscore= score.dxscore, scoregrade = score.scoregrade, combograde = score.combograde, syncgrade = score.syncgrade , rating = score.rating, chartid = chartid )
        else:
            if result[0].score < float(score.score):
                result[0].score = float(score.score)

                if result[0].scoregrade != score.scoregrade:
                    result[0].scoregrade = score.scoregrade

            if int(result[0].dxscore.replace(",","").strip().split("/")[0]) < int(score.dxscore.replace(",","").strip().split("/")[0]):
                result[0].dxscore = score.dxscore
 
            combogradedict = {
                "" : 0,
                "FC" : 1,
                "FC+" : 2,
                "AP" : 3,
                "AP+" : 4
            }

            syncgradedict = {
                "" : 0,
                "FS" : 1,
                "FS+" : 2,
                "FSD" : 3,
                "FSD+" : 4
            }
            if combogradedict[result[0].combograde] < combogradedict[score.combograde]:
                result[0].combograde = score.combograde

            if syncgradedict[result[0].syncgrade] != syncgradedict[score.syncgrade]:
                result[0].syncgrade = score.syncgrade
            
            if result[0].rating < score.rating:
                result[0].rating = score.rating
        
        commit()
        

     

def downloadImages(cj,images,imageName=None,debug=False):
    '''
    Returns a list of image names

    This function takes in a list of image links, downloads them to the images directory, and then returns the names of each image downloaded in a list.
    
    Debugging: True / False
    '''

    folder_name = "static/assets/user_img"
    if not os.path.exists(folder_name):
        os.mkdir(folder_name)
    count = 0
    if debug is True:
        print(f"Found {len(images)} images")
    if len(images) != 0:
        imageNames = []
        for i in range(len(images)):
            imageLink = images[i]
            if imageName is None:
                imageName = imageLink.replace("https://maimaidx-eng.com/maimai-mobile/img/","").replace("/","").replace("images","")
            r = requests.get(imageLink, cookies=cj).content
            with open(f"{folder_name}/"+imageName+".png", "wb+") as f:
                f.write(r)
                count += 1
                imageNames.append(imageName)
        if count == len(images) and debug is True:
            print("All the images have been downloaded!")
            
        elif count != len(images) and debug is True:
            print(f" {count} images have been downloaded out of {len(images)}")

        return imageNames
    
@db_session
def getProfileInfo(br,cj,update=False,debug = False):
    '''
    Returns a User object

    This profiles scrapes Mainet for user profile information, and returns it as a User object.

    Update: Set to true to update user profile information in database

    Debug: Set to true to enable debugging messages, User object attributes and SQL Code will be printed.
    '''

    profile = User()
    br.open("https://maimaidx-eng.com/maimai-mobile/playerData/")
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    name = Soup.find("div",class_="name_block f_l f_16").text.replace('\u3000', ' ')
    profile.name = name
    title = Soup.find("div",class_="trophy_inner_block f_13").text.strip()
    profile.title = title
    rating = Soup.find("div",class_="rating_block").text
    profile.rating = rating
    playcount = Soup.find("div",class_="m_5 m_t_10 t_r f_12").text
    profile.playcount = re.sub("\\D", "", playcount)
    images = []
    photoIcon = Soup.find("img", class_="w_112 f_l")['src']
    images.append(photoIcon)
    br.open("https://maimaidx-eng.com/maimai-mobile/friend/userFriendCode/")
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    friendcode = Soup.find("div",class_="see_through_block m_t_5 m_b_5 p_5 t_c f_15").text
    profile.friendcode = friendcode



    # courseRank = Soup.find("img", class_="h_35 f_l")['src']
    # images.append(courseRank)
    # classRank = Soup.find("img", class_="p_l_10 h_35 f_l")['src']
    # images.append(classRank)


    imageLinks = downloadImages(cj,images,friendcode)
    profile.photoIcon = imageLinks[0]+".png"
    if debug == True:
        set_sql_debug(True)
        #print(vars(profile))
    if update == True:
        #print('friendcode:', profile.friendcode)
        dbUser = select(u for u in database.UserData if u.userid == profile.friendcode)[:]
        #print("username",dbUser[0].username)
        if len(dbUser) == 1:
            dbUser[0].username = profile.name
            dbUser[0].photoicon = profile.photoIcon
            dbUser[0].rating = profile.rating
            dbUser[0].title = profile.title
            dbUser[0].playcount = profile.playcount
        else:
            database.UserData(username = profile.name, userid = profile.friendcode, photoicon = profile.photoIcon, rating = profile.rating, title = profile.title, playcount = profile.playcount)
    
    
    return profile

def getSongs(br,cj,versions,update=False,debug=0):
     with ThreadPoolExecutor() as executor:
        for x in versions:
            version = (x,versions[x])
            executor.submit(getSongsByVersion(br,cj,version,update,debug))


@db_session
def getSongsByVersion(br,cj,version,update=False,debug=0):
    '''
    This function retrieves all the necessary song information for each difficulty by using the version filter. Basic song information such as name, difficulty, version,id is attained first. Thereafter, the id is used to navigate to the detailed song information page where the rest of the necessary information is collected.

    Update: Set to True to update database entries

    Debugging: 0 - Off, 1 - SQL, 2 - Song Info, 3 - Everything
    '''
  
   
    if debug == 1 or debug == 3:
        set_sql_debug(True)
   
        
        
        
        
    if debug == 3:
        print(f"Version: {version[0]}")
    #get url for specific difficulty and version
    url = f"https://maimaidx-eng.com/maimai-mobile/record/musicVersion/search/?diff=3&version={version[1]}"
    br.open(url)
    #parse HTML response
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    #get a list of each song div element
    className = "music_master_score_back pointer w_450 m_15 p_3 f_0"
    songlist = Soup.find_all("div", class_= className)

    for item in songlist:
        song = Song() #create Song object
        song.version = version[0]
        
        #get contents of div
        content = item.contents
        name = getattr(content[1].find("div", class_="music_name_block t_l f_13 break"),'text',None)
        #check if song name is Link, as there are 2 songs with the same name.
        #Thereafter, make a distinction between the 2 of them.
        if name == "Link":
            
            
            
            if version[0] == "maimai PLUS":
                name = "Link (JOYPOLIS)"
                
            else:
                name = "Link (Circle of friends)"  

           
                
        
        song.name = name
        imageURL = content[1].find("img", class_="music_kind_icon f_r")['src']
        if (imageURL) == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png":
            chartType = "DX"

        elif (imageURL) == "https://maimaidx-eng.com/maimai-mobile/img/music_standard.png":
            chartType = "Standard"
            
        else:
            chartType = None

        song.type = chartType
        
        id = content[1].find('input', {'name': 'idx'}).get('value')
        #encode song id so that it can be used in a URL
        song.id = urllib.parse.quote(id.encode('utf-8'))
        

        #navigate to detailed song info page
        detailURL = "https://maimaidx-eng.com/maimai-mobile/record/musicDetail/?idx="
        newURL = detailURL + song.id
        br.open(newURL)

        #parse HTML response
        Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
        print(song.name)
        jacket = Soup.find("img", class_="w_180 m_5 f_l")['src']
        song.jacket = jacket
        genre = Soup.find("div", class_="m_10 m_t_5 t_r f_12 blue").text.strip()
        song.genre = genre
        artist = Soup.find("div",class_="m_5 f_12 break").text.strip()
        song.artist = artist
        rows = Soup.find_all("td",class_="f_0")
        results = [rows[i] for i in range(0,len(rows),2)]
        for index in range(len(results)):
            if index == 0:
                song.basicLevel = results[index].text
            elif index == 1:
                song.advancedLevel = results[index].text
            elif index == 2:
                song.expertLevel = results[index].text
            elif index == 3:
                song.masterLevel = results[index].text
            elif index == 4:
                song.remasterLevel = results[index].text


        #check if debug is enabled
        if debug == 2 or debug == 3:
            print(vars(song))
        
        #check if update is enabled
        if update == True:
            
            if len(select(i for i in database.Songs if i.songid == song.id )) == 0:

                s = database.Songs(songid = song.id, name = song.name, type = song.type, version= song.version, genre = song.genre, artist = song.artist, jacket = song.jacket)
                database.Charts(chartid = "".join((song.id,"Basic")), level = song.basicLevel, difficulty = "Basic", parentsong = s)
                database.Charts(chartid = "".join((song.id,"Advanced")), level = song.advancedLevel, difficulty = "Advanced", parentsong = s)
                database.Charts(chartid = "".join((song.id,"Expert")), level = song.expertLevel, difficulty = "Expert", parentsong = s)
                database.Charts(chartid = "".join((song.id,"Master")), level = song.masterLevel, difficulty = "Master", parentsong = s)
                if song.remasterLevel is not None:

                    database.Charts(chartid = "".join((song.id,"Remaster")), level = song.remasterLevel, difficulty = "Remaster", parentsong = s)
            else:
                s = database.Songs.get(name=song.name, artist= song.artist)
                if s.remasterLevel is None:
                    if song.remasterLevel is not None:

                        database.Charts(chartid = "".join((song.id,"Remaster")), level = song.remasterLevel, difficulty = "Remaster", parentsong = s)
                        commit()
                if s.basicLevel != song.basicLevel:
                    s.basicLevel = song.basicLevel

                if s.advancedLevel != song.advancedLevel:
                    s.advancedLevel = song.advancedLevel    

                if s.expertLevel != song.expertLevel:
                    s.expertLevel = song.expertLevel
                        
                if s.masterLevel != song.masterLevel:
                    s.masterLevel = song.masterLevel



@db_session
def getGenreSortID(br,cj,update=False,debug=False):

    '''
    Returns a list of genreSortIDs

    This function scrapes mainet for the genresortid of each song, and returns it as a list. As there are 2 cases of a song having the same name, 'Link', the artist will be appended to the song name for distinction purposes.

    Update: Set update to True to update database

    Debug: Set debug to True to enable debugging messages
    
    '''


    linkJoypolis = "0722b2e55f70c06118877dec818b81649100b8dc3cf1aac7b4299a519c9677cdbb5bc7d14c4d2b5bc267f26319955474053de5ed69a33ac9fb39aa14ae25be3dIbqCqTlOFRLx0p7YoVExEaJs/YP2XE2D7IzidnQClKk="
    linkCircleoffriends = "af92d05102dcf186cd274a5b15576dc68a04ff7f8a779efceed1720e2a8bcd4bc6aa45c0e7162ce866dd05aa278300a63c83aabd145a348757104dfe7c491ef6aD1B2VKvJE8jYtaTEa33Af8tHAEeyhAQHNlB0hf+BX0="
    ids = []
    br.open("https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=3")
    Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
    songs = Soup.find_all("div", class_="w_450 m_15 p_r f_0")
    for item in songs:
       
        # need to take content[1] for score dat a and content[3] for dx/std chart type
        content = item.contents
        name = getattr(content[1].find("div", class_="music_name_block t_l f_13 break"),'text',None)
        
        
        if (content[3]['src']) == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png":
            chartType = "DX"
        elif (content[3]['src']) == "https://maimaidx-eng.com/maimai-mobile/img/music_standard.png":
            chartType = "Standard" 
        else:
            chartType = None
        id = content[1].find('input', {'name': 'idx'}).get('value')
        if name == "Link":
            newurl = "https://maimaidx-eng.com/maimai-mobile/record/musicDetail/?idx=" + urllib.parse.quote(id.encode('utf-8'))
            br.open(newurl)
            linkSoup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
            artist = linkSoup.find("div",class_="m_5 f_12 break").text.strip()
            if "Circle of friends" in artist:
                name = "Link (Circle of friends)"
            else:
                name = "Link (JOYPOLIS)"    
            
        
        if debug == True:
            set_sql_debug(True)
            print(f"Name: {name}   Type: {chartType}   ID: {id}")

        if update == True:
            
            if len(select(i for i in database.Songs if i.genresortid == id)) != 1:
                s = database.Songs.get(name = name, type = chartType)
                s.genresortid = id
                
                commit()


    return ids




def initialize(sid,password):
    '''
    Intializes the required objects/methods required for scraping
    
    '''

    br = mechanize.Browser()
    cj = http.cookiejar.CookieJar()
    br.set_cookiejar(cj)
    try:
       
        br.open("https://lng-tgk-aime-gw.am-all.net/common_auth/login?site_id=maimaidxex&redirect_url=https://maimaidx-eng.com/maimai-mobile/&back_url=https://maimai.sega.com/")
        br.select_form(nr=0)
        br.form['sid'] = sid
        br.form['password'] = password
        br.submit()  
        print(br.response().read().decode('utf-8'))
        Soup = BeautifulSoup(br.response().read().decode('utf-8'), "html.parser")
        response = Soup.find("ul",id="error-ui")
        if str(response) == '<ul id="error-ui"><p>SEGA ID or password is incorrect.</p></ul>':
            errorMsg = "SEGA ID or password is incorrect"
            return False,errorMsg
        elif str(response) == '<ul id="error-ui"><p>The entered SEGA ID is currently subject to login restrictions. Please log in again after a while.</p></ul>' :
            errorMsg = "The entered SEGA ID is currently subject to login restrictions. Please log in again after a while."
            return False,errorMsg
        
        else:
            
            return True,[br,cj]
    except HTTPError as e:
        print(type(e.code))
        if e.code == 503:
            errorMsg = "Mainet is under maintenance, try logging in again after 6AM SGT."
            return False,errorMsg
    

    
    #print(getProfileInfo(cj))
    #idlist = ['1966fddeb5348bd342e830fa9b1a05178d9d4fdc23e45ec51ac643d0a6c8a4044285d6152ce87ceb7f1fdb62da8bcf963972d922b18914b89527868f6fa7e96cpivcdWOT67p9HdUvU68q41fwisrUfc7FXncpBHNxzCE=']
    #print(getSongInfo(idlist))
    #csvfile = open("mainet.csv","a",encoding='utf-8')
    # getScores(basicURL)
    # getScores(advancedURL)

    # startTime = time.time()
    # print(getSongs(versionDict,3))
    # print("--- %s seconds ---" % (time.time() - startTime))
    # getScores(remasterURL)
    #getProfileInfo(cj,True,True)
    #print(getScores(masterURL))



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

basicURL = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=0"
advancedURL = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=1"
expertURL = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=2"
masterURL = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=3"
remasterURL = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=4"
urlList = [basicURL,advancedURL,expertURL,masterURL,remasterURL]
#ret,br,cj = initialize()
#print(getScores(br,cj,urlList))