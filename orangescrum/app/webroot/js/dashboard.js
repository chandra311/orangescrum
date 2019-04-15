$(document).on('click', 'body', function (e) {
    $('.task_dependent_block').find('.showDependents').removeClass('open');
    $('.task_parent_block').find('.showParents').removeClass('open');
    $(e.target).closest('.task_dependent_block').length > 0 ? $(e.target).closest('.task_dependent_block').find('.showDependents').addClass('open') : '';
    $(e.target).closest('.task_parent_block').length > 0 ? $(e.target).closest('.task_parent_block').find('.showParents').addClass('open') : '';
});
$(document).on('click', '.task_action_bar .next', function () {
    easycase.rollNext(this);
});
$(document).on('click', '.task_action_bar .prev', function () {
    easycase.rollPrev(this);
});
$(document).on('click', '[id^="showhtml"]', function () {
    var task_data = $(this).attr('data-task').split('|');
    var csAtId = task_data[0];

    $('#showhtml' + csAtId).hide();
    $('#hidhtml' + csAtId).show();
    //$('#mem'+csAtId).fadeIn(400);
    //$('#viewmemdtls'+csAtId).fadeIn(400);
    //$('#viewatachment'+csAtId).fadeIn(400);
});
$(document).on('click', '[id^="mor_toggle"]', function () {
    var csAtId = $(this).attr('data-csatid');
    $('#mor_toggle' + csAtId).hide()
    $('#less_toggle' + csAtId).show();
    $('#tskmore_' + csAtId).slideDown(350);

    //createCookie("crtdtsk_less", '1', -365, DOMAIN_COOKIE);
});
$(document).on('click', '[id^="less_toggle"]', function () {
    var csAtId = $(this).attr('data-csatid');
    $('#mor_toggle' + csAtId).show()
    $('#less_toggle' + csAtId).hide();
    $('#tskmore_' + csAtId).slideUp(350);

    //createCookie("crtdtsk_less", '1', 365, DOMAIN_COOKIE);
});
$(document).on('click', '.task_detail .ftsk_more', function () {
    //$(this).parents('.task_due_dt').find('.tsk_files_more,.ftsk_more').toggle("slow");
    if ($(this).parents('.task_due_dt').find('.tsk_files_more').css('display') == 'none') {
        $(this).parents('.task_due_dt').find('.tsk_files_more').css('display', 'block');
        $(this).children('.more_in_menu').html(_('Less'));
        $(this).addClass("open");
    } else {
        $(this).parents('.task_due_dt').find('.tsk_files_more').css('display', 'none');
        $(this).children('.more_in_menu').html(_('More'));
        $(this).removeClass('open');
    }
});
$(document).on('click', '[id^="tsk_attac_icon"]', function () {
    var csAtId = $(this).attr('data-csatid');
    $("#tsk_attach" + csAtId).trigger('click');
});
$(document).on('click', '.link_repto_task', function () {
    var csAtId = $(this).attr('data-csatid');
    scrollPageTop($('#reply_box' + csAtId));
});
function ajaxCaseView(page) {
    //disableButtons(); //not impl
    $('#select_view div').tipsy({gravity: 'n', fade: true});
    var strURL = HTTP_ROOT;
    var isUrl = 0;
    isUrl = getURLParameter('project');
    if (isUrl != "0" && isUrl) {
        //window.location=strURL+'dashboard/#';
        parent.location.hash = "cases";
    }
    strURL = strURL + "easycases/";

    var projFil = $('#projFil').val(); // Project Uniq ID
    var caseId = $('#caseId').val(); // Close a case
    var startCaseId = $('#caseStart').val(); // Start a case
    var caseResolve = $('#caseResolve').val(); // Resolve a case
    var caseChangeType = $('#caseChangeType').val();//Change case type
    var caseChangePriority = $('#caseChangePriority').val();//Change case type
    var caseChangeDuedate = $('#caseChangeDuedate').val();//Change case type
    var caseChangeAssignto = $('#caseChangeAssignto').val();//Change case type
    var customfilter = $('#customFIlterId').val();//Change case type
    if (caseId || startCaseId || caseResolve) {
        //resetAllFilters('filters');
    }
    if ($('#lviewtype').val() == 'compact') {
        $('#select_view div').removeClass('disable');
        $('#cview_btn').addClass('disable');
    } else {
        $('#select_view div').removeClass('disable');
        $('#lview_btn').addClass('disable');
    }


    document.getElementById('caseViewSpanUnclick').style.display = 'block';

    $('#caseLoader').show();
    //document.getElementById('ajax_search').style.display='none'; //not impl

    var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
    var priFil = $('#priFil').val(); // Filter by Priority
    var caseTypes = $('#caseTypes').val(); // Filter by case Types
    var caseMember = $('#caseMember').val();  // Filter by Member
    var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
    var caseDate = $('#caseDate').val(); // Sort by Date
    var caseSearch = $("#case_search").val();
    if (caseSearch.trim() == '') {
        caseSearch = $('#caseSearch').val(); // Search by keyword  
    } else {
        $("#caseSearch").val(caseSearch);
    }
    $("#case_search").val("");

    //var casePage = document.getElementById('casePage').value; // Pagination
    var caseTitle = $('#caseTitle').val(); // Sort by Date
    var caseDueDate = $('#caseDueDate').val(); // Sort by Due Date
    var caseNum = $('#caseNum').val(); // Sort by Case#
    var caseLegendsort = $('#caseLegendsort').val(); // Sort by Status
    var caseAtsort = $('#caseAtsort').val(); // Sort by Assign to
    var caseMenuFilters = $('#caseMenuFilters').val(); // Assign To
    var milestoneIds = $('#milestoneIds').val();
    var case_srch = $('#case_srch').val(); // Search by keyword
    var case_date = $('#caseDateFil').val(); // Search by Date
    var case_due_date = $('#casedueDateFil').val(); // Search by Date
    var caseCreateDate = $('#caseCreatedDate').val(); // Sort by created  Date
    var projIsChange = $('#projIsChange').val();
    if (caseMenuFilters != 'milestone' && caseMenuFilters != 'files') {
        if (caseMenuFilters === "") {
            caseMenuFilters = "cases"; // Default tab to active
        }
        $('.cattab').removeClass('active_tab');
        $('#' + caseMenuFilters + '_id').addClass('active_tab');
        //$('#caseViewSpanUnclick').css({'margin-top':'33px'});
    } else {
        //$('#caseViewSpanUnclick').css({'margin-top':'0px'}); //not impl
    }

    if (caseMenuFilters == 'milestone') {
        //document.getElementById('menumilestone').style.display='block'; //not impl
        //document.getElementById('active').style.display='block'; //not impl
        //document.getElementById('topaction').style.display='none'; //not impl
    } else {
        //document.getElementById('menumilestone').style.display='none'; //not impl
        //document.getElementById('active').style.display='none'; //not impl
        //document.getElementById('topaction').style.display='block'; //not impl
    }

    var caseUrl = "";
    var detailscount = 0;
    var reply = 0;
    /*try {
     var caseUrl = document.getElementById('caseUrl').value;
     var chk = page.indexOf('|');
     if(chk != -1) {
     var x = page.split("|");
     var detailscount = x['1'];
     var reply = 1;
     }
     }
     catch(e){
     }*/

    if (projIsChange != projFil) {
        //milestoneIds="";var milestns="";
        //remember_filters('MILESTONES',milestns);
    }
    if (caseMenuFilters == 'milestone') {
        var mstype = "";
        var mlstype = $('#checktype').val();
        if (mlstype == 'completed') {
            var mstype = 0;
            //$("#complete").parent("li").addClass("class_active"); //not impl
            //$("#active1").parent("li").removeClass("class_active"); //not impl
        } else {
            var mstype = 1;
            //$("#active1").parent("li").addClass("class_active"); //not impl
            //$("#complete").parent("li").removeClass("class_active"); //not impl
        }
    }
    var menu_filter = caseMenuFilters;
    $.post(strURL + "case_project", {projFil: projFil, caseStatus: caseStatus, customfilter: customfilter, caseChangeAssignto: caseChangeAssignto, caseChangeDuedate: caseChangeDuedate, caseChangePriority: caseChangePriority, caseChangeType: caseChangeType, mstype: mstype, priFil: priFil, caseTypes: caseTypes, caseMember: caseMember, caseAssignTo: caseAssignTo, caseDate: caseDate, caseSearch: caseSearch, casePage: casePage, caseId: caseId, caseTitle: caseTitle, caseDueDate: caseDueDate, caseNum: caseNum, caseLegendsort: caseLegendsort, caseAtsort: caseAtsort, startCaseId: startCaseId, caseResolve: caseResolve, caseMenuFilters: caseMenuFilters, caseUrl: caseUrl, detailscount: detailscount, milestoneIds: milestoneIds, case_srch: case_srch, case_date: case_date, 'case_due_date': case_due_date, caseCreateDate: caseCreateDate, projIsChange: projIsChange}, function (res) {
        if (res) {
            if (res.errormsg) {
                $('#caseLoader').hide();
                showTopErrSucc('error', res.errormsg);
                $('#caseId').val(''); // Close a case
                $('#caseStart').val(''); // Start a case
                $('#caseResolve').val(''); // Resolve a case
                return false;
            }
            //window.location.hash = 'tasks';
            refreshTasks = 0;

            //Data needed for create task
            if (res.projUser) {
                PUSERS = res.projUser;
                for (pi in PROJECTS) {
                    if (PROJECTS[pi].Project.uniq_id == res.projUniq) {
                        defaultAssign = PROJECTS[pi].Project.default_assign;
                    }
                }
            }

            if ($('#customFIlterId').val()) {
                //$('#customFIlterId').val('');
                refreshTasks = 1;
            }
            $("#caseChangeAssignto").val('');
            $("#caseChangeDuedate").val('');
            $("#caseChangePriority").val('');
            $("#caseChangeType").val('');
            var projFil = $('#projFil').val();//alert();

            //no uses found so below code is commented
            /*if(projFil == 'all'){
             $("#statusfil").addClass("stall");
             $("#typefil").addClass("stall");
             $("#priofil").addClass("stall");
             $("#memfil").addClass("stall");
             }else{
             $("#statusfil").removeClass("stall");
             $("#typefil").removeClass("stall");
             $("#priofil").removeClass("stall");
             $("#memfil").removeClass("stall");
             }*/
            var projIsChange = $('#projIsChange').val();
            var caseMenuFilters = $('#caseMenuFilters').val(); // Assign To
            if ((projIsChange != projFil) || (caseMenuFilters === "overdue")) {
                //$("#new_case_more_div").html(""); //not impl

                loadCaseMenu(strURL + "ajax_case_menu", {"projUniq": projFil, "pageload": 0, "page": "dashboard", "filters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype})

                //$('#mlstRefresh').html(''); //not impl
            }

            if (projFil == 'all') {
                remember_filters('ALL_PROJECT', 'all');
            } else {
                remember_filters('ALL_PROJECT', '');
            }

            //document.getElementById('caseViewSpan').innerHTML = res;
            $("#caseViewSpan").html(tmpl("case_project_tmpl", res));
            //scrollPageTop();
            document.getElementById('topmostdiv').style.display = 'block';
            document.getElementById('caseViewSpan').style.display = 'block';
            document.getElementById('caseViewDetails').style.display = 'none';
            $('#caseLoader').hide();
            if ($('#lviewtype').val() == 'compact') {
                $('.tsk_tbl').addClass('compactview_tbl');
                $('#topaction').addClass('compactview_action');
            } else {
                $('.tsk_tbl').removeClass('compactview_tbl');
                $('#topaction').removeClass('compactview_action');
            }

            var params = parseUrlHash(urlHash);
            if (params[0] != "tasks") {
                parent.location.hash = "tasks";
            }

            if (page == 'details') {
                easycase.ajaxCaseDetails(params[1], 'case', 0);
            } else {
                easycase.routerHideShow('tasks'); //show task listing
                if (ioMsgClicked == 1) {
                    ioMsgClicked = 0;
                }
                $('#detail_section').html('');
            }

            //document.getElementById('sortbydiv').style.display = 'block'; //not impl
            //document.getElementById('hideshowwdgt').style.display = 'block'; //not impl
            //document.getElementById('changeheight').style.display = 'block'; //not impl

            // Custome Date range in due date 
            $("div [id^='set_due_date_']").each(function (i) {
                $(this).datepicker({
                    altField: "#CS_due_date",
                    showOn: "button",
                    buttonImage: HTTP_IMAGES + "images/calendar.png",
                    buttonStyle: "background:#FFF;",
                    changeMonth: false,
                    changeYear: false,
                    minDate: 0,
                    hideIfNoPrevNext: true,
                    onSelect: function (dateText, inst) { 
                        var caseId = $(this).parents('.cstm-dt-option').attr('data-csatid');
                        var datelod = "datelod" + caseId;
                        var showUpdDueDate = "showUpdDueDate" + caseId;
                        $("#" + showUpdDueDate).html("");
                        $("#" + datelod).show();
                        //var popupCloseDueDate = "popupCloseDueDate"+caseId;
                        //$('#'+popupCloseDueDate).fadeOut(400);
                        var text = '';
                        $.post(HTTP_ROOT + "easycases/ajax_change_DueDate", {"caseId": caseId, "duedt": dateText, "text": text}, function (data) {
                            if (data) {
                                $("#" + datelod).hide();
                                $("#" + showUpdDueDate).html(data.top + '<span class="due_dt_icn"></span>');
                                //$('#'+popupCloseDueDate).fadeOut(200);
                                //$("#case_dtls_due"+caseId).html(data.details);
                                if (data.isAssignedUserFree != 1 && GTLG == 1) {
                                    var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600); alert(estimated_hr);
                                    openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
                            }
                            }
                        }, 'json');
                    }
                });
            });
            /*if(menu_filter =='milestone'){
             $("div [id^='milestone_']").each(function(i){
             $(this).sortable({
             position:'relative',
             update: function( event, ui ) {
             var order = $(this).sortable("serialize", {attribute:"itemid"});
             var url = strURL+'sort_event?'+order;
             $.post(url);
             }
             
             });
             $(this ).sortable( "option", "handle", ".handle" );								  
             });
             }
             
             if(document.getElementById('hid_cs').value >= 1) {
             //document.getElementById('topactions').style.display='block'; //not impl
             //document.getElementById('bottomactions').style.display = 'block'; //not impl
             //document.getElementById('widgets-container').style.display ='block'; //not impl
             //document.getElementById('widgets-containertype').style.display ='block'; //not impl
             }
             else {
             if(menu_filter !='milestone' && menu_filter !='files' ){
             $('#topactions').show();
             }
             //document.getElementById('topactions').style.display='none';
             //document.getElementById('bottomactions').style.display = 'none';
             }
             //document.getElementById('changeheight').style.height = '70px';*/

            var caseId = $('#caseId').val(); // Close a case
            var startCaseId = $('#caseStart').val(); // Start a case
            var caseResolve = $('#caseResolve').val(); // Resolve a case

            //alert(caseId);
            if (caseId) {
                $('#caseId').val('');
                var chk = caseId.indexOf(",");
                if (chk != -1) {
                    showTopErrSucc('success', _('Tasks are closed.'));
                } else {
                    showTopErrSucc('success', _('Task is closed.'));
                }
                //loadCaseMenu(HTTP_ROOT+"easycases/ajax_case_menu", {"projUniq":projFil,"pageload":1,"page":"dashboard","caseMenuFilters":caseMenuFilters,'case_date':case_date,'case_due_date':case_due_date,'caseStatus':caseStatus,'caseTypes':caseTypes,'priFil':priFil,'caseMember':caseMember,'caseAssignTo':caseAssignTo,'caseSearch':caseSearch,'milestoneIds':milestoneIds,'checktype':checktype});
                //$("#casePage").val('1');
                casePage = 1;
            }
            if (startCaseId) {
                $('#caseStart').val('');
                var chk = startCaseId.indexOf(",");
                if (chk != -1) {
                    showTopErrSucc('success', _('Tasks are started.'));
                } else {
                    showTopErrSucc('success', _('Task is started.'));
                }
                //$("#casePage").val('1');
                casePage = 1;
            }
            if (caseResolve) {
                $('#caseResolve').val('');
                var chk = caseResolve.indexOf(",");

                if (chk != -1) {
                    showTopErrSucc('success', _('Tasks are resolved.'));
                } else {
                    showTopErrSucc('success', _('Task is resolved.'));
                }
                //$("#casePage").val('1');
                casePage = 1;
            }
            /*if(reply == 1) {
             var spnajx = "ajxCse"+detailscount;
             document.getElementById(spnajx).style.display = 'block';
             }*/

            var usrUrl = HTTP_ROOT + "users/";
            var url = HTTP_ROOT + "easycases/";
            var filter = $('#caseMenuFilters').val();
            var projFil = $('#projFil').val();
            var projIsChange = $('#projIsChange').val();
            $('#projIsChange').val(projFil);
            $.post(url + "ajax_project_size", {"projUniq": projFil, "pageload": 0}, function (data) {
                if (data) {
                    /*$('#csTotalSize').html(data.used_text);
                     $('#csTotalHours').html(data.hourspent);*/
                    $('#csTotalHours').html(data.used_text);
                    if (data.last_activity) {
                        $('#projectaccess').html(data.last_activity);
                        $('#last_project_id').val(data.lastactivity_proj_id);
                        $('#last_project_uniqid').val(data.lastactivity_proj_uid);
                        var url = document.URL.trim();
                        if (isNaN(url.substr(url.lastIndexOf('/') + 1)) && (url.substr(url.lastIndexOf('/') + 1)).length != 32) {
                            $('#selproject').val($('#last_project_id').val());
                            $('#project_id').val($('#last_project_id').val());
                        }
                    }
                }
            }, 'json');




            var caseId = $('#caseId').val();
            var startCaseId = $('#caseStart').val();
            var caseResolve = $('#caseResolve').val();

            var ischange = 0;
            if (caseMenuFilters && caseMenuFilters != "files") {
                ischange = 1;
            }

            $('#caseId').val('');
            $('#caseResolve').val('');
            $('#caseStart').val('');
            var checktype = $("#checktype").val();
            var caseMenuFilters = $('#caseMenuFilters').val(); // Assign To
            $.post(strURL + "ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype}, function (data) {
                if (data) {
                    $('#ajaxCaseStatus').html(tmpl("case_widget_tmpl", data)).show();
                    var width = 0;
                    $('#ajaxCaseStatus').find('div.status').each(function(){
                        width+=parseInt($(this).outerWidth());
                        
                    });
                    $('#ajaxCaseStatus').width(width + 10);
                    $('#ajaxCaseStatus').css('display','inline-block');
                    $("#upperDiv_not").hide();
                    var statusnot = $('#not_sts').html();
                    var n = '';//statusnot.indexOf("Closed");
                    if (caseMenuFilters != 'milestone' && caseMenuFilters != 'closecase' && n == -1) {
                        var closed = $("#closedcaseid").val();
                        if (closed != 0) {
                            $("#upperDiv_not").show();
                            if (closed == 1) {
                                $("#closedcases").html(_("Including")+" <b>" + closed + "</b> "+_("'Closed' task"));
                            } else {
                                $("#closedcases").html(_("Including")+" <b>" + closed + "</b> "+_("'Closed' task"));
                            }
                        } else {
                            $("#upperDiv_not").hide();
                            $("#closedcases").html('');
                        }
                    }
                    $('.f-tab').tipsy({gravity: 'n', fade: true});
                }
            });
            var caseMenuFilters = $('#caseMenuFilters').val();
            //setMenuClass(caseMenuFilters); //not impl
            //document.getElementById('csTotalSize').style.display='block'; //not impl
            //$('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true}); //not impl

            var x = $("#getcasecount").val();
            $("#showcasecount").html(x);

            if (caseMenuFilters && caseMenuFilters == "milestone") {
                $("#mileStoneFilter").show();
            } else {
                $("#mileStoneFilter").hide();
                //document.getElementById('topactions').style.margin='0'; //not impl
                //document.getElementById('changeheight').style.margin='0'; //not impl
            }
            if ((caseId || startCaseId || caseResolve || caseChangeType || caseChangeDuedate || caseChangePriority || caseChangeAssignto) && ($('#email_arr').val() != '')) {
                $.post(strURL + "ajaxemail", {'json_data': $('#email_arr').val(), 'type': 1});
            }

        }
        /*if(projFil != 'all'){
         $.post(strURL+"ajax_quickcase_mem",{"projUniq":projFil,"pageload":0}, function(data){
         if(data) {
         $('#ajxQuickMem').html(data);
         }
         });
         
         }*/
        if (caseId || startCaseId || caseResolve) {
            resetBreadcrumbFilters(strURL, caseStatus, priFil, caseTypes, caseMember, caseAssignTo, 1, case_date, case_due_date, '', '', '', '', milestoneIds);
        }
        if (!caseId && !startCaseId && !caseResolve) {
            var clearCaseSearch = $('#clearCaseSearch').val();
            var isSort = $('#isSort').val();
            $('#clearCaseSearch').val('');
            resetBreadcrumbFilters(strURL, caseStatus, priFil, caseTypes, caseMember, caseAssignTo, 0, case_date, case_due_date, casePage, caseSearch, clearCaseSearch, caseMenuFilters, milestoneIds);
        }
    });
    subscribeClient();
}

/* Called when a page number is clicked */
function casePaging(page) {
    if ($('#caseMenuFilters').val() == 'milestone') {
        $('#mlstPage').val(page);
        ManageMilestoneList();
    } else {
        //document.getElementById('casePage').value = page;
        casePage = page;
        easycase.refreshTaskList();
    }

}

function checkedAllResReply(CS_id) {
    var allchk = CS_id + 'chkAllRep';
    var allid = 'hidtotresreply' + CS_id;
    var res = $('#' + allid).val();
    var chkid = CS_id + "chk_";
    if ($('#' + allchk).is(":checked")) {
        $('input[id^="' + chkid + '"]').each(function (i) {
            if(!$(this).is(':disabled')){
                $(this).attr("checked", 'checked');
            }
        });
    } else {
        $('input[id^="' + chkid + '"]').each(function (i) {
            if(!$(this).is(':disabled')){
                $(this).removeAttr("checked");
            }
        });
    }
    /*for(var i=0;i < res;i++) {
     var chkids = CS_id+"chk_"+i;
     if(document.getElementById(allchk).checked == true) {
     document.getElementById(chkids).checked = true;
     }
     else {
     document.getElementById(chkids).checked = false;	
     }
     }*/
}
function changeToRte(id) {
    var custom = 'custom' + id;
    var txt = 'txt' + id;
    var html = 'html' + id;
    var plane = 'plane' + id;
    var editortype = 'editortype' + id;

    var cmnts = 'txa_comments' + id;
    //alert(document.getElementById(txt).style.display);
    if (document.getElementById(txt).style.display == 'none') {
        document.getElementById(custom).style.display = 'none';
        document.getElementById(txt).style.display = 'block';
        document.getElementById(html).style.display = 'block';
        document.getElementById(plane).style.display = 'none';
        $('#' + editortype).val(0);
        $("#" + cmnts).focus();
    } else {
        document.getElementById(custom).style.display = 'block';
        document.getElementById(txt).style.display = 'none';
        document.getElementById(html).style.display = 'none';
        document.getElementById(plane).style.display = 'block';
        $('#' + editortype).val(1);
    }

    var hidhtml = 'hidhtml' + id;
    var showhtml = 'showhtml' + id;
    document.getElementById(showhtml).style.display = 'none';
    document.getElementById(hidhtml).style.display = 'block';
}
function valforlegend(id, leg) {
    document.getElementById(leg).value = id;
}
function select_reply_user(cs_autoid, obj) {
    uid = $(obj).val();
    $('#' + cs_autoid + 'chk_' + uid).attr('checked', 'checked');
}
function validateComments(id, uniqid, legend, ses_type, pid) {

    var msgid = "txa_comments" + id,
            planetext = "txa_plane" + id,
            html = "html" + id,
            text = "plane" + id;

    var pj = "";
    var msg = "";
    var err = "";
    if (document.getElementById(html).style.display == 'block') {

        var text = tinyMCE.get(msgid).getContent();

        var ed = tinyMCE.get(msgid);
        ed.selection.select(ed.getBody(), true);
        var data = ed.selection.getContent({format: 'text'});
        //var data = $('#'+msgid).text().trim();
        //var data = $('#'+msgid).html($('#'+msgid).text().trim());
        if (data.trim() == "") {
            var err = _("Nothing to post!");
            document.getElementById(msgid).focus();
        } else {
            //document.getElementById(text).innerHTML='';
        }
    } else {
        if (document.getElementById(planetext).value.trim() == "") {
            var err = _("Nothing to post!");
            var msg = $('#' + msgid).val();
            document.getElementById(planetext).focus();
        } else {
            //document.getElementById(html).innerHTML='';
        }
    }

    if (err) {
        if ($('input[id^=' + id + 'jqueryfile]').length && confirm(_('Are you sure you want to post without a reply?'))) {
            err = '';
        } else {
            showTopErrSucc('error', err);
            return false;
        }
    }

    if (!err) {
        /*var case_leg = $("#legend"+id).val();
         if(case_leg == 2) {
         $("#close"+id).show();
         $("#resolve"+id).show();
         $("#start"+id).hide();
         $("#reply"+id).show();
         $("#reopen"+id).hide();
         }
         else if(case_leg == 3) {
         $("#start"+id).hide();
         $("#close"+id).hide();
         $("#resolve"+id).hide();
         $("#reply"+id).hide();
         $("#reopen"+id).show();
         }
         else if(case_leg == 4) {
         $("#start"+id).hide();
         }
         else if(case_leg == 5) {
         $("#start"+id).hide();
         $("#resolve"+id).hide();
         }
         if((case_leg == 2 || case_leg == 3 || case_leg == 4 || case_leg == 5) && ses_type != 1 && ses_type != 2) {
         $("#arch"+id).hide();
         $("#edit"+id).hide();
         }*/
        //alert(id);
        //return false;
      /*  if(legend == 1){
            $("#legend" + id).val('2');
        } */
        submitAddNewCase('Post', id, uniqid, '', '0', status, legend, pid);
        return true;
    }
}

var totalReplies = 0;
var easycase = {};
easycase.getStatus = function (type, legend) {
    /*if (type == 10) {
     return '<div class="label update">Update</div>';
     } else */if (legend == 1) {
        return '<div class="label new">New</div>';
    } else if (legend == 2 || legend == 4) {
        return '<div class="label wip">In Progress</div>';
    }
    if (legend == 3) {
        return '<div class="label closed">Closed</div>';
    } else if (legend == 4) {
        return '<div class="label wip">In Progress</div>';
    } else if (legend == 5) {
        return '<div class="label resolved">Resolved</div>';
    }
}
easycase.imageTypeIcon = function (format) {
    var iconsArr = ["gd", "db", "zip", "xls", "doc", "jpg", "png", "bmp", "pdf", "tif"]; //html,txt,ppt

    if (format == "xlsx") {
        format = "xls"
    } else if (format == "docx" || format == "rtf" || format == "odt") {
        format = "doc"
    } else if (format == "jpeg") {
        format = "jpg"
    } else if (format == "gif") {
        format = "png"
    } else if (format == "rar" || format == "gz" || format == "bz2") {
        format = "zip"
    }

    if ($.inArray(format, iconsArr) == -1) {
        format = 'html'
    }
    return format;
}
easycase.getColorStatus = function (type, legend) {
    if (type == 10) {
        return '<b class="update">Update</b>';
    } else if (legend == 1) {
        return '<b class="new">New</b>';
    } else if (legend == 2 || legend == 4) {
        return '<b class="wip">In Progress</b>';
    }
    if (legend == 3) {
        return '<b class="closed">Closed</b>';
    } else if (legend == 4) {
        return '<b class="wip">In Progress</b>';
    } else if (legend == 5) {
        return '<b class="resolved">Resolved</b>';
    }
}
easycase.getPriority = function (casePriority) {
    if (casePriority == "NULL" || casePriority == "") {
        return;
    } else if (casePriority == 0) {
        return 'high';
    } else if (casePriority == 1) {
        return 'medium';
    } else if (casePriority >= 2) {
        return 'low';
    }
}
easycase.getColorPriority = function (casePriority) {
    if (casePriority == "NULL" || casePriority == "") {
        return;
    } else if (casePriority == 0) {
        return '<b style="color:#FF0000;">High Priority</b>';
    } else if (casePriority == 1) {
        return '<b style="color:#28AF51;">Medium Priority</b>';
    } else if (casePriority == 2) {
        return '<b style="color:#B4A532;">Low Priority</b>';
    }
}
easycase.refreshTaskList = function (dtlsid, details) {
    var params = parseUrlHash(urlHash);
    var filterV = $('#caseMenuFilters').val();
    if (params[0] == 'tasks' || params[0] == 'kanban' || (params[0] == 'calendar' || filterV == 'calendar')) {
        if (details) {
            $('#t_' + dtlsid).remove();
        }
        if (params[0] == 'tasks') {
            ajaxCaseView();
        } else if (params[0] == 'calendar' || filterV == 'calendar') {
            calendarView('calendar');
        } else {
            $('#milestoneUid').val('');
            $('#milestoneId').val('');
            window.location.hash = 'kanban';
            easycase.showKanbanTaskList();
        }
    } else if (dtlsid) {
        if (details && ($('#caseMenuFilters').val() == '')) { //load task listing first if case is resolved or closed 
            refreshKanbanTask = 1;
            refreshActvt = 1;
            refreshMilestone = 1;
            refreshManageMilestone = 1;
            ajaxCaseView('details');
        } else {
            refreshTasks = 1;
            refreshKanbanTask = 1;
            refreshActvt = 1;
            refreshMilestone = 1;
            refreshManageMilestone = 1;
            easycase.ajaxCaseDetails(dtlsid, 'case', 0);
        }
    } else {
        refreshTasks = 1;
        window.location.hash = 'tasks';
    }
}
easycase.ajaxCaseDetails = function (caseUniqId, type, dtls) {
    //var projFil = document.getElementById('projFil').value;
    var strURL = HTTP_ROOT + "easycases/case_details";
    $('#caseLoader').show();
    $.post(strURL, {"caseUniqId": caseUniqId, "details": dtls}, function (data) {
        if (data) {
            totalReplies = data.total;
            if(SES_TYPE >= 3 && data.client_status == 1){
               
                 window.location.hash = 'tasks';
            }
            easycase.routerHideShow('details');
            $('#t_' + data.csUniqId).remove();
            $("#detail_section").append(tmpl("case_details_tmpl", data));
            if (TSG === 1) {
                var html1 = '<ul class="dropdown-menu quick_menu lgnd_menu"><li class="pop_arrow_new"></li>';
										
                if (typeof data.statuslist != 'undefined' && data.statuslist != 'null') {
                    for (var k in data.statuslist) {
                        var wfl = data.statuslist[k];
                        var count = 0;
                        var html = '';
                        var width = 0;
                        var totwidth = wfl.Status.length;
                        var width = parseInt(100 / totwidth) - 0.5;
                        var col = '';
                        var opacity = 0;
                        for (var key in wfl.Status) {
                            var status = wfl.Status[key];
                            count++;
                            if (data.csLgndodr == status.seq_order || status.seq_order < data.csLgndodr) {
                                col = '#666';//#8dc2f8
                                opacity = 1;
                            } else {
                                col = '#ccc';
                                opacity = 0.4;
                            }
							
                            html += "<li style='width:" + width + "%;background:" + status.color + ";border-color:" + status.color + ";opacity:" + opacity + "' onclick='changecselegend(" + data.csAtId + ", " + status.id + ")'><div class='status-title'><div class='small-caps small ellipsis-view' style='color:" + col + ";' rel='tooltip' title='" + status.name + "'>" + status.name + "</div></div></li>";
                            html1 +="<li><a href='javascript:void(0);' onclick='changecselegend(" + data.csAtId + ", " + status.id + ")' style='color:"+status.color+";font-weight:bold'>"+status.name+"</a></li>";
                        }
                    }
                } else {
                    var col = '';
                    var opacity = 0;
                    var id = 0;
                    var count = 0;
                    var html = '';
                    var width = parseInt(100 / 4) - 0.5;
                    var lgnd_val = 0;
                    if (data.csLgndRep == 4) {
                        lgnd_val = 2;
                    } else {
                        lgnd_val = data.csLgndRep;
                    }
                    for (var k in default_stages) {
                        var wfl = default_stages[k];
                        count++;
                        if (count == 3) {
                            id = 5;
                        } else if (count == 4) {
                            id = 3;
                        } else {
                            id = count;
                        }
                        /** Set the legend in a sequence **/
                        if (lgnd_val == 5) {
                            lgnd_val_new = 3;
                        } else if (lgnd_val == 3) {
                            lgnd_val_new = 4;
                        } else {
                            lgnd_val_new = lgnd_val;
                        }
                        /*end */
                        id_new = count;
                        if (id_new <= lgnd_val_new) {
                            //if (lgnd_val == id || id < wfl.Status.seq_order){
                            col = '#666';//#8dc2f8
                            opacity = 1;
                        } else {
                            col = '#ccc';
                            opacity = 0.4;
                        }
                        html += "<li style='width:" + width + "%;background:" + wfl.Status.color + ";border-color:" + wfl.Status.color + ";opacity:" + opacity + "' onclick='changecselegend(" + data.csAtId + ", " + id + ")'><div class='status-title'><div class='small-caps small ellipsis-view' style='color:" + col + ";' rel='tooltip' title='" + wfl.Status.name + "'>" + wfl.Status.name + "</div></div></li>";
                        html1 +="<li><a href='javascript:void(0);' onclick='changecselegend(" + data.csAtId + ", " + id + ")' style='color:"+ wfl.Status.color +";font-weight:bold'>"+ wfl.Status.name +"</a></li>"
                    }
                }
                //$(".status-bar-ul").html(html);
                $("#stsdiv_"+data.csAtId).first('span').append(html1);
            }
            if($('#t_'+data.csUniqId).find('.tbl_check_name').find('input[type="checkbox"]:checked').length == $('#t_'+data.csUniqId).find('.tbl_check_name').find('input[type="checkbox"]').length){
                $('#t_'+data.csUniqId).find('input[id$="chkAllRep"]').prop('checked', true);
            }

            var holder_detal = document.getElementById('holder_detl'),
                    tests = {
                        dnd_detl: 'draggable' in document.createElement('span')
                    };
            if ($('#holder_detl').length) {
                if (tests.dnd_detl) {
                    holder_detal.ondragover = function () {
                        $('#holder_detl').addClass('hover');
                        return false;
                    };
                    holder_detal.ondrop = function (e) {
                        $('#holder_detl').removeClass('hover');
                        if ($.trim(e.dataTransfer.files[0].type) === "" || e.dataTransfer.files[0].size === 0) {
                            alert(_('File "') + e.dataTransfer.files[0].name + '" '+_('has no extension!\nPlease upload files with extension.'));
                            e.stopPropagation();
                            e.preventDefault();
                        }
                        return false;
                    };
                }
                $('#holder_detl').mouseout(function () {
                    $('#holder_detl').removeClass('hover');
                });
            }

            easycase.detailPageinate();
            bindPrettyview("prettyPhoto");//This calls for images on task post and reply of case details
            bindPrettyview("prettyImg");//This calls for file list showing in right side bar of case details

            fuploadUI(data.csAtId);

            /*if(!getCookie("crtdtsk_less") || getCookie("crtdtsk_less")!=1){
             $('#mor_toggle'+data.csAtId).hide()
             $('#less_toggle'+data.csAtId).show();
             $('#tskmore_'+data.csAtId).slideDown(350);
             }*/

            var params = parseUrlHash(urlHash);
            if (params[0] != "details") {
                parent.location.hash = "details" + "/" + caseUniqId;
            }

            if (dtls == 0) {
                //var openId = document.getElementById('openId').value;
                //$("#"+spnajx).css('background', '#f5f5f5');
            } else {
                /*if(openId) {
                 document.getElementById('openId').value = "";
                 document.getElementById('urllvalueCase').value = "";
                 }*/
            }

            easycase.loadTinyMce(data.csAtId);
            $('[rel=tooltip]').tipsy({gravity: 's', fade: true});
            $("img.lazy").lazyload({placeholder: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="});
            //$(".customfile-button").removeClass('button');
            if (scrollToRep && scrollToRep == caseUniqId) {
                scrollPageTop($('#t_' + scrollToRep + ' .reply_task_block'));
            } else {
                scrollPageTop();
            }
            $(".slide_rht_con").animate({marginLeft: "0px"}, "fast");
            $(".crt_slide").css({display: "none"});
			
			// Custom Date range in due date 
            $("[id^='det_set_start_date_']").each(function (i) {
                $(this).datepicker({
                    altField: "#CS_start_date",
                    showOn: "button",
                    buttonImage: HTTP_IMAGES + "images/calendar.png",
                    buttonStyle: "background:#FFF;",
                    changeMonth: false,
                    changeYear: false,
                    minDate: 0,
                    hideIfNoPrevNext: true,
                    onSelect: function (dateText, inst) {
                        var caseId = $(this).parents('.cstm-dt-option').attr('data-csatid');
                        detChangeStartDate(caseId, dateText, '', caseUniqId, data.csNoRep);
                    }
                });
            });

            // Custome Date range in due date 
            $("div [id^='det_set_due_date_']").each(function (i) {
                $(this).datepicker({
                    altField: "#CS_due_date",
                    showOn: "button",
                    buttonImage: HTTP_IMAGES + "images/calendar.png",
                    buttonStyle: "background:#FFF;",
                    changeMonth: false,
                    changeYear: false,
                    minDate: 0,
                    hideIfNoPrevNext: true,
                    onSelect: function (dateText, inst) {
                        var caseId = $(this).parents('.cstm-dt-option').attr('data-csatid');
                        detChangeDueDate(caseId, dateText, '', caseUniqId, data.csNoRep);
                    }
                });
            });
        } else {
            alert(_('Sorry! some problem occurred.'));
        }
        $('#caseLoader').hide();
        scrollToRep = null;
    });
}
easycase.loadTinyMce = function (csAtId) {
    $("#htmlloader" + csAtId).show();
    var tiny_mce_url = HTTP_ROOT + 'js/tinymce/tiny_mce.js';
    $('#txa_comments' + csAtId).tinymce({
        // Location of TinyMCE script
        script_url: tiny_mce_url,
        theme: "advanced",
        plugins: "paste",
        theme_advanced_buttons1: "bold,italic,strikethrough,underline,|,numlist,bullist,|,indent,outdent",
        theme_advanced_resizing: false,
        theme_advanced_statusbar_location: "",
        paste_text_sticky: true,
        gecko_spellcheck: true,
        paste_text_sticky_default: true,
        forced_root_block: false,
        width: "100%",
        autoresize_min_height: "130px",
        autoresize_max_height: "300px",
        autoresize_on_init: true,
        autoresize_bottom_margin: 20,
        oninit: function () {
            $('#txa_comments' + csAtId).tinymce().focus();
            $("#htmlloader" + csAtId).hide();
        }
    });
}
easycase.showTaskDetail = function (task_data) {
    if ($('#t_' + task_data[1]).length) {
        easycase.routerHideShow('details');
        $('#t_' + task_data[1]).show();
        easycase.detailPageinate();

        if (scrollToRep && scrollToRep == task_data[1]) {
            scrollPageTop($('#t_' + scrollToRep + ' .reply_task_block'));
        } else {
            scrollPageTop();
        }
        scrollToRep = null;
    } else {
        easycase.ajaxCaseDetails(task_data[1], 'case', 0);
    }
}
easycase.showTaskLists = function (page) {
    if (refreshTasks == 1) {
        $("#caseMenuFilters").val('');
    }
    $(".menu-files").removeClass('active');
    $(".menu-milestone").removeClass('active');
    $(".menu-cases").addClass('active');
    // || $(".crt_tsk").is(':visible')
    if (($('#caseViewSpan').html() && refreshTasks == 0)) {
        easycase.routerHideShow(page);
        scrollPageTop();
    } else {
        easycase.refreshTaskList();
    }
    displayMenuProjects('dashboard', '6', '');
    //crt_popup_close();
}
easycase.detailPageinate = function () {
    if (urlHash) {
        var params = parseUrlHash(urlHash);
        if (params[1]) {
            if ($('.case-title[data-task="' + params[1] + '"]').length) {
                var prevId = $('.case-title[data-task="' + params[1] + '"]').parents('.tr_all[id^="curRow"]').attr('id');
                if ($('#' + prevId).nextAll('.tr_all[id^="curRow"]').length) {
                    //enable next
                    $('.task_detail_head .next').removeClass('disable');
                    $('.task_detail_head .next').attr('disabled', false);
                } else {
                    //disable next
                    $('.task_detail_head .next').addClass('disable');
                    $('.task_detail_head .next').attr('disabled', true);
                }

                if ($('#' + prevId).prevAll('.tr_all[id^="curRow"]').length) {
                    //enable next
                    $('.task_detail_head .prev').removeClass('disable');
                    $('.task_detail_head .prev').attr('disabled', false);
                } else {
                    //disable next
                    $('.task_detail_head .prev').addClass('disable');
                    $('.task_detail_head .prev').attr('disabled', true);
                }
            } else {
                $('.task_detail_head .next, .task_detail_head .prev').addClass('disable');
                $('.task_detail_head .next, .task_detail_head .prev').attr('disabled', true);
            }
        }
    }
    $('.task_detail_head .next, .task_detail_head .prev').tipsy({gravity: 'n', fade: true});
}
easycase.rollNext = function (el) {
    if (urlHash) {
        var params = parseUrlHash(urlHash);
        if (params[1]) {
            if ($('.case-title[data-task="' + params[1] + '"]').length) {
                var prevId = $('.case-title[data-task="' + params[1] + '"]').parents('.tr_all[id^="curRow"]').attr('id');
                if ($('#' + prevId).nextAll('.tr_all[id^="curRow"]').length) {
                    var nextId = $('#' + prevId).nextAll('.tr_all[id^="curRow"]').attr('id');
                    window.location.hash = 'details/' + $('#' + nextId).find('.case-title[id^="titlehtml"]').attr('data-task');
                }
            } else {
                window.location.hash = 'tasks';
            }
        }
    }
}
easycase.rollPrev = function (el) {
    if (urlHash) {
        var params = parseUrlHash(urlHash);
        if (params[1]) {
            if ($('.case-title[data-task="' + params[1] + '"]').length) {
                var prevId = $('.case-title[data-task="' + params[1] + '"]').parents('.tr_all[id^="curRow"]').attr('id');

                if ($('#' + prevId).prevAll('.tr_all[id^="curRow"]').length) {
                    var nextId = $('#' + prevId).prevAll('.tr_all[id^="curRow"]').attr('id');
                    window.location.hash = 'details/' + $('#' + nextId).find('.case-title[id^="titlehtml"]').attr('data-task');
                }
            } else {
                window.location.hash = 'tasks';
            }
        }
    }
}
easycase.showActivities = function () {
    $('#select_view div').tipsy({gravity: 'n', fade: true});
    $('#select_view div').removeClass('disable');
    $('#actvt_btn').addClass('disable');
    $("#caseMenuFilters").val('activities');
    if ($('#activities').html() && refreshActvt == 0) {
        easycase.routerHideShow('activities');
        scrollPageTop();
    } else {
        loadActivity('');
        loadOverdue('my');
        loadUpcoming('my');
    }
}
function loadActivity(type) {
    var displayed = $("#displayed").val();
    var prj_id = $("#projFil").val();
    var limit1, limit2, projid;
    if (type == "more") {
        limit1 = displayed;
        limit2 = 10;
        projid = prj_id;
    } else {
        limit1 = 0;
        limit2 = 29;
        projid = prj_id;
    }
    if (type == "more") {
        $(".morebar").show();
    } else {
        $("#caseLoader").show();
    }
    var strURL = HTTP_ROOT + "users/ajax_activity/";
    $("#PieChart").hide();
    $.post(strURL, {'type': type, 'limit1': limit1, 'limit2': limit2, 'projid': projid}, function (res) {
        easycase.routerHideShow('activities');
        refreshActvt = 0;
        var params = parseUrlHash(urlHash);
        if (params[0] != "activities") {
            parent.location.hash = "activities";
        }
        if (type == "more") {
            $(".morebar").hide();
            var data = tmpl("ajax_activity_tmpl", res);
            $("#activities").append(data);
            $("img.lazy").lazyload({placeholder: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="});
            var displayed = $("#displayed").val();
            var newdisplayed = (parseInt(displayed) + 1) + 10;
            $("#displayed").val(newdisplayed);
            if (prj_id == 'all') {
                $(".prj_dvs").show();
            }
        } else {
            $("#caseLoader").hide();
            var result = document.getElementById('activities');
            result.innerHTML = tmpl("ajax_activity_tmpl", res);
            $("img.lazy").lazyload({placeholder: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="});
            if (prj_id == 'all') {
                $(".prj_dvs").show();
            }
            scrollPageTop();
        }
        setStatus();
        var totalact = $("#totalact").val();
        if (parseInt(totalact) > 0) {
            if (projid != 'all') {
                $('#PieChart').load(HTTP_ROOT + 'users/activity_pichart', {'pjid': projid});
                $("#PieChart").show();
            }
        }
    });
    subscribeClient();
}
function loadOverdue(type) {
    $("#moreOverdueloader").show();
    $("#Overdue").html('');
    var prj_id = $("#projFil").val();
    var projid = prj_id;
    var strURL = HTTP_ROOT + "users/ajax_overdue/";
    $.post(strURL, {'type': type, 'projid': projid}, function (res) {
        $("#Overdue").html(res);
        $("#moreOverdueloader").hide();
    });
}
function loadUpcoming(type) {
    $("#moreOverdueloader").show();
    $("#Upcoming").html('');
    var prj_id = $("#projFil").val();
    var projid = prj_id;
    var strURL = HTTP_ROOT + "users/ajax_upcoming/";
    $.post(strURL, {'type': type, 'projid': projid}, function (res) {
        $("#Upcoming").html(res);
        $("#moreOverdueloader").hide();
    });
}

//view all reply and show latest 5
function showHideMoreReply(id, type) {
    var showhidemorereply = "showhidemorereply" + id;
    var morereply = "morereply" + id;
    var hidereply = "hidereply" + id;
    var loadreply = "loadreply" + id;

    var totdata1 = "totdata" + id;
    var totdata = $('#' + totdata1).val();

    if (totdata > 5 && type != "post") {
        if (type == "less") {
            document.getElementById(morereply).style.display = 'inline';
            document.getElementById(hidereply).style.display = 'none';
            for (i = 6; i <= totdata; i++)
            {
                var rep = "rep" + i;
                document.getElementById(rep).style.display = 'none';
            }
            $('#threadview_type' + id).val('less');
        } else if (type == "more") {
            document.getElementById(morereply).style.display = 'none';
            document.getElementById(hidereply).style.display = 'inline';
            for (i = 6; i <= totdata; i++)
            {
                var rep = "rep" + i;
                document.getElementById(rep).style.display = 'block';
            }
            $('#threadview_type' + id).val('more');
        }
    } else {
        if (type != "post") {
            document.getElementById(loadreply).style.visibility = 'visible';
        }
        var strURL = HTTP_ROOT + "easycases/case_reply";
        $.post(strURL, {"id": id, "type": type, sortorder: $('#thread_sortorder' + id).val()}, function (data) {
            if (data)
            {
                document.getElementById(loadreply).style.visibility = 'hidden';
                $("#" + showhidemorereply).html(tmpl("case_replies_tmpl", data));
                bindPrettyview("prettyPhoto");

                if (type == "post") {
                    alert(_('This is an error! Please refresh the page')); //Comment added, remove later by coml testing.
                    /*loadcomments = "loadcomments"+id;
                     document.getElementById(loadcomments).style.display='none';
                     postcomments = "postcomments"+id;
                     document.getElementById(postcomments).style.display='block';
                     
                     var html = "html"+id;
                     var plane = "plane"+id;
                     if(document.getElementById(html).style.display == 'block') {
                     var txa_comments = "txa_comments"+id;
                     document.getElementById(txa_comments).value = "";
                     }
                     else {
                     var txa_plane = "txa_plane"+id;
                     document.getElementById(txa_plane).value = "";
                     }
                     showTopErrSucc('success','Your task is posted.');*/
                } else if (type == "more") {
                    $('#threadview_type' + id).val('more');
                    document.getElementById(morereply).style.display = 'none';
                    document.getElementById(hidereply).style.display = 'inline';
                } else if (type == "less") {
                    $('#threadview_type' + id).val('less');
                    document.getElementById(morereply).style.display = 'inline';
                    document.getElementById(hidereply).style.display = 'none';
                }
            }
        });
    }
}

// Sorting reply text and reply box 
function sortreply(id, uniqid) {
    //tinymce.execCommand('mceRemoveControl',true,'#txa_comments'+uniqid); //not impl
    //var reply_text = $('#reply_content'+id).html(); //not impl
    //var reply_box = $('#reply_box'+id).html(); //not impl
    if ($('#thread_sortorder' + id).val() == 'ASC') {
        $('#thread_sortorder' + id).val('DESC');
    } else {
        $('#thread_sortorder' + id).val('ASC');
    }
    var sortorder = $('#thread_sortorder' + id).val();
    var type = $('#threadview_type' + id).val();
    var strURL = HTTP_ROOT + "easycases/case_reply";
    var showhidemorereply = "showhidemorereply" + id;
    var morereply = "morereply" + id;
    var hidereply = "hidereply" + id;
    var loadreply = "loadreply" + id;

    if (type == 'less') {
        var viewtype = 'post';
    } else {
        var viewtype = type;
    }
    if ($('#remain_case' + id).val()) {
        var rem_cases = $('#remain_case' + id).val();
    } else {
        var rem_cases = 0;
    }
    $('#loadreply_sort_' + id).css('visibility', 'visible');
    $.post(strURL, {"id": id, "type": viewtype, 'sortorder': sortorder, 'rem_cases': rem_cases}, function (data) {
        if (data) {
            $('#loadreply_sort_' + id).css('visibility', 'hidden');

            var results = document.getElementById(showhidemorereply);
            results.innerHTML = tmpl("case_replies_tmpl", data);
            bindPrettyview("prettyPhoto");

            if ($('#thread_sortorder' + id).val() == 'DESC') {
                $('#repsort_asc_' + id).show();
                $('#repsort_desc_' + id).hide();
            } else {
                $('#repsort_asc_' + id).hide();
                $('#repsort_desc_' + id).show();
            }

            if (type == "more") {
                $('#' + morereply).hide();
                $('#' + hidereply).show();
            } else if (type == "less") {
                $('#' + morereply).show();
                $('#' + hidereply).hide();
            }
        }
    });
}

//Router functions moved to script.js

function multipleCaseAction(hidid)
{
    var idfor = Array();
    var caseid = Array();
    var splt = Array();
    var done = 0;
    var cscnt = 0;
    var casenos = "";
    var x = $('#hid_cs').val();

    for (var i = 1; i <= x; i++) {
        var id = "actionChk" + i;
        if (document.getElementById(id).disabled == false)
        {
            if (document.getElementById(id).checked == true) {

                var actionCls = "actionCls" + i;
                var legend = $('#' + actionCls).val();

                var val = $('#' + id).val();
                var splt = val.split("|");

                var caseRes = 0;
                if (legend == 1 || legend == 2 || legend == 4) {
                    caseRes = 1;
                }
                if (legend == 3 && hidid == "caseId") {
                    casenos += splt[1] + ",";
                    cscnt++;
                } else if (hidid == "caseStart" && legend != 1) {
                    casenos += splt[1] + ",";
                    cscnt++;
                } else if (hidid == "caseResolve" && caseRes == 0) {
                    casenos += splt[1] + ",";
                    cscnt++;
                } else {
                    caseid.push(splt[0]);
                    idfor.push(splt[1]);
                    done++;
                    if (splt[2]) {
                        $("#t_" + splt[2]).remove();
                    }
                }
            }
        }
    }
    if (cscnt) {
        var casenos = casenos.substr(0, casenos.length - 1);
        if (hidid == "caseStart") {
            var msg = "started";
        }
        if (hidid == "caseResolve") {
            var msg = "resolved";
        }
        if (hidid == "caseId") {
            var msg = "closed";
        }
        alert(_("Task")+" #" + casenos + " "+("cannot be")+" " + msg + "!");

        if (cscnt == 1 && msg) {
            //alert("Task# "+casenos+" is already "+msg+"!");
        } else if (msg) {
            //alert("Task# "+casenos+" are already "+msg+"!");
        }
    }
    if (done) {
        $('#' + hidid).val(caseid);
        $('#slctcaseid').val(idfor);
        refreshActvt = 1;
        easycase.refreshTaskList();
    }
}

function enableButtons() {
    if ($('.chkOneTsk:not(:disabled):checked').length) {
        $('#chkAllTsk').parents('.dropdown').addClass('active');
        $('#chkAllTsk').next('.all_chk').attr('data-toggle', 'dropdown');
    } else {
        $('#chkAllTsk').parents('.dropdown').removeClass('active');
        $('#chkAllTsk').next('.all_chk').attr('data-toggle', '');
    }
}
$(function (chkAll, chkOne, row, active_class) {
    $(document).on('click', chkAll, function (e) {
        if ($(chkAll).is(':checked')) {
            $(chkOne + ":not(:disabled)").prop('checked', true);
            $(chkOne + ":not(:disabled)").parents(row).addClass(active_class);
        } else {
            $(chkAll).parent().removeClass('open');
            $(chkOne + ":not(:disabled)").prop('checked', false);
            $(chkOne + ":not(:disabled)").parents(row).removeClass(active_class);
        }
        enableButtons();
    });

    $(document).on('click', chkOne, function (e) {
        if ($(this).is(':checked')) {
            $(this).parents(row).addClass(active_class);
        } else {
            $(chkAll).parent().removeClass('open');
            $(this).parents(row).removeClass(active_class);
        }

        if ($(chkOne + ':not(:disabled):checked').length == $(chkOne + ':not(:disabled)').length) {
            $(chkAll).prop('checked', true);
        } else {
            $(chkAll).prop('checked', false);
        }
        enableButtons();
    });
}('#chkAllTsk', '.chkOneTsk', '.tr_all', 'tr_all_active'));

function startCase(id, no, dtlsid) {
    var conf = confirm(_("Are you sure you want to Start the task #") + no + " ?");
    if (conf == false) {
        $('#caseStart').val("");
        //enableButtons(); not impl
        return false;
    } else {
        refreshActvt = 1;
        refreshKanbanTask = 1
        refreshTasks = 1;
        refreshManageMilestone = 1;
        refreshMilestone = 1;
        if (dtlsid) {
            //$("#t_"+dtlsid).attr('id','');
        }
        $('#caseStart').val(id);
        var hashtag = parseUrlHash(urlHash);
        //if(hashtag=='kanban' || PAGE_NAME=='milestonelist'){
        if ((hashtag[0] == 'kanban') || (hashtag[0] == 'milestonelist') || (($('#caseMenuFilters').val() != 'cases') && (hashtag[0] == 'details'))) {
            actiononTask(id, dtlsid, no, 'start');
        } else {
            easycase.refreshTaskList(dtlsid);
        }
    }
}
function changeCaseType(id, no) {
    $('#caseChangeType').val(id);
    $('#slctcaseid').val(no);
}
function caseResolve(id, no, dtlsid) {
    var conf = confirm(_("Are you sure you want to Resolve the task #")+ + no + " ?");
    if (conf == false) {
        $('#caseResolve').val("");
        //enableButtons(); not impl
        return false;
    } else {
        refreshActvt = 1;
        refreshKanbanTask = 1
        refreshTasks = 1;
        refreshManageMilestone = 1;
        refreshMilestone = 1;
        if (dtlsid) {
            //$("#t_"+dtlsid).attr('id','');
        }
        $('#caseResolve').val(id);
        var hashtag = parseUrlHash(urlHash);
        //if(hashtag=='kanban' || PAGE_NAME=='milestonelist'){
        if (hashtag[0] == 'kanban' || hashtag[0] == 'milestonelist' || hashtag[0] == 'details') {
            actiononTask(id, dtlsid, no, 'resolve');
        } else {
            easycase.refreshTaskList(dtlsid, 1);
        }
    }
}
function setCloseCase(id, no, dtlsid) {
    var conf = confirm(_("Are you sure you want to Close the task #") + no + " ?");
    if (conf == false) {
        $('#caseId').val("");
        //enableButtons(); not impl
        return false;
    } else {
        refreshActvt = 1;
        refreshKanbanTask = 1
        refreshTasks = 1;
        refreshManageMilestone = 1;
        refreshMilestone = 1;
        $('#caseId').val(id);
        var hashtag = parseUrlHash(urlHash);
        //if(hashtag=='kanban' || PAGE_NAME=='milestonelist'){
        if ((hashtag[0] == 'kanban') || (hashtag[0] == 'milestonelist') || (($('#caseMenuFilters').val() != 'cases') && (hashtag[0] == 'details'))) {
            actiononTask(id, dtlsid, no, 'close');
        } else {
            easycase.refreshTaskList(dtlsid, 1);
        }
    }
}
//Move task one project to other project starts
function mvtoProject(id, obj, alltask) {
    var is_multiple = 0;
    if (typeof alltask != 'undefined') {
        var chked = 0;
        $('input[id^="actionChk"]').each(function (i) {
            if ($(this).is(":checked") && !($(this).is(":disabled"))) {
                chked = 1;
            }
        });
        if (chked == 0) {
            showTopErrSucc('error', _("Please check atleast one task to move"));
            return false;
        }
        var project_id = $('#curr_sel_project_id').val();
        is_multiple = 1
        case_id = '';
        var title = 'Move all task';
    } else {
        var project_id = $(obj).attr('data-prjid');
        var case_id = $(obj).attr('data-caseid');
        var case_no = $(obj).attr('data-caseno');
        var title = $("#titlehtml" + id + ' .case_title').html();
        if (title.length > 40)
            title = jQuery.trim(title).substring(0, 37).split(" ").slice(0, -1).join(" ") + "...";
    }
    openPopup();
    $(".mv_project").show();
    $('#inner_mvproj').html('');
    if ($('#caseMenuFilters').val() == 'kanban' || $('#caseMenuFilters').val() == 'milestonelist') {
        $('#header_mvprj').html(title);
    } else if (is_multiple) {
        $('#header_mvprj').html(title);
    } else {
        $('#header_mvprj').html('#' + case_no + ": " + title);
    }

    $("#err_msg_dv").hide();
    $("#mvprjloader").hide();
    $.post(HTTP_ROOT + "easycases/ajax_move_task_to_project", {"project_id": project_id, "case_id": case_id, 'is_multiple': is_multiple}, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#inner_mvproj').show();
            $('#inner_mvproj').html(data);
            $('.mv-btn').show();
            $("#mvprj_btn").show();
            $('#case_no').val(case_no);
            $("#new_project").focus();
        }
    });
}

function moveTaskToProject() {
    var prj_id = $("#project").val();
    var new_prj_id = $("#new_project").val();
    var old_prj_nm = $("#old_project_nm").val();
    var new_prj_nm = $('#new_project :selected').text();

    if ($('#ismultiple_move').val() == 1) {
        if ($('#projFil').val() != 'all') {
            var cbval = '';
            var case_id = new Array();
            var spval = '';
            var case_no = new Array();
            $('input[id^="actionChk"]').each(function (i) {
                if ($(this).is(":checked") && !($(this).is(":disabled"))) {
                    cbval = $(this).val();
                    spval = cbval.split('|');
                    case_id.push(spval[0]);
                    case_no.push(spval[1]);
                }
            });
        } else {
            return false;
        }
    } else {
        var case_id = $("#case").val();
        var case_no = $('#case_no').val();
    }
    if (parseInt(prj_id) !== parseInt(new_prj_id) && parseInt(new_prj_id)) {
        if ($('#ismultiple_move').val() == 1) {
            if (countJS(case_id) == 1) {
                var cmsg = _("Are you sure you want to move task #") + case_no[0] + " "+_("to")+" '" + new_prj_nm + "' ?";
            } else {
                var cmsg = _("Are you sure you want to move all the selected task to '") + new_prj_nm + "' ?";
            }
        } else {
            var cmsg = _("Are you sure you want to move task #") + case_no + " "+_("from")+" '" + old_prj_nm + "' "+_("to")+" '" + new_prj_nm + "' ?";
        }

        if (confirm(cmsg)) {
            $("#mvprj_btn").hide();
            $("#mvprjloader").show();
            $.post(HTTP_ROOT + "easycases/check_to_project", {"old_id": prj_id, "new_id": new_prj_id}, function (res) {
		if(res != 1){
			alert(_('Task(s) can not be moved to this project, because this project is in a different Task Status Group(workflow). Please choose another project having same Task Status Group.'));
			$("#mvprj_btn").show();
                        $("#mvprjloader").hide();
			return false;
		}
                else{
            $.post(HTTP_ROOT + "easycases/move_task_to_project", {"project_id": new_prj_id, "old_project_id": prj_id, "case_id": case_id, "case_no": case_no, 'is_multiple': $('#ismultiple_move').val()}, function (res) {
                if (parseInt(res)) {
                    refreshActvt = 1;
                    refreshKanbanTask = 1
                    refreshTasks = 1;
                    refreshManageMilestone = 1;
                    refreshMilestone = 1;
                    closePopup();
                    if ($('#ismultiple_move').val() == 1) {
                        if (countJS(case_id) == 1) {
                            showTopErrSucc('success', _("Task")+" #" + case_no[0] + " "+_("moved to")+" '" + new_prj_nm + "'");
                        } else {
                            showTopErrSucc('success', countJS(case_id) +_(" Tasks are moved to '") + new_prj_nm + "'");
                        }
                    } else {
                        showTopErrSucc('success', _("Task")+" #" + case_no +_(" moved from '") + old_prj_nm + "' "+_("to")+" '" + new_prj_nm + "'");
                    }

                    var hashtag = parseUrlHash(urlHash);
                    if (hashtag[0] == 'milestonelist') {
                        showMilestoneList();
                    } else if (hashtag[0] == 'kanban') {
                        easycase.showKanbanTaskList('kanban');
                    } else {
                        easycase.refreshTaskList();
                        var projFil = $('#projFil').val();
                        var casemenufllter = $('#caseMenuFilters').val();

                        loadCaseMenu(HTTP_ROOT + "easycases/ajax_case_menu", {"projUniq": projFil, "pageload": 1, "page": "dashboard", "filters": casemenufllter});
                    }
                    displayMenuProjects('dashboard', '6', '');
                } else {
                    $("#mvprj_btn").show();
                    $("#mvprjloader").hide();
                    showTopErrSucc('error', _("Unable to move task #") + case_no +_(" from '") + old_prj_nm + _("' to '") + new_prj_nm + "'");
                    return false;
                }
            });
                }
            });
        } else {
            return false;
        }
    } else {
        $("#err_msg_dv").show();
        return false;
    }
}

function rmverrmsg() {
    $("#err_msg_dv").hide();
}
//Move task one project to other project ends

function archiveCase(id, cno, pid, dtlsid) {
    var conf = confirm(_("Are you sure you want to archive the task #") + cno + " ?");
    if (conf == false) {
        return false;
    } else {
        refreshActvt = 1;
        var curRow = "curRow" + id;
        $("#" + curRow).fadeOut(500);
        $('#caseLoader').show();
        var strurl = HTTP_ROOT + "easycases/archive_case";//alert(strurl);
        $.post(strurl, {"id": id, "cno": cno, "pid": pid}, function (data) {
            if (data) {
                $("#" + dtlsid).remove();//attr('id','');
                $('#caseLoader').hide();
                $('#caseResolve').val('');
                showTopErrSucc('success', _("Task #") + cno +_(" is archived."));
                $.post(HTTP_ROOT + "users/project_menu", {"page": 1, "limit": 6}, function (data) {
                    if (data) {
                        $('#ajaxViewProjects').html(data);
                    }
                });
                var hashtag = parseUrlHash(urlHash);

                if (hashtag[0] == 'milestonelist') {
                    showMilestoneList();
                } else if (hashtag[0] == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    easycase.refreshTaskList();
                    var projFil = $('#projFil').val();
                    var casemenufllter = $('#caseMenuFilters').val();
                    loadCaseMenu(HTTP_ROOT + "easycases/ajax_case_menu", {"projUniq": projFil, "pageload": 1, "page": "dashboard", "filters": casemenufllter});
                }
            }
        });
    }
}
function deleteCase(id, cno, pid, dtlsid) {
    if (confirm(_("Are you sure you want to delete the task #") + cno + " ?")) {
        refreshActvt = 1;
        var curRow = "curRow" + id;
        $("#" + curRow).fadeOut(500);
        $('#caseLoader').show();
        var strurl = HTTP_ROOT + "easycases/delete_case";
        $.post(strurl, {"id": id, "cno": cno, "pid": pid}, function (data) {
            if (data) {
                /*$.post(HTTP_ROOT+"easycases/ajax_getStorage",function(res) {
                 if(res){
                 var clr = 'red';
                 var max_storage = $("#max_storage").text();
                 if(parseFloat(res) < parseFloat(max_storage)){
                 clr = 'green';
                 }
                 var str = "<font style='color:"+clr+"'> <span id='used_storage'>"+res+"</span>/<b><span id='max_storage'>"+max_storage+"</span> Mb</b></font>";
                 $("#storage_spn").html(str);
                 }
                 });*/

                $("#" + dtlsid).remove();//attr('id','');
                $('#caseLoader').hide();
                $('#caseResolve').val('');
                showTopErrSucc('success', _("Task #") + cno +_(" is deleted."));
                $.post(HTTP_ROOT + "users/project_menu", {"page": 1, "limit": 6}, function (data) {
                    if (data) {
                        $('#ajaxViewProjects').html(data);
                    }
                });
                var hashtag = parseUrlHash(urlHash);
                if (hashtag[0] == 'milestonelist') {
                    showMilestoneList();
                } else if (hashtag[0] == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    easycase.refreshTaskList();
                    var projFil = $('#projFil').val();
                    var casemenufllter = $('#caseMenuFilters').val();
                    loadCaseMenu(HTTP_ROOT + "easycases/ajax_case_menu", {"projUniq": projFil, "pageload": 1, "page": "dashboard", "filters": casemenufllter});
                }
            }
        });
    }
}
function removeThisCase(count, msid, getcount, milestone_id, cno, uid) {//alert('jyoti');exit;
    //alert(count);alert(msid);alert(getcount);
    var conf = confirm(_("Are you sure you want to remove 'task #") + cno +"'"+_(" from this milestone?"));
    if (conf == true) { //alert('yes');
        var curRow = "curRow" + getcount;
        $("#" + curRow).fadeOut(500);
        //$("#"+dtlsid).fadeOut(500);
        var caseDiv = 'caseDiv' + getcount;
        var caseImg = 'caseImg' + getcount;
        var strURL = HTTP_ROOT + 'milestones/case_listing';

        $("#" + caseImg).show();
        $.post(strURL, {"milestone_id": milestone_id, "count": getcount, "msid": msid, "uid": uid}, function (data) {
            if (data) { //alert(data);
                $('#' + caseDiv).html(data);
                $("#" + caseImg).hide();
            }
        });
        easycase.refreshTaskList();

        return true;
    } else {
        var checkBox = 'csCheckBox' + count;
        document.getElementById(checkBox).checked = true;
        return false;
    }
}
function changestatus(caseId, statusId, statusName, statusTitle, caseUniqId) {
    var typlod = "typlod" + caseId;
    var showUpdStatus = "showUpdStatus" + caseId;
    var typIconClass = $("#" + showUpdStatus).attr('class');
    $("#" + showUpdStatus).attr('class', '');
    $('#' + typlod).show();
    $("#t_" + caseUniqId).remove();
    var strURL = HTTP_ROOT + "easycases/ajax_change_status";
    $.post(strURL, {"caseId": caseId, "statusId": statusId, "statusName": statusName, "statusTitle": statusTitle}, function (data) {
        if (data) {
            $('#' + typlod).hide();
            typIconClass = 'type_' + data[0];
            $("#" + showUpdStatus).addClass('type_' + data[0]);

            /*var projUnqId = document.getElementById('projFil').value;
             var url = HTTP_ROOT+"easycases/ajax_types";
             $.post(url,{"projUniq":projUnqId,"pageload":1}, function(data){
             if(data) {
             $('#statusTypes').html(data);
             }
             });*/
            var hashtag = parseUrlHash(urlHash);
            if (hashtag[0] == 'milestonelist') {
                window.location.hash = 'milestonelist';
            } else if (hashtag[0] == 'kanban') {
                window.location.hash = 'kanban';
            } else {
                easycase.refreshTaskList();
            }
        }
    }, 'json').always(function () {
        $('#' + typlod).hide();
        $("#" + showUpdStatus).addClass(typIconClass);
    });
}
/* Used on task details */
function changetype(caseId, statusId, statusName, statusTitle, caseUniqId, cno) {
    var typlod = "dettyplod" + caseId;
    var typdiv = "typdiv" + caseId;
    $('#' + typdiv).hide();
    $('#' + typlod).show();
    $.post(HTTP_ROOT + "easycases/ajax_change_status", {"caseId": caseId, "statusId": statusId, "statusName": statusName, "statusTitle": statusTitle}, function (data) {
        if (data) {
            //$("#"+showUpdStatus).addClass('type_'+data[0]);
            $('#' + typdiv + ' div').first().attr('class', '').addClass('fl task_types_' + data[0]);
            $('#' + typdiv + ' .quick_action').html(data[1]);
        }
    }, 'json').always(function () {
        $('#' + typdiv).show();
        $('#' + typlod).hide();
        actiononTask(caseId, caseUniqId, cno, 'tasktype');
    });
}
function displayAssignToMem(csId, project, caseAssgnUid, caseUniqId, page, cno) {
    if (!page) {
        page = '';
    }
    if (countJS(PUSERS) && PUSERS[project]) {
        appendAssignUsers(csId, project, caseUniqId, page, cno);
    } else if ($('#assgnload' + csId).length || $('#detAssgnload' + csId).length) {
        //{"project":project,"csId":csId,'caseAssgnUid':caseAssgnUid,'caseUniqId':caseUniqId}
        $.post(HTTP_ROOT + "easycases/ajax_assignto_mem", {"project": project}, function (data) {
            if (data) {
                PUSERS = data;
                appendAssignUsers(csId, project, caseUniqId, page, cno);
                //$('#showAsgnToMem'+csId).html(data);
            }
        });
    } else {
        //already loaded project user list
    }
}
function appendAssignUsers(csId, project, caseUniqId, page, cno) {
    if (page != 'details')
        $('#showAsgnToMem' + csId).html('<li class="pop_arrow_new"></li>');
    else
        $('#detShowAsgnToMem' + csId).html('<li class="pop_arrow_new"></li>');
    for (ui in PUSERS[project]) {
        var t1 = PUSERS[project][ui].User.name;
        if (PUSERS[project][ui].User.id == SES_ID) {
            var t2 = 'me';
            var t = PUSERS[project][ui].User.id;
            if (page == 'details')
                $('#detShowAsgnToMem' + csId).append('<li title="' + t1 + '" class="memHover" ><a href="javascript:void(0);" style="color:#E0814E" onclick="detChangeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\'' + t + '\',\'' + cno + '\')">me</a></li>')
            else
                $('#showAsgnToMem' + csId).append('<li title="' + t1 + '" class="memHover" ><a href="javascript:void(0);" style="color:#E0814E" onclick="changeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\'' + t + '\')">me</a></li>')
        } else {
            var t2 = PUSERS[project][ui].User.name;
            var t = PUSERS[project][ui].User.id;
            if (page == 'details')
                $('#detShowAsgnToMem' + csId).append('<li title="' + t1 + '" class="memHover ttc"><a href="javascript:void(0);" onclick="detChangeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\'' + t + '\',\'' + cno + '\')">' + shortLength(t2, 10) + '</a></li>');
            else
                $('#showAsgnToMem' + csId).append('<li title="' + t1 + '" class="memHover ttc"><a href="javascript:void(0);" onclick="changeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\'' + t + '\')">' + shortLength(t2, 10) + '</a></li>');
        }

    }
    $('#detShowAsgnToMem' + csId).append('<li title="Nobody" class="memHover ttc"><a href="javascript:void(0);" onclick="detChangeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\' 0 \',\'' + cno + '\')">Nobody</a></li>');
    $('#showAsgnToMem' + csId).append('<li title="Nobody" class="memHover ttc"><a href="javascript:void(0);" onclick="changeAssignTo(\'' + csId + '\', \'' + caseUniqId + '\',\'0 \')">Nobody</a></li>');
}
function changeAssignTo(caseId, caseUniqId, assignId) {
    $('#caseChangeAssignto').val(caseId);
    var asgnlod = "asgnlod" + caseId;
    var showUpdAssign = "showUpdAssign" + caseId;
    $("#" + showUpdAssign).html("");
    $("#" + asgnlod).show();
    var caseMenuFilters = $('#caseMenuFilters').val(); // Assign To
    var projFil = $('#projFil').val(); // Project Uniq ID
    $("#t_" + caseUniqId).remove();
    $.post(HTTP_ROOT + "easycases/ajax_change_AssignTo", {"caseId": caseId, "assignId": assignId}, function (data) {
        if (data) {
            $("#" + asgnlod).hide();
            $("#" + showUpdAssign).html(data.top + '<span class="due_dt_icn"></span>');
            if (data.isAssignedUserFree != 1 && assignId != 0 && GTLG == 1) {
                var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
            }
            var hashtag = parseUrlHash(urlHash);
            if (hashtag[0] == 'milestonelist') {
                window.location.hash = 'milestonelist';
            } else if (hashtag[0] == 'kanban') {
                window.location.hash = 'kanban';
            } else {
                loadCaseMenu(HTTP_ROOT + "easycases/ajax_case_menu", {"projUniq": projFil, "pageload": 0, "page": "dashboard", "filters": caseMenuFilters});
                easycase.refreshTaskList();
            }
        }
    }, 'json');
}
/* Used on task details */
function detChangeAssignTo(caseId, caseUniqId, assignId, cno) {
    //$('#caseChangeAssignto').val(caseId);
    var asgnlod = "detasgnlod" + caseId;
    var showUpdAssign = "case_dtls_asgn" + caseId;
    //$("#"+showUpdAssign).hide();
    //$("#"+asgnlod).show();
    $('#caseLoader').show();
    $.post(HTTP_ROOT + "easycases/ajax_change_AssignTo", {"caseId": caseId, "assignId": assignId}, function (data) {
        if (data) {
            //$("#"+showUpdAssign).html(data.top+'<span class="due_dt_icn"></span>');
            if (data.isAssignedUserFree != 1 && assignId != 0 && GTLG == 1) {
                var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
            }
        }
    }, 'json').always(function () {
        //$("#"+showUpdAssign).show();
        //$("#"+asgnlod).hide();
        actiononTask(caseId, caseUniqId, cno, 'assignto');
    });
}
function changeCaseDuedate(id, no) {
    $('#caseChangeDuedate').val(id);
    $('#slctcaseid').val(no);
}
function changeDueDate(caseId, duedt, text, caseUniqId) {
    var datelod = "datelod" + caseId;
    var showUpdDueDate = "showUpdDueDate" + caseId;
    $("#" + showUpdDueDate).html("");
    $("#" + datelod).show();
    $("#t_" + caseUniqId).remove();
    //var popupCloseDueDate = "popupCloseDueDate"+caseId;
    //$('#'+popupCloseDueDate).fadeOut(400);
    $.post(HTTP_ROOT + "easycases/ajax_change_DueDate", {"caseId": caseId, "duedt": duedt, "text": text}, function (data) {
        if (data) {
            $("#" + datelod).hide();
            $("#" + showUpdDueDate).html(data.top + '<span class="due_dt_icn"></span>');
            //$('#'+popupCloseDueDate).fadeOut(200);
            //$("#case_dtls_due"+caseId).html(data.details);
            if (data.isAssignedUserFree != 1 && GTLG == 1) {
               var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
            }
            var hashtag = parseUrlHash(urlHash);
            if (hashtag[0] == 'milestonelist') {
                window.location.hash = 'milestonelist';
            } else if (hashtag[0] == 'kanban') {
                window.location.hash = 'kanban';
            } else {
                easycase.refreshTaskList();
            }
        }
    }, 'json');
}
/* Used on task details */
function detChangeStartDate(caseId, startdt, text, caseUniqId, cno) {
	var dt = new Date(startdt);
	var dtchk = $('#due_date_chng_'+caseId).val().trim();
	//var dtchk = $('#start_date_chng_'+caseId).val().trim();
	if (dtchk != 'NoDueDate'){
		if(dt.getTime() > new Date(dtchk).getTime()) {
			alert('Start date can not exceed due date.\nPlease choose start date less than or equal to due date.');
			return false;
		}
	}
    var datelod = "detSdlod" + caseId;
    var showUpdDueDate = "case_dtls_start" + caseId;
    $("#" + showUpdDueDate).hide();
    $("#" + datelod).show();
    $.post(HTTP_ROOT + "easycases/ajax_change_StartDate", {"caseId": caseId, "startdt": startdt, "text": text}, function (data) {
        if (data) {
            if (text) {
                if (text == 'No Start Date') {
                    text = text;
                } else {
                    text = 'Start ' + text;
                }
            } else {
                text = 'Start On ' + data.top;
            }
            $("#" + showUpdDueDate + ' .duequick_action').html(_(text));
            if (data.isAssignedUserFree != 1 && GTLG == 1) {
                var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
        }
        }
    }, 'json').always(function () {
        $("#" + showUpdDueDate).show();
        $("#" + datelod).hide();
        //actiononTask(caseId, caseUniqId, cno, 'duedate');
    });
}
function detChangeDueDate(caseId, duedt, text, caseUniqId, cno) {
	var dt = new Date(duedt);
	var dtchk = $('#start_date_chng_'+caseId).val().trim();
	if (dtchk != 'NoStartDate'){
		if(dt.getTime() < new Date(dtchk).getTime()) {
			alert('Due date can not be less than start date.\nPlease choose due date greater than or equal to start date.');
			return false;
		}
	}
    var datelod = "detddlod" + caseId;
    var showUpdDueDate = "case_dtls_due" + caseId;
    $("#" + showUpdDueDate).hide();
    $("#" + datelod).show();
    $.post(HTTP_ROOT + "easycases/ajax_change_DueDate", {"caseId": caseId, "duedt": duedt, "text": text}, function (data) {
        if (data) {
            if (text) {
                if (text == 'No Due Date') {
                    text = text;
                } else {
                    text = 'Due ' + text;
                }
            } else {
                text = 'Due On ' + data.top;
            }
            $("#" + showUpdDueDate + ' .duequick_action').html(_(text));
            if (data.isAssignedUserFree != 1 && GTLG == 1) {
                var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
            }
        }
    }, 'json').always(function () {
        $("#" + showUpdDueDate).show();
        $("#" + datelod).hide();
        actiononTask(caseId, caseUniqId, cno, 'duedate');
    });
}
/* Used on task details */
function detChangepriority(caseId, priority, caseUniqId, cno) {
    var prilod = "prilod" + caseId;
    var showUpdPri = "pridiv" + caseId;
    $("#" + showUpdPri).hide();
    $('#' + prilod).show();
    var pre_priority = $('#' + showUpdPri).attr('data-priority');
    if (pre_priority == priority) {
        $('#' + prilod).hide();
        $("#" + showUpdPri).show();
        //   $("#" + showUpdPri + ' .quick_action').removeClass('prio_high prio_mediem prio_low').addClass('prio_'+protyTtl.toLowerCase());            

    } else {
        $.post(HTTP_ROOT + "easycases/ajax_change_priority", {"caseId": caseId, "priority": priority}, function (data) {
            if (data && data.protyCls) {
                $("#" + showUpdPri).removeClass('high_priority medium_priority low_priority').addClass(data.protyCls);
                $("#" + showUpdPri + ' .quick_action').html(_(data.protyTtl));
                $("#" + showUpdPri).attr('data-priority', priority);
            }
        }).always(function () {
            //$("#"+showUpdPri).show();
            //$('#'+prilod).hide();
            actiononTask(caseId, caseUniqId, cno, 'priority');
        });
    }
}

function ajaxSorting(type, cases, el) {
    /*if(typeof getCookie("TASKGROUPBY") !='undefined' && getCookie("TASKGROUPBY") !='date'){
     return false;
     } */
    $('#isSort').val("1");
    if (typeof (getCookie("TASKSORTBY") != 'undefined') && getCookie("TASKSORTBY") == type) {
        var tsorder = getCookie('TASKSORTORDER');
        if (tsorder == 'ASC') {
            remember_filters("TASKSORTORDER", 'DESC');
            var tcls = 'tsk_desc';
        } else {
            remember_filters("TASKSORTORDER", 'ASC');
            var tcls = 'tsk_asc';
        }
    } else {
        remember_filters("TASKSORTBY", type);
        remember_filters("TASKSORTORDER", 'DESC');
        var tcls = 'tsk_asc';
    }
    $('.tsk_sort').removeClass('tsk_asc tsk_desc');
    var el = $('.sort' + type).children('.tsk_sort').addClass(tcls);

    /*if($(el).children('.tsk_sort.tsk_asc').length){
     $('.tsk_sort').removeClass('tsk_asc tsk_desc');
     $(el).children('.tsk_sort').addClass('tsk_desc');
     
     //$('#remember_filter').load(HTTP_ROOT+"easycases/remember_filters?issort=1&"+type+"=desc");
     
     $('#caseTitle, #caseDate, #caseDueDate, #caseNum, #caseLegendsort, #caseAtsort, #caseCreatedDate').val("");
     if(type == "title"){
     $('#caseTitle').val("desc");
     }
     if(type == "duedate") {
     $('#caseDueDate').val("desc");
     }
     if(type == "caseAtsort") {
     $('#caseAtsort').val("desc");
     }
     if(type == "caseno") {
     $('#caseNum').val("desc");
     }
     } else {
     $('.tsk_sort').removeClass('tsk_asc tsk_desc');
     $(el).children('.tsk_sort').addClass('tsk_asc');
     
     //$('#remember_filter').load(HTTP_ROOT+"easycases/remember_filters?issort=1&"+type+"=asc");
     
     $('#caseTitle, #caseDate, #caseDueDate, #caseNum, #caseLegendsort, #caseAtsort, #caseCreatedDate').val("");
     if(type == "title"){
     $('#caseTitle').val("asc");
     }
     if(type == "duedate") {
     $('#caseDueDate').val("asc");
     }
     if(type == "caseAtsort") {
     $('#caseAtsort').val("asc");
     }
     if(type == "caseno") {
     $('#caseNum').val("asc");
     }
     }*/
    //if(cases) {
    easycase.refreshTaskList();
    //}
}

function filterValue(id, prjuniqid, date, type, status, member, assignto, praiority, searchtxt, pname) {//alert(prjuniqid);
    $('#customFIlterId').val(id);
    //document.getElementById('projFil').value = prjuniqid;
    $('#caseStatus').val(status);
    $('#priFil').val(praiority);
    $('#caseTypes').val(type);
    $('#caseMember').val(member);
    $('#caseAssignTo').val(assignto);
    $('#caseDateFil').val(date);
    //document.getElementById('case_search').value = searchtxt; //not implemented
    if (prjuniqid == 'all') {
        var prjuid = 0;
        var radio = 0;
        var all = 'all';
        var page = 'dashboard';
    } else {
        var prjuid = prjuniqid;
        var radio = "proj_" + prjuniqid;
        var all = 0;
        var page = 'dashboard';
    }
    //updateAllProj(radio,prjuid,page,all,pname,searchtxt);
    easycase.refreshTaskList();
}
// Filter popup with ajax value 
function allfiltervalue(type) {
    if (arguments[1]) {
        var event = arguments[1];
        event.preventDefault();
        event.stopPropagation();
    }
    $('.dropdown_status').hide();
    $('#dropdown_menu_' + type + '_div').show();
    var hashtag = parseUrlHash(urlHash);
    if (hashtag == 'kanban') {
        $('#dropdown_menu_' + type).css({"display": "inline-block", 'float': 'right'});
    } else {
        $('#dropdown_menu_' + type).css({"display": "inline-block", 'float': 'none'});
    }
    if (type == 'utilization') {
        $('#dropdown_menu_utilization_div').show();
        $('#dropdown_menu_utilization').show();
        return false;
    } /*else if (type == 'utilization_status') {
        $('#dropdown_menu_utilization_status_div').show();
        $('#dropdown_menu_utilization_status').show();
        return false;
    } */
    var li_ldr = "<li><center><img src='" + HTTP_ROOT + "img/images/del.gif' alt='loading...' title='loading...'/><center></li>";
    $('#dropdown_menu_' + type).html(li_ldr);
    var projFil = $('#projFil').val();
    var caseMenuFilters = $('#caseMenuFilters').val();
    if (type == 'mlstn') {
        var checktype = $('#checktype').val();
        $.post(HTTP_ROOT + "easycases/ajax_milestones", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'checktype': checktype}, function (data) {
            if (data) {
                $('#dropdown_menu_mlstn').html(data);
            }
        });
    } else if (type == 'date') {
        $('#dropdown_menu_date').html(tmpl("date_filter_tmpl"));
        iniDateFilter();
    } else if (type == 'createdDate') {
        $('#dropdown_menu_createddate_div').show();
        $('#dropdown_menu_createddate').show();
    } else if (type == 'resource') {
        $('#dropdown_menu_resource_div').show();
        $('#dropdown_menu_resource').show();
        var resourcelist = "";
       // var usrids = $('#tlog_resource').val().split('-');
        var usrids = getCookie("rsrclog").split('-');
        
        $.post(HTTP_ROOT + "easycases/getProjUserList", {"projUniq": projFil}, function (data) {
            
            if (data) {
                $.each(data, function (key, val) {
            $.each(val, function (k1, v1) {
                var checked = "";
                var usrid = v1['User']['id'];
                if ($.inArray(usrid, usrids) > -1) {
                    checked = "checked";
                }
                var type = 'check';
                resourcelist += "<li class='li_check_radio'><a href='javascript:void(0)'><div class='checkbox'><label><input id='res_" + usrid + "' class='resource_check' data-id='" + usrid + "' type='checkbox' onclick='general.filterResource(" + usrid + ", \"" + type + "\")' " + checked + "/> " + v1['User']['name'] + "</label></div></a></li>";
            });
        });
        $('#dropdown_menu_resource').html(resourcelist);
            }
        },'json'); 
      
    } else {
        var caseStatus = $("#caseStatus").val();
        var case_date = $("#caseDateFil").val();
        var case_due_date = $("#casedueDateFil").val();
        var caseTypes = $("#caseTypes").val();
        var caseMember = $("#caseMember").val();
        var caseAssignTo = $("#caseAssignTo").val();
        var caseSearch = $("#caseSearch").val();
        var priFil = $("#priFil").val();
        var milestoneIds = $("#milestoneIds").val();
        var checktype = $("#checktype").val();
        if (type == 'status') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_status', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_status').html(data);
                }
            });
        } else if (type == 'types') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_types', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_types').html(data);
                }
            });
        } else if (type == 'priority') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_priority', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_priority').html(data);
                }
            });
        } else if (type == 'users') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_members', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_users').html(data);
                }
            });
        } else if (type == 'assignto') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_assignto', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_assignto').html(data);
                }
            });
        } else if (type == 'duedate') {
            $('#dropdown_menu_duedate').html(tmpl("duedate_filter_tmpl"));
            iniDueDateFilter();
        } else if (type == 'utilization_resource') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_utilization_resource', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_utilization_resource').html(data);
                }
            });
        } else if (type == 'utilization_project') {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_utilization_project', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_utilization_project').html(data);
                }
            });
        } else if (type == "utilization_status") {
            $.post(HTTP_ROOT + "easycases/ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": caseMenuFilters, 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneIds': milestoneIds, 'checktype': checktype, 'page_type': 'ajax_utilization_status', "pageload":0}, function (data) {
                if (data) {
                    $('#dropdown_menu_utilization_status').html(data);
        }
            });
    }
}
}
function iniDateFilter() {
    //Custom date range filter ini start
    $("#frm").datepicker({
        dateFormat: 'M d, yy',
        changeMonth: false,
        changeYear: false,
        //minDate: 0,
        hideIfNoPrevNext: true,
        maxDate: "0D",
        onClose: function (selectedDate) {
            $("#to").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#to").datepicker({
        dateFormat: 'M d, yy',
        changeMonth: false,
        changeYear: false,
        //minDate: 0,
        hideIfNoPrevNext: true,
        maxDate: "0D",
        onClose: function (selectedDate) {
            $("#frm").datepicker("option", "maxDate", selectedDate);
        }
    });
    $("#ui-datepicker-div").click(function (e) {
        e.stopPropagation();
    });
    //Custom date range filter ini end
}
function iniDueDateFilter() {
    //Custom duedate range filter ini start
    $("#duefrm").datepicker({
        dateFormat: 'M d, yy',
        changeMonth: false,
        changeYear: false,
        //minDate: 0,
        hideIfNoPrevNext: true,
        //maxDate: "0D",
        onClose: function (selectedDate) {
            $("#dueto").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#dueto").datepicker({
        dateFormat: 'M d, yy',
        changeMonth: false,
        changeYear: false,
        //minDate: 0,
        hideIfNoPrevNext: true,
        //maxDate: "0D",
        onClose: function (selectedDate) {
            $("#duefrm").datepicker("option", "maxDate", selectedDate);
        }
    });
    $("#ui-datepicker-div").click(function (e) {
        e.stopPropagation();
    });
    //Custom duedate range filter ini end
}
//Common Reset Individual Filters 
function common_reset_filter(ftype, id, obj) {
    casePage = 1;
    if ($('.filter_opn').length == 1) {
        $(".tipsy").remove();
        //$('#filter_div_menu').fadeOut('slow');
        $('#filtered_items').fadeOut('slow');
        $('#savereset_filter').fadeOut('slow');
    } else {
        $(obj).parent('div').fadeOut('slow');
        $(".tipsy").remove();
    }
    if (ftype == 'taskstatus') {
        var ext_val = $('#caseStatus').val();
        if (ext_val != 'all') {
            if (ext_val.indexOf('-') != -1) {
                status_str = get_formated_string(ext_val, id);
            } else {
                status_str = 'all'
            }
        }
        $('#caseStatus').val(status_str);
        remember_filters('STATUS', status_str);
    } else if (ftype == 'tasktype') {
        var ext_val = $('#caseTypes').val();
        if (ext_val != 'all') {
            if (ext_val.indexOf('-') != -1) {
                formated_str = get_formated_string(ext_val, id);
            } else {
                formated_str = 'all'
            }
        }
        $('#caseTypes').val(formated_str);
        remember_filters('CS_TYPES', formated_str);
    } else if (ftype == 'priority') {
        var ext_val = $('#priFil').val();
        if (ext_val != 'all') {
            if (ext_val.indexOf('-') != -1) {
                formated_str = get_formated_string(ext_val, id);
            } else {
                formated_str = 'all'
            }
        }
        $('#priFil').val(formated_str);
        remember_filters('PRIORITY', formated_str);
    } else if (ftype == 'members') {
        var ext_val = $('#caseMember').val();
        if (ext_val != 'all') {
            if (ext_val.indexOf('-') != -1) {
                formated_str = get_formated_string(ext_val, id);
            } else {
                formated_str = 'all'
            }
        }
        $('#caseMember').val(formated_str);
        remember_filters('MEMBERS', formated_str);
    } else if (ftype == 'assignto') {
        var ext_val = $('#caseAssignTo').val();
        if (ext_val != 'all') {
            if (ext_val.indexOf('-') != -1) {
                formated_str = get_formated_string(ext_val, id);
            } else {
                formated_str = 'all'
            }
        }
        $('#caseAssignTo').val(formated_str);
        remember_filters('ASSIGNTO', formated_str);
    } else if (ftype == 'date') {
        var x = ''
        $('#caseDateFil').val('');
        remember_filters('DATE', x);
    } else if (ftype == 'duedate') {
        var x = ''
        $('#casedueDateFil').val('');
        remember_filters('DUE_DATE', x);
    } else if (ftype == 'mlstn') {
        $('#milestoneIds').val('all');
        remember_filters('MILESTONES', 'all');
    } else if (ftype == 'search') {
        casePage = 1;
        $('#case_search, #caseSearch').val("");
        $('#case_srch').val("");
        var requiredUrl = document.URL;

        var n = requiredUrl.indexOf("filters=cases");
        if (n != -1) {
            remember_filters('CASESRCH', '');
            remember_filters('SEARCH', '');
            //ajaxCaseView("case_project.php");
            window.location = HTTP_ROOT + "dashboard/";
        } else {
            remember_filters('CASESRCH', '');
            remember_filters('SEARCH', '');
            //ajaxCaseView("case_project.php");
        }
    } else if (ftype == 'casepage') {
        //$('#casePage').val(1);
        casePage = 1;
        //ajaxCaseView("case_project.php");
    } else if (ftype == 'taskorder') {
        remember_filters('TASKSORTBY', '');
        remember_filters('TASKSORTORDER', '');
    } else if (ftype == 'taskgroupby') {
        $('.sortby_btn').removeAttr('disabled');
        $('.sortby_btn').removeClass('disable-btn');
        remember_filters('TASKGROUPBY', '');
    }
    easycase.refreshTaskList();
}
// Dashboard formated Filtered string
function get_formated_string(inputstr, cmpval) {
    var output_string = '';
    var arr = inputstr.split('-');
    var string_len = arr.length;
    for (var i = 0; i < string_len; i++) {
        if (arr[i] != cmpval) {
            output_string += arr[i] + "-";
        }
    }
    return trim(output_string, '-');
}
function openfilter_popup(flag, dropdownid) {
    if (TSG == 1) {
        if ($('#pname_dashboard').text() == 'All') {
            $('#dropdown_menu_all_filters').find('li:nth-child(4)').hide();
        } else {
            $('#dropdown_menu_all_filters').find('li:nth-child(4)').show();
        }
    }
    if ($('#' + dropdownid).is(":visible") && !flag) {
        $('#' + dropdownid).hide();
        $('.dropdown_status').hide();
    } else {
        $('.case-filter-menu ul.dropdown-menu').hide();
        $('.dropdown_status').hide();
        $('#' + dropdownid).show();
    }
}
function statusTop(status) {
    $('#caseStatus').val(status);
    //$('#casePage').val(1);
    casePage = 1;
    if (!$('#reset_btn').is(":visible") || $('#caseStatusprev').val() != status) {
        $('#caseStatusprev').val(status);
        remember_filters('STATUS', status);
        if ($('#caseMenuFilters').val() == 'kanban') {
            easycase.showKanbanTaskList();
        } else {
            ajaxCaseView('case_project.php');
        }
    } else {
        resetAllFilters();
        easycase.refreshTaskList();
    }
}

easycase.routerHideShow = function (page) {
    $('.milestonekb_detail_head').hide();
    if (page == 'details') { //hide task list
        $('.task_section').hide();
        $('.kanban_section').hide();
        $('.calendar_section').hide();
        $('.breadcrumb_div').hide();
        $('.task_detail_head').show();
        $("#caseFileDv").hide();
        $("#widgethideshow").hide();
        $('.dashborad-view-type').hide();
        $('#actvt_section').hide();
        $('#milestone_content').hide();
        //$(".slide_rht_con").css({'padding':"40px 0 35px 43px"});
        $(".slide_rht_con").css({'padding': "20px 0 35px 25px"});
    } else if (page == 'files') {
        $('.task_section').hide();
        $('.kanban_section').hide();
        $('.calendar_section').hide();
        $('.breadcrumb_div').show();
        $('.task_detail_head').hide();
        $("#caseFileDv").show();
        $("#widgethideshow").hide();
        $('.dashborad-view-type').hide();
        $('#actvt_section').hide();
        $('#milestone_content').hide();
        $(".slide_rht_con").css({'padding': "60px 0 35px 25px"});
    } else if (page == 'kanban') {
        $('#caseViewSpan').html('');
        $('#task_paginate').html('');
        $('.task_section').hide();
        $('#filter_section').show();
        if ($('#milestoneUid').val()) {
            $('.case-filter-menu').hide();
            $('#filtered_items').hide();
            $('#savereset_filter').hide();
            $('.breadcrumb_div').hide();
            $('.milestonekb_detail_head').show();
            $(".slide_rht_con").css({'padding': "20px 0 35px 25px"});
        } else {
            $('.case-filter-menu').show();
            $('.breadcrumb_div').show();
            $('#filtered_items').show();
            $('#savereset_filter').show();
            $(".slide_rht_con").css({'padding': "40px 0 35px 25px"});
        }
        $('.kanban_section').show();
        $('.calendar_section').hide();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $('.tasksortby-div').hide();
        $('#sortby_items').hide();
        $('.taskgroupby-div').hide();
        $('#groupby_items').hide();
        $("#widgethideshow").hide();
        //$('.dashborad-view-type').show();
        $('#select_view').show();
        $('#select_view_mlst').hide();
        $('#actvt_section').hide();
        $('#milestonelist').html('');
        $('#milestone_content').hide();
        $('.case-filter-menu').addClass('kanbanview-filter');
        $(".slide_rht_con").css({'padding': "40px 0 35px 25px"});
    } else if (page == 'calendar') {
        $('#milestoneUid').val('');//force fully done this 
        $('#caseViewSpan').html('');
        $('#task_paginate').html('');
        $('.task_section').hide();
        $('#filter_section').hide();
        if ($('#milestoneUid').val()) {
            $('.case-filter-menu').hide();
            $('.breadcrumb_div').hide();
            $('.milestonekb_detail_head').show();
        } else {
            //$('.case-filter-menu').show();
            $('.breadcrumb_div').show();
        }
        $('.calendar_section').show();
        $('.kanban_section').hide();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $("#widgethideshow").hide();
        //$('.dashborad-view-type').show();
        $('#select_view').show();
        $('#select_view_mlst').hide();
        $('#actvt_section').hide();
        $('#milestonelist').html('');
        $('#milestone_content').hide();
    } else if (page == 'activities') {
        //$('#caseViewSpan').html('');
        //$('#task_paginate').html('');
        $('.task_section').hide();
        $('#filter_section').hide();
        $('.case-filter-menu').hide();
        $('.kanban_section').hide();
        $('.calendar_section').hide();
        $('.breadcrumb_div').show();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $("#widgethideshow").hide();
        //$('.dashborad-view-type').show();
        $('#select_view').show();
        $('#select_view_mlst').hide();
        $('#actvt_section').show();
        $('#milestone_content').hide();
        $(".slide_rht_con").css({'padding': "60px 0 35px 25px"});
    } else if (page == 'milestone') {
        $('#caseViewSpan').hide();
        $('#task_paginate').hide('');
        $('.task_section').hide();
        $('#filter_section').hide();
        $('.case-filter-menu').hide();
        $('.kanban_section').html('');
        $('.kanban_section').hide();
        $('.calendar_section').hide();
        $('.breadcrumb_div').show();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $("#widgethideshow").hide();
        //$('.dashborad-view-type').show();
        $('#select_view').hide();
        $('#select_view_mlst').show();
        $('#actvt_section').hide();
        $('#milestone_content').show();
        $('#manage_milestone').show();
        $('#milestonelisting').hide();
        $('#mlist_crt_mlstbtn').hide();
        $(".slide_rht_con").css({'padding': "60px 0 35px 25px"});
    } else if (page == 'milestonelist') {// Milestone Kanban view
        $('#caseViewSpan').html('');
        $('#task_paginate').html('');
        $('.task_section').hide();
        $('#filter_section').hide();
        $('.case-filter-menu').hide();
        $('.kanban_section').html('');
        $('.calendar_section').hide();
        $('.kanban_section').hide();
        $('.breadcrumb_div').show();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $("#widgethideshow").hide();
        //$('.dashborad-view-type').show();
        $('#select_view').hide();
        $('#select_view_mlst').show();
        $('#actvt_section').hide();
        $('#milestone_content').show();
        $('#manage_milestone').hide();
        $('#milestonelisting').show();
        $('#mlist_crt_mlstbtn').show();
        $(".slide_rht_con").css({'padding': "60px 0 35px 25px"});
    } else { //show task list
        $('.kanban_section').hide();
        $('.calendar_section').hide();
        $('.kanban_section').html('');
        $('.task_section').show();
        $('#caseViewSpan').show();
        $('#task_paginate').show('');
        $('.breadcrumb_div').show();
        $('.task_detail_head').hide();
        $("#caseFileDv").hide();
        $("#widgethideshow").show();
        //$('.dashborad-view-type').show();
        $('#select_view').show();
        $('#select_view_mlst').hide();
        $('#actvt_section').hide();
        $('#milestonelist').html('');
        $('#milestone_content').hide();
        $('#milestoneUid').val('');
        if ($('#projFil').val() == 'all') {
            $('#mvTaskToProj').hide();
        } else {
            $('#mvTaskToProj').show();
        }
        $('#select_view div').removeClass('disable');
        if ($('#lviewtype').val() == 'compact') {
            $('.tsk_tbl').addClass('compactview_tbl');
            $('#topaction').addClass('compactview_action');
            $('#cview_btn').addClass('disable');
        } else {
            $('.tsk_tbl').removeClass('compactview_tbl');
            $('#topaction').removeClass('compactview_action');
            $('#lview_btn').addClass('disable');
        }
        $('.tasksortby-div').show();
        $('#sortby_items').show();
        $('.taskgroupby-div').show();
        $('#groupby_items').show();
        $('.case-filter-menu').removeClass('kanbanview-filter');
        $(".slide_rht_con").css({'padding': "60px 0 35px 25px"});
    }
    $('#detail_section .task_detail').hide();
    $(".crt_tsk").hide();
    $(".slide_rht_con").animate({marginLeft: "0px"}, "fast");
    $(".crt_slide").css({display: "none"});
}

/* Code for Files Listing starts */
easycase.showFiles = function (type) {
    crt_popup_close();
    easycase.routerHideShow(type);
    $("#caseMenuFilters").val('files');
    $(".menu-cases").removeClass('active');
    $(".menu-files").addClass('active');
    $(".menu-cases").removeClass('active');
    $(".menu-milestone").removeClass('active');
    $(".menu-files").addClass('active');
    $("#brdcrmb-cse-hdr").html(_('Files'));
    $('#caseMenuFilters').val('files');

    var strURL = HTTP_ROOT + "easycases/";
    //var casePage = $('#casePage').val(); // Pagination
    $('#caseLoader').show();
    var projFil = $('#projFil').val(); // Project Uniq ID
    var projIsChange = $('#projIsChange').val(); // Project Uniq ID
    displayMenuProjects('dashboard', '6', 'files');
    var fileUrl = strURL + "case_files";
    $.post(fileUrl, {"projFil": projFil, "projIsChange": projIsChange, "casePage": casePage, "file_srch": search_key}, function (res) {
        if (res) {
            $('#caseLoader').hide();
            $("#caseFileDv").show();

            var params = parseUrlHash(urlHash);
            if (params[0] != "files") {
                parent.location.hash = "files";
            }

            var result = document.getElementById('caseFileDv');
            result.innerHTML = tmpl("case_files_tmpl", res);
            bindPrettyview("prettyImage");

            scrollPageTop($("#caseFileDv"));
        }
        loadCaseMenu(strURL + "ajax_case_menu", {"projUniq": projFil, "pageload": 0, "page": "dashboard"})
    });
    if (projFil == 'all') {
        remember_filters('ALL_PROJECT', 'all');
    } else {
        remember_filters('ALL_PROJECT', '');
    }
}

var stop_repeated_calls = '';
/* Code for Kanban View starts */
easycase.showKanbanTaskList = function (type, search_key) {
    var params = parseUrlHash(urlHash);
    var milestone_uid = $('#milestoneUid').val();
    if (params[1]) {
        milestone_uid = params[1];
        $('#milestoneUid').val(params[1]);
        if (($('#caseMenuFilters').val() == 'milestone') || ($('#caseMenuFilters').val() == 'milestonelist'))
            $('#refMilestone').val($('#caseMenuFilters').val());
        //window.location.hash = 'kanban';
    }
    $('#select_view div').tipsy({gravity: 'n', fade: true});
    var globalkanbantimeout = null;
    var morecontent = '';
    var newTask_limit = 0;
    var inProgressTask_limit = 0;
    var resolvedTask_limit = 0;
    var closedTask_limit = 0;
	var custom_limit = 0;
	var dat_temp_id = '';
    if (typeof (type) != 'undefined' && type != 'kanban') {
        morecontent = type;
        newTask_limit = $('#newTask_limit').val();
        inProgressTask_limit = $('#inprogressTask_limit').val();
        resolvedTask_limit = $('#resolvedTask_limit').val();
        closedTask_limit = $('#closedTask_limit').val();
		custom_limit = $('#'+type.replace(/\s/g, '')+'_limit').val();
		dat_temp_id = $('#'+type.replace(/\s/g, '')).attr('data-temp-id');
        if (morecontent == 'newTask' && (parseInt($('#cnter_newTask').text()) <= parseInt(newTask_limit))) {
            return;
        } else if (morecontent == 'inprogressTask' && (parseInt($('#cnter_inprogressTask').text()) <= parseInt(inProgressTask_limit))) {
            return;
        } else if (morecontent == 'resolvedTask' && (parseInt($('#cnter_resolvedTask').text()) <= parseInt(resolvedTask_limit))) {
            return;
        } else if (morecontent == 'closedTask' && (parseInt($('#cnter_closedTask').text()) <= parseInt(closedTask_limit))) {
            return;
        }else if((parseInt($('#cnter_'+type.replace(/\s/g,'')).text()) <= parseInt(custom_limit))){
			return;
        }
    } else {
        crt_popup_close();
        $('#select_view div').removeClass('disable');
        $('#kbview_btn').addClass('disable');
        easycase.routerHideShow('kanban');
        $("#caseMenuFilters").val('kanban');
        $(".menu-cases").addClass('active');
        $(".menu-files").removeClass('active');
        $(".menu-milestone").removeClass('active');
        $("#brdcrmb-cse-hdr").html(_('Tasks'));
    }
    var strURL = HTTP_ROOT + "easycases/";
    //var casePage = $('#casePage').val(); // Pagination
    if (morecontent) {
        $('#loader_' + morecontent).show();
    } else {
        $('#caseLoader').show();
    }
    var projFil = $('#projFil').val();
    var projIsChange = $('#projIsChange').val();
//	if(projFil != projIsChange){
//		$('#milestoneUid').val('');
//		$('#milestoneId').val('');
//		milestoneUid ='';milestoneId='';
//	}
    var customfilter = $('#customFIlterId').value;//Change case type
    var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
    var priFil = $('#priFil').val(); // Filter by Priority
    var caseTypes = $('#caseTypes').val(); // Filter by case Types
    var caseMember = $('#caseMember').val();  // Filter by Member
    var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo

    var caseSearch = $("#case_search").val();
    if (caseSearch.trim() == '') {
        caseSearch = $('#caseSearch').val(); // Search by keyword  
    } else {
        $("#caseSearch").val(caseSearch);
    }
    $("#case_search").val("");

    var case_date = $('#caseDateFil').val(); // Search by Date
    var case_due_date = $('#casedueDateFil').val(); // Search by Date
    var case_srch = $('#case_srch').val();
    //displayMenuProjects('dashboard', '6', 'files');
    var caseId = $('#caseId').val(); // Close a case
    var fileUrl = strURL + "kanban_task";
    $.post(fileUrl, {"projFil": projFil, "projIsChange": projIsChange, "casePage": casePage, 'caseStatus': caseStatus, 'customfilter': customfilter, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'case_srch': case_srch, 'case_date': case_date, 'case_due_date': case_due_date, 'morecontent': morecontent, 'newTask_limit': newTask_limit, 'inProgressTask_limit': inProgressTask_limit, 'resolvedTask_limit': resolvedTask_limit, 'closedTask_limit': closedTask_limit, 'milestoneUid': milestone_uid, 'search_key': search_key, 'custom_limit':custom_limit,'dat_temp_id':dat_temp_id}, function (res) {
        if (res) {
            refreshKanbanTask = 0;
            $('#detail_section').html('');
            if (morecontent) {
                $('#loader_' + morecontent).hide();
            } else {
                //if(projFil == projIsChange){
                $('#milestoneId').val(res.mlstId);
                //}
                $('#caseLoader').hide();
            }
            $("#kanban_list").show();
            var params = parseUrlHash(urlHash);
            if (params[0] != "kanban") {
                parent.location.hash = "kanban";
            }
            var result = document.getElementById('kanban_list');
            if (morecontent) {
				var appnd_new = 1;
				if(stop_repeated_calls == ''){
					stop_repeated_calls = morecontent+'_'+custom_limit;
				}else if(stop_repeated_calls == morecontent+'_'+custom_limit){
					appnd_new = 0; 
				}else{
					stop_repeated_calls = morecontent+'_'+custom_limit;
				}
				//var limit = morecontent+'Task_limit';
				if(appnd_new){
                $('#'+morecontent+'_limit').val(res.custom_limit);
                $('#newTask_limit').val(res.newTask_limit);
                $('#inprogressTask_limit').val(res.inprogressTask_limit);
                $('#resolvedTask_limit').val(res.resolvedTask_limit);
                $('#closedTask_limit').val(res.closedTask_limit);
                $('#' + morecontent + '>.kanban_content .kbtask_div:last').after(tmpl("kanban_task_tmpl", res));
				}
            } else {
                result.innerHTML = tmpl("kanban_task_tmpl", res);
                //   scrollPageTop($("#kanban_list"));
            }
            $('.kanban-main').jScrollPane({autoReinitialise: true});
            var settings = {autoReinitialise: true};
            var pane = $(".custom_scroll");
            pane.jScrollPane(settings);
            $(".kanban-child .kbtask_div").live("hover", function (obj) {
                var curindex = $(this).parent().children().index(this);
                if (($(this).is(":last-child") || $(this).is(":nth-last-child(3)") || $(this).is(":nth-last-child(2)")) && (parseInt(curindex) >= 3) && ($(this).parents(".jspPane").height() > 400)) {
                    $(this).find('.dropdown').on('click', function (cobj) {
                        var hite = $(this).find('.dropdown-menu').height();
                        var popup_ht = parseInt(hite) + 12;
                        $(this).find(".dropdown-menu").css({top: "-" + popup_ht + "px"});
                        $(this).find(".pop_arrow_new").css({marginTop: hite + "px", background: "url('" + HTTP_ROOT + "img/arrow_dwn.png') no-repeat"});
                    });
                }
            });
            // Kanban view scroll pagination 
            if (!morecontent) {
				 $('.kanban_content').scroll(function(event) {
                    var loadmorecontent = $(this).parent().attr('id');
					if ($(this).scrollTop() >= 109) {
                        if (globalkanbantimeout != null)
                            clearTimeout(globalkanbantimeout);
                        globalkanbantimeout = setTimeout("easycase.showKanbanTaskList('" + loadmorecontent + "')", 300);
                    }
				});
            }
			
			
			//drag and drop functionality starts //
            $('.kbtask_div').draggable({
                cursor: "grabbing",
                helper: 'clone',
                revert: "invalid",
                zIndex: 100,
                start: function(event, ui) {
                    $(ui.helper).css({
						'width': '18.5%'
					});
				}
            });
            $(".kanban-child").droppable({
                accept: ".kbtask_div",
                drop: function(event, ui) {
                    var task_uniq_id = $(ui.helper).find('div[id^="titlehtml"]').attr('data-task');
                    var ext_sts_id = $(ui.helper).parent().parent().attr('id');
                    var curr_sts_id = $(event.target).attr('id');
                    var old_sts = getStatusNumber(ext_sts_id);
                    var new_sts = getStatusNumber(curr_sts_id);
                    //console.log(curr_sts_id);console.log(ext_sts_id);
                    if(projFil == 'all'){
                        showTopErrSucc('error', "Tasks cannot be moved");
                         $(ui.helper).css({
                            'top': '0px',
                            'left': '0px'
                        });
                    } else if (new_sts == 0) {
                        showTopErrSucc('error', "Please drop into a valid Status");
                        $(ui.helper).css({
                            'top': '0px',
                            'left': '0px'
                        });
                    } else if (old_sts == new_sts) {
                        showTopErrSucc('error', 'Opps! task has already the dropped Status');
                        $(ui.helper).css({
                            'top': '0px',
                            'left': '0px'
                        });
                    } else {
                        $(".kanban-child > .kanban_content").append($(ui.helper));
                        $(ui.helper).css({
                            'width': '25%'
                        });
                        $.post(HTTP_ROOT + 'easycases/ajax_change_legend', {
                            'caseId': '0',
                            'taskuid': task_uniq_id,
                            'legend': new_sts
                        }, function(res) {
                            if(res.errormsg){
                                showTopErrSucc('error', res.errormsg);
                            } else {
                                showTopErrSucc('success', 'Task moved successfully.');
                            }
                            easycase.showKanbanTaskList();
                        }, 'json'); 
                    }
                }
            });                       
            //drag and drop functionality ends//
			
			
			
            // Custome Date range in due date 
            $("div [id^='set_due_date_']").each(function (i) {
                $(this).datepicker({
                    altField: "#CS_due_date",
                    showOn: "button",
                    buttonImage: HTTP_IMAGES + "images/calendar.png",
                    buttonStyle: "background:#FFF;",
                    changeMonth: false,
                    changeYear: false,
                    minDate: 0,
                    hideIfNoPrevNext: true,
                    onSelect: function (dateText, inst) { 
                        var caseId = $(this).parents('.cstm-dt-option').attr('data-csatid');
                        var datelod = "datelod" + caseId;
                        var showUpdDueDate = "showUpdDueDate" + caseId;
                        $("#" + showUpdDueDate).html("");
                        $("#" + datelod).show();
                        //var popupCloseDueDate = "popupCloseDueDate"+caseId;
                        //$('#'+popupCloseDueDate).fadeOut(400);
                        var text = '';
                        $.post(HTTP_ROOT + "easycases/ajax_change_DueDate", {"caseId": caseId, "duedt": dateText, "text": text}, function (data) {
                            if (data) {
                                $("#" + datelod).hide();
                                $("#" + showUpdDueDate).html(data.top + '<span class="due_dt_icn"></span>');
                                if (data.isAssignedUserFree != 1 && GTLG == 1) {
                                    var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                                    openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
                                }
                            }
                        }, 'json');
                    }
                });
            });
            var clearCaseSearch = $('#clearCaseSearch').val();
            $('#clearCaseSearch').val("");
            if (!milestone_uid) {
                resetBreadcrumbFilters(HTTP_ROOT + 'easycases/', caseStatus, priFil, caseTypes, caseMember, caseAssignTo, 0, case_date, case_due_date, casePage, caseSearch, clearCaseSearch, 'kanban', '');
            }
            $('[rel=tooltip]').tipsy({gravity: 's', fade: true});
            if (!morecontent) {
                $.post(strURL + "ajax_case_status", {"projUniq": projFil, "pageload": 0, "caseMenuFilters": 'kanban', 'case_date': case_date, 'case_due_date': case_due_date, 'caseStatus': caseStatus, 'caseTypes': caseTypes, 'priFil': priFil, 'caseMember': caseMember, 'caseAssignTo': caseAssignTo, 'caseSearch': caseSearch, 'milestoneId': $('#milestoneId').val()}, function (data) {
                    if (data) {
                        $('#ajaxCaseStatus').html(data);
                        $('#ajaxCaseStatus').html(tmpl("case_widget_tmpl", data));
                        $("#upperDiv_not").hide();
                        var statusnot = $('#not_sts').html();
                        var n = '';
                        for (var key in data.res) {
                            var id = '';
                            if(projFil != 'all'){
                                if (key.toLowerCase() == "new" && data.res[key].id == 1) {
                                    id = 'newTask';
                                } else if (key.toLowerCase() == 'in progress' && data.res[key].id == 2) {
                                    id = 'inprogressTask';
                                } else if (key.toLowerCase() == 'resolved' && data.res[key].id == 5) {
                                    id = 'resolvedTask';
                                } else if (key.toLowerCase() == 'closed' && data.res[key].id == 3) {
                                    id = 'closedTask';
                                } else {
                                    id = key.replace(/\s/g, '');
                                }
							}else{
								id = key.replace(/\s/g, '');
							}
                            $('#cnter_' + data.res[key].id).html(data.res[key].count);
                        }
                        $('[rel=tooltip]').tipsy({gravity: 's', fade: true});
                        $("#widgethideshow").hide();
                    }
                });
                $('.tasksortby-div').hide();
                $('#sortby_items').hide();
                $('.taskgroupby-div').hide();
                $('#groupby_items').hide();
            }
        }
        if (projIsChange != projFil) {
            loadCaseMenu(strURL + "ajax_case_menu", {"projUniq": projFil, "pageload": 0, "page": "dashboard"})
        }
    });
    if (projFil == 'all') {
        remember_filters('ALL_PROJECT', 'all');
    } else {
        remember_filters('ALL_PROJECT', '');
    }
    subscribeClient();
}
function getStatusNumber(sts) {
    var legend = 0;
    if(typeof WORKFLOW_ID == 'undefined' || WORKFLOW_ID== 0){
        switch (sts) {
            case 'newTask':
                legend = 1;
                break;
            case 'inprogressTask':
                legend = 2;
                break;
            case 'resolvedTask':
                legend = 5;
                break;
            case 'closedTask':
                legend = 3;
                break;
            default:
                legend = 0;
                break;
        }
    } else { 
          for(var ststs in STATUS_LISTS){
              if(trim(ststs.replace(/\s/g, '')) == sts){
                 legend = STATUS_LISTS[ststs];break;
            }
          }
    }
    return legend;
}
function ajaxFilePage(page) {
    //$('#casePage').val(page);
    casePage = page;
    easycase.showFiles('files');
}

function archiveFile(obj) {
    var id = $(obj).attr("data-id");
    var name = $(obj).attr("data-name");
    var conf = confirm(_("Are you sure you want to archive the file '") + name + "' ?");
    if (conf == false) {
        return false;
    } else {
        var curRow = "curRow" + id;
        $("#" + curRow).fadeOut(500);

        var strurl = HTTP_ROOT + "easycases/archive_file";
        $.post(strurl, {"id": id}, function (data) {
            if (data) {
                showTopErrSucc('success', _("File '") + name + "'"+_(" is archived."));
                easycase.showFiles('files');
            }
        });
    }
}

function downloadImage(obj) {
    window.location.href = $(obj).attr('data-url');
}
function downloadTask(csUid, caseNum) {
    var url = HTTP_ROOT + "easycases/taskDownload";
    var left = (screen.width / 2) - (500 / 2);
    var top = (screen.height / 2) - (500 / 2);
    var w = window.open('', 'Download Task #' + caseNum, 'width=500,height=500,top=' + top + ',left=' + left);
    $(w.document.body).html('<div class="loader_dv" style="font-family:\'HelveticaNeue-Roman\',\'HelveticaNeue\',\'Helvetica Neue\',\'Helvetica\',\'Arial\',\'sans-serif\';font-size:17px;"><center><img src="' + HTTP_IMAGES + 'images/case_loader2.gif" alt="Loading..." title="Loading..." /><br>'+_("Please wait we are preparing your download...")+'</center></div>');
    $.post(url, {'caseUid': csUid}, function (res) {
        if (res) {
            $(w.document.body).html(res);
            $(w.document.getElementById('sendemailtouser_btn')).click(function () {
                sendDownloadTaskMail(w);
            });
            $(w.document.getElementById('download_task_link')).click(function () {
                var hrefattr = w.document.getElementById('download_task_link').getAttribute('href');
                //window.location.href=hrefattr;
                w.close();
                window.open(hrefattr, '_blank');
                //alert(hrefattr);
                //setTimeout(function(){w.close(); }, 5000);

            });
        } else {
            alert(_('Error in downloading task'));
        }
    });
}

/* Code for Files Listing ends */

function edited_priority(case_id, obj) {
    $('#CS_priority' + case_id).val($(obj).find('input:radio').val());
    //alert(case_id+'==='+$(obj).find('input:radio').val());
}

function actiononTask(taskid, taskUid, taskNum, actiontype, legend) {
    $.post(HTTP_ROOT + 'easycases/taskactions', {'taskId': taskid, 'taskUid': taskUid, 'type': actiontype, 'legend': legend}, function (res) {
        if (res) {
            if (res.succ) {
                $('#caseId').val("");
                $("#" + taskUid).remove();
                if (actiontype == 'tasktype' || actiontype == 'duedate' || actiontype == 'priority' || actiontype == 'assignto' || actiontype == 'esthour' || actiontype == 'cmpltsk') {
                    showTopErrSucc('success', _('Task #') + taskNum +_(' updated.'));
                } else {
                    if (actiontype.toLowerCase() == 'close') {
                        actiontype = 'clos'
                    }
                    if (actiontype.toLowerCase() == 'resolve') {
                        actiontype = 'resolv';
                    }
                    showTopErrSucc('success', _('Task #') + taskNum + ' ' + actiontype + 'ed.');
                }
                $.post(HTTP_ROOT + "easycases/ajaxemail", {'json_data': res.data, 'type': 1});
            } else if (res.err) {
                if (res.msg) {
                    showTopErrSucc('error', res.msg);
                    $('#caseId').val(''); // Close a case
                    $('#caseStart').val(''); // Start a case
                    $('#caseResolve').val(''); // Resolve a case
                    return false;
                } else {
                    showTopErrSucc('error', _('Error in task ') + actiontype);
            }
            }
            var hashtag = parseUrlHash(urlHash);
            if (hashtag[0] == 'details') {
                easycase.refreshTaskList(taskUid);
            } else if ($('#caseMenuFilters').val() == 'milestonelist') {
                showMilestoneList();
            } else {
                easycase.showKanbanTaskList('kanban');
            }
        }
    }, 'json');
}
// Move Task To Milestone 
function moveTask(taskid, taskno, mlstid, project_id) {
    openPopup();
    $(".movetaskTomlst").show();
    $('#mvtask_mlst').html('');
    $('.add-mlstn-btn').hide();
    $('#tsksrch').hide();
    $(".popup_bg").css({"width": '850px'});
    $(".popup_form").css({"margin-top": "6px"});
    $("#mvtask_loader").show();
    $("#mvtask_movebtn").css({'cursor': 'default'});
    $.post(HTTP_ROOT + "milestones/moveTaskMilestone", {'taskid': taskid, 'project_id': project_id, 'mlstid': mlstid, 'task_no': taskno}, function (res) {
        if (res) {
            $("#mvtask_loader").hide();
            $('#mvtask_mlst').show();
            $('.add-mlstn-btn').show();
            $('#tskloader').hide();
            $('#tsksrch').show();
            $('#mvtask_mlst').html(res);
            $("#mvtask_prj_ttl").html($("#mvtask_proj_name").val());
            if ($('#mvtask_cnt').val() == 0) {
                $("#mvtask_movebtn").attr({'disabled': 'disabled'});
            }
        }
    });
}
function removeTask(taskid, taskno, mlstid, project_id) {
    var conf = confirm(_("Are you sure you want to remove #") + taskno +_(" task from it's corresponding milestone"));
    if (conf) {
        $.post(HTTP_ROOT + "milestones/removeTaskMilestone", {'taskid': taskid, 'project_id': project_id, 'mlstid': mlstid, 'task_no': taskno}, function (res) {
            if (res) {
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    refreshMilestone = 1;
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    refreshKanbanTask = 1;
                    easycase.showKanbanTaskList();
                } else if ($('#caseMenuFilters').val() == '') {
                    refreshTasks = 1;
                    easycase.refreshTaskList();
                }
            }
        });
    }
}
function switchTaskToMilestone(obj) {
    if ($('#mvtask_cnt').val() > 0) {
        var curr_mlst_id = '';
        var ext_mlst_id = $('#ext_mlst_id').val();
        var task_id = $('#mvtask_id').val();
        var task_no = $('#mvtask_task_no').val();
        var project_id = $('#mvtask_project_id').val();
        $('.radio_cur').each(function (i) {
            if ($(this).is(':checked')) {
                curr_mlst_id = $(this).val();
            }
        });
        if (ext_mlst_id == curr_mlst_id) {
            showTopErrSucc('error', _('Opps! #') + task_no +_(" is already in selected Milestone"));
        } else {
            if (confirm(_('Are you sure you want to move  #') + task_no +_(" Task to the selected milestone?"))) {
                $.post(HTTP_ROOT + 'milestones/switchTaskToMilestone', {'taskid': task_id, 'curr_mlst_id': curr_mlst_id, 'project_id': project_id, 'ext_mlst_id': ext_mlst_id}, function (res) {
                    if (res == 'success') {
                        showTopErrSucc('success', _('Task #') + task_no +_(" moved successfully."));
                    } else {
                        showTopErrSucc('error', _('Error in moving task to milestone'));
                    }
                });
                closePopup();
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    refreshMilestone = 1;
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    refreshKanbanTask = 1;
                    easycase.showKanbanTaskList();
                } else if ($('#caseMenuFilters').val() == '') {
                    refreshTasks = 1;
                    easycase.refreshTaskList();
                }
            } else {
                return false;
            }
        }
    } else {
        showTopErrSucc('error', _('Opps! There is no milestone to move the task.'));
    }
}
function editmessage(obj, id, projid) {
    $('#editpopup' + id + ' .icon-edit').addClass('loading');
    $.post(HTTP_ROOT + "easycases/edit_reply", {'id': id, 'reply_flag': 1, projid: projid}, function (res) {
        $('#casereplytxt_id_' + id).hide();
        $('#editpopup' + id + ' .icon-edit').removeClass('loading');
        $('#casereplyid_' + id).html(res);
        var tiny_mce_url = HTTP_ROOT + 'js/tinymce/tiny_mce.js';
        $('#edit_reply_txtbox' + id).tinymce({
            // Location of TinyMCE script
            theme: "advanced",
            plugins: "paste",
            theme_advanced_buttons1: "bold,italic,strikethrough,underline,|,numlist,bullist,|,indent,outdent",
            theme_advanced_resizing: false,
            theme_advanced_statusbar_location: "",
            paste_text_sticky: true,
            gecko_spellcheck: true,
            paste_text_sticky_default: true,
            forced_root_block: false,
            cleanup_on_startup: true,
            width: "100%",
            height: "150px",
            oninit: function () {
                $('#edit_reply_txtbox' + id).tinymce().focus();
            }
        });
    });
}
function save_editedvalue_reply(caseno, id, proj_id, repUniqId) {
    var message = $('#edit_reply_txtbox' + id).val();
    $('#edit_btn' + id).hide();
    $('#edit_loader' + id).show();
    $.post(HTTP_ROOT + "easycases/save_editedvalue", {'id': id, 'message': message, 'caseno': caseno, proj_id: proj_id}, function (res) {
        if (res == 0) {
            showTopErrSucc('error', _("Message cann't be left blank"));
            $('#edit_btn' + id).show();
            $('#edit_loader' + id).hide();
        } else {
            $('#casereplytxt_id_' + id).show();
            $('#replytext_content' + id).html(message);
            $('#casereplyid_' + id).html('');
            /*var msg = updated_subcap;
             $('#post_upd_txt'+id).html(msg);*/
            showTopErrSucc('success', _('Your reply edited successfully.'));
            easycase.refreshTaskList(repUniqId);
        }
    });
}
function cancel_editor_reply(id) {
    $('#casereplytxt_id_' + id).show();
    $('#casereplyid_' + id).html('');
}
// Group by Task list with respect to the selected value
function groupby(gbtype) {
    if (gbtype == 'date') {
        $('.sortby_btn').removeAttr('disabled');
        $('.sortby_btn').removeClass('disable-btn');
    } else {
        $('.sortby_btn').prop('disabled', true);
        $('.sortby_btn').addClass('disable-btn');
    }
    remember_filters('TASKGROUPBY', gbtype);
    easycase.refreshTaskList();
}
function removeFileFrmFiles(file_id) {
    var url = HTTP_ROOT + "archives/file_remove";
    if (confirm(_("Are you sure you want to remove?")))
    {
        var val = new Array();
        val.push(file_id);
        var name = $('#file_remove_' + file_id).attr("data-name");
        $.post(url, {"val": val}, function (data) {
            showTopErrSucc('success', _("File '") + name +"'"+_(" is removed."));
            easycase.showFiles('files');
        });
    }
}
function calenderForTimeLog(page) {
    $('.bcrumbtimelog').hide();
    $('#timelogtbl').addClass('temp-class');
    $('.timelog-detail-tbl').hide();
    /*$('#'+page+'_view').show().css({
        'margin-top': '0px'
    });*/
    $('#filter_text').html("<b>"+_("for all users and all dates")+"</b>");
    $('#btn-reset-timelog').css('display', 'none');
    $('.show_total_case').css('display', 'none');
    $('#filter_section').hide();
    if ($('#milestoneUid').val()) {
        $('.case-filter-menu').hide();
        $('.breadcrumb_div').hide();
        $('.milestonekb_detail_head').show();
    } else {
        $('.breadcrumb_div').show();
    }
    $('.calendar_section').show();
    $('.kanban_section').hide();
    $('.task_detail_head').hide();
    $("#caseFileDv").hide();
    $("#widgethideshow").hide();
    $('#select_view').show();
    $('#select_view_mlst').hide();
    $('#actvt_section').hide();
    $('#milestone_content').hide();
}
function changecselegend(caseId, legend) {
    $.post(HTTP_ROOT + "easycases/ajax_change_legend", {"caseId": caseId, "legend": legend},
    function (data) {
        if (data.caseuid != '0') {
            showTopErrSucc('success', _("Status successfully changed to ") + data.name + ".");
            easycase.refreshTaskList(data.caseuid);
        }
        else {
            if(data.errormsg){
                showTopErrSucc('error', data.errormsg);
            }else{
                showTopErrSucc('error', _('Failed to change status.'));
            }
        }
    }, 'json');
}
function showDependents(id, uniqid, depends) {
    $("#task_dependent_block_" + uniqid).find('.showDependents').toggleClass('open ');
    if ($("#task_dependent_" + uniqid).find('.loader').length > 0) {
        var params = {id: id, uid: uniqid, deps: depends, mode: 'child'};
        $.ajax({url: HTTP_ROOT + "easycases/dependent_overview", dataType: "json", type: "POST", data: params, success: function (res) {
                $("#task_dependent_" + uniqid).find('li').remove();
                if (res.length > 0) {
                    $.each(res, function (key, val) {
                        var li = $('<li>').html($('<a>').attr({'class': 'case-title', 'data-task': val.Easycase.uniq_id, 'id': 'titlehtmlt' + val.Easycase.id, 'title': val.Easycase.title}).html(val.Easycase.case_no + "# " + val.Easycase.title));
                        $("#task_dependent_" + uniqid).append(li);
                    });
                    msg = _("Task can't start. Waiting on these task to be completed.");

                } else {
                    var li = $('<li>').html($('<span>').attr({'title': _('No Task Found')}).html(_('No Task Found')));
                    $("#task_dependent_" + uniqid).append(li);
                    msg = _("All tasks has been closed.");
                }
                $("#task_dependent_" + uniqid).parent().closest('ul').find('.task_dependent_msg').html(msg);
            }});
    }
}
function showParents(id, uniqid, depends) {
    $("#task_parent_block_" + uniqid).find('.showParents').toggleClass('open ');
    if ($("#task_parent_" + uniqid).find('.loader').length > 0) {
        var params = {id: id, uid: uniqid, deps: depends, mode: 'parent'};
        $.ajax({url: HTTP_ROOT + "easycases/dependent_overview", dataType: "json", type: "POST", data: params, success: function (res) {
                $("#task_parent_" + uniqid).find('li').remove();
                if (res.length > 0) {
                    $.each(res, function (key, val) {
                        var li = $('<li>').html($('<a>').attr({'class': 'case-title', 'data-task': val.Easycase.uniq_id, 'id': 'titlehtmlt' + val.Easycase.id, 'title': val.Easycase.title}).html(val.Easycase.case_no + "# " + val.Easycase.title));
                        $("#task_parent_" + uniqid).append(li);
                    });
                    msg = _("These tasks are waiting on this task.");
                } else {
                    var li = $('<li>').html($('<span>').attr({'title': _('No Task Found')}).html(_('No Task Found')));
                    $("#task_parent_" + uniqid).append(li);
                    msg = _("All tasks has been closed.");
                }
                $("#task_parent_" + uniqid).parent().closest('ul').find('.task_parent_msg').html(msg);
            }});
    }
}