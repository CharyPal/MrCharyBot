Vagrant.require_version ">= 1.5"

# Check to determine whether we're on a windows or linux/os-x host,
# later on we use this to launch ansible in the supported way
# source: https://stackoverflow.com/questions/2108727/which-in-ruby-checking-if-program-exists-in-path-from-ruby
def which(cmd)
    exts = ENV['PATHEXT'] ? ENV['PATHEXT'].split(';') : ['']
    ENV['PATH'].split(File::PATH_SEPARATOR).each do |path|
        exts.each { |ext|
            exe = File.join(path, "#{cmd}#{ext}")
            return exe if File.executable? exe
        }
    end
    return nil
end

Vagrant.configure("2") do |config|

    config.vm.provider :virtualbox do |v|
        v.name = "alicebot.local"
        v.customize [
            "modifyvm", :id,
            "--name", "alicebot.local",
            "--memory", 1024,
            "--natdnshostresolver1", "on",
            "--cpus", 1,
        ]
    end

    config.vm.box = "ubuntu/trusty64"

    config.vm.network :private_network, type: "dhcp"
    config.ssh.forward_agent = true
    config.vm.hostname = "alicebot.local"
    config.hostsupdater.remove_on_suspend = true

    # If ansible is in your path it will provision from your HOST machine
    # If ansible is not found in the path it will be instaled in the VM and provisioned from there
    if which('ansible-playbook')
        config.vm.provision "ansible" do |ansible|
            ansible.playbook = "ansible/vagrant.yml"
            ansible.groups = {
              "web" => ["default"],
              "vagrant:children" => ["web"]
            }
            ansible.limit = 'vagrant'
        end
    end

    config.vm.synced_folder "./", "/vagrant", type: "nfs"
end
