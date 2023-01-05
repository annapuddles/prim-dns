# What is prim-dns?

prim-dns is a permanent, customizable SecondLife prim URL webservice.

Scripts in SecondLife can request URLs via [`llRequestURL`](https://wiki.secondlife.com/wiki/LlRequestURL) or [`llRequestSecureURL`](https://wiki.secondlife.com/wiki/LlRequestSecureURL), which allow them to receive and respond to HTTP requests from other scripts or even from outside of SecondLife. However, these URLs are temporary, and scripts need to request new URLs periodically, for example when the region the script is in restarts. prim-dns allows a prim to create a permanent URL which points to the temporary one, and then update it whenever it receives a new temporary URL. Other entities can use this permanent URL to obtain the current temporary one at any given time.

# API documentation

## Alias

### `POST /alias`

Create or update an alias for a SecondLife prim URL. A prim should make this request whenever it receives a new URL, such as when the script is restarted, the prim changes regions, or the region the prim is in restarts.

#### Headers
- `X-SecondLife-Object-Key` The key of the requesting prim, used as the name of the alias if no custom name is provided.
- `Authorization` The auth string is required to update an existing alias, but may be omitted when creating a new alias.

#### Request JSON
- `name` A custom name for the alias. If omitted, the requesting prim's key (from the `X-SecondLife-Object-Key` header) is used as the name.
- `url` The SecondLife prim URL that the alias is for.

#### Response JSON
- `name` The name of the created alias.
- `auth` The auth string that should be used in the `Authorization` header in order to update or delete the alias.
- `endpoint` The full endpoint URL for the alias.
- `redirect` A URL that can be used to send requests directly to the prim.
  > **Note:** This cannot be used by scripts to make `POST` requests to the prim, as [`llHTTPRequest`](https://wiki.secondlife.com/wiki/LlHTTPRequest) does not handle redirects for `POST` requests transparently.

#### Example

##### Creating a new alias
```lsl
llHTTPRequest("https://annapuddles.com/prim-dns/alias", [HTTP_METHOD, "POST", HTTP_MIMETYPE, "application/json"], llList2Json(JSON_OBJECT, ["name", "example", "url", "https://google.com"]));
```
```json
{
  "name": "example",
  "auth": "0ba171f25c8e8f8fd60dc58781239faf03ffe260",
  "endpoint": "https://annapuddles.com/prim-dns/alias/example",
  "redirect": "https://annapuddles.com/prim-dns/redirect/example"
}
```

##### Updating an existing alias
```lsl
llHTTPRequest("https://annapuddles.com/prim-dns/alias", [HTTP_METHOD, "POST", HTTP_MIMETYPE, "application/json", HTTP_CUSTOM_HEADER, "Authorization", "0ba171f25c8e8f8fd60dc58781239faf03ffe260"], llList2Json(JSON_OBJECT, ["name", "example", "url", "https://google.com"]));
```
```json
{
  "name": "example",
  "endpoint": "https://annapuddles.com/prim-dns/alias/example",
  "redirect": "https://annapuddles.com/prim-dns/redirect/example"
}
```

### `GET /alias/{name}`

Get the current SecondLife prim URL for an alias.

#### Response JSON
- `url` The prim URL that the alias is for.

#### Example
```lsl
llHTTPRequest("https://annapuddles.com/prim-dns/alias/example", [], "");
```
```json
{
  "url": "https://google.com"
}
```

### `DELETE /alias/{name}`

Delete an existing alias.

#### Headers
- `Authorization` The auth string for the alias.

#### Example
```lsl
llHTTPRequest("https://annapuddles.com/prim-dns/alias/example", [HTTP_METHOD, "DELETE", HTTP_CUSTOM_HEADER, "Authorization", "0ba171f25c8e8f8fd60dc58781239faf03ffe260"], "");
```
