# Creating new entity
When creating a new entity, you should do the next steps :

1. Create the Entity
2. Generate the doctrine migration script
   - `bin/console doctrine:migrations:diff` 
3. Create the matching factory (useful for generating fake data)
   - `bin/console make:factory` 
4. Add the data generation inside `src/DataFixtures/AppFixtures`
