<!--[if lte IE 9]>
    <style>
        .widget.text-only.blinkwidget{background-color:#f2f2f2!important;}
    </style>	
<![endif]-->
<style type="text/css">
	.widget{
		background: url("<?php echo HTTP_ROOT;?>img/html5/icons/right_div.png") no-repeat 0 5px; #D6D4D4;
		margin: 0px;
		border: none;
	}
</style>
<%
var case_widgets = getCookie('CLOSE_WIDGET');
var case_news = getCookie('NEW_WIDGET');
var case_opens = getCookie('OPEN_WIDGET');
var case_starts = getCookie('START_WIDGET');
var case_resolves = getCookie('RESOLVE_WIDGET');
var chart_widgets = getCookie('CHART_WIDGET');


if(case_widgets) {
	var case_wid = "display:none;";
	var case_wid1 = "display:block;";
} else {
	var case_wid = "display:block;";
	var case_wid1 = "display:none;";
}
if(case_news) {
	var case_new = "display:none;";
	var case_new1 = "display:block;";
} else {
	var case_new = "display:block;";
	var case_new1 = "display:none;";
}
if(case_opens) {
	var case_open = "display:none;";
	var case_open1 = "display:block;";
} else {
	var case_open = "display:block;";
	var case_open1 = "display:none;";
}
if(case_starts) {
	var case_start = "display:none;";
	var case_start1 = "display:block;";
} else {
	var case_start = "display:block;";
	var case_start1 = "display:none;";
}

if(case_resolves) {
	var case_resolve = "display:none;";
	var case_resolve1 = "display:block;";
} else {
	var case_resolve = "display:block;";
	var case_resolve1 = "display:none;";
}
if(chart_widgets) {
	var chart_widget = "display:none;";
	var chart_widget1 = "display:block;";
} else {
	var chart_widget = "display:block;";
	var chart_widget1 = "display:none;";
}

if(case_widgets=="1" || case_news == "1" || case_resolves =="1" || case_starts =="1" || case_opens =="1" || chart_widgets =="1"){
	var widget="display:block;";
} else {
	var widget="display:none;";
}

var disabled = "";
if(getCookie('CURRENT_FILTER') == 'closecase') {
	disabled = 1;
}
%>
<% for(var key in res){
    var d = res[key]; 
    if(typeof d.id != 'undefined'){
        if(d.id > 5){
        var backgrnd = "background:"+d.color;        
        }%>

    <div class="fl status ellipsis-view" id="widget_<%= d.name %>" style="background:<%= d.color %> ;max-width:150px;border-right:1px solid #fff" title="<%= _(d.name) %>" >
	<a href="javascript:void(0);"  <% if(!disabled) { %> onclick="statusTop(<%= d.id %>);" <% } %>>
		<span class="num"><%= d.count %></span>&nbsp;<%= _(d.name) %>
        </a>
    </div>
<% } } %> 

	<?php
if(strtotime("+2 months",strtotime(CMP_CREATED))>=time()){?>
<!--<div  title="Click for help"  onclick="return showhelp();" class=" fl status need-help no-select"  style="">Need help getting started?</div>-->
<?php } ?>
    <div class="cb"></div>




<div class="fl" align="left" style="margin:0px 5px">
    <div class="popup_link_case_proj_parent" align="left" style="<%= widget %>" id="closewidget">
<!-- 		<div class="popup_link_case_proj" id="closedwidgetchild" style="<%= widget %>">
			<a href="javascript:jsVoid();" onclick="open_pop(this)" style="font-weight:normal;">
				<span style="font-size:12px;">Show Widget</span>
			</a>
		</div>-->
		<div class="popup_option" id="popup_option" style="display:none;position:absolute;z-index:0;">
			<div class="pop_arrow_new" style="position:absolute;left:12px"></div>
            <div class="popup_con_menu" align="left" style="left:-1px; min-width:50px;padding: 2px 8px;">
                <div align="left">
    
                    <div  id="widget_close1" style="<%= case_wid1 %>">
                        <a href="javascript:void(0);" onclick="hideCloseWidget(<%= '\'widget_close\'' %>);"><?php echo __("Closed"); ?></a>
                    </div>
    
    
                    <div  id="widget_new1" style="<%= case_new1 %>">
                        <a href="javascript:void(0);"  onclick="hideCloseWidget(<%= '\'widget_new\'' %>);" style="min-weight:10px;"><?php echo __("New"); ?></a>
                    </div>   
    
                    <div id="widget_open1" style="<%= case_open1 %>">
                        <a href="javascript:void(0);" onclick="hideCloseWidget(<%= '\'widget_open\'' %>);"><?php echo __("In Progress"); ?></a>
                    </div>
    
                    <div id="widget_start1" style="<%= case_start1 %>">
                        <a href="javascript:void(0);"  onclick="hideCloseWidget(<%= '\'widget_start\'' %>);" style="min-weight:10px;"><?php echo __("Start"); ?></a>
                    </div>
    
                    <div  id="widget_resolve1" style="<%= case_resolve1 %>">
                        <a href="javascript:void(0);" onclick="hideCloseWidget(<%= '\'widget_resolve\'' %>);" style="min-weight:10px;"><?php echo __("Resolved"); ?></a>
                    </div>
					<!--div  id="widget_chart1" style="<%= chart_widget1 %>">
                        <a href="javascript:void(0);" onclick="hideCloseWidget(<%= '\'widget_chart\'' %>);" style="min-weight:10px;">Bug Reports</a>
                    </div-->
                </div>
            </div>
		</div>
	</div>
</div>
<?php /* <input type="hidden" id="closedcaseid" value="<%= cls %>">  */ ?>
<div class="cb"></div>
