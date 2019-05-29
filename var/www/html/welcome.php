<?php 
// Check connection	
$is_connected = (exec('fping 8.8.8.8') == '8.8.8.8 is alive') ? true : false; 

// Get IP from ifconfig

exec('/sbin/ifconfig 2>&1', $output);
		
	$interfaces = array();
	foreach (preg_split("/\n\n/", implode("\n", $output)) as $int) {
	
	    preg_match("/^([A-z]*\d)\s+Link\s+encap:([A-z]*)\s+HWaddr\s+([A-z0-9:]*).*" .
	            "inet addr:([0-9.]+).*Bcast:([0-9.]+).*Mask:([0-9.]+).*" .
	            "MTU:([0-9.]+).*Metric:([0-9.]+).*" .
	            "RX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*frame:([0-9.]+).*" .
	            "TX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*carrier:([0-9.]+).*" .
	            "RX bytes:([0-9.]+).*\((.*)\).*TX bytes:([0-9.]+).*\((.*)\)" .
	            "/ims", $int, $regex);
	
	    if (!empty($regex)) {
	
	        $interface = array();
	        $interface['ip'] = $regex[4];
	    }
	}

// Get hostname
exec('cat /etc/hostname 2>&1', $hostname);

$ip_addr = shell_exec('hostname -I');

// Get existing config data
$data = json_decode(file_get_contents('/var/www/config.json'), true);
$serial_num = trim(file_get_contents('/var/www/serial_num.txt'));
//$is_connected=1;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="3" />
    
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Royalbox Status</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href='//fonts.googleapis.com/css?family=Roboto:100,400,300,500' rel='stylesheet' type='text/css'>
	
    <!-- Custom styles for this template -->
    <link href="css/style.css?9" rel="stylesheet">


	<style>li { line-height: 1.4em; padding-bottom: 15px; }</style>
	<script src="js/jquery-1.12.4.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
  </head>

  <body>

    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="inner cover">
	          <br/><br/>
	          
	          <img src='/img/royalbox-logo-color-horiz-light.png' style="width: 400px" alt="Royalbox Logo"><br/><br/>
	          
	          <br/><br/>
	          
	                  <div  <?php echo ( ! $is_connected && $data['ssid'] != '') ? '' : 'style="display:none"' ?>>
				  <img src='/img/warning.png' style="max-width:60px; opacity:.8">
				  
				  <h3>Waiting for Wifi Network</h3><br/><br/> 
			  </div>
			  <div <?php echo ($is_connected) ? 'style="display:none"' : '' ?>>
				  
				  <img src="/img/step1.png" style="max-width:200px;"><br/><br/><br/><br/>
				  
				  <div class="text-left lead" style="line-height: 2em;">
			      <ol>
				      <li>Please connect a computer, phone or tablet to the wifi network:<br/>
				      &nbsp;&nbsp;&nbsp;SSID: <code>royalbox-wifi</code><br/>
				      <li>Open a web browser, and enter the URL <code>http://royalbox.local:6969</code> into the address bar</li>
				      <li>Enter your home or office wifi credentials, then scroll down and click "Save Changes"</li>
				      <li>Your Royalbox will restart and you will be ready to start streaming!</li>
			      </ol>
				  </div>
			  </div>
	          
	          <div  <?php echo (!$is_connected) ? 'style="display:none"' : '' ?>>
		          
		          <img src="/img/step2.png" style="max-width:200px;"><br/><br/><br/><br/>
		          
		         
		          <div class="lead" style="line-height: 2em;">
			      Congratulations, your Royalbox is ready to go! <br/><br/>
		          </div>
		          Your screen will be loaded momentarily
		          <br/><br/>
		          <!--Problems? Questions? Comments? Contact us at <code>contact@royalbox.tv</code>-->
	          </div>

          </div>
          
          <div class="mastfoot">
            <div class="inner">
              <p>Visit <a href="#">https://royalbox.tv</a> for more info. IP Address: <a href="https://<?php echo $ip_addr ?>"><?php echo $ip_addr ?></a></p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <script>
	$(function() {
		<?php if ($is_connected) { ?>
		setTimeout(function(){
			//window.location="<?php echo $data['private_url'] ?>"
			var completeURL = "http:\/\/localhost/loading.html" + "?private_url=<?php echo $data['private_url'] ?>"
			//var completeURL = "http:\/\/localhost/loading.html"
			window.location=completeURL
		}, 0);
		<?php } ?>
	});
	</script>

  </body>
</html>
