	$(document).ready(function() {
		stroll.bind('#mainList ul');
		$('#cssmenu').prepend('<div id="bg-one"></div><div id="bg-two"></div><div id="bg-three"></div><div id="bg-four"></div>');
		$("#eventCal").eventCalendar({
			jsonData: trainingJSON,
			jsonDateFormat: 'human',
			eventsScrollable: true
		});
		
		// filter selection 
		$('a.filter').each(function() {
			if(trim($(this).html()) == filterValue) {
				$(this).parent("#cssmenu li").addClass("active");
			}
		});

		$('a.viewDetails,#linkTable a').click(function(e){
			e.preventDefault();
		});

		$('.regButton,.unregButton,.disabledButtonTwo,.disabledButton,.registeredBtn').click(function(e) {
	 		e.preventDefault();
	    });
		
		$('.viewAttendance').click(function(){
			id = (this.id).split("_");
			window.location = linkForAttendance+'?id='+id[1];
		});

	    $('#regBoxBtn').click(function() {
			trngid = $(this).attr("trainingId");
			showBlockUI();
			if($('#rgBtn_'+trngid).hasClass('regButton')) {
				$('#rgBtn_'+trngid).removeClass('regButton');
				$('#rgBtn_'+trngid).addClass('disabledButton');
				registerTraining(trngid);
			} else {
				if($('#rgBtn_'+trngid).hasClass('unregButton')) {
					$('#rgBtn_'+trngid).removeClass('unregButton');
					$('#rgBtn_'+trngid).addClass('disabledButtonTwo');
					unregisterTraining(trngid);
				}
			}
		});
		
		$('.regButton,.unregButton').click(function(e){
			showBlockUI();
			id = (this.id).split("_");
			trainingId = id[1];
			$("#eventCal").empty();
			$("#eventCal").html('<img src="'+loadingImg+'" style = "vertical-align: middle;"></img>');
			if($(this).hasClass('regButton')) {
				$('#rgBtn_'+trainingId).removeClass('regButton');
				$('#rgBtn_'+trainingId).addClass('disabledButton');
				registerTraining(trainingId);
			} else {
				if($(this).hasClass('unregButton')) {
					$('#rgBtn_'+trainingId).removeClass('unregButton');
					$('#rgBtn_'+trainingId).addClass('disabledButtonTwo');
					unregisterTraining(trainingId);
				}
			}
		});

		$('a.viewDetails').click(function() {
			id = (this.id).split("_");
			trainingId = id[1];
			$('#regBoxBtn').attr("trainingId", trainingId);
			$('#regBoxBtn').show();
			if($('#rgBtn_'+trainingId).hasClass('regButton')) {
				$('#regBoxBtn').attr("value","Register");
			} else if($('#rgBtn_'+trainingId).hasClass('unregButton')) {
				$('#regBoxBtn').attr("value","Unregister");
			} else {
				$('#regBoxBtn').hide();
			}
			$('#viewDetailsBox').modal();
			$('.modal-body').html('Loading..');
			$.ajax({
				type: 'GET',
				url: linkForDetails,
				data: { tid : trainingId},
				dataType: 'text',
				success: function(data) {
					$('.modal-body').html(data);
			    }
		    });
		});

		$('.filter').click(function() {
			var filter = $(this).html();
			$('#frmShowTraining').attr('action', $('#frmShowTraining').attr('action')+'?filter='+filter);
			$('#frmShowTraining').submit();
		});
	});

	function showBlockUI() {
		$.blockUI({
    		message: $('#domMessage'),
    		css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            }
    	});
	}

	function registerTraining(trainingId) {
		$.ajax({
			type: 'GET',
			url: linkForReg,
			data: { tid : trainingId, isUnreg : 'no'},
			dataType: 'json',
			success: function(data) {
		        if(data['status'] == 'failure') {
					alert('You can not register as you are trainer for this training');
					$('#rgBtn_'+trainingId).addClass('regButton');
		        } else {
					$('#rgBtn_'+trainingId).addClass('unregButton');
		        }
	        	$("#eventCal").empty();
	        	$("#eventCal").eventCalendar({
	    			jsonData: data['data'],
	    			jsonDateFormat: 'human'
	    		});
		        $('#rgBtn_'+trainingId).removeClass('disabledButton');
				$.unblockUI(null);
		    }
	    });
	}

	function unregisterTraining(trainingId) {
		$.ajax({
	        type: 'GET',
	        url: linkForReg,
	        data: { tid : trainingId, isUnreg : 'yes'},
	        dataType: 'json',
	        success: function(data) {
	        	$("#eventCal").empty();
	        	$("#eventCal").eventCalendar({
	    			jsonData: data['data'],
	    			jsonDateFormat: 'human'
	    		});
	        	$('#rgBtn_'+trainingId).removeClass('disabledButtonTwo');
				$('#rgBtn_'+trainingId).addClass('regButton');
				$.unblockUI(null);
		    }
	    });
	}