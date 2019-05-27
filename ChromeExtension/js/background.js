function stopNote() {
	chrome.notifications.clear('notif', function(id) { console.log("Last error:", chrome.runtime.lastError); });
}

function notif(title, msg) {
	var opt = {
		type: "basic",
		title: title,
		message: msg,
		iconUrl: "48.png"
	};

	chrome.notifications.create('notif', opt, function(id) { console.log("Last error:", chrome.runtime.lastError); });

	setTimeout(stopNote, 4000);		
}


function mkrequest(url, response) {
	try {
		var newURL = "http://"+localStorage.getItem('raspip')+":2020"+url;
		if (response == 1) {
			notif("Royalbox", "Processing video. Please wait ~ 30 seconds.");
		}
		var req = new XMLHttpRequest();
		req.open('GET', newURL, true);
		req.onreadystatechange = function (aEvt) {
			if (req.readyState == 4) {
				if (req.status == 200) {
					if (response == 1) {
						if (req.responseText == "1") {
							notif("Royalbox", "Video should now start playing.");	
						} else if (req.responseText == "2") {
							notif("Royalbox", "Video has been added to queue.");	
						} else {
							notif("Error", "Please make sure the link is compatible");
						}
					}
				} else {
					chrome.notifications.clear('notif', function(id) { console.log("Last error:", chrome.runtime.lastError); });
					alert("Error during requesting from server ! Make sure the ip/port are corrects, and the server is running.");
				}
			}
		};
		req.send(null);
	} 
	catch(err) {
		alert("Error ! Make sure the ip/port are corrects, and the server is running.")
		return "wrong";
	}
}

function setUpContextMenus() {
  chrome.contextMenus.create({
        "title": "Send to Rpi",
        "contexts": ["link", "video"],
        "onclick": function(info) {
            //chrome.tabs.query({'active': true, 'lastFocusedWindow': true}, function (tabs) {
            //  var url_encoded_url = encodeURIComponent(tabs[0].url);
            //  mkrequest("/stream?url="+url_encoded_url+"&slow="+localStorage.modeslow, 1);
            //});
            var url_encoded_url = encodeURIComponent(info.linkUrl);
            mkrequest("/stream?url="+url_encoded_url+"&slow="+localStorage.modeslow, 1);
        }   
  }); 
}

chrome.runtime.onInstalled.addListener(function() {
	chrome.tabs.create({url: "../options.html"});
	setUpContextMenus();
});

