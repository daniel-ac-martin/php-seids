PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
================================================================

* Added toJSON/fromJSON for priorityQueue (heap based)
* Added toArray/fromArray for priorityQueue (heap based)

[![Build Status][build status]][travis-ci]

**[PHP SEIDS]** provides drop-in replacements for the [SPL Data Structure
classes] which offer alternative implementations and/or enhanced
functionality.

The main features of this library are:

* An [array-deque] (only takes integer keys in a certain range, grows
  automatically from either end)
* An [updatable pairing heap], with associated [priority queue]

A [full list of classes] as well as [some simple tutorials] on how to use the
classes can be found in [the manual].

The library has been designed so that it is very easy to switch between them and
the original ones provided by the SPL. This means that if at some point in the
future you no longer require the extra functionality they provide you can simply
switch back to using the original SPL ones.

This is the initial release of this library which means it cannot be guaranteed
to be bug free. That said, the library has passed its extensive unit test suite
which has 100% [code coverage] (by line).

**Note:** The classes in this library are implemented directly in PHP, rather
than C as the original SPL versions are. As such, they are *not* fast and should
only be used when execution speed is not an issue or when the extra
functionality they provide is absolutely required.

Getting started
---------------

1. PHP 5.3.x is required
2. Install PHP SEIDS using [Composer] (recommended) or manually
3. Read the short [tutorials] to see how to use the library

License
-------

Copyright (C) 2015 Daniel A.C. Martin

Distributed under the MIT License.

 [PHP SEIDS]:                  http://php-seids.net
 [travis-ci]:                  https://travis-ci.org/daniel-ac-martin/php-seids
 [build status]:               https://travis-ci.org/daniel-ac-martin/php-seids.png?branch=master
 [SPL Data Structure classes]: http://php.net/manual/en/spl.datastructures.php
 [array-deque]:                http://php-seids.net/manual/en/class.seids.arrays.dynamic.arraydeque.php
 [updatable pairing heap]:     http://php-seids.net/manual/en/class.seids.heaps.pairing.heap.php
 [priority queue]:             http://php-seids.net/manual/en/class.seids.heaps.pairing.priorityqueue.php
 [full list of classes]:       http://php-seids.net/manual/en/datastructures.php
 [tutorials]:                  http://php-seids.net/manual/en/getting-started.tutorials.php
 [some simple tutorials]:      http://php-seids.net/manual/en/getting-started.tutorials.php
 [the manual]:                 http://php-seids.net/manual/en/
 [code coverage]:              http://php-seids.net/code-coverage/
 [Composer]:                   http://getcomposer.org

