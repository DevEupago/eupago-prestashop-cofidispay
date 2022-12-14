{*
*  2013-2016 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2016 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<style type="text/css">
{literal}

.frame {
    display: flex;
    /**
    Uncomment 'justify-content' below to center horizontally.
    ✪ Read below for a better way to center vertically and horizontally.
    **/

    /* justify-content: center; */
    align-items: center;
}

img {
    height: auto;

    /**
    ✪ To center this image both vertically and horizontally,
    in the .frame rule above comment the 'justify-content'
    and 'align-items' declarations,
    then uncomment 'margin: auto;' below.
    **/

    /* margin: auto; */
}

{/literal}
</style>

<div class="panel panel2">
	<div class="row eupago_cofidispay-header">
		<div class="col-xs-6 col-md-4 text-center">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/V3.svg" class="col-xs-6 col-md-4 text-center" id="payment-logo" />
		</div>
		<div class="col-xs-6 col-md-4 text-center header2">
			<h4>{l s='I pay...' mod='eupago_cofidispay'}</h4>
			<h4>{l s='therefore I am' mod='eupago_cofidispay'}</h4>
		</div>
		<div class="col-xs-12 col-md-4 text-center header2">
			<a href="http://www.eupago.pt/registo?lang=en&prestashop#registo_form" target="black" class="btn btn-primary" id="create-account-btn">{l s='Create an account now!' mod='eupago_cofidispay'}</a><br />
			{l s='Already have an account?' mod='eupago_cofidispay'}<a href="https://eupago.pt/clientes/users/login" target="blank" > {l s='Log in' mod='eupago_cofidispay'}</a>
		</div>
	</div>

	<hr />
	
	<div class="eupago_cofidispay-content">
		<div class="row">
			<div class="col-md-7">
				<h5>{l s='About us' mod='eupago_cofidispay'}</h5>
				<p>
					{l s='Eupago is a Portuguese payment institution, accredited and supervised by the Central Bank of Portugal and specialised in providing online solutions of payments, thus being the ideal solution for any e-commerce business, from companies to private sales.' mod='eupago_cofidispay'}
				</p>
				<h5>{l s='Our payment modules and gateways' mod='eupago_cofidispay'}</h5>
				<ul class="ul-spaced">
					<li>
						<strong>{l s='Multibanco' mod='eupago_cofidispay'}:</strong>
						{l s='The local payment method most used in Portugal' mod='eupago_cofidispay'}
					</li>
					
					<li>
						<strong>{l s='Payshop' mod='eupago_cofidispay'}:</strong>
						{l s='Portuguese payment method that allow your costumer pay the order in one payshop agent.' mod='eupago_cofidispay'}
					</li>
					
					<li>
						<strong>{l s='Pagaqui' mod='eupago_cofidispay'}:</strong>
						{l s='Portuguese payment method that allow your costumer pay the order in one pagaqui agent.' mod='eupago_cofidispay'}
					</li>
					
					<li>
						<strong>{l s='MBWAY' mod='eupago_cofidispay'}:</strong>
						{l s='An innovative payment solution that allows customers to make payments using your smartphone or tablet.' mod='eupago_cofidispay'}
					</li>
					
					<li>
						<strong>{l s='PaySafeCard' mod='eupago_cofidispay'}:</strong>
						{l s='paysafecard is the world‘s leading provider of prepaid payment solutions for the internet. paysafecard allows customers to pay online as simply and safely as using cash.' mod='eupago_cofidispay'}
					</li>
					
					<li>
						<strong>{l s='Credit Card' mod='eupago_cofidispay'}:</strong>
						{l s='Allow payments in all SEPA region. Secure, easy and fast.' mod='eupago_cofidispay'}
					</li>

					<li>
						<strong>{l s='Cofidis Pay' mod='eupago_cofidispay'}:</strong>
						{l s='Allow customers to pay up to 12 times in amounts tailored to their needs with total control, flexibility and security.' mod='eupago_cofidispay'}
					</li>
				</ul>
			</div>
			
			<div class="col-md-5">
				<h5>{l s='Check our backoffice' mod='eupago_cofidispay'}</h5>
				<iframe width="100%" height="315" src="https://www.youtube.com/embed/TKP4XZrgj9Q" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>

		<hr />
		
		<div class="row">
			<div class="col-md-12">
				<div class="row frame">
					<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logospagamentos.png" class="col-md-6" id="payment-logo" />
					<img src="{$module_dir|escape:'html':'UTF-8'}views/img/cofidispay.png" class="col-md-1" id="payment-logo2" />
					<div class="col-md-5 text-center">
						<h6>{l s='If you want to know more about us and our payment gateways, feel free to visit us in ' mod='eupago_cofidispay'}<a href="https:www.eupago.pt">www.eupago.pt </a>{l s='or' mod='eupago_cofidispay'}{l s=' call +351 222 061 597' mod='eupago_cofidispay'} {l s='or email us to' mod='eupago_cofidispay'} <a href="mailto:geral@eupago.pt">geral@eupago.pt</a></h6>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>