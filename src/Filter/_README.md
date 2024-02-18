## Creating a filter with JOIN relationship

To create the JOIN alias name, you must use the build-in tool `QueryNameGenerator::generateJoinAlias` (also `QueryNameGenerator::generateParameterName`).  
Using that tool allow doctrine to cache them, and also avoid us some bug we could encountered with not unique join alias name.

### Naming convention
By default you could type anything in the generateJoinAlias() value.

But for clarity and better comprehension, you should always name the alias as the column name path (i.e `member` if you make the join over the member field).

If it's a join alias on a sub level, then you just have to separate each column name with an underscore.

### Example

```php
$memberAlias = $queryNameGenerator->generateJoinAlias("member");
$memberSubLevelAlias = $queryNameGenerator->generateJoinAlias("member_subclass");

// We add the join
$queryBuilder->leftJoin(Member::class, "$memberAlias", Join::WITH, "$memberAlias.id = :value");
$queryBuilder->leftJoin(SubClass::class, "$memberSubLevelAlias", Join::WITH, "$memberSubLevelAlias.id = $memberAlias.subclass");
```
