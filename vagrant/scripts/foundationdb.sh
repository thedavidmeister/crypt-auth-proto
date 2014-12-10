#!/bin/bash

apt-get -y install curl git

wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/key-value-store/2.0.10/foundationdb-clients_2.0.10-1_amd64.deb
wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/key-value-store/2.0.10/foundationdb-server_2.0.10-1_amd64.deb

sudo dpkg -i foundationdb-clients_2.0.10-1_amd64.deb \
foundationdb-server_2.0.10-1_amd64.deb


# https://foundationdb.com/layers/sql/documentation/GettingStarted/install.local.linux.html

wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/sql-layer/2.0.2/fdb-sql-layer_2.0.2-1_all.deb
wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/sql-layer/2.0.2/fdb-sql-layer-client-tools_2.0.2-1_all.deb

sudo dpkg -i fdb-sql-layer_2.0.2-1_all.deb \
fdb-sql-layer-client-tools_2.0.2-1_all.deb

apt-get -y install php5-pgsql
