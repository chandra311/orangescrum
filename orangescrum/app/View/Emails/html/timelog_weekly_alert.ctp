<table style='border-collapse:collapse;border-spacing:0;text-align:left;width:700px;'>
    <tr>
        <td>
            <table style='border-collapse:collapse;border-spacing:0;text-align:left;width:700px;border:1px solid #5191BD'>
                <tr style='background:#5191BD;height:50px;'>
                    <td colspan="5" style='font:bold 14px Arial;padding:10px;color:#FFFFFF;'>
                        <span style='font-size:18px;'>Orangescrum</span> - Time Log Weekly Alert
                    </td>
                </tr>
                <tr style='background:#5191BD;height:50px;'>
                    <td colspan="5" style='font:bold 14px Arial;padding:10px;color:#FFFFFF;'>
                        You have not logged time for <?php echo count($data); ?> tasks from <?php echo date('d M, Y', strtotime($startDate)); ?> to <?php echo date('d M, Y', strtotime($endDate)); ?>.
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #5191BD">
                    <th style='font:bold 14px Arial;padding:10px;border:1px solid  #5191BD'>Date</th>
                    <th style='font:bold 14px Arial;padding:10px;border:1px solid  #5191BD'>Project</th>
                    <th style='font:bold 14px Arial;padding:10px;border:1px solid  #5191BD'>Task</th>
                    <th style='font:bold 14px Arial;padding:10px;border:1px solid  #5191BD'>User</th>
                    <th style='font:bold 14px Arial;padding:10px;border:1px solid  #5191BD'>Status</th>
                </tr>
                <?php if(!empty($data)){
                foreach($data as $k => $val){ ?>
                <tr>
                    <td style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'><?php echo $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $val['Easycase']['dt_created'], "date"); ?></td>
                    <td style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'><?php echo $val['project']['name']; ?></td>
                    <td style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'><?php echo $val['Easycase']['title']; ?></td>
                    <td style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'><?php echo $val['0']['name']; ?></td>
                    <td style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'><?php echo $this->Format->getStatusDetail($val['Easycase']['legend']); ?></td>
                </tr>
                <?php }
                } else{ ?>
                <tr>
                    <td colspan="5" style='font:normal 14px Arial;padding:10px;border:1px solid  #5191BD'>No Pending Time Log</td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="5" align='left' style='font:14px Arial;padding:10px;border-top:1px solid #E1E1E1'>Thanks,<br/>Team OrangeScrum</td>
                </tr>
            </table>
        </td>
    </tr>
    <?php /* <tr>
        <td>
            <table style='margin-top:5px;width:600px;'>
                <tr>
                    <td style='font:13px Arial;color:#737373;'>Don't want these emails? To unsubscribe, please click <a href='<?php echo HTTP_ROOT; ?>invoice-settings' target='_blank'>Unsubscribe</a> and trun off <b>Invoice Weekly Alert</b> E-mail notification.</td>
                </tr>
            </table>
        </td>
    </tr> */ ?>
</table>