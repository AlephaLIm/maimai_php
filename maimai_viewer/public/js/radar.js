$("document").ready(function(){
    const labels = ['Tap', 'Slide', 'Hold', 'Break', 'Touch', 'Ex'];
    var radar_elements = document.getElementsByClassName('radar');

    Array.prototype.forEach.call(radar_elements, function(r_element) {
        let dataset = [];
        dataset.push(r_element.dataset.tap, r_element.dataset.slide, r_element.dataset.hold, r_element.dataset.break, r_element.dataset.touch, r_element.dataset.ex);
        
        var data = {
            labels: labels,
            datasets: [{
                label: 'Notes Breakdown',
                data: dataset,
                backgroundColor: [
                    'rgb(241, 14, 122, 0.3)', //tap 
                    'rgb(0, 255, 255, 0.3)',//slide
                    'rgb(172, 0, 6, 0.3)',//hold
                    'rgb(255, 110, 25, 0.3)',//break
                    'rgb(255, 235, 41, 0.3)',//touch
                    'rgb(255, 255, 255, 0.3)'//ex
                ],
                borderColor: [
                    'rgb(241, 14, 122)', //tap 
                    'rgb(0, 255, 255)',//slide
                    'rgb(172, 0, 6)',//hold
                    'rgb(255, 110, 25)',//break
                    'rgb(255, 235, 41)',//touch
                    'rgb(255, 255, 255)'//ex
                ],
                hoverOffset: 1.5
            }]
        };
        
        let config = {
            backgroundColor: '#1b1b1b',
            type: 'doughnut',
            data: data,
        };

        var PieChart = new Chart(r_element, config);
    });

});