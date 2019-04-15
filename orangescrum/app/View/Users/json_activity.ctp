<% var istype = new Array();
    istype[1] = '<font color="#763532"><b><?php echo __("New"); ?></b></font>';
    istype[2] = '<font color="#0E93CA"><b><?php echo __("Replied"); ?></b></font>';
    istype[3] = '<font color="#77AB13"><b><?php echo __("Closed"); ?></b></font>';
    istype[4] = '<font color="#0E93CA"><b><?php echo __("Replied"); ?></b></font>';
    istype[5] = '<font color="#EF6807"><b><?php echo __("Resolved"); ?></b></font>';
    istype[6] = '<font color="#000"><b><?php echo __("Update"); ?></b></font>';
    istype[7] = '<font color="#000"><b><?php echo __("Comment"); ?></b></font>';
    istype[9] = '<font color="#EF6807"><b><?php echo __("Edited"); ?></b></font>';
    if(activity.length) { 
	var lastDate = "";
	var dateRepeat = "";
	var easycaseArr= new Array();
	for(var key in activity) {
	    var obj = activity[key];
	    for(var key1 in obj) {
	
	    var updated = obj[key1].Easycase.updated;
	    var lastDate = obj[key1].Easycase.lastDate;
	    
	    var easycase_caseno_projId =  obj[key1].Easycase.case_no+"_"+ obj[key1].Easycase.project_id;

	    if (dateRepeat != lastDate) { 
		easycaseArr= new Array();
	    }
	   if($.inArray(easycase_caseno_projId,easycaseArr) == -1 ){  
		var legend = obj[key1].Easycase.legend;
		var legendnm = obj[key1].Easycase.legendnm;
		var legendcol = obj[key1].Easycase.legendcol;
                if(legend == 4 ){
                    legend = 2;
		}
                var rel = legend+'-'+legendnm+'-'+legendcol;
                
		easycaseArr.push(easycase_caseno_projId);
		}
            else{
               rel= ""; }%> 
	<table width="100%" cellspacing="0" cellpadding="0">
	    <tbody>
	    <% if (dateRepeat != lastDate) { 
	    caseid_tot = obj[key1].Easycase.id; %>
	    <tr>
		<td class="act_bar">
		    <div class="fl date_flag"><span><b><%= lastDate %></b></span></div>
		</td>
		<td>
                    <div class="act_sum12">
		    <div class="fr act_sum" id="allStatus<%= caseid_tot %>"></div>
                    </div>
		</td>
	    </tr>
	    <% } else { %>
	    <tr>
		<td class="act_bar">
		    <div class="flag_stik"></div>
		</td>
		<td></td>
	    </tr>
	    <% }  %>
	    <% if (obj[key1].Easycase.msg) { %>
	    <tr>
		<td class="act_bar" valign="top">
		    <div style="text-align:center;">
			<% if(obj[key1].User.photo){ %>
			<img class="round_profile_img ppl_invol m0 lazy" data-original="<%= HTTP_ROOT %>users/image_thumb/?type=photos&file=<%= obj[key1].User.photo %>&sizex=30&sizey=30&quality=100" width="30" height="30" title="<%= obj[key1].User.name %> <%= obj[key1].User.short_name %>" rel="tooltip" alt="Loading"/>
			<% }else{ %>
                        <span class="round_profile_img <%= obj[key1].User.color %> act_prof_styl"><%= obj[key1].User.sht_name %></span>
		<?php /*	<img class="round_profile_img ppl_invol m0 lazy" data-original="<%= HTTP_ROOT %>users/image_thumb/?type=photos&file=user.png&sizex=28&sizey=28&quality=100" width="30" height="30" /> */ ?>
			<% } %>
		    </div>
		    <div class="icon_date"><%= updated %></div>
		</td>
		<td class="totalstatus allStatus<%= caseid_tot %>" rel="<%= rel %>">
		    <div class="sta_dec">
			<span class="spn-unm"><%= obj[key1].User.name %></span>
			<% if(legend == '1' || legend == '0'){ %>
<!--			    <div class="label new">New</div>-->
			<% } %>
			<% if(legend == '2' || legend == '4'){ %>
<!--			    <div class="label wip">In Progress</div>-->
			<% } %>
			<% if(legend == '5'){ %>
<!--			    <div class="label resolved">Resolved</div>-->
			<% } %>
			<% if(legend == '3'){ %>
<!--			    <div class="label closed">Closed</div>-->
			<% } %> <%= obj[key1].Easycase.msg %>
		    </div>
		    <div style="display: none;" class="prj_dvs">
			<% if(obj[key1].Project.name){ %>
			<a class="fnt999 ttc" href="<%= HTTP_ROOT %>dashboard/?project=<%= obj[key1].Project.uniq_id %>"><%= obj[key1].Project.name %></a>
			<% } %>	
		    </div>
		    <div class="cb"></div>
		</td>
	    </tr>
	    <%  } %>
	</tbody>
</table>
<% dateRepeat = lastDate; %>

<% } %>
<div class="cb h30"></div>
<% } %>
<% } else {
	if(total && total == 0){ %>
	<?php echo $this->element('no_data', array('nodata_name' => 'activity')); ?>
<%  }
} %>

<input type="hidden" id="totalact" value="<%= total %>">
