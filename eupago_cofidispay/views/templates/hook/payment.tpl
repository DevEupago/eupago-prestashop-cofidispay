{*
*  2013-2016 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2023 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="row">
	<div class="col-xs-12">
		<p class="payment_module" id="eupago_cofidispay_payment_button">
			{if $cart->getOrderTotal() >= 2}
				<a href="{$link->getModuleLink('eupago_cofidispay', 'redirect', array(), true)|escape:'htmlall':'UTF-8'}" title="{l s='Pay with my payment module' mod='eupago_cofidispay'}">
					<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/cards.png" alt="{l s='Pay with credit card' mod='eupago_cofidispay'}" />
					{l s='Pay with credit card' mod='eupago_cofidispay'}
				</a>
			{/if}
		</p>
	</div>
</div>
