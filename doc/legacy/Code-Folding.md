Vim code folding markers are optional. They may be added at the discretion of
the programmer but should be maintained if they exist.

Vim Folding Markers
===================
By convention, a blank line goes above and below a code folding marker unless
the marker is immediately followed by another marker. Opening markers should
have a short description of that they contain. This is usually the class or
method name. Method names do not need argument lists, but should have
parentheses to indicate they are methods and neither classes nor properties.

Example
=======
```php
<?php

// {{{ TestClass
 
class TestClass
{
    // {{{ __construct()

    public function __construct($id = null)
    {
        // do stuff ...
    }

    // }}}
    // {{{ square()

    public function square($x)
    {
        return $x * $x;
    }

    // }}}
}
 
// }}}

?>
```

In Vim, after expanding the outside marker, the inside of this class will
appear as:

```php
<?php

// {{{ TestClass

class TestClass
{
+--- 8 lines: __construct()--------------
+--- 8 lines: square()-------------------
}

// }}}

?>
```