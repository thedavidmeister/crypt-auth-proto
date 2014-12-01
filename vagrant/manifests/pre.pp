# Setup package repositories in an init stage.
# https://forge.puppetlabs.com/puppetlabs/apt
class {'apt': }
apt::source { 'puppetlabs':
  location   => 'http://apt.puppetlabs.com',
  repos      => 'main',
  key        => '4BD6EC30',
  key_server => 'pgp.mit.edu',
}
