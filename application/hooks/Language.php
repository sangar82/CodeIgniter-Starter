<?php
class Language {

    function get_language() {

        $config =& get_config();

        $subdomain = $this->get_subdomain();
                
        if ( $subdomain )
        {
            $config['language']=$config['languages'][$subdomain];
            $config['base_url'] = "http://$subdomain.".$config['base_domain']; 
        }        

    }

    function get_subdomain()
    {
        $url = $_SERVER['HTTP_HOST'];

        $parsedUrl = parse_url($url);

        $host = explode('.', $parsedUrl['path']);

        $subdomain = $host[0];

        $config =& get_config();

        if (array_key_exists($subdomain, $config['languages']))
        {
            return $subdomain;
        }
        else
        {
            return false;
        }

    }

}  