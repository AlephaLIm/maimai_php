
$("document").ready(function(){
    $(".filters, .searchsubmit").click(function(event){
        var selected = document.getElementsByClassName('selected');
        const ifselected = event.target.classList.contains('selected');

        if (ifselected) {
            event.target.classList.remove('selected');
        }
        else {
            event.target.classList.add('selected');
        };

        var dict = {};
        if (selected) {
            for (const el of selected) {
                let name = el.name
                let val = el.value
                if (name != null) {
                    if (!(name in dict)) {
                        dict[name] = val
                    }
                    else {
                        dict[name] = dict[name] + ',' + val 
                    }
                }
            };
        }

        let entrystr = '';
        for (const [k, v] of Object.entries(dict)) {
            entrystr += (k + "=" + v + "&")
        };

        laststr = entrystr + "search=" + document.getElementById('sinput').value

        fetch('/songs/?' + laststr).then(response => response).then(window.location.href = "/songs?" + laststr)
    });
});