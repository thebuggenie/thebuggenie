# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  # Increase default allocated memory for the virtual machines.
  config.vm.provider "virtualbox" do |vb|
    vb.memory = 1024
  end

  # Configure a single virtual machine.
  config.vm.box = "debian/contrib-buster64"
  config.vm.hostname = "tbg-dev"

  # Forward ports for accessing the web server.
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 443, host: 8443

  # Forward ports for accessing the LDAP server.
  config.vm.network "forwarded_port", guest: 389, host: 8389
  config.vm.network "forwarded_port", guest: 636, host: 8636

  # Use Ansible for provisining the virtual machine. Use local provisioning in
  # order not to polute the user's host system.
  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "ansible/provision.yml"
    ansible.install_mode = "pip"
    ansible.pip_install_cmd = "curl https://bootstrap.pypa.io/2.7/get-pip.py | sudo python"
    ansible.version = "2.9.16"
  end
end
