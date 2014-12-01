# System.
Exec { path => ["/usr/local/sbin/", "/usr/local/bin/", "/usr/sbin/", "/usr/bin/", "/sbin/", "/bin/", "/usr/games/" ] }

class {'apt': }
package {'make': }
package {'htop': }
package {'git': }

# Configure PHP.
# https://forge.puppetlabs.com/example42/php
class {'php':
  template => '/vagrant/conf/php.ini',
  require => Package['make'],
}

# php::module {'gd': }
php::module {'mysql': }
php::module {'apc':
  module_prefix => "php-"
}
php::module {'curl': }
php::module {'imap': }
# php::module {'memcache': }
# php::pecl::module { "xhprof":
#   use_package     => 'false',
#   preferred_state => 'beta',
# }
# php::pear::module {'PHP_CodeSniffer':
#   use_package => 'false',
# }

# Configure memcache
# class {'memcached':
#   max_memory => 64,
# }

# Configure Apache.
class {'apache':
  default_vhost => false,
  # Required for mod PHP.
  mpm_module => 'prefork',
  default_confd_files => true,
}
# Add mod PHP.
include apache::mod::php
include apache::mod::rewrite
include apache::mod::ssl

# Create a default vhost for shared files in /shared
apache::vhost { 'vagrant.ld':
  port    => '80',
  docroot => '/foo/web',
  default_vhost => true,
# Allow .htaccess overrides.
  directories => [ { path => '/foo/web', allow_override => ['All'] } ],
  docroot_owner => 'www-data',
  docroot_group => 'www-data',
}
->
file {'/foo/web':
  ensure => 'directory',
  owner => 'www-data',
  group => 'www-data',
}
->
file {'/foo/app/cache':
  ensure => 'directory',
  owner => 'www-data',
  group => 'www-data',
}
->
file {'/foo/app/cache/dev':
  ensure => 'directory',
  owner => 'www-data',
  group => 'www-data',
}
->
file {'/foo/app/logs':
  ensure => 'directory',
  owner => 'www-data',
  group => 'www-data',
}
