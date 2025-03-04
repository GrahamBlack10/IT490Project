#!/bin/bash

#IPs
# Graham-192.168.196.138 (DMZ)
# Eric- 192.168.196.229 (FrontEnd)
# Regis- 192.168.196.37 (Rabbit)
# Carlos- 192.168.196.134 (Database/Sevrer)


# Frontend: allow RabbitMQ and HTTP (port 80), reject everything else
sudo ufw allow from 192.168.196.229 to any port 80  # HTTP
sudo ufw allow from 192.168.196.229 to any port 443  # HTTPS
sudo ufw allow from 192.168.196.229 to 192.168.196.37 port 15672  
sudo ufw allow from 192.168.196.229 to any port 22 # SSH
sudo ufw reject from any to any

# RabbitMQ: allow frontend, DMZ, and database, reject others
sudo ufw allow from 192.168.196.229 to any port 15672  
sudo ufw allow from 192.168.196.134 to any port 15672  
sudo ufw allow from 192.168.196.138 to any port 15672 
sudo ufw allow from 192.168.196.37 to any port 22
sudo ufw reject from any to any

# DMZ: allow RabbitMQ and HTTP, reject everything else
sudo ufw allow from 192.168.196.138 to 192.168.196.37 port 15672 
sudo ufw allow from 192.168.196.138 to any port 80
sudo ufw allow from 192.168.196.138 to any port 443  
sudo ufw allow from 192.168.196.138 to any port 22
sudo ufw reject from any to any

# Database: only allow RabbitMQ, reject everything else
sudo ufw allow from 192.168.196.134 to 192.168.196.37 port 15672 
sudo ufw allow from 192.168.196.134 to any port 22
sudo ufw reject from any to any


# Enable UFW
sudo ufw enable

# To enable firewall: sudo ufw enable
# To disable firewall: sudo ufw disable
# To check ports: netstat -tuln
# To restart firewall: sudo ufw disable