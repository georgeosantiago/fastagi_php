FROM debian:buster-slim

RUN apt-get update -qq && apt-get install --no-install-recommends --no-install-suggests -yqq php7.3-cli php7.3-json php7.3-mysql gcc xinetd rsyslog && rm -rf /var/lib/apt/lists/*
RUN echo "agi             4573/tcp                        # FAST AGI entry" >> /etc/services

COPY ./agi/agiLaunch.sh /
COPY ./agi/agi.php /tmp/
COPY ./agi/xinetd_agi /etc/xinetd.d/agi

EXPOSE 4573
CMD service rsyslog start && xinetd -stayalive -dontfork -pidfile /var/run/xinetd.pid
