#!/bin/bash

set -e $DRUPAL_TI_DEBUG

cd "$DRUPAL_TI_DRUPAL_DIR"
drush cc drush
drush mi --all

echo "$MODULE_DIR/$DRUPAL_TI_MODULE_NAME/$DRUPAL_TI_PHPUNIT_CORE_SRC_DIRECTORY"
echo $(cd "$DRUPAL_TI_MODULES_PATH"; pwd)
