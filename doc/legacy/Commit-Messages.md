Commits should be self-contained changesets that can be described
collectively. 

First Line
==========
The first line on a commit message should be terse and no more
than 50 characters.

Description
===========
If additional description is needed, add a blank line and then a detailed
description. In the description, use proper sentences and punctuation with one
space following a period. Wrap lines at around 70 characters. For bullet lists,
prefix bullet items with ``` * ```.

By default, Vim will apply the above formatting rules to commit messages.

Example
=======
```
Handle multiple input types in encrypt.

This is a significant rewrite that allows different types. Encrypt
now allows for:
 * rot13
 * base64
 * base16
 * urlencode
```