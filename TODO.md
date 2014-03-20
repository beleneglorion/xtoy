Reader
======

Reader Classes allow to read data from multiple type of source 
( csv files, xls(x),database ) 
Each "line" of the source is returned as an array and passed to mapper
for conversion


Mapper
======

Mapper take a array in input and generate a new one following a set a rules
each fields of the new array is calculated using a rule definition 
  
Rules a assossiative array
  the key is the name of the output field 
  the value is an array definiting what to do.

  TODO : Doc of the rules, see samples


Writer
======
Writer take the array at the ouput of the mapper and save it on
on the destination resource, it could be  files, databases, what you want
just need to code it


Reporter
========
  The whole process can take time, then if you give to each component (reader,mapper, writer)
a reporter, they can report the current status of the conversion 
(total lines, lines read, line write, line mapped)
 


TODO
====

Composer
 add "require-dev package for Doctrine  "

WRITER
  - Doctrine
  - SQL add transaction option
  - CSV

READER
  - PDO
  - Doctrine
