<?PHP
class TransmitAuth{
    public $T2FAOption; 
  public function __construct() {
   // woocommerce_thankyou_order_id
    // indicates we are running the admin
    if ( is_admin() ) {
      require_once T2FA_PLUGIN_DIR . '/admin/admin-controller.php';  
      require_once T2FA_PLUGIN_DIR . '/admin/admin.php'; 
    }else{
        if($arrOption = get_option(T2FAWpOption)){
            $this->T2FAOption = unserialize($arrOption);
            add_filter('wp_head', array(&$this,'addHeadScript'));
            add_filter('wp_footer', array(&$this,'addFooterScript'));
            add_shortcode(T2FA_SHORTCODE,array(&$this,'T2FA_shortCodeHandle'));
            
            if(isset($_POST['requestCode']) && $_POST['requestCode']=='yes'){
                $this->sendingCodeConfirmation();
                exit();
            }elseif(isset($_POST['T2FAsubmitCode']) && $_POST['T2FAsubmitCode']=='yes'){
                $this->submitCodeConfirmation();
                exit();
            }elseif(isset($_GET['T2Fsuccess']) && $_GET['T2Fsuccess'] == 'yes'){
                if (!session_id())session_start();
                $_SESSION['T2FAPopupHide'] = "yes";
               exit();
            }
        }
       
     }
  }
  
  public function removePopup(){
     $jsScript = '<script type="text/javascript"> 
                jQuery(document).ready(function(){
                    jQuery("#T2FAModal").modal({
                        show:false,
                     });
                });
             </script>';
          echo  $jsScript;
          exit();
  }
  
  public function T2FA_shortCodeHandle($attr){
        global $post;
        if ( is_single($post->ID) ) {
            if(isset($_SESSION['T2FAPopupHide']) && $_SESSION['T2FAPopupHide'] === 'yes'){
              //  unset($_SESSION['T2FAPopupHide']);
                return ;
            }else {
                $jsScript = '<script type="text/javascript"> 
                    jQuery(document).ready(function(){
                        jQuery("#T2FAModal").modal({
                            keyboard: false,
                            backdrop:"static",
                            show:true,
                         });
                    });
                 </script>';
              return $jsScript;
          }
        }else return;
      
    
  }
    
    public function add_vary_header($headers) {
        $headers['Vary'] = 'User-Agent';
        return $headers;
    }
    public function sendingCodeConfirmation(){
        global $wpdb;
        require_once T2FA_PLUGIN_DIR . '/APIClient2.php';
        $requestId = trim($_POST['sessionId']);
        $mobilePhone = trim($_POST['mobilePhone']);
        $countryiISO = trim($_POST['T2FACountry']);        
        $codeConfirmation = substr(md5(microtime()),rand(0,26),(int)$this->T2FAOption['digitCode']);
        $WBSms = unserialize(stripslashes(get_option(T2FAWpOptionApi)));
        $transmitSmsApiKey = base64_decode($WBSms['apikey']);
        $transmitSmsApiSecret = base64_decode($WBSms['secret']);
        $codeConfirmation = strtoupper($codeConfirmation);
        //sent sms
        $WBmsAPI = new transmitsmsAPI($transmitSmsApiKey, $transmitSmsApiSecret);
      
         //reformating number
        
        $formatTonumber = $WBmsAPI->formatNumber($mobilePhone,$countryiISO);
        if(@$formatTonumber->error->code == 'SUCCESS') {
             $mobilePhone =  $formatTonumber->number->international;
             
        }else {
            echo 'WRONG_FORMAT';
            exit();
        }  
        
        $textMessage = 'Your Transmit SMS verification code is '.$codeConfirmation;
        $result=$WBmsAPI->sendSms($textMessage, trim($mobilePhone));  
         if(@$result->error->code=='SUCCESS'){ 
            $arrToken = unserialize(get_option(T2FAWpOptionToken));
            $arrToken[$requestId] =  $codeConfirmation;
            update_option(T2FAWpOptionToken,serialize($arrToken));   
            echo 'SUCCESS';
         }else echo $result->error->description;
     }
     public function submitCodeConfirmation(){
        global $wpdb;
        require_once T2FA_PLUGIN_DIR . '/APIClient2.php';
        $sessionnId = trim($_POST['T2FASession']);
        $codeConfirmation = trim(strtoupper($_POST['T2FACode']));
        $visitorName = trim($_POST['T2FAname']);
        $visitorEmail = trim($_POST['T2FAemail']);
        $visitorMobile = trim($_POST['T2FAmobile']);
        $visitorCountry = trim($_POST['T2FAcountry']);
        $visitorFileDownload = trim($_POST['T2FAFileDownload']);
        $dateTimmeNow = date('Y-m-d H:i:s');
        $arrToken = unserialize(get_option(T2FAWpOptionToken));
        $sessionCode = strtoupper(trim(@$arrToken[$sessionnId]));
        $tableCountry = $wpdb->prefix . "T2FAtransmit_country"; 
        $arrCountry = $wpdb->get_row( "SELECT * FROM $tableCountry where iso='".$visitorCountry."'", OBJECT );
        if($sessionCode == $codeConfirmation){
            $WBSms = unserialize(stripslashes(get_option(T2FAWpOptionApi)));
            $transmitSmsApiKey = base64_decode($WBSms['apikey']);
            $transmitSmsApiSecret = base64_decode($WBSms['secret']);
            $WBmsAPI = new transmitsmsAPI($transmitSmsApiKey, $transmitSmsApiSecret);
            $T2FAOption = unserialize(get_option(T2FAWpOption));
            //insert contact to list
            $formatTonumber = $WBmsAPI->formatNumber($visitorMobile,$visitorCountry);
            if($formatTonumber->error->code == 'SUCCESS') {
                 $mobilePhone =  $formatTonumber->number->international;
            }else {
                $mobilePhone =  $visitorMobile;
            }  
             $customFields=array($arrCountry->printable_name,$visitorEmail);
             $resultAddList =$WBmsAPI->addToList((int)$T2FAOption['listId'],$mobilePhone,$visitorName,'',$customFields);
             echo 'SUCCESS';
            
        }else {
            echo 'ERROR';
        }
        exit();
     }
  
    public function addHeadScript(){
        wp_enqueue_style( 'bootsrapCSS', T2FA_PLUGIN_URL . '/assets/css/bootstrap.css' );
        wp_enqueue_style( 'fontAwesome', T2FA_PLUGIN_URL . '/assets/font-awesome-4.2.0/css/font-awesome.css' );
        wp_register_script('bootstrapJs',  T2FA_PLUGIN_URL . '/assets/js/bootstrap.js', false, null);
        wp_enqueue_script('bootstrapJs');
        return;
    }
    public function addFooterScript(){
        global $wp_rewrite;
        global $wpdb;
        $tableCountry = $wpdb->prefix . "T2FAtransmit_country"; 
        $T2FAOptionThankyouPage = (int)$this->T2FAOption['thankyouPage']; 
        $T2FAThankyouPage =  get_permalink($T2FAOptionThankyouPage);
        $arrCountry = $wpdb->get_results( "SELECT * FROM $tableCountry order by name asc", OBJECT );
        ob_start();
        $randSession = substr(md5(microtime()),rand(0,26),32);
        
        ?>
         <script type="text/javascript">
            function T2FAdownloadElementHandle(T2FADownloadFile){
                jQuery('#T2FAModal').modal('show');
                jQuery('#T2FAFileDownload').val(T2FADownloadFile);
                //jQuery('#T2FARequestCode').trigger('click');
                return false;
            }
            jQuery(document).ready(function(){
                    jQuery('#T2FARequestCode').click(function(){
                        var ranSession = jQuery('#T2FASession').val();
                        var countryCode = jQuery('#T2FAcountry').val();
                        var mobilePhone = jQuery('#T2FAmobile');
                        if(mobilePhone.val().length < 1){
                            mobilePhone.css('border','1px red solid');
                            return false;
                        }else {
                            jQuery.ajax({
                               url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                               type:'POST',
                               data:'requestCode=yes&sessionId&sessionId='+ranSession+'&mobilePhone='+mobilePhone.val()+'&T2FACountry='+countryCode,
                               beforeSend:function(){
                                    jQuery("#T2FARequestCodeLoader").fadeIn('fast');
                                    jQuery('#sentCodeResult').removeClass('text-danger');
                                    jQuery('#sentCodeResult').removeClass('text-success');
                               },
                               success: function(result){
                                    jQuery("#T2FARequestCodeLoader").fadeOut('fast');
                                    if(result == 'SUCCESS'){
                                        jQuery('#sentCodeResult').addClass('text-success');
                                        jQuery('#sentCodeResult').html('<?=T2FA_successTokenSending?>');
                                    }else if(result== 'WRONG_FORMAT'){
                                        jQuery('#sentCodeResult').addClass('text-danger');
                                        jQuery('#sentCodeResult').html('<?= T2FA_phoneFormatWrong?>');
                                    }else {
                                        jQuery('#sentCodeResult').addClass('text-danger');
                                        jQuery('#sentCodeResult').html(result);
                                    }
                                }
                    		});
                        }
                       });
                    jQuery('#T2FASMSVisitorForm').submit(function(){
                        var fileDownload = jQuery('#T2FAFileDownload').val();
                        jQuery.ajax({
                               url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                               type:'POST',
                               data:jQuery(this).serialize(),
                               beforeSend:function(){
                                jQuery("#T2FASubmitCodeLoader").fadeIn('fast');
                                jQuery('#T2FASMSVisitorSubmitResult').fadeOut('fast');
                               },
                               success: function(result){
                                    jQuery("#T2FASubmitCodeLoader").fadeOut('fast');
                                    if(result == 'SUCCESS'){
                                        jQuery.ajax({
                                            url: '<?= get_site_url(); ?>?T2Fsuccess=yes',
                                           type:'GET',
                                           success:function(){
                                               location.reload();
                                           },
                                         });
                                    }else {
                                        jQuery('#T2FASMSVisitorSubmitResult').html('sorry you have entered wrong code');
                                        jQuery('#T2FASMSVisitorSubmitResult').fadeIn('fast');
                                    }
                                }
                    		});
                            return false;
                    });
                 
             })
             </script>
            <div class="modal fade" id="T2FAModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                      <!--form Visitor-->
                    <form name="T2FASMSVisitorForm" id="T2FASMSVisitorForm" />
                        <div class="modal-header alert alert-info">
                        <i  class="fa fa-shield fa-lg"> </i> 
                            <span style="font-size:larger;font-weight: bold;"> Transmit  SMS Confirmation</span> 
                        </div>
                        <div class="modal-body"  style="padding-bottom:450px">
                         <div class="form-group alert alert-danger" id="T2FASMSVisitorSubmitResult" style="display: none;"> </div>
                            <div class="form-group  col-lg-12">
                            <label class="col-sm-2 control-label" for="T2FAname">Name :</label>
                            <div class="col-sm-10">
                              <input type="text" placeholder="your name"  name="T2FAname" id="T2FAname" required="" class="form-control">
                            </div>
                          </div>
                            <div class="form-group col-lg-12">
                            <label class="col-sm-2 control-label" for="T2FAemail">Email :</label>
                            <div class="col-sm-10">
                              <input type="text" placeholder="your email"  name="T2FAemail" id="T2FAemail" required="" class="form-control">
                            </div>
                          </div>
                           <div class="form-group col-lg-12">
                            <label class="col-sm-2 control-label" for="T2FAemail" style="font-size: 12px;">Country :</label>
                            <div class="col-sm-10">
                                <select name="T2FAcountry" id="T2FAcountry" class="form-control">
                                     <?PHP foreach($arrCountry as $keyCountry=>$listcountry){
                                            ?>
                                                <option value="<?=$listcountry->iso?>"><?=$listcountry->name?></option>
                                            <?PHP                                
                                        }
                                    ?>
                                </select>
                              </div>
                          </div>
                          
                            <div class="form-group  col-lg-12">
                            <label class="col-sm-2 control-label" for="T2FAmobile">Mobile :</label>
                            <div class="col-sm-10">
                              <input type="text" placeholder="your mobile number"  name="T2FAmobile" id="T2FAmobile" required="" class="form-control">
                               <button type="button" class="btn btn-success" id="T2FARequestCode" style="margin-top: 5px;"><i class="fa fa-mobile"></i> &nbsp;REQUEST CODE</button>
                                <img id="T2FARequestCodeLoader" style="display: none;" src="<?php echo T2FA_PLUGIN_URL ?>/assets/images/loading.gif" title="still loading" > 
                                <span id="sentCodeResult" style="font-size: 10px;"></span>
                            </div>
                          </div>
                            <div class="form-group  col-lg-12">
                            <label class="col-sm-2 control-label" for="T2FAmobile">Code :</label>
                            <div class="col-sm-10">
                              <input type="text" placeholder="enter code"  name="T2FACode" id="T2FACode" required="" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer alert alert-default">
                            <input type="hidden" name="T2FAFileDownload" id="T2FAFileDownload" />
                              <input type="hidden" value="yes" name="T2FAsubmitCode" id="T2FAsubmitCode" />
                            <input type="hidden" name="T2FASession" id="T2FASession" value="<?=$randSession;?>" />
                            <img id="T2FASubmitCodeLoader" style="display: none;" src="<?php echo T2FA_PLUGIN_URL ?>/assets/images/loading.gif" title="still loading" > 
                            <!--
                            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times fa-lg"></i> CLOSE</button>
                            -->
                            <button type="submit"  class="btn btn-primary"><i class="fa fa-arrow-circle-o-right fa-lg"></i> SUBMIT</button>
                    </div>
                </form>
                
                
            </div>
          </div>
         </div>
         <?PHP
          $T2FAExtraCode = ob_get_contents();
          ob_end_clean();  
          echo $T2FAExtraCode;
         return;
    }
  
}
