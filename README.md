#install project
make build
make install

#The site will be available on
http://localhost/

#Start docker
make up

#install composer
make install

#update composer
make update

#Stop docker
make down

#Start tests
make test