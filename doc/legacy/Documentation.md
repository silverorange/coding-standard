Docblocks
=========
All classes, methods, functions and properties require docblocks. Docblocks
are parsed by the phpDocumenter to automatically generate API documentation.
Docblocks are formatted as follows:

 1. The first line is a short-as-possible summary beginning with a capital
    letter and not ending with a period. For methods and functions, this
    should be a third-person verb phrase. Imagine the verb phrase as
    beginning with "This method ..."
 2. Following the summary is a blank line.
 3. The next line is an optional multi-line long description that restates and
    elaborates on the summary. This description is written as fully punctuated
    sentences. One space follows a period.
 4. Following the description is a blank line.
 5. Required tags, each starting with ```@``` symbol. See the
    [phpDocumenter manual](http://manual.phpdoc.org/HTMLframesConverter/default/phpDocumentor/tutorial_tags.pkg.html)
    for more information.
 6. Optional tags, each starting with ```@``` symbol. See the
    [phpDocumenter manual](http://manual.phpdoc.org/HTMLframesConverter/default/phpDocumentor/tutorial_tags.pkg.html)
    for more information.

For parameter descriptions, the second line and following lines of are
indented with spaces so that they are one character past the start of the
first description line.

An Example docblock:

```php
<?php

/**
 * Encrypts a string
 *
 * Calls the encrypt process to encrypt the string.
 *
 * @param string $string the string to encrypt.
 * @param integer $type the type of encryption to use. Valid types are defined
 *                       in ValidTypes.php.
 *
 * @return string the encrypted data.
 */

?>
```

Identifying Pending Tasks
=========================

Use ```TODO:``` to prefix in internal comments that identify regions requiring
further attention. This special keyword is highlighted in vim and can easily
be searched in other editors.

You may optionally use the ```@todo``` phpDocumentor tag.

Internal and Inline Comments
============================
Small (1-4 lines) internal comments can use the C++ ```//``` commenting style,
but put a single space after the ```//``` and before the text. Such comments
do not have to be proper sentences. For multiline comments of more than 5
lines (typically a paragraph of explanation), use C-style comments. Some
example comments are:

```php
<?php

// Single line comments use C++ style slashes.

// Multiline comment using C++ style slashes. The comment is too
// long to fit on one line, but is not a whole paragraph of
// explanation.

/*
 * Multiline comments are like docblocks, but are missing a star 
 * at the top left. Use C-style commenting for long paragraphs of
 * explanatory text. Use C++ style comments for smaller notes that
 * span one to five lines of text. Comments should be meaningful
 * and understandable when returning to the code at a later date.
 */

?>
```