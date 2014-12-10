#!/bin/bash

# https://gist.github.com/plainprogrammer/8653013

apt-get -y install curl git

wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/key-value-store/2.0.10/foundationdb-clients_2.0.10-1_amd64.deb
wget https://foundationdb.com/downloads/f-ufnxuoixtaxut/I_accept_the_FoundationDB_Community_License_Agreement/key-value-store/2.0.10/foundationdb-server_2.0.10-1_amd64.deb

sudo dpkg -i foundationdb-clients_2.0.10-1_amd64.deb \
foundationdb-server_2.0.10-1_amd64.deb
