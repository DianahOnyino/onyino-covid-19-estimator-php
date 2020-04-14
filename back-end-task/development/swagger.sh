#!/bin/bash

mkdir ../public/swagger
php ../../vendor/ --bootstrap ./swagger-constants.php --output ../public/swagger ./swagger-v1.php ../app/Http/Controllers
