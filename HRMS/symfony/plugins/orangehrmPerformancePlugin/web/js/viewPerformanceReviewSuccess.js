function showRatingDesc(element){
    $('#msgtooltip'+element.id).show();
	fetchRatings(element.id);
}
    
function fetchRatings(kpiId) {
    params = 'kpiId=' + kpiId;
    $.ajax({
        type: 'GET',
        url: getKpiRatingsUrl,
        data: params,
        dataType: 'json',
        success: function(data) {   
            var count = data.length;
            var html = '';
            var rows = 0;
            $('#msgtooltip'+ kpiId).html('');
            if (count > 0) {
                html = "<table class='table'><tr><th>Rating</th></tr>";
                for (var i = 0; i < count; i++) {
                    var css = "odd";
                    rows++;
                    if (rows % 2) {
                        css = "even";
                    }
                     var rate="";
                     switch (data[i]['rate']){
                    	case '1':
                    		rate = "- Unacceptable Performance";
                    		break;
                    	case '2':
                    		rate = "- Satisfactory performance <br />(Needs improvement)";
                    		break;
                    	case '3':
                    		rate = "- Good <br />(Meets expectations)";
                    		break;
                    	case '4':
                    		rate = "- Very Good <br />(Sometimes exceeds expectations)";
                    		break;
                    	case '5':
                    		rate = "- Outstanding";
                    		break;
                    }
                    html = html + '<tr class="' + css + '"><td>'+data[i]['rate']+ ' '+rate+'</td></tr>';
                }
                html = html + '</table>';
            } else {
			   html = "No Rating description found";
            }
            $('#msgtooltip'+kpiId).append(html);
	     }
    });
}

function hidefetchedRatings() {
	 $('.messages').hide();
}


function selfGoalRatingDesc(id){
	$('#selfGoalsRating'+id).show();
	$('#selfGoalsRating'+id).html('');
	html = "<table class='table'><tr><th>Rating</th></tr>";
	html = html + "<tr class='odd'><td> 1 - Unacceptable Performance </td></tr>";
	html = html + "<tr class='even'><td>2 - Satisfactory performance <br />(Needs improvement) </td></tr>";
	html = html + "<tr class='odd'><td>3 - Good <br />(Meets expectations) </td></tr>";
	html = html + "<tr class='even'><td>4 - Very Good <br />(Sometimes exceeds expectations) </td></tr>";
	html = html + "<tr class='odd'><td>5 - Outstanding </td></tr>";
	html = html + "</table>";
	$('#selfGoalsRating'+id).append(html);
}
function hideselfGoalRatingDesc(){
	hidefetchedRatings();
}