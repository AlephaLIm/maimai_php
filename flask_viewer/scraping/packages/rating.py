def CalculateRating(score, chart_constant):
    score = float(score)
    #print(f"Score in rating calculator is {score}")
    chart_constant = float(chart_constant)
    if score<80:
        return 0
    elif score>=100.5:
        return(int(22.512 * chart_constant))

   
    # for i in result:
    #     print(i,type(i))
    print(score,type(score))
    result = GetValues(score)
    print(result)
    car = result[0]     #Current achievement range
    nar = result[1]   #Next achievement range
    FactorL = result[2][0]     #Factor lower value
    FactorH = result[2][1]     #Factor higher value

    #print(car, nar, FactorL, FactorH)

    score_diff = score - car
    score_progress = score_diff / (nar - car)
    multiplier = (score_progress * (FactorH - FactorL)) + FactorL
    rating = int(multiplier * chart_constant)

    return(rating)


def GetValues(score):
    achievement_range = [80.0,90.0,94.0,97.0,98.0,99.0,99.5,100.0,100.5]
    factor = [[10.880,12.240],[13.680,14.288],[15.792,16.296],[19.4,19.6],[19.894,20.097],[20.592,20.696],[20.995,21.1],[21.6,21.708]]

    for i in range(len(achievement_range)):
        if float(score) < 80.0:
            return (None,80.0,[0.0,0.0])
        elif float(score) < float(achievement_range[i]):
            return (achievement_range[i-1], achievement_range[i], factor[i-1])


#print(GetValues(99.7299)[0])
#print(CalculateRating(100.0303,13.9))