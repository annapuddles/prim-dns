# Resources

## Alias

### `POST /alias`

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

#### Headers
- `Authorization` The auth string for the alias.

#### Example
```sh
curl -X DELETE -H 'Authorization: 0ba171f25c8e8f8fd60dc58781239faf03ffe260' https://annapuddles.com/prim-url/alias/example
```
