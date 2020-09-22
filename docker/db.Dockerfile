FROM postgres:11.5

ARG user_uid

RUN usermod -u ${user_uid? invalid argument} postgres
RUN groupmod -g ${user_uid? invalid argument} postgres