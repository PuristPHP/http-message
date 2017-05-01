FROM nicholasruunu/php:7.1-cli

COPY ./run-tests.sh /bin
RUN chmod +x /bin/run-tests.sh

VOLUME /code
WORKDIR /code
