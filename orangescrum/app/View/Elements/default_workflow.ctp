<style>
    .col-xs-11 span{margin:10px;}
</style>
<section class="content">
    <div class="dealstages-editor col-xs-8">
        <div class="wfl_name"><?php echo __("Default Task Status Group"); ?></div>
        <ul class="stages">
            <% for(var key in default_stages){
                var getdata = default_stages[key]; %>
                <li class="stage row well no-select">
                    <h1 class="order sort_number handle col-xs-1"><%= getdata.Status.seq_order %></h1>
                    <div class="col-xs-11">
                        <span class="option-label form-control name"><%= _(getdata.Status.name) %></span>
                        <span class="option-label color form-control" style="background-color:<%= getdata.Status.color %>"></span>
                        <div class="percentage prcnt_bx">
                            <span class="percent_txt"><%= getdata.Status.percentage %> %</span>
                        </div>
                        <input id="percentagehid_<%= getdata.Status.id %>" type="hidden" value="<%= getdata.Status.percentage %>" class="option-label form-control percent" name="data[Status][color]"/>
                    </div>
                </li>
            <% } %>
        </ul>
    </div>
</section>
<div class="cb"></div>