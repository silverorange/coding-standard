SQL Indenting
=============
```sql
select <select clause>,
    <more select clause>
from <tables>
    <join clauses>
where <conditions>
    <more conditions>
group by <group by clause>
having <conditions>
order by <orderby>
limit <number of rows>
offset <number of rows>
```

SQL Queries in PHP
==================
Inside PHP code, use the following format to make SQL queries:

```php
<?php
$sql = sprintf(
    'select * from articles where
    shortname = %s and enabled = %s',
    $this->app->db->quote($shortname, 'text'),
    $this->app->db->quote(true, 'boolean')
);
?>
```

Each ```quote()``` call is placed on a separate line.