#!/bin/bash

##################################################
# SCRIPT PARA LINUX/MAC VERSÃO 2.0 - Leandro Silveira
##################################################

iniciarDocker() {
  docker-compose up -d --build
 
  echo
  echo "--== Atualizando php.ini ==--"
  echo
  sleep 2
  docker container exec projeto-patterns-app cp /var/www/html/server-config/php.ini /usr/local/etc/php
 
  echo
  echo "--== Aplicando Reescrita no Apache ==--"
  echo
  sleep 2
  docker container exec projeto-patterns-app a2enmod rewrite
  docker container restart projeto-patterns-app
 
  echo
  echo "--== Atualizando Composer ==--"
  echo
  sleep 2
  docker container exec projeto-patterns-app composer update
 
  echo
  echo "--=======================--"
  echo "--== SERVIDOR INICIADO ==--"
  echo "--=======================--"
  echo
}

finalizarDocker() {
  docker-compose down
 
  echo
  echo "--=========================--"
  echo "--== SERVIDOR FINALIZADO ==--"
  echo "--=========================--"
  echo
}

case $1 in
start)
  iniciarDocker
  exit 0
  ;;
stop)
  finalizarDocker
  exit 1
  ;;
*)
  echo
  echo "Use start para iniciar o Docker e stop para finalizar o Docker"
  echo
  exit 2
  ;;
esac
