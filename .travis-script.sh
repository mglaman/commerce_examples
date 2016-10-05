#!/bin/bash

set -e $DRUPAL_TI_DEBUG

cd "$DRUPAL_TI_DRUPAL_DIR"
drush cset migrate_plus.migration_group.commerce_demo_tshirt shared_configuration.source.path ./modules/commerce_demo/data/demo_t_shirts.csv --yes
drush cset migrate_plus.migration_group.commerce_demo_ebook shared_configuration.source.path ./modules/commerce_demo/data/demo_ebooks.csv --yes
drush cc drush
drush mi --all
