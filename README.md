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

## How to use this image with PHPAGI (http://phpagi.sourceforge.net/)

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
