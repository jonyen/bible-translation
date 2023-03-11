FROM php:7.4-apache
WORKDIR /var/www/html

COPY Bible-Passage-Reference-Parser-master/ Bible-Passage-Reference-Parser-master
COPY en/ en                                    
COPY assets/ assets                                
COPY index.html index.html                            
COPY stylesheets/ stylesheets
#Procfile                              
#composer.json                         
#index.php                             
#composer.lock                         
COPY javascripts/ javascripts                           
COPY vendor/ vendor

#COPY index.php index.php
EXPOSE 80
