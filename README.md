# fastagi_php:7.3

Fastagi server for PHP 7.3

## Running it.

```docker
docker run   -itd -p4573:4573 --name fastagi  georgeosantiago/fastagi_php:7.3
```
## What is it based on?

debian:buster-slim

## Building it.

```docker
docker build . -t georgeosantiago/fastagi_php:7.3
```
## Dockerfile

```docker
FROM debian:buster-slim

RUN apt-get update -qq && apt-get install --no-install-recommends --no-install-suggests -yqq php7.3-cli php7.3-json php7.3-mysql gcc xinetd rsyslog && rm -rf /var/lib/apt/lists/*
RUN echo "agi             4573/tcp                        # FAST AGI entry" >> /etc/services

COPY ./agi/agiLaunch.sh /
COPY ./agi/agi.php /tmp/
COPY ./agi/xinetd_agi /etc/xinetd.d/agi

EXPOSE 4573
CMD service rsyslog start && xinetd -stayalive -dontfork -pidfile /var/run/xinetd.pid
```
## agiLaunch.sh

```bash
#!/bin/bash
php -q /tmp/agi.php 2> /dev/null
```
## xinetd_agi

```docker
# description: agi service for PHP fastagi interaction
service agi
{
        socket_type  = stream
        user         = root
        group        = nobody
        log_type     = FILE /var/log/xinetd.log
        server       = /agiLaunch.sh
        wait         = no
        protocol     = tcp
        bind         = 0.0.0.0
        disable      = no
        per_source   = UNLIMITED
        instances    = UNLIMITED
        cps          = 1000 0
}
```
## agi.php

```php
<?php
	// This is just a demo of doing something with agi
	echo 'VERBOSE "Hey hey, PHP via FastAGI!" 3';
?>
```
## Test

```cmd
telnet localhost 4573
```

# How to use this image with PHPAGI (http://phpagi.sourceforge.net/)

## Create the directory structure and copy the project files

```text
├── phpagi
│   ├── agiLaunch.sh
│   ├── phpagi-asmanager.php
│   ├── phpagi.conf
│   ├── phpagi-fastagi.php
│   ├── phpagi.php
│   └── test
│       └── sample.php
```

## agiLaunch.sh

### agiLaunch.sh should run PHP FastAGI bootstrap

```bash
#!/bin/bash
php -q /var/lib/asterisk/agi-bin/phpagi-fastagi.php 2> /dev/null
```

## Configure phpagi.conf

```text
[phpagi]
debug=false							; enable debuging
error_handler=true					; use internal error handler
;admin=errors@mydomain.com			; mail errors to
;hostname=sip.mydomain.com			; host name of this server
tempdir=/var/spool/asterisk/tmp/	; temporary directory for storing temporary output
;tempdir=/tmp/

[asmanager]
server=localhost		; server to connect to
port=5038				; default manager port
username=admin			; username for login
secret=admin			; password for login

[fastagi]
setuid=true							; drop privileges to owner of script
basedir=/var/lib/asterisk/agi-bin/	; path to script folder

[festival]							; text to speech engine
;text2wave=/usr/bin/text2wave		; path to text2wave binary

[cepstral]						; alternate text to speech engine
;swift=/opt/swift/bin/swift		; path to switft binary
;voice=David					; default voice
```

## Mount the directories with docker-compose

```text
version: '3'
 
services:
    
  fastagi:
    container_name: fastagi_php    
    restart: unless-stopped
    image: georgeosantiago/fastagi_php:7.3
    hostname: fastagi_php
    domainname: fastagi_php
    networks:
      - default
      
    ports:
      - 4573:4573
      
    volumes:
      - ./phpagi/agiLaunch.sh:/agiLaunch.sh      
      - ./phpagi/phpagi.conf:/etc/asterisk/phpagi.conf      
      - ./phpagi/:/var/lib/asterisk/agi-bin/      
```