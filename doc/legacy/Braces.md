The silverorange conventions on braces match those of
[PEAR](http://pear.php.net/manual/en/standards.funcdef.php).

Classes and Functions
=====================
For classes and functions, use the *one-true-brace* convention. For example:

```php
<?php

class MyClass
{
    public function myMethod($argument)
    {
        // do stuff ...
    }
}

?>
```

Control Structures
==================
For control structures such as ```while```, ```for```, ```foreach```,
 ```if```, ```elseif```, ```else``` and ```switch``` the brace goes on the same
line as the control structure. For example:

```php
<?php

if ($my_value === null) {
    echo 'Cannot have null values!';
}

foreach ($objects as $key => $object) {
    echo $key, ' => ';
    print_r($object);
}

?>
```