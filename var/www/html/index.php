<?php 
	
$VERSION = 1.00;

// Save data if form submitted
if ($_POST){ 
	
	// Set a default URL if no URL provided
	if ( ! $_POST['private_url']) {
		$_POST['private_url'] = 'http://localhost/loading.html';
	}

    if ( ! $_POST['ssid']) {
        $result = array(
                        'status'=>'danger',
                        'message'=>'Please select a wifi network'
                        );
    } else {
        if (file_put_contents('/var/www/config.json', json_encode($_POST))) {
            $result = array(
                'status'=>'success',
                'message'=>'Settings saved! Your Royalbox will restart momentarily...!  Once your TV shows connected click here to start watching <a href="http://royalbox.local/index.html">Go to Channels</a>'
            );
        }
        else {
            $result = array(
                'status'=>'danger',
                'message'=>'Unable to save settings'
            );
        }


        // Build wpa_supplicant
        $wpa = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n".
        "update_config=1\n".
        "country=US\n\n".

        "network={\n".
        "\tssid=\"$_POST[ssid]\"\n";

        if ($_POST['security_type'] == 'WPA2') {
            $wpa .= "\tkey_mgmt=WPA-PSK\n" .
                "\tpsk=\"$_POST[password]\"\n";
        }
        if ($_POST['security_type'] == 'WPA2-Ent') {
            $wpa .= "\tkey_mgmt=WPA-EAP\n" .
                "\tproto=RSN\n" .
                "\tpairwise=CCMP TKIP\n" .
                "\tgroup=CCMP TKIP\n" .
                "\tidentity=\"$_POST[identity]\"\n" .
                "\tpassword=\"$_POST[password]\"\n" .
                "\tphase1=\"peaplabel=0\"\n" .
                "\tphase2=\"auth=MSCHAPV2\"\n";
        }
        elseif ($_POST['security_type'] == 'WEP') {
            $wpa .= "\tkey_mgmt=NONE\n";
            // if password is hex, no quotes around string
            if (ctype_xdigit($_POST['password'])) {
                $wpa .= "\twep_key0=$_POST[password]\n";
            }
            else {
                $wpa .= "\twep_key0=\"$_POST[password]\"\n";
            }
        }
        elseif ($_POST['security_type'] == 'None') {
            $wpa .= "\tkey_mgmt=NONE\n";
        }

        $wpa .= "}\n";

        file_put_contents('/etc/wpa_supplicant/wpa_supplicant.conf', $wpa);

        // Save display rotate
        $config = "disable_overscan=1\n".
            "framebuffer_depth=24\n".
            "gpu_mem=192\n".
            "hdmi_force_hotplug=1\n".
            "dtparam=audio=on\n".
            "display_rotate=" . $_POST['screen_orientation'] . "\n";

        if ($_POST['resolution']) {
            $config .= 	"hdmi_group=2\n".
                "hdmi_mode=".$_POST['resolution']."\n";
        }
        if ($_POST['resolution'] == 87) {
            $config .= 	"hdmi_ignore_edid=0xa5000080\n".
                "hdmi_cvt=".$_POST['resolution_w'].' ' . $_POST['resolution_h'] . ' ' . $_POST['resolution_r'] . "\n";
        }



        shell_exec('sudo /bin/bash -c "echo -e \''.$config.'\' > /boot/config.txt" && sync');


        // Save private url
        $url = str_replace('/', '\/', $_POST['private_url']);


        exec("/bin/sed -i 's/kiosk.*$/kiosk $url/g' /home/pi/.config/lxsession/LXDE-pi/autostart");

        // Save timezone
        echo shell_exec("sudo /usr/bin/timedatectl set-timezone $_POST[timezone]");

        // Save display on/off
        if ($_POST['display_on'] < 0 || $_POST['display_off'] < 0) {
            file_put_contents('/var/www/hdmi', '');
        }
        else {
            $display = "# Turn display off\n" .
            "0 $_POST[display_off] * * * root /home/pi/rpi-hdmi.sh off\n\n" .

            "# Turn display on\n" .
            "0 $_POST[display_on] * * * root /home/pi/rpi-hdmi.sh on \n";

            file_put_contents('/var/www/hdmi', $display);
        }
        echo shell_exec("sudo /home/pi/scripts/connect_to_wifi_royalbox.sh &");
    }
} 

// Get existing config data
$data = json_decode(file_get_contents('/var/www/config.json'), true);

$serial_num = file_get_contents('/var/www/serial_num.txt');

// Scan for nearby wifi networks
$networks = shell_exec('sudo iwlist wlan0 scanning');

$networks = explode('Cell ', $networks);

foreach ($networks as $network) {
	
	$matches = array();
	$essid = preg_match_all('/Quality=([0-9]*\/[0-9]*)|Encryption key:([a-z].*)|ESSID:"(.*)"/', $network, $matches);
	
	// if essid
	if ($matches[3][2] && substr($matches[3][2], 0 ,4) != '\x00') {
	  $network_arr[$matches[3][2]] = array(
		'ssid'=>$matches[3][2],
		'encryption'=>$matches[2][1],
		'quality'=>$matches[1][0]
	  );
	}
}

// Get on/off status of monitor
$screen_status = (stristr(shell_exec('sudo /usr/bin/tvservice -s'), 'TV is off') !== false) ? 0 : 1;
 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Royalbox Settings</title>
    <meta name="apple-itunes-app" content="app-id=1450861330">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
    
    
    <style>
	    body {
		    font-family: 'Roboto', 'Arial', sans-serif;
		    background: #f3f2f0;
	    }
	    a, h4, .content-group h4 {
		    color: #e6894c;
	    }
	    .btn-primary {
		    border: 0;
		    background: #e6894b;
		}
		.btn-primary:hover {
		    background: #ed6b1d;
		    color: #fff;
		}
		.cover {
		  padding: 0 20px;
		}
		.cover .btn-lg {
		  padding: 10px 20px;
		  font-weight: bold;
		}
		
		@media (min-width: 768px) {
		  .cover-container {
		    width: 100%; /* Must be percentage or pixels for horizontal alignment */
		  }
		}
		
		@media (min-width: 992px) {
		  .cover-container {
		    width: 950px;
		  }
		}
		
		.breadcrumb {
		    font-size: 18px;
		    background: none;
		    padding: 10px 0 20px 0;
		    border-bottom: 2px solid #ddd;
		    border-radius: 0;
		    
		}
		.content-group {
		    padding: 10px 15px;
		    background: #ffffff;
		    border-radius: 2px;
		    margin-bottom: 1.5em;
		    box-shadow: 0px 1px 2px rgba(0,0,0,.1);
		}
		
		.content-group h4 {
			margin-top: 0;
			padding-bottom: 1em;
		}
		
		/**
		 * Toggles
		 * ---------------------------------------- */
		.toggle {
		  position: absolute;
		  margin-left: -9999px;
		  visibility: hidden;
		}
		.toggle + label {
		  display: inline-block;
		  position: relative;
		  cursor: pointer;
		  outline: none;
		  user-select: none;
		  overflow: hidden;
		  color: transparent;
		}
		input.toggle-default + label {
		  padding: 2px;
		  width: 60px;
		  height: 25px;
		  background-color: #b2bdc5;
		  border-radius: 30px;
		  transition: background 0.2s;
		  /*font-family: 'Glyphicons Halflings';*/
		  text-align: left;
		
		}
		input.toggle-default + label:before,
		input.toggle-default + label:after {
		  display: block;
		  position: absolute;
		  content: "";
		}
		input.toggle-default + label:before {
		  top: 2px;
		  left: 2px;
		  bottom: 2px;
		  right: 2px;
		  background-color: #b1bdc5;
		  border-radius: 30px;
		  transition: background 0.1s;
		  /*content: "\e014";*/
		  content: "OFF";
		  color: #fff;
		  padding-left: 26px;
		  padding-top: 2px;
		  font-size: 12px;
		}
		input.toggle-default:checked + label:before {
			background-color: #e6894b;
			/*content: "\e013";*/
			content: "ON";
			color: #fff;
			padding-left: 10px;
		}
		input.toggle-default + label:after {
		  top: 4px;
		  left: 5px;
		  bottom: 4px;
		  width: 18px;
		  background-color: #fff;
		  border-radius: 20px;
		  transition: margin 0.1s, background 0.1s;
		}
		input.toggle-default:checked + label {
		  background-color: #e6894b;
		}
		input.toggle-default:checked + label:after {
		  margin-left: 32px;
		  background-color: #fff;
		}
		.toggle-label {
			display: inline-block;
			vertical-align: top; 
			padding-top: 2px; 
			padding-right: 5px; 
		}
		</style>
  </head>

  <body>
   <!-- <div class="site-wrapper">
      <div class="site-wrapper-inner">
        <div class="cover-container">
	
          <div class="inner cover">
          -->  
         <div class="container">

            <?php if ($result) { ?>
                <div class="alert alert-<?php echo $result['status'] ?>"><?php echo $result['message'] ?></div>
                <?php if ($result['status'] === 'success') {
                    system("(sleep 5 ; sudo /sbin/reboot ) > /dev/null 2>&1 & echo $!");
                  }
            } ?>
            
            <form method="post" action="?" class="text-left form-horizontal">
	          	
	        <div class="content-group">
	          	<h4>Networking</h4>
			  	<div class="form-group">
				    <label for="inputEmail3" class="control-label col-sm-2 ">Name/SSID</label>
				    <div class="col-sm-10">
				    <input type="text" 
				    	class="form-control" 
				    	id="ssid" 
				    	name="ssid" 
				    	placeholder="Wifi network name/SSID" 
				    	value="<?php echo $data['ssid'] ?>" 
				    	style="<?php if (is_array($network_arr)) { echo 'display: none;'; } ?>">
				    	
				    <?php if (is_array($network_arr)) {
						//echo '<select name="ssid" class="form-control">';
						?>
						<div class="btn-group" id="wifi-network-dropdown">
	  					  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    						<span id="wifi-network-selected"><?php echo (($data['ssid']) ? $data['ssid'] : 'Select ...') ?></span> <span class="caret"></span>
	  					  </button>
	  					  <ul class="dropdown-menu">
							<?php 
							foreach ($network_arr as $ssid=>$details) {
							  //echo '<option value="'.$ssid.'" '.(($data['ssid'] == $ssid) ? 'selected' : '').'>'.
							  //  $ssid. (($details['encryption'] == 'off') ? ' [unsecured]' : '').'</option>';
							echo '<li class="wifi-network '.(($data['ssid'] == $ssid) ? 'active' : '').'"><a href="#" value="'.$ssid.'">'.
								$ssid . (($details['encryption'] == 'off') ? ' [unsecure]' : '') . '</a></li>';
							}
							?>
							<li role="separator" class="divider"></li>
		    					<li><a href="#" id="join-other-network">Join Other Network...</a></li>
							</ul>
						</div>
						<?php 
						//echo '</select>';
				    } ?>    
				</div>
				</div>
	       
				
				<div class="form-group">
				    <label for="inputEmail3" class="control-label col-sm-2">Security Type</label>
				    <div class="col-sm-10">
				    <select class="form-control" id="security_type" name="security_type">
					    <option value="WPA2" <?php if ($data['security_type'] == 'WPA2') echo 'selected' ?>>WPA2</option>
					    <option value="WPA2-Ent" <?php if ($data['security_type'] == 'WPA2-Ent') echo 'selected' ?>>WPA2 Enterprise</option>
					    <!--<option value="WPA">WPA</option>-->
					    <option value="WEP" <?php if ($data['security_type'] == 'WEP') echo 'selected' ?>>WEP</option>
					    <option value="None" <?php if ($data['security_type'] == 'None') echo 'selected' ?>>None</option>
				    </select>
				    </div>
				</div>
				
				<div class="form-group" id="identity_group">
				    <label for="identity" class="control-label col-sm-2">Username/Identity</label>
				    <div class="col-sm-10">
				    <input type="text" class="form-control" id="identity" name="identity" placeholder="Username/identity" value="<?php echo $data['identity'] ?>">
				    </div>
				</div>
				
				<div class="form-group" id="password_group">
				    <label for="password" class="control-label col-sm-2">Password</label>
				    <div class="col-sm-10">
				    <input type="password" class="form-control" id="password" name="password" placeholder="Wifi password" value="<?php echo $data['password'] ?>">
				    <span class="help-block" style="margin-bottom:0"><label><input type="checkbox" id="show-wifi-pass" /> Show password</label></span>
				    </div>
				</div>
			</div><!-- /content-group -->
				
			
			<div class="content-group">
				<h4>Timezone</h4>
			  	<div class="form-group">
				    <label for="inputEmail3" class="control-label col-sm-2 ">Timezone</label>
				    <div class="col-sm-10">
				    	<select class="form-control" name="timezone">
							<?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
				      	foreach ($tzlist as $timezone) { ?>
					      	<option value="<?php echo $timezone ?>" <?php if ($data['timezone'] == $timezone) echo 'selected' ?>><?php echo $timezone ?></option>
				      	<?php } ?>
				    	</select>
				    </div>
				</div><br/>
			 </div><!-- /content-group -->
	      	
				
			<div class="content-group">
				<h4>Screen</h4>
				
				<div class="form-group">
				    <label for="time_format" class="col-sm-2 control-label">Orientation</label>
				    <div class="col-sm-10" style="margin-top: 5px;">
				      	<label><input type="radio" name="screen_orientation" value="1" <?php if ($data['screen_orientation'] == 1) echo 'checked' ?>> Portrait</label> (vertical)<br/>
						<label><input type="radio" name="screen_orientation" value="0" <?php if ($data['screen_orientation'] == 0) echo 'checked' ?>> Landscape</label> (horizontal)<br/>
						<label><input type="radio" name="screen_orientation" value="3" <?php if ($data['screen_orientation'] == 3) echo 'checked' ?>> Portrait Flipped </label> (270 deg)<br/>
						<label><input type="radio" name="screen_orientation" value="2" <?php if ($data['screen_orientation'] == 2) echo 'checked' ?>> Landscape Flipped </label> (180 deg)
					</div>
				</div>
				
			  	<div class="form-group">
				    <label for="resolution" class="control-label col-sm-2 ">Resolution</label>
				    <div class="col-sm-10">
				    	<select class="form-control" name="resolution" id="resolution">
							<?php $res = array(0=>'Auto', 82=>'1080p', 83=>'900p', 85=>'720p',87=>'Custom');
				      	foreach ($res as $res_id=>$resolution) { ?>
					      	<option value="<?php echo $res_id ?>" <?php if ($data['resolution'] == $res_id) echo 'selected' ?>><?php echo $resolution ?></option>
				      	<?php } ?>
				    	</select>
				    	<div id="resolution_details" style="margin-top: 10px;">
					    	<div class="row">
						    	<div class="col-sm-4">
							    	<input name="resolution_w" class="form-control" value="<?php echo $data['resolution_w'] ?>">
							    	<span class="help-block">Width (in pixels)</span>
						    	</div>
						    	<div class="col-sm-4">
							    	<input name="resolution_h" class="form-control" value="<?php echo $data['resolution_h'] ?>">
							    	<span class="help-block">Height (in pixels)</span>
						    	</div>
						    	<div class="col-sm-4">
							    	<select class="form-control" name="resolution_r" id="resolution_r">
										<?php $res = array(60,30,15);
							      	foreach ($res as $rate) { ?>
								      	<option value="<?php echo $rate ?>" <?php if ($data['resolution_r'] == $rate) echo 'selected' ?>><?php echo $rate ?></option>
							      	<?php } ?>
							    	</select>
							    	<span class="help-block">Refresh Rate (FPS)</span>
						    	</div>
					    	</div>
				    	</div>
				    </div>
				</div>
				
				<div class="form-group">
				    <label for="inputEmail3" class="control-label col-sm-2">Sleep Schedule</label>
				    
				    <div class="col-sm-5">
				    <select class="form-control" id="display_off" name="display_off">
					    <option value="-1" <?php if ($data['display_off'] == -1) echo 'selected' ?>>Always on</option>
					    <?php for ($i=0; $i<24; $i++) { ?>
					    	<option value="<?php echo $i ?>" <?php if ($data['display_off'] == $i) echo 'selected' ?>>
					    		Off at <?php if ($i < 10) echo '0' ?><?php echo $i ?>:00 (<?php echo ($i % 12 == 0) ? '12' : ($i % 12) ?>:00<?php echo ($i < 12) ? 'AM' : 'PM' ?>)
					    	</option>
					    <?php } ?>
				    </select>
				    <span class="help-block">Turn screen <strong>off</strong></span>
				    </div>
				    
				    <div class="col-sm-5">
				    <select class="form-control" id="display_on" name="display_on">
					    <option value="-1" <?php if ($data['display_on'] == -1) echo 'selected' ?>>Always on</option>
					    <?php for ($i=0; $i<24; $i++) { ?>
					    	<option value="<?php echo $i ?>" <?php if ($data['display_on'] == $i) echo 'selected' ?>>
					    		On at <?php if ($i < 10) echo '0' ?><?php echo $i ?>:00 (<?php echo ($i % 12 == 0) ? '12' : ($i % 12) ?>:00<?php echo ($i < 12) ? 'AM' : 'PM' ?>)
					    	</option>
					    <?php } ?>
				    </select>
				    <span class="help-block">Turn screen <strong>on</strong></span>
				    </div>
				</div>
			 </div><!-- /content-group -->
			 
			<div class="content-group">
				<h4>Royalbox Dashboard URL</h4>
			  	<div class="form-group">
				    <label for="inputEmail3" class="control-label col-sm-2 ">URL</label>
				    <div class="col-sm-10">
					    <a href="#" id="edit-private-url-btn" style="margin-top: 7px; display: inline-block; <?php if($data['private_url'] == 'http://localhost/loading.html') echo 'display: none;' ?>">Click to change</a>
					    <div id="edit-private-url" <?php if($data['private_url'] != 'http://localhost/loading.html') echo 'style="display: none;"' ?>>
						    <input type="text" class="form-control" id="inputEmail3" name="private_url" placeholder="" value="<?php echo $data['private_url'] ?>">
						    <span class="help-block">You can use the default dashboard or update it with your own custom dashboard URL and it displayed on your TV.</span>
					    </div>
				    </div>
				</div>
			</div> <!-- /content-group -->

			
			  <div class="form-group">
			    <div class="col-sm-4">
			      <button type="submit" class="btn btn-primary form-control" id="submit-btn">Save Changes</button>
			    </div>
			  </div>
	
			</form>
            
            <br/><br/>
            <div class="text-center" style="color: #aaa"><small>Royalbox vers. <?php echo $VERSION ?>&nbsp;&nbsp;&nbsp;<span style="color: #ddd">|</span>&nbsp;&nbsp;&nbsp;S/N <?php echo $serial_num ?></small></div>
            <br/><br/>
  </div>
          <!--</div>  

        </div>
	  </div>
	</div>
-->
    
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
   
   <script>
   $(function() {
	  
	   $('.wifi-network a').on('click', function() {
		  $('#wifi-network-selected').text($(this).attr('value'));
		  $('#ssid').val($(this).attr('value')); 
		  $('#password').val('').focus();
	   });
	   
	   $('#join-other-network').on('click', function() {
		   $('#wifi-network-dropdown').hide();
		   $('#ssid').show().val('').focus();
	   });
	   
	   // Turn screen on/off
	   $('#screen_status').on('click', function() {
		   var action = ($(this).is(':checked')) ? 'on' : 'off';
					   
		   $.ajax({
			  method: "POST",
			  url: "api.php",
			  data: { method: "tvservice", action: action }
			});
	   })
	   
	   $('#edit-private-url-btn').on('click', function() {
		   $(this).hide();
		   $('#edit-private-url').show().find('input').focus();
		   return false;
	   });
	   
	   $('#show-wifi-pass').on('click', function() {
		  	 $('#password').attr('type', 'text');
	   });
	   
	   $('#security_type').on('change', function() {
		 	//console.log($('#security_type :selected').val());
		 	if ($('#security_type :selected').val() == 'None') {
			 	$('#password_group').hide();
			 	$('#identity_group').hide();
		 	}  
		 	else if ($('#security_type :selected').val() == 'WPA2-Ent') {
			 	$('#password_group').show();
			 	$('#identity_group').show();
		 	}  
		 	else {
			 	$('#password_group').show();
			 	$('#identity_group').hide();
		 	}
	   });
	   
	   $('#security_type').change();
	   
	   $('#submit-btn').on('click', function() {
		   $(this).text('Saving...').removeClass('btn-primary').addClass('btn-default');
	   })
	   
	   $('#resolution').on('change', function() {
		   var res = $('#resolution :selected').val();
		   
		   if (res == 87) {
			   $('#resolution_details').show();
		   }
		   else {
			   $('#resolution_details').hide();
		   }
	   });
	   
	   $('#resolution').change();
   })
   </script>
  </body>
</html>
