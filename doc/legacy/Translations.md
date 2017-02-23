GNU Gettext is used for translation.

Quoting
=======
Strings passed to gettext can be either single or double quoted. The xgettext
utility extracts both string types.

Examples
========
```php
<?php

$my_string = _('This is a translatable string.');

$plural = sprintf(
    ngettext(
        'There is one true brace style.',
        'There are %s true brace styles.',
        $count
    ),
    $count
);

?>
```