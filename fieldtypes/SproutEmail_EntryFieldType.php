<?php
namespace Craft;

class SproutEmail_EntryFieldType extends BaseFieldType
{
	/**
	 * Field Type name
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t( 'SproutEmail Entry' );
	}
	
	/**
	 * Define database column
	 *
	 * @return false
	 */
	public function defineContentAttribute()
	{
		return AttributeType::String;
	}

	/**
	 * getInputHtml function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @param mixed $value
	 * @return void
	 */
	public function getInputHtml($name, $value)
	{
		// Set up an array of the defaults
		$campaignRecord = new SproutEmail_CampaignModel();
		$campaign = SproutEmail_CampaignModel::populateModel( $campaignRecord );

		$fields = array(
			"campaign"                => $campaign,
			"emailProviderRecipientListId"  => "",
			"enabled"                       => FALSE,
		);

		if(!$sectionCampaign)
		{
			// The section isn't email enabled
			return $fields;
		}
		else
		{
		// Its enabled 
			$fields["enabled"] = TRUE;
							
			// Get the RecipientListId
			$list = craft()->sproutEmail_campaign->getCampaignRecipientLists($sectionCampaign["id"] );
			
			$fields["campaign"]["emailProviderRecipientListId"] = array();
			if(isset($list))
			{
				$arr = array();
				foreach($list as $recipients)
				{
					$arr[] = $recipients->emailProviderRecipientListId;
				}
				$fields["campaign"]["emailProviderRecipientListId"] = $arr;
			}

			// Merge on the 
			$keys = array('fromName','fromEmail','replyToEmail','emailProvider');
			foreach($keys as $val)
			{
				$fields["campaign"][$val] = $sectionCampaign[$val];    
			}
		}
	
		// Merge on the entry level settings 
		$value = json_decode($value,TRUE);
		
		if(!empty($value))
		{
			foreach($value as $key=>$val)
			{
				if($key == 'emailProviderRecipientListId')
				{
					if(is_string($val))
					{
						$val = (array) $val;
					}
				}
				$fields["campaign"][$key] = $val;
			}
		}

		return craft()->templates->render('/sproutemail/_cp/fieldtypes/entry/input', array(
			'name'      => $name,
			'value'     => $fields
		));
	}

	public function prepValue($value)
	{
		return $value;
	}

	/**
	 * prepValueFromPost function.
	 * 
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function prepValueFromPost($value)
	{   
		$overrideFields = craft()->request->getPost('fields.sproutEmail');

		return json_encode($overrideFields);
	}

	protected function defineSettings()
	{
		return array(
			'initialSlots' => array(AttributeType::Number, 'min' => 0)
		);
	}
	
	public function getSettingsHtml()
	{
		return craft()->templates->render('/sproutemail/_cp/fields/entry/settings', array(
			'settings' => $this->getSettings()
		));
	}
	
}