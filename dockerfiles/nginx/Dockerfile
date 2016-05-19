FROM nginx:1.8.1-alpine
MAINTAINER Christophe Graniczny <christophe@getintouch.com>
COPY wharf.template /etc/nginx/conf.d/wharf.template

RUN nginx -g 'daemon off;'

CMD cp /etc/nginx/conf.d/wharf.template /etc/nginx/conf.d/default.conf && \
    sed -i "s/\$DIRECTORY/$DIRECTORY/" /etc/nginx/conf.d/default.conf
#    nginx -g 'daemon off;'
