# Events Fired

## General

* `oss2/auth::pre_credentials_lookup` - called just before a users credentials are checked in the database.
* `oss2/auth::credentials_retrieved( [ [ 'credentials' => $credentials, 'user' => $user ] ] )` - `$user` may be falde here as credentials are not neceesarily valid.
* `oss2/auth::credentials_valid( [ [ 'credentials' => $credentials, 'user' => $user ] ] )`
* `oss2/auth::credentials_invalid( [ [ 'credentials' => $credentials, 'user' => $user ] ] )`


## /login

* `auth.attempt`
* `auth.login`
* `oss2/auth::auth_failed` with login parameters
