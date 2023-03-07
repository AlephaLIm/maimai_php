from packages import mainetscraper, database, wikiscraper
import time



def populateDatabase():
    

    startTime = time.time()
    br,cj = mainetscraper.initialize(True)
    # # time2 = time.time() - startTime 

    # # startTime = time.time()
    # mainetscraper.getProfileInfo(br,cj,True,True)
    # # #time3 = time.time() - startTime 

    # # #startTime = time.time()
    # mainetscraper.getSongs(br,cj,mainetscraper.versionDict,True,3)
    
    
    # # #time4 = time.time() - startTime 

    # # #startTime = time.time()
    # # #mainetscraper.getGenreSortID(br,True,True)
    # # #time5 = time.time() - startTime

    # # #startTime= time.time()
    # database.updateXMLValues()
    # # #time6 = time.time() - startTime

    # database.insertNoteData()

    # database.getWikiID()


    # database.getMissingValues(mainetscraper.versionDict)

    # database.constant_validator()

    #mainetscraper.getScores(br,cj,"8015043563620",mainetscraper.urlList,True)

    #database.getAlias()
    
    print("Total time taken: ", time.time()-startTime)



  

    
    
    
    
    


populateDatabase()