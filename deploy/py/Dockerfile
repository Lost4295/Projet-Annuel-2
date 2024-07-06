FROM ubuntu:20.04 AS base

WORKDIR /home/docker
RUN apt-get update && \
apt-get install -y openssl shellinabox && apt-get clean

CMD [ "shellinaboxd", "-t" ]
FROM base AS dev
RUN useradd -c docker -m -d /home/docker -s /bin/bash -m docker && echo "docker:docker" | chpasswd

FROM base AS prod
RUN useradd -c prod -m -d /home/docker -s /bin/bash -m prod && echo "prod:prod" | chpasswd
COPY ./appTickets/request.py /home/docker/app.py



