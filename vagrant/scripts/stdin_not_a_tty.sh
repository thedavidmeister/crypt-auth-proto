#!/usr/bin/env bash
# https://github.com/mitchellh/vagrant/issues/1673#issuecomment-34040409
(grep -q -E '^mesg n$' /root/.profile && sed -i 's/^mesg n$/tty -s \&\& mesg n/g' /root/.profile) || exit 0;
