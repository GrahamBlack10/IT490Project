#!/bin/bash

#Start Server
echo "Starting RabbitMQ server..."
sudo ~/git/IT490Project/Backend/testRabbitMQServer.php &

#Wait for rabbit  to start 
sleep 5

#Run Data Collection
echo "Running sample API..."
php ~/git/IT490Project/DMZ/sampleAPI.php

#Stop Rabbit Server from Running
kill $RABBITMQ_PID

echo "Data collection process is completed"

# Steps:
# 1. Create this shell file in DMZ
# nano ~/git/IT490Project/DMZ/rabbitmq_data_collection.sh
# 2. Test to make sure it works
# chmod +x ~/git/IT490Project/DMZ/rabbitmq_data_collection.sh
# ~/git/IT490Project/DMZ/rabbitmq_data_collection.sh
# 3. create crontab to run this every 12 hours
# crontab -e                            Your user folder                                                    Your user folder
# Add this to crontab: 0 */12 * * * /home/GrahamB/git/IT490Project/DMZ/rabbitmq_data_collection.sh >> /home/GrahamB/git/IT490Project/DMZ/cron.log 2>&1
# Write and Exit
# run crontab -l to verify that the crontab is there
# 4. Create cron log for errors
#t ouch /home/GrahamB/git/IT490Project/DMZ/cron.log
# chmod 644 /home/GrahamB/git/IT490Project/DMZ/cron.log
# Run again to check for errors
# /home/GrahamB/git/IT490Project/DMZ/rabbitmq_data_collection.sh
# cat /home/GrahamB/git/IT490Project/DMZ/cron.log
# check system cron for errors
# grep cron /var/log/syslog
# If works, check cron one more time
# crontab -l
