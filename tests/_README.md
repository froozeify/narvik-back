## Debugging
If a test fail and you want to check the database status at this moment you can do the following :

[Source](https://github.com/dmaicher/doctrine-test-bundle?tab=readme-ov-file#debugging)
```php
public function testMyTestCaseThatINeedToDebug()
{
    // ... something thats changes the DB state
    $this->debugTestDatabase();
    // now the DB changes are actually persisted and you can debug them
}
```
