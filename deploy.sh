#!/bin/bash

echo
echo -e "\e[48;5;124m ALWAYS RUN UNIT TESTS BEFORE CREATING DEPLOYMENT PACKAGE! \e[0m"
echo
sleep 2

# Cleanup any leftovers
echo -e "\e[32mCleaning up...\e[0m"
rm -rf ./core.zip
rm -rf ./core

# Create deployment source
echo -e "\e[32mSTEP 1:\e[0m Copying plugin source..."
mkdir core
cp -r ./* core


# Ensure proper composer dependencies
echo -e "\e[32mSTEP 2:\e[0m Installing composer dependencies..."
cd ./core
cd..

# Remove unnecessary files from final release archive
echo -e "\e[32mSTEP 3:\e[0m Removing unnecessary files from final release archive..."
rm deploy.sh
rm .gitignore
rm -rf .git/
rm -rf .idea/
rm -rf core

rm -rf vendor

# Adding PrestaShop mandatory index.php file to all folders
find . -type d -exec cp index.php {} \;
