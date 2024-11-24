# Formatting a CSV response for entities
You can specify a custom format when user request csv.  
For example by calling `GET /member-presences.csv` or even `GET /member-presences/1.csv`

All api-platform normalization and context are applied.  
Watch out of the `@Groups` annotation or even `@ApiProperty(security="is_granted('ROLE_ADMIN')")` that will be applied to the fields

## File structure

Typical structure
```yaml
App\Entity\ClubDependent\MemberPresence:
    id: ~
    date: ~
    createdAt: creation

    member:
        fields:
            licence: ~
            fullName: ~

    activities:
        prefix: activity
        fields:
            id: ~
            name: ~
```

The property must match to an existing property of the target entity.

### Normal property

Possible values:

- `not defined`: won't be normalized in the csv
- `~`: the column in the csv will have the same name, for example `id`
- `<string>`: any string value will rename the csv column with the string you defined

### Relational property

For OneToMany/ManyToOne/OneToOne relationships

Structure example:
```yaml
activities:
    prefix: activities
    fields:
        id: ~
        name: ~
```

- `prefix`: optional fields, should be defined for `OneToMany` relationships since it will be an array.  
  The prefix will be applied in front of the fields name. Or in front of the loopings for array. In the previous example it will be `activities.0.id, activities.0.name, ...`  
  **If not defined, the fields will be defined as it was in the parent**

- `fields`: always required, a structure, that could be either a `normal property` or `relational property`

## Validation

You can run `php bin/console serializer:validate:csv` to verify the custom csv is valid
