{*?template charset=utf-8?*}
<div class="maincontentheader">
<h1>{"Select gateway"|i18n("design/standard/workflow")}</h1>
</div>

<form method="post" action={"shop/checkout"|ezurl}>
	{if $tipos|contains( 'boleto' )}
	    <input type="radio" name="tipo" value="boleto" />Boleto Bancário
	{/if}
	{if $tipos|contains( 'transferencia' )}
        <input type="radio" name="tipo" value="transferencia" />Transferência Online
    {/if}
    {if $tipos|contains( 'credito' )}
        <input type="radio" name="tipo" value="credito" />Cartão de Crédito
    {/if}
    {if $tipos|contains( 'debito' )}
        <input type="radio" name="tipo" value="debito" />Cartão de Débito
	{/if}
	<div class="warning">
	    <p>Clique em "Selecionar" e aguarde, o meio de pagamento pode demorar alguns instantes</p>
	   <br />
	</div>	
	<br />
	<br />
    <div class="buttonblock">
        <input class="button" type="submit" name="SelectButton"  value="{'Select'|i18n('design/standard/workflow')}" />
        <input class="button" type="submit" name="CancelButton"  value="{'Cancel'|i18n('design/standard/workflow')}" />
    </div>
</form>