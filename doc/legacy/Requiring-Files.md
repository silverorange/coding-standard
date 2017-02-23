When including other files, parenthesis should not be used for
 ```require_once``` or ```include_once``` statements. These are statements,
 not function calls. Some examples are:

```php
<?php
require_once 'SwatHtmlTag.php';
include_once 'javascript/swat-checkall.js';
?>
```

Relative Includes
=================
For includes, the relative path will check from the document root first. If
the file is not found PHP will then check the folder of the last required
file. This means if you have a site directory structure as follows:

```
/www/index.php
/include/MyClass.php
/include/MyOtherClass.php
```

In ```MyClass.php``` you may use either:

 1. ```include_once '../include/MyOtherClass.php';``` or
 2. ```include_once 'MyOtherClass.php';```