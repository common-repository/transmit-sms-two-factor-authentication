=== Transmit SMS Two Factor Authentication ===
Contributors: Transmit SMS Team
Donate link: 
Plugin URI:https://wordpress.org/plugins/transmit-sms-two-factor-authentication/
Tags: SMS, Notifications, Order Confirmations, Delivery Notifications, Text Message Notifications, Text Message Alerts , Burst SMS
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Authentication system using SMS validation

== Description ==

Controls access to for downloading file in your site by sending a code to visitor mobile phone when they try tp download file.

You need a [Burst SMS Account](http://burstsms.com.au/) and some Burst SMS credit to use this plugin.

== Installation ==

1. Upload the 'transmit2FA' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Klik menu Transmit SMS > 2FA Transmit SMS.
3. Enter your Burst SMS API key and secret in the Settings tab.
4. Set your options for Two-Factor Authentication

== Frequently asked questions ==

= What is a Burst SMS API key? = 

To send SMS you will need to sign up for a BURST SMS account
and purchase some SMS credit. When you sign up you'll be given an API key.

= What format should the mobile number be in? =

All mobile numbers Format would accepted, you can entered with in international format or local format, but remember to choose right country.

== Screenshots ==
1. http://burst360.com.au/wp-content/uploads/T2faScreenshot/backend.jpg
2. http://burst360.com.au/wp-content/uploads/T2faScreenshot/frontend.jpg

== Changelog ==
= 1.0 =
* Basic code development
= 1.1 =
* Adding mobile number validation with country code
* Adding data country ISO  
= 1.2 =
* Automatically create BurstSMS List 'call Wordpress 2FA'
* Save downloaded visitor to Wordpress 2FA List
= 1.3 =
* Fixing messed popup. 
* Add feature to detecting new latest version release.
* Update backend design, change bootstrap.css to prevent conflicted style.
= 1.4 =
* Adding feature user can define type of file who using 2FA
= 1.5 =
* Shange whole system now 2FA will bloking page with contain shortcode
* Using shortcode to define with page will access using 2FA

== Upgrade notice ==
Latest stable version is 1.5, please upgrade to version 1.5