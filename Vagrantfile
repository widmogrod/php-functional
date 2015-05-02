Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"

  config.vm.provision "shell", inline: <<-SHELL
    sudo locale-gen UTF-8
    sudo apt-get update
    sudo apt-get install -y php5
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/bin --filename=composer
  SHELL
end
