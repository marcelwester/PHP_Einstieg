#!/bin/bash

. ~/.bashrc


cd /var/www/html/telegram-bot

php send_message.php 1>>/var/log/telegrami-bot.log 2>&1


exit
