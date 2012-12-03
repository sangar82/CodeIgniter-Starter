<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
 
class MY_Lang extends CI_Lang
{
 function __construct()
 {
  parent::__construct(); 
  $config =& get_config();
  if(isset($config['languages']) && isset($config['redirect_urls']))
  {
   global $URI, $CFG, $IN;
   //var_dump($URI);

   $subdomain = $this->get_subdomain();
   $lang = (isset($subdomain))?$subdomain:'';
   $uri=$URI->segments;
   if(strlen($lang) == 2 && array_key_exists($lang,$config['languages']) == true)
   {
    $config['language']         = $config['languages'][$lang];
    $config['prefix_language']  = $lang;
    $config['base_url']         = "http://$lang.".$config['base_domain'];
   }
   elseif($config['redirect_urls'] == true || strlen($lang) == 2 && array_key_exists($lang,$config['languages']) == false)
   {
    //todo: arreglar
    $url=$config['base_url'];
    $url.=(empty($config['index_page']))?'':$config['index_page'].'/';
    $url.=array_search($config['language'],$config['languages']).'/';
    if(strlen($lang)==2)
    {
     array_shift($uri);
     $url.=implode('/',$uri);
    }else
    {
     $url.=implode('/',$uri);
    }
    header("location: $url");
   }
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