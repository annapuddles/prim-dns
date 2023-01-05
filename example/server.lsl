string prim_dns_api = "https://annapuddles.com/prim-dns/alias";
string prim_dns_auth = "change me";

default
{
    state_entry()
    {
        llRequestURL();
    }
    
    on_rez(integer param)
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
        if (method == URL_REQUEST_GRANTED)
        {
            llOwnerSay("URL request granted: " + body);
            
            list headers = [
                HTTP_METHOD, "POST",
                HTTP_MIMETYPE, "application/json",
                HTTP_CUSTOM_HEADER, "Authorization", prim_dns_auth
            ];
            
            list data = [
                "name", llGetKey(),
                "url", body
            ];
            
            llHTTPRequest(prim_dns_api, headers, llList2Json(JSON_OBJECT, data));
        }
        else if (method == URL_REQUEST_DENIED)
        {
            llOwnerSay("URL request denied: " + body);
        }
        else
        {
            llHTTPResponse(request_id, 200, "Hello, world!");
        }
    }
    
    http_response(key request_id, integer status, list metadata, string body)
    {
        if (status == 200)
        {
            string auth = llJsonGetValue(body, ["auth"]);
            string endpoint = llJsonGetValue(body, ["endpoint"]);
            
            if (auth == JSON_INVALID)
            {
                llOwnerSay("Server URL updated successfully for " + endpoint);
            }
            else
            {
                llOwnerSay("Server URL registered successfully at " + endpoint);
                llOwnerSay("Copy this string into the prim_dns_auth variable in the script: " + auth);
            }
        }
        else
        {
            llOwnerSay("Registration failed: [" + (string) status + "] " + llJsonGetValue(body, ["error"]));
        }
    }
}
