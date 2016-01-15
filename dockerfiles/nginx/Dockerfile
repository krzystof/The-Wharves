FROM nginx:latest

MAINTAINER Christophe Graniczny

COPY nginx.conf /etc/nginx/conf.d/default.conf

RUN mkdir /etc/nginx/sites
VOLUME ["/etc/nginx/sites"]
