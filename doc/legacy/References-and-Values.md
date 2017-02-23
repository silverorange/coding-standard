When References are Used Automatically
======================================
Objects are passed, assigned, returned and created *by reference* in PHP5.
Primitive types are passed, assigned, returned and created *by value* in PHP5.
Primitive types are:

 * integer
 * float
 * array
 * string

When to use Explicit References
===============================
In PHP code, use as few explicit reference operators (```&```) as possible.

 * Function Arguments
   * Prefix non-object arguments with ```&``` in the function declaration if
     passing by reference is required.
 * Function Return Values
   * Prefix the function name with ```&``` in the function declaration if
   returning a reference to a non-object is required.
 * Assignment
   * To assign a reference to a primitive , prefix the right-hand-side of the
     assignment with ```&```. To make a copy of an object use clone. PHP
     5.2.x+ uses copy-on-write for arrays, so assigning arrays by reference
     is discouraged.
 * Foreach Statement
   * If the array in a foreach loop contains objects, PHP5 will assign
     references to the iterator variable. If the array contains non-objects
     then the iterator variable will be assigned a copy by value. To force
     assignment of references for non-objects, prefix the iterator variable
     with ```&``` in the foreach statement.

When to use Clone
=================
Use the ```clone``` operator in assignments when you want or need to assign
an object by value rather than by reference.