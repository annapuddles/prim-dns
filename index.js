window.addEventListener('load', () => {
	document.getElementById('create-alias').addEventListener('click', () => {
		fetch('alias', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				name: document.getElementById('alias').value
			})
		}).then(resp => resp.json().then(data => {
			if (resp.status == 200) {
				document.getElementById('name').innerHTML = data.name;
				document.getElementById('auth').innerHTML = data.auth;
				document.getElementById('endpoint').innerHTML = `<a href="${data.endpoint}">${data.endpoint}</a>`;
				document.getElementById('redirect').innerHTML = `<a href="${data.redirect}">${data.redirect}</a>`;

				document.getElementById('code').innerHTML = `// server
string prim_dns_api = "https://annapuddles.com/prim-dns/alias";
string server_alias = "${data.name}";
string server_alias_auth = "${data.auth}";

default
{
    state_entry()
    {
        llRequestURL();
    }
    
    changed(integer change)
    {
        if (change & (CHANGED_REGION | CHANGED_REGION_START))
        {
            llRequestURL();
        }
    }
    
    http_request(key request_id, string method, string body)
    {
        // Update the registered URL alias
        if (method == URL_REQUEST_GRANTED)
        {
            list headers = [
                HTTP_METHOD, "POST",
                HTTP_MIMETYPE, "application/json",
                HTTP_CUSTOM_HEADER, "Authorization", server_alias_auth
            ];
            
            list data = [
                "name", server_alias,
                "url", body
            ];
            
            llHTTPRequest(prim_dns_api, headers, llList2Json(JSON_OBJECT, data));
            
            return;
        }
        
        if (method == URL_REQUEST_DENIED)
        {
            llOwnerSay("Failed to obtain a URL: " + body);
            return;
        }
        
        // Handle other requests here
        llHTTPResponse(request_id, 200, "Hello, world!");
    }
    
    http_response(key request_id, integer status, list metadata, string body)
    {
        if (status == 200)
        {
            llOwnerSay("Server alias registered at " + llJsonGetValue(body, ["redirect"]));
        }
        else
        {
            llOwnerSay("Failed to register server alias: [" + (string) status + "] " + llJsonGetValue(body, ["error"]));
        }
    }
}


// client
string server_alias_endpoint = "${data.endpoint}";
string server_url;

default
{
    state_entry()
    {
        llHTTPRequest(server_alias_endpoint, [], "");
    }
    
    http_response(key request_id, integer status, list metadata, string body)
    {
        if (status == 200)
        {
            server_url = llJsonGetValue(body, ["url"]);
            llOwnerSay("Found server URL: " + server_url);
            state ready;
        }
        else
        {
            llOwnerSay("Failed to get server URL: [" + (string) status + "] " + llJsonGetValue(body, ["error"]));
        }
    }
}

state ready
{
    state_entry()
    {
        llHTTPRequest(server_url, [], "");
    }
    
    http_response(key request_id, integer status, list metadata, string body)
    {
        llOwnerSay("Server reply: " + body);
    }
}`;

				document.getElementById('results').style.display = 'block';
			} else {
				alert(data.error);
			}
		}));
	});
});
