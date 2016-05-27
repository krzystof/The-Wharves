FROM nginx:1.8-alpine
MAINTAINER Christophe Graniczny <christophe@getintouch.com>
COPY wharf.template /etc/nginx/conf.d/wharf.template

CMD cp /etc/nginx/conf.d/wharf.template /etc/nginx/conf.d/default.conf && \
    sed -i "s/\$DIRECTORY/$DIRECTORY/" /etc/nginx/conf.d/default.conf && \
    nginx -g 'daemon off;'
