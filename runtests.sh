#!/bin/sh

cd tests
phpunit --bootstrap __bootstrap.php $1 .