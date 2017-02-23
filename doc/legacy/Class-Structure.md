Property Declarations
=====================

All properties must be declared for every class even if the properties are
private. Properties follow the [property naming conventions](Naming.md) and
should be assigned a default value appropriate to the type of data they
contain.

A scope keyword must precede all properties. Class properties must be ordered
by scope as follows:

 1. public
 2. protected
 3. private

All class properties should be documented following the
[documentation rules](Documentation.md).

Method Declarations
===================
All methods follow the [method naming conventions](Naming.md). A scope
keyword is required for every method including the constructor.

All method definitions should occur *after* all property definitions. They
should be listed in the following order:

 1. public
 2. protected
 3. private

All methods should be documented following the
[documentation rules](Documentation.md).

Example
=======
This example illustrates the class structure conventions, but it omits
documentation. Documentation should be included.

```php
<?php

class TestClass
{
    public $id = null;

    protected $children = array();

    private $counter = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function display()
    {
        // display this test class ...
        $this->displayChildren();
    }

    protected displayChildren()
    {
        // display children ...
    }

    private incrementCounter()
    {
        $this->counter++;
    }
}

?>
```
