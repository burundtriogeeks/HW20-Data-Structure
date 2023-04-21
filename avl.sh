#!/bin/bash

docker run -it --rm --name avl-php-script -v ./app:/usr/src/myapp -w /usr/src/myapp php:7.4-cli php avl_run.php