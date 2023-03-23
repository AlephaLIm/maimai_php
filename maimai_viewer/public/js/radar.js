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
                    'rgb(123, 0, 218, 0.3)',
                    'rgb(242, 209, 17, 0.3)',
                    'rgb(152, 206, 0, 0.3)',
                    'rgb(172, 0, 6, 0.3)',
                    'rgb(55, 114, 255, 0.3)',
                    'rgb(255, 119, 0, 0.3)'
                ],
                borderColor: [
                    'rgb(123, 0, 218)',
                    'rgb(242, 209, 17)',
                    'rgb(152, 206, 0)',
                    'rgb(172, 0, 6)',
                    'rgb(55, 114, 255)',
                    'rgb(255, 119, 0)'
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