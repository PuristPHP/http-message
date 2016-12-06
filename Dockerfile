FROM php:7.1-rc-cli

ADD ./run-tests.sh /bin

RUN apt-get update && apt-get install -y git zlib1g-dev &&\
    docker-php-ext-install zip &&\
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" &&\
    php -r "if (hash_file('SHA384', 'composer-setup.php') === 'aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" &&\
    php composer-setup.php --install-dir=/bin --filename=composer &&\
    php -r "unlink('composer-setup.php');" &&\
    chmod +x /bin/run-tests.sh /bin/composer

VOLUME /code
WORKDIR /code
