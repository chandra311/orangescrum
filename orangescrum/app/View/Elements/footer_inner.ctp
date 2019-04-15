</div>
</div>
<span id="remember_filter" style="display:none;color:#666666"></span>
<footer <?php if(CONTROLLER == 'easycases' && PAGE_NAME == 'help'){ ?> class="help_footer" <?php } ?> id="footersection">
	<div class="col-lg-5 ft_lt_div" id="csTotalHours">
	</div>
	<div class="col-lg-2 text-centre ft_md_div">
	Orangescrum  <?php echo VERSION; ?>
	</div>
	<div class="col-lg-5 text-right rht_ft_txt ft_rt_div" id="projectaccess">
	
	</div>
    
    <div style="clear:both"></div>
    <?php /* <div style="text-align:center;padding:10px 0 0 0;">
    <a href="https://groups.google.com/forum/#!forum/orangescrum-community-support" target="_blank" style="margin:0;"><img src="<?php echo HTTP_ROOT."img/google_groups.jpg"; ?>" style="width:100px;"/></a>
    <br/>
    You can ask for help, share your ideas, contribute to the community edition and also let us know your feedback using the <a href="https://groups.google.com/forum/#!forum/orangescrum-community-support" target="_blank" style="margin:0;">Orangescrum's Google Group</a>.
    </div> */ ?>
    <?php if(defined('TLG') && TLG == 1){ ?>
    <div class="timer-div">
        <div class="timer-header" onclick="expandTimer()">
            <div class="timer-sec">
                <span class="timer-time"></span>
                <span class="timer-pause-btn" onclick="pauseTimer(event)"><img height="10" width="15" src="<?php echo HTTP_ROOT; ?>img/pause.png" /></span>
                <span class="timer-play-btn" onclick="resumeTimer(event)" style="display:none"><img height="18" width="15" src="<?php echo HTTP_ROOT; ?>img/play.png" /></span>
            </div>
            <span class="tsk-title" style="display: none"></span>
            <span class="open-activity up"></span>
        </div>
        <div class="timer-detail pr">
            <input type="hidden" id="timer_hidden_tsk_id" />
            <input type="hidden" id="timer_hidden_tsk_uniq_id" />
            <input type="hidden" id="timer_hidden_proj_id" />
            <input type="hidden" id="timer_hidden_proj_nm" />
            <input type="hidden" id="timer_hidden_duration" />
            <div class="control-group">
                <select id="select-timer-proj" placeholder="<?php echo __("Select a Project"); ?>"></select>
            </div>
            <div class="control-group">
                <select id="select-timer-task" placeholder="<?php echo __("Select a Task"); ?>"></select>
            </div>
            <?php /* <span class="tsk-span">Task: </span><span class="tsk-ttl ellipsis-view"></span><div class="cb"></div> */ ?>
            <div class="form-group form-group-lg label-floating timer-desc">
               <input class="form-control" placeholder="<?php echo __("Note"); ?>" id="timerdesc"  onkeyup="setDescription()" />
           </div>
            <div class="checkbox custom-checkbox">
                <label>
                    <input type="checkbox" id="timer_is_billable" checked="checked"/><?php echo __("Is Billable"); ?>?
                </label>
            </div>
            <div class="cb"></div>
            <div class="popup-btn">
                <span class="hover-pop-btn" id="save-tm-span"><a id="save_timer_btn" class="btn btn_blue" onclick ="saveTimer()"><?php echo __("Save"); ?></a></span>
                <span class="hover-pop-btn" id="start-tm-span" style="display:none"><a href="javascript:void(0)" id="start_timer_btn" class="btn btn_blue" onclick ="startTaskTimer()"><?php echo __("Start Timer"); ?></a></span>
                <span class="or_cancel cancel_on_direct_pj" id="cancel-timer-btn"><?php echo __("or"); ?> <a href="javascript:void(0);" onclick="stopTimer();"><?php echo __("Cancel"); ?></a></span>
                <span style="display:none;margin-right:20px;" id="timerquickloading">
                    <img alt="<?php echo __("Loading"); ?>..." title="<?php echo __("Loading"); ?>..." src="<?php echo HTTP_ROOT;?>img/images/case_loader2.gif">
                </span>
                <div class="cb"></div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php  if(defined('CHAT') && CHAT == 1){ ?>
        <!-- Chat button -->
         <div class="chat_btn_btm">         
            <div class="os_plus">    
                <div class="ctask_ttip"><span class="label label-default"><?php echo __("Start Conversation"); ?></span></div>     
                <a href="javascript:void(0)" onclick="chatStart();"><i class="material-icons">&#xE0B7;</i></a>         
            </div>
         </div>
        <span class="chat-count-min"></span>
        <style>
            .ui-dialog.ui-widget.ui-widget-content.ui-corner-all.ui-front.ui-draggable.ui-resizable{-webkit-box-shadow: -34px 32px 51px -31px rgba(0,0,0,0.66);-moz-box-shadow: -34px 32px 51px -31px rgba(0,0,0,0.66);box-shadow: -34px 32px 51px -31px rgba(0,0,0,0.66);}
            .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default{background: none; border: none;}
            .ui-corner-all.round-dialog{border-radius: 50px; -moz-border-radius: 50px; -webkit-border-radius: 50px; left:92% !important; background: #f6911d;}
            .round-dialog button,.round-dialog .ui-dialog-titlebar-close{display:none;}
            .ui-dialog .ui-dialog-titlebar-restore{height:26px !important; width:20px !important; line-height: 50px; color: #fff; position:absolute; right:15px !important; top:4px;}
            .ui-dialog ,.ui-dialog .ui-dialog-content{z-index:99999;padding:0px; }
            .ui-dialog .ui-dialog-titlebar {border-bottom: none;}
            .ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br{border-radious:0px;-webkit-border-radius:0px;}
            .ui-dialog-titlebar{background:#5276a6;}
            .ui-dialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix{z-index:999999; background:none;}
            .ui-dialog .ui-dialog-content{overflow: initial;}
            .ui-dialog .ui-dialog-titlebar-close span{top:0px;left:0px;}
            .chat_btn_btm {position: fixed;right: 4%;top: 85%; z-index: 9;}
            .chat_btn_btm a .material-icons{margin-top:18px;}
            .chat_loading {animation-name: rotate;animation-duration: 2s;animation-iteration-count: infinite;animation-timing-function: linear;}
            @keyframes rotate {from {transform: rotate(0deg);}to {transform: rotate(360deg);}}
            .chat-count-min{position:fixed; top:85%; right:7%; z-index: 999999; background:#253650; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%; min-width:20px; height:20px; font-size:12px; font-weight:bold; color:#fff; text-align: center; display: none;}
        </style>
        
        <!-- End of chat button -->
     <?php } ?>
</footer>
<!-- Footer ends -->  

<script type="text/javascript">
var DOMAIN = '<?php echo DOMAIN; ?>'; //Domain
var HTTP_ROOT = '<?php echo HTTP_ROOT; ?>'; //pageurl
var HTTP_IMAGES = '<?php echo HTTP_IMAGES; ?>'; //hid_http_images
var MAX_FILE_SIZE = '<?php echo MAX_FILE_SIZE; ?>'; //fmaxilesize
var SES_ID = '<?php echo SES_ID; ?>'; //pub_show
var SES_TYPE = '<?php echo SES_TYPE; ?>';
var GLOBALS_TYPE = <?php echo json_encode($GLOBALS['TYPE']); ?>;
var DESK_NOTIFY = <?php echo (int)DESK_NOTIFY; ?>;
var CONTROLLER = '<?php echo CONTROLLER; ?>';
var PAGE_NAME = '<?php echo PAGE_NAME; ?>';
var DEFAULT_TASKVIEW = '<?php echo DEFAULT_TASKVIEW; ?>';
var DEFAULT_MILESTONEVIEW = '<?php echo DEFAULT_MILESTONEVIEW; ?>';
var DEFAULT_PROJECTVIEW = '<?php echo DEFAULT_PROJECTVIEW; ?>';
var ARC_CASE_PAGE_LIMIT = 10;
var ARC_FILE_PAGE_LIMIT = 10;
var PUSERS = <?php echo json_encode($GLOBALS['projUser']); ?>;
var PROJECTS = <?php echo json_encode($GLOBALS['getallproj']); ?>;
var defaultAssign = '<?php echo $defaultAssign; ?>';
var dassign;
var TASKTMPL = <?php echo json_encode($GLOBALS['getTmpl']); ?>;
var SITENAME = 'Orangescrum';
var TITLE_DLYUPD = '<?php echo "Daily Update - ".date("m/d"); ?>';
<?php if(defined('TSG') && TSG == 1){ ?>
var default_stages = <?php echo json_encode($GLOBALS['default_stages']);?>;
<?php } ?>
var TLG = <?php echo TLG; ?>;
var INV = <?php echo INV; ?>;
var TSG = <?php echo TSG; ?>;
var GNC = <?php echo GNC; ?>;
var RCT = <?php echo RCT; ?>;
var API = <?php echo API; ?>;
var CHT = <?php echo CHAT; ?>;
var CR = <?php echo CR; ?>;
var PT = <?php echo PT; ?>;
var MAPI = <?php echo MAPI; ?>;
var TPAY = <?php echo TPAY; ?>;
var LANG = '<?php echo LANG; ?>';
var GTLG = <?php echo GTLG; ?>;
var PROFILE_DTTM = '<?php echo PROFILE_DTTM; ?>';
if(GTLG == 1){
    var CompWorkHR = <?php echo $GLOBALS['company_work_hour'] == '' ? 8 : $GLOBALS['company_work_hour']; ?>;
}
var NODEJS_HOST ='<?php echo NODEJS_HOST; ?>';
var NODEJS_SECURE  =<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?"true":"false"; ?>;
var STATUS_LISTS = <?php echo json_encode( $GLOBALS['status_lists']);?> ;
var WORKFLOW_ID = '<?php echo $GLOBALS['workflow_id'] ; ?>';
var PRJCTID = '<?php echo PROJ_ID ; ?> ';
<?php if(defined('GNC') && GNC == 1){ ?>
    var access_type = 0 ;
  <?php  if(isset($GLOBALS['gantt_access_type'])){?>
     access_type = <?php echo $GLOBALS['gantt_access_type'];?>;
<?php } } ?>
</script>

<script type="text/javascript" src="<?php echo JS_PATH; ?>os_core.js?v=<?php echo RELEASE; ?>"></script>
<?php if((CONTROLLER == 'templates') || (CONTROLLER == 'easycases' && PAGE_NAME == "mydashboard")){ ?>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-ui-1.10.3.js"></script>
<?php }else{ ?>
<!--<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-ui.min.1.8.10.js"></script>-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-ui-1.9.2.custom.min.js"></script>
<?php } ?>
<?php $locale = Configure::read('Config.language');
$langArr = array('spa' => 'es', 'dum' => 'de', 'ger' => 'de', 'fre' => 'fr', 'por' => 'pt', 'rum' => 'ro', 'tur' => 'tr', 'chi' => 'zh', 'ita' => 'it');
if(defined('LANG') && LANG == 1 && $locale != 'eng') { ?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/i18n/jquery-ui-i18n.min.js"></script>
<script>
    $(function(){
        var options = $.extend(
            {},                                 
            $.datepicker.regional["<?php echo $langArr[Configure::read('Config.language')]; ?>"],         
            { dateFormat: "M d, D" }
        );
        $.datepicker.setDefaults(options);
    });
</script>
<link rel="gettext" type="application/x-po" href="/locale/<?php echo $locale ?>/LC_MESSAGES/default.po" />
<link rel="gettext" type="application/x-mo" href="/locale/<?php echo $locale ?>/LC_MESSAGES/default.mo" />
<link href="<?php echo HTTP_ROOT; ?>languages/<?php echo $locale; ?>.json" lang="<?php echo $langArr[$locale]; ?>" rel="gettext" type="application/json" /> 
<?php } ?>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>js/gettext.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>script.js?v=<?php echo RELEASE; ?>"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>easycase_new.js?v=<?php echo RELEASE; ?>"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>status_group.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.tipsy.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.lazyload.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>tinymce/jquery.tinymce.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>selectize.min.js"></script>
<?php if(defined('TLG') && TLG == 1){
    #echo $this->Html->script('resource_availability');
} ?>

<!-- Dropbox starts-->
<?php if(defined('USE_LOCAL') && USE_LOCAL==1) {?>
<script type="text/javascript" src="<?php echo JS_PATH; ?>dropins.js" id="dropboxjs" data-app-key="<?php echo DROPBOX_KEY;?>"></script>
<?php } else {?>
<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropins.js" id="dropboxjs" data-app-key="<?php echo DROPBOX_KEY;?>"></script>
<?php }?>
<!-- Dropbox ends-->

<!-- Google drive starts-->
<script type="text/javascript">
    var CLIENT_ID = "<?php echo CLIENT_ID; ?>";
    var REDIRECT = "<?php echo REDIRECT_URI; ?>";
    var API_KEY = "<?php echo API_KEY; ?>";
    var DOMAIN_COOKIE = "<?php echo DOMAIN_COOKIE; ?>";
</script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>google_drive.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>google_contact.js"></script>
<?php /*if(defined('USE_LOCAL') && USE_LOCAL==1) {?>
<script src="<?php echo JS_PATH; ?>jsapi.js"></script>
<script src="<?php echo JS_PATH; ?>client.js"></script>
<?php } else { */?>
<script src="https://www.google.com/jsapi?key=<?php echo API_KEY; ?>"></script>
<script src="https://apis.google.com/js/client.js"></script>
<?php //} ?>
<!-- Google drive ends-->

<script type="text/javascript" src="<?php echo JS_PATH; ?>fileupload.js"></script>

<?php //if(PAGE_NAME == "dashboard"){ ?>

<script type="text/javascript">
$(document).ready(function(){
	var pjuniq=$('#projFil').val();
	var url = "<?php echo HTTP_ROOT?>easycases/ajax_case_menu";
	loadCaseMenu(url,{"projUniq":pjuniq,"pageload":1,"page":"<?php echo PAGE_NAME; ?>","filters":"<?php echo $filters; ?>","case":"<?php echo $caseunid; ?>"}, 1);
});
</script>

<?php 
if(defined('NODEJS_HOST') && trim(NODEJS_HOST)){ ?>
<!--script src="<?php echo HTTP_ROOT; ?>js/socket.io.js"></script-->
<script src="<?php echo NODEJS_HOST;?>socket.io/socket.io.js"></script>
<?php  if(defined('CHAT') && CHAT == 1){ ?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="<?php echo HTTP_ROOT; ?>chat/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>chat/js/jquery.dialogextend.min.js"></script>
<?php } ?>
<script type="text/javascript">
var client;
var chat_client; 
var chat_client_login;
function subscribeClient(){
	var prjuniqid = $("#CS_project_id").val();
	if(client && prjuniqid!='all'){
		client.emit('subscribeTo', { channel: prjuniqid });
		return;
	}
	
	var alltasks = new Array();
	try{
		client = io.connect('<?php echo NODEJS_HOST; ?>',{secure: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?"true":"false"; ?>});
		
		client.on('connect',function (data) {
			var prjuniqid = $("#CS_project_id").val();
			//alert('Joining client to: '+prjuniqid);
			if(prjuniqid!='all'){
				client.emit('subscribeTo', { channel: prjuniqid });
			}
		});
	
	
		client.on('iotoclient', function (data) {
			var message = data.message;//alert(message);
                        if(message.indexOf("~~") >= 0){
			var session_id = message.split('~~')[1];
			var msg = message.split('~~')[0];
			var caseNum = message.split('~~')[2];
			var caseTyp = message.split('~~')[3];
			var caseTtl = message.split('~~')[4];
			var projShName =  message.split('~~')[5];
			//var show_pub = $("#pub_show").val();
			
			if(session_id != SES_ID)          
			{
				var counter =$("#pub_counter").val();
				var casenumHid = $("#hid_casenum").val();
				if(casenumHid == '0') {
					alltasks = [];
				}
				
				//var index = alltasks.indexOf(caseNum);
				var index = $.inArray(caseNum, alltasks);
				
				if(index == -1) { //if the case number is not present
					alltasks.push(caseNum);
					$("#hid_casenum").val(alltasks);
					counter ++;
				} 
				
				if(counter == 1) {
					var tsk = "Task";
				} else {
					var tsk = "Tasks";
				}
				$("#punnubdiv").show();
				$("#pub_counter").val(counter);
				$('#pubnub_notf').html(counter+' '+ tsk +' '+msg);
				$("#pubnub_notf").slideDown("1000");
				//if (window.webkitNotifications) {
					notify(getImNotifyMsg(projShName, caseNum, caseTtl, caseTyp),'Orangescrum.com');
				//}
			}
                         }
		});
	} catch(e){ console.log('Socket ERROR\n'); console.log(e); }
			
       <?php  if(defined('CHAT') && CHAT == 1){ ?>
        /// Chat logic
        if(chat_client){
            chat_client.emit('subscribeToChat', { channel: <?php echo SES_COMP;?> });
            return;
	}
        try{
		chat_client = io.connect('<?php echo NODEJS_HOST; ?>',{secure: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?"true":"false"; ?>});

			
                chat_client.on('connect',function (data) {
			chat_client.emit('subscribeToChat', { channel: '<?php echo SES_COMP;?>'});                       
                       
		});
                
		chat_client.on('iotoclientchat', function (data) { 
                    $arr=JSON.parse(data['message']);                    
                        if($arr['Oschat']['user_id'] != "<?php echo SES_ID; ?>"){ 
                            if($arr['Oschat']['receiver_id'] == "<?php echo SES_ID; ?>"){
                                showChat($arr);
                            }else if($arr['Oschat']['chat_group_id'] != '' && typeof ($arr['Oschat']['group_receiver_id']) != "undefined"){
                            split_str = $arr['Oschat']['group_receiver_id'].split(",");
                            if (split_str.indexOf("<?php echo SES_ID; ?>") !== -1) { //|| ("#og"+$arr['Oschat']['chat_group_id']).length > 0
                                console.log(split_str);
                                 showChat($arr);
                            }
                            }
                    }
		});
                
                chat_client.on('iotoclientlogout', function (data) { console.log(data);
                    showOnlines();
		});
		
	} catch(e){ console.log('Socket ERROR\n'); console.log(e); }
       <?php } ?>
}
</script>
<?php } else { ?>
<script type="text/javascript">
	function subscribeClient(){}
</script>
<?php } ?>

<?php //}?>
  <?php  if(defined('CHAT') && CHAT == 1){ ?>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>chat/js/chat_helper.js"></script>
  <?php } ?>
<?php 
if(CONTROLLER == "templates" && (PAGE_NAME == "tasks" || PAGE_NAME == "projects")){
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#desc').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo HTTP_ROOT; ?>js/tinymce/tiny_mce.js',
			theme : "advanced",
			plugins : "paste",
			theme_advanced_buttons1 : "bold,italic,strikethrough,underline,|,numlist,bullist,|,indent,outdent",
			theme_advanced_resizing : false,
			theme_advanced_statusbar_location : "",
			paste_text_sticky : true,
			gecko_spellcheck : true,
			paste_text_sticky_default : true,
			forced_root_block : false,
			width : "650px",
			height : "200px",
		});
		$('#desc_edit').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo HTTP_ROOT; ?>js/tinymce/tiny_mce.js',
			theme : "advanced",
			plugins : "paste",
			theme_advanced_buttons1 : "bold,italic,strikethrough,underline,|,numlist,bullist,|,indent,outdent",
			theme_advanced_resizing : false,
			theme_advanced_statusbar_location : "",
			paste_text_sticky : true,
			gecko_spellcheck : true,
			paste_text_sticky_default : true,
			forced_root_block : false,
			width : "650px",
			height : "200px",
		});
	});
</script>
<?php
}
if(PAGE_NAME == "dashboard" || PAGE_NAME=='milestone' || (CONTROLLER == "archives" && PAGE_NAME == "listall") || PAGE_NAME=='milestonelist' || PAGE_NAME == 'time_log' || PAGE_NAME == 'resource_utilization') {?>
<script type="text/javascript" src="<?php echo JS_PATH; ?>dashboard.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.prettyPhoto.js"></script>
<?php }
if(PAGE_NAME == "mydashboard" || PAGE_NAME=='milestone' || PAGE_NAME=='dashboard' || PAGE_NAME=='milestonelist') {?>
	<script type="text/javascript" src="<?php echo HTTP_ROOT;?>js/jquery/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="<?php echo HTTP_ROOT;?>js/jquery/jquery.jscrollpane.min.js"></script>
<?php } ?>
<script type="text/javascript">
<?php	if(PAGE_NAME != "dashboard" && PAGE_NAME !='pricing' && PAGE_NAME !='onbording') {?>
	<?php if((CONTROLLER == "milestones" && PAGE_NAME == "manage") || PAGE_NAME == "mydashboard" || CONTROLLER == "reports") {?>
			var project = $("#projFil").val();
	<?php }else{?>
			var project = 'all';
	<?php } ?>
	$.post(HTTP_ROOT+"easycases/ajax_project_size",{"projUniq":project,"pageload":0}, function(data){
		 if(data){
			$('#csTotalHours').html(data.used_text);
			if(data.last_activity){
				$('#projectaccess').html(data.last_activity);
				$('#last_project_id').val(data.lastactivity_proj_id);
				$('#last_project_uniqid').val(data.lastactivity_proj_uid);
				var url=document.URL.trim();
				if(isNaN(url.substr(url.lastIndexOf('/')+1)) && (url.substr(url.lastIndexOf('/')+1)).length != 32){
					$('#selproject').val($('#last_project_id').val());
					$('#project_id').val($('#last_project_id').val());
				}
		<?php if(CONTROLLER == "milestones" && PAGE_NAME == "add" && !$milearr['Milestone']['project_id']){	?>
					$('#selproject').val(data.lastactivity_proj_id);
					$('#project_id').val(data.lastactivity_proj_id);
		<?php }	?>
			}
		  }
		},'json');
<?php }
if(!$this->Format->isiPad()) { ?>
$(function(){
	checkuserlogin();
});
<?php } ?>
$(function(){
	
	$(".more_in_menu").parent("li").click(function(){
		if($(".more_menu_li").css("display")=="none"){
			$(".more_menu_li").css({display:"block"});
			//$(this).children("a.more_in_menu").text("Less");
			$(this).addClass("open");
			$(".cust_rec").css({display:"none"});
		}
		else{
			$(".more_menu_li").css({display:"none"});
			//$(this).children("a.more_in_menu").text("More");
			$(this).removeClass("open");
			$(".cust_rec").css({display:"block"});
		}
	});
	var window_height=$(window).height();
	var top_menubar_height=$(".navbar.custom-navbar").height();
	var left_menu_height=(window_height)-(top_menubar_height);
	$(".side-nav").css({"height":left_menu_height-30});
	
	$('[rel=tooltip]').tipsy({gravity:'s', fade:true});
	$(".scrollTop").click(function(){
		$('html, body').animate({ scrollTop: 0 }, 1200);
	});
	$('body').click(function() {
		$(".tipsy").remove();
	 });
});

function showhelp(){
	openPopup();
	$('.popup_bg').css({'width':'700px'});
	$('.loader_dv').hide();
	$('.help_popup').show();
}
</script>

<?php if(PAGE_NAME == "profile") {?>
    <script type="text/javascript" src="<?php echo JS_PATH;?>scripts/jquery.imgareaselect.pack.js"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.fileupload-ui.js"></script>

<!-- For multi autocomplete and tagging -->
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.fcbkcomplete.js"></script>

<?php /*?>Moved from Create New project ajax request page<?php */?>
<script type="text/javascript" src="<?php echo JS_PATH;?>wiki.js?v=<?php echo RELEASE; ?>"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.textarea-expander.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>highcharts.js"></script>
<?php if(Configure::read('Config.language') == 'spa') { ?>
<script>
    $(function(){
    Highcharts.setOptions({
        lang: {
            months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            contextButtonTitle : _('Download'),
            downloadJPEG: _('PNG'),
            downloadPNG: _('JPEG'),
            downloadPDF: _('PDF'),
            printChart: _('Print'),
            downloadSVG: _('SVG')
        }
    });
    });
</script>
<?php } ?>
<script type="text/javascript" src="<?php echo JS_PATH; ?>exporting.js"></script>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>js/timelog.js"></script>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>js/jquery.timepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ROOT; ?>css/jquery.timepicker.css"/>
<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>js/colorpicker/colorpicker.js"></script>
<link type="text/css" href="<?php echo HTTP_ROOT; ?>css/colorpicker/colorpicker.css"/>
<style>
    #holder_detl { border: 4px dashed #F8F81E;padding: 8px;height:85px;background: #F0F0F0;}
    #holder_detl.hover { border: 4px dashed #0c0; }
</style>
