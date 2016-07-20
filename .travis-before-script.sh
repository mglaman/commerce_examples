#!/bin/bash

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
# Note: This function is re-entrant.
drupal_ti_ensure_drupal

# Add custom modules to drupal build.
cd "$DRUPAL_TI_DRUPAL_DIR"

# Download custom branches of address and composer_manager.
(
    # These variables come from environments/drupal-*.sh
    mkdir -p "$DRUPAL_TI_MODULES_PATH"
    cd "$DRUPAL_TI_MODULES_PATH"

    git clone --branch 8.x-1.x http://git.drupal.org/project/composer_manager.git
    git clone --branch 8.x-1.x http://git.drupal.org/project/address.git
    git clone --branch 8.x-1.x http://git.drupal.org/project/entity.git
    git clone --branch 8.x-1.x http://git.drupal.org/project/inline_entity_form.git
    git clone --branch 8.x-1.x http://git.drupal.org/project/state_machine.git
    git clone --branch 8.x-1.x http://git.drupal.org/project/profile.git
)

# Ensure the module is linked into the codebase.
drupal_ti_ensure_module_linked

# Initialize composer_manager.
php modules/composer_manager/scripts/init.php
composer drupal-rebuild
composer update -n --lock --verbose

# Enable main module and submodules.
drush en -y commerce commerce_product commerce_order commerce_checkout

# Turn on PhantomJS for functional Javascript tests
phantomjs --ssl-protocol=any --ignore-ssl-errors=true $DRUPAL_TI_DRUPAL_DIR/vendor/jcalderonzumba/gastonjs/src/Client/main.js 8510 1024 768 2>&1 >> /dev/null &

if [[ "$BLACKFIRE" = "on" ]]; then
  openssl aes-256-cbc -K $encrypted_f20332cf8ed0_key -iv $encrypted_f20332cf8ed0_iv -in .blackfire.travis.ini.enc -out ~/.blackfire.ini -d
  curl -L https://blackfire.io/api/v1/releases/agent/linux/amd64 | tar zxpf -
  chmod 755 agent && ./agent --config=~/.blackfire.ini --socket=unix:///tmp/blackfire.sock &

  curl -L https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$(php -r "echo PHP_MAJOR_VERSION . PHP_MINOR_VERSION;")-zts | tar zxpf -
  echo "extension=$(pwd)/$(ls blackfire-*.so | tr -d '[[:space:]]')" > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/blackfire.ini
  echo "blackfire.agent_socket=unix:///tmp/blackfire.sock" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/blackfire.ini
  # Link the module's .blackfire.yml to the docroot so it's tests will catch.
  ln -sf "$TRAVIS_BUILD_DIR/.blackfire.yml" "$DRUPAL_TI_DRUPAL_DIR/.blackfire.yml"
fi
