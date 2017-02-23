Some text editors look for special lines that define editor settings to use
with the file. At the beginning of files it is allowed to place a special
comment line containing settings before the first docblock. We include a Vim
settings line in many files.

Vim
===
The following settings line is used for Vim.

```php
<?php
/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
?>
```
