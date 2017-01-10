# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  # Configure a single virtual machine.
  config.vm.box = "bento/debian-8.6"
  config.vm.hostname = "tbg"

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
    ansible.version = "2.2.0.0"
  end
end
