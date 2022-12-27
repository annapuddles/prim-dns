# Resources

## Alias

### `POST /alias`

#### Headers
- `X-SecondLife-ObjectKey` The key of the requesting prim, used as the name of the alias if no custom name is provided.
- `Authorization` The auth string is required to update an existing alias, but may be omitted when creating a new alias.

#### Request JSON
- `name` A custom name for the alias. If omitted, the requesting prim's key is used as the name.
- `url` The SecondLife prim URL that the alias is for.

#### Response JSON
- `name` The name of the created alias.
- `auth` The auth string that should be used in the `Authorization` header in order to update or delete the alias.
- `endpoint` The full endpoint URL for the alias.
- `redirect` A URL that can be used to send requests directly to the prim.

### `GET /alias/{name}`

#### Response JSON
- `url` The prim URL that the alias is for.

### `DELETE /alias/{name}`

#### Headers
- `Authorization` The auth string for the alias.
