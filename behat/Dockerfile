FROM krzystof/the-wharves-composer

RUN composer require \
    behat/behat \
    behat/mink \
    behat/mink-extention \
    behat/mink-goutte-driver \
    laracasts/behat-laravel-extension

ENTRYPOINT ["behat"]
