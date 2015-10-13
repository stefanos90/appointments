$(document).ready(function() {
//dates has the dates (2015-05-05)
//dateVisits has the count for each date
var data = {
    labels: dates,
    datasets: [
        {
            label:'Visits',
            strokeColor: "red",
            fillColor: "rgba(234, 143, 94, 0.3)",
            strokeColor: "rgba(220,220,220,0.6)",
            pointColor: "#B94282",
            pointStrokeColor: "#fff",
            pointHighlightFill: "red",
            pointHighlightStroke: "rgba(151,100,205,1)",
            data: dateVisits
        },
        /*{
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [28, 48, 40, 19, 86, 27, 90]
        }*/
    ]
};
if (dateVisits!='undefined') {
    var total = 0;
    for (var i = 0; i < dateVisits.length; i++) {
        total=total+parseInt(dateVisits[i], 10 ); //parseint converts string to int 
        
    };
    $('#showCount').html("Total appointments: "+total);

};

if($("#visits").length>0) {
    var ctx = document.getElementById("visits").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data);
}






});