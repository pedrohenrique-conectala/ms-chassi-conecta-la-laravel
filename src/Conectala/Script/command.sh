#!/bin/sh

#---------------------------------------------------------------------------------
# Data:        14 de Outubro de 2022
# Script:      command_after.sh
# Descrição:   Estrutura o chassi do microsserviço, após a criação do container.
#---------------------------------------------------------------------------------

MS_NAME=$1

MAIN_PATH=./
APP_PATH=$MAIN_PATH/app

#-----------------ESTRUTURA CONDICIONAL--------------------
if [ -n "$MS_NAME" ]; then
  sed -i "s@prefix('api')@prefix('$MS_NAME/api')@g" "$APP_PATH/Providers/RouteServiceProvider.php"
fi;

exit 1;
