#!/bin/sh

#---------------------------------------------------------------------------------
# Data:        14 de Outubro de 2022
# Script:      command_after.sh
# Descrição:   Estrutura o chassi do microsserviço, após a criação do container.
#---------------------------------------------------------------------------------

MS_NAME=$1

MAIN_PATH=../../../../../../
APP_PATH=$MAIN_PATH/app
LIBRARY_PATH=$MAIN_PATH/vendor/pedrohenrique-conectala/ms-chassi-conecta-la/src/Conectala

DATABASE_PATH=$MAIN_PATH/database

MIGRATION_PATH_SYSTEM=$DATABASE_PATH/migrations/system
MIGRATION_PATH_TENANT=$DATABASE_PATH/migrations/tenant

CONFIG_PATH_LUMEN=$MAIN_PATH/config

#-----------------ESTRUTURA CONDICIONAL--------------------

if [ ! -e "$MIGRATION_PATH_SYSTEM" ]; then
  mkdir $MIGRATION_PATH_SYSTEM
fi;

if [ ! -e "$MIGRATION_PATH_TENANT" ]; then
  mkdir $MIGRATION_PATH_TENANT
fi;

if [ ! -e "$CONFIG_PATH_LUMEN" ]; then
  mkdir $CONFIG_PATH_LUMEN
fi;

if [ ! -e "$APP_PATH/Http/Controllers/API/V1" ]; then
  mkdir -p "$APP_PATH/Http/Controllers/API/V1"
fi;

cp "$LIBRARY_PATH/MultiTenant/Migration/Migrations"/* "$MIGRATION_PATH_SYSTEM";

if [ -n "$MS_NAME" ]; then
  cp "$LIBRARY_PATH/MultiTenant/Routes/web.php" "$MAIN_PATH/routes/web.php"
  sed -i "s/ms_creating_replace/$MS_NAME/g" "$MAIN_PATH/routes/web.php"
  cp "$LIBRARY_PATH/MultiTenant/Commands/Copy/Kernel.php" "$APP_PATH/Console/Kernel.php";
  cp "$LIBRARY_PATH/Config"/* "$CONFIG_PATH_LUMEN";

  sed -i "s/ms_creating_replace_conectala/ms_${MS_NAME}_conectala/g" "$DATABASE_PATH/seeders/TenantClientsTableSeeder.php"
fi;

exit 1;