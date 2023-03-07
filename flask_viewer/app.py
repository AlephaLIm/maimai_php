from flask import Flask, render_template, url_for, request, redirect, jsonify, make_response
from scraping.packages import mainetscraper
from scraping.packages import database
from scraping.packages import predict
import re
from pony.orm import *

app = Flask(__name__)

#Adapt database data to suitable frontend display data
type_converter = {
    "Basic":"BASIC",
    "Advanced":"ADVANCED",
    "Expert":"EXPERT",
    "Master":"MASTER",
    "Remaster":"Re:MASTER"
}

#Set specific color sets depending on difficulty
d_c_palette = {
    "BASIC": {"base":"#02A726", "bg":"#1F3615", "rate":"#1BD644", "accent":"#81D955", "text":"#B00000"},
    "ADVANCED": {"base":"#D57100", "bg":"#3E1600", "rate":"#F8B709", "accent":"#CBA560", "text":"#00BBD8"},
    "EXPERT": {"base":"#AB0000", "bg":"#2E1A1A", "rate":"#AD2E24", "accent":"#FE8994", "text":"#CBC400"},
    "MASTER": {"base":"#6D00C1", "bg":"#180020", "rate":"#8000E2", "accent":"#C99FC9", "text":"#F0C400"},
    "Re:MASTER": {"base":"#743978", "bg":"#270E1D", "rate":"#DADADA", "accent":"#E3ABF4", "text":"#E71D36"}
}

#Set colors for chart type
c_t_color = {"Standard":"#00ABDA", "DX":"#FFBF00"}

#define frontend user class
class User_front:
    def __init__(self, id, name, title, img, rating):
        self.id = id
        self.name = name
        self.title = title
        self.img = img
        self.rating = rating

#define frontend chart detail class
class Detail:
    def __init__(self, name, alias, img, artist, genre, bpm, version):
        self.name = name
        self.alias = alias
        self.img = img
        self.artist = artist
        self.genre = genre
        self.bpm = bpm
        self.version = version

#define frontend chart user stats class
class U_stats:
    def __init__(self, id, score, rank, rating, ap, fsd, dxscore):
        self.id = id
        self.score = score
        self.rank = rank
        self.rating = rating
        self.ap = ap
        self.fsd = fsd
        self.dxscore = dxscore

#define frontend chart note types class
class ChartNotes:
    def __init__(self, total, tap, slide, hold, br_count, touch, ex):
        self.total = total
        self.tap = tap
        self.slide = slide
        self.hold = hold
        self.br_count = br_count
        self.touch = touch
        self.ex = ex

#define parent frontend chart class 
class Chart:
    def __init__(self, chart_id, level, diff, cons:float, c_type, c_palette, m_color, detail:Detail, user_stats:U_stats, notes:ChartNotes, chart):
        self.chart_id = chart_id
        self.level = level
        self.diff = diff
        self.cons = cons
        self.c_type = c_type
        self.c_palette = c_palette
        self.m_color = m_color
        self.detail = detail
        self.user_stats = user_stats
        self.notes = notes
        self.chart = chart

#dynamic sorter key builder based on lambda expression
def sorter_f(x:list):
    if not x:
        return 
    else:
        replacer = {"level":"level_convert(y.level)",
                    "constant":"y.cons",
                    "score":"y.user_stats.score",
                    "dx_score":"y.user_stats.dxscore",
                    "sync":"sort_ap_fsd(y.user_stats.fsd)",
                    "combo":"sort_ap_fsd(y.user_stats.ap)"}
        default = "lambda y: ("
        for i in x:
            default += (replacer[i] + ", ")
        default += ")"
        return eval(default)

#dynamic filter key builder based on lambda expression
def filter_f(g:bool, v:bool, l:bool):
    exp = "lambda s: "
    if g or v or l: 
        if g:
            exp += "s.parentsong.genre in exp['genre']"
        if v and (g == True):
            exp += " and s.parentsong.version in exp['version']"
        elif v:
            exp += "s.parentsong.version in exp['version']"
        if (l == True) and (v or g):
            exp += " and s.level in exp['level']"
        elif l:
            exp += "s.level in exp['level']"
        return exp
    else:
        return 

#Convert dx scores to number of stars
def dx_convert(x):
    (top, bot) = x.replace(',','').split('/')
    ratio = float(top) / float(bot)
    if ratio >= 0.97:
        return 5
    elif ratio >= 0.95:
        return 4
    elif ratio >= 0.93:
        return 3
    elif ratio >= 0.90:
        return 2
    elif ratio >= 0.85:
        return 1
    else:
        return 0

#Convert levels to a numerical value; helper function for sorter_f
def level_convert(x):
    if x[-1] == '+':
        return (float(x[:-1]) + 0.5)
    else:
        return float(x)

#Convert combo and sync grades to numerical equivalents; helper function for sorter_f
def sort_ap_fsd(x):
    value_dict = { '':0, "FC":1, "FC+":2, "AP":3, "AP+":4, "FS":1, "FS+":2, "FSD":3, "FSD+":4 }
    return value_dict[x]

#Convert database entity instances to frontend instances based on database.Scores entity
def convert_class(class_list):
    r_list = []
    for entry in class_list:
        if entry.chartid.parentsong.type == "Standard":
            c_type = "SD"
        else:
            c_type = entry.chartid.parentsong.type
        m_color = c_t_color[entry.chartid.parentsong.type]
        diff = type_converter[entry.chartid.difficulty]
        c_palette = d_c_palette[diff]
        dx = dx_convert(entry.dxscore)
        chartname = entry.chartid.parentsong.name + entry.chartid.difficulty + entry.chartid.parentsong.type
        specialchars = '"/\?%,.*:><|;='
        for char in specialchars:
            chartname = chartname.replace(char,"")
        file = chartname
        detail = Detail(entry.chartid.parentsong.name, entry.chartid.parentsong.alias, entry.chartid.parentsong.jacket, entry.chartid.parentsong.artist, entry.chartid.parentsong.genre, entry.chartid.parentsong.bpm, entry.chartid.parentsong.version)
        u_stats = U_stats(entry.userid, entry.score, entry.scoregrade, entry.rating, entry.combograde, entry.syncgrade, dx)
        notes = ChartNotes(entry.chartid.totalnotecount, entry.chartid.tapcount, entry.chartid.slidecount, entry.chartid.holdcount, entry.chartid.breakcount, entry.chartid.touchcount, entry.chartid.excount)
        chart = Chart(entry.chartid.chartid, entry.chartid.level, diff, entry.chartid.constant, c_type, c_palette, m_color, detail, u_stats, notes, file)
        r_list.append(chart)
    return r_list

#Convert database entity instances to frontend instances based on database.Charts entity
def convert_charts(class_list, usr_id):
    r_list = []
    for entry in class_list:
        if entry.parentsong.type == "Standard":
            c_type = "SD"
        else:
            c_type = entry.parentsong.type
        m_color = c_t_color[entry.parentsong.type]
        diff = type_converter[entry.difficulty]
        c_palette = d_c_palette[diff]
        chartname = entry.parentsong.name + entry.difficulty + entry.parentsong.type
        specialchars = '"/\?%,.*:><|;='
        for char in specialchars:
            chartname = chartname.replace(char,"")
        file = chartname
        score = select(s for s in entry.scores if s.userid.userid == usr_id)[:]
        if score:
            dx = dx_convert(score[0].dxscore)
            u_stats = U_stats(score[0].userid, score[0].score, score[0].scoregrade, score[0].rating, score[0].combograde, score[0].syncgrade, dx)
        else:
            u_stats = U_stats(usr_id, 0, '', 0, '', '', 0)
        detail = Detail(entry.parentsong.name, entry.parentsong.alias, entry.parentsong.jacket, entry.parentsong.artist, entry.parentsong.genre, entry.parentsong.bpm, entry.parentsong.version)
        notes = ChartNotes(entry.totalnotecount, entry.tapcount, entry.slidecount, entry.holdcount, entry.breakcount, entry.touchcount, entry.excount)
        chart = Chart(entry.chartid, entry.level, diff, entry.constant, c_type, c_palette, m_color, detail, u_stats, notes, file)
        r_list.append(chart)
    return r_list

#Login route and defined main generator function for login
@app.route("/login", methods=['POST', 'GET'])
def login():
    log_css = 'css/login.css'
    if request.method == 'POST':
        name = request.form['username']
        password = request.form['pass']
        if name == '' or password == '':
            e_user = ""
            e_pass = ""
            if name == '':
                e_user += "Please enter a Username!"
            if password == '':
                e_pass += "Please enter a Password!"
            attempt = True
            return render_template('login.html',
                                    title="Login",
                                    page_css=log_css,
                                    name=name,
                                    password=password,
                                    attempt=attempt,
                                    error_user=e_user,
                                    error_pass=e_pass)
        else:
            response, packet = mainetscraper.initialize(name, password)
            if not response:
                e_user = packet
                name = ''
                password = ''
                attempt = True
                return render_template('login.html',
                                        title="Login",
                                        page_css=log_css,
                                        name=name,
                                        password=password,
                                        attempt=attempt,
                                        error_user=e_user)

            else:
                (br, cj) = packet
                user = mainetscraper.getProfileInfo(br,cj)
                mainetscraper.getScores(br, cj, user.friendcode, mainetscraper.urlList)
                return redirect(url_for('achievement', usr_id=user.friendcode))
    else:
        return render_template('login.html', title="Login", page_css=log_css)

#default route, redirects to login
@app.route("/")
def homepage():
    return redirect("/login")    

#Achievements route, main generator for achievements webpages
@app.route("/achievements/<string:usr_id>")
@app.route("/achievements/<string:usr_id>/<string:filter_id>")
@db_session
def achievement(usr_id,filter_id=None):
    usr = database.UserData.get(userid = usr_id)
    favicon = url_for('static', filename='assets/user_img/' + usr_id + '.png')
    user = User_front(usr_id, usr.username, usr.title, favicon, usr.rating)
    status = "active"
    selected_option = "All songs rating"
    achievement_css = "css/achievement.css"

    all = ''
    new = ''
    old = ''

    new_song_list = []
    old_song_list = []

    new_songs = select(s for s in database.Scores if s.userid.userid == user.id and s.chartid.parentsong.version == "FESTiVAL").order_by(desc(database.Scores.rating))[:15]
    old_songs = select(s for s in database.Scores if s.userid.userid == user.id and s.chartid.parentsong.version != "FESTiVAL").order_by(desc(database.Scores.rating))[:35]

    new_song_list = convert_class(new_songs)
    old_song_list = convert_class(old_songs)
        

    if filter_id == "new-songs-only":
        selected_option = "New songs rating"
        new = "active"
        songs = new_song_list

    elif filter_id == "old-songs-only":
        selected_option = "Old songs rating"
        old = "active"
        songs = old_song_list
    else:
        all = "active"
        songs = sorted(new_song_list + old_song_list, key = lambda x: x.user_stats.rating, reverse=True)
    return render_template('achievement.html',
                            title="Achievement",
                            page_css=achievement_css,
                            user=user,
                            achieve=status,
                            selected_option=selected_option,
                            all=all,
                            new=new,
                            old=old,
                            songs=songs,
                            usr_id=usr_id)

#Default song finder route and generator for base song finder page
@app.route("/songfilter/<string:usr_id>", methods=['GET'])
@db_session
def songfilter(usr_id):
    song_css = "css/songfilter.css"
    usr = database.UserData.get(userid = usr_id)
    favicon = url_for('static', filename='assets/user_img/' + usr_id + '.png')
    user = User_front(usr_id, usr.username, usr.title, favicon, usr.rating)
    status = "active"

    g_status = {}
    v_status = {}
    l_status = {}
    s_status = {}
    set_msg = "-------- Set filters or search to begin --------"

    return render_template('songfilter.html',
                            title="Song filter",
                            page_css=song_css,
                            user=user,
                            song_finder=status,
                            msg=set_msg,
                            g_status=g_status,
                            v_status=v_status,
                            l_status=l_status,
                            s_status=s_status,
                            search='',
                            usr_id=usr_id)

#Song finder redirected route from jquery redirect in template, generates filtered results
@app.route('/songfilter/<string:usr_id>/load', methods=['GET'])
@db_session
def load(usr_id):
    song_css = "css/songfilter.css"
    usr = database.UserData.get(userid = usr_id)
    favicon = url_for('static', filename='assets/user_img/' + usr_id + '.png')
    user = User_front(usr_id, usr.username, usr.title, favicon, usr.rating)
    status = "active"

    args = request.args
    if not args:
        return redirect(url_for('songfilter'))

    song_list = []
    d_list = ["level", "constant", "score", "dx_score", "sync", "combo"]
    g_status = {}
    v_status = {}
    l_status = {}
    s_status = {}
    search = ''
    sids = []
    keyword = args.get('search')
    genre = args.get('genre')
    version = args.get('version')
    level = args.get('level')
    sort_d = args.get('sort')
    key = args.get('key')

    if key is not None:
        key = False
    else:
        key = True
    if keyword != '':
        search = "selected"
        song_a = select(a.songid for a in database.Alias if (keyword in a.alias) or (keyword in a.alias.lower()))[:]
        sids = [s.songid for s in song_a]
        print(sids)
    exp = {}
    if genre is not None:
        g_list = genre.split(',')
        for item in g_list:
            g_status[item] = "selected"
            exp['genre'] = g_list
    if version is not None:
        v_list = version.split(',')
        for item in v_list:
            v_status[item] = "selected"
            exp['version'] = v_list
    if level is not None:
        l_list = level.split(',')
        for item in l_list:
            l_status[item] = "selected"
            exp['level'] = l_list
    f_func = filter_f((genre is not None), (version is not None), (level is not None))
    if f_func is not None and keyword != '':
        charts = database.Charts.select(eval(f_func))[:]
        songs = [s for s in charts if s.parentsong.songid in sids]
        song_list = convert_charts(songs, user.id)
    elif f_func is not None:
        songs = database.Charts.select(eval(f_func))[:]
        song_list = convert_charts(songs, user.id)
    else:
        songs = select(s for s in database.Charts if s.parentsong.songid in sids)
        song_list = convert_charts(songs, user.id)
    if sort_d is not None:
        s_list = sort_d.split(',')
        for item in s_list:
            s_status[item] = "selected"
        song_list.sort(key=sorter_f(s_list), reverse=key)
    else:
        song_list.sort(key=sorter_f(d_list), reverse=key)
    if not song_list:
        set_msg = "-------- No songs found --------"
        if key:
            k_status = ''
        else:
            k_status = "selected"
        return render_template('songfilter.html',
                                title="Song filter",
                                page_css=song_css,
                                user=user,
                                song_finder=status,
                                msg=set_msg,
                                g_status=g_status,
                                v_status=v_status,
                                l_status=l_status,
                                s_status=s_status,
                                key=k_status,
                                search=search,
                                usr_id=usr_id)
    else:
        if key:
            k_status = ''
        else:
            k_status = "selected"
        return render_template('songfilter.html',
                                title="Song filter",
                                page_css=song_css,
                                user=user,
                                song_finder=status,
                                songs=song_list,
                                g_status=g_status,
                                v_status=v_status,
                                l_status=l_status,
                                s_status=s_status,
                                key=k_status,
                                search=search,
                                usr_id=usr_id)

#Recommendations route and generator; implements prediction algorithm
@app.route('/recommendations/<string:usr_id>')
@db_session
def recommendation(usr_id):
    rec_css = "css/rec.css"
    usr = database.UserData.get(userid = usr_id)
    favicon = url_for('static', filename='assets/user_img/' + usr_id + '.png')
    user = User_front(usr_id, usr.username, usr.title, favicon, usr.rating)
    status = "active"
    file = 'assets/user_stats/' + user.id + '.png'
    p_img = url_for('static', filename=file)    

    x = predict.generateValues(user.id)
    song_list = []
    n_score = {}
    rate_gain = {}
    for (score, weight, next, rate) in x:
        song_list.append(score)
        n_score[score.chartid.chartid] = next
        rate_gain[score.chartid.chartid] = rate
    songs = convert_class(song_list)
    return render_template('recommendations.html',
                            page_css=rec_css,
                            user=user,
                            recommend=status,
                            p_img=p_img,
                            songs=songs,
                            score=n_score,
                            rate_gain=rate_gain,
                            usr_id=usr_id)