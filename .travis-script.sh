#!/bin/bash

set -e $DRUPAL_TI_DEBUG

cd "$DRUPAL_TI_DRUPAL_DIR"
drush cc drush
drush mi --all
