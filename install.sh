#!/bin/bash

sudo apt-get install php5
wget http://pecl.php.net/get/mailparse-2.1.5.tgz
tar -xzvf mailparse-2.1.5.tgz

cd mailparse-2.1.5
phpize
./configure
make
sudo make install

cd ..
rm -rf mailparse-2.1.5 mailparse-2.1.5.tgz package*.xml

mkdir mime-mail-parser
wget http://php-mime-mail-parser.googlecode.com/svn/trunk/MimeMailParser.class.php -P mime-mail-parser
wget http://php-mime-mail-parser.googlecode.com/svn/trunk/attachment.class.php -P mime-mail-parser

echo -e "\n-----------------------"
echo -e "Installation finished."
echo -e "Now please add the following line to your /etc/php5/cli/php.ini file ( the php-cli configuration file):"
echo -e "extension=mailparse.so"
