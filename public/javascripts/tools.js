function performCurrencyChange()
{
  var selectCurrency = document.getElementById("currency");
  var index	      = selectCurrency.selectedIndex;
  var currencyValue   = selectCurrency.options[index].value;
   
  var callURL="<?php echo $this->url(array('lang'=>$translate->getLocale(),
					   'controller'=>'credits',
					   'action'=>'selection'),
			                   'default', null, true);?>";
  
 // var form = document.getElementById("productform");
  document.productform.action=callURL;
  document.productform.submit();

  
  
}