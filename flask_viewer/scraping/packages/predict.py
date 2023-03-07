import scraping.packages.database as database
from pony.orm import * 



def CalculateRating(score, chart_constant):
    score = float(score)
    if score<80:
        return 0
    elif score>=100.5:
        return(int(22.512 * chart_constant))
    #print(f"Score in rating calculator is {score}")
    chart_constant = float(chart_constant)
    

   
    # for i in result:
    #     print(i,type(i))
   
    result = getAR(score)
   
    car = result[0]     #Current achievement range
    nar = result[1]   #Next achievement range
    FactorL = result[2][0]     #Factor lower value
    FactorH = result[2][1]     #Factor higher value

    #print(car, nar, FactorL, FactorH)

    score_diff = score - car
    score_progress = score_diff / (nar - car)
    multiplier = (score_progress * (FactorH - FactorL)) + FactorL
    rating = int(multiplier * chart_constant)

    return rating


def getAR(score):
    achievement_range = [80.0,90.0,94.0,97.0,98.0,99.0,99.5,100.0,100.5]
    factor = [[10.880,12.240],[13.680,14.288],[15.792,16.296],[19.4,19.6],[19.894,20.097],[20.592,20.696],[20.995,21.1],[21.6,21.708],[22.512,22.512]]

    for i in range(len(achievement_range)):
        if float(score) < 80.0:
            return (None,80.0,[0.0,0.0],[10.880,12.240])
        elif float(score) < float(achievement_range[i]):
            return (achievement_range[i-1], achievement_range[i], factor[i-1],factor[i])
        elif float(score) >= 100.5:
            return(None,None,[22.512,22.512],None)
@db_session
def calculateAverage(userid):
    averageScores = []
    levels = select(c.level for c in database.Charts)[:]
    for level in levels:
        averageScore = avg(s.score for s in database.Scores if s.chartid.level == level)
        
        convert = float(level.replace("+",".7"))
        
        if averageScore != None:
            averageScores.append((convert,averageScore))
        else:
            averageScores.append((convert,0))
        

    averageScores = sorted(averageScores, key=lambda x : x[0])
   
    maxtotal = 0
    maxtap = 0
    maxslide = 0
    maxtouch = 0
    maxhold = 0
    maxbreak = 0
    playertotal = 0
    playertap = 0
    playerslide = 0
    playertouch = 0
    playerhold = 0
    playerbreak = 0

    charts = select(c for c in database.Charts)[:]
    for chart in charts:

        radartotal = chart.radartotal
        if radartotal == None:
            radartotal = 0
        radartap = chart.radartap
        if radartap == None:
            radartap = 0
        radarslide = chart.radarslide
        if radarslide == None:
            radarslide = 0
        radarhold = chart.radarhold
        if radarhold == None:
            radarhold = 0
        radartouch = chart.radartouch
        if radartouch == None:
            radartouch = 0
        radarbreak = chart.radarbreak
        if radarbreak == None:
            radarbreak = 0
        
        maxtotal+=5*radartotal
        maxtap+=5*radartap
        maxslide+=5*radarslide
        maxtouch+=5*radartouch
        maxhold+=5*radarhold
        maxbreak+=5*radarbreak

        score = database.Scores.get(chartid = chart.chartid, userid = userid)
        if score != None:
            playerscore = score.score

        else:
            chartlevel = float(chart.level.replace("+",".7"))
            count = 0
            for i in averageScores:
                
                if i[0] == chartlevel:
                    scoreAverage = i[1]
                    break
                count+=1
            
            for i in range(count+1,len(averageScores)):
                if averageScores[i][1] > scoreAverage:
                    scoreAverage = averageScores[i][1]

            if level == "15" and scoreAverage == 0:
                scoreAverage = 0.25
            playerscore = scoreAverage - 0.25
        
        if playerscore >= 100.9:
            weight = 5
        
        elif playerscore >= 100.75:
            weight = 4.75
        
        elif playerscore >= 100.5:
            weight = 4.5

        elif playerscore >= 100.25:
            weight = 3
        
        elif playerscore >= 100:
            weight = 2.5

        elif playerscore >= 99.9:
            weight = 2

        elif playerscore >= 99.75:
            weight = 1.75
        
        elif playerscore >= 99.5:
            weight = 1.5
        
        elif playerscore >= 99:
            weight = 1.25
        
        elif playerscore >= 98:
            weight = 1

        elif playerscore >= 97:
            weight = 0.75
        
        else:
            weight = 0.25
        
        playertotal+=weight*radartotal
        playertap+=weight*radartap
        playerslide+=weight*radarslide
        playertouch+=weight*radartouch
        playerhold+=weight*radarhold
        playerbreak+=weight*radarbreak

    results = {
        'maxtotal' : maxtotal,
        'maxtap' : maxtap,
        'maxslide' : maxslide,
        'maxtouch' : maxtouch,
        'maxhold' : maxhold,
        'maxbreak' : maxbreak,
        'playertotal' : playertotal/maxtotal,
        'playertap' : playertap/maxtap,
        'playerslide' : playerslide/maxslide,
        'playertouch' : playertouch/maxtouch,
        'playerhold' : playerhold/maxhold,
        'playerbreak' : playerbreak/maxbreak
    }
    #print(results)
    return results, averageScores
   #averageLevel= avg(s for s in database.Scores if s.userid.userid == userid)[:]




@db_session       
def generateValues(userid):
    scores = select(s for s in database.Scores if s.userid.userid == userid)[:]
    #print(type(scores))
    festivalLow = select(s for s in database.Scores if s.chartid.parentsong.version == "FESTiVAL" and s.userid.userid == userid).without_distinct().order_by(desc(database.Scores.rating))[:15]
    othersLow = select(s for s in database.Scores if s.chartid.parentsong.version != "FESTiVAL" and s.userid.userid == userid).without_distinct().order_by(desc(database.Scores.rating))[:35]
    
    #print(festivalLow[-1].chartid.parentsong.name, festivalLow[-1].chartid.difficulty, festivalLow[-1].rating)
    #print(othersLow[-1].chartid.parentsong.name, othersLow[-1].chartid.difficulty, othersLow[-1].rating)
    # return
    potential = []
    for score in scores:
        weight = 0
        currentScore = score.score
        constant = score.chartid.constant
        name = score.chartid.parentsong.name
        version = score.chartid.parentsong.version
        if version == "FESTiVAL":
            target = festivalLow[-1]
        else:
            target = othersLow[-1]
        if currentScore >= 100.5:
            continue
        else:
            
            arValues = getAR(currentScore)
            nextRange = arValues[1]
            nextFactorRange = arValues[3]
            currentRating = CalculateRating(currentScore,constant)
            nextRating = CalculateRating(nextRange,constant)
            #(f"{name} - {currentScore} - {currentRating} - {nextRange} - {nextRating} - +{nextRating-currentRating}")
            if nextRating > target.rating:
                 #check how close current score is to next jump, assign weights based on that, closer = higher weight
                if nextRange - currentScore <= 0.1:
                    weight+=5
                elif nextRange - currentScore <= 0.2:
                    weight+=4
                elif nextRange - currentScore <= 0.3:
                    weight+=3
                elif nextRange - currentScore <= 0.4:
                    weight+=2
                elif nextRange - currentScore <= 0.5:
                    weight+=1
                else:
                    weight+=0.5

                #check potential rating gain, assign weight based on that, higher gain = higher weight
                potentialRatingGain = nextRating - target.rating 

                if potentialRatingGain >= 10:
                    weight+=3
                elif potentialRatingGain >= 7:
                    weight+=2
                elif potentialRatingGain >= 4:
                    weight+=1
                else:
                    weight+=0.5
                

                #add function to calculate player skill, then compare skill to note distribution of chart, assign weight based on how close skill is to chart radar values, if  radar value is None, skip this step
                results, avg_scores = calculateAverage(userid)
                playertotal = results['playertotal']
                playertap = results['playertap']
                playerslide = results['playerslide']
                playertouch = results['playertouch']
                playerhold = results['playerhold']
                playerbreak = results['playerbreak']
                totalratio = playertotal / score.chartid.radartotal
                tapratio = playertap / score.chartid.radartap
                slideratio = playerslide / score.chartid.radarslide
                holdratio = playerhold / score.chartid.radarhold
                #print(score.chartid.parentsong.name)
                if (score.chartid.radartouch == 0):
                    touchratio = 1
                else:
                    touchratio = playertouch / score.chartid.radartouch
                breakratio = playerbreak / score.chartid.radarbreak

                values = [playertotal,playertap,playerslide,playerhold,playertouch,playerbreak]
                ratios = [totalratio,tapratio,slideratio,touchratio,holdratio,breakratio]
                for ratio in ratios:
                    if ratio >= 1:
                        weight+=10
                    elif ratio <= 0.6:
                        weight+=6
                    elif ratio <= 0.7:
                        weight += 7
                    elif ratio <= 0.8:
                        weight +=8
                    elif ratio <= 0.9:
                        weight +=9
                    else:
                        weight +=1
                
                potential.append((score,weight,nextRange,potentialRatingGain))
    

    return sorted(potential, key= lambda x : x[1],reverse=True)[:10]