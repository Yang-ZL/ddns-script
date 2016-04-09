<?php
/**
*   This is a automatic php script for change public IP. The script can 
*   be used for LAN(Local Area Network) servers or other devices which need have a 
*   fix domain to point to it. 
*   NOTE: this script is using DNSPOD.CN API services currently. That is to say, you need to 
*   have a DNSPOD account if you want to use this script.
*
*   @Author     yang_zl(zlyang65@gmail.com)
*   @DateTime   2016-04-06 23:10
*
*/

require_once dirname(__file__) . '/func.class.php';
require_once dirname(__file__) . '/config.php';

date_default_timezone_set('Asia/Shanghai');
$data = date('[c]');

$ddns = new RPI_DDNS(DOMAIN, SUBDOMAIN); // here is your domain and subdomain which you want to DDNS

list($status, $code, $value) = $ddns->getPublicIP();
if (!$status)
    return $ddns->updateLog("{$data} error code: {$code}, {$value}.\n");
else 
    $curIP = $value;

list($status, $code, $value) = $ddns->getDomainID();
if (!$status) 
    return $ddns->updateLog("{$data} error code: {$code}, {$value}.\n");
else
    $domainID = $value;

list($status, $code, $value) = $ddns->getOneRecord($domainID);
if (!$status)
    return $ddns->updateLog("{$data} error code: {$code}, {$value}.\n");
else
    $recordID = $value;

// var_dump($curIP);
// var_dump($recordIP);

list($status, $code, $value) = $ddns->getOneRecord($domainID, 'ip');
if (!$status)
    return $ddns->updateLog("{$data} error code: {$code}, {$value}.\n");
else
    $recordIP = $value;

if (strcmp($curIP, $recordIP) == 0) 
    return $ddns->updateLog("{$data} No need to update, current public ip is {$recordIP}.\n");
else {
    list($status, $code, $value) = $ddns->updateIP($domainID, $recordID, $curIP);
    if (!$status)
        return $ddns->updateLog("{$data} error code: {$code}, {$value}.\n");
    else
        return $ddns->updateLog("{$data} Success to update IP.\n");
}