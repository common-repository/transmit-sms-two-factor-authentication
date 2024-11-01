<?php
//ajax handle
if(isset($_POST['getConnect']) && $_POST['getConnect']== 'Y' && $_GET['page'] == '2FA-Transmit-SMS'){
    require_once T2FA_PLUGIN_DIR .'/APIClient2.php';
    $apikey = trim($_POST['apikey']);
    $secret = trim($_POST['secret']);
    $api=new transmitsmsAPI($apikey,$secret);
    $result=$api->getLists(1,100);
    if($result->error->code=='SUCCESS'){
        $opt = array('apikey'=>base64_encode($apikey),'secret'=>base64_encode($secret));
        update_option(T2FAWpOptionApi, addslashes(serialize($opt)));
        //checking and crete list
        $T2FAOption = unserialize(@get_option(T2FAWpOption));
        $listExist = false;
      //  if(empty($T2FAOption['listId'])){
            foreach($result->lists as $keyList =>$valList){
                //list exitst
                if($valList->name == T2FA_BurstSMSList){
                    $listExist = true;
                    $T2FAOption['listId'] = $valList->id;
                    update_option(T2FAWpOption,serialize($T2FAOption));
                    break;
                }
            }
            if(!$listExist){
                $T2FAcustomFieldsBurstSMSList = array('country','email');
                $resultCreateList=$api->addList(T2FA_BurstSMSList,$T2FAcustomFieldsBurstSMSList);
                if($resultCreateList->error->code=='SUCCESS'){
                    $T2FAOption['listId'] =  (int)$resultCreateList->id;
                    update_option(T2FAWpOption,serialize($T2FAOption));
                }else{
                    echo $result->error->description;
                    exit();
                 }
            }   
       // }
       echo 'SUCCESS';
    }else{
        delete_option(T2FAWpOptionApi);
        echo $result->error->description;
    }
    
    exit();
 
}
if(isset($_POST['T2FA_submitOption']) && $_POST['T2FA_submitOption'] == 'Y' && $_GET['page'] == '2FA-Transmit-SMS'){
    $T2FA_digitCode = (int)$_POST['T2FA_CodeDigit'];
    $T2FAOption = unserialize(@get_option(T2FAWpOption));
    $arrOption = array('digitCode'=>$T2FA_digitCode);
    $arrOption['listId'] = $T2FAOption['listId'];
    update_option(T2FAWpOption, serialize($arrOption));
    echo   'SUCCESS';    
    exit();
}
?>
