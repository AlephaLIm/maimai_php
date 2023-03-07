import numpy as np
import matplotlib
matplotlib.use('Agg')
from matplotlib import pyplot as plt
from matplotlib.spines import Spine
from matplotlib.path import Path
from matplotlib.transforms import Affine2D
from matplotlib.projections.polar import PolarAxes
from matplotlib.projections import register_projection
from scraping.packages import predict
import os
from pony.orm import *


def radar_factory(num_var):
    theta = np.linspace(start=0, stop=2*np.pi, num=num_var, endpoint=False)
    class RadarTransform(PolarAxes.PolarTransform):
        #interpolation to change grid lines
        def transform_path_non_affine(self,path):
            if path._interpolation_steps > 1:
                path=path.interpolated(num_var)
            return Path(self.transform(path.vertices), path.codes)

    class RadarAxes(PolarAxes):
        PolarTransform = RadarTransform
        
        #change spine from circle to heptagon
        def _gen_axes_spines(self):
            spine = Spine(axes=self, spine_type='circle' , path=Path.unit_regular_polygon(num_var))
            spine.set_transform(Affine2D().scale(.5).translate(.5,.5)+self.transAxes)
            return {'polar':spine}
    register_projection(RadarAxes)
    return theta
    


def SaveChart(header, charts, name):
    # header = ["Notes", "Tap", "Slide", "Hold", "Touch", "EX", "Break"]
    
    #angles of labels on chart
    label_placement = radar_factory(len(header))
    #chart size
    plt.figure(figsize=(6,6))                
    
    #plot around a point
    ax = plt.subplot(polar=True)
    #put first label on the top middle of chart
    ax.set_theta_offset(np.pi/2)    
    ax.set_theta_direction("clockwise")
    
    plt.ylim(0,1)

    #plot chart
    plt.plot(label_placement, charts, color = "#FFBF00")    #edit chart outline color
    plt.fill(label_placement, charts, color="#FFBF00")      #edit chart fill color
    
    #add line and label to graph
    lines, labels = plt.thetagrids(np.degrees(label_placement), labels=header, color="#FFBF00")    #edit color of labels  
   
    #colour and transparency of grid lines
    plt.grid(color = "#BA30B6", alpha=0.4)

    os.makedirs("RadarCharts", exist_ok=True)

    #plt.title(name)    #add title of chart
    plt.savefig(("static/assets/user_stats/" +name + ".png"), transparent = True)        #edit save directory of png (../folder/)
    
    plt.close()

headers = ["Notes", "Tap", "Slide", "Hold", "Touch", "Break"]
something, values = predict.generateValues("8015043563620")
SaveChart(headers, values, "8015043563620")

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
    density = Optional(float)
    

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
    
    set_sql_debug(debug)
    db.bind(provider='sqlite', filename='database.sqlite', create_db=True)
    db.generate_mapping(create_tables=True)
   

setup()
@db_session
def GenerateCharts():
    result = Charts.select_by_sql("SELECT * FROM Charts")
    for c in result:
        chartlist = []
        if c.radartotal==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radartotal)

        if c.radartap==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radartap)

        if c.radarslide==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radarslide)
        
        if c.radarhold==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radarhold)
            
        if c.radartouch==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radartouch)       
        
        if c.radarex==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radarex)

        if c.radarbreak==None:
            chartlist.append(0)
        else:
            chartlist.append(c.radarbreak)
        chartname = c.parentsong.name + c.difficulty + c.parentsong.type
        specialchars = '"/\?%,.*:><|;='
        for char in specialchars:
            chartname = chartname.replace(char,"")
        SaveChart(chartlist, chartname)

#generate all radar charts for charts in database
#GenerateCharts()
