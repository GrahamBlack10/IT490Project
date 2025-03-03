#!/bin/bash

#IPs
# Graham-192.168.196.138 (DMZ/Database)
# Eric- 192.168.196.229 (FrontEnd)
# Regis- 192.168.196.37 (Rabbit)
# Carlos- 192.168.196.134 (Server)


# Frontend: allow RabbitMQ and HTTP (port 80), reject everything else
sudo ufw allow from 192.168.196.229 to any port 80  # HTTP
sudo ufw allow from 192.168.196.229 to any port 443  # HTTPS
sudo ufw allow from 192.168.196.229 to 192.168.196.37 port 5672  
sudo ufw reject from any to any

# RabbitMQ: allow frontend, DMZ, and database, reject others
sudo ufw allow from 192.168.196.229 to any port 5672  
sudo ufw allow from 192.168.196.134 to any port 5672  
sudo ufw allow from 192.168.196.138 to any port 5672  
sudo ufw reject from any to any

# DMZ: allow RabbitMQ and HTTP, reject everything else
ufw allow from 192.168.196.138 to any port 5672
ufw allow from 192.168.196.138 to any port 80
ufw reject from any to any

# Database: only allow RabbitMQ, reject everything else
sudo ufw allow from 192.168.196.37 to any port 5672
sudo ufw reject from any to any

# Server: only allow RabbitMQ, reject everything else
sudo ufw allow from 192.168.196.37 to any port 5672  
sudo ufw reject from any to any

# Enable UFW
sudo ufw enable

# To enable firewall: sudo ufw enable
# To disable firewall: sudo ufw disable
# To check ports: netstat -tuln
# To restart firewall: sudo ufw disable