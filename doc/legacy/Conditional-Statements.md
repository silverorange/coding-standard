Type Juggling
=============
Do not rely on PHP type juggling; only test known boolean types. Use the
appropriate operator to test for null, integers, strings, etc.  For example,
use:

```php
<?php
if ($widget instanceof SwatWidget) {
    // do thing ...
}
?>
```

rather than:

```php
<?php
if ($widget) {
    // do thing ...
}
?>
```

Equality vs Identity Operator
=============================
Where possible use identity operators over equality operators. Identity
operators preserve the semantics of null. Null is equal but not identical to
 ```(int)0```, ```(boolean)false```, ```(string)''```. Identity operators prevent
 ```null == 0``` logic errors that are hard to debug. For example, use:

```php
<?php
if ($value !== null) {
    // do thing...
}
?>
```

rather than:

```php
<?php
if ($value != null) {
    // do thing...
}
?>
```

String Comparison
=================
Use ```($string == '')``` instead of ```(strlen($string) === 0)``` to check
for an empty string. The reasoning is as follows:

 1. ```$string == ''``` is easier to write.
 2. ```strlen($string)``` is slower, especially when the string is long and mb_string overloading it turned on.
 3. ```$string == ''``` is arguably easier to read for PHP developers and is used extensively in other projects.

Brace Style
===========
See the rules on [braces](Braces).
