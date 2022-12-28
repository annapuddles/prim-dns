# What is prim-url?

prim-url is a permanent, customizable SecondLife prim URL webservice.

Scripts in SecondLife can request URLs via [`llRequestURL`](https://wiki.secondlife.com/wiki/LlRequestURL) or [`llRequestSecureURL`](https://wiki.secondlife.com/wiki/LlRequestSecureURL), which allow them to receive and respond to HTTP requests from other scripts or even from outside of SecondLife. However, these URLs are temporary, and scripts need to request new URLs periodically, for example when the region the script is in restarts. prim-url allows a prim to create a permanent URL which points to the temporary one, and then update it whenever it receives a new temporary URL. Other entities can use this permanent URL to obtain the current temporary one at any given time.

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
  > **Note:** Using this URL in scripts can be tricky if you need to make a `POST` request to the prim. [`llHTTPRequest`](https://wiki.secondlife.com/wiki/LlHTTPRequest) does not handle redirects for `POST` requests transparently, and there is no way to access response headers such as `Location` from within the [`http_response`](https://wiki.secondlife.com/wiki/Http_response) event handler. However, the prim URL will also be included in the body of the redirect response just as it is in [a `GET` request to the endpoint](#get-aliasname), so it may still be possible to use this URL for `POST` requests in scripts.

#### Example
```sh
curl -H 'Content-Type: application/json' -d '{"name": "example", "url": "google.com"}' https://annapuddles.com/prim-url/alias
```
```json
{
  "name": "example",
  "auth": "0ba171f25c8e8f8fd60dc58781239faf03ffe260",
  "endpoint": "https://annapuddles.com/prim-url/alias/example",
  "redirect": "https://annapuddles.com/prim-url/redirect/example"
}
```

### `GET /alias/{name}`

Get the current SecondLife prim URL for an alias.

#### Response JSON
- `url` The prim URL that the alias is for.

#### Example
```sh
curl https://annapuddles.com/prim-url/alias/example
```
```json
{
  "url": "google.com"
}
```

### `DELETE /alias/{name}`

Delete an existing alias.

#### Headers
- `Authorization` The auth string for the alias.

#### Example
```sh
curl -X DELETE -H 'Authorization: 0ba171f25c8e8f8fd60dc58781239faf03ffe260' https://annapuddles.com/prim-url/alias/example
```
