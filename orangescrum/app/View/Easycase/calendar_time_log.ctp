<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ROOT; ?>css/fullcalendar/fullcalendar.css"/>
<script src='<?php echo HTTP_ROOT; ?>js/moment.js'></script>
<script src='<?php echo HTTP_ROOT; ?>js/fullcalendar/fullcalendar.min.js'></script>
<?php if(defined('LANG') && LANG == 1 && Configure::read('Config.language') != 'eng') {
$langArr = array('spa' => 'es', 'dum' => 'de', 'ger' => 'de', 'fre' => 'fr', 'por' => 'pt', 'rum' => 'ro', 'tur' => 'tr', 'chi' => 'zh', 'ita' => 'it'); ?>
<script src='<?php echo HTTP_ROOT; ?>js/fullcalendar/lang/es.js'></script>
<?php } ?>
<script src='<?php echo HTTP_ROOT; ?>js/timelog.js'></script>

<script>
	var usr = [];
	$(document).ready(function() {
	    var strURL = HTTP_ROOT + "easycases/";		
		var url = strURL+"getTimeLogs";
		var current_url = '';
		var new_url     = '';
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();               
		var calendar = $('#calendar').fullCalendar({
            lang: '<?php echo $langArr[Configure::read('Config.language')]; ?>',
			header: { 
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: true,
			selectHelper: true,
			defaultView: 'month',
			slotEventOverlap:false,
			eventLimit: 4 ,
			events: function( start, end, timezone, callback ) {
			    $('.fc-button-today').text('Today');			        
			    $('.fc-button-month').text('Month');
			    $('.fc-button-agendaWeek').text('Week');
			    $('.fc-button-agendaDay').text('Day');
			    var year = end.year();
			    var month = end.month();
			    
			    var s_year = start.year();
			    var s_month = start.month();
			    var type ='calendar';
			    new_url  = url;
				var params = parseUrlHash(urlHash);
				var milestone_uid = $('#milestoneUid').val();
				if(params[1]){
					milestone_uid = params[1];
					$('#milestoneUid').val(params[1]);
					if(($('#caseMenuFilters').val() =='milestone') || ($('#caseMenuFilters').val()=='milestonelist'))
						$('#refMilestone').val($('#caseMenuFilters').val());
				}
				$('#select_view div').tipsy({gravity:'n', fade:true});
				var globalkanbantimeout =null;
				var morecontent ='';
				if(type =='calendar'){
					$('#select_view div').removeClass('disable');
					$('#calendar_btn').addClass('disable');
					calenderForTimeLog('calendar');
					$("#caseMenuFilters").val('calendar');
					$(".menu-files").removeClass('active');
					$(".menu-milestone").removeClass('active');
				}
				var casePage = $('#casePage').val();
				$('#caseLoader').show();
				var projFil = $('#projFil').val(); 
				var projIsChange = $('#projIsChange').val(); 
				var customfilter = $('#customFIlterId').value;//Change case type
				var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
				var priFil = $('#priFil').val(); // Filter by Priority
				var caseTypes = $('#caseTypes').val(); // Filter by case Types
				var caseMember = $('#caseMember').val();  // Filter by Member
				var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
				var caseSearch = $('#case_search').val(); // Search by keyword
				var case_date = $('#caseDateFil').val(); // Search by Date
				var case_due_date = $('#casedueDateFil').val(); // Search by Date
				var case_srch = $('#case_srch').val();
				var tskURL = strURL+"getTimeLogs";
				$.post(tskURL,{"from_view_year":s_year,"from_view_month":s_month,"to_view_year":year,"to_view_month":month,"projFil":projFil,"projIsChange":projIsChange,"casePage":casePage,'caseStatus':caseStatus,'customfilter':customfilter,'caseTypes':caseTypes,'priFil':priFil,'caseMember':caseMember,'caseAssignTo':caseAssignTo,'caseSearch':caseSearch,'case_srch':case_srch,'case_date':case_date,'case_due_date':case_due_date,'morecontent':'','milestoneUid':milestone_uid},function(res){
				    $('#caseLoader').hide();
                                       $('#ttl_hr').text("-- --");
                                        $('#blbl_hr').text("-- --");
                                        $('#non_blbl').text("-- --");
                                        $('#est_hrs').hide();
					$('.fc-button-month, .fc-button-agendaWeek, .fc-button-agendaDay, .fc-button-today, .fc-button-next, .fc-button-prev').on('click',function(){
						get_timeloghrs();
					});
				    callback(res);
					get_timeloghrs();
				},'json');
			},
			select: function(start, end, allDay) {
				//var check = $.fullCalendar.formatDate(start,'yyyy-MM-dd');
                var check = moment(start).format('YYYY-MM-DD');
				//var today = $.fullCalendar.formatDate(new Date(),'yyyy-MM-dd');
                var today = moment(new Date()).format('YYYY-MM-DD');
					createlog('0', '', '', check);
			},
			eventClick:function(calEvent, jsEvent, view){
					edittimelogCalendar(calEvent.log_id,calEvent.project_id);
			},
			eventRender: function(event, element) {
			    var prj_typ = $('#projFil').val();
			 	var message = 'Assigned to : '+event.name;
			 	element.find('.fc-time').text(event.duration);
			 	element.find(".fc-time").after("<br/>");
                element.find('.fc-content').attr({title:message,rel:'tooltip'}).tipsy({html: true });
			    var clrCod = '';
				clrCod = getRandomColor(event.uniq_id);
			    if(clrCod != ''){
				    element.find('.fc-content').parent().css('border','1px solid '+clrCod);
				    element.find('.fc-content').css('background-color',clrCod);
			    }			    
			    $('[rel=tooltip]').tipsy({gravity:'s', fade:true});
			},
			editable: false
		    });		    
	});
    /*
    *This function generates random color codes every time 
    *it gets triggered.Each Color is unique for 
    *a resource but they are random.
    */
	function getRandomColor(user_uniq_id) {
		if(!usr[user_uniq_id]){
	    var letters = '0123456789ABCDEF'.split('');
	    var color = '#';
	    for (var i = 0; i < 6; i++ ) {
	        color += letters[Math.floor(Math.random() * 16)];
	    }
	    	return usr[user_uniq_id] = color;
		}else{
			return usr[user_uniq_id];
		}
	}
        var chk_view = '';
	var chk_start = '';
	var chk_end = '';
	function get_timeloghrs(){
		var view = $('#calendar').fullCalendar('getView');
		var t_st = moment(new Date(view.start)).format('YYYY-MM-DD');
		var e_st = moment(new Date(view.end)).format('YYYY-MM-DD');
		var projFil = $('#projFil').val(); 
		if(t_st != chk_start || e_st != chk_end){
			
			chk_start = t_st;
			chk_end = e_st;
                        /** Call to new RequestsController function as per optimization process */
			$.post(HTTP_ROOT + "easycases/get_timeloghrs",{'chk_start':chk_start,'chk_end':chk_end,'projFil':projFil,'is_cnt':1},function(data) {
                                $('#ttl_hr').text(data.total_hr);
                                $('#blbl_hr').text(data.billable_hr);
                                 $('#non_blbl').text(data.non_billable);
                                $('#est_hrs').hide();
                                		
			},'json');
		}
	}
</script>
<style>
#calendar {
    margin-top: 50px;
    margin-bottom: 50px;
    width: 94%;
    margin: 0 auto;
    margin-left:20px
 }
 .fc-event-container{
     z-index: 1 !important;
 }
 .fc-button{
     position: static !important;
 }
 .round_profile_img{
     top: 0px !important;
 }
.fc-past{
     background-color: #F8F8FF;
 }
 .fc-row .fc-content-skeleton td, .fc-row .fc-helper-skeleton td{border-color: #ccc}
 .fc-last, .fc-first{
     background-color: #EEEFFF !important; /*#EDEDED*/
 }
 .fc-widget-header{
     background-color: #fff !important;
 }
 .fc-today{
     background-color: #FCFCCE !important;
 }
</style>
<div id='calendar'></div>
