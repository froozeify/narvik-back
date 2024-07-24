# Loading the fixture
Various commands are available:

- `composer reload-fixture`
  - Empty the database then load all the fixtures
- `composer reload-db`
  - Completely drop the database and recreate it from scratch, then load all fixtures

# Creating new entity
When creating a new entity, you should do the next steps :

1. Create the Entity (and his linked repository)
   - `bin/console make:entity`
2. Add the route in `config/packages/security.yaml`
3. Generate the doctrine migration script
   - `bin/console make:migration` 
4. Create the matching factory (useful for generating fake data)
   - `bin/console make:factory`
5. Optional, Create the matching story
    - `bin/console make:story`
6. Add the data generation inside `src/DataFixtures/AppFixtures`
