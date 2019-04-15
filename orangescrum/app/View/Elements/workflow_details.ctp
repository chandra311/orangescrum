<section class="content">
    <div class="dealstages-editor col-xs-8">
        <% for(var key in res){
        var data = res[key];
        var wfl_id = data.Workflow.id;
        var wfl_name = data.Workflow.name}%>
        <input type="hidden" id="workflow_hid" value="<%= wfl_id %>">
        <div class="wfl_name" rel="tooltip" title="Edit Task Status Group Name">
        <input id="wfl_txt" class="option-label form-control wflname fl" type="text" placeholder="Enter Task Status Group name" onchange="changeWorkflowName(<%= wfl_id %>, this)"/>
        <input id="wfl_prev_txt"  type="hidden" value="<%= wfl_name %>"/>
        </div>
        <ul class="stages">
            <% var lst_k = 0; for(var key in data.Status){
                var getdata = data.Status[key]; %>
                <li id="<%= getdata.id %>" class="stage row well no-select ui-state-disabled1">
                    <h1 class="order sort_number handle col-xs-1"><%= getdata.seq_order %></h1>
                    <h1 class="order delete handle col-xs-1 fr"><img src="<?php echo HTTP_ROOT; ?>img/images/delete.png" title="Delete status" style="cursor:pointer" onclick="delete_status(<%= getdata.id %>,0,this)"></h1>
                    <div class="col-xs-11">
                        <input id="name_<%= getdata.id %>" class="option-label form-control name" type="text" placeholder="Enter status name" value="<%= getdata.name %>" onchange="changeStatusName(<%= getdata.id %>, this)" />
                        <input id="prev_name_<%= getdata.id %>" class="option-label form-control" type="hidden" value="<%= getdata.name %>"  />
                        
                        <input id="color_<%= getdata.id %>" type="text" value="<%= getdata.color %>" class="fl option-label color form-control" rel="tooltip" title="Click here to change color" name="data[Status][color]" placeholder="#ffffff" maxlength="10" onchange="changeStatusColor(<%= getdata.id %>)" style="background:<%= getdata.color %>;color:<%= getdata.color %>"/>
                        <a href="javascript:void(0);" class="col-hlp-icn fl">
                            <div class="col-hlp-img">
                                <img src="<?php echo HTTP_ROOT; ?>img/click-to-save.jpg" alt="click button to save color" />
                            </div>
                        </a><div class="cb"></div>
                        <div class="relative">
                            <div id="percentage_<%= getdata.id %>" class="percentage"></div>
                            <span id="txt_<%= getdata.id %>" class="percent_txt"><%= getdata.percentage %> %</span>
                        </div>
                        <input id="percentagehid_<%= getdata.id %>" type="hidden" value="<%= getdata.percentage %>" class="option-label form-control percent" name="data[Status][color]"/>
                    </div>
                </li>
				<% 
				if(parseInt(getdata.id) > parseInt(lst_k)){
					lst_k = getdata.id;
				} %>
            <% } %>
        </ul>
		<input type="hidden" value="<%= lst_k %>" id="most_latestk" />
        <div class="create_status m20" onclick="createStatus();">
            <div class="create_text">Add Status</div>
        </div>
    </div>
    <?php /* <div class="col-xs-3 delete-wfl">
        <div class="del-txt">Drag status here to delete.</div>
    </div> */ ?>
</section>
<div class="cb"></div>