Lines
=====
Lines in PHP source files are wrapped at a maximum of 80 characters. The
line-length of 80 characters is chosen as a lowest common denominator for
editing on a variety of platforms, including emailed code diffs. XHTML-based
layout files may exceed the 80-character line length limit.

No block of code should be longer than a single screen (about 30 lines).
Limiting the length of code blocks enhances the readability of source code. If
code blocks are becoming too long, create helper methods, or restructure the
lengthy code.

Single and Multi-Line Statements
================================
When control statements statements are followed by a single statement rather
than a code block, put the single statement on the next line indented one level
further. Then place one line of whitespace after the statement. The control
statements this convention applies to are ```if``` and ```while```. Some example
are:

```php
<?php

if ($has_password) {
    echo 'Welcome, User!';
}

while ($more_rows) {
    echo $row['name'];
}

?>
```

Several related single-line statements may go on concurrent lines. Any
multi-line statement is separated from other statements with a single empty
line following the multi-line statement.

When breaking a single statement across multiple lines at an operator, the
last character on the first line should be the operator. Some examples are:

```php
<?php

$long_variable_name = $this->someMethod('test') +
    SOME_CONSTANT;

if ($my_variable + 12 > $max ||
    $default === null) {
    // ...
}

?>
```

Formatting
==========
You may line up multiple related statements to promote readability. When
lining up multiple statements, always use spaces to line up instead of tabs.
This ensures all statements line up regardless of the tab size of the
editor. For example:

```php
<?php

$this->id        = 0;
$this->shortname = 'test';
$this->title     = 'Test Article';

?>
```

Switch statements are formatted as:

```php
<?php

switch ($variable) {
case 0:
  ...
case 1:
  ...
default:
  ...
}

?>
```

A single space always follows a comma in code, and all operators are surrounded
by spaces. This makes code much easier to read and debug. The one exception is
the concatenate (```.```) operator. The concatenation operator is not
surrounded by spaces. Some examples are:

```php
<?php

$my_variable = $other_variable + 1;
echo 'this is a test '.$my_variable;
$array = array(1, 2, 3);

?>
```

Indenting
=========
To ensure code always looks normal, always set the editor tab size to 4
spaces. Real tab characters should be used as opposed to indenting with
spaces. Tabs should only be used for indenting, and never for spacing. The
following command will set vim to use 4 spaces for tab characters:

```vim
:set ts=4
```

When editing, be careful not to add extra whitespace on blank lines. Some
editors add this automatically if auto-indenting is turned on. No extra
whitespace should be on blank lines.

Code should not be indented more than 5 levels. If code needs to be indented
more than 5 levels then it is probably too complex. Declare a method, or
restructure the code to make it less complex.
