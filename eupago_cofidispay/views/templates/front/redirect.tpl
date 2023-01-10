{*
*  2013-2016 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2023 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div>
	<h3>{l s='Redirect your customer' mod='eupago_cofidispay'}:</h3>
	<ul class="alert alert-info">
			<li>{l s='This action should be used to redirect your customer to the website of your payment processor' mod='eupago_cofidispay'}.</li>
	</ul>
	
	<div class="alert alert-warning">
		{l s='You can redirect your customer with an error message' mod='eupago_cofidispay'}:
		<a href="{$link->getModuleLink('eupago_cofidispay', 'redirect', ['action' => 'error'], true)|escape:'htmlall':'UTF-8'}" title="{l s='Look at the error' mod='eupago_cofidispay'}">
			<strong>{l s='Look at the error message' mod='eupago_cofidispay'}</strong>
		</a>
	</div>
	
	<div class="alert alert-success">
		{l s='You can also redirect your customer to the confirmation page' mod='eupago_cofidispay'}:
		<a href="{$link->getModuleLink('eupago_cofidispay', 'confirmation', ['cart_id' => $cart_id, 'secure_key' => $secure_key], true)|escape:'htmlall':'UTF-8'}" title="{l s='Confirm' mod='eupago_cofidispay'}">
			<strong>{l s='Go to the confirmation page' mod='eupago_cofidispay'}</strong>
		</a>
	</div>
</div>
