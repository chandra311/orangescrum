<?php

/* * *******************************************************************************
 * Orangescrum Community Edition is a web based Project Management software developed by
 * Orangescrum. Copyright (C) 2013-2014
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact Orangescrum, 2059 Camden Ave. #118, San Jose, CA - 95124, US. 
  or at email address support@orangescrum.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * Orangescrum" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by Orangescrum".
 * ****************************************************************************** */


/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Router::connect('/', array('controller' => 'users', 'action' => 'login'));
Router::connect('/mydashboard', array('controller' => 'easycases', 'action' => 'mydashboard'));
Router::connect('/dashboard', array('controller' => 'easycases', 'action' => 'dashboard'));
Router::connect('/getting_started/*', array('controller' => 'users', 'action' => 'getting_started'));
Router::connect('/onbording', array('controller' => 'projects', 'action' => 'onbording'));
Router::connect('/license/*', array('controller' => 'users', 'action' => 'license'));
Router::connect('/bug-report/*', array('controller' => 'reports', 'action' => 'glide_chart'));
Router::connect('/task-report/*', array('controller' => 'reports', 'action' => 'chart'));
Router::connect('/hours-report/*', array('controller' => 'reports', 'action' => 'hours_report'));
Router::connect('/how-it-works/*', array('controller' => 'users', 'action' => 'tour'));

Router::connect('/users/notification', array('controller' => 'users', 'action' => 'email_notification'));
Router::connect('/activities', array('controller' => 'users', 'action' => 'activity'));
Router::connect('/help', array('controller' => 'easycases', 'action' => 'help'));
Router::connect('/help/*', array('controller' => 'easycases', 'action' => 'help'));
Router::connect('/reminder-settings', array('controller' => 'projects', 'action' => 'groupupdatealerts'));
Router::connect('/import-export', array('controller' => 'projects', 'action' => 'importexport'));
Router::connect('/task-type', array('controller' => 'projects', 'action' => 'task_type'));
Router::connect('/Task-Status-Group', array('plugin' => 'TaskStatusGroup', 'controller' => 'Workflows', 'action' => 'workflow'));
Router::connect('/my-company', array('controller' => 'users', 'action' => 'mycompany'));
Router::connect('/milestone/saveMilestoneTitle', array('controller' => 'milestones', 'action' => 'saveMilestoneTitle'));
Router::connect('/milestone/*', array('controller' => 'milestones', 'action' => 'milestone'));

Router::connect('/install/*', array('controller' => 'Install', 'action' => 'index'));
Router::connect('/installed-addons', array('controller' => 'Install', 'action' => 'installed_addons'));

Router::connect('/timelog', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'time_log'));
Router::connect('/export_csv_timelog', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'export_csv_timelog'));
Router::connect('/existing_task', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'existing_task'));
Router::connect('/project_time_details', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'project_time_details'));
Router::connect('/add_tasklog', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'add_tasklog'));
Router::connect('/timelog_details', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'timelog_details'));
Router::connect('/delete_timelog', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'delete_timelog'));
Router::connect('/saveTimer', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'saveTimer'));
Router::connect('/existing_task', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'existing_task'));
Router::connect('/resource-utilization', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'resource_utilization'));
Router::connect('/resource-availability', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'resource_availability'));
Router::connect('/ajax_resource_utilization', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'ajax_resource_utilization'));
Router::connect('/ajax_resource_utilization_export_csv', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'ajax_resource_utilization_export_csv'));
Router::connect('/timelog_graph', array('plugin' => 'Timelog', 'controller' => 'LogTimes', 'action' => 'timelog_graph'));

/*Router::connect('/api/v1.0/add/*', array('controller' => 'rests', 'action' => 'add'));
Router::connect('/api/v1.0/create_task', array('controller' => 'rests', 'action' => 'create_task'));
Router::connect('/api/v1.0/create_task/*', array('controller' => 'rests', 'action' => 'create_task'));*/

Router::connect('/invoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'invoice'));
Router::connect('/getCountInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'getCountInvoice'));
Router::connect('/ajaxCustomerList', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'ajaxCustomerList'));
Router::connect('/ajaxInvoiceList', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'ajaxInvoiceList'));
Router::connect('/ajaxArchivedInvoiceList', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'ajaxArchivedInvoiceList'));
Router::connect('/ajaxTimeList', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'ajaxTimeList'));
Router::connect('/updateInvoicedropdown', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'updateInvoicedropdown'));
Router::connect('/addCustomer', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'add_customer'));
Router::connect('/customerDetails', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'customer_details'));
Router::connect('/ajaxInvoicePage', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'ajaxInvoicePage'));
Router::connect('/sendInvoiceEmail', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'sendInvoiceEmail'));
Router::connect('/invoiceLogo', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'invoiceLogo'));
Router::connect('/saveInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'save_invoice'));
Router::connect('/createInvoicePdf', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'createInvoicePdf'));
Router::connect('/add2Invoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'add2Invoice'));
Router::connect('/deleteInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'deleteInvoice'));
Router::connect('/invoice/invoices/invoicePdf', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'invoicePdf'));
Router::connect('/deleteInvoiceTimeLog', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'deleteInvoiceTimeLog'));
Router::connect('/invoice-settings', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'settings'));
Router::connect('/save-settings', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'save_settings'));
Router::connect('/markInvoicePaid', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'markInvoicePaid'));
Router::connect('/markGroupInvoicePaid', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'markGroupInvoicePaid'));
Router::connect('/markUnpaidInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'markUnpaidInvoice'));
Router::connect('/markGroupInvoiceUnpaid', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'markGroupInvoiceUnpaid'));
Router::connect('/archiveInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'archiveInvoice'));
Router::connect('/restoreInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'restoreInvoice'));
Router::connect('/groupRestoreInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'groupRestoreInvoice'));
Router::connect('/groupArchiveInvoice', array('plugin' => 'Invoice', 'controller' => 'invoices', 'action' => 'groupArchiveInvoice'));

Router::connect("/taskstatusgroup/:controller/:action",array('plugin' => 'TaskStatusGroup'));

Router::connect('/gantt-chart', array('plugin' => 'Ganttchart', 'controller' => 'Ganttchart', 'action' => 'ganttv2'));
Router::connect('/ganttchart/:controller/:action', array('plugin' => 'Ganttchart'));
Router::connect('/Ganttchart/:controller/:action', array('plugin' => 'Ganttchart'));

Router::connect('/chat', array('plugin'=>'Chat','controller' => 'chats', 'action' => 'freeChat'));
Router::connect('/chat/:controller/:action', array('plugin' => 'Chat'));

Router::connect('/clientrestriction/:controller/:action', array('plugin' => 'ClientRestriction'));

Router::connect('/projecttemplate/:controller/:action', array('plugin' => 'ProjectTemplate'));

Router::connect('/api-settings', array('plugin' => 'API', 'controller' => 'Apis', 'action' => 'settings'));
Router::connect('/api/v1.0/:action/*', array('plugin' => 'API','controller' => 'Apis'));
Router::connect('/Timelog/:controller/:action', array('plugin' => 'Timelog'));
Router::connect('/language/:controller/:action', array('plugin' => 'MultiLanguage'));
Router::connect('/language-settings', array('plugin' => 'MultiLanguage', 'Controller' => 'MultiLanguage', 'action' => 'settings'));

Router::connect("/ProjectTemplate/:controller/:action",array('plugin' => 'ProjectTemplate'));
// RecurringTask
Router::connect('/RecurringTask/:controller/:action', array('plugin' => 'RecurringTask'));
Router::connect('/recurringTask/:controller/:action', array('plugin' => 'RecurringTask'));
if (strpos(Router::url(), 'api/v1.0') > -1) {
    Router::mapResources('API.Apis');
    Router::parseExtensions();
}
Router::connect('/api/v2.0/:action', array('plugin' => 'MobileApi','controller' => 'MobileApis'));
if (strpos(Router::url(), 'api/v2.0/') > -1) {
    Router::mapResources('MobileApi.MobileApis');
    Router::parseExtensions('rss', 'json');
}


/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::loadAll();
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
