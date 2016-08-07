Mr. Chary Bot
=============

Mr. CharyBot is your financial adjutant implemented as Telegram Bot that tracks your expenses and income. It uses simple categories to make reports. Nothing special there.

Utilizes the following technologies to make his living:

* PHP
* Nginx
* Percona
* Bootstrap
* Symfony3, Doctrine2 and friends
* Ansible
* Vagrant

Development Installation
------------------------

    vagrant up
    
And you should be good. Then `vagrant ssh` and your on a provisioned machine.

Deployment
----------

Provisioning is done with Ansible on top of Ubuntu 14.04. Might actually work on 16.04, but I never tested that. You will need a OS that can run Ansible (basically anything except Windows™).

#### Install required ansible roles

    ansible-galaxy install -r requirements.yml

#### Create ansible inventory and group_vars file

Create `ansible/invs/enviromentName` describing your inventory. Create `ansible/group_vars/environmentName.yml` with secret variables or things you would like to redefine from `ansible/group_vars/all.yml`.

#### Full provisioning

Run the following command for full provisioning:

    cd ansible
    ansible-playbook -i invs/environmentName playbook.yml
    
#### Deploy bot only without other software

    cd ansible
    ansible-playbook -i invs/environmentName app.yml    

License
-------

See [LICENSE](LICENSE) file.

Credits
-------

[Pavlo Chubatyy](https://github.com/Xobb).
