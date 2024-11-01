<?php
add_action( 'admin_init', 'jQueryUi' );
function jQueryUi() {
    wp_enqueue_style( 'baseCSS', T2FA_PLUGIN_URL . '/assets/css/style.css' );
    wp_enqueue_style( 'bootsrapCSS', T2FA_PLUGIN_URL . '/assets/css/bootstrap.css' );
    wp_register_script('bootstrapJs',  T2FA_PLUGIN_URL . '/assets/js/bootstrap.js', false, null);
    wp_enqueue_style( 'fontAwesome', T2FA_PLUGIN_URL . '/assets/font-awesome-4.2.0/css/font-awesome.css' );
    wp_enqueue_script('bootstrapJs');
    wp_enqueue_script('jquery-ui');
}
function T2FA_admin_menu() {
        global $menu;
        $exist = false;
        foreach( $menu as $k => $item ){
      	     if($item[2] == 'transmit-sms'){$exist = true; break;}
    	}
        if(!$exist){
            // add_menu_page('Transmit SMS', 'Transmit SMS', 'manage_options','transmit-sms');
            add_menu_page('Transmit SMS', 'Transmit SMS', 'manage_options','transmit-sms',NULL,TSC_PLUGIN_URL.'/assets/images/sms-wp-favicon.png');
            wp_register_script('admin-js',  T2FA_PLUGIN_URL . '/admin/admin.js', false, null);
            wp_enqueue_script('admin-js');
        }
        add_action( 'wp_enqueue_scripts', 'T2FA_removeSubmenu' );
        add_submenu_page('transmit-sms', 'Two Factor Authentication Transmit SMS', '2FA Transmit SMS', 'manage_options', '2FA-Transmit-SMS', 'T2FASMSC_options' );
}
function T2FASMSC_options() {
    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if(isset($_POST['T2FA_hidden']) && $_POST['T2FA_hidden'] == "Y"){
            T2FASMSC_handleSubmit();
        }
        echo T2FASMSC_settingForm();
}
function T2FASMSC_settingForm(){
    require_once T2FA_PLUGIN_DIR .'/APIClient2.php';
    global $T2FA_downloadFileType;
    $T2FASms = unserialize(stripslashes(get_option(T2FAWpOptionApi)));
    if(!empty($T2FASms['apikey'])){
        $T2FAapikey  = base64_decode($T2FASms['apikey']);
        $T2FAapiSecret  = base64_decode($T2FASms['secret']);
    }else {
        $T2FAapikey = '';
        $T2FAapiSecret = '';
    }
    $api=new transmitsmsAPI($T2FAapikey,$T2FAapiSecret);
    $T2FAOption = unserialize(get_option(T2FAWpOption));
    $T2FAOptionDigitCode = (int)@$T2FAOption['digitCode'];
   
    $offset=0;
    $limit=10;
    $arrVisitor = $api->getList((int)$T2FAOption['listId'],$offset,$limit);
    ob_start();  
?>

<script type="text/javascript">
						
 var selectedInput = null;
    jQuery(document).ready(function(){
        jQuery('#T2FA_pageafter').addClass('form-control');
        //jQuery('#T2FA_pageafter').css('width', '400');
        apiConnect();
        jQuery('#T2Fa_optionForm').submit(function(){
            jQuery.ajax({
               url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
               type:'POST',
               data:jQuery(this).serialize(),
               beforeSend:function(){
                jQuery("#submitLoader").fadeIn('fast');
                jQuery('#submitOptionResult').removeClass('updated');
                jQuery('#submitOptionResult').removeClass('error');
               },
               success: function(result){
                    jQuery("#submitLoader").fadeOut('fast');
                    if(result == 'SUCCESS'){
                        jQuery('#submitOptionResult').addClass('updated');
                        jQuery('#submitOptionResult').html('<?=T2FA_successSubmit?>');
                    }else {
                        jQuery('#submitOptionResult').addClass('error');
                        jQuery('#submitOptionResult').html('<?= T2FA_failSubmit?>');
                    }
                }
    		});
            return false;
        })
        
        
      
     });
     
     function apiConnect(){
        var apikey = jQuery('#T2FA_apikey').val();
        var apisecret = jQuery('#T2FA_apisecret').val();
        if(apikey != "" && apisecret != ""){
            jQuery.ajax({
               url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
               type:'POST',
               data:'getConnect=Y&apikey=' + apikey + '&secret=' + apisecret,
               beforeSend:function(){
                jQuery("#verifyLoader").fadeIn('fast');
                jQuery('#T2FA_optionPanel').fadeOut('fast');
                jQuery('#connectionResult').removeClass('updated');
                jQuery('#connectionResult').removeClass('error');
               },
               success: function(result){
                    jQuery("#verifyLoader").fadeOut('fast');
                    if(result == 'SUCCESS'){
                        jQuery('#connectionResult').addClass('updated');
                        jQuery('#connectionResult').html('<?=T2FA_successVerify?>');
                        jQuery('#T2FA_optionPanel').fadeIn('fast');
                    }else {
                        jQuery('#connectionResult').addClass('error');
                        jQuery('#connectionResult').html(result);
                    }
                }
		});
               }
      return false;
    }

 </script>

<div class="row"> 
    <div id="icon-options-general" class="icon32 col-lg-12">
        <br> </div>
        <div class="col-lg-12">
<h2> <?php echo  __( 'Transmit SMS 2 Step Authentication', 'T2FA_trdom' );?> </h2><br>
</div>
<div class="col-lg-6">
    <div class="col-lg-12">
        <div id="postbox-container-2" class="panel panel-info">
            <div id="revisionsdiv" class="panel-heading">
                <span style="float:left; margin:3px 7px 20px 0;" class="fa fa-gear"></span> <span>
                        <?php    echo "<span>" . __( 'Settings', 'T2FA_trdom' ) . "</span>"; ?>
            </div>
            <div style="padding:10px" class="panel-body">
                <form name="T2FAform" id="T2FAform" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
                    <input type="hidden" name="T2FA_hidden" value="Y">  
                    <table class="form-table" style="col-lg-12" cellpadding='1'>
                        <tr>
                            <td><label for="T2FA_apikey"><?php _e("API Key : " ); ?> </label></td>
                            <td><input required="" class="form-control" type="text" name="T2FA_apikey" style="width: 100%;" id="T2FA_apikey" value="<?php echo $T2FAapikey; ?>" ></td>
                        </tr>
                        <tr>
                            <td><label for="T2FA_apisecret"><?php _e("API Secret : " ); ?></label></td>
                            <td><input required="" class="form-control" type="text" name="T2FA_apisecret"  style="width: 100%;" id="T2FA_apisecret" value="<?php echo $T2FAapiSecret; ?>" >
                            <span style="font-style: italic;"> Get these details from the API settings section of your account.</span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                             <button id="verify" class="button button-primary button-large" type="button" onclick="apiConnect();" accesskey="p" name="verify"><i class="fa fa-plug"></i> Verify key</button>
                                <img id="verifyLoader"  style="margin-left:10px !important;display:none" src="<?php echo T2FA_PLUGIN_URL ?>/assets/images/loading.gif" title="still loading" > 
                            <div id="connectionResult"></div>
                            </td>
                        </tr>
                    
                    
                    </table>
                      
                        
                </form>  
                </div>
                </div>
        </div>
<!----- row 2-->   
   <div class="col-lg-12" id="T2FA_optionPanel" style="display: none;">
   <div class="panel panel-info">
        <div class="panel panel-heading">
            <i class="fa fa-gear"></i> Options
        </div>
        <div class="panel panel-body">
        <form id="T2Fa_optionForm" name="T2Fa_optionForm" />
        <?PHP
             $argsPage = array(
                    'depth'                 => 0,
                    'child_of'              => 0,
                    'selected'              => $T2FAOptionThankyouPage,
                    'echo'                  => 1,
                    'name'                  => 'T2FA_pageafter',
                    'id'                    => 'T2FA_pageafter', // string
                     'class'                =>'form-control',
                    'show_option_none'      => null, // string
                    'show_option_no_change' => null, // string
                    'option_none_value'     => null, // string
                     'post_type'            => 'page',
                );
            ?>
         <table class="form-table" style="col-lg-12" cellpadding='1'>
            <tr>
                <td><label for="T2FA_apikey" title="digit of confirmation code">Code digit</label></td>
                <td>
                
                    <input type="number" min="5" value="<?=$T2FAOptionDigitCode?>"   name="T2FA_CodeDigit" id="T2FA_CodeDigit"  required="" style="width: 100px;" />
                </td>
              </tr>
              <tr>
                <td>
                   <label for="T2FA_apikey" title="digit of confirmation code">Short code </label> 
                </td>
                <td>
                    <kbd class="badge">[<?= T2FA_SHORTCODE ?>]</kbd> <br />
                    <small>put this short code to post or page to apply Transmit 2 FA</small>                    
                </td>
                
              </tr>
                  <tr>
            
                <td>&nbsp;</td>
                <td><input type="hidden" name="T2FA_submitOption" id="T2FA_submitOption" value="Y" />
                <img id="submitLoader"  style="margin-left:10px !important;display:none" src="<?php echo T2FA_PLUGIN_URL ?>/assets/images/loading.gif" title="still loading" > 
                      
                <button id="T2Fa_submitUpdateOption" type="submit" class="button button-large button-primary" style="width:150px;height:50px"> <i class="fa fa-save fa-2x"></i>&nbsp; Update Option</button>
                </td>
            </tr>
              
         </table>
           </form> 
            <div id="submitOptionResult" class="col-lg-12" style="width:80%"></div>
        </div>
   </div>
   
             
             </div>
    </div>
<div class="col-lg-5">
    <div id="postbox-container-2" class="panel panel-info">
        <div id="revisionsdiv" class="panel-heading">
            <span style="float:left; margin:3px 7px 20px 0;" class="fa fa-group"></span> <span>
                    <?php    echo "<span>" . __( '#'.T2FA_BurstSMSList.' contact list', 'T2FA_trdom' ) . "</span>"; ?>
        </div>
        <div style="padding:10px" class="panel-body">
        <?PHP
       if($arrVisitor->error->code=='SUCCESS' && (int)$arrVisitor->members_total > 0){
           ?>
           <table style="font-size: 12px;" class="table table-striped table-responsive table-hover">
            <thead>
                <tr>
                    <td>No</td>
                    <td>Name</td>
                    <td>Phone</td>
                    <td>Email</td>
                    <td>Country</td>
                </tr>
             </thead>
            <tbody>
           <?PHP
            $i = 0;
            foreach($arrVisitor->members as $visitor){
            $i++;
                ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?=$visitor->first_name;?></td>
                    <td><?=$visitor->msisdn;?></td>
                    <td><?=@$visitor->fields->email;?></td>
                    <td><?=$visitor->fields->country;?></td>
                </tr>
                <?PHP
             }
             ?>
             </tbody>
            </table>
             <?PHP
             
        }
   ?>
        
        
        </div>
    </div>
</div>
    <?php
    $FORM = ob_get_contents();
    ob_end_clean();
    
   return $FORM;
    
}
function  T2FASMSC_handleSubmit(){
    
    $apikey = base64_encode(trim($_POST['T2FA_apikey']));
    $apisecret = base64_encode(trim($_POST['T2FA_apisecret']));
    $recivernumber = base64_encode(trim($_POST['T2FA_adminNumber']));
    $ownerCostum = empty($_POST['T2FA_ownerCostum'])?'':$_POST['T2FA_ownerCostum'];
    $receivedCustom = trim($_POST['T2FA_receivedCustom']);
    $listId = $_POST['T2FA_addToList'];
    $statuses = (array) get_terms( 'shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );

    
    $burstcc = array();
    if(isset($_POST['burstcc']) && is_array($_POST['burstcc'])){
        foreach($_POST['burstcc'] as $key => $val){
            $arrBccTemp = explode('|',$val);
            $burstcc[$arrBccTemp[1]] = $arrBccTemp[0];        
        }
    }
    $arrSEtting = array('apikey'=>$apikey,'apisecret'=>$apisecret,'reciver_number' =>$recivernumber,
                    'ownerCostum'=>$ownerCostum,'receivedCustom'=> $receivedCustom,
                    'enaReceivedCustom' => empty($_POST['T2FA_enaReceivedCustom'])?'':$_POST['T2FA_enaReceivedCustom'], 
                   'list_id'=>$listId,'country_code'=>$burstcc,'default_country_code' => $_POST['defburstcc']);
                   
    //add order status
   
    foreach($statuses as $ks => $orderStatus){
        if($orderStatus->slug == 'on-hold')  $orderStatus->slug = 'onhold';
        $arrSEtting[$orderStatus->slug.'Custom'] =   trim($_POST['T2FA_'.$orderStatus->slug.'Custom']);
        $arrSEtting['ena'.ucfirst($orderStatus->slug).'Custom'] =  empty($_POST['T2FA_ena'.ucfirst($orderStatus->slug).'Custom'])?'':$_POST['T2FA_ena'.ucfirst($orderStatus->slug).'Custom'];
        
    }
            
    /* 'completedCustom' => $completedCustom,
                    'pendingCustom' => trim($_POST['T2FA_pendingCustom']), 'failedCustom' =>trim($_POST['T2FA_failedCustom']), 'onholdCustom' => trim($_POST['T2FA_onholdCustom']),
                    'refundedCustom' =>trim($_POST['T2FA_refundedCustom']), 'cancelledCustom' =>trim($_POST['T2FA_cancelledCustom']),
                    
                    'enaReceivedCustom' => empty($_POST['T2FA_enaReceivedCustom'])?'':$_POST['T2FA_enaReceivedCustom'], 'enaProcessingCustom' => empty($_POST['T2FA_enaProcessingCustom'])?'':$_POST['T2FA_enaProcessingCustom'],
                    'enaCompletedCustom' => empty($_POST['T2FA_enaCompletedCustom'])?'':$_POST['T2FA_enaCompletedCustom'],'enaPendingCustom' => empty($_POST['T2FA_enaPendingCustom'])?'':$_POST['T2FA_enaPendingCustom'],
                    'enaRefundedCustom' => empty($_POST['T2FA_enaRefundedCustom'])?'':$_POST['T2FA_enaRefundedCustom'],'enaCancelledCustom' => empty($_POST['T2FA_enaCancelledCustom'])?'':$_POST['T2FA_enaCancelledCustom'],
                    'enaOnholdCustom' => empty($_POST['T2FA_enaOnholdCustom'])?'':$_POST['T2FA_enaOnholdCustom'],'enaFailedCustom' =>empty($_POST['T2FA_enaFailedCustom'])?'':$_POST['T2FA_enaFailedCustom'],
                    
                    ); */
                    
    update_option( 'T2FASmsSettings', addslashes(serialize($arrSEtting)));
}


?>