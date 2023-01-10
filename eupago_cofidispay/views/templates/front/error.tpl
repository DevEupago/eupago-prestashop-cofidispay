{*
*  2013-2016 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2023 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div>
	<h3>{l s='An error occurred' mod='eupago_cofidispay'}:</h3>
	<ul class="alert alert-danger">
		{foreach from=$errors item='error'}
			<li>{$error|escape:'htmlall':'UTF-8'}.</li>
		{/foreach}
	</ul>
</div>