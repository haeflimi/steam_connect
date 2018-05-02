<?php

namespace Concrete\Package\SteamAuthentication\Authentication\Steam;

defined('C5_EXECUTE') or die('Access Denied');
require('openid.php');

use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use User;
use Core;
use URL;
use Symfony\Component\HttpFoundation\RedirectResponse;
use LightOpenID;

class Controller extends GenericOauth2TypeController
{
    protected $openid;
    protected $openid_identity = 'http://steamcommunity.com/openid';
    protected $authenticationTypeImage = '';

    public function registrationGroupID()
    {
        return \Config::get('auth.steam.registration.group');
    }

    public function supportsRegistration()
    {
        return \Config::get('auth.steam.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-steam"></i>';
    }

    public function getHandle()
    {
        return 'steam';
    }

    public function view()
    {

    }

    public function handle_authentication_attempt()
    {
        $this->openid  = new LightOpenID( BASE_URL );
        $this->openid->returnUrl = (string)URL::to('/ccm/system/authentication/oauth2/steam/callback');
        $this->openid->identity = $this->openid_identity;
        $url = $this->openid->authUrl();
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_authentication_callback()
    {
        $user = new User();
        if ($user && !$user->isError() && $user->isLoggedIn()) {
            $this->handle_attach_callback();
        }

        $this->openid  = new LightOpenID( BASE_URL );
        if(!$this->openid->data['openid_identity']){
            $this->showError(t('Failed authentication'));
            exit;
        }

        $id = $this->extractSteamId($this->openid);

        if ($id) {
            $user_id = $this->getBoundUserID($id);
            if ($user_id && $user_id > 0) {
                $user = \User::loginByUserID($user_id);
                if ($user && !$user->isError()) {
                    $this->redirect(URL::to('/'));
                }
            }
        }
        $this->showError(t('Failed to complete authentication.'));
        exit;
    }

    public function handle_attach_attempt()
    {
        $this->openid  = new LightOpenID( BASE_URL );
        $this->openid->returnUrl = (string)URL::to('/ccm/system/authentication/oauth2/steam/attach_callback');
        $this->openid->identity = $this->openid_identity;
        $url = $this->openid->authUrl();
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_attach_callback()
    {
        $this->openid  = new LightOpenID( BASE_URL );
        if(!$this->openid->data['openid_identity']){
            $this->showError(t('Failed authentication'));
            exit;
        }

        $id = $this->extractSteamId($this->openid);

        $user = new User();
        if (!$user->isLoggedIn()) {
            id(new RedirectResponse(\URL::to('')))->send();
            exit;
        }

        if ($id) {
            if ($this->bindUser($user, $id)) {
                $this->redirect('/account/edit_profile', 'account_attached', 'Steam');
                exit;
            }
        }
        $this->showError(t('Unable to attach user.'));
        exit;
    }

    private function extractSteamId($openid){
        $id = $openid->data['openid_identity'];
        $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
        preg_match($ptn, $id, $matches);
        return $matches[1];
    }

    public function getUniqueId($u = false){
        if(!$u)$u = new User;
        $qb = \Database::connection()->createQueryBuilder();
        $qb->select('oum.binding')
            ->from('OauthUserMap', 'oum')
            ->where('oum.namespace = ?')
            ->andWhere('oum.user_id = ?')
            ->setParameters([$this->getHandle(), $u->getUserID()]);
        $result = $qb->execute();
        return $result->fetchColumn();
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.steam.apikey', $args['apikey']);
        \Config::save('auth.steam.registration.enabled', (bool) $args['registration_enabled']);
        \Config::save('auth.steam.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.steam.apikey'));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }
}
