# AbstractModel

The problem: writing `get` and `set` methods for specific
properties can get tiresome, especially when there is no extra
logic other than getting, and setting. `AbstractModel` solves
this problem by letting you build a data model and
automatically generating the mutators.

The `AbstractModel` class implements the PSR-11 Container
interface. 

## Installation

    $ composer require dsmithhayes/abstractmodel

## Usage

```php
<?php

use Dsh\AbstractModel;

/**
 * By default all public values are treated as such.
 */
class User extends AbstractModel
{
    /**
     * Protected properties get mutators by default
     */
    protected $username;
    
    /**
     * Private properties do not.
     */
    private $password;
    
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
}

// `init()` actually reads the properties and builds a store
// for the values
$user = (new User('dave', 'password'))->init();

echo $user->getUsername(); // yields 'dave'
echo $user->getPassword(); // throws an Exception

$user->init(User::USE_ALL);

echo $user->getPassword(); // yields 'password'
```