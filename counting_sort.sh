#!/bin/bash

docker run -it --rm --name avl-php-script -v ./app:/usr/src/myapp -w /usr/src/myapp php:7.4-cli php counting_sort.php