<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

 /**
  * PHP version 5
  * @copyright  Stephan Jahrling (Stephan Jahrling - Softwarelösungen), 2011
  * @author     Stephan Jahrling <info@jahrling-software.de>
  * @license    commercial
  */


/**
 * Class sjGMapWizard
 *
 * Provide methods to handle google-map coordinates
 * @copyright  Stephan Jahrling 2011
 * @author     Stephan Jahrling <info@jahrling-software.de>
 * @package    Controller
 */
class sjGMapWizard extends Widget
{
	
	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';
	
	protected $strSjGMapTemplate = 'be_sjGMapWizard';


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
	
		switch ($strKey)
		{
			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			case 'GMapApiKey':
				$this->arrConfiguration['GMapApiKey'] = $varValue;
				break;
				
			case 'mapCenter':
				$this->arrConfiguration['mapCenter'] = $varValue;
				break;
			
			case 'addressFields':
				if (is_array($varValue))
					$this->arrConfiguration['addressFields'] = $varValue;
				else
					$this->arrConfiguration['addressFields'] = array();
				break;
					
			case 'mandatoryFields':
				if (is_array($varValue))
					$this->arrConfiguration['mandatoryFields'] = $varValue;
				else
					$this->arrConfiguration['mandatoryFields'] = array(0);
				break;
					
			case 'mapIsClickable':
				$this->arrConfiguration['mapIsClickable'] = $varValue;
				break;
					

			default:
				parent::__set($strKey, $varValue);
				break;
		}
		
	}

	/**
	 * Validate input and set value
	 */
	public function validator($varInput)
	{
		
		// 1. check for array
		if (!is_array($varInput))
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
			
			
		// 2. check for mandatory (geocodes-field)
		if ($this->arrConfiguration['mandatory'] && !strlen($varInput['geocodes']))
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
		
		
		// 3. check for mandatory address fields
		if (is_array($this->arrConfiguration['mandatoryFields']) && count($this->arrConfiguration['mandatoryFields']))
			foreach ($this->arrConfiguration['mandatoryFields'] as $field)
				if (!strlen($varInput[$field]))
					$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $GLOBALS['TL_LANG']['sjGMapWizard'][$field][0]));
		
		
		return $varInput;
		
	}
	
	
	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		if (is_array($GLOBALS['TL_JAVASCRIPT']))
		{
			array_insert($GLOBALS['TL_JAVASCRIPT'], 1, 'system/modules/sjGMapWizard/html/js/sjGMapWizard.js');
			array_insert($GLOBALS['TL_JAVASCRIPT'], 1, 'http://maps.google.com/maps?file=api&v=2&sensor=false&key=' . $this->arrConfiguration['GMapApiKey']);
		}
		else
		{
			$GLOBALS['TL_JAVASCRIPT'] = array('system/modules/sjGMapWizard/html/js/sjGMapWizard.js');
			$GLOBALS['TL_JAVASCRIPT'] = array('http://maps.google.com/maps?file=api&v=2&sensor=false&key=' . $this->arrConfiguration['GMapApiKey']);
		}


		
		$objTemplate = new BackendTemplate($this->strSjGMapTemplate);
		
		
		$arrAddressFields = array(
			'street' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['sjGMapWizard']['street'],
				'inputType'               => 'text',
				'eval'                    => array('mandatory'=>(is_array($this->arrConfiguration['mandatoryFields']) && in_array('street', $this->arrConfiguration['mandatoryFields'])), 'maxlength'=>255, 'tl_class'=>'w50')
			),
			'postal' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['sjGMapWizard']['postal'],
				'inputType'               => 'text',
				'eval'                    => array('mandatory'=>(is_array($this->arrConfiguration['mandatoryFields']) && in_array('postal', $this->arrConfiguration['mandatoryFields'])), 'rgxp'=>'digit', 'maxlength'=>5, 'tl_class'=>'w50')
			),
			'city' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['sjGMapWizard']['city'],
				'inputType'               => 'text',
				'eval'                    => array('mandatory'=>$this->arrConfiguration['mandatory'], 'maxlength'=>255, 'tl_class'=>'w50')
			),
			'country' => array
			(
				'label'					  => &$GLOBALS['TL_LANG']['sjGMapWizard']['country'],
				'inputType'               => 'select',
				'options'                 => $this->getCountries(),
				'eval'                    => array('mandatory'=>$this->arrConfiguration['mandatory'], 'includeBlankOption'=>false, 'tl_class'=>'w50'),
			),
			'geocodes' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['sjGMapWizard']['geocodes'],
				'inputType'               => 'text',
				'eval'                    => array('maxlength'=>255, 'tl_class'=>'clr', 'mandatory'=>$this->arrConfiguration['mandatory'])
			)
		);
		
		
		foreach ($arrAddressFields as $field => $arrData)
		{
			
			if ((!is_array($this->arrConfiguration['addressFields']) || !in_array($field, $this->arrConfiguration['addressFields'])) && $field !== 'geocodes')
				continue;
				
			
			$strClass = $GLOBALS['BE_FFL'][$arrData['inputType']];
			
			$arrData['decodeEntities'] = true;
			$arrData['eval']['required'] = $arrData['eval']['mandatory'];
			
			
			
			$objWidget = new $strClass($this->prepareForWidget($arrData, $this->strName . '[' . $field . ']', (strlen($this->value[$field])) ? $this->value[$field] : $this->arrConfiguration['defaultAddress'][$field]));
			if (is_array($arrData['attributes']))
				$objWidget->addAttributes($arrData['attributes']);
				
			$output .= $objWidget->parse();
				
		}
		
		
		$objTemplate->fields = $output;
		$objTemplate->mapIsClickable = $this->arrConfiguration['mapIsClickable'] ? true : false;
		$objTemplate->mapCenter = $this->arrConfiguration['mapCenter'];
		$objTemplate->strFieldName = $this->strName;
		$objTemplate->blnShowAddressButton = (is_array($this->arrConfiguration['addressFields']) && in_array('city', $this->arrConfiguration['addressFields']) && in_array('country', $this->arrConfiguration['addressFields'])) ? true : false;
		
		
		return $objTemplate->parse();
	}
}
  
  
?>