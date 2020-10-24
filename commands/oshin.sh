#!/usr/bin/env bash
php /var/www/lnoapi/yii oshin/prune-data
php /var/www/lnoapi/yii oshin/clean-data
php /var/www/lnoapi/yii oshin/clean-event