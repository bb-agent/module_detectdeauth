<? 
/*
    Copyright (C) 2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];


function killRegex($regex){
	
	$exec = "ps aux|grep -E '$regex' | grep -v grep | awk '{print $2}'";
	exec($exec,$output);
	
	if (count($output) > 0) {
		$exec = "kill " . $output[0];
		exec_fruitywifi($exec);
	}	
}

function copyLogsHistory() {
	
	global $bin_cp;
	global $bin_mv;
	global $mod_logs;
	global $mod_logs_history;
	global $bin_echo;
	
	if ( 0 < filesize( $mod_logs ) ) {
		$exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
		exec_fruitywifi($exec);
		
		$exec = "$bin_echo '' > $mod_logs";
		//exec_fruitywifi($exec);
	}
}

if($service == "detectdeauth") {
	if ($action == "start") {
		
		$exec = "$bin_echo '' > $mod_logs";
		exec_fruitywifi($exec);
		
		$email_conf = "email.conf";
		$exec = "echo '[email]' > $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    from = $mod_detectdeauth_email_from' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    to = $mod_detectdeauth_email_to' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    server = $mod_detectdeauth_smtp_server' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    port = $mod_detectdeauth_smtp_port' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    user = $mod_detectdeauth_smtp_user' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    pass = $mod_detectdeauth_smtp_pass' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    auth = $mod_detectdeauth_smtp_auth' >> $email_conf";
		exec_fruitywifi($exec);
		$exec = "echo '    starttls = $mod_detectdeauth_smtp_starttls' >> $email_conf";
		exec_fruitywifi($exec);
		
		
		# OPTIONS
		if ($mod_detectdeauth_channel != "") $options_channel = "-c $mod_detectdeauth_channel";
		if ($mod_detectdeauth_alert == "1") $options_alert = "-a";
		if ($mod_detectdeauth_jump == "1") $options_jump = "-j";
		
		$exec = "python scan-deauth.py -i mon0 $options_channel $options_alert -d $mod_detectdeauth_alert_delay -l $mod_logs -n $mod_detectdeauth_number $options_jump > /dev/null 2 &";
		exec_fruitywifi($exec);
	
	} else if($action == "stop") {
	
		killRegex("scan-deauth");
		killRegex("scan-deauth");
		
		// LOGS COPY
		copyLogsHistory();
		
	}
}

if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_fruitywifi($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_fruitywifi($exec);

    header("Location: ../../install.php?module=$mod_name");
    exit;
}

if ($page == "status") {
    header("Location: ../../../action.php");
} else {
    header("Location: ../../action.php?page=$mod_name");
}

?>
