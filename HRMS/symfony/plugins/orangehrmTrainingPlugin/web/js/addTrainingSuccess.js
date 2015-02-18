	$(document).ready(function() {
		var numberOfSchedules = 0;
		var widDiv = $('#widContainer');
		if(trainingId > 0) {
			$.each(details, function(i, item) {
            	setWidgets(item.id, item.date, item.topic, item.desc, item.fromtime, item.totime, window.i);
				window.i++;
			});
		}
		
		$.fn.serializeObject = function()
		{
		    var o = {};
		    var a = this.serializeArray();
		    $.each(a, function() {
		        if (o[this.name] !== undefined) {
		            if (!o[this.name].push) {
		                o[this.name] = [o[this.name]];
		            }
		            o[this.name].push(this.value || '');
		        } else {
		            o[this.name] = this.value || '';
		        }
		    });
		    return o;
		};
		
		$('#widContainer input.sessionDate').each(function (i) {
			if($(this).val()) {
				widDate = new Date($(this).val()).getTime();
				currentDate = new Date().getTime();
				if(widDate <= currentDate) {
					$("#rmvBtn_"+i).hide();
					$(this).parent().find('input').attr("disabled", "disabled");
					$(this).parent().find('.timeEntry-control').hide();
					$(this).removeClass("validation-error");
				}
			}
		});
		
		$('#btnSave').click(function() {
			var schedule = [];
			var isValid = true;
			
			//validation for session dates
			$('#widContainer input.sessionDate').each(function (event) {	
				var idNo = this.id;
				var getNo = idNo.split("_");
				if(!$('#ssnDate_'+getNo[1]).attr("disabled")) {
					if($(this).val() == ""){
						$("#validate_"+getNo[1]).text("Required");
						$("#ssnDate_"+getNo[1]).addClass("validation-error");
						isValid = false;
						return;
					} else if($(this).datepicker( 'getDate' ) <= new Date()) {
						$("#validate_"+getNo[1]).text("Invalid date");
						$("#ssnDate_"+getNo[1]).addClass("validation-error");
						isValid = false;
						return;	
					} else if($.inArray( $(this).val(), schedule ) > -1 && $(this).val() != "") {
						$("#validate_"+getNo[1]).text("This Date has already been taken");
						$("#ssnDate_"+getNo[1]).addClass("validation-error");
						isValid = false;
						return;				
					} else {
						schedule.push($(this).val());
						$("#ssnDate_"+getNo[1]).removeClass("validation-error");
					}
				}
			});

			//validation for from date.
			$('#widContainer input.sessionFromTime').each(function(){
				var idNo = this.id;
				var getNo = idNo.split("_");
				if($(this).val() == ""){
					$("#validate_fromtime_"+getNo[1]).text("Required");
					$("#validate_fromtime_"+getNo[1]).addClass("showError");
					$(this).addClass("validation-error");
					isValid = false;
					return;
				} else if($('#toEntry_'+getNo[1]).val() != "") {
					if(compareDates($(this).val(), $('#toEntry_'+getNo[1]).val())) {
						$("#validate_fromtime_"+getNo[1]).text("Invalid");
						$("#validate_fromtime_"+getNo[1]).addClass("showError");
						$(this).addClass("validation-error");
						isValid = false;
					} else {
						$("#validate_fromtime_"+getNo[1]).removeClass("showError");
						$(this).removeClass("validation-error");
					}
				} else {
					$("#validate_fromtime_"+getNo[1]).removeClass("showError");
					$(this).removeClass("validation-error");
				}
			});

			$('#widContainer input.sessionToTime').each(function(){
				var idNo = this.id;
				var getNo = idNo.split("_");
				if($(this).val() == ""){
					$("#validate_totime_"+getNo[1]).text("Required");
					$("#validate_totime_"+getNo[1]).addClass("showError");
					$(this).addClass("validation-error");
					isValid = false;
					return;
				} else if($('#fromEntry_'+getNo[1]).val() != "") {
					if(!compareDates($(this).val(), $('#fromEntry_'+getNo[1]).val())) {
						$("#validate_totime_"+getNo[1]).text("Invalid");
						$("#validate_totime_"+getNo[1]).addClass("showError");
						$(this).addClass("validation-error");
						isValid = false;
					} else {
						$("#validate_totime_"+getNo[1]).removeClass("showError");
						$(this).removeClass("validation-error");
					}
				} else {
					$("#validate_totime_"+getNo[1]).removeClass("showError");
					$(this).removeClass("validation-error");
				}
			});
		
			if(numberOfSchedules < 1) {
				$("#addButtonSpan").text("Add atleast one session.");
				$("#addButton").addClass("validation-error");
				$("#addButtonSpan").show();
				isValid=false;
			} else {
				$("#addButtonSpan").removeClass("validation-error");
				$("#addButtonSpan").hide();
			}
			
			if($('#frmAddTraining').valid() && isValid) {
				$('#widContainer input').attr("disabled", false);
				showBlockUI();
				$('#training_trainingDates').val(JSON.stringify($('#frmAddTraining').serializeObject()));
 				$('#frmAddTraining').submit();
			}
		});

		//to add new schedule
        $("#addButton").live('click', function(){
        	numberOfSchedules++;
	        var i = numberOfSchedules;
				stringHTML =  '<input type="hidden" name = "ssn_'+i+'_id" value = "">';
				stringHTML += '<input type="text" style="width:150px" id="ssnDate_'+i+'" size="30" name="ssn_'+i+'_date"  placeholder="Session Date" class = "calender sessionDate"/>';
				stringHTML += '<span id="validate_'+i+'" class="valid-date"></span>';
				stringHTML += '<div class = "inlineElements">';
				stringHTML += '<input type="text" class = "sessionFromTime" value="" name="ssn_'+i+'_from" placeholder="From Time" id="fromEntry_'+i+'" style="width:67px">';
				stringHTML += '<span id="validate_fromtime_'+i+'" class = "validation-error fromTimeMsg"></span>';
            	stringHTML += '</div>';
				stringHTML += '<div class = "inlineElements">';
				stringHTML += '<input type="text" class = "sessionToTime" value="" name="ssn_'+i+'_to" placeholder="To Time" id="toEntry_'+i+'" style="width:67px">';
				stringHTML += '<span id="validate_totime_'+i+'" class = "validation-error toTimeMsg"></span>';
            	stringHTML += '</div>';
				stringHTML += '<div class = "inlineElements">';
            	stringHTML += '<input type="text" id="ssnTopic_'+i+'" size="200" name="ssn_'+i+'_topic" value="" placeholder="Topic" />';
            	stringHTML += '</div>';
            	stringHTML += '<div class = "inlineElements">';
            	stringHTML += '<input type="text" id="ssnDesc_'+i+'" name="ssn_'+i+'_description" value="" placeholder="Description" />';
            	stringHTML += '</div>';
				stringHTML += '<div class = "inlineElements" style = "padding-top:7px;">';
            	stringHTML += '<a href="#" class = "linkss" id = "rmvBtn_'+i+'">Remove</>';
            	stringHTML += '</div>';
	        	stringHTML += '</li>';
	        	
				if(i == 0) {
					stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >Schedule</label>'+ stringHTML;
 				} else {
 	 				stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >&nbsp;</label>'+ stringHTML;
 				}
                $(stringHTML).appendTo(widDiv);
                $('#ssnDate_'+i).datepicker({dateFormat: datepickerDateFormat});
                $('#rowBox_'+i).show('slow');
				$('#validate_'+i).show('slow');
                window.i++;
				$('#fromEntry_'+i).timeEntry();
				$('#toEntry_'+i).timeEntry();
        });

        $('a.links').click(function(e) {
		    e.preventDefault();
		});

        //to delete added training schedule
        $("div").on('click', 'a.linkss', function(e) {
        	numberOfSchedules--;
        	var id = parseInt(this.id.substring(7));
            $('#rowBox_'+id).remove();
            e.preventDefault();
            return false;
        });

        function setWidgets(id, date , topic, desc, fromtime, totime, i) {
			numberOfSchedules++;
    		var widDiv = $('#widContainer');
    		if(typeof(date)==='undefined') date = "";
    		if(typeof(topic)==='undefined') topic = "";
    		if(typeof(desc)==='undefined') desc = "";
			if(typeof(fromtime)==='undefined') fromtime = "";
			if(typeof(totime)==='undefined') totime = "";
			stringHTML =  '<input type="hidden" name = "ssn_'+i+'_id" value = "'+id+'">';
            stringHTML += '<input type="text" style="width:150px" id="ssnDate_'+i+'" size="30" name="ssn_'+i+'_date" value="" placeholder="Session Date" class = "sessionDate"/>';
            stringHTML += '<span id="validate_'+i+'" class="valid-date"></span>';
			stringHTML += '<div class = "inlineElements">';
			stringHTML += '<input type="text" class = "sessionFromTime" value="" name="ssn_'+i+'_from" placeholder="From Time" id="fromEntry_'+i+'" style="width:67px">';
			stringHTML += '<span id="validate_fromtime_'+i+'" class = "validation-error fromTimeMsg"></span>';
			stringHTML += '</div>';
			stringHTML += '<div class = "inlineElements">';
			stringHTML += '<input type="text" class = "sessionToTime" value="" name="ssn_'+i+'_to" placeholder="To Time" id="toEntry_'+i+'" style="width:67px">';
			stringHTML += '<span id="validate_totime_'+i+'" class = "validation-error toTimeMsg"></span>';
			stringHTML += '</div>';
			stringHTML += '<div class = "inlineElements">';
        	stringHTML += '<input type="text" id="ssnTopic_'+i+'" size="200" name="ssn_'+i+'_topic" value="" placeholder="Topic"/>';
        	stringHTML += '</div>';
        	stringHTML += '<div class = "inlineElements">';
        	stringHTML += '<input type="text" id="ssnDesc_'+i+'" name="ssn_'+i+'_description" value="" placeholder="Description"/>';
        	stringHTML += '</div>';
			stringHTML += '<div class = "inlineElements" style= "padding-top:7px;">';
        	stringHTML += '<a href="#" class = "linkss" id = "rmvBtn_'+i+'">Remove</>';
        	stringHTML += '</div>';
        	stringHTML += '</li>';
				if(i==0) {
					stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >Schedule</label>'+ stringHTML;
    			} else {
					stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >&nbsp;</label>'+ stringHTML;
    			}
            $(stringHTML).appendTo(widDiv);
            $('#ssnDate_'+i).datepicker({dateFormat: datepickerDateFormat});
            $('#ssnDate_'+i).val(date);
            $('#ssnTopic_'+i).val(topic);
            $('#ssnDesc_'+i).val(desc);
			$('#fromEntry_'+i).val(fromtime);
			$('#toEntry_'+i).val(totime);
            $('#rowBox_'+i).show('slow');
			$('#validate_'+i).show('slow');
			$('#fromEntry_'+i).timeEntry();
			$('#toEntry_'+i).timeEntry();
    	}
    	
		//token input for trainer
		$("#training_trainer").tokenInput(trainerList, {
		    prePopulate: prePopulatedTrainers,
		    tokenLimit: 10,
		    preventDuplicates: true,
		    disabled: false,
		    required: true 
		});
		
		$('.token-input-input-token').focusout(function() {
			$('#frmAddTraining').valid();
		});
		
		function showBlockUI(){
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
		
		var validator = $("#frmAddTraining").validate({
			ignore: "",
			 rules: {
			 	'training[topic]': {
				 	required: true,
			 		},
		 		'training[trainingDesc]': {
				 	required: false,
			 		},
		 		'training[attendeePoint]': {
				 	required: true,
			 		},
		 		'training[trainerPoint]': {
				 	required: true,
			 		},
		 		'training[totalHours]': {
				 	required: false,
				 	number: true
			 		},
		 		'training[trainer]': {
				 	required: true,
			 		},
		 		'.sessionDate': {
		 			required: true,
				 	valid_date: function() {
	                    return {
	                        format: datepickerDateFormat, 
	                        required: true, 
	                        displayFormat: displayDateFormat
	                    } 
	                } 
		 			},
			 },
			 messages: {
				'training[topic]': {
					required: lang_required,
					},
				'training[trainingDesc]': {
					required: lang_required,
					},
				'training[attendeePoint]': {
				 	required: lang_required,
			 		},
		 		'training[trainerPoint]': {
				 	required: lang_required,
			 		},
				'training[totalHours]': {
				 	required: lang_required,
				 	number: lang_number
			 		},
				'training[trainer]': {
					required: lang_required,
					},
				'.sessionDate': {
					required: lang_required,
					valid_date: errorForInvalidFormat
			 		},
			 }
		});
		function createDateObject(dateString) {
			var hour = dateString.split(":");
			var timeString = hour[1].split("M");
			var nowDate = new Date();
			var time = timeString[0];
			min = time.substring(0, time.length - 1);
			zone = time.charAt(time.length - 1);
			if(zone == "P" &&  parseInt(hour[0])!= 12)
				hours = parseInt(hour[0]) + 12;
			else
				hours = hour[0];
			nowDate.setMinutes(min);
			nowDate.setHours(hours);
			return nowDate;
		}

		//fuction returns false if first date is lesser than second date
		function compareDates(dateOne, dateTwo) {
			var dateOne = createDateObject(dateOne);
			var dateTwo = createDateObject(dateTwo);
			if(dateOne < dateTwo) {
				return false;
			} else {
				return true;
			}
		}
	});

	