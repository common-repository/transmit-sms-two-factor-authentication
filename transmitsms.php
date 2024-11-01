<?php
/*
Plugin Name: Transmit SMS Two Factor Authentication
Plugin URI: 
Description: Authentication system using SMS validation.
Version: 1.5
Author: 2FA  >> Transmit SMS
Author URI: 
*/
define( 'T2FA_VERSION', '1.5' );
define( 'T2FA_REQUIRED_WP_VERSION', '3.5' );
if ( ! defined( 'T2FA_PLUGIN_BASENAME' ) )
	define( 'T2FA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'T2FA_PLUGIN_NAME' ) )
	define( 'T2FA_PLUGIN_NAME', trim( dirname(T2FA_PLUGIN_BASENAME), '/' ));
if ( ! defined( 'T2FA_PLUGIN_DIR' ) )
	define( 'T2FA_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
if ( ! defined( 'T2FA_PLUGIN_URL' ) )
	define( 'T2FA_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
if ( ! defined( 'T2FA_PLUGIN_MODULES_DIR' ) )
	define( 'T2FA_PLUGIN_MODULES_DIR', T2FA_PLUGIN_DIR . '/modules' );
if ( ! defined( 'T2FA_SHORTCODE' ) )
	define( 'T2FA_SHORTCODE', 'T2FA_SHORTCODE' );
    
if ( ! defined( 'TS2_LOAD_JS' ) )
	define( 'T2FA_LOAD_JS', true );

if ( ! defined( 'TS2_LOAD_CSS' ) )
	define( 'TS2_LOAD_CSS', true );

if ( ! defined( 'TS2_AUTOP' ) )
	define( 'T2FA_AUTOP', true );

if ( ! defined( 'T2FA_USE_PIPE' ) )
	define( 'T2FA_USE_PIPE', true );
    
    
if(!defined('T2FA_successVerify'))
    define('T2FA_successVerify',"Your key has been verified successfully");
if(!defined('T2FA_failVerify'))
    define('T2FA_failVerify',"Sorry..api key and secret you entered  still invalid");
if(!defined('T2FA_successSubmit'))
    define('T2FA_successSubmit',"Your configuration settings has been saved");
if(!defined('T2FA_failSubmit'))
    define('T2FA_failSubmit',"oops sorry something went wrong please try again later");
if(!defined('T2FA_phoneFormatWrong'))
    define('T2FA_phoneFormatWrong',"sorry you have entered wrong phone number");
define('T2FAWpOption','T2FASmsSettings'); 
define('T2FAWpOptionApi','T2FASmsSettingsApi');
define('T2FAWpOptionToken','T2FASmsSettingsToken');
define('T2FA_successTokenSending','confirmation code was sending to your mobile');
define('T2FA_BurstSMSList','Wordpress 2FA');
$T2FA_downloadFileType = array('zip','rar','pdf','doc','xlsx','docx','exe','jpg','png');
register_activation_hook(__FILE__, 'T2FActivatePlugin');
register_uninstall_hook(__FILE__, 'T2FAdeletePlugin');

require_once T2FA_PLUGIN_DIR . '/settings.php';

function T2FActivatePlugin(){
    global $wpdb;
    $tablefile = $wpdb->prefix . "T2FAtransmit_country"; 
    $tablefileCodeTable  = "CREATE TABLE IF NOT EXISTS $tablefile (
  iso CHAR(2) NOT NULL PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  printable_name VARCHAR(80) NOT NULL,
  iso3 CHAR(3),
  numcode SMALLINT);";
  
  $tablefileCodeTable .="
    INSERT INTO $tablefile VALUES ('AF','AFGHANISTAN','Afghanistan','AFG','004');
    INSERT INTO $tablefile VALUES ('AL','ALBANIA','Albania','ALB','008');
    INSERT INTO $tablefile VALUES ('DZ','ALGERIA','Algeria','DZA','012');
    INSERT INTO $tablefile VALUES ('AS','AMERICAN SAMOA','American Samoa','ASM','016');
    INSERT INTO $tablefile VALUES ('AD','ANDORRA','Andorra','AND','020');
    INSERT INTO $tablefile VALUES ('AO','ANGOLA','Angola','AGO','024');
    INSERT INTO $tablefile VALUES ('AI','ANGUILLA','Anguilla','AIA','660');
    INSERT INTO $tablefile VALUES ('AQ','ANTARCTICA','Antarctica',NULL,NULL);
    INSERT INTO $tablefile VALUES ('AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG','028');
    INSERT INTO $tablefile VALUES ('AR','ARGENTINA','Argentina','ARG','032');
    INSERT INTO $tablefile VALUES ('AM','ARMENIA','Armenia','ARM','051');
    INSERT INTO $tablefile VALUES ('AW','ARUBA','Aruba','ABW','533');
    INSERT INTO $tablefile VALUES ('AU','AUSTRALIA','Australia','AUS','036');
    INSERT INTO $tablefile VALUES ('AT','AUSTRIA','Austria','AUT','040');
    INSERT INTO $tablefile VALUES ('AZ','AZERBAIJAN','Azerbaijan','AZE','031');
    INSERT INTO $tablefile VALUES ('BS','BAHAMAS','Bahamas','BHS','044');
    INSERT INTO $tablefile VALUES ('BH','BAHRAIN','Bahrain','BHR','048');
    INSERT INTO $tablefile VALUES ('BD','BANGLADESH','Bangladesh','BGD','050');
    INSERT INTO $tablefile VALUES ('BB','BARBADOS','Barbados','BRB','052');
    INSERT INTO $tablefile VALUES ('BY','BELARUS','Belarus','BLR','112');
    INSERT INTO $tablefile VALUES ('BE','BELGIUM','Belgium','BEL','056');
    INSERT INTO $tablefile VALUES ('BZ','BELIZE','Belize','BLZ','084');
    INSERT INTO $tablefile VALUES ('BJ','BENIN','Benin','BEN','204');
    INSERT INTO $tablefile VALUES ('BM','BERMUDA','Bermuda','BMU','060');
    INSERT INTO $tablefile VALUES ('BT','BHUTAN','Bhutan','BTN','064');
    INSERT INTO $tablefile VALUES ('BO','BOLIVIA','Bolivia','BOL','068');
    INSERT INTO $tablefile VALUES ('BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH','070');
    INSERT INTO $tablefile VALUES ('BW','BOTSWANA','Botswana','BWA','072');
    INSERT INTO $tablefile VALUES ('BV','BOUVET ISLAND','Bouvet Island',NULL,NULL);
    INSERT INTO $tablefile VALUES ('BR','BRAZIL','Brazil','BRA','076');
    INSERT INTO $tablefile VALUES ('IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL);
    INSERT INTO $tablefile VALUES ('BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN','096');
    INSERT INTO $tablefile VALUES ('BG','BULGARIA','Bulgaria','BGR','100');
    INSERT INTO $tablefile VALUES ('BF','BURKINA FASO','Burkina Faso','BFA','854');
    INSERT INTO $tablefile VALUES ('BI','BURUNDI','Burundi','BDI','108');
    INSERT INTO $tablefile VALUES ('KH','CAMBODIA','Cambodia','KHM','116');
    INSERT INTO $tablefile VALUES ('CM','CAMEROON','Cameroon','CMR','120');
    INSERT INTO $tablefile VALUES ('CA','CANADA','Canada','CAN','124');
    INSERT INTO $tablefile VALUES ('CV','CAPE VERDE','Cape Verde','CPV','132');
    INSERT INTO $tablefile VALUES ('KY','CAYMAN ISLANDS','Cayman Islands','CYM','136');
    INSERT INTO $tablefile VALUES ('CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF','140');
    INSERT INTO $tablefile VALUES ('TD','CHAD','Chad','TCD','148');
    INSERT INTO $tablefile VALUES ('CL','CHILE','Chile','CHL','152');
    INSERT INTO $tablefile VALUES ('CN','CHINA','China','CHN','156');
    INSERT INTO $tablefile VALUES ('CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL);
    INSERT INTO $tablefile VALUES ('CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL);
    INSERT INTO $tablefile VALUES ('CO','COLOMBIA','Colombia','COL','170');
    INSERT INTO $tablefile VALUES ('KM','COMOROS','Comoros','COM','174');
    INSERT INTO $tablefile VALUES ('CG','CONGO','Congo','COG','178');
    INSERT INTO $tablefile VALUES ('CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD','180');
    INSERT INTO $tablefile VALUES ('CK','COOK ISLANDS','Cook Islands','COK','184');
    INSERT INTO $tablefile VALUES ('CR','COSTA RICA','Costa Rica','CRI','188');
    INSERT INTO $tablefile VALUES ('CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV','384');
    INSERT INTO $tablefile VALUES ('HR','CROATIA','Croatia','HRV','191');
    INSERT INTO $tablefile VALUES ('CU','CUBA','Cuba','CUB','192');
    INSERT INTO $tablefile VALUES ('CY','CYPRUS','Cyprus','CYP','196');
    INSERT INTO $tablefile VALUES ('CZ','CZECH REPUBLIC','Czech Republic','CZE','203');
    INSERT INTO $tablefile VALUES ('DK','DENMARK','Denmark','DNK','208');
    INSERT INTO $tablefile VALUES ('DJ','DJIBOUTI','Djibouti','DJI','262');
    INSERT INTO $tablefile VALUES ('DM','DOMINICA','Dominica','DMA','212');
    INSERT INTO $tablefile VALUES ('DO','DOMINICAN REPUBLIC','Dominican Republic','DOM','214');
    INSERT INTO $tablefile VALUES ('EC','ECUADOR','Ecuador','ECU','218');
    INSERT INTO $tablefile VALUES ('EG','EGYPT','Egypt','EGY','818');
    INSERT INTO $tablefile VALUES ('SV','EL SALVADOR','El Salvador','SLV','222');
    INSERT INTO $tablefile VALUES ('GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ','226');
    INSERT INTO $tablefile VALUES ('ER','ERITREA','Eritrea','ERI','232');
    INSERT INTO $tablefile VALUES ('EE','ESTONIA','Estonia','EST','233');
    INSERT INTO $tablefile VALUES ('ET','ETHIOPIA','Ethiopia','ETH','231');
    INSERT INTO $tablefile VALUES ('FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK','238');
    INSERT INTO $tablefile VALUES ('FO','FAROE ISLANDS','Faroe Islands','FRO','234');
    INSERT INTO $tablefile VALUES ('FJ','FIJI','Fiji','FJI','242');
    INSERT INTO $tablefile VALUES ('FI','FINLAND','Finland','FIN','246');
    INSERT INTO $tablefile VALUES ('FR','FRANCE','France','FRA','250');
    INSERT INTO $tablefile VALUES ('GF','FRENCH GUIANA','French Guiana','GUF','254');
    INSERT INTO $tablefile VALUES ('PF','FRENCH POLYNESIA','French Polynesia','PYF','258');
    INSERT INTO $tablefile VALUES ('TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL);
    INSERT INTO $tablefile VALUES ('GA','GABON','Gabon','GAB','266');
    INSERT INTO $tablefile VALUES ('GM','GAMBIA','Gambia','GMB','270');
    INSERT INTO $tablefile VALUES ('GE','GEORGIA','Georgia','GEO','268');
    INSERT INTO $tablefile VALUES ('DE','GERMANY','Germany','DEU','276');
    INSERT INTO $tablefile VALUES ('GH','GHANA','Ghana','GHA','288');
    INSERT INTO $tablefile VALUES ('GI','GIBRALTAR','Gibraltar','GIB','292');
    INSERT INTO $tablefile VALUES ('GR','GREECE','Greece','GRC','300');
    INSERT INTO $tablefile VALUES ('GL','GREENLAND','Greenland','GRL','304');
    INSERT INTO $tablefile VALUES ('GD','GRENADA','Grenada','GRD','308');
    INSERT INTO $tablefile VALUES ('GP','GUADELOUPE','Guadeloupe','GLP','312');
    INSERT INTO $tablefile VALUES ('GU','GUAM','Guam','GUM','316');
    INSERT INTO $tablefile VALUES ('GT','GUATEMALA','Guatemala','GTM','320');
    INSERT INTO $tablefile VALUES ('GN','GUINEA','Guinea','GIN','324');
    INSERT INTO $tablefile VALUES ('GW','GUINEA-BISSAU','Guinea-Bissau','GNB','624');
    INSERT INTO $tablefile VALUES ('GY','GUYANA','Guyana','GUY','328');
    INSERT INTO $tablefile VALUES ('HT','HAITI','Haiti','HTI','332');
    INSERT INTO $tablefile VALUES ('HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL);
    INSERT INTO $tablefile VALUES ('VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT','336');
    INSERT INTO $tablefile VALUES ('HN','HONDURAS','Honduras','HND','340');
    INSERT INTO $tablefile VALUES ('HK','HONG KONG','Hong Kong','HKG','344');
    INSERT INTO $tablefile VALUES ('HU','HUNGARY','Hungary','HUN','348');
    INSERT INTO $tablefile VALUES ('IS','ICELAND','Iceland','ISL','352');
    INSERT INTO $tablefile VALUES ('IN','INDIA','India','IND','356');
    INSERT INTO $tablefile VALUES ('ID','INDONESIA','Indonesia','IDN','360');
    INSERT INTO $tablefile VALUES ('IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN','364');
    INSERT INTO $tablefile VALUES ('IQ','IRAQ','Iraq','IRQ','368');
    INSERT INTO $tablefile VALUES ('IE','IRELAND','Ireland','IRL','372');
    INSERT INTO $tablefile VALUES ('IL','ISRAEL','Israel','ISR','376');
    INSERT INTO $tablefile VALUES ('IT','ITALY','Italy','ITA','380');
    INSERT INTO $tablefile VALUES ('JM','JAMAICA','Jamaica','JAM','388');
    INSERT INTO $tablefile VALUES ('JP','JAPAN','Japan','JPN','392');
    INSERT INTO $tablefile VALUES ('JO','JORDAN','Jordan','JOR','400');
    INSERT INTO $tablefile VALUES ('KZ','KAZAKHSTAN','Kazakhstan','KAZ','398');
    INSERT INTO $tablefile VALUES ('KE','KENYA','Kenya','KEN','404');
    INSERT INTO $tablefile VALUES ('KI','KIRIBATI','Kiribati','KIR','296');
    INSERT INTO $tablefile VALUES ('KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK','408');
    INSERT INTO $tablefile VALUES ('KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR','410');
    INSERT INTO $tablefile VALUES ('KW','KUWAIT','Kuwait','KWT','414');
    INSERT INTO $tablefile VALUES ('KG','KYRGYZSTAN','Kyrgyzstan','KGZ','417');
    INSERT INTO $tablefile VALUES ('LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO','418');
    INSERT INTO $tablefile VALUES ('LV','LATVIA','Latvia','LVA','428');
    INSERT INTO $tablefile VALUES ('LB','LEBANON','Lebanon','LBN','422');
    INSERT INTO $tablefile VALUES ('LS','LESOTHO','Lesotho','LSO','426');
    INSERT INTO $tablefile VALUES ('LR','LIBERIA','Liberia','LBR','430');
    INSERT INTO $tablefile VALUES ('LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY','434');
    INSERT INTO $tablefile VALUES ('LI','LIECHTENSTEIN','Liechtenstein','LIE','438');
    INSERT INTO $tablefile VALUES ('LT','LITHUANIA','Lithuania','LTU','440');
    INSERT INTO $tablefile VALUES ('LU','LUXEMBOURG','Luxembourg','LUX','442');
    INSERT INTO $tablefile VALUES ('MO','MACAO','Macao','MAC','446');
    INSERT INTO $tablefile VALUES ('MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD','807');
    INSERT INTO $tablefile VALUES ('MG','MADAGASCAR','Madagascar','MDG','450');
    INSERT INTO $tablefile VALUES ('MW','MALAWI','Malawi','MWI','454');
    INSERT INTO $tablefile VALUES ('MY','MALAYSIA','Malaysia','MYS','458');
    INSERT INTO $tablefile VALUES ('MV','MALDIVES','Maldives','MDV','462');
    INSERT INTO $tablefile VALUES ('ML','MALI','Mali','MLI','466');
    INSERT INTO $tablefile VALUES ('MT','MALTA','Malta','MLT','470');
    INSERT INTO $tablefile VALUES ('MH','MARSHALL ISLANDS','Marshall Islands','MHL','584');
    INSERT INTO $tablefile VALUES ('MQ','MARTINIQUE','Martinique','MTQ','474');
    INSERT INTO $tablefile VALUES ('MR','MAURITANIA','Mauritania','MRT','478');
    INSERT INTO $tablefile VALUES ('MU','MAURITIUS','Mauritius','MUS','480');
    INSERT INTO $tablefile VALUES ('YT','MAYOTTE','Mayotte',NULL,NULL);
    INSERT INTO $tablefile VALUES ('MX','MEXICO','Mexico','MEX','484');
    INSERT INTO $tablefile VALUES ('FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM','583');
    INSERT INTO $tablefile VALUES ('MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA','498');
    INSERT INTO $tablefile VALUES ('MC','MONACO','Monaco','MCO','492');
    INSERT INTO $tablefile VALUES ('MN','MONGOLIA','Mongolia','MNG','496');
    INSERT INTO $tablefile VALUES ('MS','MONTSERRAT','Montserrat','MSR','500');
    INSERT INTO $tablefile VALUES ('MA','MOROCCO','Morocco','MAR','504');
    INSERT INTO $tablefile VALUES ('MZ','MOZAMBIQUE','Mozambique','MOZ','508');
    INSERT INTO $tablefile VALUES ('MM','MYANMAR','Myanmar','MMR','104');
    INSERT INTO $tablefile VALUES ('NA','NAMIBIA','Namibia','NAM','516');
    INSERT INTO $tablefile VALUES ('NR','NAURU','Nauru','NRU','520');
    INSERT INTO $tablefile VALUES ('NP','NEPAL','Nepal','NPL','524');
    INSERT INTO $tablefile VALUES ('NL','NETHERLANDS','Netherlands','NLD','528');
    INSERT INTO $tablefile VALUES ('AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT','530');
    INSERT INTO $tablefile VALUES ('NC','NEW CALEDONIA','New Caledonia','NCL','540');
    INSERT INTO $tablefile VALUES ('NZ','NEW ZEALAND','New Zealand','NZL','554');
    INSERT INTO $tablefile VALUES ('NI','NICARAGUA','Nicaragua','NIC','558');
    INSERT INTO $tablefile VALUES ('NE','NIGER','Niger','NER','562');
    INSERT INTO $tablefile VALUES ('NG','NIGERIA','Nigeria','NGA','566');
    INSERT INTO $tablefile VALUES ('NU','NIUE','Niue','NIU','570');
    INSERT INTO $tablefile VALUES ('NF','NORFOLK ISLAND','Norfolk Island','NFK','574');
    INSERT INTO $tablefile VALUES ('MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP','580');
    INSERT INTO $tablefile VALUES ('NO','NORWAY','Norway','NOR','578');
    INSERT INTO $tablefile VALUES ('OM','OMAN','Oman','OMN','512');
    INSERT INTO $tablefile VALUES ('PK','PAKISTAN','Pakistan','PAK','586');
    INSERT INTO $tablefile VALUES ('PW','PALAU','Palau','PLW','585');
    INSERT INTO $tablefile VALUES ('PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL);
    INSERT INTO $tablefile VALUES ('PA','PANAMA','Panama','PAN','591');
    INSERT INTO $tablefile VALUES ('PG','PAPUA NEW GUINEA','Papua New Guinea','PNG','598');
    INSERT INTO $tablefile VALUES ('PY','PARAGUAY','Paraguay','PRY','600');
    INSERT INTO $tablefile VALUES ('PE','PERU','Peru','PER','604');
    INSERT INTO $tablefile VALUES ('PH','PHILIPPINES','Philippines','PHL','608');
    INSERT INTO $tablefile VALUES ('PN','PITCAIRN','Pitcairn','PCN','612');
    INSERT INTO $tablefile VALUES ('PL','POLAND','Poland','POL','616');
    INSERT INTO $tablefile VALUES ('PT','PORTUGAL','Portugal','PRT','620');
    INSERT INTO $tablefile VALUES ('PR','PUERTO RICO','Puerto Rico','PRI','630');
    INSERT INTO $tablefile VALUES ('QA','QATAR','Qatar','QAT','634');
    INSERT INTO $tablefile VALUES ('RE','REUNION','Reunion','REU','638');
    INSERT INTO $tablefile VALUES ('RO','ROMANIA','Romania','ROM','642');
    INSERT INTO $tablefile VALUES ('RU','RUSSIAN FEDERATION','Russian Federation','RUS','643');
    INSERT INTO $tablefile VALUES ('RW','RWANDA','Rwanda','RWA','646');
    INSERT INTO $tablefile VALUES ('SH','SAINT HELENA','Saint Helena','SHN','654');
    INSERT INTO $tablefile VALUES ('KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA','659');
    INSERT INTO $tablefile VALUES ('LC','SAINT LUCIA','Saint Lucia','LCA','662');
    INSERT INTO $tablefile VALUES ('PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM','666');
    INSERT INTO $tablefile VALUES ('VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT','670');
    INSERT INTO $tablefile VALUES ('WS','SAMOA','Samoa','WSM','882');
    INSERT INTO $tablefile VALUES ('SM','SAN MARINO','San Marino','SMR','674');
    INSERT INTO $tablefile VALUES ('ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP','678');
    INSERT INTO $tablefile VALUES ('SA','SAUDI ARABIA','Saudi Arabia','SAU','682');
    INSERT INTO $tablefile VALUES ('SN','SENEGAL','Senegal','SEN','686');
    INSERT INTO $tablefile VALUES ('CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL);
    INSERT INTO $tablefile VALUES ('SC','SEYCHELLES','Seychelles','SYC','690');
    INSERT INTO $tablefile VALUES ('SL','SIERRA LEONE','Sierra Leone','SLE','694');
    INSERT INTO $tablefile VALUES ('SG','SINGAPORE','Singapore','SGP','702');
    INSERT INTO $tablefile VALUES ('SK','SLOVAKIA','Slovakia','SVK','703');
    INSERT INTO $tablefile VALUES ('SI','SLOVENIA','Slovenia','SVN','705');
    INSERT INTO $tablefile VALUES ('SB','SOLOMON ISLANDS','Solomon Islands','SLB','090');
    INSERT INTO $tablefile VALUES ('SO','SOMALIA','Somalia','SOM','706');
    INSERT INTO $tablefile VALUES ('ZA','SOUTH AFRICA','South Africa','ZAF','710');
    INSERT INTO $tablefile VALUES ('GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL);
    INSERT INTO $tablefile VALUES ('ES','SPAIN','Spain','ESP','724');
    INSERT INTO $tablefile VALUES ('LK','SRI LANKA','Sri Lanka','LKA','144');
    INSERT INTO $tablefile VALUES ('SD','SUDAN','Sudan','SDN','736');
    INSERT INTO $tablefile VALUES ('SR','SURINAME','Suriname','SUR','740');
    INSERT INTO $tablefile VALUES ('SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM','744');
    INSERT INTO $tablefile VALUES ('SZ','SWAZILAND','Swaziland','SWZ','748');
    INSERT INTO $tablefile VALUES ('SE','SWEDEN','Sweden','SWE','752');
    INSERT INTO $tablefile VALUES ('CH','SWITZERLAND','Switzerland','CHE','756');
    INSERT INTO $tablefile VALUES ('SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR','760');
    INSERT INTO $tablefile VALUES ('TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN','158');
    INSERT INTO $tablefile VALUES ('TJ','TAJIKISTAN','Tajikistan','TJK','762');
    INSERT INTO $tablefile VALUES ('TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA','834');
    INSERT INTO $tablefile VALUES ('TH','THAILAND','Thailand','THA','764');
    INSERT INTO $tablefile VALUES ('TL','TIMOR-LESTE','Timor-Leste',NULL,NULL);
    INSERT INTO $tablefile VALUES ('TG','TOGO','Togo','TGO','768');
    INSERT INTO $tablefile VALUES ('TK','TOKELAU','Tokelau','TKL','772');
    INSERT INTO $tablefile VALUES ('TO','TONGA','Tonga','TON','776');
    INSERT INTO $tablefile VALUES ('TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO','780');
    INSERT INTO $tablefile VALUES ('TN','TUNISIA','Tunisia','TUN','788');
    INSERT INTO $tablefile VALUES ('TR','TURKEY','Turkey','TUR','792');
    INSERT INTO $tablefile VALUES ('TM','TURKMENISTAN','Turkmenistan','TKM','795');
    INSERT INTO $tablefile VALUES ('TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA','796');
    INSERT INTO $tablefile VALUES ('TV','TUVALU','Tuvalu','TUV','798');
    INSERT INTO $tablefile VALUES ('UG','UGANDA','Uganda','UGA','800');
    INSERT INTO $tablefile VALUES ('UA','UKRAINE','Ukraine','UKR','804');
    INSERT INTO $tablefile VALUES ('AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE','784');
    INSERT INTO $tablefile VALUES ('GB','UNITED KINGDOM','United Kingdom','GBR','826');
    INSERT INTO $tablefile VALUES ('US','UNITED STATES','United States','USA','840');
    INSERT INTO $tablefile VALUES ('UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL);
    INSERT INTO $tablefile VALUES ('UY','URUGUAY','Uruguay','URY','858');
    INSERT INTO $tablefile VALUES ('UZ','UZBEKISTAN','Uzbekistan','UZB','860');
    INSERT INTO $tablefile VALUES ('VU','VANUATU','Vanuatu','VUT','548');
    INSERT INTO $tablefile VALUES ('VE','VENEZUELA','Venezuela','VEN','862');
    INSERT INTO $tablefile VALUES ('VN','VIET NAM','Viet Nam','VNM','704');
    INSERT INTO $tablefile VALUES ('VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB','092');
    INSERT INTO $tablefile VALUES ('VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR','850');
    INSERT INTO $tablefile VALUES ('WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF','876');
    INSERT INTO $tablefile VALUES ('EH','WESTERN SAHARA','Western Sahara','ESH','732');
    INSERT INTO $tablefile VALUES ('YE','YEMEN','Yemen','YEM','887');
    INSERT INTO $tablefile VALUES ('ZM','ZAMBIA','Zambia','ZMB','894');
    INSERT INTO $tablefile VALUES ('ZW','ZIMBABWE','Zimbabwe','ZWE','716');"; 
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($tablefileCodeTable);
 }
 function T2FAdeletePlugin(){
    global $wpdb;
    delete_option('T2FASmsSettings');
    delete_option('T2FASmsSettingsApi');
    delete_option('T2FASmsSettingsToken');
    //drop table
    $table = $wpdb->prefix."T2FAtransmit_country";
    $wpdb->query("DROP TABLE IF EXISTS $table");
 }
 if ( 'plugins.php' === $pagenow )
{
    // Better update message
    $file   = basename( __FILE__ );
    $folder = basename( dirname( __FILE__ ) );
    $hook = "in_plugin_update_message-{$folder}/{$file}";
    add_action( $hook, 'T2FAUpdateMessageCb', 20, 2 );
}

function T2FAUpdateMessageCb( $plugin_data, $r )
{
    // readme contents
    $data       = file_get_contents( 'https://plugins.svn.wordpress.org/transmit-sms-share/trunk/readme.txt' );
    $arrUpgardeNoticeT2FA = explode('Upgrade notice',$data);
    $upgradeNoticeT2FA= trim($arrUpgardeNoticeT2FA[1]);
    $upgradeNoticeT2FA = substr($upgradeNoticeT2FA,2);
    if($separateString =  strpos($upgradeNoticeT2FA,'==') !== false){
        if($separateString > 5){
            $upgradeNoticeT2FA =  substr($upgradeNoticeT2FA,0,$separateString);
        }
    }
	$upgradeNoticeT2FA = str_replace('=','',$upgradeNoticeT2FA);
    $output = '<div style="margin-top:10px" class="alert alert-info"><i class="fa fa-info-circle fa-lg"></i> '.$upgradeNoticeT2FA.'</div>';
    return print $output;
}
 
?>
