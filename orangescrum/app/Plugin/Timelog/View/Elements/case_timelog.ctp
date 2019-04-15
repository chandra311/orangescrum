<% 
    var SHOWTIMELOG = getCookie('SHOWTIMELOG'); 
    var pagename = typeof logtimes.page !='undefined' ? logtimes.page : ''; 
    if(pagename == 'taskdetails' && SHOWTIMELOG == ''){
        SHOWTIMELOG = 'No';
    }
    SHOWTIMELOG = typeof logtimes.page !='undefined' && logtimes.page == 'taskdetails' ? SHOWTIMELOG : 'Yes'; 
%>

<style type="text/css">
    .logmore-btn a.taskdetails{background-color:#AAE1CB;padding-top:6px;}
    .logmore-btn a.taskdetails:hover{background-color: #3fba8b;}
    .timelog-detail-tbl table tr.timelog-hover-block{background: #FFFFFF;}
    .timelog-detail-tbl table tr.timelog-hover-block:hover{background: #FFFCDB;}
    
    .timelog-detail-tbl td{padding:8px 5px;}
    .timelog-detail-tbl td.center{text-align:center;}
    .spent-time .total{margin-right:5px;display:none;}
    .timelog-table-head .spent-time{float: left; margin-left: 10px;margin-top: 6px;}
    .timelog-table-head .time-log-head{float:left; display:inline-block;font-size:20px;}
    .sprite.down-btn{background-position: 2px -99px;}
    .hidetablelog .ht_log span,
    .timelog-table .ht_log span
    {color:#3DBB89}
    .hidetablelog .ht_log a:hover span,
    .timelog-table .ht_log a:hover span
    {color:#FF8B1A}
    .timelog-detail-tbl th{background-color:#eee; border:1px solid #bbb; padding:6px 5px 6px 5px; border-top: 0px none; border-bottom: 0px none;}
    .timelog .sprite.yes{float: none; display: inline-block; height: 15px; width: 15px; background-position: -4px -44px; top: auto; left: auto;}
    .timelog .sprite.no{float: none; display: inline-block; height: 15px; width: 15px; background-position: -4px -24px; top: auto; left: auto;}
    <% if(pagename == 'taskdetails') {%>
                .tl-msg-header{width:75%;}
            .timelog-table-head .time-log-head{font-size:15px;font-weight:normal;float:left; display:inline-block;}
            .timelog-detail-tbl th{font-weight:normal;font-size:14px;}
                .sprite.btn-clock{background-position: 0px -61px;left: 3px; top: 3px; position:absolute;}
                .logmore-btn a{height: 26px;line-height: 12px;position:relative; width: 125px; padding-left: 25px;}
            .timelog-table-head .spent-time{margin-top: 1px;}
			.logmore-btn{margin-bottom:5px;}
    <% }else{ %>
              .logmore-btn a{height:30px; line-height:20px; position:relative;width: 150px; padding-left: 25px;}
              .sprite.btn-clock{background-position: 0px -61px;left: 7px; top: 5px; position:absolute;}
    <% } %>
    .timelog-overlap{opacity: 0.2;height: 100%;width: 100%;z-index: 1;display: block;background: #ccc;position: absolute;top: 0px;left: 0px;}
    .relative {position:relative;}
    .timelog-opts{position: absolute; right: -50px; top: 0; border: 1px solid #ccc; padding: 12% 5px 7px; display:none;z-index:1;height:100%;background:#FFFCDB;}
    .timelog-hover-block:hover .timelog-opts{display:block;}
</style>
<div class="timelog-table">
    <div class="timelog-table-head">
        <div>
            <div class="fl tl-msg-header">
                <span class="time-log-head">Time Log<span class="tl-colon">:</span></span>
                <div class="spent-time tl-msg-box">
                    <div class="fl">
                        <span class="total">Total:</span>
                        <span class="use-time">Logged:</span>
                        <span><%= format_time_hr_min(logtimes.details.totalHrs) %></span>
                    </div>
                    <div class="fl" style="margin:0px 20px 0px 20px;">
                        <span class="use-time">Billable:</span>
                        <span><%= format_time_hr_min(logtimes.details.billableHrs) %></span>
                    </div>
                    <div class="fl">
                        <span class="use-time">Estimated:</span>
                        <span><%= format_time_hr_min(logtimes.details.estimatedHrs) %></span>
                    </div>
                    <div class="cb"></div>
                </div>
            </div>
            <div class="fr tl-msg-btn">
                <% if(typeof logtimes.page != "undefined" && logtimes.page == 'timelog'){ %>
                    <a href="javascript:void(0);" class="fl btn btn_blue aply_btn" id="btn-reset-timelog" style="margin: 0px 10px;">Reset</a>
                <% } %>
                <div class="logmore-btn fr" style="<% if(SHOWTIMELOG=='No'){ %>display:none;<% } %>">
                    <% if (typeof csLgndRep != 'undefined' && (csLgndRep!=3 && csLgndRep!=5)) { %>
                        <a class="<%=logtimes.page%> anchor log-more-time" onclick="startTimer(<%= '\'' + logtimes.task_id + '\'' %>,<%= '\'' + escape(logtimes.task_title) + '\'' %>,<%= '\'' + logtimes.task_uniqId + '\'' %>,<%= '\'' + logtimes.project_uniqId + '\'' %>,<%= '\'' + logtimes.project_name + '\'' %>)">Start Timer</a>
                    <% } %>
                    <a class="<%=logtimes.page%> anchor" onclick="createlog(<%= logtimes.task_id %>,'<%= escape(logtimes.task_title)%>',0,<%= '\'' + logtimes.project_uniqId + '\'' %>)">Log more time<span class="sprite btn-clock"></span></a>
                </div>
                <div class="showreplylog ht_log" style="<% if(SHOWTIMELOG!='No'){ %>display:none;<% } %>margin-left:35px;margin-top:0px;">
                    <a href="javascript:void(0);" style="font-size:13px;text-decoration:none;" onclick="showreplytimelog();">
                        <span class="fl">Expand Time Log</span><span class="fl sprite down-btn"></span>
                    </a>
                </div>
            </div>
            <div class="cb"></div>
        </div>
    </div>
</div>
<div class="hidetablelog" style="<% if(SHOWTIMELOG=='No'){ %>display:none;<% } %>">
    <div class="timelog-detail-tbl">
        <table cellpadding="3" cellspacing="4">
            <tr>
                <th style="width:11%;">Date</th>
                <th style="width:15%;">Name</th>
                <% if(typeof logtimes.showTitle != "undefined" && logtimes.showTitle == 'Yes'){ %>
                <th style="width:20%;">Task</th>
                <% } %>
                <th style="width:24%;">Description</th>
                <th>Start</th>
                <th>End</th>
                <th style="width:8%;">Break</th>
                <th style="width:5%;">Billable</th>
                
                <th style="width:10%;">Hours</th>
                <%  if(typeof logtimes.page != "undefined" && logtimes.page == 'timelog'){ %>
                <th style="text-align: center;padding: 0px;width: 5%;">Action</th>
                <% } %>
            </tr>
            <% if(logtimes.logs.length > 0){%>
                <% for(var logKey in logtimes.logs){
                    var getdata = logtimes.logs[logKey];
                %>
                    <tr class="timelog-hover-block">
                        <?php /*<td><%= formatDate('M dd, yy',getdata.LogTime.start_datetime) %></td>*/?>
                        <td><%= formatDate('M dd, yy',getdata[0].start_datetime_v1) %></td>
                        <td><%= getdata[0].user_name %></td>
                        <% if(typeof logtimes.showTitle != "undefined" && logtimes.showTitle == 'Yes'){ %>
                            <td>
                                <% if(typeof getdata[0].task_name == 'string' && getdata[0].task_name !=''){ %>
                                <% var task_dtl = getdata[0].task_name.split('||'); %>
                                <a id="titlehtml_<%= task_dtl[1] %>" data-task='<%= task_dtl[1] %>' class="anchor">
                                <%= shortLength(task_dtl[0],20) %>
                                </a>
                            <% } else { %>
                                <a class="anchor">---</a>
                            <% } %>
                            </td>
                        <% } %>
                        <td><%= shortLength(formatText(nl2br(strip_tags(getdata.LogTime.description))),20) %></td>
                        
                        <td><%= format_24hr_to_12hr(getdata.LogTime.start_time) %></td>
                        <td><%= format_24hr_to_12hr(getdata.LogTime.end_time) %></td>
                        <td>
                            <span class="fl"><%= format_time_hr_min(getdata.LogTime.break_time) %></span>
                        </td>
                        <td class="timelog center"><span <% if(getdata.LogTime.is_billable == '1'){ %> class="sprite yes" <% } else { %> class="sprite no" <% } %> ></span></td>
                        
                        <%  if(getdata.LogTime.user_id == SES_ID || SES_TYPE == 1 || SES_TYPE == 2){ %>
						<td class="relative" data-logid="<%= getdata.LogTime.log_id %>" data-prjctUniqid="<%= logtimes.project_uniqId %>" data-prjctId="<%= getdata.LogTime.project_id %>">
						<% } else { %>
							<td class="relative">
						<% } %>
						<span class="fl"><%= format_time_hr_min(getdata.LogTime.total_hours) %></span>
                            <% if(typeof logtimes.page != "undefined" && logtimes.page == 'timelog'){ %>
                            <% } else { %>
						<div class="timelog-opts edtdltlog" data-logid="<%= getdata.LogTime.log_id %>">
								<%  if(getdata.LogTime.user_id == SES_ID || SES_TYPE == 1 || SES_TYPE == 2){ %>
								<% } else { %>
									<div class="timelog-overlap" style="" rel="tooltip" title="You are not authorised to modify."></div>
								<% } %>
                                <a class="anchor edit_time_log" onclick="editTimelog(this)"><span class="fl sprite note"></span></a>
                                <a class="anchor delete_time_log" onclick="deletetimelog(this);"> <span class="fl sprite delete"></span></a>
                            </div>
                            <% } %>
                        </td>
                        <% if(typeof logtimes.page != "undefined" && logtimes.page == 'timelog'){ %>
                                <%  if(getdata.LogTime.user_id == SES_ID || SES_TYPE == 1 || SES_TYPE == 2){ %>
                                    <td class="edtdltlog" data-logid="<%= getdata.LogTime.log_id %>">
                                <% } else { %>
                                    <td style="position:relative;">
                                        <div class="timelog-overlap" style="" rel="tooltip" title="You are not authorised to modify."></div>
                                <% } %>
                                <a class="anchor edit_time_log" onclick="editTimelog(this)"><span class="fl sprite note"></span></a>
                                <a class="anchor delete_time_log" onclick="deletetimelog(this);"><span class="fl sprite delete"></span></a>
                            </td>
                        <% } %>
                    </tr>
                <% } %>
            <% }else{ %>
            <tr>
                    <td colspan="10">No records......</td>
            </tr>
                <% $("#TimeLog_paginate").hide(); %>
            <% } %>
        </table>
    </div>
    <% if(typeof logtimes.page != "undefined" && logtimes.page == 'timelog'){ %>
    <% } else{ %>
        <div class="timelog-table-head" style="margin: 0px;border: 1px solid #ccc;border-top-width: 0px;">
            <div class="fl tl-msg-header" style="padding:8px 0 0 5px;">
                <span class="time-log-head">Time Log<span class="tl-colon">:</span></span>
                <div class="spent-time tl-msg-box">
                    <div class="fl">
                        <span class="total">Total:</span>
                        <span class="use-time">Logged:</span>
                        <span><%= format_time_hr_min(logtimes.details.totalHrs) %></span>
                    </div>
                    <div class="fl" style="margin:0px 20px 0px 20px;">
                        <span class="use-time">Billable:</span>
                        <span><%= format_time_hr_min(logtimes.details.billableHrs) %></span>
                    </div>
                    <div class="fl">
                        <span class="use-time">Estimated:</span>
                        <span><%= format_time_hr_min(logtimes.details.estimatedHrs) %></span>
                    </div>
                    <div class="cb"></div>
                </div>
            </div>
            <div class="cb"></div>
        </div>
    
    
        <div style="border-bottom:1px solid #ccc;">
            <div class="fr ht_log">
                <a href="javascript:void(0);" onclick="hidereplytimelog();"><span class="fl">Hide Time Log</span><span class="fl sprite up-btn"></span></a>
            </div>
            <div class="cb"></div>
        </div>
    <% } %>
    <% if(typeof logtimes.caseCount != 'undefined'){ %>
        <% if(logtimes.caseCount && logtimes.caseCount!=0) {
                var pageVars = {pgShLbl:logtimes.pgShLbl,csPage:logtimes.csPage,page_limit:logtimes.page_limit,caseCount:logtimes.caseCount};
        %>
            <div style="border-bottom:1px solid #ccc;">
                <% $("#TimeLog_paginate").html(tmpl("paginate_tmpl", pageVars)).show(); %>
                <div class="cb"></div>
            </div>
        <% } %>
    <% } %>
</div>
