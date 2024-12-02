## What's new
- Support multiple clubs per installation
  - `SUPER_ADMIN_ROLE` to manage them all

## Breaking changes
### Globally
Most entities now depend on a club and to getting the list of them the club uuid must be specified in the path.

For example `GET /clubs/{clubUuid}/activities`  
Will return the list of activities related to that club only.

The request to create them also depend on the clubUuid : `POST /clubs/{clubUuid}/activities`

### Activity entity
Custom route `POST /activities/{uuid}/merge-to/{targetUuid}` change for

```http
PATCH /clubs/{clubUuid}/activities/{uuid}/merge
Content-Type: application/merge-patch+json

{
  "target": "{targetUuid}"
}
```
