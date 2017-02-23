JavaScript and PHP
==================
Use PERL Compatible Regular Expressions (PCRE) rather than POSIX regular
expressions. This means use the ```preg_*``` functions instead of the
```ereg_*``` functions. This is chosen for a number of reasons:

 1. PERL style regular expressions are used in JavaScript, PERL and Ruby. Most
    developers will be familiar with them.
 2. PERL regular expressions support things that POSIX style regular
    expressions do not.
 3. PERL regular expression handling in PHP is faster than POSIX style for
    long strings.
 4. Ereg functions are deprecated in newer version of PHP.

The PHP manual has a good [PCRE pattern syntax guide](http://www.php.net/manual/en/pcre.pattern.php).

Databases
=========
Both MySQL and PostgreSQL support POSIX style regular expressions.
PostgreSQL's POSIX style regular expression matching also supports PERL style
character classes and modifiers. See the
[PostgreSQL manual](http://www.postgresql.org/docs/current/static/functions-matching.html)
for details.

MySQL's regular expression handling is detailed in the
[MySQL regular expression manual](http://dev.mysql.com/doc/mysql/en/regexp.html).
MySQL does not support PERL style character classes and modifiers.