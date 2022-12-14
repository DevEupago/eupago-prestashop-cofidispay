{*
*  2013-2022 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2022 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="panel">
	<div class="row eupago_cofidispay-header">
		<div class="col-xs-6 col-md-4 text-center">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/V3.svg" class="col-xs-6 col-md-4 text-center" id="payment-logo" />
		</div>
		<div class="col-xs-6 col-md-4 text-center header2">
			<h4>{l s='Eupago - payment solutions' mod='eupago_cofidispay'}</h4>
			<h4>{l s='Cofidis Pay payment´s' mod='eupago_cofidispay'}</h4>
		</div>
		<div class="col-xs-12 col-md-4 text-center header2">
			<a href="http://www.eupago.pt/registo?lang=en&prestashop#registo_form" target="black" class="btn btn-primary" id="create-account-btn">{l s='Create an account now!' mod='eupago_cofidispay'}</a><br />
			{l s='Already have an account?' mod='eupago_cofidispay'}<a href="https://eupago.pt/clientes/users/login" target="blank" > {l s='Log in' mod='eupago_cofidispay'}</a>
		</div>
	</div>

	<hr />
	
	<div class="eupago_cofidispay-content">
		<div class="row">
			<div class="col-md-6">
				<h5>{l s='Eupago Cofidis Pay payment offers the following benefits' mod='eupago_cofidispay'}</h5>
				<dl>
					<dt>&middot; {l s='Cofidis Pay is a fractional payment solution' mod='eupago_cofidispay'}</dt>
					<dd>{l s='Allow customers to pay up to 12 times in amounts tailored' mod='eupago_cofidispay'}</br>{l s='to their needs with total control, flexibility and security.' mod='eupago_cofidispay'}</dd>
					
 					<dt>&middot; {l s='Enhanced security' mod='eupago_cofidispay'}</dt>
					<dd>{l s='Multiple firewalls, encryption protocols and fraud protection.' mod='eupago_cofidispay'}</dd>
				</dl>
			</div>
			
			<div class="col-md-6">
				<h5>{l s='Check our backoffice' mod='eupago_cofidispay'}</h5>
				<iframe width="100%" height="315" src="https://www.youtube.com/embed/aZ2nrbsU20A" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>

		<hr />
		
		<div class="row">
			<div class="col-md-12">
				<h4>{l s='Accept payments with:' mod='eupago_cofidispay'}</h4>
				
				<div class="row">
					<img style="max-width:150px;" src="{$module_dir|escape:'html':'UTF-8'}views/img/cofidispay.png" class="col-md-6" id="payment-logo" />
					<div class="col-md-6">
						<p class="text-branded">{l s='Call +351 222 061 597 if you have any questions or need more information!' mod='eupago_cofidispay'}</br>
						<a class="link" href="https://www.cofidis.pt/cofidispay" target="blank">{l s='What is Cofidis Pay?' mod='eupago_cofidispay'}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
