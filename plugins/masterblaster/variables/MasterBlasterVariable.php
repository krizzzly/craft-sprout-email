<?php
namespace Craft;

/**
 * Main MasterBlaster variable interface
 *
 */
class MasterBlasterVariable
{
	
    /**
     * Plugin Name
     * Make your plugin name available as a variable
     * in your templates as {{ craft.YourPlugin.name }}
     *
     * @return string
     */
    public function getName()
    {
        $plugin = craft()->plugins->getPlugin('masterblaster');

        return $plugin->getName();
    }

    /**
     * Get All Campaigns
     * By default, this returns all campaigns that are normal campaigns.  If you want
     * to get only Section-based campaigns, pass 'true' to the $sectionId paramenter
     * @todo  - make this more intuitive, kinda clunky parameter names
     * 
     * @return mixed Campaign model
     */
    public function getCampaigns()
    {
    	return craft()->masterBlaster->getAllCampaigns();
    }
    
    /**
     * Get All Section Based Campaigns
     * By default, this returns all campaigns that are normal campaigns.  If you want
     * to get only Section-based campaigns, pass 'true' to the $sectionId paramenter
     * @todo  - make this more intuitive, kinda clunky parameter names
     *
     * @return mixed Campaign model
     */
    public function getSectionCampaigns()
    {
    	//Craft::dump(craft()->getBaseUrl(true));die();
    	return craft()->masterBlaster->getSectionCampaigns();
    }

    /**
     * Get a Campaign by id     *
     * @param  int    $campaignId
     * @return object campaign record
     */
    public function getCampaignById($campaignId)
    {
        return craft()->masterBlaster->getCampaign(array('id' => $campaignId));
    }

    /**
     * Get All Sections for Options dropdown     * 
     * @param  string $indexBy 
     * @return array
     */
    public function getAllSections($indexBy = null)
    {
        $result = craft()->sections->getAllSections($indexBy);

        $options = array(array(
            'label' => 'Select a Section...',
            'value' => ''
        ));

        foreach ($result as $key => $section) 
        {
            array_push($options, array(
                'label' => $section->name,
                'value' => $section->id
            ));
        }
        
        return $options;
    }
    
    /**
     * Get all user groups
     * @param string $indexBy
     * @return array
     */
    public function getAllUserGroups($indexBy = null)
    {
    	$result = craft()->userGroups->getAllGroups($indexBy);
    	$options = array();
    	foreach($result as $key => $group)
    	{
    		$options[$group->id] = $group->name;
    	}
    	return $options;
    }
    
    /**
     * Get subscriber list for specified provider
     * @param string $provider
     * @return array
     */
    public function getSubscriberList($provider = 'masterblaster')
    {
    	$service = 'masterBlaster_' . $provider;
    	return craft()->{$service}->getSubscriberList();
    }

	/**
	 * Get templates
	 * @return array
	 */
    public function getTemplatesDirListing()
    {
		return craft()->masterBlaster->getTemplatesDirListing();
    }
    
    /**
     * Get email providers
     * @return array
     */
    public function getEmailProviders()
    {
		return craft()->masterBlaster_emailProvider->getEmailProviders();
    }

    /** 
     * Get notifications
     * @return array
     */
    public function getNotifications()
    {
    	return craft()->masterBlaster->getNotifications();
    }
    
    /**
     * Get notification events
     * @param string $notificationEvent
     * @param bool $return_full_objects
     * @return array
     */
    public function getNotificationEvents($notificationEvent = null, $return_full_objects = false)
    {
    	// we'll use this opportunity to clean up and register plugin registration events;
    	// although this is more of an 'install' type script, doing it here limits
    	// its execution and keeps the events fresh
    	craft()->masterBlaster_integration->registerEvents();
    	
    	$events = craft()->masterBlaster->getNotificationEvents($notificationEvent);

    	if($return_full_objects)
    	{
    		return $events;
    	}
    	
    	$out = array();
    	foreach($events as $event)
    	{
    		if($event->registrar == 'craft')
    		{
    			$out[str_replace('.', '---', $event->event)] = $event->description;
    		}
    		else 
    		{
    			$out[$event->id] = $event->description;
    		}
    	}
    	
    	return $out;
    }
    
    /**
     * Get notification event for specified id
     * @param int $id
     * @return obj
     */
    public function getNotificationEventById($id)
    {
    	return craft()->masterBlaster->getNotificationEventById($id);
    }
    
    /**
     * Get notification event options
     * @return array
     */
    public function getNotificationEventOptions()
    {    	
    	$res = craft()->masterBlaster->getNotificationEventOptions();
    	
    	$out = array();
    	foreach($res as $key => $template)
    	{
    		if($key == 'plugin_options') continue;
    		list($event, $options) = explode('/', $template);
    		$out['system_options'][$event][] = $options;
    	}

    	if(isset($res['plugin_options']))
    	{    	
    		foreach($res['plugin_options'] as $k=>$v)
    		{
    			$decoded = json_decode($v);
    			if(empty($decoded)) continue;
    			
    			$out['plugin_options'][$k] = (array) json_decode($v);
    		}	
    	}
    	
    	// parse html
    	foreach($out['plugin_options'] as $k=>$v)
    	{
    		$out['plugin_options'][$k] = $v;
    	}

    	return $out;
    }

    /**
     * Twig helper for dumping data
     * @param mixed $mixed
     */
    public function dump($mixed)
    {
    	Craft::dump($mixed);die();
    }
}
