<div class="tl_sjGMapWizard" id="ctrl_<?php echo $this->strId; ?>" <?php echo $this->attributes; ?> >

	
  <?php echo $this->fields; ?>

  <?php if ($this->blnShowAddressButton): ?>
  <br /><br />

  <button id='sjGetGeocodes' name='btnGeoCodes' /><?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['encodeAddress']; ?></button>
  <?php endif; ?>
  
  
  <div id="begmap" style="border: 1px solid #000000; width: 98%; height: 240px; margin-top: 20px;"></div>
	
	
  <script>
  	<!--//--><![CDATA[//><!--
  	var GMapIsClickable = <?php echo $this->mapIsClickable ? 'true' : 'false'; ?>;
  	var fieldName = "<?php echo $this->strFieldName; ?>";
  	var strWrongAddress = "<?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['wrongAddress']; ?>";
  	var strKoordinates  = "<?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['geocodes'][0]; ?>";
  	var strAddress  = "<?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['address']; ?>";
  	var strCountry  = "<?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['country'][0]; ?>";
  	var strUseAddress = "<?php echo $GLOBALS['TL_LANG']['sjGMapWizard']['useAddress']; ?>";
  	<?php if (strlen($this->mapCenter)) echo 'var ptMapCenter = new GLatLng(' . $this->mapCenter . ');'; ?>
  	//--><!]]>
  </script>
	
</div>