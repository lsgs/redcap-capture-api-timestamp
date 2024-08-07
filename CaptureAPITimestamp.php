<?php
/**
 * REDCap External Module: Capture API Timestamp
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
namespace MCRI\CaptureAPITimestamp;

use ExternalModules\AbstractExternalModule;

class CaptureAPITimestamp extends AbstractExternalModule
{
    public function redcap_every_page_before_render($project_id=null) 
    {
        if (!defined('PAGE' )) return;
        if (PAGE!='api/index.php') return;
        if (!isset($_POST['token'])) return;
        if (isset($_POST['mobile_app'])) return; // ignore mobile app token use due to USERID exceptions in Records.php
        $saveToPid = $this->getSystemSetting('log-project');
        $saveData = array(
            'token' => $this->escape($_POST['token']),
            'last_ts' => NOW
        );
        $result = \REDCap::saveData(array(
            'project_id' => $saveToPid,
            'dataFormat' => 'json',
            'data' => json_encode_rc(array($saveData))
        ));
        if (isset($result['errors']) && !empty($result['errors'])) {
            $this->log(json_encode_rc($result));
        }
    }
}