//Closing popup by pressing escape key.
$(document).keydown(function (evt) {
    if (evt.keyCode == 27) {
        closePopup();
    }
});
$(document).ready(function () {
    //Assign & Remove Project check box events on manage project section
    checkboxCheckUncheck('#checkAllprojects', '.removePrjFromuser', '.tr_all', 'tr_active');
    checkboxCheckUncheck('#checkAllAddPrj', '.AddPrjToUser', '.tr_all', 'tr_active');
    $('body').click(function () {
        allowChromeDskNotify();
    });
    $(document).on('keyup', '.check_minute_range', function (e) {
        var inpt = $(this).val().trim();
        var char_restirict = /^[0-9\.\:]+$/.test(inpt);
        if (!char_restirict) {
            $(this).val(inpt.substr(0, inpt.length - 1));
        }
        var t_inpt = inpt.split(":");
        var d_inpt = inpt.split(".");
        var len = t_inpt.length - 1;
        var d_len = d_inpt.length - 1;
        if (len >= 2 || d_len >= 2 || (len & d_len)) {
            $(this).val(inpt.substr(0, inpt.length - 1));
            showTopErrSucc('error', _("Invalid time"));
        } else {
            if (len > 0 || d_len > 0) {
                var c_ln = 0;
                var d_ln = 0;
                if (inpt.indexOf(":") != -1) {
                    var sec_part = inpt.substr(inpt.indexOf(":") + 1);
                    c_ln = sec_part.length;
                }
                if (inpt.indexOf(".") != -1) {
                    var dsec_part = inpt.substr(inpt.indexOf(".") + 1);
                    d_ln = dsec_part.length;
                }
                if (c_ln > 2 || d_ln > 2) {
                    $(this).val(inpt.substr(0, inpt.length - 1));
                    showTopErrSucc('error', _("Minute can not exceed 2 digit"));
                }
            }
        }
    });
});

var usersListData = 0; //Require to implement the Lazy Load functionality for the users section
var curr_location;
var urlHash;
var scrollToRep;
var ioMsgClicked = 0;
var _checkUrlInterval;
var _filterInterval;
var _searchInterval;
var refreshTasks = 0;
var refreshKanbanTask = 0;
var refreshMilestone = 0;
var refreshManageMilestone = 0;
var refreshActvt = 0;
var caseListData = 0; //Require to implement the Lazy Load functionality for the Archive Task List section
var fileListData = 0; //Require to implement the Lazy Load functionality for the Archive File List section
var tinyPrevContent = '';
var search_key = '';
var casePage = 1;
var widget = '';

var gDueDate = 1;

$(function () {
    if (CONTROLLER == 'users' && PAGE_NAME == 'manage') {
        usersListData = 1;
    } else {
        usersListData = 0;
    }

    _checkUrlInterval = setInterval(checkUrl, 50);
    setInterval(trackLogin, 900000);
});

/**************** OnLoad Events **************/
function trackLogin() {
    $.post(HTTP_ROOT + "users/session_maintain", {}, function (data) {
        if (data) {
            if (data == 1) {
                window.top.location = HTTP_ROOT + "users/login";
            }
        }
    });
}

function getHash(window) {
    var match = (window || this).location.href.match(/#(.*)$/);
    return match ? match[1] : '';
}
function checkUrl() {
    if (curr_location === (window || this).location.href) {
        return false;
    } else {
        curr_location = (window || this).location.href;
        urlHash = getHash();
        //alert(urlHash);
        routeOSHash();
    }
}

function parseUrlHash(hash) {
    var urlVars = {};
    var params = (hash.substr(0)).split("/");
    return params;
    /*for (i = 0; i < params.length; i++) {
     var a = params[i].split("=");
     urlVars[a[0]] = a[1];
     }
     return urlVars;*/
}

/* Routing on Task & Files Menu */
function checkHashLoad(type) {
    //    alert(parseUrlHash(urlHash));
    //    alert(type);
    // Add for switching between compact view and List view
    if (type == "compactTask") {
        $('#lviewtype').val('compact');
        remember_filters('LISTVIEW_TYPE', 'compact');
    } else if (type == 'tasks') {
        $('#lviewtype').val('comfort');
        remember_filters('LISTVIEW_TYPE', 'comfort');
    } else if (type == "logList") {
        fetchLogLists();
    }
    if ($("#caseLoader").is(':visible') == false) {
        var hashtag = parseUrlHash(urlHash);
        search_key = '';
        if (type == "files" && hashtag == "files") {
            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                $("#widgethideshow").hide();
                easycase.showFiles(hashtag);
            }
        } else if (type == "files") {
            //$('#casePage').val("1");
            casePage = 1;
        }
        if (type == 'task' || type == 'kanban' || type == 'activities' || type == 'milestone' || hashtag == 'task' || hashtag == 'milestone' || hashtag == 'kanban' || hashtag == 'activities') {
            if (widget) {
                widget.insertBefore('.slide_rht_con');
            }
            if (type) {
                $('#prvhash').val(type);
            } else {
                $('#prvhash').val(hashtag);
            }
        }
        if (type == "kanban" && hashtag == "kanban") {
            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                $("#widgethideshow").show();
                easycase.showKanbanTaskList(hashtag);
            }
        } else if (type == "kanban") {
            //$('#casePage').val("1");
            casePage = 1;
        }
        if (type == "milestone") {
            widget = $("#widgethideshow").detach();
            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                ManageMilestoneList();
            } else {
                ManageMilestoneList();
            }
        } else if (type == "kanban") {
            //$('#casePage').val("1");
            casePage = 1;
        }
        if ((type == "tasks" || type == "compactTask") && hashtag == "tasks") {
            $('#ajaxViewProjects').html('');
            $('#ajaxViewProjects').hide();
            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                //refreshTasks = 1;
                $('.case-filter-menu').hide();
                $("#widgethideshow").show();
                easycase.showTaskLists(hashtag);
            }
        } else if (type == "tasks") {
            //$('#casePage').val("1");
            casePage = 1;
        }

        if (type == "activities" && hashtag == "activities") {
            refreshActvt = 1;
            easycase.showActivities();
        }
        if (type == 'kanbanmilestone' && hashtag == 'kanbanmilestone') {
            routeHideshowMilestone('kanbanmilestone');
            showMilestoneList();
        }
    }
}
/* End */

function routeOSHash() {
    $('#show_search_kanban').html('');
    $('#resetting_kanban').html('');
    $('#kanban_list').css('margin-top', '');
    if (CONTROLLER == 'projects' && PAGE_NAME == 'manage') {
        $("#case_search").attr("placeholder", _("Search Projects"));
        $('.customFilter').html('');
        addUserToProject();
    } else if (CONTROLLER == 'users' && PAGE_NAME == 'manage') {
        $("#case_search").attr("placeholder", "Search Users");
        $("img.lazy").lazyload({
            placeholder: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
        });
        $('.customFilter').html('');
        addProjectToUser();
    } else {
        var params = parseUrlHash(urlHash);
        if (params != '') {
            /*bottom project status with hour update*/
            footer_update();
        }
        //Need to check
        $('#filter_section').show();
        if (params[0] != 'details') {
            $('#widgethideshow').show();
        }
        switch (params[0]) {
            case 'tasks':
                $('.milestonenextprev').hide();
                $('#manage_milestonelist').css('display', 'none');
                $('#select_view').show();
                $('#select_view_mlst').hide();
                $('#caseMenuFilters').val('');
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    if ($("#caseLoader").is(':visible') == false) {
                        $("#case_search").attr("placeholder", _("Search Tasks"));
                        $("#brdcrmb-cse-hdr").html(_('Tasks'));
                        if (params[1]) {
                            setCustomStatus(params[0], params[1]);
                            document.getElementById('customFIlterId').value = params[1];
                            refreshTasks = 1;
                        } else {
                            document.getElementById('customFIlterId').value = '';
                            $('.case-filter-menu').hide();
                            $('.kanban_section').html('');
                            $("#widgethideshow").show();
                            easycase.showTaskLists(params[0]);
                        }
                    }
                }
                break;
            case 'files':
                //ajaxFileView('case_files');
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $("#case_search").attr("placeholder", _("Search Files"));
                    $('.customFilter').html('');
                    if ($("#caseLoader").is(':visible') == false) {
                        $("#widgethideshow").hide();
                        easycase.showFiles(params[0]);
                    }
                }
                break;
            case 'kanban':
                $('#select_view').show();
                $('#select_view_mlst').hide();
                $("#case_search").attr("placeholder", _("Search Tasks"));
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('.customFilter').html('');
                    if ($("#caseLoader").is(':visible') == false) {
                        if (($('#kanban_list').html() && refreshKanbanTask == 0 && !params[1])) {
                            easycase.routerHideShow('kanban');
                            scrollPageTop();
                            $('.custom_scroll').jScrollPane();
                            var settings = {
                                autoReinitialise: true
                            };
                            var pane = $(".custom_scroll");
                            pane.jScrollPane(settings);

                            $('#select_view div').removeClass('disable');
                            $('#kbview_btn').addClass('disable');
                        } else {
                            $("#widgethideshow").show();
                            easycase.showKanbanTaskList(params[0]);
                        }
                    }
                }
                break;
            case 'milestone':

                $(".side-nav li").removeClass('active');
                $(".menu-milestone").addClass('active');
                if ($('#caseMenuFilters').val() == 'files') {
                    displayMenuProjects('dashboard', '6', '');
                }
                $('#caseMenuFilters').val('milestone');
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('.customFilter').html('');
                    $('#select_view').hide();
                    $('#select_view_mlst').show();
                    if ($("#caseLoader").is(':visible') == false) {
                        $("#case_search").attr("placeholder", _("Search Milestones"));
                        if ($('#manage_milestone_list').html() && refreshManageMilestone == 0) {
                            easycase.routerHideShow('milestone');
                            scrollPageTop();
                            $('#select_view_mlst div').removeClass('disable');
                            $('#mlview_btn').addClass('disable');
                        } else {
                            $('#milestoneLimit').val('0');
                            $('#totalMlstCnt').val('0');
                            $("#widgethideshow").show();
                            ManageMilestoneList();
                        }
                    }
                    $('#filter_section').hide();
                }
                break;
            case 'milestonelist':
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('.customFilter').html('');
                    $('#select_view').hide();
                    $('#select_view_mlst').show();
                    $(".side-nav li").removeClass('active');
                    $(".menu-milestone").addClass('active');
                    $('#widgethideshow').hide();
                    $('#all_prjct').hide();
                    if ($("#caseLoader").is(':visible') == false) {
                        $("#case_search").attr("placeholder", _("Search Milestones"));
                        if ($('#milestonelist').html() && refreshMilestone == 0) {
                            easycase.routerHideShow('milestonelist');
                            scrollPageTop();
                            $('.custom_scroll').jScrollPane();
                            var settings = {
                                autoReinitialise: true
                            };
                            var pane = $(".custom_scroll");
                            pane.jScrollPane(settings);
                            $('#select_view_mlst div').removeClass('disable');
                            $('#mkbview_btn').addClass('disable');
                            showMilestoneList('', 1);
                        } else {
                            $('#milestoneLimit').val('0');
                            $('#totalMlstCnt').val('0');
                            $("#widgethideshow").show();
                            showMilestoneList();
                        }
                    }
                }
                break;
            case 'activities':
                $('#select_view').show();
                $('#select_view_mlst').hide();
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('.customFilter').html('');
                    if ($("#caseLoader").is(':visible') == false) {
                        easycase.showActivities();
                    }
                }
                break;
            case 'details':
                if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('.customFilter').html('');
                    easycase.showTaskDetail(params);
                }
                break;
            case 'caselist':
                if (CONTROLLER == 'archives' && PAGE_NAME == 'listall') {
                    $('.customFilter').html('');
                    caseListData = 1;
                    fileListData = 0;
                    changeArcCaseList('');
                }
                break;
            case 'filelist':
                if (CONTROLLER == 'archives' && PAGE_NAME == 'listall') {
                    $('.customFilter').html('');
                    caseListData = 0;
                    fileListData = 1;
                    changeArcFileList('');
                }
                break;
            case 'calendar':
                if (CONTROLLER == 'LogTimes' && PAGE_NAME == 'time_log') {
                    getCalenderForTimeLog('hash');
                } else {
                    calendarView('hash');
                }
                break;
            case 'chart':
                if (CONTROLLER == 'LogTimes' && PAGE_NAME == 'time_log') {
                    getChartForTimeLog('hash');
                }
                break;
            default:
                if (CONTROLLER == 'archives' && PAGE_NAME == 'listall') {
                    $('.customFilter').html('');
                    caseListData = 1;
                    fileListData = 0;
                    changeArcCaseList('');
                } else if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                    $('#select_view').show();
                    $('#select_view_mlst').hide();
                    $('.customFilter').html('');
                    easycase.showTaskLists('tasks');
                }
        }
    }
}

function closePopup() {
    if ($('#pagename').val() == 'profile') {
        $('#profilephoto').imgAreaSelect({
            hide: true
        });
        //$('#up_files1').html('');
        $('#up_files_usr').html('');
    }
    $(".popup_overlay").css({
        display: "none"
    });
    $(".popup_bg").css({
        display: "none"
    });
    $(".sml_popup_bg").css({
        display: "none"
    });
    $(".cmn_popup").hide();
}

function closePopupEdit() {
    $(".popup_overlay").css({
        display: "block"
    });
    $(".popup_bg").css({
        display: "block",
        "width": '850px'
    });
    $(".popup_form").css({
        "margin-top": "-5px"
    });
    $(".loader_dv").hide();
    $(".remove_from_task").show();
    $(".task_project_edit").hide();
}
function openPopup(role) {
    $('#popup_bg_main').removeClass('popup_bg_main');
    $(".popup_overlay").css({
        display: "block"
    });
    if (role == 'log') {
        $(".popup_bg").css({
            display: "block",
            "width": '790px'
        });
    } else {
        $(".popup_bg").css({
            display: "block",
            "width": '546px'
        });
    }
    /*   $(".popup_form").css({
     "margin-top": "20px"
     }); */
    $(".loader_dv").show();
    scrolltotop();
}

$(".more_in_menu").click(function ()
{
    var textData = $(".more_in_menu").html().trim();
    if (textData == "More") {
        $(this).next().removeClass("open_analytics_archive");
        $(this).next().addClass("menu_more_arr");
    } else {
        if ($(this).parent('li').hasClass("close")) {
            $(this).parent('li').removeClass("close")
        }
    }
});
function ReportMenu(uniq) {
    var url = HTTP_ROOT;
    createCookie('timelogsort', '', -365, '');
    createCookie('logstrtdt', '', -365, '');
    createCookie('logenddt', '', -365, '');
    createCookie('rsrclog', '', -365, '');
    if (!uniq) {
        window.location = url + 'task-report/';
    } else {
        $('#main_con_task').load(url + 'reports/chart/ajax/' + uniq, function (res) {
            $('#pname_dashboard').html($('#pjname').val());
            $('#projpopup').hide();
        });
    }
    footer_update(uniq);
}

function hoursreport(uniq) {
    var url = HTTP_ROOT;
    createCookie('timelogsort', '', -365, '');
    createCookie('logstrtdt', '', -365, '');
    createCookie('logenddt', '', -365, '');
    createCookie('rsrclog', '', -365, '');
    if (!uniq) {
        window.location = url + 'hours-report/';
    } else {
        $('#main_con_hours').load(url + 'reports/hours_report/ajax/' + uniq, function (res) {
            /*bottom project status with hour update*/
            $('#pname_dashboard').html($('#pjname').val());
            $('#projpopup').hide();
        });
    }
    footer_update(uniq);
}

function ReportGlideMenu(uniq) {
    var url = HTTP_ROOT;
    if (!uniq) {
        window.location = url + 'bug-report/';
    } else {
        $('#main_con').load(url + 'reports/glide_chart/ajax/' + uniq, function (res) {
            $('#pname_dashboard').html($('#pjname').val());
            $('#projpopup').hide();
        });
    }
}

function validatechart(type) {
    document.getElementById('apply_btn').style.display = 'none';
    document.getElementById('apply_loader').style.display = 'block';
    var start_date = document.getElementById('start_date');
    var end_date = document.getElementById('end_date');
    var errMsg;
    var done = 1;
    if (start_date.value.trim() == "") {
        errMsg = _("From Date cannot be left blank!");
        start_date.focus();
        done = 0;
    } else if (end_date.value.trim() == "") {
        errMsg = _("To Date cannot be left blank!");
        end_date.focus();
        done = 0;
    } else if (Date.parse(start_date.value) > Date.parse(end_date.value)) {
        errMsg = _("From Date cannot exceed To Date!");
        end_date.focus();
        done = 0;
    }
    if (done == 0) {
        var op = 100;
        showTopErrSucc('error', errMsg);
        document.getElementById('apply_btn').style.display = 'block';
        document.getElementById('apply_loader').style.display = 'none';
        return false;
    } else {
        var pjid = $('#pjid').val();
        var sdate = $('#start_date').val();
        var edate = $('#end_date').val();
        if (type == 'bug') {

            $('#piechart_container').load(HTTP_ROOT + 'reports/bug_pichart', {
                'type_id': 1,
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate,
                'dtsearch': 1
            }, function (res) {
                if (res.length > 150) {
                    $('#piechart_container').parent(".col-lg-6").addClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#piechart_container').parent(".col-lg-6").removeClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").addClass('error_box');
                }
            });

            $('#statistic_container').load(HTTP_ROOT + 'reports/bug_statistics', {
                'type_id': 1,
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#statistic_container').parent(".col-lg-6").addClass('m-con');
                    $('#statistic_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#statistic_container').parent(".col-lg-6").removeClass('m-con');
                    $('#statistic_container').parent(".col-lg-6").addClass('error_box');
                }
            });

            $('#linechart_container').load(HTTP_ROOT + 'reports/bug_linechart', {
                'type_id': 1,
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#linechart_container').parent(".col-lg-12").addClass('con-100');
                    $('#linechart_container').parent(".col-lg-2").removeClass('error_box_main');
                } else {
                    $('#linechart_container').parent(".col-lg-2").removeClass('con-100');
                    $('#linechart_container').parent(".col-lg-12").addClass('error_box_main');
                }
            });

            $('#glide_container').load(HTTP_ROOT + 'reports/bug_glide', {
                'type_id': 1,
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#glide_container').parent(".col-lg-12").addClass('con-100');
                    $('#glide_container').parent(".col-lg-12").removeClass('error_box_main');
                } else {
                    $('#glide_container').parent(".col-lg-12").removeClass('con-100');
                    $('#glide_container').parent(".col-lg-12").addClass('error_box_main');
                }
                document.getElementById('apply_btn').style.display = 'block';
                document.getElementById('apply_loader').style.display = 'none';
            });
        } else if (type == "hours") {

            $('#piechart_container').load(HTTP_ROOT + 'reports/hours_piechart', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate,
                'dtsearch': 1
            }, function (res) {
                if (res.length > 150) {
                    $('#piechart_container').parent(".col-lg-6").addClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#piechart_container').parent(".col-lg-6").removeClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").addClass('error_box');
                }
            });
            $('#linechart_container').load(HTTP_ROOT + 'reports/hours_linechart', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#linechart_container').parent(".col-lg-6").addClass('m-con');
                    $('#linechart_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#linechart_container').parent(".col-lg-6").removeClass('m-con');
                    $('#linechart_container').parent(".col-lg-6").addClass('error_box');
                }
            });
            $('#grid_container').load(HTTP_ROOT + 'reports/hours_gridview', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if ($('#thrs').length > 0) {
                    $('#hrspent').html("<b>" + $('#thrs').val() + "</b>");
                } else {
                    $('#hrspent').html("");
                }

                if (res.length > 150) {
                    $('#grid_container').parent(".col-lg-6").addClass('m-con');
                    $('#grid_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#grid_container').parent(".col-lg-6").removeClass('m-con');
                    $('#grid_container').parent(".col-lg-6").addClass('error_box');
                }
                document.getElementById('apply_btn').style.display = 'block';
                document.getElementById('apply_loader').style.display = 'none';
            });
        } else if (type == "task") {
            $('#piechart_container').load(HTTP_ROOT + 'reports/tasks_pichart', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#piechart_container').parent(".col-lg-6").addClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#piechart_container').parent(".col-lg-6").removeClass('m-con');
                    $('#piechart_container').parent(".col-lg-6").addClass('error_box');
                }
            });

            $('#statistic_container').load(HTTP_ROOT + 'reports/tasks_statistics', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#statistic_container').parent(".col-lg-6").addClass('m-con');
                    $('#statistic_container').parent(".col-lg-6").removeClass('error_box');
                } else {
                    $('#statistic_container').parent(".col-lg-6").removeClass('m-con');
                    $('#statistic_container').parent(".col-lg-6").addClass('error_box');
                }
            });

            $('#container').load(HTTP_ROOT + 'reports/tasks_trend', {
                'pjid': pjid,
                'sdate': sdate,
                'edate': edate
            }, function (res) {
                if (res.length > 150) {
                    $('#container').parent(".col-lg-12").addClass('con-100');
                    $('#container').parent(".col-lg-12").removeClass('error_box_main');
                } else {
                    $('#container').parent(".col-lg-12").removeClass('con-100');
                    $('#container').parent(".col-lg-12").addClass('error_box_main');
                }
                document.getElementById('apply_btn').style.display = 'block';
                document.getElementById('apply_loader').style.display = 'none';
            });
        }
    }
}
function scrolltotop() {
    /* $('html, body').animate({
     scrollTop: $(".popup_bg").offset().top - 200
     }, 1000); */
}
function numeric_decimal_colon(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    //console.log(unicode);
    if (unicode != 8)
    {
        if (unicode < 9 || unicode > 9 && unicode < 46 || unicode > 57 || unicode == 47 || unicode == 186 || unicode == 58) {
            if (unicode == 37 || unicode == 38 || unicode == 186 || unicode == 58) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else
    {
        return true;
    }
}
var time;
function showTopErrSucc(type, msg) {
    $("#topmostdiv").show();
    $("#upperDiv").show();
    if (type == 'error') {
        $("#upperDiv").find(".msg_span").removeClass('success');
    } else {
        $("#upperDiv").find(".msg_span").removeClass('error');
    }
    $("#upperDiv").find(".msg_span").addClass(type);
    if ($('.popup_overlay').is(':visible')) {
        $("#msg-spn").removeClass('fls-spn');
        $("#msg-spn").addClass('fls-spn-popup');
    } else {
        $("#msg-spn").addClass('fls-spn');
        $("#msg-spn").removeClass('fls-spn-popup');
    }
    $("#upperDiv").find(".msg_span").html(msg);
    clearTimeout(time);
    time = setTimeout(removeMsg, 6000);
}

function showSaveFilter() {
    $(".sml_popup_bg").css({
        display: "block"
    });
    saveAllFilters();
}
function saveAllFilters() {
    var caseStatus = $('#caseStatus').val();
    var caseType = $('#caseTypes').val();
    var caseDate = $('#caseDateFil').val();
    var caseMemeber = $('#caseMember').val();
    var caseAssignTo = $('#caseAssignTo').val();
    var casePriority = $('#priFil').val();
    var caseSearch = $('#caseSearch').val();
    var strURL = HTTP_ROOT + "easycases/";
    $.post(strURL + "ajax_save_filter", {
        'caseStatus': caseStatus,
        'caseType': caseType,
        'caseDate': caseDate,
        'caseMemeber': caseMemeber,
        'caseAssignTo': caseAssignTo,
        'casePriority': casePriority,
        'caseSearch': caseSearch
    }, function (data) {
        if (data) {
            $('#inner_save_filter_td').html(data);
            $('#savefilter_name').focus();
        }
    });
}

function submitfilter() {
    $(".eror_txt").hide();
    var filtername = $('#savefilter_name').val();
    if (filtername.trim() != "") {
        var filter_case_status = $('#fstatus').val();
        var filter_date = $('#fdate').val();
        var filter_duedate = $('#fduedate').val();
        var filter_type = $('#ftype').val();
        var filter_priority = $('#fpriority').val();
        var filter_member = $('#fmember').val();
        var filter_assignto = $('#fassignto').val();
        var filter_search = $('#fsearch').val();
        var projuniqid = $('#projFil').val();
        $("#saveFilBtn").hide();
        $("#svloader").show();
        var strURL = HTTP_ROOT + "easycases/";
        $.post(strURL + "ajax_customfilter_save", {
            'caseStatus': filter_case_status,
            'caseType': filter_type,
            'caseDate': filter_date,
            'casedueDate': filter_duedate,
            'caseMemeber': filter_member,
            'caseAssignTo': filter_assignto,
            'casePriority': filter_priority,
            'filterName': filtername,
            'projuniqid': projuniqid,
            'caseSearch': filter_search
        }, function (data) {
            if (data) {
                if (data == 'success') {
                    showTopErrSucc('success', _('Custom filter saved sucessfully..'));
                    closePopup();
                    resetAllFilters();
                    $("#customFil").addClass('open'); //Require to open the custom filter section automatically
                    openAjaxCustomFilter('auto');
                    var hashtag = parseUrlHash(urlHash);
                    if (hashtag[0] == 'kanban') {
                        easycase.showKanbanTaskList('kanban');
                    } else {
                        ajaxCaseView('case_project');
                    }
                } else {
                    showTopErrSucc('error', _('Custom filter name already exists..'));
                    showSaveFilter();
                    $('#savefilter_name').focus();
                }
            }
        });
    } else {
        $(".eror_txt").show();
        $('#savefilter_name').focus();
    }
}

$(document).keypress(function (evt) {
    if (evt.keyCode == 27) {
        $("#inner_save_filter").hide();
    }
});

function jsVoid() {
}

function removeMsg() {
    $('#upperDiv').fadeOut(300);
    $("#btnDiv").hide();
}
function removeMsg_err() {
    $('#upperDiv_err').fadeOut(300);
    $("#btnDiv").hide();
}
function removeMsg_alert() {
    $('#upperDiv_alert').fadeOut(300);
    $("#btnDiv").hide();
}
function removeMsg_not() {
    $('#upperDiv_not').fadeOut(300);
    $("#btnDiv").hide();
}
var memberListLoaded = 0;
function newProject() {
    $(".add_usr_prj").hide();
    openPopup();
    $(".new_project").show();
//    $('#inner_proj').html('');
    if (typeof PT != 'undefined' && PT == 1 && PAGE_NAME != 'onbording') {
        var strURL = HTTP_ROOT + "projecttemplate/ProjectTemplates/getTemplates";
        $.post(strURL, {}, function (data) {
            if (data) {
              $("#sel_Typ").html('<option value="0" selected>[Select]</option>');
                $("#sel_Typ").append(data);
                if(SES_TYPE < 3){
                    $("#sel_Typ").append('<option value="new">Add New Template</option>');
                }
                
                $("#sel_Typ").val(0);
            }
        });
    }
    $(".loader_dv").hide();

    //setting default form field value
    $('#inner_proj .proj_mem_chk').attr('checked', true);
    $('#validate').val(0)
    $('#inner_proj #members_list, #inner_proj #txt_Proj, #inner_proj #txt_shortProj').val('');

    $('#inner_proj').show();
    //$('#inner_proj').html(data);
    $("#txt_Proj").focus();
    if (!memberListLoaded) {
        getMemeberList();
    }
    memberListLoaded = 1;
    /*}
     });*/
}


var collection = new Array();
function getMemeberList() {
    var url = HTTP_ROOT;
    $.post(url + "projects/member_list", function (res) {
        if (res) {
            $(".new_project").show();
            var cnt = 0;
            $.each(res, function (key, value) {
                cnt++;
                if (value != 'null') {
                    collection.push(value);
                }
            });
            if (cnt >= 1) {
                $('#default_assignto_tr').show();
                $('#add_new_member_txt').text(_('Add Users:'));
            }
            siw = null;
            if (document.addEventListener) {
                document.addEventListener("keydown", handleKeyDown, false);
                document.addEventListener("keyup", handleKeyPress, false);
                document.addEventListener("mouseup", handleClick, false);
                document.addEventListener("mouseover", handleMouseOver, false);
            } else {
                document.onkeydown = handleKeyDown;
                document.onkeyup = handleKeyPress;
                document.onmouseup = handleClick;
                document.onmouseover = handleMouseOver;
            }
            registerSmartInputListeners();
            $('#autopopup').html('<table id="smartInputFloater" class="floater" cellpadding="0" cellspacing="0"><tr><td id="smartInputFloaterContent" nowrap="nowrap" style="padding:0px 0px 0px 0px;text-align: left;font-size:14px;"><\/td><\/tr><\/table>');
            for (x = 0; x < collection.length; x++) {
                collection[x] = collection[x].replace(/\,/gi, '');
            }
            collectionIndex = new Array();
            ds = "";
        }
    }, 'json');
}

function addremoveadmin(obj) {
    var projectuserid = $(obj).val();
    var projectusername = $('#puser' + projectuserid).text();
    if ($(obj).is(":checked")) {
        var selectoptions = "<option value='" + projectuserid + "' selected='selected'>" + projectusername + "</option>";
        $("#select_default_assign option").removeAttr('selected');
        $("#select_default_assign").append(selectoptions);
    } else {
        $("#select_default_assign option[value='" + projectuserid + "']").remove();
    }
}

function creatask() {
    if ($("#caseLoader").is(':visible')) {
        return;
    }
    $(".crt_task_btn_btm").hide();
    $('#opt3').parent().removeClass('option-toggle_active').addClass('option-toggle');
    $('#date_dd').css('font-weight', 'normal');
    if ($('#easycase_uid').val()) {
        //Hide show of project div
        $('#edit_project_div').hide();
        $('#create_project_div').show();
        $('#edit_project_div').html('');
        $('#CS_project_id').val($('#curr_active_project').val());

        $('#easycase_uid').val('');
        $('#CSeasycaseid').val('');
        $('#CS_title').val('');
        $('#drive_tr_0').remove();
        $('#usedstorage').val('');
        $('#up_files').empty();
        $('#CS_message').val('');
        $('#ctask_btn').html(_('Create'));
        $('#taskheading').html(_('Create'));
        $('#ctask_icons').removeClass('icon-edit-tsk').addClass('icon-create-tsk')
        $('.loader_dv_edit').hide();
    }
    $('#editRemovedFile').val('');
    /*var projFil = $('#projFil').val();
     $.post(HTTP_ROOT+"easycases/ajax_quickcase_mem",{"projUniq":projFil,"pageload":0}, function(data){
     if(data) {
     $('#ajxQuickMem').html(data);
     }
     });*/
    var mid = '';
    if (arguments[0] && $('#main-title-holder_' + arguments[0] + ' a').text() != '') {
        mid = arguments[0];
    }
    showProjectName($('#projUpdateTop').html().trim(), $('#CS_project_id').val(), mid);
    if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
        $(".menu-files").removeClass('active');
        $(".menu-milestone").removeClass('active');
        $(".menu-cases").addClass('active');
        $("#widgethideshow").hide();
        $(".milestonenextprev").hide();
    }
    if (CONTROLLER == 'LogTimes' && PAGE_NAME == 'time_log') {
        $("#calendar_view").hide();
        // $(".menu-milestone").removeClass('active');
        // $(".menu-cases").addClass('active');
        // $("#widgethideshow").hide();
        // $(".milestonenextprev").hide();
    }

    $('.task_detail_head').hide();
    $(".slide_rht_con").animate({
        marginLeft: "-100%"
    }, "fast", function () {
        $(".crt_tsk").show();
        $(".crt_tsk").animate({
            left: "175px",
            right: "auto"
        }, "fast");
        $('#CS_title').focus();
    });
    $(".breadcrumb_div,.case-filter-menu").css({
        display: "none"
    });
    $('.dashborad-view-type').hide();
    $(".crt_slide").css({
        display: "block"
    });

    /*if($('#new_case_more_div').is(':visible')){
     $('#less_tsk_opt_div').show();
     $('#more_tsk_opt_div').hide();
     }else{
     $('#less_tsk_opt_div').hide();
     $('#more_tsk_opt_div').show();
     }*/

    /*if(!getCookie("crtdtsk_less") || getCookie("crtdtsk_less")!=1){
     opencase();
     }*/
    $("#footersection").hide();

    scrollPageTop();
    $('#CS_title').focus();
    openEditor('');
//milstoneonTask();
}

function reloadTaskDetail(caseid) {
    easycase.ajaxCaseDetails(caseid, 'case', 0);
}

function restoreTaskDetail(caseid, caseNo) {
    if (confirm(_("Are you sure you want to restore task") + " #" + caseNo + " ?")) {
        var caseids = Array();
        caseids.push(caseid);
        $.post(HTTP_ROOT + "archives/move_list", {
            "val": caseids
        }, function (data) {
            if (data) {
                easycase.ajaxCaseDetails(caseid, 'case', 0);
                showTopErrSucc('success', _("Task #") + caseNo + " " + _("has been restored") + ".");
            }
        });
    }
}

function editask(csuid, projUid, projName) {
    if (typeof csuid == 'undefined') {
        csuid = 0;
    }
    if (csuid) {
        $('.task_detail_head').hide();
        $(".slide_rht_con").animate({
            marginLeft: "-100%"
        }, "fast", function () {
            $(".crt_tsk").show();
            $(".crt_tsk").animate({
                left: "175px",
                right: "auto"
            }, "fast");
        });
        $(".breadcrumb_div,.case-filter-menu").css({
            display: "none"
        });
        $('.dashborad-view-type').hide();
        $(".crt_slide").css({
            display: "block"
        });
        $('#create_project_div').hide();
        $('#edit_project_div').show();
        $('#edit_project_div').html(projName);
        $('#CS_project_id').val(projUid);
        $('#ctask_btn').html(_('Update'));
        $('#taskheading').html(_('Edit'));
        $('#drive_tr_0').remove();
        $('#usedstorage').val('');
        $('#up_files').empty();
        $('#CS_message').val('');
        $('#CS_milestone').val('');
        $('#ctask_icons').removeClass('icon-create-tsk').addClass('icon-edit-tsk')
        $('.popup_overlay').show();
        $('.loader_dv_edit').show();
        $('#editRemovedFile').val('');
        focus_txt();
        scrollPageTop();
        $.post(HTTP_ROOT + 'easycases/edit_task_details', {
            'csUniqid': csuid
        }, function (res) {
            $("#footersection").hide();
            var projFil = $('#projFil').val();
            if (res.files) {
                var file = '';
                var incr = 1;
                $.each(res.files, function (key, value) {
                    var sizeinMb = parseFloat(value.CaseFile.file_size) / 1024;
                    file += '<tr style=""><td valign="top" style="color:#0683B8;"><div id="jquerydiv' + incr + '"><input type="checkbox" style="cursor:pointer;" onclick="return hideEditFile(\'jqueryfile' + incr + '\',\'jquerydiv' + incr + '\',' + sizeinMb + ',' + value.CaseFile.id + ');" checked="">&nbsp;&nbsp;<a style="text-decoration:underline;position:relative;top:-2px;" href="' + HTTP_ROOT + 'easycases/download/' + value.CaseFile.file + '">' + value.CaseFile.file + ' (' + value.CaseFile.file_size + ' Kb)</a><input type="hidden" value="' + value.CaseFile.file + '|' + value.CaseFile.file_size + '|' + value.CaseFile.id + '" id="jqueryfile' + incr + '" name="data[Easycase][name][]" class="ajxfileupload"></div></td></tr>';
                    incr++;
                });
                $('#up_files').html(file);
                $('#totfiles').val(--incr);
            }
            $.post(HTTP_ROOT + "easycases/ajax_quickcase_mem", {
                "projUniq": projUid,
                'csuniqid': res.data.id,
                "pageload": 0
            }, function (data) {
                if (data) {
                    //$('#ajxQuickMem').html(data);
                    PUSERS = data.quickMem;
                    defaultAssign = data.defaultAssign;
                    dassign = data.dassign;
                    case_quick(res.data);
                    //$('#prjchange_loader').hide();
                    //$("#new_case_more_div").slideDown();
                    //$("#more_tsk_opt_div").hide();
                    //$("#less_tsk_opt_div").show();
                    //scrollPageTop();

                    $('#CS_title').val(res.data.title);
                    $('#easycase_uid').val(csuid);
                    var easycaseid = res.data.id;


                    $('#prjchange_loader').hide();
                    opencase();
                    $('[rel=tooltip], #main-nav span, .loader').tipsy({
                        gravity: 's',
                        fade: true
                    });
                    $('#loadquick').hide();
                    $("#usedstorage").val($("#storageusedqc").val());
                    $('#easycase_uid').val(csuid);
                    $('#CSeasycaseid').val(easycaseid);
                    $('.popup_overlay').hide();
                    $('.loader_dv_edit').hide();
                    if (res.mlst_list) {
                        $('#more_opt8 ul li').remove();
                        $('#selected_milestone').html(_('No Milestone'));
                        $('#CS_milestone').val('');
                        $.each(res.mlst_list, function (key, value) {
                            $('#more_opt8 ul').append('<li><a href="javascript:jsVoid()" onclick="open_more_opt(\'more_opt8\');" ><span class="value">' + key + '</span>&nbsp;&nbsp;' + ucfirst(formatText(value)) + '</a></li>');
                        });
                        addTaskEvents();
                    } else {
                        $('#more_opt8 ul li').remove();
                        $('#selected_milestone').html(_('No Milestone'));
                        $('#CS_milestone').val('');
                    }
                    $('#selected_milestone').html(res.data.milestone);
                    $('#CS_milestone').html(res.data.milestone_id);
                }
            });


            // Moreoption content
            /*$('#loadquick').show();
             $.post(HTTP_ROOT+"easycases/case_quick",{'newcase':1,'sel_myproj':projFil,'csuniqid':csuid},function(data1){
             $('#prjchange_loader').hide();
             $("#new_case_more_div").slideDown(300);
             $("#new_case_more_div").html(data1);
             $("#more_tsk_opt_div").hide();
             $("#less_tsk_opt_div").show();
             $('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true});
             $('#loadquick').hide();
             $("#usedstorage").val($("#storageusedqc").val());
             openEditor();
             //Popup show
             if(CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard'){
             $(".menu-files").removeClass('active');
             $(".menu-cases").addClass('active');
             $("#widgethideshow").hide();
             }
             $('#easycase_uid').val(csuid);
             $('#CSeasycaseid').val(easycaseid);
             $('.popup_overlay').hide();
             $('.loader_dv_edit').hide();
             });*/
        }, 'json');
    }
}
function case_quick(easycase) { // add assignto, user checkboxs,
    var i = k = 0, chked = "", defaultAsgnName = '', defaultAsgn = parseInt(defaultAssign);
    $('#ajxQuickMem #viewmemdtls, #more_opt5 ul').html('');
    $('#ajxQuickMem #viewmemdtls .notify_cls').prop('checked', false);
    $('#task_priority_td table').show();
    if (RCT == 1) {
        $('.isRecurring').show();
        $('#is_recurring').removeAttr('checked');
        $('#recurring_task_block').hide();
        $('#repeat_type').html("<span class='ttfont'>  " + _('None') + "</span>");
        $("#CSrepeat_type").val('');
        $("#occur, #date").attr('disabled', 'disabled');
        $("#occur, #date").prop('checked', false);
        $('#occurrence, #end_datePicker, #start_datePicker').attr('disabled', 'disabled').val('');
    }
    if (CR == 1) {
        $('#clientdiv').show();
        $('#clientdiv').children('.color_tag').remove();
    }
    var userIds = '';
    var userNames = ''
    if (countJS(PUSERS)) {

        var dassignArr = Array();
        var UserClients = '';
        if (dassign) {
            for (ui in dassign) {
                dassignArr.push(dassign[ui]);
            }
        }

        for (ipusr in PUSERS) {
            for (ipusr1 in PUSERS[ipusr]) {
                var pusr = PUSERS[ipusr][ipusr1]
                chked = '';
                /***Start *****/
                var chk_client = 0;
                userIds += ',' + pusr.User.id;
                userNames += ',' + pusr.User.name;
                var title = '';
                if (pusr.User.last_name != '') {
                    title = pusr.User.name + '&nbsp;' + pusr.User.last_name;
                } else {
                    title = pusr.User.name;
                }
                title = ucfirst(title);

                if (typeof pusr.CompanyUser != 'undefined' && pusr.CompanyUser.is_client == 1) {
                    chk_client = 1;
                    if (UserClients == '') {
                        UserClientsId = pusr.User.id;
                        UserClients = pusr.User.name;
                        UserClientsEmail = pusr.User.email;
                    } else {
                        UserClientsId += ',' + pusr.User.id;
                        UserClients += ', ' + pusr.User.name;
                        UserClientsEmail += ', ' + pusr.User.email;
                    }
                    $('#clientdiv').append("<span class='color_tag' id='make_clientspn' title='" + title + "'>" + ucfirst(pusr.User.name) + "</span>");
                }
                if (typeof pusr.CompanyUser != 'undefined' && pusr.User.id == SES_ID && pusr.CompanyUser.is_client == 1) {
                    $('#clientdiv').hide();
                }
                $('#userIds').val(userIds);
                $('#userNames').val(userNames);
                /* End */


                if ($.inArray(pusr.User.id, dassignArr) != -1) {
                    chked = "checked='checked'";
                } else if (pusr.User.id == defaultAsgn) {
                    chked = "checked='checked'";
                } else if ((!defaultAsgn && pusr.User.id == SES_ID) || (typeof pusr.UserNotification != 'undefined' && pusr.UserNotification.new_case == 1)) {
                    chked = "checked='checked'";
                }
                if (!pusr.User.name) {
                    var i = pusr.User.email.indexOf("@");
                    if (pusr.User.email.indexOf("@") != -1) {
                        pusr.User.name = pusr.User.email.substring(0, i);
                    }
                }

                if (chk_client == 1) {
                    $('#viewmemdtls').append('<div class="fl checkbox custom-checkbox  add-user-pro-chk" style="margin-top:15px; margin-right:10px;"><label data-toggle="tooltip" data-placement="top" title="" data-original-title="' + ucfirst(pusr.User.email) + '"><input type="checkbox" name="data[Easycase][user_emails][]" id="chk_' + pusr.User.id + '" class="notify_cls fl chk_client" value="' + pusr.User.id + '" onClick="removeAll()" ' + chked + ' />&nbsp;' + ucfirst(shortLength(pusr.User.name, 15)) + '</label></div>');
                } else {
                    $('#viewmemdtls').append('<div class="fl checkbox custom-checkbox  add-user-pro-chk" style="margin-top:15px; margin-right:10px;"><label data-toggle="tooltip" data-placement="top" title="" data-original-title="' + ucfirst(pusr.User.email) + '"><input type="checkbox" name="data[Easycase][user_emails][]" id="chk_' + pusr.User.id + '" class="notify_cls fl" value="' + pusr.User.id + '" onClick="removeAll()" ' + chked + ' />&nbsp;' + ucfirst(shortLength(pusr.User.name, 15)) + '</label></div>');
                }
                $('.notify_user').addClass('fl');
                $('.notify_cls').addClass('fl');
                $('.chk_client').addClass('fl');
                i = i + 1;
                k = i % 4;
                if (k == 0) {
                    $('#viewmemdtls').append('<div class="cb"></div>');
                }
//                        if (SES_ID == pusr.User.id) { /*$('#more_opt5'+ext+' ul').append("<li><a href="+"javascript:jsVoid()"+" class="+"ttfont"+" title="+title+" onclick="+"notified_users("+pusr.User.id+",'"+pusr.User.name.trim()+"','"+objid+"')"+" data-name='"+pusr.User.name+"' value='"+pusr.User.id+"'><span class="+"value"+" rel="+"tooltip"+">"+pusr.User.id+"</span>&nbsp;&nbsp;me</a></li>");*/
//                            $('.crtskasgnusr').append("<option value='" + pusr.User.id + "'>me</option>");
//                        } else { /*$('#more_opt5'+ext+' ul').append("<li><a href="+"javascript:jsVoid()"+" class="+"ttfont"+" title="+title+" onclick="+"notified_users("+pusr.User.id+",'"+pusr.User.name+"','"+objid+"')"+" data-name='"+pusr.User.name+"' value='"+pusr.User.id+"'><span class="+"value"+" rel="+"tooltip"+">"+pusr.User.id+"</span>&nbsp;&nbsp;"+ucfirst(formatText(pusr.User.name))+"</a></li>");*/
//                            $('.crtskasgnusr').append("<option value='" + pusr.User.id + "'>" + ucfirst(pusr.User.name) + "</option>");
//                        }

                //get default assigned name for to select
                if (easycase && easycase.assign_to && easycase.assign_to == pusr.User.id && easycase.assign_to != SES_ID) {
                    defaultAsgnName = pusr.User.name;
                    defaultAsgn = easycase.assign_to;
                } else if (easycase && easycase.assign_to && easycase.assign_to == pusr.User.id && easycase.assign_to == SES_ID) {
                    defaultAsgnName = pusr.User.name;
                    defaultAsgn = easycase.assign_to;
                } else if (!defaultAsgnName && defaultAsgn && defaultAsgn == pusr.User.id) {
                    defaultAsgnName = pusr.User.name;
                    defaultAsgn = defaultAsgn;
                }
                if (easycase && easycase.assign_to == 0) {
                    defaultAsgnName = 'Nobody';
                    defaultAsgn = 0;
                }
                if (SES_ID == pusr.User.id) {
                    $('#more_opt5 ul').append('<li><a href="javascript:jsVoid()" onclick="notified_users(' + pusr.User.id + ');" value="' + pusr.User.id + '" onClick="removeAll()"><span class="value">' + pusr.User.id + '</span>&nbsp;&nbsp;me</a></li>');
                } else {
                    $('#more_opt5 ul').append('<li><a href="javascript:jsVoid()" onclick="notified_users(' + pusr.User.id + ');" value="' + pusr.User.id + '" onClick="removeAll()"><span class="value">' + pusr.User.id + '</span>&nbsp;&nbsp;' + ucfirst(formatText(pusr.User.name)) + '</a></li>');
                }
            }
        }
        $('#more_opt5 ul').append('<li><a href="javascript:jsVoid()" onclick="notified_users(0);" value="0" onClick="removeAll()"><span class="value">0</span>&nbsp;&nbsp;Nobody</a></li>');



//                if ($.inArray(pusr.User.id, dassignArr) != -1) {
//                    chked = "checked='checked'";
//                } else if (pusr.User.id == defaultAsgn) {
//                    chked = "checked='checked'";
//                } else if (!defaultAsgn && pusr.User.id == SES_ID) {
//                    chked = "checked='checked'";
//                }
//                if (!pusr.User.name) {
//                    var i = pusr.User.email.indexOf("@");
//                    if (pusr.User.email.indexOf("@") != -1) {
//                        pusr.User.name = pusr.User.email.substring(0, i);
//                    }
//                }
//                //add checkboxes
//                $('#ajxQuickMem #viewmemdtls').append('<div class="viewmemdtls_cls fl"><input type="checkbox" name="data[Easycase][user_emails][]" id="chk_' + pusr.User.id + '" class="notify_cls fl" value="' + pusr.User.id + '" onClick="removeAll()" ' + chked + ' />&nbsp;<div class="fl user_email" style="padding-left:6px;" title="' + ucfirst(pusr.User.email) + '" rel="tooltip">' + ucfirst(shortLength(pusr.User.name, 20)) + '&nbsp;&nbsp;</div></div>');
//
//                i = i + 1;
//                k = i % 3;
//                if (k == 0) {
//                    $('#ajxQuickMem #viewmemdtls').append('<div class="cb"></div>');
//                }
//
//                //add assign to dropdown

//
//                //get default assigned name for to select
//                if (easycase && easycase.assign_to && easycase.assign_to == pusr.User.id && easycase.assign_to != SES_ID) {
//                    defaultAsgnName = pusr.User.name;
//                    defaultAsgn = easycase.assign_to;
//                } else if (!defaultAsgnName && defaultAsgn && defaultAsgn == pusr.User.id) {
//                    defaultAsgnName = pusr.User.name;
//                    defaultAsgn = defaultAsgn;
//                }
//            }
//        }

    } else {
        $('#more_opt5 ul').append('<li><a href="javascript:jsVoid()" onclick="open_more_opt(\'more_opt5\');">&nbsp;&nbsp;me<span class="value">' + SES_ID + '</span></a></li>');
    }

    if ($('#ajxQuickMem #viewmemdtls').find('input[type="checkbox"]:checked').length == $('#ajxQuickMem #viewmemdtls').find('input[type="checkbox"]').length) {
        $('#ajxQuickMem').find('#chked_all').prop('checked', true);
    }
    //add to whom default assigned on assign to select
    if (defaultAsgn && defaultAsgnName && defaultAsgn != SES_ID) {
        $('#tsk_asgn_to').html(defaultAsgnName);
        $('#CS_assign_to').val(defaultAsgn);
    } else {
        $('#tsk_asgn_to').html(_('me'));
        $('#CS_assign_to').val(SES_ID);
        if (defaultAsgn == 0) {
            $('#tsk_asgn_to').html(defaultAsgnName);
            $('#CS_assign_to').val(defaultAsgn);
        }
    }

    //default values for create task
    if (parseInt($("#is_default_task_type").val())) {
        $.each(GLOBALS_TYPE, function (key, value) {
            if (value.Type.name == 'Development') {
                $('#CS_type_id').val(2);
                $('#ctsk_type').html('<img class="flag" src="' + HTTP_IMAGES + 'images/types/dev.png" alt="type" style="padding-top:3px;"/>' + _('Development'));
            }
        });
    }

    $('#CS_priority').val(1);
    $('#priority_mid').prop('checked', true);
    $('#priority_low, #priority_high').prop('checked', false);

    if (parseInt(gDueDate)) {
        $('#CS_due_date').val('No Due Date');
        $('#date_dd').html(_('No Due Date'));
    } else {
        gDueDate = 1;
    }

    $('#CS_message').val('');
    $('#estimated_hours, #hours, #gantt_start_date, #CS_start_date').val('');
	
    if (easycase) {
        if (RCT == 1) {
            $('.isRecurring').hide();
            $('#recurring_task_block').hide();
        }
        if (easycase.type_id) {
            $('#CS_type_id').val(easycase.type_id);
            if (easycase.type_id < 12) {
                $('#ctsk_type').html('<img class="flag" src="' + HTTP_IMAGES + 'images/types/' + easycase.type_shortname + '.png" alt="type" style="padding-top:3px;"/>&nbsp;' + _(easycase.type_name));
            } else {
                $('#ctsk_type').html(_(easycase.type_name));
            }
            /*for (typi in GLOBALS_TYPE) {
             if (easycase.type_id == GLOBALS_TYPE[typi].Type.id) {
             $('#ctsk_type').html('<img class="flag" src="' + HTTP_IMAGES + 'images/types/' + GLOBALS_TYPE[typi].Type.short_name + '.png" alt="type" style="padding-top:3px;"/>&nbsp;' + _(GLOBALS_TYPE[typi].Type.name));
             break;
             }
             }*/
        }
        if (easycase.gantt_start_date) {
            $('#gantt_start_date').val(moment(easycase.gantt_start_date, 'YYYY-MM-DD').format('MMM DD, YYYY'));
            $('#CS_start_date').val(moment(easycase.gantt_start_date, 'YYYY-MM-DD').format('YYYY-MM-DD'));
        }
        if (easycase.priority) {
            $('#CS_priority').val(easycase.priority);

            $('#priority_low, #priority_high, #priority_mid').prop('checked', false);
            if (easycase.priority == 2) {
                $('#priority_low').prop('checked', true);
            } else if (easycase.priority == 0) {
                $('#priority_high').prop('checked', true);
            } else {
                $('#priority_mid').prop('checked', true);
            }
        }
        if (easycase.due_date) {
            $('#CS_due_date').val(easycase.due_date);
            $('#date_dd').html(formatDate('M d, D', easycase.due_date));

        }

        if (easycase.message) {
            $('#CS_message').val(easycase.message);
        }
        if (easycase.estimated_hours) {
            $('#estimated_hours').val(easycase.estimated_hours);
        }
        if (easycase.hours) {
            $('#hours').val(easycase.hours);
        }
        openEditor(easycase.message);
    }
    addTaskEvents(); //add evens for dropdown, date and others
}
function crt_popup_close() {//return;
    $("#footersection").show();
    $(".crt_task_btn_btm").show();
    $(".crt_tsk").animate({
        left: "100%"
    }, "fast", function () {
        var caseMenuFilters = $('#caseMenuFilters').val();
        var params = parseUrlHash(urlHash);
        if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard' && caseMenuFilters == "") {
            if (params[0] != "details") {
                $(".breadcrumb_div,.case-filter-menu").css({
                    display: "block"
                });
            }
            $("#widgethideshow").show();
            if ($('#caseViewSpan').html() == "" && params[1]) {
                easycase.routerHideShow(params[0]);
                $('#t_' + params[1]).show();
            } else {
                if ($('#easycase_uid').val() && params[0] == 'details') {
                    easycase.refreshTaskList($('#easycase_uid').val());
                } else if (params[0] != "tasks") {
                    parent.location.hash = "tasks";
                }
                easycase.routerHideShow(params[0]);
            }
        } else if (CONTROLLER == 'LogTimes' && PAGE_NAME == 'time_log') {
            $('#prj_drpdwn,#select_view').show();
            if (PAGE_NAME == 'time_log') {
                $('.breadcrumb_div').show();
                $(".crt_tsk").hide();
                $(".slide_rht_con").animate({
                    marginLeft: "0px"
                }, "fast");
                $(".crt_slide").css({
                    display: "none"
                });
            }
            if (params[0] != '' && params[0] == 'calendar') {
                $('#calendar_view').show();
                // $('#timelogtbl').addClass('temp-class');
                // $('.timelog-detail-tbl').hide();
                // $('#calendar_view').show().css({
                //                'margin-top' : '0px',
                //                'margin-left' : '22px'
                // });
                calenderForTimeLog(params[0]);
            }

        } else {
            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                if (params[0] === "milestonelist") {
                    if (parseInt($("#totalMlstCnt").val()) > 3) {
                        $(".milestonenextprev").show();
                    }
                }
                easycase.routerHideShow(params[0]);
            } else {
                $('.breadcrumb_div').show();
                $(".crt_tsk").hide();
                $(".slide_rht_con").animate({
                    marginLeft: "0px"
                }, "fast");
                $(".crt_slide").css({
                    display: "none"
                });
            }
            if (params[0] == "details") {
                $('#t_' + params[1]).show();
            }
        }
    });
}


function view_btn_case(id) {
    if (id != 0) {
        $('#btn_cse').show();
    } else {
        $('#btn_cse').hide();
    }
}

function noSpace(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8) {
        if (unicode == 32) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

function addUserToProject() {
    var prj_id = getCookie('LAST_PROJ');
    if (parseInt(prj_id)) {
        createCookie("LAST_PROJ", '', -365, DOMAIN_COOKIE);
        var prj_name = $("a.icon-add-usr[data-prj-id='" + prj_id + "']").attr('data-prj-name');
        if (confirm(_("Do you want to add user in") + " '" + prj_name + "' ?")) {
            add_user(prj_id, prj_name);
        }
    }
}

function projectAdd(txtProj, shortname, loader, btn) {
    document.getElementById('err_msg').innerHTML = "";
    document.getElementById('validate').value = '1'
    var proj1 = "";
    proj1 = document.getElementById(txtProj).value;
    shortname1 = document.getElementById(shortname).value;
    var strURL = HTTP_ROOT;
    proj1 = proj1.trim();
    if (proj1 == "") {
        msg = _("'Project Name' cannot be left blank!");
        document.getElementById('err_msg').style.display = 'block';
        document.getElementById('err_msg').innerHTML = msg;
        document.getElementById(txtProj).focus();
        return false;
    } else {
        if (!proj1.match(/^[A-Za-z0-9]/g)) {
            msg = _("'Project Name' must starts with an Alphabet or Number!");
            $('#err_msg').show();
            $('#err_msg').html(msg);
            $('#' + txtProj).focus();
            return false;
        }
    }
    if (shortname1.trim() == "") {
        msg = _("'Project Short Name' cannot be left blank!");
        document.getElementById('err_msg').style.display = 'block';
        document.getElementById('err_msg').innerHTML = msg;
        document.getElementById(shortname).focus();
        return false;
    } else {
        var x = shortname1.substr(-1);
        if (!shortname1.match(/^[a-z0-9]+$/i)) {
            msg = _("'ShortName' must be alphanumeric!");
            document.getElementById('err_msg').style.display = 'block';
            document.getElementById('err_msg').innerHTML = msg;
            document.getElementById(shortname).focus();
            return false;
        }
        var email_id = $('#members_list').val();
        var done = 1;
        if (email_id) {
            var email_arr = email_id.split(',');
            var totlalemails = 0;
            var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!email_id.match(emailRegEx)) {
                if (email_id.indexOf(',') != -1) {
                    for (var i = 0; i < email_arr.length; i++) {
                        if (email_arr[i].trim() != "") {
                            if ((!email_arr[i].trim().match(emailRegEx))) {
                                done = 0;
                                msg = "Invalid Email: '" + email_arr[i] + "'";
                                $('#err_mem_email').show();
                                $('#err_mem_email').html(msg);
                                $('#members_list').focus();
                                return false;
                            }
                        } else {
                            totlalemails++;
                        }
                    }
                    if (totlalemails == email_arr.length) {
                        msg = _("Entered stirng is not a valid email");
                        $('#err_mem_email').show();
                        $('#err_mem_email').html(msg);
                        $('#members_list').focus();
                        return false;
                    }
                } else {
                    msg = "Invalid E-Mail!";
                    $('#err_mem_email').show();
                    $('#err_mem_email').html(msg);
                    $('#members_list').focus();
                    return false;
                }
            }
        } else {
            $('#err_mem_email').html();
        }
        document.getElementById('err_msg').style.display = 'none';
        document.getElementById(loader).style.display = 'block';
        document.getElementById(btn).style.display = 'none';

        $.post(strURL + "projects/ajax_check_project_exists", {
            "name": escape(proj1),
            "shortname": escape(shortname1)
        }, function (data) {

            if (data == "Project") {
                document.getElementById(loader).style.display = 'none';
                document.getElementById(btn).style.display = 'block';
                msg = "'Project Name' is already exists!";
                document.getElementById('err_msg').style.display = 'block';
                document.getElementById('err_msg').innerHTML = msg;
                document.getElementById(shortname).focus();
                return false;
            } /*else if (data == "ShortName") {
                document.getElementById(loader).style.display = 'none';
                document.getElementById(btn).style.display = 'block';
                msg = "'Project Short Name' is already exists!";
                document.getElementById('err_msg').style.display = 'block';
                document.getElementById('err_msg').innerHTML = msg;
                document.getElementById(shortname).focus();
                return false;
            } */else {
                if (email_id) {
                    $.post(strURL + 'users/check_fordisabled_user', {
                        'email': email_id
                    }, function (res) {
                        if (res != '1') {
                            $('#' + loader).hide();
                            $('#' + btn).show();
                            if (res.indexOf(',') != -1) {
                                var msg = "'" + res + "' " + _("Users are disabled users.They are not allowed to add into a project") + ".";
                            } else {
                                msg = "'" + res + "' " + _("is a disabled user, So cann't be added to a project");
                            }

                            $('#err_mem_email').show();
                            $('#err_mem_email').html(msg);
                            $('#members_list').focus();
                            return false;
                        } else {
                            $('#err_mem_email').html('');
                            $('#err_mem_email').hide();
                            document.projectadd.submit();
                            return true;
                        }
                    });
                } else {
                    document.projectadd.submit();
                    return true;
                }
            }
        });
        return false;
    }
    return false;
}

function newUser() {
    $(".add_prj_usr").hide();
    openPopup();
    $(".new_user").show();
    /*$('#inner_user').html('');
     var strURL = HTTP_ROOT + "users/new_user";
     
     $.post(strURL, {}, function(data) {
     if (data) {*/
    $(".loader_dv").hide();

    //setting default form field value
    $('#txt_email').css('height', '60px');
    $('#txt_email').val('');
    $('.auto_tab_fld').html('<select name="data[User][pid]" id="sel_custprj" class="form-control"></select>');
    $('#sel_Typ').val(3);

    $('#inner_user').show();
    //$('#inner_user').html(data);
    $("#txt_email").focus();
    getAutocompleteTag("sel_custprj", "users/getProjects", "340px", _("Type to select projects"));
    /*}
     });*/
}

//Multiple autocomplete with tagging.
//Tutorial site - https://github.com/emposha/FCBKcomplete
function getAutocompleteTag(id, url, width, plchlder) {
    $("#" + id).fcbkcomplete({
        json_url: HTTP_ROOT + url,
        addontab: true,
        maxitems: 10,
        input_min_size: 0,
        height: 10,
        cache: true,
        filter_selected: true,
        firstselected: true,
        width: width,
        complete_text: plchlder
    });
}

function memberCustomer(txtEmailid, selprj, loader, btn) {
    var email_id = document.getElementById(txtEmailid).value;
    var email_arr = email_id.split(',');
    var done = 1;
    if (email_id == "") {
        done = 0;
        msg = _("Email cannot be left blank!");
        document.getElementById('err_email_new').innerHTML = "";
        document.getElementById('err_email_new').style.display = 'block';
        document.getElementById('err_email_new').innerHTML = msg;
        document.getElementById(txtEmailid).focus();
        return false;
    } else {
        var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!email_id.match(emailRegEx)) {
            if (email_id.indexOf(',') != -1) {
                var totlalemails = 0;
                for (var i = 0; i < email_arr.length; i++) {
                    if (email_arr[i].trim() != "") {
                        if ((!email_arr[i].trim().match(emailRegEx))) {
                            done = 0;
                            msg = _("Invalid Email") + ": '" + email_arr[i] + "'";
                            document.getElementById('err_email_new').innerHTML = "";
                            document.getElementById('err_email_new').style.display = 'block';
                            document.getElementById('err_email_new').innerHTML = msg;
                            document.getElementById(txtEmailid).focus();
                            return false;
                        }
                    } else {
                        totlalemails++;
                    }
                }
                if (totlalemails == email_arr.length) {
                    msg = _("Entered stirng is not a valid email");
                    $('#err_email_new').html("");
                    $('#err_email_new').show();
                    $('#err_email_new').html(msg);
                    $('#' + txtEmailid).focus();
                    return false;
                }
            } else {
                done = 0;
                msg = _("Invalid E-Mail!");
                document.getElementById('err_email_new').innerHTML = "";
                document.getElementById('err_email_new').style.display = 'block';
                document.getElementById('err_email_new').innerHTML = msg;
                document.getElementById(txtEmailid).focus();
                return false;
            }
        }
        if (done != 0) {
            var type = document.getElementById('sel_Typ').value;
            if (type == 2) {
                var usertype = "Admin";
            } else if (type == 3) {
                var usertype = "Member";
            }
            document.getElementById('err_email_new').style.display = 'none';

            $("#ldr").show();
            $("#btn_addmem").hide();

            var uniq_id = $("#uniq_id").val();
            var strURL = HTTP_ROOT;
            if (email_id.indexOf(',') != -1) {
                $.post(strURL + "users/ajax_check_user_exists", {
                    "email": escape(email_id),
                    "uniq_id": escape(uniq_id)
                }, function (data) {
                    if (data == "success") {
                        document.myform.submit();
                        return true;
                    } else {
                        if (data == 'errorlimit') {
                            $("#ldr").hide();
                            $("#btn_addmem").show();
                            $("#err_email_new").show();
                            $("#err_email_new").html(_("Sorry! You are exceeding your user limit."));
                        } else {
                            $("#ldr").hide();
                            $("#btn_addmem").show();
                            $("#err_email_new").show();
                            $("#err_email_new").html(_("Oops! Invitation already sent to") + " '" + data + "'!");
                        }
                        return false;
                    }
                });
            } else {
                $.post(strURL + "users/ajax_check_user_exists", {
                    "email": escape(email_id),
                    "uniq_id": escape(uniq_id)
                }, function (data) {
                    if (data == "invited" || data == "exists" || data == "owner" || data == "account") {
                        $("#ldr").hide();
                        $("#btn_addmem").show();
                        $("#err_email_new").show();
                        if (data == "owner") {
                            $("#err_email_new").html(_("Ah... you are inviting the company Owner!"));
                        } else if (data == "account") {
                            $("#err_email_new").html(_("Ah... you are inviting yourself!"));
                        } else {
                            $("#err_email_new").html(_("Oops! Invitation already sent to") + " '" + email_id + "'!");
                        }
                        return false;
                    } else {
                        document.myform.submit();
                        return true;
                    }
                });
            }
        }
    }
    return false;
}

/*search sliding*/
function sch_slide() {
    //$(".search_top").onClick(function(){
    $(".search_top").animate({
        width: '380px'
    }, 400);
    // });
    $(".search_top").blur(function () {
        if ($(this).val() == "") {
            $(this).animate({
                width: '150px'
            }, 400);
        }
    });
}
/*search slding ends*/

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

//Switch Project dropdown starts

$(document).ready(function () {
    $("#prj_ahref").hover(function () {
        if ($('#prj_drpdwn').hasClass('dropdown')) {
            $('#projpopup').removeAttr('style');
        }
    });
    //Create task tooltip starts
    $('.show_tooltip').hide();
    $('a.wink').hover(function () {
        $(this).find('.show_tooltip').show();
    }, function () {
        $(this).find('.show_tooltip').hide();
    });
//Create task tooltip starts
});

$(document).keypress(function (e) {
    if (e.which == 13) {
        if ($('a').hasClass('popup_selected')) {
            eval($('.popup_selected').attr('onClick'));
        }
    }
});

function view_project_menu(page) {
    if (typeof page == 'undefined') {
        page = 'dashboard';
    }
    var caseMenuFilters = $('#caseMenuFilters').val();
    if (caseMenuFilters == 'calendar') {
        $('.fc-view-agendaWeek').css('z-index', '1');
        $('.fc-view-agendaDay').css('z-index', '1');
    }
    var usrUrl = HTTP_ROOT;
    if ($('#ajaxViewProjects').html() == "") {
        if ($('#ajaxViewProjects').is(':visible')) {
            $('#loader_prmenu').hide();
        } else {
            $("#search_project_menu_txt").val('');
            $('#ajaxViewProject').html('');
            $('#ajaxViewProjects').html('');
            $("#find_prj_dv").hide();
            $('#loader_prmenu').show();
            var params = parseUrlHash(urlHash);
            if (params[0] == "milestonelist" || params[0] == "milestone") {
                page = "milestonelist";
            }
            $.post(usrUrl + "users/project_menu", {
                "page": page,
                "limit": 6,
                "filter": caseMenuFilters
            }, function (data) {
                if (data) {
                    $('#loader_prmenu').hide();
                    $('#projpopup').show();
                    $("#find_prj_dv").show();
                    $('#ajaxViewProjects').show();
                    $('#ajaxViewProjects').html(data);
                    $('#checkload').val('1');
                    $('#search_project_menu_txt').focus();
                }
            });
        }
    } else {
        $("#search_project_menu_txt").val('');
        $('#ajaxViewProject').html('');
        $('#ajaxViewProject').hide();
        $("#find_prj_dv").show();
        $('#search_project_menu_txt').focus();
        page == 'time_log' && $('#ajaxViewProjects').parent('.dropdown-menu').is(':visible') ? '' : $('#ajaxViewProjects').parent('.dropdown-menu').show();
        if (page == 'time_log' && $('#ajaxViewProjects').parent('.dropdown-menu').is(':visible')) {
            $('#ajaxViewProjects').parent('.dropdown-menu').hover(function () {
                $(this).show();
            }, function () {
                $(this).hide();
            });
            $('#prj_ahref').hover(function () {
                $(this).next('.dropdown-menu').show();
            }, function () {
                $(this).next('.dropdown-menu').hide();
            });
        }
        $('#ajaxViewProjects').show();
    }
}

function search_project_menu(page, val, e) {
    var key = e.keyCode;
    if (key == 13)
        return;
    var menu_div_id = 'ajaxViewProjects';
    if ($('#ajaxViewProject').is(":visible")) {
        var menu_div_id = 'ajaxViewProject';
        $('#ajaxViewProjects > li').removeClass('popup_selected');
    }

    if (e.keyCode == 40 || e.keyCode == 38) {
        var selected = "$('#" + menu_div_id + " > a')";
        if (key == 40) { // Down key
            if (!$('#' + menu_div_id + ' > a').length || $('#' + menu_div_id + '> a').filter('.popup_selected').is(':last-child')) {
                $current = $('#ajaxViewProject > a').eq(0);
                //$current.addClass('popup_selected');
            } else {
                if ($('#' + menu_div_id + '> a').hasClass('popup_selected')) {
                    $current = $('#' + menu_div_id + '> a').filter('.popup_selected').next('hr').next('a');
                } else {
                    $current = $('#' + menu_div_id + ' > a').eq(0);
                }
            }
        } else if (key == 38) {// Up key
            if (!$('#' + menu_div_id + ' > a').length || $('#' + menu_div_id + '> a').filter('.popup_selected').is(':first-child')) {
                $current = $('#' + menu_div_id + ' > a').last('a');
            } else {
                $current = $('#' + menu_div_id + ' > a').filter('.popup_selected').prev('hr').prev('a');
            }
        }
        $('#' + menu_div_id + ' > a').removeClass('popup_selected');
        $current.addClass('popup_selected');
    } else {
        var caseMenuFilters = $('#caseMenuFilters').val();//alert(caseMenuFilters);
        var strURL = HTTP_ROOT + "users/";
        if (val != "") {
            $('#load_find_dashboard').show();
            $.post(strURL + "search_project_menu", {
                "page": page,
                "val": val,
                "filter": caseMenuFilters,
                "page_name": ''
            }, function (data) {//pgname
                if (data) {
                    $('#ajaxViewProject').show();
                    $('#ajaxViewProjects').hide();
                    $('#ajaxViewProject').html(data);
                    $('#load_find_dashboard').hide();
                }
            });
        } else {
            $('#ajaxViewProject').hide();
            $('#ajaxViewProjects').show();
            $('#load_find_dashboard').hide();
        }
    }
}

function updateAllProj(radio, projId, page, all, pname, srch) {
    // Code added for reset filteration during switch project---- Start
    var stop_repeated_calls = '';
    var params = parseUrlHash(urlHash);
    refreshTasks = 1;
    refreshActvt = 1;
    refreshKanbanTask = 1;
    refreshMilestone = 1;
    refreshManageMilestone = 1;
    search_key = '';
    createCookie('timelogsort', '', -365, '');
    createCookie('logstrtdt', '', -365, '');
    createCookie('logenddt', '', -365, '');
    createCookie('rsrclog', '', -365, '');
    createCookie("All_Project", "", -365, "");

    if ($('#reset_btn').is(":visible") && !$('#customFIlterId').val() && !($('#filtered_items .filter_opn').length == 1 && casePage > 1)) {
        if (confirm(_('Do you want to reset the filters already active ?'))) {
            if ($('#search_txt_spn').text()) {
                $('#clearCaseSearch').val(1);
            }
            $('#caseStatus').val("all"); // Filter by Status(legend)
            $('#priFil').val("all"); // Filter by Priority
            $('#caseTypes').val("all"); // Filter by case Types
            $('#caseMember').val("all");  // Filter by Member
            $('#caseAssignTo').val("all");  // Filter by AssignTo
            //$('#casePage').val("1"); // Pagination
            $('#case_srch').val("");
            $('#caseDateFil').val("");
            $('#casedueDateFil').val("");// filter by due date
            $('#status_all').attr('checked', 'checked');
            $('#status_new').removeAttr('checked');
            $('#status_open').removeAttr('checked');
            $('#status_close').removeAttr('checked');
            $('#status_resolve').removeAttr('checked');
            $('#status_file').removeAttr('checked');
            $('#status_upd').removeAttr('checked');
            var totid = $('#totMemId').val();
            for (var i = 1; i <= totid; i++) {
                var checkboxid = "mems_" + i;
                $('#' + checkboxid).removeAttr('checked');
            }

            var totasnid = $('#totAsnId').val();
            for (var i = 1; i <= totasnid; i++) {
                var checkboxid = "Asns_" + i;
                $('#' + checkboxid).removeAttr('checked');
            }
            $('#priority_all').checked = true;
            $('#priority_High').removeAttr('checked');
            $('#priority_Medium').removeAttr('checked');
            $('#priority_Low').removeAttr('checked');

            var totid = $('#totType').val();
            for (var i = 1; i <= totid; i++) {
                var checkboxid = "types_" + i;
                $('#' + checkboxid).removeAttr('checked');
            }
            $('#case_search').val(''); // Search text
            $('#closesrch').hide();
            $('#srch_load2').show();
            $('#case_search').val('');
            $('#caseDateFil').val('');
            $('#milestoneIds').val('');
            remember_filters('reset', 'all');
        }
    } else {
        //$('#casePage').val("1");
        casePage = 1;
    }
    casePage = 1;
    // Code added for reset filteration during switch project---- End
    if (all == '0') {
        $("#projUpdateTop").html(decodeURIComponent(pname));
        //$('#projpopup').hide();
        $('#prj_drpdwn').removeClass("open");
        $(".dropdown-menu.lft").hide();
        $("#find_prj_dv").hide();
        if (pname && (page != "import")) {

            var fst = $('#pname_dashboard_hid').val();
            var secnd = $('#first_recent_hid').val();
            var thrd = $('#second_recent_hid').val();
            var decodepname = decodeURIComponent(pname);
            var ucpname = decodepname.charAt(0).toUpperCase() + decodepname.substr(1);

            //var cnt = "<a href='javascript:void(0);' onClick='updateAllProj(\""+radio+"\",\""+projId+"\",\""+page+"\",\""+all+"\",\""+pname+"\",\""+srch+"\");'>"+shortLength(ucpname,20)+"</a>";
            var cnt = shortLength(ucpname, 20);

            if (secnd == ucpname) {
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);
                $('#first_recent_hid').val(fst.charAt(0).toUpperCase() + fst.substr(1));
                $('#pname_dashboard_hid').val(secnd.charAt(0).toUpperCase() + secnd.substr(1));
            } else if (thrd == ucpname) {
                $('#second_recent').html($('#first_recent').html());
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);

                $('#second_recent_hid').val(secnd.charAt(0).toUpperCase() + secnd.substr(1));
                $('#first_recent_hid').val(fst.charAt(0).toUpperCase() + fst.substr(1));
                $('#pname_dashboard_hid').val(thrd.charAt(0).toUpperCase() + thrd.substr(1));

            } else if (fst == ucpname) {
                $('#pname_dashboard').html(cnt);
            } else {
                $('#second_recent').html($('#first_recent').html());
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);
                if (typeof (secnd) != "undefined") {
                    $('#second_recent_hid').val(secnd.charAt(0).toUpperCase() + secnd.substr(1));
                }
                if (typeof (fst) != "undefined") {
                    $('#first_recent_hid').val(fst.charAt(0).toUpperCase() + fst.substr(1));
                }
                $('#pname_dashboard_hid').val(ucpname);

            }

            $('.pname_dashboard').html(ucfirst(decodeURIComponent(pname)));
        }
        if (page == "dashboard") {
            updateProj(radio, projId);
            var params = parseUrlHash(urlHash);
            if (params[0] == "details" && $('#easycase_uid').val()) {
                easycase.refreshTaskList($('#easycase_uid').val());
            } else if (params[0] == "activities") {
                easycase.showActivities();
            } else {
                var caseUrl = $("#caseUrl").val();
                if (caseUrl) {
                    window.location = HTTP_ROOT + "dashboard/?project=" + projId;
                    return false;
                }
                if ($('#caseMenuFilters').val() == "files") {
                    easycase.showFiles("files");
                } else {

                    if ($('#caseMenuFilters').val() == 'milestonelist') {
                        $('#milestoneLimit').val('0');
                        $('#totalMlstCnt').val('0');
                        showMilestoneList();
                    } else if ($('#caseMenuFilters').val() == 'milestone') {
                        $('#mlstPage').val(1);
                        ManageMilestoneList();
                    } else if (CONTROLLER == "Apis" && PAGE_NAME == "settings") {
                        window.location = HTTP_ROOT + "dashboard/?project=" + projId;
                    } else {
                        easycase.refreshTaskList();
                    }
                    /*if($('#caseMenuFilters').val()=='kanban'){
                     easycase.showKanbanTaskList('kanban');
                     }else{
                     ajaxCaseView('case_project');
                     }*/
                }
            }
        } else if (page == "time_log") {
            updateProj(radio, projId);
            if (params[0] != '' && params[0] == 'calendar') {
                getCalenderForTimeLog(params[0]);
                return false;
            }
            $('#pagingtable').remove();
            $('#projFil').val(projId);
            $('#logstrtdt').val('');
            $('#logenddt').val('');
            $('#rsrclog').val('');
            var usrhtml = "";

            $.post(HTTP_ROOT + "timelog", {'projFil': projId, 'page': 'log'}, function (data) {
                if (data) {
                    $('.main-container-div').html(data)
                    $('#projFil').val(projId);
                    $('#whosassign1').val(SES_ID);
                    $('#ajaxViewProjects').parent('.dropdown-menu').hide();
                    $('#prj_ahref').hover(function () {
                    });
                    $('[rel=tooltip]').tipsy({
                        gravity: 's',
                        fade: true
                    });
                    footer_update();
                }
            });
        } else if (page == "invoice") {
            // $.post(HTTP_ROOT+"easycases/update_invoice_project",{'projuniqid':projId},function(data) {
            //         showUnbilled();
            // });
            $('#priFil').val("all");
            $('#caseTypes').val("all");
            $('#caseStatus').val("all");
            $('#caseMember').val("all");
            $('#caseAssignTo').val("all");
            $('#caseDateFil').val("");
            $('#casedueDateFil').val("");
            remember_filters('reset', 'all');
            updateProj(radio, projId);
            $.post(HTTP_ROOT + "easycases/update_invoice_project", {'projuniqid': projId}, function (data) {
                if (TLG && TLG == 1) {
                    switch_tab('logtime');
                } else {
                    switch_tab('invoice');
                }
            });
        } else if (page == "milestone") {
            window.location = HTTP_ROOT + 'milestone/?pj=' + projId;
        } else if (page == "import") {
            window.location = HTTP_ROOT + 'projects/importexport/' + projId;
        } else if (page == 'milestonelist') {
            $('#milestoneLimit').val('0');
            $('#totalMlstCnt').val('0');
            updateProj(radio, projId);
            showMilestoneList();
            //window.location.href = HTTP_ROOT + 'milestones/milestonelist';
        } else if (page == "manage") {
            var pjname = $("#pname_dashboard_hid").val();
            $("#milestonelist").hide();
            $("#caseLoader").css("display", "block");
            $('#projFil').val(projId);
            createCookie('prjid', projId, 365, DOMAIN_COOKIE);
            createCookie('pjname', pname, 365, DOMAIN_COOKIE);
            //delete_cookie('mlstnid');
            var ganttprjcookie = getCookie('prjid');
            if (ganttprjcookie) {
                projId = ganttprjcookie;
            }
            $.post(
                    HTTP_ROOT + "Ganttchart/get_milestones",
                    {prjid: projId},
            function (res) {
                $("#milestonelist").html(res);
                $("#milestonelist").show();
                $("#caseLoader").css("display", "none");
            }
            );
        } else if (page == 'ganttv2') {
            var pjname = $("#pname_dashboard_hid").val();
            $("#milestonelist").hide();
            $("#caseLoader").css("display", "block");
            $('#projFil').val(projId);
            createCookie('prjid', projId, 365, DOMAIN_COOKIE);
            createCookie('pjname', pname, 365, DOMAIN_COOKIE);
            //delete_cookie('mlstnid');
            var ganttprjcookie = getCookie('prjid');
            if (ganttprjcookie) {
                projId = ganttprjcookie;
            }

            var prof = new Profiler("loadServerSide");
            prof.reset();

            $.ajax(HTTP_ROOT + "ganttchart/Ganttchart/ganttv2_ajax", {
                dataType: "json",
                data: {CM: "LOADPROJECT", prjid: projId},
                type: "POST",
                success: function (response) {
                    if (response.ok) {
                        if (response.project.tasks.length > 0) {
                            $('#workSpace_img').hide();
                            $("#workSpace,.ganttButtonBar").show();
                        } else {
                            $("#workSpace,.ganttButtonBar").hide();
                            $('#workSpace_img').show();
                        }
                        prof.stop();

                        ge.loadProject(response.project);
                        ge.checkpoint(); //empty the undo stack

                        if (typeof (callback) == "function") {
                            callback(response);
                        }
                        updateTaskStatusGroup(response.task_status_list);
                    } else {
                        jsonErrorHandling(response);
                    }
                }
            });
        } else {
            window.location = HTTP_ROOT + 'dashboard/?project=' + projId;
            $('#projFil').val(projId);
        }

        footer_update();
    } else {
        //document.getElementById('projpopup').style.display = 'none';
        $("#find_prj_dv").hide();
        $('#prj_drpdwn').removeClass("open");
        $(".dropdown-menu.lft").hide();
        $('#ajaxCaseStatus').hide();

        if (pname) {
            var fst = $('#pname_dashboard a').html();
            var secnd = $('#first_recent a').html();
            var thrd = $('#second_recent a').html();

            var cnt = "<a href='javascript:void(0);' onClick='updateAllProj(\"" + radio + "\",\"" + projId + "\",\"" + page + "\",\"" + all + "\",\"" + pname + "\",\"" + srch + "\");'>" + shortLength(decodeURIComponent(pname), 20) + "</a>";

            if (secnd == decodeURIComponent(pname)) {
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);
            } else if (thrd == decodeURIComponent(pname)) {
                $('#second_recent').html($('#first_recent').html());
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);
            } else if (fst == decodeURIComponent(pname)) {
                $('#pname_dashboard').html(cnt);
            } else {
                $('#second_recent').html($('#first_recent').html());
                $('#first_recent').html($('#pname_dashboard').html());
                $('#pname_dashboard').html(cnt);
            }
            // $('#pname_dashboard').html(decodeURIComponent(pname));
            $('.pname_dashboard').html(ucfirst(decodeURIComponent(pname)));
        }
        if (page == "dashboard") {
            updateProj1('all');
            if ($('#caseMenuFilters').val() == "files") {
                easycase.showFiles("files");
                //ajaxFileView('case_files');
            } else {
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    $('#milestoneLimit').val('0');
                    $('#totalMlstCnt').val('0');
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'milestone') {
                    $('#mlstPage').val(1);
                    ManageMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList('kanban');
                } else if ($('#caseMenuFilters').val() == 'calendar') {
                    calendarView('calendar');
                } else if ($('#caseMenuFilters').val() == "activities") {
                    easycase.showActivities();
                } else {
                    ajaxCaseView('case_project');
                }
            }
        } else if (page == 'milestonelist') {
            updateProj1('all');
            $('#milestoneLimit').val('0');
            $('#totalMlstCnt').val('0');
            showMilestoneList();
        } else if (page == "time_log") {
            //  updateProj("", all);
            createCookie("All_Project", "all", 365, "");
            // console.log(getCookie('All_Project'));
            updateProj1('all');
            if (params[0] != '' && params[0] == 'calendar') {
                getCalenderForTimeLog(params[0]);
                return false;
            }
            $('#pagingtable').remove();
            $('#projFil').val('all');
            $('#logstrtdt').val('');
            $('#logenddt').val('');
            $('#rsrclog').val('');
            var usrhtml = "";

            $.post(HTTP_ROOT + "timelog", {'projFil': "all", 'page': 'log'}, function (data) {
                if (data) {
                    $('.main-container-div').html(data)
                    $('#projFil').val(all);
                    $('#whosassign1').val(SES_ID);
                    $('#ajaxViewProjects').parent('.dropdown-menu').hide();
                    $('#prj_ahref').hover(function () {
                    });
                    $('[rel=tooltip]').tipsy({
                        gravity: 's',
                        fade: true
                    });
                    $('.pname_dashboard').html('All');
                    footer_update();
                }
            });
        } else {
            window.location = HTTP_ROOT + 'easycases/dashboard/?project=all';
        }
    }
}
function footer_update(proj_id) {
    var projFil = typeof proj_id != 'undefined' ? proj_id : $('#projFil').val();
    var url = HTTP_ROOT + "easycases/";
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
}
function CaseActivity(pjid, pname) {
    $('#pname_dashboard').html(decodeURIComponent(pname));
    $('#prjid').val(pjid);

    $('#projpopup').hide();
    $("#find_prj_dv").hide();
    $('#prj_drpdwn').removeClass("open");
    $(".dropdown-menu.lft").hide();

    $("#activities").html('');
    $("#moreloader").show();
    loadActivity('');
    loadOverdue('my');
    loadUpcoming('my');
}

function updateProj(id, uniq) {
    document.getElementById('projFil').value = uniq;
    document.getElementById('CS_project_id').value = uniq;
    $("#ajaxViewProjects").slideUp(300);
    $('#curr_active_project').val(uniq);
}

function updateProj1(all) {
    document.getElementById('projFil').value = 'all';
    $("#ajaxViewProjects").slideUp(300);
}

function displayMenuProjects(page, limit, filter) {
    var strURL = HTTP_ROOT + "users/project_menu";
    if (limit == "all") {
        $('#showMenu_case_txt').hide();
        $('#loaderMenu_case').show();
    }

    $.post(strURL, {
        "page": page,
        "limit": limit,
        "filter": filter
    }, function (data) {
        if (data) {
            $('#ajaxViewProjects').html(data);
        }
    });
}
//Switch Project dropdown ends
//Tab Bucket starts
function caseMenuFileter(value, page, filters, caseid) {
    //setMenuClass(value);//Not impl
    var url = HTTP_ROOT;
    var durl = document.URL;
    if (page == "dashboard") {
        //document.getElementById('casePage').value = 1;
        casePage = 1;
        document.getElementById('caseMenuFilters').value = value;
        /*if (value == "files") {//Not impl
         if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
         window.location = url + "dashboard?filters=" + value;
         } else {
         ajaxFileView('case_files');
         document.getElementById('pageheading').innerHTML = 'Files';
         }
         
         }
         else*/ if (value == "assigntome") {
            if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
                window.location = url + "dashboard?filters=" + value;
            } else {
                ajaxCaseView('case_project');
                //document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'Assigned To Me';
            }
        }
        /*else if (value == "closecase") {//Not Impl
         resetAllFilters('filters');
         if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
         window.location = url + "dashboard?filters=" + value;
         } else {
         ajaxCaseView('case_project');
         document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'Closed';
         }
         }*/
        else if (value == "overdue") {
            //resetAllFilters('filters');
            if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
                window.location = url + "dashboard?filters=" + value;
            } else {
                ajaxCaseView('case_project');
                //document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'Bug';
            }
        } else if (value == "delegateto") {
            if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
                window.location = url + "dashboard?filters=" + value;
            } else {
                ajaxCaseView('case_project');
                //document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'Delegated To Others';
            }
        }
        /*else if (value == "latest") { //Not impl
         if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
         window.location = url + "dashboard?filters=" + value;
         } else {
         ajaxCaseView('case_project');
         document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'Recent';
         }
         
         }*/
        else if (value == "highpriority") {
            if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
                window.location = url + "dashboard?filters=" + value;
            } else {
                ajaxCaseView('case_project');
                //document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'High Priority';
            }

        }
        /*else if (value == "milestone") { //Not impl
         if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
         window.location = url + "dashboard?filters=" + value;
         } else {
         ajaxCaseView('case_project');
         document.getElementById('pageheading').innerHTML = 'Milestone';
         }
         }*/
        else {
            if ((durl.indexOf('?case=') != -1) && (durl.indexOf('&project=') != -1)) {
                window.location = url + "dashboard?filters=" + value;
            } else {
                ajaxCaseView('case_project');
                //document.getElementById('pageheading').innerHTML = 'Tasks' + img + 'All';
            }

        }

        strUrl = url + "easycases/";
        var projFil = document.getElementById('projFil').value;
    } else {
        if (value) {
            window.location = url + "dashboard?filters=" + value;
        } else { //alert('dashboard');
            window.location = url + "dashboard";
        }
    }
}

function newcategorytab() {
    $('#inner_select_tab').html('');
    openPopup();
    $(".popup_bg").css({
        "width": '575px'
    });
    $(".select_tab").show();

    $.post(HTTP_ROOT + "users/categorytab", function (res) {
        $(".loader_dv").hide();
        $('#inner_select_tab').html(res);
    });
}

function savecategorytab() {
    var total_tab_value = 0;
    $('.cattab_cls').each(function () {
        if ($(this).is(':checked')) {
            total_tab_value += parseInt($(this).val());
        }
    });
    $("#btn_cattype").hide();
    $("#tab_ldr").show();
    $.post(HTTP_ROOT + "users/ajax_savecategorytab", {
        'tabvalue': total_tab_value,
        'is_ajaxflag': 1
    }, function (res) {
        if (parseInt(res) === 1) {
            window.location.href = HTTP_ROOT + 'dashboard?filters=cases';
        }
    });
}

$('.ctab_td').hover(function () {
    $(this).css({
        'background': '#EBE8E8'
    });
}, function () {
    $(this).css({
        'background': ''
    });
});
//Tab Bucket ends
//Profile Starts
function submitProfile() {
    var name1 = $('#profile_name').val().trim();
    var last_name = $('#profile_last_name').val().trim();
    var short_name = $('#short_name').val().trim();
    var errMsg;
    var done = 1;
    if (name1 == "") {
        errMsg = _("First Name cannot be left blank!");
        $('#profile_name').focus();
        done = 0;
    } else if (last_name == "") {
        errMsg = _("Last Name cannot be left blank!");
        $('#profile_last_name').focus();
        done = 0;
    } else if (short_name == "") {
        errMsg = _("Short Name cannot be left blank!");
        $('#short_name').focus();
        done = 0;
    }

    if (done == 0) {
        var op = 100;
        showTopErrSucc('error', errMsg);
        return false;
    } else {
        $('#subprof1').hide();
        $('#subprof2').show();
    }
}

function cancelProfile(url) {
    window.location.href = url;
}

function checkPasswordMatch(a, b, c, d) {
    var errMsg;
    var done = 1;
    if (d == 0) {
        var pass = $("#" + c).val();
        if (pass.trim() != "") {
            var pass_new = $("#" + a).val();
            var retypr_pass = $("#" + b).val();
            if (pass_new.trim() == "") {
                errMsg = _("Password cannot be  blank!");
                document.getElementById(a).focus();
                done = 0;
            }
            else if (pass_new.trim().length < 6) {
                errMsg = "Password should be at least 6 characters!";
                document.getElementById(a).focus();
                done = 0;
            }
            /*else if (pass_new.length > 15) {
             errMsg = "Password should be between 6-15 characters!";
             document.getElementById(a).focus();
             done = 0;
             }*/
            else if (retypr_pass.trim() == "") {
                errMsg = _("Confirm Password cannot be  blank!");
                document.getElementById(b).focus();
                done = 0;
            } else if (retypr_pass.trim() != pass_new.trim()) {
                errMsg = _("Passwords do not match!");
                document.getElementById(b).focus();
                done = 0;
            } else {
                document.getElementById('subprof2').style.display = 'block';
                document.getElementById('subprof1').style.display = 'none';
            }
        } else {
            errMsg = _("Old Password cannot be left blank!");
            document.getElementById(c).focus();
            done = 0;
        }
    } else {
        var pass_new = $("#" + a).val();
        var retypr_pass = $("#" + b).val();
        if (pass_new.trim() == "") {
            errMsg = _("Password cannot be  blank!");
            document.getElementById(a).focus();
            done = 0;
        }
        else if (pass_new.trim().length < 6) {
            errMsg = "Password should be at least 6 characters!";
            document.getElementById(a).focus();
            done = 0;
        }
        /*else if (pass_new.length > 15) {
         errMsg = "Password should be between 6-15 characters!";
         document.getElementById(a).focus();
         done = 0;
         }*/
        else if (retypr_pass.trim() == "") {
            errMsg = _("Confirm Password cannot be  blank!");
            document.getElementById(b).focus();
            done = 0;
        } else if (retypr_pass.trim() != pass_new.trim()) {
            errMsg = _("Passwords do not match!");
            document.getElementById(b).focus();
            done = 0;
        } else {
            document.getElementById('subprof2').style.display = 'block';
            document.getElementById('subprof1').style.display = 'none';
        }
    }
    if (done == 0) {
        showTopErrSucc('error', errMsg);
        return false;
    }
}
//Profile Ends

//Profile image Starts
function openProfilePopup() {
    $("#upldphoto").trigger('click');
    $('#prof_ttl_id').html("Profile Image");
    if (arguments[0]) {
        $('#prof_ttl_id').html("Company Logo");
        $('#comp_logo').val(arguments[0]);
    }
}

function loadprofilePopup() {
    $(".popup_overlay").css({
        display: "block"
    });
    $(".popup_bg").css({
        display: "block"
    });
    $(".prof_img").show();

    //$('#up_files1').html('');
    $('#up_files_usr').html('');
    $("#actConfirmbtn").hide();
    $("#inactConfirmbtn").show();
}

function showEditDeleteImg() {
    $("#editDeleteImg").show();
}
function hideEditDeleteImg() {
    $("#editDeleteImg").hide();
}
function profilePopupCancel() {
    $('#profilephoto').imgAreaSelect({
        hide: true
    });
    //$('#up_files1').html('');
    $('#up_files_usr').html('');
    closePopup();
}

$(function () {
    $('#upldphoto').change(function () {
        profilePopupClose();
        var ext = this.value.match(/\.(.+)$/)[1].toLowerCase();
        if ($.inArray(ext, ["jpg", "jpeg", "png", "gif", "bmp"]) == -1) {
            alert(_("Sorry") + ", '" + ext + "' " + _("file type is not allowed!"));
            this.value = '';
        } else {
            loadprofilePopup();
            $("#inactConfirmbtn").hide();
            $("#actConfirmbtn").show();
            $("#profLoader").show();
        }
    });
    $('#file_upload1').fileUploadUI({
        //uploadTable: $('#up_files1'),
        //downloadTable: $('#up_files1'),
        uploadTable: $('#up_files_usr'),
        downloadTable: $('#up_files_usr'),
        buildUploadRow: function (files, index) {
            var filename = files[index].name;
            if (filename.length > 35) {
                filename = filename.substr(0, 35);
            }
        },
        buildDownloadRow: function (file) {
            if (file.name != "error") {
                if (file.message == "success") {
                    var filesize = file.sizeinkb;
                    if (filesize >= 1024) {
                        filesize = filesize / 1024;
                        filesize = Math.round(filesize * 10) / 10;
                        filesize = filesize + " Mb";
                    } else {
                        filesize = Math.round(filesize * 10) / 10;
                        filesize = filesize + " Kb";
                    }

                    var imgNm = HTTP_ROOT + "files/profile/orig/" + file.filename;
                    //$('#up_files1').html('<img src="' + imgNm + '" id="profilephoto">');
                    $(' #up_files_usr').html('<img src="' + imgNm + '" id="profilephoto">');
                    $("#imgName1").val(file.filename);
                    $("#profLoader").hide();
                    $('#profilephoto').imgAreaSelect({
                        handles: true,
                        instance: true,
                        x1: 10,
                        y1: 20,
                        x2: 60,
                        y2: 60,
                        fadeSpeed: 500,
                        aspectRatio: '1:1',
                        minHeight: 80,
                        minWidth: 80,
                        maxHeight: 170,
                        maxWidth: 170,
                        //show:true,
                        onInit: setInfo,
                        onSelectChange: setInfo
                    });

                } else if (file.message == "small size image") {
                    alert(_("The image you tried to upload is too small. It needs to be at least 100 pixels wide.\nPlease try again with a larger image."));
                    $("#profLoader").hide();
                    $("#inactConfirmbtn").show();
                    $("#actConfirmbtn").hide();
                } else if (file.message == "exceed") {
                    alert(_("Error uploading file.\nStorage usage exceeds by") + " " + file.storageExceeds + " Mb!");
                } else if (file.message == "size") {
                    alert(_("Error uploading file. File size cannot be more then") + " " + fmaxilesize + " Mb!");
                } else if (file.message == "error") {
                    alert(_("Error uploading file. Please try with another file"));
                } else if (file.message == "s3_error") {
                    alert(_("Due to some network problem your file is not uploaded.Please try again after sometime."));
                } else {
                    alert(_("Sorry") + ", " + file.message + " " + _("file type is not allowed") + "!");
                }
            } else {
                alert(_("Error uploading file. Please try with another file"));
            }
        }
    });
});
function setInfo(i, e) {
    $('#x').val(e.x1);
    $('#y').val(e.y1);
    $('#w').val(e.width);
    $('#h').val(e.height);
}
function preview(img, selection) {
    if (!selection.width || !selection.height)
        return;

    $('#x').val(selection.x1);
    $('#y').val(selection.y1);
    $('#x2').val(selection.x2);
    $('#y2').val(selection.y2);
    $('#w').val(selection.width);
    $('#h').val(selection.height);
}
function profilePopupClose() {
    $('#profilephoto').imgAreaSelect({
        hide: true
    });
    //$('#up_files1').html('');
    $('#up_files_usr').html('');
    closePopup();
}

function doneCropImage() {
    var x = $('#x').val();
    var y = $('#y').val();
    var width = $('#w').val();
    var height = $('#h').val();
    var imgName = $("#imgName1").val();
    $('#file_confirm_btn_loader').show();
    $('.file_confirm_btn').hide();
    if (width != 0 && height != 0 && imgName.trim() != '') {
        $.post(HTTP_ROOT + "users/done_cropimage", {
            'x-cord': x,
            'y-cord': y,
            'width': width,
            'height': height,
            'imgName': imgName
        }, function (res) {
            if (res) {
                profilePopupClose();
                /*if ($("#existProfImg").length) {
                 $("#existProfImg").hide();
                 }*/
                $("#defaultUserImg").hide();
                $('#profilephoto').imgAreaSelect({
                    hide: true
                });
                //$("#profDiv").html('');
                //$("#profDiv").html('<img src="' + url + 'files/profile/thumb/' + res + '" alt="testimg">');
                $("#imgName1").val(res);
                if (PAGE_NAME == 'mycomapny') {
                    $("#submit_Pass").trigger('click');
                } else {
                    $("#submit_Profile").trigger('click');
                }
                $('#file_confirm_btn_loader').hide();
                $('.file_confirm_btn').show();
            }
        });
    }
}
//Profile image ends

function checkuserlogin() {
    setInterval('checkloginstatus()', 5000);
}
function checkloginstatus() {
    if (!getCookie('USER_UNIQ') || !getCookie('USERTYP') || !getCookie('USERTZ')) {
        window.location.href = HTTP_ROOT + 'users/logout/';
    }
}
String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}

//Manage project Starts
$(".icon-edit-usr").click(function () {
    var prj_id = $(this).attr('data-prj-id');
    var prj_name = $(this).attr('data-prj-name');
    openPopup();
    $(".edt_prj").show();
    $("#header_prj").html(prj_name);
    $('#inner_prj_edit').hide();
    var url = HTTP_ROOT;
    if (TSG == 1) {
        url += "taskstatusgroup/Workflows/ajax_edit_project";
    } else {
        url += "projects/ajax_edit_project";
    }
    $.post(url, {
        "pid": prj_id
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#inner_prj_edit').show();
            $('#inner_prj_edit').html(data);
        }
    });
});

function viewTemplateCases() {
    var temp_id = $('#sel_Typ').val();
    var url = HTTP_ROOT + "templates/view_templates/" + temp_id;
    var win = window.open(url, '_blank');
    win.focus();
}
function EditTaskTemp(tempId, tasktempname, pagenum) {
    openPopup();
    $(".edt_task_temp").show();
    $("#header_task_temp").html(tasktempname);
    $('#inner_task_temp_edit').hide();
    $(".loader_dv_task").show();
    $.post(HTTP_ROOT + "templates/ajax_add_task_template", {
        "tempId": tempId,
        "pagenum": pagenum
    }, function (data) {
        if (data) { //alert(data['CaseTemplate']['id']);
            $(".loader_dv_task").hide();
            $("#tasktemptitle_edit").val(data['CaseTemplate']['name']);
            $("#desc_edit").val(data['CaseTemplate']['description']);
            $("#hid_edit_id").val(data['CaseTemplate']['id']);
            $("#hid_edit_user_id").val(data['CaseTemplate']['user_id']);
            $("#hid_edit_company_id").val(data['CaseTemplate']['company_id']);
            $("#hid_edit_page_num").val(data['CaseTemplate']['pageNum']);
            $('#inner_task_temp_edit').show();
            $(".edt_task_temp").show();
            $(".popup_bg").css({
                "width": '850px'
            });
            //$('#inner_prj_edit').html(data);
        }
    }, 'json');
}
function submitProject(proj, shrt) {
    var done = 1;
    var msg = "";
    var proj_name = document.getElementById(proj).value.trim();
    var short_name = document.getElementById(shrt).value.trim();
    if (proj_name == "") {
        msg = _("'Project Name' cannot be left blank!");
        document.getElementById(proj).focus();
        done = 0;
    } else if (!proj_name.match(/^[A-Za-z0-9]/g)) {
        msg = _("'Project Name' must starts with an Alphabet or Number!");
        document.getElementById(proj).focus();
        done = 0;
    }

    var patern = /^[a-z0-9]+$/i;
    if (short_name == "") {
        msg = _("'ShortName' cannot be left blank!");
        document.getElementById(shrt).focus();
        done = 0;
    } else if (!short_name.match(/^[a-z0-9]+$/i)) {
        msg = _("'ShortName' must be alphanumeric!");
        document.getElementById(shrt).focus();
        done = 0;
    } else {
        var x = short_name.substr(-1);
        /*if (!isNaN(x)) {
            msg = _("'ShortName' cannot have numbers at the end!");
            document.getElementById(shrt).focus();
            done = 0;
        }*/
    }

    if (done == 0) {
        showTopErrSucc('error', msg);
        return false;
    }
    var uniqid = $("#uniqid").val();
    $("#btn").css({
        "visibility": "hidden"
    });
    $("#settingldr").css({
        "display": "block"
    });

    $(".project_edit_button").hide();

    $.post(HTTP_ROOT + "projects/ajax_check_project_exists", {
        "uniqid": uniqid,
        "name": escape(document.getElementById(proj).value.trim()),
        "shortname": escape(document.getElementById(shrt).value.trim())
    }, function (data) {
        if (data == "Project") {
            $("#btn").css({
                "visibility": "visible"
            });
            $(".project_edit_button").show();
            $("#btn").show();
            $("#settingldr").hide();
            msg = _("'Project Name' is already exists!");
            showTopErrSucc('error', msg);
            document.getElementById(proj).focus();
            return false;
        } /*else if (data == "ShortName") {
            $("#btn").css({
                "visibility": "visible"
            });
            $("#btn").show();
            $("#settingldr").hide();
            msg = _("'Project Short Name' is already exists!");
            showTopErrSucc('error', msg);
            document.getElementById(shrt).focus();
            return false;
        } */else {
            $("#pg").val($(".button_page").html());
            $("#validateprj").val('1');
            document.projsettings.submit();
            return true;
        }
    });
    return false;
}

$(".del_prj").click(function () {
    var prj_unq_id = $(this).attr('data-prj-id');
    var prj_nm = $(this).attr('data-prj-name');
    if (confirm(_("Are you sure to delete project") + " '" + prj_nm + "'")) {
        if (confirm(_("All the Tasks, Files associated with") + " '" + prj_nm + "' " + _("will be deleted permanently") + ".")) {
            var pg = $(".button_page").html();
            var loc = HTTP_ROOT + "projects/deleteprojects/" + prj_unq_id;
            if (parseInt(pg) > 1) {
                loc = loc + "/" + pg;
            }
            window.location = loc;
        } else {
            return false;
        }
    } else {
        return false;
    }
});

$(".enbl_prj").click(function () {
    var prj_id = $(this).attr('data-prj-id');
    var prj_name = $(this).attr('data-prj-name');
    var conf = confirm(_("Are you sure you want to enable") + " '" + prj_name + "' ?");
    if (conf == true) {
        var pg = $(".button_page").html();
        var loc = HTTP_ROOT + 'projects/gridview/?id=' + prj_id + '&action=activate';
        if (parseInt(pg) > 1) {
            loc = loc + "&pg=" + pg;
        }
        window.location = loc;
    } else {
        return false;
    }
});

$(".disbl_prj").click(function () {
    var prj_id = $(this).attr('data-prj-id');
    var prj_name = $(this).attr('data-prj-name');
    var conf = confirm(_("Are you sure you want to disable") + " '" + prj_name + "' ?");
    if (conf == true) {
        var pg = $(".button_page").html();
        var loc = HTTP_ROOT + 'projects/gridview/?id=' + prj_id + '&action=deactivate';
        if (parseInt(pg) > 1) {
            loc = loc + "&pg=" + pg;
        }
        window.location = loc;
    } else {
        return false;
    }
});

$(".icon-remove-usr").click(function () {
    var prj_id = $(this).attr('data-prj-id');
    var prj_name = $(this).attr('data-prj-name');
    openPopup();
    $(".rmv_prj_usr").show();
    $("#header_prj_usr_rmv").html(prj_name);
    $('#inner_prj_usr_rmv').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    $('.rmv-btn').hide();
    $('#rmname').val('');
    $('#remusersrch').hide();
    $.post(HTTP_ROOT + "projects/user_listing", {
        "project_id": prj_id
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#inner_prj_usr_rmv').show();
            $('#inner_prj_usr_rmv').html(data);
            if (parseInt($("#is_users").val())) {
                $('.rmv-btn').show();
                $('#remusersrch').show();
                enableAddUsrBtns('.rem-usr-prj');
            }
        }
    });
});

function removeusers() {
    var done = 0;
    var user_name = '';
    var remove_prj_name = $("#header_prj_usr_rmv").text();
    $('#inner_prj_usr_rmv input:checked:not(":disabled")').each(function () {
        if ($(this).attr('id') !== 'remcheckAll') {
            user_name = user_name + ", " + $(this).attr('data-usr-name');
            done++;
        }
    });
    user_name = user_name.replace(', ', '');
    if (done) {
        if (confirm(_("Are you sure you want to remove") + " '" + user_name + "' " + _("from") + " '" + remove_prj_name + "'?")) {
            var project_id = $('#pjid').val();
            $('#inner_prj_usr_rmv input:checked:not(":disabled")').each(function () {
                if ($(this).attr('id') !== 'remcheckAll') {
                    var listid = $(this).attr('id');
                    var userid = $(this).attr('value');
                    var listing = $("#" + listid).parents("tr").attr('id');

                    $("#" + listing).fadeOut(1000);
                    $("#" + listid).attr("checked", false);
                    var is_invite = '';
                    var dcrs_cnt = 1;
                    if ($("#" + listing).hasClass("invited-cls")) {
                        is_invite = "InvitedUser";
                        dcrs_cnt = 0;
                    }

                    if ($("#" + listing).hasClass("disable-cls")) {
                        dcrs_cnt = 0;
                    }
                    var strURL = HTTP_ROOT + 'projects/user_listing';
                    $.post(strURL, {
                        "InvitedUser": is_invite,
                        "userid": userid,
                        "project_id": project_id
                    }, function (data) {
                        if (data) {
                            $("#" + listing).remove();
                        }
                        enableAddUsrBtns('.rem-usr-prj');
                    });
                    if (parseInt(dcrs_cnt)) {
                        var total_user = parseInt($("#tot_prjusers" + project_id).text()) - 1;
                        $("#tot_prjusers" + project_id).html(total_user);
                        if (parseInt(total_user) == 0) {
                            $("#remove" + project_id).hide();
                            $("#ajax_remove" + project_id).hide();
                            closePopup();
                        }
                    }
                }
            });
            showTopErrSucc('success', _("User(s)") + " '" + user_name + "' " + _("removed from") + " '" + remove_prj_name + "'");
        } else {
            return false;
        }
    }
}

function selectremuserAll(arg, i) {
    if (parseInt(arg)) {
        if ($('#remcheckAll').is(":checked")) {
            $(".rem-usr-prj").attr("checked", "checked");
            $('.rw-cls').css({
                'background-color': '#FFFFCC'
            });
        } else {
            $(".rem-usr-prj").attr("checked", false);
            $('.rw-cls').css({
                'background-color': ''
            });
        }
    } else {
        var listing = "listing" + i;
        if ($('.rem-usr-prj:checked').length == $('.rem-usr-prj').length) {
            $("#remcheckAll").attr("checked", "checked");
            $('#' + listing).css({
                'background-color': '#FFFFCC'
            });
        } else {
            $("#remcheckAll").attr("checked", false);
            if ($('#usCheckBox' + i).is(":checked")) {
                $('#' + listing).css({
                    'background-color': '#FFFFCC'
                });
            } else {
                $('#' + listing).css({
                    'background-color': ''
                });
            }
        }
    }
    enableAddUsrBtns('.rem-usr-prj');
}

function searchremuserkey() {
    var name = $('#rmname').val();
    var project_id = $('#pjid').val();
    if (project_id) {
        var strURL1 = HTTP_ROOT + 'projects/user_listing';
        $('#popupload2').css({
            "top": "48px"
        });
        $('#popupload2').show();
        $.post(strURL1, {
            "project_id": project_id,
            "name": name
        }, function (data) {
            if (data) {
                $('#popupload2').hide();
                $('#inner_prj_usr_rmv').html(data);
                enableAddUsrBtns('.rem-usr-prj');
            }
        });
    }
}

function setemail(obj, type, id, type2) {
    var strURL = HTTP_ROOT + 'projects/update_email_notification';
    $.post(strURL, {
        "type": type,
        "projectuser_id": id
    }, function (data) {
        if (data) {
        }
    });
    $(obj).parent("li").siblings("li").removeClass(type2);
    $(obj).parent("li").addClass(type);
}

$(".icon-add-usr").click(function () {
    var prj_id = $(this).attr('data-prj-id');
    var prj_name = $(this).attr('data-prj-name');
    add_user(prj_id, prj_name);
});

function add_user(prj_id, prj_name) {
    openPopup();
    $("#userList").html('');
    hduserid = new Array();
    $(".add_prj_usr").show();
    $("#header_prj_usr_add").html(prj_name);
    $('#inner_prj_usr_add').hide();
    $('.add-usr-btn').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    /*   $(".popup_form").css({
     "margin-top": "6px"
     }); */
    $("#name").val('');
    $('#usersrch').hide();
    $.post(HTTP_ROOT + "projects/add_user", {
        "pjid": prj_id,
        "pjname": prj_name
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#usersrch').show();
            $('#inner_prj_usr_add').show();
            $('#inner_prj_usr_add').html(data);
            $('.add-usr-btn').show();
            enableAddUsrBtns('.ad-usr-prj');
        }
    });
}

$(".create_project_temp").click(function () {
    openPopup();
    $("#projtemptitle").val('');
    $("#projtemptitle").focus();
    $("#project_temp_err").html('');
    $(".project_temp_popup").show();
});

$(".create_task_temp").click(function () {
    openPopup();
    $("#task_temp_err").html('');
    $("#tasktemptitle").val('');
    $("#desc").val('');
    $("#tasktemptitle").focus();
    $(".task_temp_popup").show();
    $(".popup_bg").css({
        "width": '850px'
    });
});

function searchListWithInt(type, int) {
    if (_searchInterval) {
        clearInterval(_searchInterval);
    }
    if (!int) {
        int = 1000;
    }
    _searchInterval = setTimeout(function () {
        if (type == 'searchuser') {
            searchuserkey();
        } else if (type == 'searchuserrem') {
            searchremuserkey();
        } else if (type == 'searchproj') {
            searchprojkey();
        } else if (type == 'searchprojrem') {
            searchremprjkey();
        }
    }, int);
}

function searchuserkey() {
    var name = $('#name').val();
    var project_id = '';
    try {
        var project_id = $('#projectId').val();
        var pjname = $('#project_name').val();
        var cntmng = $('#cntmng').val();
    } catch (e) {
    }
    if (project_id) {
        var strURL1 = HTTP_ROOT + 'projects/add_user';
        $("#popupload1").show();
        $.post(strURL1, {
            "pjname": pjname,
            "pjid": project_id,
            "name": name,
            "cntmng": cntmng
        }, function (data) {
            if (data) {
                $('#inner_prj_usr_add').html(data);
                $("#popupload1").hide();
                $("#popupContactClose, .c_btn").click(function () {
                    disablePopup();
                });
                enableAddUsrBtns('.ad-usr-prj');
            }
        });
    }
}

function assignuser(el) {
    var userid = new Array();
    var done = 0;
    var cntmng = $('#cntmng').val();
    var page_name = $('#pagename').val();

    userid = hduserid;
    if (hduserid.length != 0) {
        done++;
    }
    if (done) {
        $(".chkbx_cur").prop("disabled", true);
        var tot = userid.length;
        var pjid = $('#projectId').val();
        var pjname = $('#project_name').val();
        var strURL = HTTP_ROOT + 'projects/assign_userall';
        $('#confirmbtn').hide();
        $('#userloader').show();
        $.post(strURL, {
            "userid": userid,
            "pjid": pjid
        }, function (data) {
            if (data == "success") {
                var total_user = parseInt(userid.length) + parseInt($("#tot_prjusers" + pjid).text());
                $("#tot_prjusers" + pjid).html(total_user);
                $('#userloader').hide();
                $('#confirmbtn').show();
                if (parseInt(total_user) > 0) {
                    $("#remove" + pjid).hide();
                    $("#ajax_remove" + pjid).show();
                }
                $("#userList").html('');
                hduserid = new Array();
                showTopErrSucc('success', tot + ' ' + _("User(s) added successfully"));

                if (el && el.id == "confirmuserbut") {
                    $('#name').val('');
                    var strURL1 = HTTP_ROOT + 'projects/add_user';
                    $("#popupload").show();
                    $.post(strURL1, {
                        "pjid": pjid,
                        "pjname": pjname,
                        "cntmng": cntmng
                    }, function (data) {
                        if (data) {
                            $("#popupload").hide();
                            $('#inner_prj_usr_add').html(data);

                            if (page_name == 'dashboard') {
                                ajaxCaseView();
                            }
                            enableAddUsrBtns('.ad-usr-prj');
                        }
                    });
                } else {
                    closePopup();
                    if (page_name == 'onbording') {
                        window.location.reload();
                    }
                }
            }
        });
    }
}
var hduserid = new Array();
function selectuserAll(arg, i, usernm) {
    if (parseInt(arg)) {
        if ($('#checkAll').is(":checked")) {
            $(".ad-usr-prj").attr("checked", "checked");
            $('.rw-cls').css({
                'background-color': '#FFFFCC'
            });
            $("#userList").html('');
            $('#inner_prj_usr_add input:checked').each(function () {
                if ($(this).attr('id') !== 'checkAll') {
                    var id = $(this).attr('value');
                    var userArr = id.split('@@|@@');
                    var user_id = userArr[0];
                    var user_name = decodeURIComponent(userArr[1].replace(/\+/g, ' '));

                    var exstId = $("#userList").find('li[id="' + user_id + '"]').length;
                    if (exstId == 0) {
                        $("#userList").append('<li class="bit-box" rel="7" id="' + user_id + '">' + user_name + '<a class="closebutton" id="close' + user_id + '" href="javascript:void(0);" onclick="removeUserName(\'' + user_id + '\',\'' + $(this).attr('id') + '\');"></a></li>');
                        hduserid.push(user_id);
                    }
                }
            });
        } else {
            $(".ad-usr-prj").attr("checked", false);
            $('.rw-cls').css({
                'background-color': ''
            });
            hduserid = new Array;
            $("#userList").html('');
        }
    } else {
        var listing = "listing" + i;
        if ($('.ad-usr-prj:checked').length == $('.ad-usr-prj').length) {
            $("#checkAll").attr("checked", "checked");
            $('#' + listing).css({
                'background-color': '#FFFFCC'
            });
            $('#inner_prj_usr_add input:checked').each(function () {
                if ($(this).attr('id') !== 'checkAll') {
                    var id = $(this).attr('value');
                    var userArr = id.split('@@|@@');
                    var user_id = userArr[0];
                    var user_name = decodeURIComponent(userArr[1].replace(/\+/g, ' '));

                    var exstId = $("#userList").find('li[id="' + user_id + '"]').length;
                    if (exstId == 0) {
                        $("#userList").append('<li class="bit-box" rel="7" id="' + user_id + '">' + user_name + '<a class="closebutton" id="close' + user_id + '" href="javascript:void(0);" onclick="removeUserName(\'' + user_id + '\',\'' + $(this).attr('id') + '\');"></a></li>');
                        hduserid.push(user_id);
                    }
                }
            });
        } else {
            var id = $("#actionChk" + i).val();
            var userArr = id.split('@@|@@');
            var user_id = userArr[0];
            var user_name = decodeURIComponent(userArr[1].replace(/\+/g, ' '));
            $("#checkAll").attr("checked", false);
            if ($('#actionChk' + i).is(":checked")) {
                $('#' + listing).css({
                    'background-color': '#FFFFCC'
                });
                if ($("#actionChk" + i).is(":checked")) {
                    var exstId = $("#userList").find('li[id="' + user_id + '"]').length;
                    if (exstId == 0) {
                        $("#userList").append('<li class="bit-box" rel="7" id="' + user_id + '">' + user_name + '<a class="closebutton" id="close' + user_id + '" href="javascript:void(0);" onclick="removeUserName(\'' + user_id + '\',\'' + "actionChk" + i + '\');"></a></li>');
                        hduserid.push(user_id);
                    }
                } else {
                    removeUserName(user_id, "actionChk" + i);
                }
            } else {
                $('#' + listing).css({
                    'background-color': ''
                });
                removeUserName(user_id, "actionChk" + i);
            }
        }
    }
    enableAddUsrBtns('.ad-usr-prj');
}

//Manage project Ends
function getPage() {
    var type = "tasks";
    urlHash = getHash();
    if (CONTROLLER == 'projects' && PAGE_NAME == 'manage') {
        type = "projects";
    } else if (CONTROLLER == 'users' && PAGE_NAME == 'manage') {
        type = "users";

    } else if (urlHash == "files") {
        type = "files";
    } else if (urlHash == "milestonelist" || urlHash == 'milestone') {
        type = "milestones";
    } else if (urlHash.substring(0, 6) == 'kanban') {
        type = "kanban";
    }
    return type;
}

//Search for tasks, Projects, Users in header inner starts
var globalTimeout = null;
var globalCount = 0;
var focusedRow = null;
$("#case_search").keyup(function (e) {
    var page = getPage();
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 13 && unicode != 40 && unicode != 38) {

        var srch = $("#case_search").val();
        srch = srch.trim();
        if (srch == "") {
            $('#srch_load1').hide();
        } else {
            if (page != 'milestones') {
                $('#srch_load1').show();
            }
        }
        if (globalTimeout != null)
            clearTimeout(globalTimeout);
        $('#ajax_search').html("");
        focusedRow = null;
        globalCount = 0;
        globalTimeout = setTimeout(ajaxCaseSearch, 1000);
    }
    if (unicode == 40 || unicode == 38) {
        if ($('.ajx-srch-tbl tr').hasClass("alltrcls")) {
            var rowCount = $('.ajx-srch-tbl tr').length;
            if (focusedRow == null) {
                focusedRow = $('tr:nth-child(2)', '.ajx-srch-tbl');
                globalCount = 1;
            } else if (unicode === 38) {
                focusedRow.toggleClass('selctd-srch');
                focusedRow = focusedRow.prev('tr');
                globalCount--;
            } else if (unicode === 40) {
                focusedRow.toggleClass('selctd-srch');
                focusedRow = focusedRow.next('tr');
                globalCount++;
            }
            if ((parseInt(rowCount) == globalCount) || (globalCount == 0)) { //Last row
                focusedRow = $('tr:nth-child(2)', '.ajx-srch-tbl');
                focusedRow.toggleClass('selctd-srch');
                globalCount = 1;
            } else {
                focusedRow.toggleClass('selctd-srch');
            }
        }
    }
});
function ajaxCaseSearch() {
    var srch = $("#case_search").val();
    srch = srch.trim();
    var page = getPage();
    if (srch == "") {
        $('#ajax_search').show();
        return false;
    } else {
        $('#ajax_search').show();
    }

    //$('#casePage').val('1');
    casePage = 1;
    $('#closesrch').hide();

    var pjuniq = $('#projFil').val();

    var url = HTTP_ROOT + "easycases/";
    if (page != 'milestones') {
        $.post(url + "ajax_search", {
            srch: srch,
            page: page,
            pjuniq: pjuniq
        }, function (data) {
            if (data) {
                $('#ajax_search').html(data);
                $('#srch_load1').hide();
                $('#closesrch').hide();
                globalTimeout = null;
            }
        });
    }
}

function searchProject(role, uniq_id, proj_srch) {
    $("#ajax_search").hide();
    if (proj_srch) {
        window.location = HTTP_ROOT + 'projects/manage/?proj_srch=' + proj_srch;
    } else if (uniq_id) {
        window.location = HTTP_ROOT + 'projects/manage/' + role + '?project=' + uniq_id;
    }
}

function searchUser(role, uniq_id, user_srch) {
    $("#ajax_search").hide();
    if (user_srch) {
        window.location = HTTP_ROOT + 'users/manage/?role=all&user_srch=' + user_srch;
    } else if (uniq_id) {
        window.location = HTTP_ROOT + 'users/manage/?role=' + role + '&user=' + uniq_id;
    }
}

function searchTasks(caseno, uniq_id) {
    var url = HTTP_ROOT;
    $("#ajax_search").hide();

    $("#case_search").val("");
    if (caseno.trim() != "") {
        window.location = url + 'dashboard#details/' + uniq_id;
    } else {
        $('#case_search').focus();
    }
}

function searchFile(file_id, uniq_id, file_srch) {
    $("#ajax_search").hide();
    $("#case_search").val("");
    //if (file_id.trim() != "") {
    if (uniq_id.trim() != "") {
        var projFil = uniq_id.trim();
    } else {
        var projFil = $('#projFil').val();
    }
    var strURL = HTTP_ROOT + "easycases/";
    //$('#casePage').val(1);
    casePage = 1;
    //var casePage = $('#casePage').val(); // Pagination
    $('#caseLoader').show();
    var projIsChange = $('#projIsChange').val();
    var fileUrl = strURL + "case_files";
    search_key = file_srch;
    $.post(fileUrl, {
        "projFil": projFil,
        "projIsChange": projIsChange,
        "casePage": casePage,
        "caseFileId": file_id,
        "file_srch": search_key
    }, function (res) {
        if (res) {
            $('#srch_load1').hide();
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
        loadCaseMenu(strURL + "ajax_case_menu", {
            "projUniq": uniq_id,
            "pageload": 0,
            "page": "dashboard"
        })
    });
    remember_filters('ALL_PROJECT', '');
    /*}else{
     $('#case_search').focus();
     }*/
}
function searchMilestone(file_id, uniq_id, file_srch, isActive) {
    if (!file_srch) {
        return false;
    }
    isActive = (isActive != '') ? isActive : 1;
    $('#search_text').val(file_srch);
    //    $('#filter_section').show();
    $('#milestone_content').css('margin-top', '50px');
    $('#show_search').html(_("Search Results for") + ":<span> " + file_srch + '</span>');
    $('#resetting').html(' &nbsp;<a href="javascript:void(0);" onclick="resetMilestoneSearch();">' + _("Reset") + ' </a>');

    $("#ajax_search").hide();
    $("#case_search").val("");
    if (uniq_id.trim() != "") {
        var projFil = uniq_id.trim();
    } else {
        var projFil = $('#projFil').val();
    }
    $('#caseLoader').show();
    if ($('#view_type').val() == 'kanban') {
        //              var strURL = HTTP_ROOT+"milestones/";
        //                casePage = 1;

        var projIsChange = $('#projIsChange').val();
        //    var fileUrl = strURL+"ajax_milestonelist";
        search_key = file_srch;
        showMilestoneList(3, isActive, '', file_srch);
        //    $.post(fileUrl,{
        //        "projFil":projFil,
        //        "projIsChange":projIsChange,
        //        "casePage":casePage,
        //        "caseFileId":file_id,
        //        "file_srch":search_key,
        //        'isActive':isActive
        //    },function(res) {
        //        if(res){
        //            res.isActive=isActive;
        //            $('#srch_load1').hide();
        //            $('#caseLoader').hide();
        ////            $("#caseFileDv").show();
        //            var params = parseUrlHash(urlHash);
        //            if(params!= "milestonelist") {
        //                parent.location.hash = "milestonelist";
        //            }
        //            var result = document.getElementById('show_milestonelist');
        //            result.innerHTML = tmpl("milestonelist_tmpl",res);
        //          }
        //    });
    } else {
        if ($('#storeIsActivegrid').val() == '' || $('#storeIsActivegrid').val() == 1) {
            ManageMilestoneList(1, file_srch);
        } else {
            ManageMilestoneList('', file_srch);
        }
    }
    $('#srch_load1').hide();
//            scrollPageTop($("#caseFileDv"));

//        loadCaseMenu(strURL+"ajax_case_menu", {
//            "projUniq":uniq_id,
//            "pageload":0,
//            "page":"dashboard"
//        })

//    remember_filters('ALL_PROJECT','');
    /*}else{
     $('#case_search').focus();
     }*/
}
function validateSearch() {
    $('#ajax_search').hide();
    var srch = $("#case_search").val();
    $('#srch_load1').hide();
    if (srch.trim() != "") {
        $('#case_srch').val("");
        casePage = 1;
        var url_string = window.location.href;
        if (url_string.search("dashboard") != -1) {
            if ($('#caseMenuFilters').val() == 'kanban') {
                easycase.showKanbanTaskList('kanban');
            } else {
                ajaxCaseView('case_project');
            }
            remember_filters('SEARCH', escape(srch));
            remember_filters('CASESRCH', '');
        } else {
            window.location = HTTP_ROOT + 'dashboard/?filters=cases&search=' + srch;
        }
    } else {
        document.getElementById('case_search').focus();
    }
}

function onKeyPress(e, id) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 13) {
        var srch = $("#" + id).val();
        if (srch.trim() == "") {
            $("#ajax_search").hide();
        } else {
            $("#ajax_search").show();
        }
    }
}

function goForSearch(e, click) {
    var done = 0;
    if (e) {
        var unicode = e.charCode ? e.charCode : e.keyCode;
        //        return false;
        if (unicode == 13) {
            if (focusedRow !== null) {
                if ($('.ajx-srch-tbl tr').hasClass("selctd-srch")) {
                    var page = getPage();
                    var uniq_id = $(focusedRow).attr("data-id");
                    if (page == "users") {
                        var role = $(focusedRow).attr("data-role");
                        searchUser(role, uniq_id);
                    } else if (page == "projects") {
                        var role = $(focusedRow).attr("data-role");
                        searchProject(role, uniq_id);
                    } else if (page == "files") {
                        var role = $(focusedRow).attr("data-role");
                        searchFile(role, uniq_id);
                    } else if (page == "milestones") {
                        var role = $(focusedRow).attr("data-role");
                        searchMilestone(role, uniq_id);
                    } else {
                        var caseno = $(focusedRow).attr("data-case-no");
                        searchTasks(caseno, uniq_id);
                    }
                    focusedRow = null;
                    $('#srch_load1').hide();
                    return false;
                }
            } else {
                done = 1;
            }
        }
    }
    if (click) {
        done = 1;
    }
    if (done == 1) {
        var page = getPage();
        var srch = $("#case_search").val();
        srch = srch.trim();
        if (page == "users") {
            searchUser('', '', srch);
        } else if (page == "projects") {
            searchProject('', '', srch);
        } else if (page == "files") {
            searchFile('', '', srch);
        } else if (page == "milestones") {
            var isActive = $('#storeIsActive').val();
            searchMilestone('', '', srch, isActive);
        } else if (page == "kanban") {
            searchMilestoneTasks(srch);
        } else {
            validateSearch();
        }
        return false;
    }
}

function hideupdatebtn() {
    $('#subprof1').hide();
    $('#subprof2').show();
    return true;
}

function validateemailrpt() {
    var errMsg;
    var done = 1;
    $('#subprof1').hide();
    $('#subprof2').show();
    if ($('#dlyupdateyes').is(":checked")) {
        var hr = $('#not_hr').val();
        var mn = $('#not_mn').val();
        var prjct = $('#rpt_selprj').val();
        if (hr == "") {
            errMsg = _("Hours field cannot be blank");
            document.getElementById("not_hr").focus();
            done = 0;
        } else if (mn == "") {
            errMsg = _("Minutes field cannot be blank");
            document.getElementById("not_mn").focus();
            done = 0;
        } else if (prjct == null) {
            errMsg = _("Project field cannot be blank");
            document.getElementById("rpt_selprj").focus();
            done = 0;
        }
    }
    if (done == 0) {
        showTopErrSucc('error', errMsg);
        $('#subprof2').hide();
        $('#subprof1').show();
        return false;
    }
}

function submitCompany() {
    $('#subprof1').hide();
    $('#subprof2').show();
    var cmpname = $("#cmpname").val();
    var website = $("#website").val();
    var phone = $("#contact_phone").val();
    var errMsg;
    var done = 1;
    //var regUrl = "^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)";
    var regUrl = /^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/i;
    var rxAlphaNum = /^([0-9\(\)-]+)$/;

    if (cmpname.trim() == "") {
        errMsg = _("Name cannot be left blank!");
        $("#name").focus();
        done = 0;
    } else if (website.trim().length != 0 && !website.trim().match(regUrl)) {
        errMsg = _('Please enter valid website url.');
        $("#website").focus();
        done = 0;
    } else if (phone.trim().length != 0 && !phone.trim().match(rxAlphaNum)) {
        errMsg = _('Please enter valid contact number.');
        $("#phone").focus();
        done = 0;
    }

    if (done == 0) {
        var op = 100;
        showTopErrSucc('error', errMsg);
        $('#subprof2').hide();
        $('#subprof1').show();
        return false;
    } else {
        $('#subprof1').hide();
        $('#subprof2').show();
    }
}
//Search for tasks, Projects, Users in header inner ends

//Manage users starts
function filterUserRole(role, user_srch) {
    var url = HTTP_ROOT + "users/manage/?role=" + role;
    if (user_srch) {
        url = url + "&user_srch=" + user_srch;
    }
    window.location = url;
}

function addProjectToUser() {
    var usr_id = getCookie('LAST_INVITE_USER');
    usr_id = decodeURIComponent(usr_id);
    if (usr_id) {
        var user_id = usr_id.split(",");
        var usr_name = '';
        for (var i in user_id) {
            usr_name = usr_name + "," + $("div.invite_user_cls[data-usr-id='" + user_id[i] + "']").attr('data-usr-name');
        }
        usr_name = trim(usr_name, ',');
        createCookie("LAST_INVITE_USER", '', -365, DOMAIN_COOKIE);
        if (confirm(_("Do you want to assign project to") + " '" + usr_name + "' ?")) {
            usr_name = shortLength(usr_name, 50);
            add_project(usr_id, usr_name, 1);
        }
    }
}

$(".icon-assign-usr").click(function () {
    var usr_id = $(this).attr('data-usr-id');
    var usr_name = $(this).attr('data-usr-name');
    var is_invited_user = $("#is_invited_user").val();
    add_project(usr_id, usr_name, is_invited_user);
});

function add_project(usr_id, usr_name, is_invite_user) {
    openPopup();
    $("#prjList").html('');
    hdprojectid = new Array();
    $(".add_usr_prj").show();
    $("#header_usr_prj_add").html(usr_name);
    $('#inner_usr_prj_add').hide();
    $('.add-prj-btn').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    /* $(".popup_form").css({
     "margin-top": "6px"
     }); */
    $("#proj_name").val('');
    $('#prjsrch').hide();
    $.post(HTTP_ROOT + "users/add_project", {
        "uid": usr_id,
        "is_invite_user": is_invite_user
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#prjsrch').show();
            $('#inner_usr_prj_add').show();
            $('#inner_usr_prj_add').html(data);
            $('.add-prj-btn').show();
            enableAddPrjBtns('.AddPrjToUser');
        }
    });
}
function searchprojkey() {
    var name = $('#proj_name').val();
    var user_id = '';
    try {
        var user_id = $('#user_id').val();
        var count = $('#count').val();
    } catch (e) {
    }
    if (user_id) {
        var strURL1 = HTTP_ROOT + 'users/add_project';
        $("#prjpopupload1").show();
        var is_invite_user = $("#is_invite_user").val();
        $.post(strURL1, {
            "count": count,
            "uid": user_id,
            "name": name,
            "is_invite_user": is_invite_user
        }, function (data) {
            if (data) {
                $('#inner_usr_prj_add').html(data);
                $("#prjpopupload1").hide();
                enableAddPrjBtns('.AddPrjToUser');
                $("#popupContactClose, .c_btn").click(function () {
                    closePopup();
                });
            }
        });
    }
}

$(".icon-remprj-usr").click(function () {
    var usr_id = $(this).attr('data-usr-id');
    var usr_name = $(this).attr('data-usr-name');
    openPopup();
    $(".rmv_usr_prj").show();
    $("#header_usr_prj_rmv").html(usr_name);
    $('#inner_usr_prj_rmv').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    $('.rmv-prj-btn').hide();
    $('#rmprjname').val('');
    $('#remprjsrch').hide();
    var is_invite_user = $("#is_invited_user").val();
    $.post(HTTP_ROOT + "users/project_listing", {
        "user_id": usr_id,
        "is_invite_user": is_invite_user
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#inner_usr_prj_rmv').show();
            $('#inner_usr_prj_rmv').html(data);
            if (parseInt($("#is_prj").val())) {
                $('.rmv-prj-btn').show();
                $('#remprjsrch').show();
            }
            enableAddPrjBtns('.removePrjFromuser');
        }
    });
});

function grantOrRemoveModerator(obj) {
    var usr_id = $(obj).attr('data-usr-id');
    var usr_name = $(obj).attr('data-usr-name');
    var type = $(obj).attr('data-type');
    var msg, suc_msg, err_msg, data_type = 0, text = '';
    add_class = rmv_class = '';
    msg = suc_msg = err_msg = "";
    if (parseInt(type)) {
        msg = "grant";
        suc_msg = "Granted";
        err_msg = "grant";
        data_type = 0;
        text = _("Revoke Moderator");
        add_class = 'icon-remove-modrt';
        rmv_class = 'icon-add-modrt';
    } else {
        msg = "revoke";
        suc_msg = "Revoked";
        err_msg = "revoke";
        data_type = 1;
        text = _("Grant Moderator");
        add_class = 'icon-add-modrt';
        rmv_class = 'icon-remove-modrt';
    }
    if (confirm(_("Are you sure you want to") + " " + msg + " " + _("moderator to") + " '" + usr_name + "'?")) {
        $.post(HTTP_ROOT + "users/grant_moderate", {
            "type": type,
            "user_id": usr_id
        }, function (data) {
            if (parseInt(data)) {
                $(obj).attr('data-type', data_type);
                $(obj).addClass(add_class);
                $(obj).removeClass(rmv_class);
                $(obj).text(text);
                showTopErrSucc('success', suc_msg + ' ' + _("moderator privilege successfully"));
            } else {
                showTopErrSucc('error', _("Error in") + " " + err_msg + " " + _("to moderator"));
            }
        });
    }
}
function searchremprjkey() {
    var name = $('#rmprjname').val();
    var user_id = $('#usrid').val();
    var is_invite_user = $("#is_invite_user").val();
    if (user_id) {
        var strURL1 = HTTP_ROOT + 'users/project_listing';
        $('#rempopupload').css({
            "top": "48px"
        });
        $('#rempopupload').show();
        $.post(strURL1, {
            "user_id": user_id,
            "name": name,
            "is_invite_user": is_invite_user
        }, function (data) {
            if (data) {
                $('#rempopupload').hide();
                $('#inner_usr_prj_rmv').html(data);
                enableAddPrjBtns('.removePrjFromuser');
            }
        });
    }
}

function arrayRemove(str, rmvstr) {
    var array = new Array();
    var rmvArray = new Array();
    str = $.trim(str.toLowerCase());
    array = str.split(', ');

    rmvstr = $.trim(rmvstr.toLowerCase());
    rmvArray = rmvstr.split(', ');
    for (var i = 0; i < rmvArray.length; i++) {
        var element = rmvArray[i];
        var index = array.indexOf(element);
        if (index != -1) {
            array.splice(index, 1);
        }
    }
    var string = '';
    if (array !== '') {
        for (var i = 0; i < array.length; i++) {
            var ToCamelCaseTitle = toTitleCase(array[i]); //Function is require to camelcase the name of the remaining projects
            string = string + ", " + ToCamelCaseTitle;
            //string = string +", "+array[i].charAt(0).toUpperCase()+ array[i].slice(1);
        }
        if (string) {
            string = string.replace(', ', '');
            //string = shortLength(string,20)
        }
    }
    return string;
}

//Function is require to camelcase the name of the remaining projects
function toTitleCase(str) {
    return str.replace(/(?:^|\s)\w/g, function (match) {
        return match.toUpperCase();
    });
}

function removeprojects() {
    var done = 0;
    var project_name = '';
    var remaining_projects = Array();
    var rmv_user_name = $("#header_usr_prj_rmv").text();
    var user_id = $('#usrid').val();
    var all_project = $("#rmv_allprj_" + user_id).val();
    $('#inner_usr_prj_rmv input:checked').each(function () {
        if ($(this).attr('id') !== 'checkAllprojects') {
            project_name = project_name + ", " + $(this).attr('data-prj-name');
            done++;
        }
    });

    project_name = project_name.replace(', ', '');
    remaining_projects = arrayRemove(all_project, project_name);
    //alert(project_name);alert(all_project);
    if (done) {
        if (confirm(_("Are you sure you want to remove") + " '" + project_name + "' " + _("from") + " '" + rmv_user_name + "'?")) {
            $('#inner_usr_prj_rmv input:checked').each(function () {
                if ($(this).attr('id') !== 'checkAllprojects') {
                    var listid = $(this).attr('id');
                    var project_id = $(this).attr('value');
                    //var listing = $("#"+listid).parents("tr").attr('id');
                    var listing = $(this).parents("tr").attr('id');
                    //$("#" + listid).prop('checked',false);
                    $(this).prop('checked', false);
                    //$("#" + listing).fadeOut(1000);
                    $(this).parents("tr").fadeOut(1000);
                    $(this).parents("tr").remove();
                    enableAddPrjBtns('.removePrjFromuser');
                    var strURL = HTTP_ROOT + 'users/project_listing';

                    var is_invite_user = $("#is_invited_user").val();
                    $.post(strURL, {
                        "user_id": user_id,
                        "project_id": project_id,
                        "is_invite_user": is_invite_user
                    }, function (data) {
                        if (data) {
                            //$("#" + listing).remove();
                        }
                    });
                }
            });
            var total_project = $("#total_projects").val();
            var total_projects = parseInt(total_project) - parseInt(done);
            if (parseInt(total_projects) > 0) {
                $("#total_projects").val(total_projects);
            } else {
                $("#rmv_prj_" + user_id).hide();
                closePopup();//remaining_projects
            }
            /* Require to display the Remaining projects on the project delete starts here */
            //alert(remaining_projects);
            if (remaining_projects) {
                $("#rmv_allprj_" + user_id).val(remaining_projects);
                $("#remain_prj_" + user_id).html(_("Projects") + ": <span class='fnt13'>" + shortLength(remaining_projects, 20) + "</span>");
            } else {
                $("#rmv_allprj_" + user_id).val("");
                $("#remain_prj_" + user_id).html(_("Projects") + ": <span class='fnt13' style='color:#9E9E9E'>N/A</span>");
            }
            /* Require to display the Remaining projects on the project delete ends here */
            showTopErrSucc('success', _("Project(s)") + " '" + project_name + "' " + _("removed from") + " '" + rmv_user_name + "'");
        } else {
            return false;
        }
    }
}

function assignproject(el) {
    var projectid = new Array();
    var project_name = '';
    var done = 0;
    var count = $('#count').val();

    projectid = hdprojectid;
    project_name = hdproject_name;
    if (hdprojectid.length != 0) {
        done++;
    }
    project_name = project_name.replace(', ', '');
    if (done) {
        $(".chkbx_cur").prop("disabled", true);
        var usrid = $('#user_id').val();
        var strURL = HTTP_ROOT + 'users/assign_prj';
        $("#confirmbtnprj").hide();
        $('#prjloader').show();
        var is_invite_user = $("#is_invite_user").val();
        $.post(strURL, {
            "projectid": projectid,
            "userid": usrid,
            "is_invite_user": is_invite_user
        }, function (data) {
            if (data) {
                var parr = data.split("::");
                if (parr[0] == "success") {
                    var user_id = usrid.split(",");
                    for (var i in user_id) {
                        var assignedProjects = $("#rmv_allprj_" + user_id[i]).val();
                        if (!assignedProjects) {
                            $("#rmv_allprj_" + user_id[i]).val(project_name);
                            $("#remain_prj_" + user_id[i]).html(_("Projects") + ": <span class='fnt13'>" + shortLength(project_name, 20) + "</span>");
                            $("#rmv_prj_" + user_id[i]).show();
                        } else {
                            project_name = assignedProjects + ", " + project_name;
                            $("#rmv_allprj_" + user_id[i]).val(project_name);
                            $("#remain_prj_" + user_id[i]).html(_("Projects") + ": <span class='fnt13'>" + shortLength(project_name, 20) + "</span>");
                            $("#rmv_prj_" + user_id[i]).show();
                        }
                    }
                    if (el && el.id == "confirmprjbut") {
                        $('#proj_name').val('');
                        var strURL1 = HTTP_ROOT + 'users/add_project';
                        $("#prjpopupload").show();
                        $.post(strURL1, {
                            "uid": usrid,
                            'count': count,
                            "is_invite_user": is_invite_user
                        }, function (data) {
                            if (data) {
                                $('#inner_usr_prj_add').html(data);
                                $("#prjpopupload").hide();
                                enableAddPrjBtns('.AddPrjToUser');
                            }
                        });
                    } else {
                        closePopup();
                    }
                    $('#prjloader').hide();
                    $("#confirmbtnprj").show();
                    $("#prjList").html('');
                    hdprojectid = new Array();
                    showTopErrSucc('success', _('Project assigned successfully'));
                }
            }
        });
    }
}
var hdprojectid = new Array();
var hdproject_name = '';
function checkboxCheckUncheck(chkAll, chkOne, row, active_class) {
    $(document).on('click', chkAll, function (e) {
        if ($(chkAll).is(':checked')) {
            $(chkOne).prop('checked', true);
            $(chkOne).parents(row).addClass(active_class);
            $("#prjList").html('');
            $('#inner_usr_prj_add input:checked').each(function () {
                if ($(this).attr('id') !== 'checkAllAddPrj') {
                    var project_id = $(this).attr('value');
                    var project_name = decodeURIComponent($(this).attr('data-prj-name').replace(/\+/g, ' '));

                    var exstId = $("#prjList").find('li[id="' + project_id + '"]').length;
                    if (exstId == 0) {
                        $("#prjList").append('<li class="bit-box" rel="7" id="' + project_id + '">' + project_name + '<a class="closebutton" id="close' + project_id + '" href="javascript:void(0);" onclick="removeProjectName(\'' + project_id + '\',\'' + $(this).attr('id') + '\',\'' + chkAll + '\',\'' + chkOne + '\',\'' + row + '\',\'' + active_class + '\');"></a></li>');
                        hdprojectid.push(project_id);
                        hdproject_name = hdproject_name + ", " + project_name;
                    }
                }
            });
        } else {
            $(chkOne).prop('checked', false);
            $(chkOne).parents(row).removeClass(active_class);
            hdprojectid = new Array();
            hdproject_name = '';
            $("#prjList").html('');
        }
        enableAddPrjBtns(chkOne);
    });
    $(document).on('click', chkOne, function (e) {
        if ($(chkOne + ':checked').length == $(chkOne).length) {
            $(chkAll).prop('checked', true);
            $('#inner_usr_prj_add input:checked').each(function () {
                if ($(this).attr('id') !== 'checkAllAddPrj') {
                    var project_id = $(this).attr('value');
                    var project_name = decodeURIComponent($(this).attr('data-prj-name').replace(/\+/g, ' '));

                    var exstId = $("#prjList").find('li[id="' + project_id + '"]').length;
                    if (exstId == 0) {
                        $("#prjList").append('<li class="bit-box" rel="7" id="' + project_id + '">' + project_name + '<a class="closebutton" id="close' + project_id + '" href="javascript:void(0);" onclick="removeProjectName(' + project_id + ',\'' + $(this).attr('id') + '\',\'' + chkAll + '\',\'' + chkOne + '\',\'' + row + '\',\'' + active_class + '\');"></a></li>');
                        hdprojectid.push(project_id);
                        hdproject_name = hdproject_name + ", " + project_name;
                    }
                }
            });
        } else {
            if ($(this).is(':checked')) {
                $(this).parents(row).addClass(active_class);
                $('#inner_usr_prj_add input:checked').each(function () {
                    if ($(this).attr('id') !== 'checkAllAddPrj') {
                        var project_id = $(this).attr('value');
                        var project_name = decodeURIComponent($(this).attr('data-prj-name').replace(/\+/g, ' '));

                        var exstId = $("#prjList").find('li[id="' + project_id + '"]').length;
                        if (exstId == 0) {
                            $("#prjList").append('<li class="bit-box" rel="7" id="' + project_id + '">' + project_name + '<a class="closebutton" id="close' + project_id + '" href="javascript:void(0);" onclick="removeProjectName(' + project_id + ',\'' + $(this).attr('id') + '\',\'' + chkAll + '\',\'' + chkOne + '\',\'' + row + '\',\'' + active_class + '\');"></a></li>');
                            hdprojectid.push(project_id);
                            hdproject_name = hdproject_name + ", " + project_name;
                        }
                    }
                });
            } else {
                var project_id = $(this).attr('value');
                var project_name = decodeURIComponent($(this).attr('data-prj-name').replace(/\+/g, ' '));
                $(this).parents(row).removeClass(active_class);
                removeProjectName(project_id, $(this).attr('id'), chkAll, chkOne, row, active_class);
            }
        }
        enableAddPrjBtns(chkOne);
    });
}

function enableAddPrjBtns(chkOne) {
    if (chkOne == '.AddPrjToUser') {
        if ($(chkOne + ':checked').length) {
            $('#confirmprjcls').removeClass('btn_blue_inactive');
            $('#confirmprjbut').removeClass('btn_blue_inactive');
        } else {
            $('#confirmprjcls').addClass('btn_blue_inactive');
            $('#confirmprjbut').addClass('btn_blue_inactive');
        }
    } else if (chkOne == '.removePrjFromuser') {
        if ($(chkOne + ':checked').length) {
            $('#rmvprjbtn').removeClass('btn_blue_inactive');
        } else {
            $('#rmvprjbtn').addClass('btn_blue_inactive');
        }
    }
}
function enableAddUsrBtns(chkOne) {
    if (chkOne == '.ad-usr-prj') {
        if ($(chkOne + ':checked').length) {
            $('#confirmusercls').removeClass('btn_blue_inactive');
            $('#confirmuserbut').removeClass('btn_blue_inactive');
        } else {
            $('#confirmusercls').addClass('btn_blue_inactive');
            $('#confirmuserbut').addClass('btn_blue_inactive');
        }
    } else if (chkOne == '.rem-usr-prj') {
        if ($(chkOne + ':checked').length) {
            $('#rmvbtn').removeClass('btn_blue_inactive');
        } else {
            $('#rmvbtn').addClass('btn_blue_inactive');
        }
    }
}
function resend_invitation(qrst, email) {
    if (confirm(_('Are you sure you want to Resend Invitation email to') + ' \'' + email + '\' ?')) {
        $("#projectLoader").show();
        $.post(HTTP_ROOT + 'users/resend_invitation', {
            'ajax_flag': 1,
            'querystring': qrst
        }, function (res) {
            if (res.msg == 'succ') {
                $("#projectLoader").hide();
                showTopErrSucc('success', _("Invitation link send successfully to email") + " '" + email + "'. ");
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            } else {
                $("#projectLoader").hide();
                showTopErrSucc('error', _("Error in sending invitation link!"));
            }
        }, 'json');
    } else {
        return false;
    }
}
$(".proj_mng_div .contain").hover(function () {
    $(this).find(".proj_mng").stop(true, true).animate({
        bottom: "0px",
        opacity: 1
    }, 400);
}, function () {
    $(this).find(".proj_mng").stop(true, true).animate({
        bottom: "-42px",
        opacity: 0
    }, 400);
});
//Manage users ends

//Activity starts
function myactivities(myTab, delegatedTab) {
    if ($('#' + delegatedTab).parents("li").hasClass('active') == true) {
        $('#' + delegatedTab).parents("li").removeClass('active');
        $('#' + myTab).parents("li").addClass('active');
        loadOverdue('my');
        loadUpcoming('my');
    }
}
function delegateactivities(myTab, delegatedTab) {
    if ($('#' + myTab).parents("li").hasClass('active') == true) {
        $('#' + myTab).parents("li").removeClass('active');
        $('#' + delegatedTab).parents("li").addClass('active');
        loadOverdue('delegated');
        loadUpcoming('delegated');
    }
}
/*function loadActivity(type) {
 var displayed = $("#displayed").val();
 var prj_id = $("#prjid").val();
 var limit1, limit2, projid;
 if(type == "more") {
 limit1 = displayed;
 limit2 = 10;
 projid = prj_id;
 } else {
 limit1 = 0;
 limit2 = 29;
 projid = prj_id;
 }
 if(type == "more") {
 $(".morebar").show();
 } else {
 $("#moreloader").show();
 }
 var strURL = HTTP_ROOT+"users/ajax_activity/";
 $("#PieChart").hide();
 $.post(strURL,{'type':type,'limit1':limit1,'limit2':limit2,'projid':projid}, function(res){
 
 if(type == "more") {
 $(".morebar").hide();
 var data = tmpl("ajax_activity_tmpl", res);
 $("#activities").append(data);
 $("img.lazy").lazyload({ placeholder : "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" });
 var displayed = $("#displayed").val();
 var newdisplayed = (parseInt(displayed)+1)+10;
 $("#displayed").val(newdisplayed);
 if(prj_id == 'all') {
 $(".prj_dvs").show();
 }
 } else {
 $("#moreloader").hide();
 var result = document.getElementById('activities');
 result.innerHTML = tmpl("ajax_activity_tmpl", res);
 $("img.lazy").lazyload({ placeholder : "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" });
 if(prj_id == 'all') {
 $(".prj_dvs").show();
 }
 }
 setStatus();
 var totalact = $("#totalact").val();
 if(parseInt(totalact) > 0) {
 $('#PieChart').load(HTTP_ROOT+'users/activity_pichart',{'pjid':projid});
 $("#PieChart").show();
 }
 });
 }*/

var globalTimeoutArcCase = null;
var globalTimeoutArcFile = null;
var globalTimeoutActivity = null;
var globalTimeoutUsers = null;

$(document).ready(function () {
    $(window).scroll(function () {
        var height = parseInt($(document).height() - $(window).height()) - parseInt($(window).scrollTop());
        if (parseInt(height) >= 0 && parseInt(height) <= 100) {
            if (caseListData == 1 && fileListData == 0) {
                var totalcaselist = $("#totalCases").val();
                var displayedcases = $("#displayedCases").val();
                if (parseInt(totalcaselist) > parseInt(displayedcases)) {
                    if (globalTimeoutArcCase != null)
                        clearTimeout(globalTimeoutArcCase);
                    globalTimeoutArcCase = setTimeout(changeArcCaseList, 1000, 'more');
                }
            } else if (caseListData == 0 && fileListData == 1) {
                var totalfilelist = $("#totalFiles").val();
                var displayedfiles = $("#displayedFiles").val();
                if (parseInt(totalfilelist) > parseInt(displayedfiles)) {
                    if (globalTimeoutArcFile != null)
                        clearTimeout(globalTimeoutArcFile);
                    globalTimeoutArcFile = setTimeout(changeArcFileList, 1000, 'more');
                }
            } else if (usersListData == 1) {
                var totalUsersData = $("#total_users_count").val();
                var displayedUsersData = $("#displayed_users_count").val();
                //alert(totalUsersData);alert(displayedUsersData);
                if (parseInt(totalUsersData) > parseInt(displayedUsersData)) {
                    if (globalTimeoutUsers != null)
                        clearTimeout(globalTimeoutUsers);
                    globalTimeoutUsers = setTimeout(moreUsersList, 1000, 'more');
                }
            } else {
                var totalact = $("#totalact").val();
                var displayed = $("#displayed").val();
                if (parseInt(totalact) > parseInt(displayed) && $('#actvt_section').css('display') != 'none') {
                    if (globalTimeoutActivity != null)
                        clearTimeout(globalTimeoutActivity);
                    globalTimeoutActivity = setTimeout(loadActivity, 1000, 'more');
                }
            }
        }
    });
    /*$(document).on('click', '.edit_time_log', function() {
     if (typeof $(this).closest('td').attr('data-logid') == 'undefined')
     return false;
     if ($('#projFil').val() == 'all') {
     createlog('', '', $(this).closest('td').attr('data-logid'), 0, 0, $(this).attr('data-task-id'), escape($(this).attr('data-prj-name')));
     } else {
     createlog('', '', $(this).closest('td').attr('data-logid'), 0, 0, $(this).attr('data-task-id'));
     }
     });*/
});

function setStatus() {
    $("td div[id^='allStatus']").each(function (res) {
        var v_new = 0;
        var v_replied = 0;
        var v_resolved = 0;
        var v_closed = 0;
        var id = this.id;
        var tot = {};
        $("#" + id).html('');
        $("." + id).each(function () {
            var sts = $(this).attr("rel");

            sts = sts.split('-');
            var sts_id = sts[0];
            var sts_nm = sts[1];
            var sts_col = sts[2];

            if (sts_id.trim() != '' && sts_nm.trim() != '' && sts_col.trim() != '') {
                tot[parseInt(sts_id)] = {'id': sts_id, 'name': sts_nm, 'color': sts_col, 'cnt': ((typeof tot[parseInt(sts_id)] != 'undefined') ? parseInt(tot[parseInt(sts_id)].cnt) + 1 : 1)};
            }

        });
        var status = '';
        for (var key in tot) {
            var data = tot[key];
            status += data.name + "<strong style='color:" + data.color + "'>(" + data.cnt + ")</strong> <span>|</span>";
        }

        $("#" + id).html(status);
    });
}
/*function loadOverdue(type) {
 $("#moreOverdueloader").show();
 $("#Overdue").html('');
 var prj_id = $("#prjid").val();
 var projid = prj_id;
 var strURL = HTTP_ROOT+"users/ajax_overdue/";
 $.post(strURL,{'type':type,'projid':projid}, function(res){
 $("#Overdue").html(res);
 $("#moreOverdueloader").hide();
 });
 }
 function loadUpcoming(type) {
 $("#moreOverdueloader").show();
 $("#Upcoming").html('');
 var prj_id = $("#prjid").val();
 var projid = prj_id;
 var strURL = HTTP_ROOT+"users/ajax_upcoming/";
 $.post(strURL,{'type':type,'projid':projid}, function(res){
 $("#Upcoming").html(res);
 $("#moreOverdueloader").hide();
 });
 }*/
function loadMembers(type) {
    var prj_id = $("#prjid").val();
    var projid = prj_id;
    var strURL = HTTP_ROOT + "users/ajax_member/";
    $.post(strURL, {
        'type': type,
        'projid': projid
    }, function (res) {
        $("#Members").html(res);
        $("#moreMemberloader").hide();
    });
}
//Activity ends

//Support starts
$(".support-popup").click(function () {
    closePopup();
    openPopup();
    if (!$(this).parent().is('li')) {
        $('.support_title').text(_('Support'));
    } else {
        $('.support_title').text(_('Feedback'));
    }
    $(".support_popup").show();
    $("#support_name").focus();
    $("#support_err").html('').hide();
    $("#url_sendding").val(document.URL);
});

function postSupport() {
    var geturl = $("#url_sendding").val().trim();
    var support_name = $("#support_name").val().trim();
    var support_email = $("#support_email").val().trim();
    var support_msg = $("#support_msg").val().trim();
    $("#support_err").hide();

    if (support_name == "") {
        $("#support_err").show();
        $("#support_err").html(_("Name can not be blank!"));
        $("#support_name").focus();
        return false;
    } else if (support_email == "") {
        $("#support_err").show();
        $("#support_err").html(_("E-mail can not be blank!"));
        $("#support_email").focus();
        return false;
    } else {
        var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!support_email.match(emailRegEx)) {
            $("#support_err").show();
            $("#support_err").html(_("Please enter a valid E-mail!"));
            $("#support_email").focus();
            return false;
        } else if (support_msg == "") {
            $("#support_err").show();
            $("#support_err").html(_("Message can not be blank!"));
            $("#support_msg").focus();
            return false;
        } else {
            $("#spt_btn").hide();
            $("#sprtloader").show();
            $.post(HTTP_ROOT + "users/post_support_inner", {
                "support_refurl": escape(geturl),
                "support_email": escape(support_email),
                "support_msg": escape(support_msg),
                "support_name": escape(support_name)
            }, function (data) {
                if (data == "success") {
                    showTopErrSucc('success', _('Thanks for your feedback. We will get back to you as soon as possible.'));
                } else {
                    showTopErrSucc('error', _("Error in post to support!"));
                }
                $("#spt_btn").show();
                $("#sprtloader").hide();
                $("#support_msg").val('');
                closePopup();
            });
        }
    }
    return false;
}
//Support ends


// Daily Update Alerts starts

function getProjectMembers(obj) {
    var strURL = HTTP_ROOT + "projects/projectMembers";
    $("#loading_sel").show();
    $("#err_msg_spn").hide();
    daily_update_id = 0;
    $("#tr_members").remove();
    $("#tr_timezone").remove();
    $("#tr_time").remove();
    $("#tr_days").remove();
    $.post(strURL, {
        "id": obj.value
    }, function (res) {

        var res = res.replace("<head/>", "");
        var res = res.replace("<head/ >", "");
        var res = res.replace("<head />", "");
        if (res) {
            is_project_members = 1;
            $(res).insertAfter($("#tr_project"));
            daily_update_id = $("#daily_update_id").val();
            $("#daily_btn_disable").hide();
            $("#daily_btn").show();
        } else {
            is_project_members = 0;
            var str = '<tr id="tr_members"><td colspan="2"></td></tr>';
            $(str).insertAfter($("#tr_project"));
            $("#daily_btn_disable").show();
            $("#daily_btn").hide();
        }
        if (parseInt(daily_update_id)) {
            $("#daily_btn").html("<i class='icon-big-tick'></i>" + _("Update"));
            $("#cancel_daily_update").show();
        } else {
            $("#cancel_daily_update").hide();
            $("#daily_btn").html("<i class='icon-big-tick'></i>" + _("Save"));
        }
        $("#loading_sel").hide();
    });
}
function checkUncheckAll(arg) {
    if (parseInt(arg)) {
        if ($('#user_all').is(":checked")) {
            $(".prj_users").attr("checked", "checked");
        } else {
            $(".prj_users").attr("checked", false);
        }
    } else {
        if ($('.prj_users:checked').length == $('.prj_users').length) {
            $("#user_all").attr("checked", "checked");
        } else {
            $("#user_all").attr("checked", false);
        }
    }
    $("#err_msg_spn").hide();
}
function validateDailyMail() {
    var done = 1;
    if ($.trim($("#project_id").val()) == '') {
        errMsg = _("Please select project.");
        done = 0;
    }
    /*if(parseInt(is_project_members)){
     if ($('.prj_users:checked').length){
     done = 1;
     }else{
     errMsg = "Please choose atleast one user.";
     done = 0;
     }
     }else{
     errMsg = "No users assigned to this Project!";
     done = 0;
     }*/

    if ($('.prj_users:checked').length == 0) {
        errMsg = _("Please choose atleast one user.");
        done = 0;
    } else if ($.trim($("#upd_hour").val()) == '') {
        errMsg = _("Please select hour.");
        done = 0;
    } else if ($.trim($("#upd_minute").val()) == '') {
        errMsg = _("Please select minute.");
        done = 0;
    }
    if (done == 0) {
        showTopErrSucc('error', errMsg);
        return false;
    } else {
        $('#subprof1').hide();
        $('#subprof2').show();
        $('#dailyUpdateForm').submit();
    }
}
function showError(msg) {
    $("#err_msg_spn").html(msg);
    $("#err_msg_spn").show();
}
function hideErr() {
    $("#err_msg_spn").hide();
}
function cancel_daily_update() {
    if (confirm(_("Are you sure you want to cancel Daily Catch-Up alert for this project?"))) {
        $('#subprof1').hide();
        $('#cancel_daily_update').hide();
        $('#subprof2').show();
        var path = "projects/cancelDailyUpdate/";
        window.location.href = HTTP_ROOT + path + daily_update_id;
    }
}
function openEditor(editormessage) {
    $("#divNewCase").hide();
    $("#divNewCaseLoader").show();
    (function ($) {
        if (typeof (tinymce) != "undefined") {//console.log('Inside remove123---');
            tinymce.execCommand('mceRemoveControl', true, 'CS_message'); // remove any existing references
        }

        createTaskTemplatePlugin();

        $('#CS_message').tinymce({
            // Location of TinyMCE script
            script_url: HTTP_ROOT + 'js/tinymce/tiny_mce.js',
            theme: "advanced",
            plugins: "paste, -tasktemplate", // - tells TinyMCE to skip the loading of the plugin
            theme_advanced_buttons1: "bold,italic,strikethrough,underline,|,numlist,bullist,|,indent,outdent,|,tasktemplate",
            theme_advanced_resizing: false,
            theme_advanced_statusbar_location: "",
            paste_text_sticky: true,
            gecko_spellcheck: true,
            paste_text_sticky_default: true,
            forced_root_block: false,
            width: "638px",
            autoresize_min_height: "130px",
            autoresize_max_height: "300px",
            autoresize_on_init: true,
            autoresize_bottom_margin: 20,
            oninit: function () {
                $("#divNewCaseLoader").hide();
                $("#divNewCase").show();
                //$('#CS_message').tinymce().focus();
                $('#CS_message').val(editormessage);
                $('#CS_message').tinymce().setContent(editormessage);
                $("#tmpl_open").show();
            }
        });

    })($);
}
function createTaskTemplatePlugin() {
    if (typeof (tinymce) != "undefined") {
        // Creates a new plugin class and a custom listbox
        tinymce.create('tinymce.plugins.TaskTemplatePlugin', {
            createControl: function (n, cm) {
                switch (n) {
                    case 'tasktemplate':
                        var mlb = cm.createListBox('tasktemplate', {
                            title: _('Task Template'),
                            onselect: function (v) {
                                //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                                if (v && v.indexOf('##') != -1) {
                                    showTemplates(v.split('##')[0], v.split('##')[1]);
                                } else {
                                    tinyMCE.activeEditor.setContent(tinyPrevContent);
                                }
                            }
                        });

                        // Add task templete values to the list box
                        mlb.add(_('Set to default'), 0);
                        if (countJS(TASKTMPL)) {
                            for (var tmpl in TASKTMPL) {
                                mlb.add(TASKTMPL[tmpl].CaseTemplate.name, TASKTMPL[tmpl].CaseTemplate.id + '##' + TASKTMPL[tmpl].CaseTemplate.name);
                            }
                        }
                        // Return the new listbox instance
                        return mlb;
                }

                return null;
            }
        });
        // Register plugin with a short name
        tinymce.PluginManager.add('tasktemplate', tinymce.plugins.TaskTemplatePlugin);
    }
}
function showTemplates(id, name) {
    tinyPrevContent = tinyMCE.activeEditor.getContent();
    tinyMCE.activeEditor.setContent('');
    if (id != "New") {
        document.getElementById('CS_message_ifr').disable = true;
        $("#CS_message_ifr").hide();

        $.post(HTTP_ROOT + "easycases/ajax_case_template", {
            "tmpl_id": id
        }, function (data) {
            $("#CS_message_ifr").show();
            if (data) {
                tinyMCE.activeEditor.setContent(data);
            }
        });
    }
}
function submitAddNewCase(postdata, CS_id, uniqid, cnt, dtls, status, prelegend, pid) {
    var description = '';
    var title = '';
    var id = tinyMCE.activeEditor.editorId;
    description = tinymce.activeEditor.getContent();
    var res = description.match(/attach/gi);
    var res2 = description.match(/screenshot/gi);
    var res3 = description.match(/screen-shot/gi);
    var res4 = description.match(/screen shot/gi);
    if (id == 'CS_message') {
        title = $('#CS_title').val();
    }
    var res1 = title.match(/attach/gi);
    var res5 = title.match(/screenshot/gi);
    var res6 = title.match(/screen shot/gi);
    var res7 = title.match(/screen-shot/gi);
    var conf = 0;
    var attachment = $("#table1 input[type=checkbox]:checked").length;
    if ((res1 || res || res2 || res3 || res4 || res5 || res6 || res7) && !attachment) {
        conf = confirm(_('Did you mean to add an attachment or screenshot to this Task?'));
    }
    if (!conf) {
        var CS_type_id = 2;
        var CS_priority = 1;
        var CS_assign_to = 0;
        var CS_message = "";
        var CS_due_date = "";
        var CS_legend = status;
        var CS_milestone = "";
        var cs_hours = 0;
        var est_hours = 0;
        var completed = 0;
        var task_uid = 0;
        var taskid = 0;
        var CS_isRecurring = 0;
        var CS_recurringType = '';
        var CS_recurring_endDate = '';
        var CS_recurring_startDate = '';
        var CS_occurrence = '';
        var CS_recurringEndType = '';
        var recurringData = '';
        if (CS_id) {
            var CS_legend = $("#legend" + CS_id).val();
        }
        var done = 1;
        if (CS_id) {
            var project_id = "CS_project_id" + CS_id;
            var istype = "CS_istype" + CS_id;
            var title = "CS_title" + CS_id;

            var CS_project_id = document.getElementById(project_id).value;
            var CS_istype = document.getElementById(istype).value;
            var CS_title = document.getElementById(title).value;

            var type_id = "CS_type_id" + CS_id;
            var priority = "CS_priority" + CS_id;
            var case_no = "CS_case_no" + CS_id;

            var CS_type_id = $('#' + type_id).val();
            var CS_priority = $('#' + priority).val();
            var CS_case_no = $('#' + case_no).val();

            var html = "html" + CS_id;
            var plane = "plane" + CS_id;
            if ($('#' + html).is(":visible")) {
                var txa_comments = "txa_comments" + CS_id;
                CS_message = $('#' + txa_comments).html();//document.getElementById(txa_comments).value;
            } else {
                var txa_plane = "txa_plane" + CS_id;
                CS_message = nl2br($.trim(document.getElementById(txa_plane).value));
            }
            var editortype = "editortype" + CS_id;
            var datatype = document.getElementById(editortype).value;

            var totfiles = "totfiles" + CS_id;
            var hidalluser = "hidtotresreply" + CS_id;

            var assign_to = "CS_assign_to" + CS_id;
            var CS_assign_to = document.getElementById(assign_to).value;
        } else {
            var CS_project_id = document.getElementById('CS_project_id').value;
            var CS_istype = document.getElementById('CS_istype').value;
            var CS_title = document.getElementById('CS_title').value;
            var CS_start_date = $('#CS_start_date').val();
            var CS_estimated = $('#estimated_hours').val();
            var totfiles = "totfiles";
            var hidalluser = "hidtotproj";
            var datatype = 0;
            if ($('#easycase_uid').val()) {
                task_uid = $('#easycase_uid').val();
                taskid = $('#CSeasycaseid').val();
            }
            $('#projAllmsg').hide();
            if (CS_project_id == 'all') {
                $('#projAllmsg').show();
                $('#ctask_popup a').css({
                    'border-color': '#CE2129'
                });
                alert(_('Oops! No project selected.'));
                return false;
            }

            if (!CS_id) {
                if (CS_project_id == "") {
                    done = 0;
                }
                if (CS_title.trim() == "" || CS_title.trim() == "Add a task here and hit enter...") {
                    $('#CS_title').css('border-color', '#CE2129');
                    $("#CS_title").focus();
                    done = 0;
                } else {
                    $('#CS_title').css('border-color', '');
                }
                /* if(TLG == 1){
                 if (CS_start_date.trim() == "") {
                 $('#gantt_start_date').css('border-color', '#CE2129');
                 $("#gantt_start_date").focus();
                 done = 0;
                 } else {
                 $('#gantt_start_date').css('border-color', '');
                 }
                 if (CS_estimated.trim() == "") {
                 $('#estimated_hours').css('border-color', '#CE2129');
                 $("#estimated_hours").focus();
                 done = 0;
                 } else {
                 $('#estimated_hours').css('border-color', '');
                 }
                 } */
            }
        }
        if (RCT == 1) {
            CS_recurringType = $('#CSrepeat_type').val();
            if (trim(CS_recurringType) != '' && trim(CS_recurringType) != 'None') {
                CS_isRecurring = 1;
                CS_recurring_startDate = trim($('#CSrepeat_start_date').val()) != '' ? trim($('#CSrepeat_start_date').val()) : trim($('#CS_due_date').val());
                if ($('#occur').is(':checked')) {
                    CS_recurringEndType = 'occur';
                    CS_occurrence = trim($('#occurrence').val());
                    if (CS_recurring_startDate != '' && CS_occurrence == '') {
                        showTopErrSucc('error', _('Number of occurrences can not be left blank'));
                        $('#occurrence').focus();
                        return false;
                    }
                } else if ($('#date').is(':checked')) {
                    CS_recurringEndType = 'date';
                    CS_recurring_endDate = trim($('#end_datePicker').val());
                    if (CS_recurring_startDate != '' && CS_recurring_endDate == '') {
                        showTopErrSucc('error', _('End date can not be left blank'));
                        $('#end_datePicker').focus();
                        return false;
                    }
                }
            }
        }

        if (RCT == 1) {
            if ($('#is_recurring').is(":checked")) {
                CS_isRecurring = 1;
                recurringData = serializeDatatoArray($('#recurrence_details_form').serializeArray());
            }
        }

        var emailUser = Array();
        var allUser = Array();
        var allFiles = Array();
        try {
            if (CS_id) {
                var chk = CS_id + "chk_";
            } else {
                var chk = "chk_";
            }
            $('input[id^="' + chk + '"]').each(function (i) {
                if ($(this).is(':checked')) {
                    emailUser.push($(this).val());
                }
            });
        } catch (e) {
        }
        try {
            if (done == 1) {
                if ((typeof (gFileupload) != 'undefined') && gFileupload == 0) {
                    /*if(confirm("Files upload is in progress... Are you sure you want to post?")){
                     done = 1;
                     gFileupload = 1;
                     }else{
                     done = 0;
                     }*/
                    alert(_('Oops! File upload is in Progress'));
                    document.getElementById('quickcase').style.display = 'block';
                    return false;
                } else {
                    done = 1;
                }
            }
            var editRemovedFile = '';
            if (task_uid) {
                editRemovedFile = $('#editRemovedFile').val();
            }

            var totfiles = document.getElementById(totfiles).value;
            if (parseInt(totfiles) && done == 1) {
                if (!CS_id) {
                    $('.ajxfileupload').each(function (i) {
                        allFiles.push($(this).val());
                    });
                } else {
                    for (var i = 1; i <= totfiles; i++) {
                        var fid = CS_id + "jqueryfile" + i;
                        if ($('#' + fid) && $('#' + fid).val()) {
                            allFiles.push($('#' + fid).val());
                        }
                    }
                }
                var file_size = 0;
                var storage_max = $("#max_storage").text();
                if (parseFloat(storage_max)) {
                    var storage_used = $("#used_storage").text().trim();
                    for (var indx in allFiles) {
                        var first = parseInt(allFiles[indx].indexOf("|"));
                        var second = parseInt(allFiles[indx].indexOf("|", first + 1));
                        file_size = parseFloat(file_size) + parseFloat(allFiles[indx].substring(first + 1));
                    }
                    var total_size = parseFloat(storage_used) + parseFloat(file_size / 1024);
                    total_size = total_size.toFixed('2');
                    done = 1;
                    /*if(parseFloat(total_size) <= parseFloat(storage_max)){
                     done = 1;
                     }else{
                     done = 0;
                     alert("Storage limit exceeded!\nUpgrade your account to get more storage.\n\nOR, remove any of the attached file.");
                     }*/
                }
            }
        } catch (e) {
        }

        if (!CS_id && CS_start_date.trim() != "" && CS_estimated.trim() == "") {
            done = confirm(_('Are you sure, you want to post task without estimated hour(s)?'));
        }

        if ((done == 1 && emailUser.length != "0") || (done == 1 && confirm(_("Are you sure you want to proceed without notifying anyone?")))) {
            if (!CS_id) {
                if (document.getElementById('new_case_more_div').innerHTML) {
                    CS_type_id = document.getElementById('CS_type_id').value;
                    CS_priority = document.getElementById('CS_priority').value;
                    cs_hours = $("#hours").val();
                    est_hours = $("#estimated_hours").val();
                    try {
                        CS_message = tinyMCE.activeEditor.getContent();
                    } catch (e) {
                    }
                    CS_due_date = document.getElementById('CS_due_date').value;
                    CS_milestone = document.getElementById('CS_milestone').value;
                }
                CS_assign_to = document.getElementById('CS_assign_to').value;
                var own_session_id = document.getElementById('own_session_id').value;
                if (CS_assign_to == '') {
                    CS_assign_to = own_session_id;
                } else {
                    CS_assign_to = CS_assign_to;
                }
                document.getElementById('quickcase').style.display = 'none';
                document.getElementById('quickloading').style.display = 'block';
            } else {
                var postcomments = "postcomments" + CS_id;
                var loadcomments = "loadcomments" + CS_id;
                document.getElementById(postcomments).style.display = 'none';
                document.getElementById(loadcomments).style.display = 'block';
                var cs_hours = $("#hours" + CS_id).val();
                var completed = $("#completed" + CS_id).val();
            }
            var pagename = document.getElementById('pagename').value;
            var strURL = HTTP_ROOT;
            strURL = strURL + "easycases/";
            if (!cs_hours) {
                cs_hours = 0;
            }
            if (!est_hours) {
                est_hours = 0;
            }
            var cloud_storages;
            if (CS_id) {
                cloud_storages = $('#cloud_storage_form_' + CS_id).serialize();
            } else {
                cloud_storages = $('#cloud_storage_form_0').serialize();
            }
            var is_client = 0;
            if (CR == 1) {

                if ($('#make_client').is(':checked')) {
                    is_client = 1;
                }
                if ($('#make_client_dtl').is(':checked')) {
                    is_client = 1;
                }
                if (is_client == 1) {
                    var asn_to = CS_assign_to;
                    var uarr = new Array();
                    $('.chk_client').each(function () {
                        if ($(this).is(':checked')) {
                            uarr.push($(this).val());
                        }
                    });
                    if ($.inArray(asn_to, uarr) > -1) {
                        $('#tsk_asgn_to').text(_('me'));
                        $('#CS_assign_to').val(SES_ID);
                    }
                }

            }
            if (RCT == 1) {
                var x = 0;
                if (new Date(CS_due_date).getTime() > new Date(CS_recurring_startDate).getTime()) {
                    showTopErrSucc('error', _('Task Due date can not be greater than reccurrence start date'));
                    x = 1;
                }
                if (new Date(CS_due_date).getTime() > new Date(CS_recurring_endDate).getTime()) {
                    showTopErrSucc('error', _('Task Due date can not be greater than reccurrence end date'));
                    x = 1
                }
                if (new Date(CS_recurring_endDate).getTime() < new Date(CS_recurring_startDate).getTime()) {
                    showTopErrSucc('error', _('Reccurrence End date can not be less than reccurrence start date'));
                    x = 1
                }
                if (x == 1) {
                    $('#quickloading').hide();
                    $('#quickcase').show();
                    $('#sendCaret').show();
                    return false;
                }
            }
            var tskURL = (cloud_storages) ? strURL + "ajaxpostcase?" + cloud_storages : strURL + "ajaxpostcase";
            //alert(completed);return false;
            $.post(tskURL, {
                pid: pid,
                CS_project_id: CS_project_id,
                CS_istype: CS_istype,
                CS_title: CS_title,
                CS_type_id: CS_type_id,
                CS_priority: CS_priority,
                CS_message: CS_message,
                CS_assign_to: CS_assign_to,
                CS_due_date: CS_due_date,
                CS_milestone: CS_milestone,
                postdata: postdata,
                pagename: pagename,
                emailUser: emailUser,
                allUser: allUser,
                allFiles: allFiles,
                CS_id: CS_id,
                CS_case_no: CS_case_no,
                datatype: datatype,
                CS_legend: CS_legend,
                prelegend: prelegend,
                'hours': cs_hours,
                'estimated_hours': est_hours,
                'completed': completed,
                'taskid': taskid,
                'task_uid': task_uid,
                'editRemovedFile': editRemovedFile,
                'gantt_start_date': CS_start_date,
                'is_client': is_client,
                'CS_isRecurring': CS_isRecurring,
                'CS_recurringType': CS_recurringType,
                'CS_recurring_endDate': CS_recurring_endDate,
                'CS_recurring_startDate': CS_recurring_startDate,
                'CS_occurrence': CS_occurrence,
                'CS_recurringEndType': CS_recurringEndType,
                'recurringData': recurringData
            }, function (data) {
                if (data) {
                    if (!CS_id) {
                        try {
                            if ($('#caseMenuFilters').val() != 'kanban' && $('#caseMenuFilters').val() != 'milestonelist') {
                                document.getElementById('caseMenuFilters').value = "";
                            }
                            document.getElementById('CS_assign_to').value = '';
                            document.getElementById('pageheading').innerHTML = 'Tasks';
                            tinyMCE.activeEditor.setContent('');
                        } catch (e) {
                        }
                        if (data.isAssignedUserFree != 1 && GTLG == 1) {

                        } else {
                            if (task_uid) {
                                $("#t_" + task_uid).remove();
                                showTopErrSucc('success', _('Your task has been Updated.'));
                            } else {
                                showTopErrSucc('success', _('Your task has been posted.'));
                            }
                        }
                        $('#drive_tr_0').remove();
                        $('#usedstorage').val('');
                        $('#up_files').empty();

                        if (data.storage_used) {
                            var clr = 'red';
                            var max_storage = $("#max_storage").text();
                            if (parseFloat(data.storage_used) < parseFloat(max_storage)) {
                                clr = '#84d1f2';
                            }
                            var str = "<font style='color:" + clr + "'> <span id='used_storage'>" + data.storage_used + "</span>/<b><span id='max_storage'>" + max_storage + "</span> Mb</b></font>";
                            $("#storage_spn").html(str);
                        }

                        document.getElementById('quickcase').style.display = 'block';
                        document.getElementById('quickloading').style.display = 'none';
                        //document.getElementById('new_case_more_div').innerHTML="";
                        document.getElementById('CS_title').value = "";
                        if (data.isAssignedUserFree != 1 && GTLG == 1) {

                        } else {
                            if (CONTROLLER == 'easycases' && PAGE_NAME == 'dashboard') {
                                crt_popup_close();
                            }

                            if (data.pagename == "dashboard") {
                                updateAllProj('proj' + data.formdata, data.formdata, data.pagename, '0', data.projName);
                            } else {
                                if (pagename == 'onbording') {
                                    window.location = HTTP_ROOT + "onbording";
                                } else {
                                    var rqUrl = document.URL;
                                    var n = rqUrl.indexOf("activities");
                                    if (n != -1) {
                                        window.location = HTTP_ROOT + "dashboard";
                                    } else {
                                        window.location = HTTP_ROOT + "dashboard";
                                    }
                                }

                            }
                        }
                        var CS_project_id = document.getElementById('CS_project_id').value;
                    } else {
                        //updateCaseListing(CS_id,cnt,postdata,dtls,CS_assign_to,data.format,CS_legend,prelegend);
                        easycase.refreshTaskList(uniqid);
                        if (typeof data.depend_msg != 'undefined') {
                            showTopErrSucc('success', data.depend_msg);
                        } else {
                            showTopErrSucc('success', _('Your reply is posted.'));
                        }

                        if (data.storage_used) {
                            var clr = 'red';
                            var max_storage = $("#max_storage").text();
                            if (parseFloat(data.storage_used) < parseFloat(max_storage)) {
                                clr = 'green';
                            }
                            var str = "<font style='color:" + clr + "'> <span id='used_storage'>" + data.storage_used + "</span>/<b><span id='max_storage'>" + max_storage + "</span> Mb</b></font>";
                            $("#storage_spn").html(str);
                        }

                        try {
                            if (!CS_legend) {
                                document.getElementById('actionCls' + cnt).value = 2;
                            } else {
                                document.getElementById('actionCls' + cnt).value = CS_legend;
                            }
                            var actionChk = "actionChk" + cnt;
                            if (postdata == "Post") {
                                var xdata = document.getElementById(actionChk).value;
                                var exdt = xdata.split("|");
                                if (exdt[2] == "closed") {
                                    $("#" + actionChk).removeAttr('disabled');
                                    $("#" + actionChk).removeAttr('checked');
                                }
                            } else {
                                document.getElementById(actionChk).disabled = true;
                                document.getElementById(actionChk).checked = true;
                            }
                        } catch (e) {
                        }

                        var project_id = "CS_project_id" + CS_id;
                        var CS_project_id = document.getElementById(project_id).value;

                        $.post(HTTP_ROOT + "easycases/update_assignto", {
                            "caseId": CS_id
                        }, function (res) {
                            if (res) {
                                $('#showUpdAssign' + CS_id).html(res);
                            }
                        });

                        var caseMenuFilters = document.getElementById('caseMenuFilters').value;
                        var url = HTTP_ROOT + "easycases/ajax_case_status";

                        var case_date = $("#caseDateFil").val();
                        var caseStatus = $("#caseStatus").val();
                        var caseTypes = $("#caseTypes").val();
                        var caseMember = $("#caseMember").val();
                        var caseAssignTo = $("#caseAssignTo").val();
                        var caseSearch = $("#case_search").val();
                        var priFil = $("#priFil").val();
                        var milestoneIds = $("#milestoneIds").val();
                        var checktype = $("#checktype").val();

                        $.post(url, {
                            "projUniq": CS_project_id,
                            "pageload": 1,
                            "caseMenuFilters": caseMenuFilters,
                            'case_date': case_date,
                            'caseStatus': caseStatus,
                            'caseTypes': caseTypes,
                            'priFil': priFil,
                            'caseMember': caseMember,
                            'caseAssignTo': caseAssignTo,
                            'caseSearch': caseSearch,
                            'milestoneIds': milestoneIds,
                            'checktype': checktype
                        }, function (data) {
                            //$.post(url,{"projUniq":CS_project_id,"pageload":1,"caseMenuFilters":caseMenuFilters}, function(data)
                            if (data) {

                                //$('#ajaxCaseStatus').html(data);
                                $('#ajaxCaseStatus').html(tmpl("case_widget_tmpl", data));

                                $('[rel=tooltip], #main-nav span, .loader').tipsy({
                                    gravity: 's',
                                    fade: true
                                });
                                $('.tooltip_widget').tipsy({
                                    gravity: 'e',
                                    fade: true
                                });

                                $('.close-widget').click(
                                        function () {
                                            $(this).parent().fadeTo(350, 0, function () {
                                                $(this).slideUp(600);
                                            }); // Hide widgets
                                            return false;
                                        }
                                );

                                if (document.getElementById('reset_btn').style.display != 'none') {
                                    $('#upperDiv_alert').fadeIn();
                                    setTimeout(removeMsg_alert, 6000);
                                } else {
                                    $('#upperDiv_alert').fadeOut();
                                }
                            }
                        });
                    }
                    if (data.isAssignedUserFree != 1 && GTLG == 1 && data.isAssignedUserFree != null) {
                        CS_start_date = typeof data.reply_strt_date != 'undefined' ? data.reply_strt_date : CS_start_date;
                        CS_due_date = typeof data.reply_due_date != 'undefined' ? data.reply_due_date : CS_due_date;
                        est_hours = typeof data.reply_estimated != 'undefined' ? data.reply_estimated : est_hours;
                        data.caseid = typeof data.reply_caseId != 'undefined' ? data.reply_caseId : data.caseid;
                        data.caseUniqId = typeof data.reply_caseUniqId != 'undefined' ? data.reply_caseUniqId : data.caseUniqId;
                        openResourceNotAvailablePopup(CS_assign_to, CS_start_date, CS_due_date, est_hours, data.projId, data.caseid, data.caseUniqId, data.isAssignedUserFree)
                            }
                    if (data.caseNo) {
                        var url_ajax = strURL + "ajaxemail";
                        $.post(url_ajax, {
                            'projId': data.projId,
                            'emailUser': emailUser,
                            "allfiles": data.allfiles,
                            'caseNo': data.caseNo,
                            'emailTitle': data.emailTitle,
                            'emailMsg': data.emailMsg,
                            'casePriority': data.casePriority,
                            'caseTypeId': data.caseTypeId,
                            'msg': data.msg,
                            'emailbody': data.emailbody,
                            'caseIstype': data.caseIstype,
                            'csType': data.csType,
                            'caUid': data.caUid,
                            'caseid': data.caseid,
                            'caseUniqId': data.caseUniqId
                        });
                    }
                    //check size
                    check_proj_size();

                    /*$.post(HTTP_ROOT+"easycases/ajax_case_menu",{"projUniq":CS_project_id,"pageload":1,"page":"dashboard"}, function(res){
                     if(res) {
                     $('#ajaxMenucaseNo').html(res);
                     $('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true});
                     }
                     });*/
                }

                loadCaseMenu(HTTP_ROOT + "easycases/ajax_case_menu", {
                    "projUniq": CS_project_id,
                    "pageload": 1,
                    "page": "dashboard"
                });
                var projUpdateTop = $("#projUpdateTop").html();
                $("#pname_dashboard").html(projUpdateTop);
                $('#defaultmem').show();

            }, 'json');
            //ajaxCaseView('case_project.php');
        } else {
            return false;
        }
    }
}
function openResourceNotAvailablePopup(CS_assign_to, CS_start_date, CS_due_date, est_hours, projId, caseid, caseUniqId, isAssignedFree) {
    openPopup();
    $('#inner_resource_notavailable').html('');
    if(isAssignedFree == 3){
        $('#rsrc_not_avail_title').html(_('Resource on Leave'));
    }else{
        $('#rsrc_not_avail_title').html(_('Resource Not Available'));
    }
    $(".resource_notavailable").show();
     var not_available_ajax = HTTP_ROOT + 'Timelog/logTimes/checkAvailableUsers';
    $.post(not_available_ajax, {'assignedId': CS_assign_to, 'gantt_start_date': CS_start_date, 'CS_due_date': CS_due_date, 'estimated_hours': est_hours, 'project_id': projId, 'caseid': caseid, 'caseuniqid': caseUniqId}, function (res) {
        if (res) {
            $('.loader_dv').hide();
            $('#inner_resource_notavailable').html(res);
        }
    });
}
function blur_txt() {
    $("#CS_title").css({
        color: "#666666"
    });
    if ($("#CS_title").val() == "") {
        //$("#CS_title").val("Add a task here and hit enter...");
    }
    if ($("#CS_title").val() != "Add a task here and hit enter...") {
        $("#CS_title").css({
            color: "#000000"
        });
    }
}
function checkAllProj() {
    var projFil = document.getElementById('CS_project_id').value; // Project Uniq ID
    if (projFil == 'all') {
        //alert('Oops! you have not selected any project.');
        document.getElementById('projAllmsg').style.display = 'block';
        return false;
    } else {
        $('#projAllmsg').hide();
        return true;
    }
}
function focus_txt() {
    $("#CS_title").css({
        color: "#000"
    });
    if ($("#CS_title").val() == "Add a task here and hit enter...") {
        $("#CS_title").val("");
    }
}
function onEnterPostCase(e)
{
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode == 13 && !$('#quickloading').is(':visible')) {
        submitAddNewCase('Post', 0);
    }
}
function checktitle_value() {
    var tasktitle = $.trim($('#CS_title').val());
    if (tasktitle == "" || tasktitle == "Add a task here and hit enter...") {
    } else {
        $('#CS_title').css('border-color', '');
    }
}
function check_proj_size() {
    if ($('#add_new_popup').is(":visible")) {
        var sizeUrl = HTTP_ROOT + "easycases/";
        $.post(sizeUrl + "ajax_check_size", {
            "check": 'size'
        }, function (data) {
            if (data) {

                $("#ajax_check_size").html(data);

                var isExceed = $("#isExceed").val();
                $("#usedstorage").val($("#storageusedqc").val());
            }
        });
    }
}
function search_project_easypost(val, e) {

    var key = e.keyCode;
    if (key == 13)
        return;
    var menu_div_id = 'ajaxbeforesrchc';
    if ($('#ajaxaftersrchc').is(":visible")) {
        var menu_div_id = 'ajaxaftersrchc';
        $('#ajaxbeforesrchc > li').removeClass('popup_selected');
    }

    if (e.keyCode == 40 || e.keyCode == 38) {
        if (key == 40) { // Down key
            if (!$('#' + menu_div_id + ' > a').length || $('#' + menu_div_id + '> a').filter('.popup_selected').is(':last-child')) {
                $current = $('#ajaxaftersrchc > a').eq(0);
                //$current.addClass('popup_selected');
            } else {
                if ($('#' + menu_div_id + '> a').hasClass('popup_selected')) {
                    $current = $('#' + menu_div_id + '> a').filter('.popup_selected').next('hr').next('a');
                } else {
                    $current = $('#' + menu_div_id + ' > a').eq(0);
                }
            }
        } else if (key == 38) {// Up key
            if (!$('#' + menu_div_id + ' > a').length || $('#' + menu_div_id + '> a').filter('.popup_selected').is(':first-child')) {
                $current = $('#' + menu_div_id + ' > a').last('a');
            } else {
                $current = $('#' + menu_div_id + ' > a').filter('.popup_selected').prev('hr').prev('a');
            }
        }
        $('#' + menu_div_id + ' > a').removeClass('popup_selected');
        $current.addClass('popup_selected');
    } else {
        var strURL = HTTP_ROOT;
        strURL = strURL + "users/";
        if (val != "") {
            $('#load_find_addtask').show();
            $.post(strURL + "search_project_menu", {
                "val": val
            }, function (data) {
                if (data) {
                    $('#ajaxaftersrchc').show();
                    $('#ajaxbeforesrchc').hide();
                    $('#ajaxaftersrchc').html(data);
                    $('#load_find_addtask').hide();
                }
            });
        } else {
            $('#ajaxaftersrchc').hide();
            $('#ajaxbeforesrchc').show();
            $('#load_find_addtask').hide();
        }
    }



//	var strURL = HTTP_ROOT+"users/";
//	if(val!=""){
//	     $('#load_find_addtask').show();
//	     $.post(strURL+"search_project_menu",{"val":val}, function(data){
//		       if(data) {
//			     $('#ajaxaftersrchc').show();
//			     $('#ajaxbeforesrchc').hide();
//			     $('#ajaxaftersrchc').html(data);
//			     $('#load_find_addtask').hide();
//		       }
//	     });
//     }else{
//	     $('#ajaxaftersrchc').hide();
//	     $('#ajaxbeforesrchc').show();
//	     $('#load_find_addtask').hide();
//	}
}
function createCookie(name, value, days, domain) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else
        expires = "";
    if (domain)
        var domain = " ; domain=" + DOMAIN_COOKIE;
    else
        var domain = '';
    document.cookie = name + "=" + value + expires + "; path=/" + domain;
}
function removeFile(id, div, storage) {
    var x = document.getElementById(id).value;
    document.getElementById(id).value = '';
    var strURL = HTTP_ROOT + "easycases/";
    if (storage) {
        var usedstorage = $("#usedstorage").val();
        var newstorage = usedstorage - storage;
        $("#usedstorage").val(newstorage);
    }
    $.post(strURL + "fileremove", {
        "filename": x
    }, function (data) {
        if (data) {
        }
    });
    $('#' + div).parent().parent().remove();
}
function hideEditFile(id, div, storage, caseFileId) {
    var x = document.getElementById(id).value;
    document.getElementById(id).value = '';
    var strURL = HTTP_ROOT + "easycases/";
    if (storage) {
        var usedstorage = $("#usedstorage").val();
        var newstorage = usedstorage - storage;
        $("#usedstorage").val(newstorage);
    }
    //	$.post(strURL+"fileremove",{"filename":x},function(data) {
    //		if(data) {
    //		}
    //	});
    var remfile = $('#editRemovedFile').val();
    if (remfile) {
        $('#editRemovedFile').val(remfile + "," + caseFileId);
    } else {
        $('#editRemovedFile').val(caseFileId);
    }

    $('#' + div).parent().parent().hide();
}
function cancelReplyFile(file_name) {
    if (reply_total_files.length) {
        reply_total_files.pop(file_name);
    }

    if (reply_total_files.length == 0) {
        gFileupload = 1;
    }
}
function removefile() {
    open_pop(this);
    var pjid = document.getElementById('pjid').value;
    var count = document.getElementById("all").value;
    var val = new Array();
    for (var i = 1; i <= count; i++) {
        if (document.getElementById("file" + i).checked == true) {
            val.push(document.getElementById("file" + i).value);
        }
    }
    var url = HTTP_ROOT + "archives/file_remove";
    if (val.length != '0') {
        if (confirm(_("Are you sure you want to remove?"))) {
            document.getElementById('caseLoader').style.display = "block";
            $.post(url, {
                "val": val
            }, function (data) {
                if (data) {
                    showTopErrSucc('success', _('File is removed.'));
                    var url = HTTP_ROOT + "archives/file_list";
                    $.post(url, {
                        "pjid": pjid
                    }, function (data) {
                        if (data) {
                            document.getElementById('caseLoader').style.display = "none";
                            $('#filelistall').html(data);
                        }
                    });
                }
            });
        }
    } else {
        alert(_("No file selected!"));
    }
}
function checkedAllRes() {
    var ids = $('#userIds').val();
    ids = ids.split(',');
    var names = $('#userNames').val();
    names = names.split(',');

    if ($('#chked_all').is(":checked")) {
        $('.viewmemdtls_cls').show();
        $('.notify_cls').prop("checked", true);
        if ($('#make_client').prop("checked")) {
            $('.chk_client').prop("checked", false);
        } else {
            $('.chk_client').prop("checked", true);
        }
    } else {
        $('.notify_cls').prop("checked", false);
        $('.chk_client').prop("checked", false);
    }
}
function removeAll() {
    if (!$('input.notify_cls[type=checkbox]:not(:checked)').length) {
        $('#chked_all').attr("checked", "checked");
    } else {
        $('#chked_all').removeAttr("checked");
    }
}
function removeAllReply(CS_id) {
    if (!$('input.chk_fl[type=checkbox]:not(:checked)').length) {
        $('#' + CS_id + 'chkAllRep').attr("checked", "checked");
    } else {
        $('#' + CS_id + 'chkAllRep').removeAttr("checked");
    }
//		var allchk = CS_id+'chkAllRep';
//		document.getElementById(allchk).checked = false;
}
function showHideMemDtls(cls) {
    if ($('.' + cls).css('display') == 'none') {
        $('.' + cls).slideDown(200);
    } else {
        $('.' + cls).slideUp(200);
    }
    $('#defaultmem').slideUp();
}
function show_prjlist(event) {
    if ($('.more_opt').find('ul').is(":visible")) {
        $('.more_opt').find('ul').hide();
    }
    event.preventDefault();
    event.stopPropagation();
    $('#openpopup').toggle();
    $('#ajaxbeforesrchc').show();
    $('#ctask_input_id').focus();
}
$(document).ready(function (event) {
    $(document).click(function (e) {
        $('#openpopup').hide();
        $('#mlstnpopup').hide();
    });
    $("#switch_mlstn").click(function (event) {
        event.stopPropagation();
    });
});
function showProjectName(name, id, mid) {
    $('#prjchange_loader').show();
    $('#ctask_popup a').css({
        'border-color': '#CCCCCC'
    })
    $('#projUpdateTop').html(decodeURIComponent(name));
    $('#CS_project_id').val(id);
    $('#openpopup').hide();
    $('#projAllmsg').hide();
    $('#curr_active_project').val(id);

    if (countJS(PUSERS) && PUSERS[id]) {
        dassign = {};
        var url = HTTP_ROOT + "easycases/ajax_quickcase_mem";
        $.post(url, {
            "projUniq": id,
            "pageload": 0
        }, function (data) {
            if (data) {
                PUSERS = data.quickMem;
                defaultAssign = data.defaultAssign;
                dassign = data.dassign;
                case_quick();
            }
        });
        $('#prjchange_loader').hide();
        /*if(!getCookie("crtdtsk_less") || getCookie("crtdtsk_less")!=1){
         opencase();
         }*/
        //scrollPageTop();
    } else {
        // Quick case User Listing
        var url = HTTP_ROOT + "easycases/ajax_quickcase_mem";
        $.post(url, {
            "projUniq": id,
            "pageload": 0
        }, function (data) {
            if (data) {
                PUSERS = data.quickMem;
                defaultAssign = data.defaultAssign;
                dassign = data.dassign;
                //$('#ajxQuickMem').html(data);
                case_quick();
                $('#prjchange_loader').hide();
                /*if(!getCookie("crtdtsk_less") || getCookie("crtdtsk_less")!=1){
                 opencase();
                 }*/
                //scrollPageTop();
            }
        });
    }
    if (mid != '') {
        milstoneonTask($('#main-title-holder_' + mid + ' a').text(), mid);
    } else {
        milstoneonTask();
    }
// Quick case User Listing
    /*var url = HTTP_ROOT+"easycases/ajax_default_email";
     $.post(url,{"projUniq":id,"pageload":0}, function(data){
     if(data) {
     $('#displayMembers').html(data);
     if($('#totaldefault').val() != 0) {
     $('#defaultmem').show();
     }
     else {
     $('#defaultmem').hide();
     }
     }
     });*/
//opencase('changeproj');
}
// Quick case
function opencase(type) {
    /*if($('#new_case_more_div').html() == "" || type == "changeproj") {
     $('#loadquick').show();
     
     var sel_myproj = $("#CS_project_id").val();
     var url = HTTP_ROOT;
     casequick = url+"easycases/";
     $.post(casequick+"case_quick",{newcase:1,sel_myproj:sel_myproj},function(res){
     $('#prjchange_loader').hide();
     $("#new_case_more_div").slideDown(300);
     $("#new_case_more_div").html(res);
     $("#more_tsk_opt_div").hide();
     $("#less_tsk_opt_div").show();
     $('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true});
     $('#loadquick').hide();
     $("#usedstorage").val($("#storageusedqc").val());
     //$("#new_case_more_div").slideDown(300);
     //$("#wrapper").css({minHeight:"960px"});
     });
     }else {
     if($('#new_case_more_div').is(":visible")){
     $("#new_case_more_div").slideUp();
     $("#more_tsk_opt_div").show();
     $("#less_tsk_opt_div").show();
     $('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true});
     //$("#wrapper").css({minHeight:"960px"});
     }else{
     $("#new_case_more_div").slideDown();
     $("#more_tsk_opt_div").hide();
     $("#less_tsk_opt_div").show();
     //$("#wrapper").css({minHeight:"960px"});
     }
     }*/
    /*if(typeof type != 'undefined' && type=='click'){
     createCookie("crtdtsk_less", '1', -365, DOMAIN_COOKIE);
     }*/
    $("#new_case_more_div").slideDown();
    $("#more_tsk_opt_div").hide();
    $("#less_tsk_opt_div").show();
    if (RCT == 1) {
        //$('.isRecurring').show();
    }
//scrollPageTop();
}
// Create Task Scroll top
function scrolltop() {
    scrollPageTop();
}
//function select_notify_member(){
//	 $('#custm_email_list_div').show();
//	 getAutocompleteTag("custom_email_list", "users/getProjects", "340px", "Type to select projects");
//}
// Daily Update Alerts ends

function scrollPageTop(el) {
    if (typeof el !== 'undefined' && el) {
        $('html, body').animate({
            scrollTop: el.offset().top - 200
        }, 1000);
    } else {
        $('html, body').animate({
            scrollTop: 0
        });
    }
}
function removePubnubMsg() {
    $('#punnubdiv').fadeOut(300);
    $("#pub_counter").val(0);
    $("#hid_casenum").val(0);
    ioMsgClicked = 1;
    easycase.refreshTaskList();
}
//chrome desktop notification function
function notify(title, desc) {
    if (DESK_NOTIFY) {
        notifyMe(title, desc, HTTP_IMAGES + 'transparent_logo.png');
        return true;
    }
    if (DESK_NOTIFY && window.webkitNotifications) {
        var havePermission = window.webkitNotifications.checkPermission();
        if (havePermission == 0) {
            // 0 is PERMISSION_ALLOWED
            var notification = window.webkitNotifications.createNotification(
                    HTTP_IMAGES + 'transparent_logo.png',
                    title,
                    desc
                    );
            notification.onclick = function () {
                try {
                    window.focus();
                    removePubnubMsg();
                    notification.cancel();
                } catch (e) {
                }
            };
            setTimeout(function () {
                try {
                    notification.cancel();
                } catch (e) {
                }
            }, 10000);
            notification.show();
        } else {
            window.webkitNotifications.requestPermission();
        }
    }
}
function allowChromeDskNotify(check) {
    if ((DESK_NOTIFY || check) && window.webkitNotifications && window.webkitNotifications.checkPermission() != 0) {
        window.webkitNotifications.requestPermission();
    }
}
function getImNotifyMsg(projShName, caseNum, caseTtl, caseTyp) {
    var action = '';
    switch (caseTyp) {
        case 'NEW':
            action = "New Task Created";
            break;
        case 'UPD':
            action = "Task Updated";
            break;
        case 'ARC':
            action = "Task Archived";
            break;
        case 'DEL':
            action = "Task Deleted";
            break;
        default:
            action = "New Notification";
    }
    return action + ': ' + projShName + '# ' + caseNum + ' - ' + caseTtl;
}
function notifyMe(title, desc, icon) {
    //https://developer.mozilla.org/en/docs/Web/API/notification
    // Let's check if the browser supports notifications
    if (!("Notification" in window)) {
        //alert("This browser does not support desktop notification");
    }

    // Let's check if the user is okay to get some notification
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        var notification = new Notification(title, {body: desc, icon: icon});
    }

    // Otherwise, we need to ask the user for permission
    // Note, Chrome does not implement the permission static property
    // So we have to check for NOT 'denied' instead of 'default'
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            //alert(permission);
            // Whatever the user answers, we make sure we store the information
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            // If the user is okay, let's create a notification
            if (permission === "granted") {
                var notification = new Notification(title, {body: desc, icon: icon});
            }
        });
    }

    // At last, if the user already denied any notification, and you 
    // want to be respectful there is no need to bother him any more.
}
//end chrome desktop notification function

//JS function from case_quick.ctp
function numericDecimal(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8)
    {
        if (unicode < 9 || unicode > 9 && unicode < 46 || unicode > 57 || unicode == 47) {
            if (unicode == 37 || unicode == 38) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else
    {
        return true;
    }
}
function notified_users(uid) {
    $('#chk_' + uid).attr('checked', 'checked');
}
function check_priority(obj) {
    $(obj).find('input:radio').attr('checked', 'checked');
    var pvalue = $(obj).find('input:radio').val();
    $("#CS_priority").val(pvalue);
}
function closecase() {
    $("#new_case_more_div").slideUp(200);
    $("#more_tsk_opt_div").show();
    $("#less_tsk_opt_div").hide();
    if (RCT == 1) {
        $('.isRecurring').hide();
        $('#recurring_task_block').hide();
        $('#is_recurring').removeAttr('checked');
    }
    //$("#wrapper").css({minHeight:"550px"});
    scrollPageTop();
//createCookie("crtdtsk_less", '1', 365, DOMAIN_COOKIE);
}
function open_more_opt(more_opt) {
    $('.more_opt').filter(':not(#' + more_opt + ')').children('ul').hide();
    $("#" + more_opt).children("ul").toggle();
}
function getSelectedValue(id) {
    return $("#" + id).find("a span.value").html();
}
function getTmplvalue(objId) {
    var text = $(objId).html();
    var path = $(objId).closest("ul").parent("div").prev("div").attr("id");
    $("#" + path).children("a").children("span").html(text);
    $("#" + path).children("a").children("span").children("a").css({
        'display': 'inline-block'
    });
    var tmpl_id = $("#" + path).children("a").find("span.value").text();
    $('#hid_tmpl').val(tmpl_id);
    $("#" + path).next("div").children("ul").hide();
    $('#header_crtprjtmpl').text(_('Update Project Template'));
    $('#crtprjtmpl_btn').find('#tmpl-btn').text(_('Update'));
    //Get All Milestones
    var strURL = HTTP_ROOT + "projecttemplate/ProjectTemplates/getMilestones";
    $.ajax({
        url: strURL,
        type: 'POST',
        dataType: 'json',
        data: {tmpl_id: tmpl_id},
    })
            .done(function (res) {
                $('.temp-late-mlstsn li:not(:last)').remove();
                $('#hid_mlstntemp').val('');
                $.each(res.ProjectTemplateMilestone, function (index, val) {
                    var str = val.title;
                    str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
                        return letter.toUpperCase();
                    });
                    $('.temp-late-mlstsn').find("#last_new_tem_mlstn").before('<li onclick="setMlstnvalue(this);"><a class="ellipsis-view" href="javascript:void(0);"  rel="tooltip" title="' + val.title + '"><span class="value">' + val.id + '</span>' + str + '</a></li>');
                });
            });
}
function setMlstnvalue(obj) {
    var text = $(obj).html();
    var path = $(obj).closest("ul").parent("div").prev("div").attr("id");
    $("#" + path).children("a").children("span").html(text);
    $("#" + path).children("a").children("span").children("a").css({
        'display': 'inline-block'
    });
    var mlst_id = $("#" + path).children("a").find("span.value").text();
    $('#hid_mlstntemp').val(mlst_id);
    $("#" + path).next("div").children("ul").hide();
}
$(document).on("click", 'body', function (e) {
    $(e.target).closest('div').hasClass('opt50') ? $('#more_opt50').find('ul').show() : $('#more_opt50').find('ul').hide();
});
function showRecurringTask() {
    if ($('#is_recurring').is(':checked')) {
        $('#recurring_task_block').show();
        var case_end_date = '';
        if ($("#CS_due_date").val() != '' && $("#CS_due_date").val() != 'No Due Date') {
            case_end_date = formatDate('M d, yy', $("#CS_due_date").val());
            console.log(case_end_date + '----');
            $('#start_datePicker').val($("#date_dd").text());
        } else {
            var today = new Date();
            case_end_date = formatDate('M d, yy', today);
            console.log(case_end_date);
            $('#start_datePicker').val(case_end_date);
            $('#CSrepeat_start_date').val(case_end_date);
        }
        $("#occur, #date").removeAttr('disabled');
        $("#date").prop('checked', false);
        $("#occur").prop('checked', true);
        $('#occurrence, #start_datePicker').removeAttr('disabled', 'disabled');
        $('#repeat_type').find('span').text(_('Weekly'));
        $('#CSrepeat_type').val('Weekly');
        $('#occurrence').val('1');
        $('#end_datePicker').datepicker({
            altField: "#CSrepeat_due_date",
            dateFormat: 'M d, yy',
            altFormat: 'yy-m-d',
            changeMonth: false,
            changeYear: false,
            minDate: 0,
            hideIfNoPrevNext: true,
        });
        $('#start_datePicker').datepicker({
            altField: "#CSrepeat_start_date",
            dateFormat: 'M d, yy',
            altFormat: 'yy-m-d',
            changeMonth: false,
            changeYear: false,
            minDate: case_end_date,
            hideIfNoPrevNext: true,
        });
    } else {
        $('#repeat_txt').text(_('Repeat'));
        $('#repeat_type').html("<span class='ttfont'>  " + _("None") + "</span>");
        $("#CSrepeat_type").val('');
        $("#occur, #date").attr('disabled', 'disabled');
        $("#occur, #date").prop('checked', false);
        $('#occurrence, #end_datePicker, #start_datePicker').attr('disabled', 'disabled').val('');
        $('#recurring_task_block').hide();
    }
}
function addTaskEvents() {
    $(".more_opt ul li a").click(function () {
        var text = $(this).html();
        var path = $(this).parent("li").parent("ul").parent("div").prev("div").attr("id");
        $("#" + path).children("a").children("span").html(text);

        if (path == "opt3") {
            var hidden_val = $("#" + path).find("a span.value").html();
            if (RCT == 1) {
                var case_end_date = '';
                if (hidden_val != '' && hidden_val != 'No Due Date') {
                    $('#start_datePicker').val(hidden_val);
                    $("#end_datePicker").val(hidden_val);
                } else {
                    var today = new Date();
                    case_end_date = formatDate('M d, yy', today);
                    $('#start_datePicker').val(case_end_date);
                    $("#end_datePicker").val(case_end_date);
                }
            }
            $("#date_dd").html(hidden_val);
            $("#CS_due_date").val(hidden_val);
            if (RCT == 1) {
                $("#CS_repeat_start_date").val(formatDate('yy-m-d', hidden_val));
                $("#start_datePicker").datepicker("option", "minDate", case_end_date);
            }
        } else if (path == "opt2") {
            $("#CS_priority").val(getSelectedValue("opt2"));
        } else if (path == "opt4") {
            $("#CS_milestone").val(getSelectedValue("opt4"));
        } else if (path == "opt5") {
            $("#CS_assign_to").val(getSelectedValue("opt5"));
        } else if (path == "opt8") {
            $("#CS_milestone").val(getSelectedValue("opt8"));
        } else if (path == 'opt40') {
            var hidden_val = $("#" + path).find("a span.value").html();
            $("#repeat_type").html('<span class="ttfont">' + hidden_val + '</span>');
            if (hidden_val == 'None') {
                $("#CSrepeat_type").val('');
                $("#start_datePicker").attr('disabled', 'disabled').val('');
                $("#occur, #date").attr('disabled', 'disabled');
                $("#occur, #date").prop('checked', false);
                $('#occurrence, #end_datePicker').attr('disabled', 'disabled').val('');
            } else {
                $("#CSrepeat_type").val(hidden_val);
                $("#occur, #date").removeAttr('disabled');
                $("#occur").prop('checked', true);
                $('#occurrence').removeAttr('disabled');
                $("#start_datePicker").removeAttr('disabled');
                $('#occurrence').val('1');
                $('#end_datePicker').attr('disabled', 'disabled');
                var case_end_date = '';
                if ($("#date_dd").html() != '' && $("#date_dd").html() != 'No Due Date') {
                    $('#start_datePicker').val($("#date_dd").html());
                    $("#end_datePicker").val($("#date_dd").html());
                } else {
                    var today = new Date();
                    case_end_date = formatDate('M d, yy', today);
                    $('#start_datePicker').val(case_end_date);
                    $("#end_datePicker").val(case_end_date);
                }
            }
        } else {
            $("#CS_type_id").val(getSelectedValue("opt1"));
            $('#task_priority_td table').show();
            if ($("#CS_type_id").val() == 10) {
                $('#task_priority_td table').hide();
                $("#CS_title").val(TITLE_DLYUPD);
                document.getElementById("CS_title").style.color = '#000';
            } else if ($("#CS_type_id").val() != 10 && $("#CS_title").val() == TITLE_DLYUPD) {
                document.getElementById("CS_title").value = "";
            }
        }
        $("#" + path).next("div").children("ul").hide();
    });

    $(document).bind('click', function (e) {
        var $clicked = $(e.target);
        if (!($clicked.parents().hasClass("dropdown")) && !($('#ui-datepicker-div').is(":visible"))) {
            $(".dropdown .more_opt ul").hide();
        }
    });

    $("#due_date").datepicker({
        altField: "#CS_due_date",
        dateFormat: 'M d, D',
        showOn: "button",
        buttonImage: HTTP_IMAGES + "images/calendar.png",
        buttonStyle: "background:#FFF;",
        changeMonth: false,
        changeYear: false,
        minDate: 0,
        hideIfNoPrevNext: true,
        onSelect: function (dateText, inst) {
            $("#date_dd").html(dateText);
            $("#more_opt3").children("ul").hide();
            if (RCT == 1) {
                $("#end_datePicker").val(dateText);
                $("#start_datePicker").val(dateText);
                $("#CS_repeat_start_date").val(formatDate('yy-m-d', dateText));
            }
        }
    });
    $("#gantt_start_date").datepicker({
        altField: "#CS_start_date",
        dateFormat: 'M d, D',
        altFormat: 'yy-m-d',
        changeMonth: false,
        changeYear: false,
        minDate: 0,
        hideIfNoPrevNext: true,
        onSelect: function (dateText, inst) {
            if ($('#estimated_hours').val() == '') {
                $("#date_dd").html(dateText);
                $("#CS_due_date").val(moment($('#CS_start_date').val(), 'YYYY-MM-DD').format('YYYY-MM-DD'));
                $("#more_opt3").children("ul").hide();
            } else {
                var days = Math.ceil($('#estimated_hours').val() / CompWorkHR) - 1;
                var due_date = moment($('#CS_start_date').val(), 'YYYY-MM-DD').add(days, 'days');
                $("#date_dd").html(due_date.format('MMM DD, ddd'));
                $("#CS_due_date").val(due_date.format('YYYY-MM-DD'));
            }
        }
    });
}

function changeDate() {
    var estimatedTime = $('#estimated_hours').val();
    var startDate = $('#CS_start_date').val();
    if (estimatedTime != '' && startDate != '') {
        var days = Math.ceil(estimatedTime / CompWorkHR) - 1;
        var due_date = moment(startDate, 'YYYY-MM-DD').add(days, 'days');
        $("#date_dd").html(due_date.format("MMM DD, ddd"));
        $("#CS_due_date").val(due_date.format("MMM DD, ddd"));
    }
}
//END JS function from case_quick.ctp

//Milestone starts
function addEditMilestone(obj, mileuniqid, mid, name, cnt, mlstfrom) {
    if (typeof mlstfrom == 'undefined') {
        mlstfrom = '';
    }
    if (mlstfrom == 'createTask') {
        var projUid = $('#curr_active_project').val();
    } else if (mlstfrom == 'dashboard') {
        var projUid = $('#curr_active_project').val();
    } else {
        var projUid = $('#projFil').val();
    }
    if (obj) {
        var mid = $(obj).attr("data-id");
        var mileuniqid = $(obj).attr("data-uid");
        var name = $(obj).attr("data-name");
    }
    openPopup();
    $(".mlstn").show();
    $('#inner_mlstn').html('');
    $("#addeditMlst").show();
    if (mid === '') {
        $("#icon_mlstn").removeClass("icon-edit-projct");
        $("#icon_mlstn").addClass("icon-create-proj");
        $("#header_mlstn").html("Create Milestone");
    } else {
        $("#icon_mlstn").removeClass("icon-create-proj");
        $("#icon_mlstn").addClass("icon-edit-projct");
        $("#header_mlstn").html(name);
        $('#header_mlstn').attr('title', name);
    }
    $.post(HTTP_ROOT + "milestones/ajax_new_milestone", {
        'mid': mid,
        'mileuniqid': mileuniqid,
        'mlstfrom': mlstfrom,
        'projUid': projUid
    }, function (res) {
        if (res) {
            $("#addeditMlst").hide();
            $('#inner_mlstn').show();
            $('#inner_mlstn').html(res);
            $("#project_id").focus();
            refreshManageMilestone = 1;
        }
    });
}
function assignToUser(obj, mileuniqid, mid, name, cnt, mlstfrom) {
    if (typeof mlstfrom == 'undefined') {
        mlstfrom = '';
    }
    if (mlstfrom == 'createTask') {
        var projUid = $('#curr_active_project').val();
    } else if (mlstfrom == 'dashboard') {
        var projUid = $('#curr_active_project').val();
    } else {
        var projUid = $('#projFil').val();
    }
    if (obj) {
        var mid = $(obj).attr("data-id");
        var mileuniqid = $(obj).attr("data-uid");
        var name = $(obj).attr("data-name");
		name = 'Assign user to <b>'+name+'</b> milestone';
    }
    openPopup();
    $(".mlstn").show();
    $('#inner_mlstn').html('');
    $("#addeditMlst").show();
	var url = getHash();
	url = (url == '')?'milestonelist':url;
    if (mid === '') {
        $("#icon_mlstn").removeClass("icon-edit-projct");
        $("#icon_mlstn").addClass("icon-create-proj");
        $("#header_mlstn").html("Create Milestone");
    } else {
        $("#icon_mlstn").removeClass("icon-create-proj");
        $("#icon_mlstn").addClass("icon-edit-projct");
        $("#header_mlstn").html(name);
        $('#header_mlstn').attr('title', name);
    }
    $.post(HTTP_ROOT + "milestones/assign_to_user", {
        'mid': mid,
        'mileuniqid': mileuniqid,
        'mlstfrom': mlstfrom,
        'projUid': projUid
    }, function (res) {
        if (res) {
            $("#addeditMlst").hide();
            $('#inner_mlstn').show();
            $('#inner_mlstn').html(res);
			$('#urlname_assin_user').val(url);
            $("#project_id").focus();
            refreshManageMilestone = 1;
        }
    });
}
function delMilestone(obj, name, uniqid) {
    if (obj) {
        var uniqid = $(obj).attr("data-uid");
        var name = decodeURIComponent($(obj).attr("data-name"));
    }
    if (confirm(_("Are you sure you want to delete milestone") + " '" + name + "' ?")) {
        //window.location.href = HTTP_ROOT+"milestones/delete_milestone/"+uniqid;
        var loc = HTTP_ROOT + "milestones/delete_milestone/";
        $.post(loc, {
            'uniqid': uniqid
        }, function (res) {
            if (res.err == 1) {
                showTopErrSucc('error', res.msg);
            } else {
                showTopErrSucc('success', res.msg);
                refreshManageMilestone = 1;
            }
            if ($('#caseMenuFilters').val() == 'milestonelist') {
                showMilestoneList();
            } else {
                ManageMilestoneList();
            }
        }, 'json');
    }
    return false;
}

function milestoneArchive(obj, uniqid, title) {
    if (obj) {
        var uniqid = $(obj).attr("data-uid");
        var title = decodeURIComponent($(obj).attr("data-name"));
    }
    if (confirm(_("Are you sure you want to complete the milestone") + " '" + title + "' ?")) {
        var loc = HTTP_ROOT + "milestones/milestone_archive/";
        $.post(loc, {
            'uniqid': uniqid
        }, function (res) {
            if (res.error) {
                showTopErrSucc('error', res.msg);
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    ManageMilestoneList(1);
                }
            } else if (res.success) {
                showTopErrSucc('success', res.msg);
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    showMilestoneList('', 1);
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    ManageMilestoneList(1);
                }
            }
        }, 'json');
        //window.location = loc;
    }
    refreshMilestone = 1;
    return false;
}

function milestoneRestore(obj, uniqid, title) {
    if (obj) {
        var uniqid = $(obj).attr("data-uid");
        var title = decodeURIComponent($(obj).attr("data-name"));
    }

    if (confirm(_("Are you sure you want to restore the milestone") + " '" + title + "' ?")) {
        var loc = HTTP_ROOT + "milestones/milestone_restore/";
        $.post(loc, {
            'uniqid': uniqid
        }, function (res) {
            if (res.error) {
                showTopErrSucc('error', res.msg);
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    ManageMilestoneList(0);
                }
            } else if (res.success) {
                showTopErrSucc('success', res.msg);
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    showMilestoneList('', 0);
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList();
                } else {
                    ManageMilestoneList(0);
                }

            }
        }, 'json');
        refreshMilestone = 1;
        //window.location = loc;
    } else {
        return false;
    }
}

function addTaskToMilestone(obj, mstid, projid, cnt) {
    $('.showhidebtn').addClass('btn_blue_inactive');
    $('.showhidebtn').attr('disabled', true);
    if (obj) {
        var mstid = $(obj).attr("data-id");
        var projid = $(obj).attr("data-prj-id");
    }
    openPopup();
    $(".mlstn_case").show();
    $('#inner_mlstn_case').html('');
    $('.add-mlstn-btn').hide();
    $('#tsksrch').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    /*  $(".popup_form").css({
     "margin-top": "6px"
     }); */
    $(".loader_dv").show();
    $('#tsk_name').val('');
    $("#mlstnpopup").hide();
    $("#addtsk").css({
        'cursor': 'default'
    });
    $("#addtskncont").css({
        'cursor': 'default'
    });
    $("#tsk_name").val('');
    $.post(HTTP_ROOT + "milestones/add_case", {
        'mstid': mstid,
        'projid': projid
    }, function (res) {
        if (res) {
            $(".loader_dv").hide();
            $('#inner_mlstn_case').show();
            $('.add-mlstn-btn').show();
            $('#tskloader').hide();
            $('#tsksrch').show();
            $('#inner_mlstn_case').html(res);
            $("#header_prj_ttl").html($("#cur_proj_name").val());
            $("#header_mlstn_ttl").html($("#addcsmlstn_titl").val());
        }
    });
}

function searchMilestoneCase() {
    var project_id = '';
    var milestone_id = '';
    var title = $('#tsk_name').val();
    title = title.trim();
    try {
        var project_id = $('#project_id').val();
        var milestone_id = $('#milestone_id').val();
    } catch (e) {
    }
    if (project_id && milestone_id) {
        $("#tskpopupload1").show();
        $("#mlstnpopup").hide();
        $("#addtsk").css({
            'cursor': 'default'
        });
        $("#addtskncont").css({
            'cursor': 'default'
        });
        $.post(HTTP_ROOT + "milestones/add_case", {
            "mstid": milestone_id,
            "projid": project_id,
            "title": title
        }, function (res) {
            if (res) {
                $('#inner_mlstn_case').html(res);
                $("#tskpopupload1").hide();
            }
        });
    }
}

function selectMilestones(arg, i, chkall) {
    $("#addtsk").css({
        'cursor': 'default'
    });
    $("#addtskncont").css({
        'cursor': 'default'
    });
    if (parseInt(arg)) {
        if ($('#' + chkall).is(":checked")) {
            $('.showhidebtn').removeClass('btn_blue_inactive');
            $('.showhidebtn').attr('disabled', false);
            $(".ad-mlstn").attr("checked", "checked");
            $('.rw-cls').css({
                'background-color': '#FFFFCC'
            });
            $("#addtsk").css({
                'cursor': 'pointer'
            });
            $("#addtskncont").css({
                'cursor': 'pointer'
            });
        } else {
            $('.showhidebtn').addClass('btn_blue_inactive');
            $('.showhidebtn').attr('disabled', true);
            $(".ad-mlstn").attr("checked", false);
            $('.rw-cls').css({
                'background-color': ''
            });
        }
    } else {
        var listing = "listings" + i;
        if ($('.ad-mlstn:checked').length == $('.ad-mlstn').length) {
            $("#" + chkall).attr("checked", "checked");
            $('#' + listing).css({
                'background-color': '#FFFFCC'
            });
            $("#addtsk").css({
                'cursor': 'pointer'
            });
            $("#addtskncont").css({
                'cursor': 'pointer'
            });
            $('.showhidebtn').removeClass('btn_blue_inactive');
            $('.showhidebtn').attr('disabled', false);
        } else {
            $("#" + chkall).attr("checked", false);
            if ($('#actionChk' + i).is(":checked")) {
                $('#' + listing).css({
                    'background-color': '#FFFFCC'
                });
                $("#addtsk").css({
                    'cursor': 'pointer'
                });
                $("#addtskncont").css({
                    'cursor': 'pointer'
                });
            } else {
                $('#' + listing).css({
                    'background-color': ''
                });
            }
        }
        if (!$('.ad-mlstn:checked').length) {
            $('.showhidebtn').addClass('btn_blue_inactive');
            $('.showhidebtn').attr('disabled', true);
        } else {
            $('.showhidebtn').removeClass('btn_blue_inactive');
            $('.showhidebtn').attr('disabled', false);
        }
    }
}

function switchMilestone(obj, milestone_id, project_id) {
    if (project_id && milestone_id) {
        $('#milestone_id').val(milestone_id);
        $('#header_mlstn_ttl').html($(obj).text());
        $("#mlstnpopup").hide();
        $("#tskpopupload1").show();
        $("#addtsk").css({
            'cursor': 'default'
        });
        $("#addtskncont").css({
            'cursor': 'default'
        });
        $("#tsk_name").val('');
        $.post(HTTP_ROOT + "milestones/add_case", {
            "mstid": milestone_id,
            "projid": project_id
        }, function (res) {
            if (res) {
                $('#inner_mlstn_case').html(res);
                $("#tskpopupload1").hide();
            }
        });
    }
}

function assignCaseToMilestone(el) {
    $("#mlstnpopup").hide();
    var caseid = Array();
    var done = 0;
    $('#inner_mlstn_case input:checked').each(function () {
        if ($(this).attr('id') !== 'checkAll') {
            caseid.push($(this).attr('value'));
            done++;
        }
    });

    if (done) {
        var project_id = $('#project_id').val();
        var milestone_id = $('#milestone_id').val();
        $("#confirmbtntsk").hide();
        $('#tskloader').show();
        $.post(HTTP_ROOT + 'milestones/assign_case', {
            "caseid": caseid,
            "project_id": project_id,
            "milestone_id": milestone_id
        }, function (data) {
            if (data == "success") {
                var total_tasks = parseInt(caseid.length) + parseInt($("#tot_tasks" + milestone_id).text());
                $("#tot_tasks" + milestone_id).html(total_tasks);
                if (el && el.id == "addtskncont") {
                    $("#addtsk").css({
                        'cursor': 'default'
                    });
                    $("#addtskncont").css({
                        'cursor': 'default'
                    });
                    $("#tsk_name").val('');
                    $('.showhidebtn').addClass('btn_blue_inactive');
                    $('.showhidebtn').attr('disabled', true);
                    $.post(HTTP_ROOT + 'milestones/add_case', {
                        "mstid": milestone_id,
                        "projid": project_id,
                    }, function (data) {
                        if (data) {
                            $('#inner_mlstn_case').html(data);
                        }
                    });
                } else {
                    closePopup();
                }
                $('#tskloader').hide();
                $("#confirmbtntsk").show();
                showTopErrSucc('success', _('Task added successfully.'));
                if ($('#caseMenuFilters').val() == 'milestonelist') {
                    showMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'milestone') {
                    ManageMilestoneList();
                } else if ($('#caseMenuFilters').val() == 'kanban') {
                    easycase.showKanbanTaskList();
                }
                /*if(PAGE_NAME=='milestonelist'){
                 window.location.reload();
                 }*/
            }
        });
    } else {
        showTopErrSucc('error', _('Choose task to add in milestone.'));
    }
}

function view_project_milestone() {
    if ($('#mlstnpopup').is(':visible')) {
        $("#mlstnpopup").hide();
    } else {
        $("#mlstnpopup").show();
    }
    var project_id = $('#project_id').val();
    /*if ($('#ajaxViewMilestonesCP').html()) {
     $('#loader_mlsmenu').hide();
     } else {*/
    $('#ajaxViewMilestones').html('');
    $('#loader_mlsmenu').show();
    $.post(HTTP_ROOT + "milestones/ajax_milestone_menu", {
        "project_id": project_id
    }, function (data) {
        if (data) {
            $('#ajaxViewMilestonesCP').html(data);
            $('#loader_mlsmenu').hide();
        }
    });
//}
}

function caseMilestone(pjid, pname, page) {
    if (typeof (page) == 'undefined') {
        page = 1;
    }
    $('#pname_dashboard').html(decodeURIComponent(pname));
    $('#prjid').val(pjid);

    $('#projpopup').hide();
    $("#find_prj_dv").hide();
    $('#prj_drpdwn').removeClass("open");
    $(".dropdown-menu.lft").hide();
    var mlsttype = $('#mlsttype').val();
    if (pjid) {
        $("#moreloader").show();
        $.post(HTTP_ROOT + "milestones/milestone", {
            "project_id": pjid,
            'page': page,
            'mlsttype': mlsttype
        }, function (data) {
            if (data) {
                $("#moreloader").hide();
                $("#mlstnlistingDv").html(data);
                $(".proj_mng_div .contain").hover(function () {
                    $(this).find(".proj_mng").stop(true, true).animate({
                        bottom: "0px",
                        opacity: 1
                    }, 400);
                }, function () {
                    $(this).find(".proj_mng").stop(true, true).animate({
                        bottom: "-42px",
                        opacity: 0
                    }, 400);
                });
            }
        });
    }
}
//Milestone ends
/* Code for Milestone Kanban View starts */
showMilestoneList = function (mlstpaginate, isActive, pointer, search_key) {
    $('#storeIsActive').val(isActive);
    $('#view_type').val('kanban');
    if (!search_key) {
        search_key = $('#search_text').val();
    }
    if (isActive == 0) {
        $('#mlstab_cmpl_kanban').addClass('active')
        $('#mlstab_act_kanban').removeClass('active')
    } else {
        $('#mlstab_cmpl_kanban').removeClass('active')
        $('#mlstab_act_kanban').addClass('active')
    }
    //var morecontent ='';var newTask_limit=0;var inProgressTask_limit=0;var resolvedTask_limit=0;var closedTask_limit=0;
    $('#mlview_btn,mkbview_btn').tipsy({
        gravity: 'n',
        fade: true
    });
    $(".side-nav li").removeClass('active');
    $(".menu-milestone").addClass('active');
    var ispaginate = '';
    if (typeof (mlstpaginate) != 'undefined' && mlstpaginate) {
        ispaginate = mlstpaginate;
    }
    $("#brdcrmb-cse-hdr").html(_('Milestones'));
    $('#caseLoader').show();
    $('#select_view_mlst div').removeClass('disable');
    $('#mkbview_btn').addClass('disable');
    var projFil = $('#projFil').val();
    var projIsChange = $('#projIsChange').val();

    var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
    var priFil = $('#priFil').val(); // Filter by Priority
    var caseTypes = $('#caseTypes').val(); // Filter by case Types
    var caseMember = $('#caseMember').val();  // Filter by Member
    var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
    var caseSearch = $("#case_search").val();
    var case_date = $('#caseDateFil').val(); // Search by Date
    var case_due_date = $('#casedueDateFil').val(); // Search by Date
    var case_srch = $('#case_srch').val();
    //displayMenuProjects('dashboard', '6', 'files');
    //var caseId = document.getElementById('caseId').value; // Close a case

    var mlimit = ((isActive == 0 || isActive == 1) && pointer != 1) ? 0 : $('#milestoneLimit').val();
    var mURL = HTTP_ROOT + "milestones/ajax_milestonelist";
    easycase.routerHideShow('milestonelist');
    $("#caseMenuFilters").val('milestonelist');
    $('#milestoneUid').val('');
    $('#milestoneId').val('');
    $.post(mURL, {
        "projFil": projFil,
        "projIsChange": projIsChange,
        'caseStatus': caseStatus,
        'caseTypes': caseTypes,
        'priFil': priFil,
        'caseMember': caseMember,
        'caseAssignTo': caseAssignTo,
        'caseSearch': caseSearch,
        'case_srch': case_srch,
        'case_date': case_date,
        'case_due_date': case_due_date,
        'mlimit': mlimit,
        "caseMenuFilters": 'milestonelist',
        'ispaginate': ispaginate,
        'isActive': isActive,
        'file_srch': search_key
    }, function (res) {
        //	   alert(JSON.stringify(res));
        if (res) {
            res.isActive = isActive;
            refreshMilestone = 0;
            $('[rel=tooltip]').tipsy({
                gravity: 's',
                fade: true
            });
            $('#caseLoader').hide();
            if (!res.error) {
                if (res.totalMlstCnt > 3) {
                    $('.milestonenextprev').show();
                    if (res.mlimit <= 3) {
                        $('.milestonenextprev .prev').addClass('disable');
                        $('.milestonenextprev .prev').attr('disabled', 'disabled');
                    } else {
                        $('.milestonenextprev .prev').removeClass('disable');
                        $('.milestonenextprev .prev').removeAttr('disabled');
                    }
                    if (res.mlimit >= res.totalMlstCnt) {
                        $('.milestonenextprev .next').addClass('disable');
                        $('.milestonenextprev .next').attr('disabled', 'disabled');
                    } else {
                        $('.milestonenextprev .next').removeClass('disable');
                        $('.milestonenextprev .next').removeAttr('disabled');
                    }
                } else {
                    $('.milestonenextprev').hide();
                }
                $('#milestoneLimit').val(res.mlimit);
                $('#totalMlstCnt').val(res.totalMlstCnt);
            } else {
                $('.milestonenextprev').hide();
                $('#milestoneLimit').val('0');
                $('#totalMlstCnt').val('0');
            }

            var result = document.getElementById('show_milestonelist');
            //			alert(JSON.stringify(res));
            result.innerHTML = tmpl("milestonelist_tmpl", res);
            //                       alert('here');
            //scrollPageTop($("#kanban_list"));

            var settings = {
                autoReinitialise: true
            };
            var pane = $(".custom_scroll");
            pane.jScrollPane(settings);
            $(".kanban-child .kbtask_div").live("hover", function (obj) {
                var curindex = $(this).parent().children().index(this);
                if (($(this).is(":last-child") || $(this).is(":nth-last-child(3)") || $(this).is(":nth-last-child(2)")) && (parseInt(curindex) > 1) && ($(this).parents(".jspPane").height() > 400)) {
                    $(this).find('.dropdown').on('click', function (cobj) {
                        var hite = $(this).find('.dropdown-menu').height();
                        var popup_ht = parseInt(hite) + 12;
                        $(this).find(".dropdown-menu").css({
                            top: "-" + popup_ht + "px"
                        });
                        $(this).find(".pop_arrow_new").css({
                            marginTop: hite + "px",
                            background: "url('" + HTTP_ROOT + "img/arrow_dwn.png') no-repeat"
                        });
                    });
                }
            });


            // drag and drop of tasks from one milestone to another milestone //
            $('.kbtask_div').draggable({
                cursor: "grabbing",
                helper: 'clone',
                // containment: '.kanban-main',
                // scroll: false,
                revert: "invalid",
                zIndex: 100,
                start: function (event, ui) {
                    $(ui.helper).css({
                        'width': '22.5%'
                    });
                }

            });
            $(".kanban-child").droppable({
                accept: ".kbtask_div",
                drop: function (event, ui) {
                    $("#show_milestonelist").stop(true, false);
                    var project_id = projFil; //$('.prjhid').val();
                    var task_uniq_id = $(ui.helper).find('div[id^="titlehtml"]').attr('data-task');
                    var ext_mlst_id = $(ui.helper).parent().parent().attr('id').split('_');
                    ext_mlst_id = ext_mlst_id[1];
                    var curr_mlst_id = $(event.target).attr('id').split('_');
                    curr_mlst_id = curr_mlst_id[1];
                    // console.log(ext_mlst_id);console.log(curr_mlst_id);return false;
                    if (projFil == 'all') {
                        showTopErrSucc('error', "Please select a Project");
                        $(ui.helper).css({
                            'top': '0px',
                            'left': '0px'
                        });
                    }
                    if (curr_mlst_id.trim() == '') {
                        showTopErrSucc('error', "Please select a Milestone");
                    } else if (ext_mlst_id == curr_mlst_id) {
                        $(ui.helper).css({
                            'top': '0px',
                            'left': '0px'
                        });
                    } else {
                        $(".kanban-child > .kanban_content").append($(ui.helper));
                        $(ui.helper).css({
                            'width': '25%'
                        });						//alert(task_uniq_id+'--'+curr_mlst_id+'---'+project_id+'=='+ext_mlst_id);return;
                        $.post(HTTP_ROOT + 'milestones/switchTaskToMilestone', {
                            'taskid': '0',
                            'taskuid': task_uniq_id,
                            'curr_mlst_id': curr_mlst_id,
                            'project_id': project_id,
                            'ext_mlst_id': ext_mlst_id
                        }, function (res) {
                            if (res == 'success') {
                                showTopErrSucc('success', 'Task moved successfully.');
                                if ($('#mlstab_cmpl_kanban').hasClass('active')) {
                                    showMilestoneList('', 0);
                                } else {
                                    showMilestoneList('', 1);
                                }
                            } else {
                                showTopErrSucc('error', 'Error in moving task to task group');
                            }
                        });
                    }
                }
            });
            // End of drag and drop functionality

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
                        var text = '';
                        $.post(HTTP_ROOT + "easycases/ajax_change_DueDate", {
                            "caseId": caseId,
                            "duedt": dateText,
                            "text": text
                        }, function (data) {
                            if (data) {
                                $("#" + datelod).hide();
                                $("#" + showUpdDueDate).html(data.top + '<span class="due_dt_icn"></span>');
                            }
                        }, 'json');
                    }
                });
            });
            var clearCaseSearch = $('#clearCaseSearch').val();
            $('#clearCaseSearch').val("");
            //resetBreadcrumbFilters(HTTP_ROOT+'easycases/',caseStatus,priFil,caseTypes,caseMember,caseAssignTo,0,case_date,case_due_date,casePage,caseSearch,clearCaseSearch,'kanban','');

            $('[rel=tooltip]').tipsy({
                gravity: 's',
                fade: true
            });
        }
    });
    if (projFil == 'all') {
        remember_filters('ALL_PROJECT', 'all');
    } else {
        remember_filters('ALL_PROJECT', '');
    }
    $('#manage_milestonelist').css('display', 'block');
//$('#filter_section').show();
}
/* Code for Milestone Kanban View starts */
ManageMilestoneList = function (mlsttype, search_key) {
    $('#view_type').val('grid');
    $('#mlview_btn,mkbview_btn').tipsy({
        gravity: 'n',
        fade: true
    });
    if (!search_key) {
        search_key = $('#search_text').val();
    }
    $(".side-nav li").removeClass('active');
    $(".menu-milestone").addClass('active');
    if (typeof (mlsttype) == 'undefined') {
        mlsttype = $('#mlsttabvalue').val();
    } else {
        $('#mlsttabvalue').val(mlsttype);
        $('#mlstPage').val(1);
    }
    $('#mlsttab li').removeClass('active');
    if (!parseInt(mlsttype)) {
        $('#mlstab_cmpl').addClass('active');
        $('#storeIsActivegrid').val('0');
    } else {
        $('#mlstab_act').addClass('active');
        $('#storeIsActivegrid').val(1);
    }
    $("#brdcrmb-cse-hdr").html(_('Milestones'));
    $('#caseLoader').show();
    $('#select_view_mlst div').removeClass('disable');
    $('#mlview_btn').addClass('disable');
    var projFil = $('#projFil').val();
    var projIsChange = $('#projIsChange').val();

    var mPage = $('#mlstPage').val();
    var mURL = HTTP_ROOT + "milestones/manage_milestone";
    easycase.routerHideShow('milestone');
    $("#caseMenuFilters").val('milestone');
    $('#milestoneUid').val('');
    $('#milestoneId').val('');
    $.post(mURL, {
        "projFil": projFil,
        "projIsChange": projIsChange,
        'page': mPage,
        "caseMenuFilters": 'milestone',
        'mlsttype': mlsttype,
        "file_srch": search_key
    }, function (res) {
        if (res) {
            refreshManageMilestone = 0;
            $('#caseLoader').hide();
            var result = document.getElementById('manage_milestone_list');
            result.innerHTML = tmpl("manage_milestone_tmpl", res);
            $(".proj_mng_div .contain").hover(function () {
                $(this).find(".proj_mng").stop(true, true).animate({
                    bottom: "0px",
                    opacity: 1
                }, 400);
            }, function () {
                $(this).find(".proj_mng").stop(true, true).animate({
                    bottom: "-42px",
                    opacity: 0
                }, 400);
            });
            $('#clearCaseSearch').val("");
            //resetBreadcrumbFilters(HTTP_ROOT+'easycases/',caseStatus,priFil,caseTypes,caseMember,caseAssignTo,0,case_date,case_due_date,casePage,caseSearch,clearCaseSearch,'kanban','');
            $('[rel=tooltip]').tipsy({
                gravity: 's',
                fade: true
            });
        }
    });
    if (projFil == 'all') {
        remember_filters('ALL_PROJECT', 'all');
    } else {
        remember_filters('ALL_PROJECT', '');
    }
}
function trackclick(msg) {
    if (SITENAME == 'Orangescrum.com') {
        console.log("Google Track Event: " + msg);
        _gaq.push(['_trackEvent', 'Help and Support', msg, msg]);
    }
}

//Dashboard page Starts
$(function () {
    //$( ".sortable" ).disableSelection();
    $(".sortable").sortable({
        connectWith: ".sortable",
        handle: ".portlet-header",
        stop: function () {
            var order = $(".sortable").sortable("serialize");
            $.post(HTTP_ROOT + "easycases/ajax_save_dashboard_order", {
                "order": order
            }, function (res) {
                if (res) {

                }
            });
        }
    });
});

function CaseDashboard(pjid, pname) {
    $('#curr_active_project').val(pjid);
    $('#CS_project_id').val(pjid);
    $('#projFil').val(pjid);
    $('#projUpdateTop').html(decodeURIComponent(pname));
    //PRB

    $('#pname_dashboard').html(shortLength(decodeURIComponent(pname), 20));
    $('#projpopup').hide();
    $("#find_prj_dv").hide();
    $('#prj_drpdwn').removeClass("open");
    $(".dropdown-menu.lft").hide();
    createCookie('timelogsort', '', -365, '');
    createCookie('logstrtdt', '', -365, '');
    createCookie('logenddt', '', -365, '');
    createCookie('rsrclog', '', -365, '');
    loadDashboardPage(pjid);
    footer_update(pjid);
}

function loadDashboardPage(projid) {
    //var order = $("#seq_order").attr("data-order");
    //var sequency = order.split(",");

    if (projid == 'all') {
        remember_filters('ALL_PROJECT', 'all');
    } else {
        remember_filters('ALL_PROJECT', '');
    }

    var orderStr = Array();
    if (getCookie('DASHBOARD_ORDER') && $.inArray('7', getCookie('DASHBOARD_ORDER').split('::')[1].split(',')) === -1) {
        var orderCookie = getCookie('DASHBOARD_ORDER').split('::')[1].split(',');
        for (var i in orderCookie) {
            if (DASHBOARD_ORDER[orderCookie[i]]) {
                orderStr.push(DASHBOARD_ORDER[orderCookie[i]].name.toLowerCase().replace(' ', '_'));
            }
        }
    } else {
        for (var i in DASHBOARD_ORDER) {
            orderStr.push(DASHBOARD_ORDER[i].name.toLowerCase().replace(' ', '_'));
        }
    }

    var sequency = orderStr;

    for (var i in sequency) {
        if ($("#" + sequency[i]).html() !== '') {
            $("#" + sequency[i]).html('');
        }
    }

    //Hide recent projects section when a project is switched
    (projid == 'all') ? $('#list_2').show() : $('#list_2').hide();

    $(".loader_dv_db").show();
    $(".moredb").hide();
    $('[rel=tooltip]').tipsy({
        gravity: 's',
        fade: true
    });

    var dncRecentProj = 0;
    if (projid != 'all') {
        dncRecentProj = 1;
    }
    sequency.reverse();
    loadSeqDashboardAjax(sequency, projid);
}

function showTaskStatus(obj, projid) {
    if ($("#task_type").html() !== '') {
        $("#task_type").html('');
        $("#task_type_msg").html('');
    }
    $("#task_type_ldr").show();
    var url = HTTP_ROOT + "easycases/task_type";
    var task_type_id = $(obj).val();
    createCookie("TASK_TYPE_IN_DASHBOARD", task_type_id, 365, DOMAIN_COOKIE);

    $.post(url, {"projid": projid, "task_type_id": task_type_id}, function (res) {
        if (res) {
            cmnDashboard("task_type", res);
        }
    });
}

function loadSeqDashboardAjax(sequency, projid) {
    //Remove recent_projects from array to prevent ajax call when a project is switched
    (sequency[sequency.length - 1] == 'recent_projects' && projid !== 'all') ? sequency.pop() : '';
    var url = HTTP_ROOT + "easycases/";
    var action = sequency[sequency.length - 1];
    var task_type_id = 0;
    if (sequency[sequency.length - 1] === 'task_type') {
        task_type_id = $("#sel_task_type").val();
    }
    if (action == 'time_log') {
        url = HTTP_ROOT;
        action = 'timelog_graph';
    }
    $.post(url + action, {
        "projid": projid,
        "task_type_id": task_type_id
    }, function (res) {
        if (res) {
            cmnDashboard(action, res);
            if (projid == 'all') {
                $('#list_9,#list_8').hide();
            } else {
                $('#list_9,#list_8').show();
            }
            sequency.pop();
            if (sequency.length >= 1) {
                loadSeqDashboardAjax(sequency, projid);
            }
        }
    });
}

function cmnDashboard(id, res) {
    if (id == 'task_type') {
        iniChartTskProgress(id, res);
    } else if (id == 'task_status') {
        iniChartTskProgress(id, res);
    } else {
        $("#" + id + "_ldr").hide();
        $("#" + id).html(res);
        if ($("#" + id + "_more").length > 0 && $("#" + id + "_more").attr("data-value") && ($("#" + id + "_more").attr("data-value") > 10)) {
            $("#more_" + id).show();
            $("#more_" + id + ' span#todos_cnt').html('(' + $("#" + id + "_more").attr("data-value") + ')').show();
        }
        $('.custom_scroll').jScrollPane({
            autoReinitialise: true
        });
    }
}
function iniChartTskProgress(id, res) {
    $("#" + id + "_ldr").hide();
    $("#" + id + '_msg').html(_(res.sts_msg)).attr('title', res.sts_msg_ttl);
    $('.custom_scroll').jScrollPane({
        autoReinitialise: true
    });

    if ($("#" + id).highcharts()) {
        $("#" + id).highcharts().destroy();
    }

    if (!res.task_prog) {
        if (id == 'task_type') {
            $("#" + id).html('<div class="mytask"></div><div class="mytask_txt">' + _('No Task Type') + '</div>');
        } else {
            $("#" + id).html('<div class="mytask"></div><div class="mytask_txt">' + _('No Tasks') + '</div>');
        }
        return;
    }

    var HighChartVars = {
        startAngle: -90,
        endAngle: 90,
        center: ['50%', '85%'],
        size: '130%',
        innerSize: '60%'
    }
    if (id == 'task_status') {
        HighChartVars = {
            startAngle: 0,
            endAngle: 0,
            center: ['50%', '47%'],
            size: '110%',
            innerSize: '50%'
        }
    }
    Highcharts.setOptions({
        lang: {
            contextButtonTitle: _('Download')
        }
    });
    $("#" + id).highcharts({
        credits: {
            enabled: false
        },
        exporting: {
            buttons: {
                contextButton: {
                    symbolStrokeWidth: 2,
                    symbolStroke: '#969696',
                    menuItems: [{
                            text: _('PNG'),
                            onclick: function () {
                                this.exportChart();
                            },
                            separator: false
                        }, {
                            text: _('JPEG'),
                            onclick: function () {
                                this.exportChart({
                                    type: 'image/jpeg'
                                });
                            },
                            separator: false
                        }, {
                            text: _('PDF'),
                            onclick: function () {
                                this.exportChart({
                                    type: 'application/pdf'
                                });
                            },
                            separator: false
                        }, {
                            text: _('Print'),
                            onclick: function () {
                                this.print();
                            },
                            separator: false
                        }]
                }
            },
            filename: id
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
            height: 270
        },
        title: {
            text: '',
            align: 'center',
            verticalAlign: 'middle',
            y: 50
        },
        tooltip: {
            formatter: function () {
                var precsson = 3;
                if (this.point.percentage < 1)
                    precsson = 2;
                if (this.point.percentage >= 10)
                    precsson = 4;
                return '<b>' + this.point.name + '</b>: ' + parseFloat((this.point.percentage).toPrecision(precsson)) + ' %';
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -30,
                    color: 'white',
                    formatter: function () {
                        var precsson = 3;
                        if (this.point.percentage < 1)
                            precsson = 2;
                        if (this.point.percentage >= 10)
                            precsson = 4;
                        return this.point.percentage > 1 ? parseFloat((this.point.percentage).toPrecision(precsson)) + '%' : null;
                    }
                },
                startAngle: HighChartVars.startAngle,
                endAngle: HighChartVars.endAngle,
                center: HighChartVars.center,
                showInLegend: true,
                size: HighChartVars.size
            }
        },
        series: [{
                type: 'pie',
                name: ' ',
                innerSize: HighChartVars.innerSize,
                data: res.task_prog
            }]
    });
}
function showTasks(arg) {
    //Reset cookies
    remember_filters("reset", "all");

    //Set cookies
    var action = 'tasks';
    if (arg == 'my') {
        createCookie("CURRENT_FILTER", 'assigntome', 365, DOMAIN_COOKIE);
        createCookie("STATUS", '2-1', 365, DOMAIN_COOKIE);//New and In progress
    } else if (arg == 'all') {
        createCookie("CURRENT_FILTER", 'cases', 365, DOMAIN_COOKIE);
    } else if (arg == 'activities') {
        action = 'activities';
    }
    //createCookie("ALL_PROJECT", 'all', 365, DOMAIN_COOKIE);
    window.location = HTTP_ROOT + 'dashboard#' + action;
}
//Dashboard page Ends

function remember_filters(name, value) {
    if (name == 'reset') {
        createCookie('STATUS', '', -1, DOMAIN_COOKIE);
        createCookie('PRIORITY', '', -1, DOMAIN_COOKIE);
        createCookie('CS_TYPES', '', -1, DOMAIN_COOKIE);
        createCookie('MEMBERS', '', -1, DOMAIN_COOKIE);
        createCookie('ASSIGNTO', '', -1, DOMAIN_COOKIE);
        createCookie('DATE', '', -1, DOMAIN_COOKIE);
        createCookie('DUE_DATE', '', -1, DOMAIN_COOKIE);
        createCookie('MILESTONES', '', -1, DOMAIN_COOKIE);
        if (value == 'all') {
            createCookie('IS_SORT', '', -1, DOMAIN_COOKIE);
            createCookie('ORD_DATE', '', -1, DOMAIN_COOKIE);
            createCookie('ORD_TITLE', '', -1, DOMAIN_COOKIE);
            createCookie('CASESRCH', '', -1, DOMAIN_COOKIE);
            createCookie('SEARCH', '', -1, DOMAIN_COOKIE);
        }
    } else if (value) {
        createCookie(name, value, 30, DOMAIN_COOKIE);
    } else {
        createCookie(name, value, -1, DOMAIN_COOKIE);
    }
}
/* Assigned Project */
function deleteAssignedProject(id, userId, name, isInvite) {
    if (id) {
        var conf = confirm(_("Are you sure you want to delete assigned project") + " '" + decodeURIComponent(name.replace(/\+/g, ' ')) + "' ?");
        if (conf == true) {
            var strURL = document.getElementById('pageurl').value;
            strURL = strURL + "users/";
            $.post(strURL + "ajax_assignedproject_delete", {
                'id': id,
                'userId': userId,
                'isInvite': isInvite
            }, function (data) {
                if (data) {
                    if (data == 'success') {
                        $("#extlisting_" + id).fadeOut('slow');
                    } else {
                        return false;
                    }
                }
            });
        } else {
            return false;
        }
    }
}
function removeProjectName(pid, id, chkAll, chkOne, row, active_class) {
    if ($("#prjloader").is(':visible')) {
        return false;
    } else {
        removeArrayElement(hdprojectid, pid);
        $("#" + id).prop("checked", false);
        $(chkAll).prop('checked', false);
        $(chkOne).parents(row).removeClass(active_class);
        $("#" + pid).remove();
        if ($(".chkbx_cur:checked").length == 0) {
            hdprojectid = new Array();
            hdproject_name = '';
        }
    }

}
/* Existing Users in a particular project */
function deleteUsersInProject(userId, projectId, name) {
    if (userId) {
        var conf = confirm(_("Are you sure you want to delete the user") + " '" + decodeURIComponent(name.replace(/\+/g, ' ')) + "' from this project?");
        if (conf == true) {
            var strURL = document.getElementById('pageurl').value;
            var dcrs_cnt = 1;
            strURL = strURL + "projects/ajax_existuser_delete";
            $.post(strURL, {
                'userid': userId,
                'project_id': projectId
            }, function (data) {
                if (data) {
                    if (data == 'success') {
                        showTopErrSucc('success', _('User deleted successfully.'));
                        $("#extlisting" + userId).fadeOut('slow');
                        if (parseInt(dcrs_cnt)) {
                            var total_user = parseInt($("#tot_prjusers" + projectId).text()) - 1;
                            $("#tot_prjusers" + projectId).html(total_user);
                            if (parseInt(total_user) == 0) {
                                $("#remove" + projectId).hide();
                                $("#ajax_remove" + projectId).hide();
                                closePopup();
                            }
                        }
                    } else {
                        return false;
                    }
                }
            });
        } else {
            return false;
        }
    }
}
function removeUserName(uid, id) {
    if ($("#userloader").is(':visible')) {
        return false;
    } else {
        removeArrayElement(hduserid, uid);
        $("#" + id).attr("checked", false);
        $("#checkAll").attr("checked", false);
        $('.rw-cls').css({
            'background-color': ''
        });
        $("#" + uid).remove();
        if ($(".chkbx_cur:checked").length == 0) {
            hduserid = new Array();
        }
    }

}
function removeArrayElement(array, itemToRemove) {
    for (var i = 0; i < array.length; i++) {
        if (array[i] === itemToRemove) {
            array.splice(i, 1);
            break;
        }
    }
}
//Remove task from milestone
function removeTaskFromMilestone(obj) {
    if (obj) {
        var mstid = $(obj).attr("data-id");
        var projid = $(obj).attr("data-prj-id");
    }
    openPopup();
    $(".mlstn_remove_task").show();
    $('#inner_mlstn_removetask').html('');
    $('.add-mlstn-btn').hide();
    $('#tsksrch').hide();
    $(".popup_bg").css({
        "width": '850px'
    });
    $(".popup_form").css({
        "margin-top": "6px"
    });
    $("#rmv_case_loader").show();
    //$("#mlstnpopup").hide();
    $("#addtsk").css({
        'cursor': 'default'
    });
    $("#addtskncont").css({
        'cursor': 'default'
    });
    $("#tsk_name").val('');
    $.post(HTTP_ROOT + "milestones/removeCasesFromMilestone", {
        'mstid': mstid,
        'projid': projid
    }, function (res) {
        if (res) {
            $("#rmv_case_loader").hide();
            $('#inner_mlstn_removetask').show();
            $('.add-mlstn-btn').show();
            $('#tskloader').hide();
            $('#tsksrch').show();
            $('#inner_mlstn_removetask').html(res);
            $("#header_prj_ttl_rt").html($("#cur_proj_name_rt").val());
            $("#header_mlstn_ttl_rt").html($("#addcsmlstn_titl_rt").val());
        }
    });
}

//Remove task from milestone
function removecaseFromMilestone(obj) {
    if (confirm(_('Are you sure you want to remove selected tasks from Milestone?'))) {
        $("#mlstnpopup").hide();
        var caseid = Array();
        var done = 0;
        $('#inner_mlstn_removetask input:checked').each(function () {
            if ($(this).attr('id') !== 'checkAll') {
                caseid.push($(this).attr('value'));
                done++;
            }
        });
        if (done) {
            var project_id = $('#project_id_rt').val();
            var milestone_id = $('#milestone_id_rt').val();
            $("#confirmbtntsk").hide();
            $('#tskloader').show();
            $.post(HTTP_ROOT + 'milestones/remove_case', {
                "caseid": caseid,
                "project_id": project_id,
                "milestone_id": milestone_id
            }, function (data) {
                if (data == "success") {
                    var total_tasks = parseInt(caseid.length) + parseInt($("#tot_tasks_rt" + milestone_id).text());
                    $("#tot_tasks" + milestone_id).html(total_tasks);
                    /*if (el && el.id == "addtskncont") {
                     $("#addtsk").css({'cursor': 'default'});
                     $("#addtskncont").css({'cursor': 'default'});
                     $("#tsk_name").val('');
                     $.post(HTTP_ROOT + 'milestones/remove_case', {"mstid": milestone_id, "projid": project_id,}, function(data) {
                     if (data) {
                     $('#inner_mlstn_case').html(data);
                     }
                     });
                     }else {*/
                    closePopup();
                    //}
                    $('#tskloader').hide();
                    $("#confirmbtntsk").show();
                    showTopErrSucc('success', _('Task removed successfully.'));
                    if ($('#caseMenuFilters').val() == 'milestonelist') {
                        showMilestoneList();
                    } else if ($('#caseMenuFilters').val() == 'milestone') {
                        ManageMilestoneList();
                    } else if ($('#caseMenuFilters').val() == 'kanban') {
                        easycase.showKanbanTaskList();
                    }
                    /*if(PAGE_NAME=='milestonelist'){
                     window.location.reload();
                     }*/
                }
            });
        } else {
            showTopErrSucc('error', _('Choose task to remove from milestone.'));
        }
    }
}
// Milestone list appended in the Create Task
function milstoneonTask(mlstname, mlstid) {
    $.post(HTTP_ROOT + 'milestones/milestone_list', {
        'project_id': $('#curr_active_project').val()
    }, function (res) {
        if (res) {
            $('#more_opt8 ul li').remove();
            if (typeof mlstname == 'undefined') {
                $('#selected_milestone').html(_('No Milestone'));
                $('#CS_milestone').val('');
            } else {
                $('#selected_milestone').html(shortLength(ucfirst(formatText(mlstname)), 15));
                $('#CS_milestone').val(mlstid);
            }

            $('#more_opt8 ul').append('<li><a href="javascript:jsVoid()">&nbsp;&nbsp;No Milestone</a></li>');
            $.each(res, function (key, value) {
                $('#more_opt8 ul').append('<li><a href="javascript:jsVoid()" onclick="open_more_opt(\'more_opt8\');" ><span class="value">' + key + '</span>&nbsp;&nbsp;' + shortLength(ucfirst(formatText(value)), 15) + '</a></li>');
            });
            addTaskEvents();
        } else {
            $('#more_opt8 ul li').remove();
            $('#selected_milestone').html(_('No Milestone'));
            $('#CS_milestone').val('');
            $('#more_opt8 ul').append('<li><a href="javascript:jsVoid()" onclick="addEditMilestone(this,\'\',\'\',\'\',\'\',\'createTask\');" class="cnew_mlst"><span class="value"></span>&nbsp;+ ' + _('Create Milestone') + '</a></li>');
        }
    }, 'json');
}
function calendarView(type) {
    var filterV = $('#caseMenuFilters').val();
    if ((type == 'hash' && urlHash == 'calendar') || (type == 'calendar' && urlHash == 'calendar') || (filterV == 'calendar' && type == 'calendar')) {
        $('#calendar_view').hide();
        easycase.routerHideShow('calendar');
        var type = 'calendar';
        var params = parseUrlHash(urlHash);
        $('#select_view div').tipsy({
            gravity: 'n',
            fade: true
        });
        var globalkanbantimeout = null;
        var morecontent = '';
        if (type == 'calendar') {
            $('#select_view div').removeClass('disable');
            $('#calendar_btn').addClass('disable');
            $("#caseMenuFilters").val('calendar');
            $(".menu-files").removeClass('active');
            $(".menu-milestone").removeClass('active');
            milestone_uid = '';
        }
        var strURL = HTTP_ROOT + "easycases/";
        var casePage = $('#casePage').val(); // Pagination
        $('#caseLoader').show();
        var projFil = $('#projFil').val();
        var projIsChange = $('#projIsChange').val();
        var customfilter = $('#customFIlterId').value;//Change case type
        var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
        var priFil = $('#priFil').val(); // Filter by Priority
        var caseTypes = $('#caseTypes').val(); // Filter by case Types
        var caseMember = $('#caseMember').val();  // Filter by Member
        var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
        var caseSearch = $("#case_search").val();
        var case_date = $('#caseDateFil').val(); // Search by Date
        var case_due_date = $('#casedueDateFil').val(); // Search by Date
        var case_srch = $('#case_srch').val();
        //displayMenuProjects('dashboard', '6', 'files');
        //var caseId = document.getElementById('caseId').value; // Close a case
        var strURL = HTTP_ROOT + "easycases/";
        var tskURL = strURL + "calendarView";
        //easycase.routerHideShow('calendar');
        //$('#caseLoader').show();
        $.post(tskURL, {
            "projFil": projFil,
            "projIsChange": projIsChange,
            "casePage": casePage,
            'caseStatus': caseStatus,
            'customfilter': customfilter,
            'caseTypes': caseTypes,
            'priFil': priFil,
            'caseMember': caseMember,
            'caseAssignTo': caseAssignTo,
            'caseSearch': caseSearch,
            'case_srch': case_srch,
            'case_date': case_date,
            'case_due_date': case_due_date,
            'morecontent': '',
            'milestoneUid': milestone_uid
        }, function (res) {
            //$.post(tskURL,{},function(res){
            //$('#caseLoader').hide();
            $('#calendar_view').show();
            $('#calendar_view').html(res);
            loadCaseMenu(strURL + "ajax_case_menu", {
                "projUniq": projFil,
                "pageload": 0,
                "page": "dashboard"
            })
        });
        if (projFil == 'all') {
            remember_filters('ALL_PROJECT', 'all');
        } else {
            remember_filters('ALL_PROJECT', '');
        }
    }

}
function getCalenderForTimeLog(type) {
    /*if(TPAY == 1){
     window.location.hash = "calendar";
     if(type == 'calendar' && urlHash == ''){
     urlHash = getHash();
     }
     }else{*/
    var filterV = $('#caseMenuFilters').val();
    if ((type == 'hash' && urlHash == 'calendar') || (type == 'calendar' && urlHash == 'calendar') || (filterV == 'calendar' && type == 'calendar')) {
        $('#calendar_view').hide();
        calenderForTimeLog('calender');
        var pathToLook = "ajax_project_size"
        var type = 'calendar';
        var params = parseUrlHash(urlHash);
        $('#select_view div').tipsy({
            gravity: 'n',
            fade: true
        });
        var globalkanbantimeout = null;
        var morecontent = '';
        if (type == 'calendar') {
            $('#chart_view').hide();
            $('#timelogtbl').show();
            $('.filter-info').show();
            $('#select_view div').removeClass('disable');
            $('#calendar_btn').addClass('disable');
            $("#caseMenuFilters").val('calendar');
            $(".menu-files").removeClass('active');
            $(".menu-milestone").removeClass('active');
            $('.tlog-fltr-vtn').hide();
            milestone_uid = '';
        }
        if (TPAY == 1) {
            $('#calendar_view').hide();
            $('#payment_view').hide();
            $('#invce_view').hide();
            $('#tlg_fltr').hide();
            $('#tlg_exprt').show();
            $('#tlg_tab').hide();
            $('#hrs_details').show();
            $('#unpaid_hrs_time').hide();
        }
        var strURL = HTTP_ROOT + "easycases/";
        var casePage = $('#casePage').val(); // Pagination
        $('#caseLoader').show();
        var projFil = $('#projFil').val();
        var projIsChange = $('#projIsChange').val();
        var customfilter = $('#customFIlterId').value;//Change case type
        var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
        var priFil = $('#priFil').val(); // Filter by Priority
        var caseTypes = $('#caseTypes').val(); // Filter by case Types
        var caseMember = $('#caseMember').val();  // Filter by Member
        var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
        var caseSearch = $("#case_search").val();
        var case_date = $('#caseDateFil').val(); // Search by Date
        var case_due_date = $('#casedueDateFil').val(); // Search by Date
        var case_srch = $('#case_srch').val();
        //var caseId = document.getElementById('caseId').value; // Close a case
        var tskURL = strURL + "calendarTimeLog";
        $.post(tskURL, {
            "projFil": projFil,
            "projIsChange": projIsChange,
            "casePage": casePage,
            'caseStatus': caseStatus,
            'customfilter': customfilter,
            'caseTypes': caseTypes,
            'priFil': priFil,
            'caseMember': caseMember,
            'caseAssignTo': caseAssignTo,
            'caseSearch': caseSearch,
            'case_srch': case_srch,
            'case_date': case_date,
            'case_due_date': case_due_date,
            'morecontent': '',
            'milestoneUid': milestone_uid
        }, function (res) {
            $('#chart_view').hide();
            $('#calendar_view').show();
            $('#projFil').val(projFil);
            $('#caseLoader').hide();
            $('#calendar_view').html(res);
            if (TPAY == 1) {
                $('#tlg_fltr').hide();
                $('#tlg_exprt').hide();
                $('#tlg_tab').hide();
                $('#hrs_details').show();
                $('#unpaid_hrs_time').hide();
                $('#resrc-utl').hide();
                $('#unbilled_pg_limit').hide();
                $('#showUnbilledTime_paginate').hide();
            }
        });
        if (projFil == 'all') {
            $('#pname_dashboard').html(_("All"));
            remember_filters('ALL_PROJECT', 'all');
        } else {
            remember_filters('ALL_PROJECT', '');
        }
    }
    //}
}

function getChartForTimeLog(type, currentDate) {
    /*if(TPAY == 1){
     window.location.hash = "chart";
     if(type == 'chart' && urlHash == ''){
     urlHash = getHash();
     }
     }else{ */
    var filterV = $('#caseMenuFilters').val();
    if ((type == 'hash' && urlHash == 'chart') || (type == 'chart' && urlHash == 'chart') || (filterV == 'chart' && type == 'chart')) {
        $('#chart_view').hide();
        calenderForTimeLog('chart');
        var pathToLook = "ajax_project_size"
        var type = 'chart';
        var params = parseUrlHash(urlHash);
        $('#select_view div').tipsy({
            gravity: 'n',
            fade: true
        });
        var globalkanbantimeout = null;
        var morecontent = '';
        if (type == 'chart') {
            $('#calendar_view').hide();
            $('#timelogtbl').hide();
			$('#timelog_lstng_view').hide();
            $('.filter-info').hide();
            $('#select_view div').removeClass('disable');
            $('#chart_btn').addClass('disable');
            $("#caseMenuFilters").val('calendar');
            $(".menu-files").removeClass('active');
            $(".menu-milestone").removeClass('active');
            $('.tlog-fltr-vtn').hide();
            milestone_uid = '';
        }
        if (TPAY == 1) {
            $('#chart_view').hide();
            $('#payment_view').hide();
            $('#invce_view').hide();
            $('#tlg_fltr').hide();
            $('#tlg_exprt').show();
            $('#tlg_tab').hide();
            $('#hrs_details').show();
            $('#unpaid_hrs_time').hide();
        }
        var strURL = HTTP_ROOT + "Timelog/logTimes";
        var casePage = $('#casePage').val(); // Pagination
        $('#caseLoader').show();
        var projFil = $('#projFil').val();
        var projIsChange = $('#projIsChange').val();
        var customfilter = $('#customFIlterId').value;//Change case type
        var caseStatus = $('#caseStatus').val(); // Filter by Status(legend)
        var priFil = $('#priFil').val(); // Filter by Priority
        var caseTypes = $('#caseTypes').val(); // Filter by case Types
        var caseMember = $('#caseMember').val();  // Filter by Member
        var caseAssignTo = $('#caseAssignTo').val();  // Filter by AssignTo
        var caseSearch = $("#case_search").val();
        var case_date = $('#caseDateFil').val(); // Search by Date
        var case_due_date = $('#casedueDateFil').val(); // Search by Date
        var case_srch = $('#case_srch').val();
        var currentdate = (typeof currentDate != 'undefined') ? currentDate : "";
        var tskURL = strURL + "/showChartView";
        $.post(tskURL, {
            "projFil": projFil,
            "projIsChange": projIsChange,
            "casePage": casePage,
            'caseStatus': caseStatus,
            'customfilter': customfilter,
            'caseTypes': caseTypes,
            'priFil': priFil,
            'caseMember': caseMember,
            'caseAssignTo': caseAssignTo,
            'caseSearch': caseSearch,
            'case_srch': case_srch,
            'case_date': case_date,
            'case_due_date': case_due_date,
            'morecontent': '',
            'milestoneUid': milestone_uid,
            'currentdate': currentdate
        }, function (res) {
            $('#chart_view').show();
            $('#projFil').val(projFil);
            $('#caseLoader').hide();
            $('#chart_view').html(res);
            if (TPAY == 1) {
                $('#tlg_fltr').hide();
                $('#tlg_exprt').hide();
                $('#tlg_tab').hide();
                $('#hrs_details').show();
                $('#unpaid_hrs_time').hide();
                $('#resrc-utl').hide();
                $('#unbilled_pg_limit').hide();
                $('#showUnbilledTime_paginate').hide();
            }
        });
        if (projFil == 'all') {
            $('#pname_dashboard').html(_("All"));
            remember_filters('ALL_PROJECT', 'all');
        } else {
            remember_filters('ALL_PROJECT', '');
        }
    }
    //}
}

resetMilestoneSearch = function () {
    $('#search_text').val('');
    $('#show_search').html('');
    $('#resetting').html('');
    $('#milestone_content').css('margin-top', '');
    var view_type = $('#view_type').val();
    if (view_type == 'grid') {
        if ($('#storeIsActivegrid').val() == 0) {
            ManageMilestoneList();
        } else {
            ManageMilestoneList();
        }
    } else {
        if ($('#storeIsActive').val() == 0) {
            showMilestoneList(3, 0);
        } else {
            showMilestoneList(3, 1);
        }
    }
}
searchMilestoneTasks = function (srch) {
    var params = parseUrlHash(urlHash);
    if (!srch) {
        return false;
    }
    $('#kanban_list').css('margin-top', '30px');

    $('#show_search_kanban').html(_("Search Results for") + ":<span> " + srch + '</span>');
    $('#resetting_kanban').html(' &nbsp;<a href="javascript:void(0);" onclick="resetKanbanSearch();">' + _("Reset") + '</a>');
    easycase.showKanbanTaskList(params[0], srch);
    $('#srch_load1').hide();
}
resetKanbanSearch = function () {
    $('#show_search_kanban').html('');
    $('#resetting_kanban').html('');
    $('#kanban_list').css('margin-top', '');
    var params = parseUrlHash(urlHash);
    easycase.showKanbanTaskList(params[0]);
}
function quickEditMilestone(mid) {
    $('#edit-link_' + mid).hide();
    $('#edit-save_' + mid).show();
    $('#milstone_edit_' + mid).focus();
}
function saveMilesatoneTitle(mid) {
    var title = $('#milstone_edit_' + mid).val();
    if (title.trim() != '') {
        $('#milstone_edit_' + mid).css('border-color', '#66AFE9');
        $.post(HTTP_ROOT + "milestone/saveMilestoneTitle", {'mid': mid, 'title': title}, function (data) {
            if (title.length > 28) {
                title = title.substr(0, 28) + '...';
            }
            $('#main-title-holder_' + mid).find('a').text(title);
            $('#edit-link_' + mid).show();
            $('#edit-save_' + mid).hide();
        });
    } else {
        $('#milstone_edit_' + mid).attr('placeholder', 'Enter the title..');
        $('#milstone_edit_' + mid).css('border', '1px solid red');
        $('#milstone_edit_' + mid).focus();
    }
}

function addNewTaskType() {
    openPopup();
    $('#newtask_btn').text('Add');
    $(".new_tasktype").show();
    $(".loader_dv").hide();
    //setting default form field value

    $('#inner_tasktype').show();
    $("#task_type_nm").val('');
    $("#task_type_shnm").val('');
    $("#task_type_shnm").on("keyup", function () {
        $(this).val($(this).val().toLowerCase());
    });

    $("#task_type_nm").focus();
}

function validateTaskType() {
    var msg = "";
    var nm = $.trim($("#task_type_nm").val());
    var shnm = $.trim($("#task_type_shnm").val());
    var id = $.trim($("#new-typeid").val());
    $("#tterr_msg").html("");

    if (nm === "") {
        msg = _("'Name' cannot be left blank!");
        $("#tterr_msg").show().html(msg);
        $("#task_type_nm").focus();
        return false;
    } else {
        if (!nm.match(/^[A-Za-z0-9]/g)) {
            msg = _("'Name' must starts with an Alphabet or Number!");
            $("#tterr_msg").show().html(msg);
            $("#task_type_nm").focus();
            return false;
        }
    }

    if (shnm === "") {
        msg = _("'Short Name' cannot be left blank!");
        $("#tterr_msg").show().html(msg);
        $("#task_type_shnm").focus();
        return false;
    } else {
        var x = shnm.substr(-1);
        if (!isNaN(x)) {
            msg = _("'Short Name' cannot end with a number or space!");
            $("#tterr_msg").show().html(msg);
            $("#task_type_shnm").focus();
            return false;
        }
        $.post(HTTP_ROOT + "projects/validateTaskType", {'id': id, 'name': nm, 'sort_name': shnm}, function (data) {
            if (data.status == 'success') {
                $("#tterr_msg").hide();
                $("#ttbtn").hide();
                $("#ttloader").show();
                $('#customTaskTypeForm').submit();
            } else {
                $("#ttbtn").show();
                $("#ttloader").hide();
                if (data.msg == 'name') {
                    $("#tterr_msg").show().html(_('Name already esists!. Please enter another name.'));
                } else if (data.msg == 'sort_name') {
                    $("#tterr_msg").show().html(_('Short Name already esists!. Please enter another short name.'));
                } else {
                    $("#tterr_msg").show().html(_('Oops! Missing input parameters.'));
                }
                return false;
            }
        }, 'json');
    }
}

function saveTaskType() {
    var isTaskIds = 0;
    $(".all_tt").each(function () {
        if ($(this).is(":checked")) {
            isTaskIds = 1;
        }
    });

    if (parseInt(isTaskIds)) {
        $('.all_tt').attr('disabled', false);
        $("#tt_save_btn").hide();
        $("#loader_img_tt").show();
        $('#task_types').attr("action", HTTP_ROOT + "projects/saveTaskType");
        document.task_types.submit();
        return true;
    } else {
        showTopErrSucc('error', _('Check atleast one task type.'));
        return false;
    }
}

function deleteTaskType(obj) {
    var nm = $(obj).attr("data-name");
    var id = $(obj).attr("data-id");

    if (confirm(_("Are you sure you want to delete") + " '" + nm + "' " + _("task type") + " ?")) {
        $("#del_tsk_" + id).hide();
        $("#lding_tsk_" + id).show();
        $.post(HTTP_ROOT + "projects/deleteTaskType", {"id": id}, function (res) {
            if (parseInt(res)) {
                $("#dv_tsk_" + id).fadeOut(300, function () {
                    $(this).remove();
                    showTopErrSucc('success', _("Task type") + " '" + nm + "' " + _("has deleted successfully") + ".");
                });
            } else {
                $("#lding_tsk_" + id).hide();
                $("#del_tsk_" + id).show();
                showTopErrSucc('error', _('Error in deletion of task type.'));
            }
        });
    }
}
function editTaskType(obj) {
    var nm = $(obj).attr("data-name");
    var id = $(obj).attr("data-id");
    var srt_name = $(obj).attr("data-sortname");
    $('#newtask_btn').text(_('Update'));
    openPopup();
    $(".new_tasktype").show();
    $(".loader_dv").hide();
    $('#inner_tasktype').show();
    $("#task_type_nm").val(nm);
    $("#task_type_shnm").val(srt_name);
    $("#new-typeid").val(id);
    $("#task_type_shnm").on("keyup", function () {
        $(this).val($(this).val().toLowerCase());
    });
    $("#task_type_nm").focus();
}

function chk_client() {
    $('#chked_all').prop('checked', false);
    if ($('#make_client').prop('checked')) {
        var asn_to = $('[id^="CS_assign_to"]').val() != '' ? $('[id^="CS_assign_to"]').val() : '';
        var uarr = new Array();
        $('.chk_client').each(function () {
            var clnt_id = $(this).val();
            uarr.push(clnt_id);
            $('#more_opt5').find('ul li a').each(function () {
                if ($(this).attr('value') == clnt_id) {
                    $(this).addClass('clnt-asgn').hide();
                }
            });
        });
        if ($.inArray(asn_to, uarr) > -1) {
            $('#tsk_asgn_to').text(_('me'));
            $('#CS_assign_to').val(SES_ID);
            $('#more_opt5').find('input.fakeinput').val(_('me'));
        }
        $('.chk_client').prop('disabled', true);
        $('.chk_client').prop('checked', false);
    } else {
        $('.chk_client').prop('disabled', false);
        $('#more_opt5').find('ul li a.clnt-asgn').show();
    }
}

function chk_client_reply() {
    $('.checkedkAllRep').prop('checked', false);
    if ($('#make_client_dtl').prop('checked')) {
        var asn_to = $('[id^="CS_assign_to"]').val() != '' ? $('[id^="CS_assign_to"]').val() : '';
        var tsk_id = '';
        var uarr = new Array();
        $('.chk_client_reply').each(function () {
            var clnt_id = $(this).val();
            uarr.push(clnt_id);
            tsk_id = $(this).attr('data-tsk-id');
            /*$('.assign-to-fld').find('.dropdownjs').find('ul li').each(function(){
             if($(this).attr('value') == clnt_id){
             $(this).addClass('clnt-asgn').hide();
             }
             });*/
            $('.assign-to-fld-repl').each(function () {
                if ($(this).attr('value') == clnt_id) {
                    $(this).addClass('clnt-asgn').hide();
                }
            });
        });
        asn_to = $('#CS_assign_to' + tsk_id).val();
        if ($.inArray(asn_to, uarr) > -1) {
            $('#tsk_asgn_to').text(_('me'));
            $('#CS_assign_to').val(SES_ID);            //$('.assign-to-fld').find('.dropdownjs').find('input.fakeinput').val(_('me'));
            $('#CS_assign_to' + tsk_id).val(SES_ID);
        }
        $('.chk_client_reply').prop('disabled', true);
        $('.chk_client_reply').prop('checked', false);
    } else {
        $('.chk_client_reply').prop('disabled', false);
        $('.assign-to-fld').find('.dropdownjs').find('ul li.clnt-asgn').show();
        $('.chk_client_reply').each(function () {
            $('.clnt-asgn').show();
            var clnt_id = $(this).val();
            uarr.pop(clnt_id);
            $('.clnt-asgn').each(function () {
                $(this).removeClass('clnt-asgn');
            });
        });
    }
}



/*Author: GKM
 * it is used to show time formats
 * */
function format_time_hr_min(secs) {
    var hrs = Math.floor(secs / 3600) > 0 ? Math.floor(secs / 3600) + ' hr' + (Math.floor(secs / 3600) > 1 ? 's' : '') + ' ' : '';
    var mins = Math.round((secs % 3600) / 60) > 0 ? Math.round((secs % 3600) / 60) + ' min' + (Math.round((secs % 3600) / 60) > 1 ? 's' : '') + '' : '';
    return hrs != '' || mins != '' ? hrs + mins : '---';
}
/*author: GKM
 * it is used to format 24 hr to 12 hr with am / pm format
 */
function format_24hr_to_12hr(time) {
    var out_time = time.substr(0, (time.lastIndexOf(':')));
    var out_time_arr = time.split(':');
    var out_mode = parseInt(out_time_arr[0]) < 12 ? 'am' : 'pm';
    var out_hr = parseInt(out_time_arr[0]) > 12 ? parseInt(out_time_arr[0]) - 12 : parseInt(out_time_arr[0]);
    var out_min = parseInt(out_time_arr[1])
    return (out_hr > 0 ? out_hr : 12) + ':' + (out_min < 10 ? '0' : '') + out_min + '' + out_mode;
}
function formatDate(format, date) {
    return $.datepicker.formatDate(format, new Date(date));
    var monthNamesShort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    return date.toString();
}
function setOccurrenceEndDate(obj) {
    var occurrence = $(obj).val();
    $('#CSrepeat_occurrence').val(occurrence);
    var repeat_type = $('#CSrepeat_type').val();
    var due_date = $('#CS_due_date').val();
    if (due_date.indexOf(',') != -1) {
        var date = due_date.split(',');
        var year = new Date().getFullYear();
        due_date = new Date(date[0] + ' ' + year);
    }
    var daysToAdd = '';
    if (repeat_type == 'Weekly') {
        daysToAdd = 7 * occurrence;
    } else if (repeat_type == 'Monthly') {
        daysToAdd = 30 * occurrence;
    } else if (repeat_type == 'Quarterly') {
        daysToAdd = 90 * occurrence;
    } else if (repeat_type == 'Yearly') {
        daysToAdd = 365 * occurrence;
    }
    due_date = new Date(due_date);
    due_date.setDate(due_date.getDate() + daysToAdd);
    var dateFormated = due_date.toISOString().substr(0, 10);
    $('#CSrepeat_due_date').val(dateFormated);
}

function setNoOfOccurrence(obj) {
    var occurrenceEndDate = $(obj).val();
    var repeat_type = $('#CSrepeat_type').val();
    var due_date = $('#CS_due_date').val();
    if (due_date.indexOf(',') !== -1) {
        var date = due_date.split(',');
        var year = new Date().getFullYear();
        due_date = new Date(date[0] + ' ' + year);
    }
    var one_day = 1000 * 60 * 60 * 24;
    var occurrenceEndDate_ms = new Date(occurrenceEndDate).getTime();
    var due_date_ms = new Date(due_date).getTime();
    var difference_ms = occurrenceEndDate_ms - due_date_ms;
    var diff = Math.round(difference_ms / one_day);
    var occurrence = '';
    if (repeat_type == 'Weekly') {
        occurrence = Math.round(diff / 7);
    } else if (repeat_type == 'Monthly') {
        occurrence = Math.round(diff / 30);
    } else if (repeat_type == 'Quarterly') {
        occurrence = Math.round(diff / 90);
    } else if (repeat_type == 'Yearly') {
        occurrence = Math.round(diff / 365);
    }
    $('#CSrepeat_occurrence').val(occurrence);
}

function enableTextBox(type) {
    if (type == 'occur') {
        $('#occurrence').removeAttr('disabled');
        $('#end_datePicker').attr('disabled', 'disabled');
    } else if (type == 'date') {
        $('#end_datePicker').removeAttr('disabled');
        $('#occurrence').attr('disabled', 'disabled');
    }
}
function createPojectTemplate(id, obj, alltask) {
    var is_multiple = 0;
    var prjunid = $('#projFil').val();
    if (prjunid == 'all') {
        showTopErrSucc('error', _('Oops! You are in All project. Please choose a project.'));
        return false;
    }
    if (typeof alltask != 'undefined') {
        var chked = 0;
        $('input[id^="actionChk"]').each(function (i) {
            if ($(this).is(":checked") && !($(this).is(":disabled"))) {
                chked = 1;
            }
        });
        if (chked == 0) {
            showTopErrSucc('error', _("Please check atleast one task to create a template"));
            return false;
        }
        var project_id = $('#curr_sel_project_id').val();
        is_multiple = 1;
        case_id = '';
        var title = _('Create Project Template');
    }
    openPopup();
    $('#popup_bg_main').css({'width': '620px'});
    $(".crt_project_tmpl").show();
    $(".loader_dv").show();
    $('#inner_crtprojtmpl').html('');
    if (is_multiple) {
        $('#header_crtprjtmpl').html(title);
    }
    $("#errtmpl_msg_dv").hide();
    $("#crtprjtmplloader").hide();
    case_no = '';
    var tempTitle = '';
    if (arguments[3] && arguments[3] != '') {
        tempTitle = arguments[3];
    }
    if (arguments[4] && arguments[4] != '') {
        var tempId = arguments[4];
    }
    $.post(HTTP_ROOT + "projecttemplate/ProjectTemplates/all_project_templates", {
        "project_id": project_id,
        "case_id": case_id,
        'is_multiple': is_multiple,
        'tempTitle': tempTitle
    }, function (data) {
        if (data) {
            $(".loader_dv").hide();
            $('#inner_crtprojtmpl').show();
            $('#inner_crtprojtmpl').html(data);
            $('#tmpl-btn').show();
            $("#crtprjtmpl_btn").show();
            $("#new_cp_project").focus();
            $("#errcp_msg_dv").hide();
            if (tempId) {
                $('#hid_tmpl').val(tempId);
                $('#opt50').find('span#sel_tmpl_nm').html('<span class="value">' + tempId + '</span>' + tempTitle);
                $("#errcp_msg_dv").show();
            }
        }
    });
}
/* Resource Availability */
function changeUnavailableResource() {
    if ($('input[name=resource]').is(':checked')) {
    var $clickedOption = $('#inner_resource_notavailable').find('input:checked');
    var caseId = $clickedOption.attr('data-caseId');
    var caseUniqId = $clickedOption.attr('data-caseUniqId');
    var projectId = $clickedOption.attr('data-projectId');
    var assignTo = $clickedOption.attr('data-resource');
        var dtes = $clickedOption.attr('data-nxt-avail-date');
        var str_date = $clickedOption.attr('data-gantt-start-date');
        var est_hr = $clickedOption.attr('data-est-hour');
        $('#btn_tsk_avl').hide();
        $('#cust_loader_tsk_avl').show();

    var url = HTTP_ROOT + "Timelog/logTimes/changeresource";
        $.post(url, {
            'caseId': caseId,
            'assignTo': assignTo,
            'start_date': dtes,
            'caseUniqId': caseUniqId,
            'projectId': projectId,
            'str_date': str_date,
            'est_hr': est_hr
        }, function (res) {
        if (res) {
                var hashtag = parseUrlHash(urlHash);
                //  alert(hashtag[0]); return false ;
                closePopup();
                if (typeof easycase != 'undefined') {
                    if (hashtag[0] == 'details') {
                        easycase.refreshTaskList(caseUniqId);

                    } else {
                    easycase.refreshTaskList();
                    }
                } else {
                    window.location = HTTP_ROOT + 'dashboard#tasks';
        }
            }
    });
    } else {
        alert("Please select an user to assign this task!")
}
}

function closeChangeResourcePopup() {
    if ($('input[name=resource]').is(':checked')) {
      /*  var $clickedOption = $('#inner_resource_notavailable').find('input:checked');
        var caseId = $clickedOption.attr('data-caseId');
        var caseUniqId = $clickedOption.attr('data-caseUniqId');
        var projectId = $clickedOption.attr('data-projectId');
      //  var assignTo = $clickedOption.attr('data-resource');
        var assignTo = $('#inner_resource_notavailable').find('#task_assigned_id').val();
        var dtes = $clickedOption.attr('data-nxt-avail-date');
        var str_date = $clickedOption.attr('data-gantt-start-date');
        var est_hr = $clickedOption.attr('data-est-hour'); */
        var caseId = $('#inner_resource_notavailable').find('#task_id').val();
        var caseUniqId = $('#inner_resource_notavailable').find('#task_uniq_id').val();
        var projectId = $('#inner_resource_notavailable').find('#task_project_id').val();
      //  var assignTo = $clickedOption.attr('data-resource');
        var assignTo = $('#inner_resource_notavailable').find('#task_assigned_id').val();
      //  var dtes = $('#inner_resource_notavailable').find('#task_assigned_id').val();
        var str_date =$('#inner_resource_notavailable').find('#task_gantt_start_date').val();
        var est_hr = $('#inner_resource_notavailable').find('#task_estimated_hr').val();
        // $('#change_btn'+assignTo).hide();
        // $('#changeloader'+assignTo).show();
        $('#btn_tsk_avl').hide();
        $('#cust_loader_tsk_avl').show();
        var url = HTTP_ROOT + "Timelog/logTimes/overloadUsers";
        $.post(url, {
            'caseId': caseId,
            'assignTo': assignTo,
          //  'start_date': dtes,
            'caseUniqId': caseUniqId,
            'projectId': projectId,
            'str_date': str_date,
            'est_hr': est_hr
        }, function (res) {
            if (res) {
                var hashtag = parseUrlHash(urlHash);
    closePopup();
                if (typeof easycase != 'undefined') {
                    if (hashtag[0] == 'details') {
                        easycase.refreshTaskList(caseUniqId);

                    } else {
                        easycase.refreshTaskList();
            }
                } else {
                    window.location = HTTP_ROOT + 'dashboard#tasks';
                }
            }
        });
    } else {
        alert("Please select an user to assign this task!")
    }
}
function gantt_setting() {
    openPopup();
    $('#inner_gant_setng').html('');
    $('.gannt_setting').show();
    var url = HTTP_ROOT + "ganttchart/Ganttchart/get_user_list";

    $.post(url, {}, function (data) {
        if (data) {
            $('#inner_gant_setng').show();
            $('.loader_dv').hide();
            $('#inner_gant_setng').html(data);
        }
    });

}
function openRecurringTaskPopup() {
    if ($('#is_recurring').is(":checked")) {
        openPopup();
        $(".recurring_task").show();
    }
}
function serializeDatatoArray(recurrenceDetailsArr) {
    var data = {};
    data.weekly_days = '';
    $(recurrenceDetailsArr).each(function (index, obj) {
        if (obj.name === 'weekly_days') {
            data.weekly_days += obj.value + ',';
        } else {
            data[obj.name] = obj.value;
        }
    });
    if (data.weekly_days != '') {
        data.weekly_days = data.weekly_days.substring(0, data.weekly_days.length - 1);
    }
    return data;
}

function showRecurringInfo(id) {
    openPopup();
    $(".recurring_info").show();
    $(".loader_dv").show();
    $.post(HTTP_ROOT + 'RecurringTask/RecurringTasks/getRecurringTasks', {
        id: id
    }, function (res) {
        $(".loader_dv").hide();
        $("#recurring_info_id").html(res);
    });
}
function stopRecurring(eid) {
    if (confirm("Are sure you want to stop the recurring task?")) {
        $("#srloader").show();
        $("#srbtn").hide();
        $.post(HTTP_ROOT + 'RecurringTask/RecurringTasks/stopRecurringTasks', {
            eid: eid
        }, function (res) {
            if (res.status == 1) {
                $("#recurringTaskId_" + eid).hide();
                closePopup();
                $("#srbtn").show();
                $("#srloader").hide();
                showTopErrSucc('success', "Recurring task stopped successfully.");
            } else {
                $("#srbtn").show();
                $("#srloader").hide();
                showTopErrSucc('error', "Recurring Task cannot stop. Please try again later.");
            }
        }, 'json');
    }
}