<section class="content">
    <div class="dealstages-editor col-xs-8">
        <div class="wfl_name" rel="tooltip" title="<?php echo __('Edit Task Status Group  Name'); ?>">
        <input id="new_wfl_txt" class="option-label form-control wflnew fl" type="text" placeholder="<?php echo __('Enter Task Status Group name'); ?>"  onchange="addWorkflow()"/>
        </div>
        <ul class="stages">
            <% var count = 0;
            for(var key in default_stages){
                var getdata = default_stages[key]; 
                count ++; %>
                <li id="<%= count %>" class="stage row well no-select ui-state-disabled1">
                    <h1 class="order sort_number handle col-xs-1"><%= count %></h1>
                    <div class="col-xs-11" style="float:none;">
                        <input id="name_<%= count %>" class="option-label form-control name" type="text" placeholder="<?php echo __("Enter status name"); ?>" value="<%= _(getdata.Status.name) %>" onchange="changeStatusName(<%= count %>, this)" />
                        <input id="color_<%= count %>" type="text" value="<%= getdata.Status.color %>" class="option-label color form-control" name="data[Status][color]" placeholder="#ffffff" maxlength="10" onchange="changeStatusColor(<%= count %>)" style="background:<%= getdata.Status.color %>;color:<%= getdata.Status.color %>"/>
                        <a href="javascript:void(0);" class="col-hlp-icn fl new_wrklw">
                            <div class="col-hlp-img">
                                <img src="<?php echo HTTP_ROOT; ?>img/click-to-save.jpg" alt="<?php echo __("click button to save color"); ?>" />
                            </div>
                        </a><div class="cb"></div>
                        <div class="relative">
                            <div id="percentage_<%= count %>" class="percentage"></div>
                            <span id="txt_<%= count %>" class="percent_txt"><%= _(getdata.Status.percentage) %> %</span>
                        </div>
                        <input id="percentagehid_<%= count %>" type="hidden" value="<%= _(getdata.Status.percentage) %>" class="option-label form-control percent" name="data[Status][color]"/>
                    </div>
                </li>
            <% } %>
        </ul>
        <div class="create_status m20" onclick="createStatus();">
             <div class="create_text"><?php echo __("Add Status"); ?></div>
            </div>
        </div>
</section>
<div class="cb"></div>