#!/bin/sh

#---------------------------------------------------------------------------------
# Data:        14 de Outubro de 2022
# Script:      command_after.sh
# Descrição:   Estrutura o chassi do microsserviço, após a criação do container.
#---------------------------------------------------------------------------------

MS_NAME=$1

MAIN_PATH=./
APP_PATH=$MAIN_PATH/app
LIBRARY_PATH=$MAIN_PATH/vendor/pedrohenrique-conectala/ms-chassi-conecta-la-laravel/src/Conectala

#-----------------ESTRUTURA CONDICIONAL--------------------
if [ -n "$MS_NAME" ]; then
  cp "$LIBRARY_PATH/MultiTenant/Routes/api.php" "$MAIN_PATH/routes/api.php"
  sed -i "s/->prefix('api')/->prefix('$MS_NAME/api')/g" "$APP_PATH/Providers/RouteServiceProvider.php"
fi;

exit 1;
