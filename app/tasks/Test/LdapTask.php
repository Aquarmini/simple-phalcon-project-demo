<?php

namespace App\Tasks\Test;

use App\Tasks\Task;
use limx\phalcon\Cli\Color;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;

class LdapTask extends Task
{
    public $baseDn;

    public function onConstruct()
    {
        $this->baseDn = env('LDAP_BASE_DN');
    }

    public function mainAction()
    {
        echo Color::head('Help:'), PHP_EOL;
        echo Color::colorize('  Ldap扩展测试'), PHP_EOL, PHP_EOL;

        echo Color::head('Usage:'), PHP_EOL;
        echo Color::colorize('  php run Test\\\\Ldap [action]', Color::FG_GREEN), PHP_EOL, PHP_EOL;

        echo Color::head('Actions:'), PHP_EOL;
        echo Color::colorize('  user         查询Person方法', Color::FG_GREEN), PHP_EOL;
        echo Color::colorize('  group        查询Group方法', Color::FG_GREEN), PHP_EOL;
    }

    protected function client()
    {
        $adapter = new Adapter(array(
            'host' => env('LDAP_HOST'),
            'port' => env('LDAP_PORT'),
            // 'encryption' => 'tls',
            'options' => array(
                'protocol_version' => 3,
                'referrals' => false,
            ),
        ));
        $ldap = new Ldap($adapter);

        $ldap->bind(env('LDAP_BIND_DN'), env('LDAP_PASSWORD'));

        return $ldap;
    }

    public function userAction()
    {
        $client = $this->client();
        $objectClass = env('LDAP_QUERY_USER');
        $user = $client->query($this->baseDn, "(&(objectClass={$objectClass})(uid=limingxin))")
            ->execute()
            ->toArray();

        $res = $user[0]->getAttributes();
        print_r($res);
    }

    public function groupAction()
    {
        $client = $this->client();
        $objectClass = env('LDAP_QUERY_GROUP');
        $user = $client->query($this->baseDn, "(&(objectClass={$objectClass})(gidNumber=10093))")
            ->execute()
            ->toArray();
        
        $res = $user[0]->getAttributes();
        print_r($res);
    }

}

