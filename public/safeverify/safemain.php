<?php
include 'safeban.php';
$safeVerifyResult = safeVerifyExecute(10,1000, 300000); // 连续10次/1秒内/不验证时5分钟失效
// loger($safeVerifyResult);
if($safeVerifyResult['code'] == 10000){
    $token = $safeVerifyResult['token'];
    $goToUrl = HTTP_PATH.'public/safeverify/safetips.php?token='.$token.'&url='.urlencode(SYS_WEB_URL);
    header("Location:$goToUrl");exit;
}
