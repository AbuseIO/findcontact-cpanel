<?php

namespace AbuseIO\FindContact;

use AbuseIO\Models\Account;
use AbuseIO\Models\Contact;
use Log;

/**
 * Class Cpanel
 * @package AbuseIO\FindContact
 */
class Cpanel
{
    private $finder;

    /**
     * Cpanel constructor.
     */
    public function __construct()
    {
        $this->initFinder();
    }


    /**
     * Get the abuse email address registered for this ip.
     * @param  string $ip IPv4 Address
     * @return mixed Returns contact object or false.
     */
    public function getContactByIp($ip)
    {
        $result = false;

        try {
            $result = $this->_getContactWithData(
                $this->_getContactDataForIp($ip)
            );
        } catch (\Exception $e) {
            Log::debug("Error while talking to the Cpanel Stat API : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this domain.
     * @param  string $domain Domain name
     * @return mixed Returns contact object or false.
     */
    public function getContactByDomain($domain)
    {
        $result = false;

        try {
            $result = $this->getContactWithData(
                $this->_getContactDataForDomain($domain)
            );
        } catch (\Exception $e) {
            Log::debug("Error while talking to the Cpanel Stat API : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this ip.
     * @param  string $id ID/Contact reference
     * @return mixed Returns contact object or false.
     */
    public function getContactById($id)
    {
        return false;
    }

    /**
     * search the ip using the plesk api and if found, return the abuse mailbox and network name
     *
     * @param $ip
     * @return array
     */
    private function _getContactDataForIp($ip)
    {
        return $this->getContactDataForQuery('ip', $ip);
    }

    /**
     * search the domain using the plesk api and if found, return the abuse mailbox and network name
     *
     * @param $ip
     * @return array
     */
    private function _getContactDataForDomain($domain)
    {
        return $this->getContactDataForQuery('domain', $domain);
    }

    /**
     * @throws \Exception
     */
    private function initFinder()
    {
        $host = config("Findcontact.findcontact-cpanel.host");
        $username = config("Findcontact.findcontact-cpanel.username");
        $auth_type = config("Findcontact.findcontact-cpanel.auth_type") ? config("Findcontact.findcontact-plesk.auth_type") : 'hash';
        $password = config("Findcontact.findcontact-cpanel.password");

        if (empty($host)) {
            throw new \Exception('please set the host in the config of cpanel findcontact');
        }

        if (empty($username)) {
            throw new \Exception('please set the username in the config of cpanel findcontact');
        }

        if (empty($password)) {
            throw new \Exception('please set the passwrd in th config of cpanel findcontact');
        }


        $this->finder = new \Gufy\CpanelPhp\Cpanel([
            'host'      => $host,
            'username'  => $username,
            'auth_type' => $auth_type,
            'password'  => $password,
        ]);
    }

    /**
     * @param $data
     * @return Contact
     */
    private function getContactWithData($data)
    {
        // construct new contact
        $result = new Contact();
        $result->name = $data['name'];
        $result->reference = $data['name'];
        $result->email = $data['email'];
        $result->enabled = true;
        $result->auto_notify = config("Findcontact.findcontact-cpanel.auto_notify");
        $result->account_id = Account::getSystemAccount()->id;
        $result->api_host = '';
        return $result;
    }

    private function getContactDataForQuery($field, $value)
    {
        $data = [];
        $name = null;
        $email = null;

        $userInfo = $this->finder->listaccts(['searchtype' => $field, 'search' => '', 'exact', 'search' => $value]);

        // only create a result data if both email and name are set
        if (!is_null($userInfo->name) && !is_null($userInfo->email)) {
            $data['name'] = $userInfo->name;
            $data['email'] = $userInfo->email;
        }

        return $data;
    }
}
