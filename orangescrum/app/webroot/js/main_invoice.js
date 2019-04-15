function reset_tabs(){
		$('#mlstab_act_unbill').removeClass('active');
        $('#mlstab_cmpl_invoice').removeClass('active');
        $('#tab_manage_customers').removeClass('active');
        $('.newInvoice').remove();

        $('#showUnbilled').hide();
        $('#showInvoiceDiv').hide();
        $('#showCustomers').hide();
        $('.InvoiceDownloadEmail').hide();
}

    function get_mode(){
        return trim(window.location.hash.replace('#',''));
    }

    function ajaxSorting(){
    	var tcls = '';
		if(typeof(getCookie("INVOICE_SORTBY")!='undefined') && getCookie("INVOICE_SORTBY") ==cases){
			if(getCookie('INVOICE_SORTORDER')=='ASC'){
				remember_filters("INVOICE_SORTORDER",'DESC');
				tcls = 'tsk_desc';
			}else{
				remember_filters("INVOICE_SORTORDER",'ASC');
				tcls = 'tsk_asc';
			}
		}else{
			remember_filters("INVOICE_PAGE", type);
			remember_filters("INVOICE_SORTBY", cases);
			remember_filters("INVOICE_SORTORDER",'DESC');
			tcls = 'tsk_asc';
		}
	
	$('.tsk_sort').removeClass('tsk_asc tsk_desc'); 
	$(el).find('.tsk_sort').addClass(tcls);
        invoices.switch_tab(invoices.get_mode());
    }

    function add_customer_action(){
        $("#cust_err_msg").html('');
        var errMsg;
        var err = false;
        var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var rxAlphaNum = /^([0-9\(\)-]+)$/;
        if(trim($('#cust_fname').val())==''){
            errMsg = 'Please enter customer name.';
            err = true;
        }else if(trim($('#cust_email').val())==''){
            errMsg = _('Please enter email address.');
            err = true;
        }else if(!trim($('#cust_email').val()).match(emailRegEx)){
            errMsg = _('Please enter proper email address.');
            err = true;
        }else if(trim($('#cust_currency').val())==''){
            errMsg = _('Please select currency.');
            err = true;
        }else if(trim($("#cust_phone").val()) != '' && !trim($("#cust_phone").val()).match(rxAlphaNum)) {
            errMsg = _('Please enter proper phone number.');
            err = true;
        }

        if (err == true) {
            showTopErrSucc('error', errMsg);
            return false;
        }
        if(invoices.flag == true){
            invoices.flag = false;
            $('#cust_loader').show();
            $("#btn_add_customer").hide();
            $.ajax({
                url:$('#frm_add_customer').attr('action'),
                data:$('#frm_add_customer').serialize(),
                method:'post',
                dataType:'json',
                success:function(response){
                    invoices.flag = true;
                    $('#cust_loader').hide();
                    $("#btn_add_customer").show();
                    if(response.success == 'No'){
                        showTopErrSucc('error', _(response.msg));
                        return false;
                    }
                    if(get_mode() == 'customers'){
                        switch_tab('customers');
                    }else{
                        if(response.success == 'Yes' && response.mode == 'Add' && response.status == 'Active'){
                               
                               var new_item =  $('<a>').attr({
                                   class:"anchor customer_opts",
                                   'data-name': response.name,
                                   'data-id': response.details,
                                   'data-cid': response.id,
                                   'data-currency': response.currency,
                                   'data-email': response.email,
                                   id: 'opt_customer_'+response.id,
                                }).html(response.name);
                                var li = $("<li>").html(new_item);
                                    
                                    
                            $('#more_opt1123123').find('ul').append(li)
                            //$('#more_opt1123123').find('ul').append(response.html)
                            $('#more_opt1123123').find('.customer_opts').unbind('click').bind('click');
                            $('#edit_invoice_to').val(response.details);
                            $('#invoice_customer_id').val(response.id);
                            $('#invoice_customer_opts').find('.opt1').html(response.name);
                            $('#invoice_currency_code').html(''+response.currency+'');
                            $('#invoice_customer_currency').val(response.currency);
                            $('.inv_currency').html(response.currency);
                            $('#invoice_customer_email').val(response.email);
                        }
                    }
                    closePopup();
                }
            });
        }
    }
var invoices = {
    flag:true,
    set_date:function(obj,val){
        obj.datepicker("setDate", new Date(val) );
    },
    set_due_date:function(selectedDate){
         if($('#invoice_terms').val() != ''){
            var dt = new Date(selectedDate);
            dt.setDate(parseInt(dt.getDate()) + parseInt($('#invoice_terms').val()));
        }else{
            var dt = new Date(selectedDate)
        }
        this.set_date($("#edit_due_date"),dt);
    },
    set_invoice_to_details:function(cid){
        $("#invoice_customer_opts").find('.opt1').html(cid.attr('data-name'));
        $("#edit_invoice_to").html(cid.attr('data-id'));
        $('#more_opt1123123').hide();
        $("#invoice_customer_id").val(cid.attr('data-cid'));
        $("#invoice_customer_email").val(cid.attr('data-email'));
        $("#invoice_customer_email").val(cid.attr('data-email'));
        $("#invoice_currency_code").html(''+(cid.attr('data-currency')!=''?cid.attr('data-currency'):'USD')+'');
        $("#invoice_customer_currency").val((cid.attr('data-currency')!=''?cid.attr('data-currency'):'USD'));
        $(".inv_currency").html((cid.attr('data-currency')!=''?cid.attr('data-currency'):'USD'));
    },
    date_diff:function(date1,date2,interval){
        var date1 = new Date(date1);
        var date2 = new Date(date2);
        var interval = typeof interval != 'undefined' ? interval : 'days';
        var second=1000, minute=second*60, hour=minute*60, day=hour*24, week=day*7;   
        var timediff = date2 - date1;
        if (isNaN(timediff)) return NaN;
        switch (interval) {
            case "years": return date2.getFullYear() - date1.getFullYear();
            case "months": return ((date2.getFullYear()*12+date2.getMonth()) - (date1.getFullYear()*12+date1.getMonth()));
            case "weeks"  : return Math.floor(timediff / week);
            case "days"   : return Math.floor(timediff / day); 
            case "hours"  : return Math.floor(timediff / hour); 
            case "minutes": return Math.floor(timediff / minute);
            case "seconds": return Math.floor(timediff / second);
            default: return undefined;
        }
    },
    ajaxSorting:function(type,cases,el){
        var tcls = '';
		if(typeof(getCookie("INVOICE_SORTBY")!='undefined') && getCookie("INVOICE_SORTBY") ==cases){
			if(getCookie('INVOICE_SORTORDER')=='ASC'){
				remember_filters("INVOICE_SORTORDER",'DESC');
				tcls = 'tsk_desc';
			}else{
				remember_filters("INVOICE_SORTORDER",'ASC');
				tcls = 'tsk_asc';
			}
		}else{
			remember_filters("INVOICE_PAGE", type);
			remember_filters("INVOICE_SORTBY", cases);
			remember_filters("INVOICE_SORTORDER",'DESC');
			tcls = 'tsk_asc';
		}
	
	$('.tsk_sort').removeClass('tsk_asc tsk_desc'); 
	$(el).find('.tsk_sort').addClass(tcls);
        switch_tab(get_mode());
    }
};
    /* By : CP
    * used to format date time in ctp
    */
    function formatDate(format,date){
        return $.datepicker.formatDate(format, new Date(date));
        var monthNamesShort =  ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]; // For formatting
        return date.toString();
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '')
          .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
          sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
          dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
          s = '',
          toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
              .toFixed(prec);
          };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
          .split('.');
        if (s[0].length > 3) {
          s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
          .length < prec) {
          s[1] = s[1] || '';
          s[1] += new Array(prec - s[1].length + 1)
            .join('0');
        }
        return s.join(dec);
      }
    