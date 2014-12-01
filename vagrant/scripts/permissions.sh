#!/usr/bin/env bash
# Add vagrant to the www-data group to make it easier to work with Apache.
usermod -G www-data vagrant;
