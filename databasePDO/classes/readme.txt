

-- Configure PHP 
./configure  --with-gd --enable-sigchild --with-oci8=$ORACLE_HOME --with-apxs2=/usr/bin/apxs  --prefix=/opt/install/php \
             --with-mysqli=/usr/bin/mysql_config --with-pdo-mysql --with-pdo-oci=$ORACLE_HOME --with-pdo-pgsql