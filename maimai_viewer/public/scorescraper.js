async function getDetailedInfo(url) {
    var response = await fetch(url);
    var text = await response.text();
    var dom = new DOMParser().parseFromString(text, 'text/html');
    var jacket = dom.querySelector("img.w_180.m_5.f_l").src
    var genre = dom.querySelector("div.m_10.m_t_5.t_r.f_12.blue").innerText.trim();
    var artist = dom.querySelector("div.m_5.f_12.break").innerText.trim()
    var rows = dom.querySelectorAll("td.f_0");
    var levels = [];
    for (i = 0; i < rows.length; i += 2) {
        levels.push(rows[i]);
    }
    for (i = 0; i < levels.length; i++) {


        if (i == 0) {
            var basicLevel = levels[i].innerText.trim();
        }

        else if (i == 1) {
            var advancedLevel = levels[i].innerText.trim();
        }
        else if (i == 2) {
            var expertLevel = levels[i].innerText.trim();
        }
        else if (i == 3) {
            var masterLevel = levels[i].innerText.trim();
        }
        else if (i == 4) {
            var remasterLevel = levels[i].innerText.trim();
        }


    }
    if (remasterLevel == undefined) {
        remasterLevel = null;
    }
    var ret = {
        "jacket": jacket,
        "genre": genre,
        "artist": artist,
        "basicLevel": basicLevel,
        "advancedLevel": advancedLevel,
        "expertLevel": expertLevel,
        "masterLevel": masterLevel,
        "remasterLevel": remasterLevel
    }

    return ret;
}


async function checkLatestVersion() {
    var url = "https://maimaidx-eng.com/maimai-mobile/record/musicVersion/search/?version=19&diff=3";
    var no = [];
    //var no = ["mystique as iris","M.S.S.Planet","劣等上等","分解収束テイル","田中","Random","蜘蛛の糸","Tricolor⁂circuS","フォニイ","ヴィラン","ホシシズク","[X]","ジレンマ","火炎地獄","アニマル","EYE","自傷無色","踊","響縁","Rainbow Rush Story","Luminaria","残響散歌","モザイクロール","スカーレット警察のゲットーパトロール24時"];
    var postCheck = await sendPostRequest("test", "https://maiviewer.ordinarymagician.com/songinfo");
    console.log(postCheck);
    if (postCheck.length == 0) {
        return true;
    }
    else {
        for (const item of postCheck) {
            no.push(item.name);
        }
    }


    //console.log(no);
    try {
        var response = await fetch(url);
        var text = await response.text(); // Replaces body with response
        var dom = new DOMParser().parseFromString(text, 'text/html')
        var nodes = dom.querySelectorAll('div.music_master_score_back.pointer.w_450');
        var toSend = [];
        for (const row of nodes) {

            if (row.children.length == 1) {
                base = row.children[0];
                //console.log(base.children);
                var title = base.children[4].innerText.trim();
                var idx = encodeURIComponent(base.children['idx'].value);
                var detailUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicDetail/?idx=" + idx;
                if (no.includes(title)) {
                    continue;
                }
                else {
                    if (base.children[1].src == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png") {
                        var chartType = "DX";
                    }
                    else {
                        var chartType = "Standard"
                    }

                    var details = await getDetailedInfo(detailUrl);
                    if (title == "Link") {

                        if (details.genre == "maimai") {
                            title = "Link (JOYPOLIS)";
                        }
                        else {
                            title = "Link (Circle of friends)";
                        }
                    }
                    var songid = title.trim() + details.artist.trim() + chartType;
                    songid = songid.replaceAll(/[!,'()"]/g, '');
                    var song = {
                        "name": title,
                        "type": chartType,
                        "songid": songid,
                        "jacket": details.jacket,
                        "genre": details.genre,
                        "artist": details.artist,
                        "basicLevel": details.basicLevel,
                        "advancedLevel": details.advancedLevel,
                        "expertLevel": details.expertLevel,
                        "masterLevel": details.masterLevel,
                        "remasterLevel": details.remasterLevel

                    }
                    toSend.push(song);
                }

            }
        }
        console.log(toSend);
        var resp = await sendPostRequest(toSend, "https://maiviewer.ordinarymagician.com/newsongs");
        if (resp.message == "success") {
            return true;
        }
        else {
            return resp.mesage;
        }



    }
    catch (err) {
        console.log('Error:' + err); // Error handling
    }
}


async function checkLink(url) {
    try {
        var response = await fetch(url);
        var text = await response.text(); // Replaces body with response
        var dom = new DOMParser().parseFromString(text, 'text/html')
        var artist = dom.querySelector('div.m_5.f_12.break');
        return artist.innerText;


    }
    catch (err) {
        console.log('Error:' + err); // Error handling
    }
}


async function fetchScores(url) {
    try {

        var response = await fetch(url); // Gets a promise
        // var blob = await response.blob();
        // var arraybuffer = await blob.arrayBuffer();
        // var text = new TextDecoder('utf-8').decode(arraybuffer);
        var text = await response.text(); // Replaces body with response
        var dom = new DOMParser().parseFromString(text, 'text/html')

        var nodes = dom.querySelectorAll('div.w_450.m_15.p_r.f_0');
        var scoreArray = [];
        for (const row of nodes) {

            //console.log(row.children[0].children[0].children['idx']);
            try {
                var score = row.children[0].children[0].children[4].innerText.trim();
            } catch {
                var score = null;
            }
            if ((score == null) || (score == "")) {
                continue;
            }
            if (row.children[1].src == "https://maimaidx-eng.com/maimai-mobile/img/music_dx.png") {
                var chartType = "DX";
            } else {
                var chartType = "Standard"
            }

            var title = row.children[0].children[0].children[3].innerText.trim();
            if (title == "Link") {
                //console.log(row.children[0].children[0].children['idx']);
                var newUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicDetail/?idx=" + encodeURIComponent(row.children[0].children[0].children['idx'].value);
                var artist = await checkLink(newUrl);
                if (artist.includes("Circle of friends")) {
                    title = "Link (Circle of friends)";
                }
                else {
                    title = "Link (JOYPOLIS)";
                }
                //console.log("Link has been renamed to: ", title);
            }

            var level = row.children[0].children[0].children[2].innerText.trim();



            try {
                var dxscore = row.children[0].children[0].children[5].innerText.trim();
            } catch {
                var dxscore = null;
            }
            var sync;
            try {
                var syncSrc = row.children[0].children[0].children[6].src.replace("https://maimaidx-eng.com/maimai-mobile/img/music_icon_", "");

                if (syncSrc.includes("fsdp")) {
                    sync = "FSD+";
                } else if (syncSrc.includes("fsd")) {
                    sync = "FSD";
                } else if (syncSrc.includes("fsp")) {
                    sync = "FS+";
                } else if (syncSrc.includes("fs")) {
                    sync = "FS";
                } else {
                    sync = null;
                }
            }
            catch {
                sync = null;
            }
            var combo;
            try {
                var comboSrc = row.children[0].children[0].children[7].src.replace("https://maimaidx-eng.com/maimai-mobile/img/music_icon_", "");
                if (comboSrc.includes("app")) {
                    combo = "AP+";
                } else if (comboSrc.includes("ap")) {
                    combo = "AP";
                } else if (comboSrc.includes("fcp")) {
                    combo = "FC+";
                } else if (comboSrc.includes("fc")) {
                    combo = "FC";
                } else {
                    combo = null;
                }

            }
            catch {
                combo = null;
            }







            var scoregrade;

            if (Number.parseFloat(score) > 100.5) {
                scoregrade = "SSS+";
            }
            else if (Number.parseFloat(score) > 100.0) {
                scoregrade = "SSS";
            }
            else if (Number.parseFloat(score) > 99.5) {
                scoregrade = "SS+";
            }
            else if (Number.parseFloat(score) > 99) {
                scoregrade = "SS";
            }
            else if (Number.parseFloat(score) > 98) {
                scoregrade = "S+";
            }
            else if (Number.parseFloat(score) > 97) {
                scoregrade = "S";
            }
            else if (Number.parseFloat(score) > 94) {
                scoregrade = "AAA";
            }
            else if (Number.parseFloat(score) > 90) {
                scoregrade = "AA";
            }
            else if (Number.parseFloat(score) > 80) {
                scoregrade = "A";
            }
            else if (Number.parseFloat(score) > 75) {
                scoregrade = "BBB";
            }
            else if (Number.parseFloat(score) > 70) {
                scoregrade = "BB";
            }
            else if (Number.parseFloat(score) > 60) {
                scoregrade = "B";
            }
            else if (Number.parseFloat(score) > 50) {
                scoregrade = "C";
            }
            else {
                scoregrade = "D";
            }









            var scoreObj = {
                "title": title,
                "level": level,
                "score": parseFloat(score),
                "dxscore": dxscore,
                "type": chartType,
                "combo": combo,
                "sync": sync,
                "scoregrade": scoregrade
            }
            scoreArray.push(scoreObj);


        }
        //console.log(scoreArray);
        //console.log("wan le")
        return scoreArray;

    } catch (err) {
        console.log('Error:' + err); // Error handling
    }
}
async function getFriendCode() {
    try {
        var response = await fetch("https://maimaidx-eng.com/maimai-mobile/friend/userFriendCode/");
        var text = await response.text(); // Replaces body with response
        var dom = new DOMParser().parseFromString(text, 'text/html')
        var friendcode = dom.querySelector('div.see_through_block.m_t_5.m_b_5.p_5.t_c.f_15');
        return friendcode.innerText;


    }
    catch (err) {
        console.log('Error:' + err); // Error handling
    }
}





function blobToBase64(blob) {
    return new Promise((resolve, _) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.readAsDataURL(blob);
    });
}
async function getUserProfile() {

    try {
        var response = await fetch("https://maimaidx-eng.com/maimai-mobile/playerData/");
        var text = await response.text(); // Replaces body with response
        var dom = new DOMParser().parseFromString(text, 'text/html')

        var profilePicture = dom.querySelector('img.w_112.f_l').src;
        var image = await fetch(profilePicture).then(response => {
            return response.blob();

        })

        var b64str = await blobToBase64(image);
        console.log(b64str);
        //console.log(image);
        //var imageURL = URL.createObjectURL(imageBlob);
        //console.log(imageURL);

        //uncomment to view blob data
        // var reader = new FileReader();
        // reader.readAsDataURL(image);
        // var blobData = reader.result;
        // reader.onloadend = function() {
        //     var b64 = reader.result
        // }


        var name = dom.querySelector('div.name_block').innerText;
        var rating = parseInt(dom.querySelector('div.rating_block').innerText);
        var playcount = parseInt(dom.querySelector('div.m_5.m_t_10.t_r.f_12').innerText.replace(/\D/g, ''));
        var courserank = dom.querySelector('img.h_35.f_l').src;
        var classrank = dom.querySelector('img.p_l_10.h_35.f_l').src;
        var title = dom.querySelector('div.trophy_inner_block.f_13').innerText.trim();
        var friendcode = await getFriendCode();
        var user = {
            "name": name,
            "rating": rating,
            "playcount": playcount,
            "picture": b64str.replace("data:image/png;base64,", ""),
            "friendcode": friendcode,
            "classrank": classrank,
            "courserank": courserank,
            "title": title
        };
        //    friendcode.then(value => {
        //     user.friendcode = value;
        //    })

        //    console.log(name);
        //    console.log(rating);
        //    console.log(playcount);
        console.log(user);
        return user;
    }
    catch (err) {
        console.log('Error:' + err); // Error handling
    }
}
async function sendPostRequest(postdata, url) {
    console.log(postdata);
    const rawResponse = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json; charset=utf-8'
        },
        body: JSON.stringify(postdata)
    });
    const content = await rawResponse.json();
    console.log(url)
    console.log(content);
    return content;
};

async function sendFormData(formdata, url) {
    console.log(formdata);
    const rawResponse = await fetch(url, {
        method: 'POST',
        body: formdata
    });
    const content = await rawResponse.json();
    console.log(url)
    console.log(content);
    return content;
};


async function preparePostData() {

    var basicUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=0";
    var advancedUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=1";
    var expertUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=2";
    var masterUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=3";
    var remasterUrl = "https://maimaidx-eng.com/maimai-mobile/record/musicGenre/search/?genre=99&diff=4";

    return await Promise.all([getUserProfile(), fetchScores(basicUrl), fetchScores(advancedUrl), fetchScores(expertUrl), fetchScores(masterUrl), fetchScores(remasterUrl)]).then(async values => {
        //console.log(values);


        var postdata = {
            "user": values[0],
            "basicScores": values[1],
            "advancedScores": values[2],
            "expertScores": values[3],
            "masterScores": values[4],
            "remasterScores": values[5]
        }





        return await sendPostRequest(postdata, 'https://maiviewer.ordinarymagician.com/data').then((postCheck) => {
            if (postCheck == "success") {
                return true;
            }
            else {
                return postCheck;
            }
        })




    });

}

async function main() {

    var verCheck = await checkLatestVersion();
    if (verCheck == true) {
        console.log(verCheck);
        console.log("Version Check Completed");
        var dataCheck = await preparePostData();

        if (dataCheck.message == "success") {
            console.log(dataCheck.message);
            console.log("Scores Uploaded Successfully");
            alert("Scores Scraped Successfully!");
            window.open("https://maiviewer.ordinarymagician.com/login");
        }
        else {
            console.log("Error for preparePostData()");
            console.log(dataCheck.message);
        }
    }
    else {
        console.log("Error for checkLatestVersion()");
        console.log(verCheck);

    }


}

main();












