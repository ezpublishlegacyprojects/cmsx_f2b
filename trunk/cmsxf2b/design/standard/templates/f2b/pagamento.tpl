{*?template charset=utf-8?*}
<h1>Informações sobre o pagamento</h1>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">
<p>
{'Name'|i18n('design/standard/shop')}: {$account_information.nome|wash}<br />
{'Email'|i18n('design/standard/shop')}: {$account_information.email|wash}<br />
{'Email'|i18n('design/standard/shop')} alternativo: {$account_information.email2|wash}<br />
{$account_information.id_type|wash|upcase}: {$account_information.id|wash}<br />
</p>

<p>
<b>Telefones:</b>
</p>
<p>
Principal: {$account_information.tel_ddd|wash} {$account_information.tel_numero|wash}<br />
Comercial: {$account_information.tel_ddd_com|wash} {$account_information.tel_numero_com|wash}<br />
Celular: {$account_information.tel_ddd_cel|wash} {$account_information.tel_numero_cel|wash}<br />
<p>

</td>
<td valign="top">

<p>
<b>{"Address"|i18n("design/standard/shop")}</b>
</p>
<p>
Endereço: {$account_information.logradouro|wash}<br />
Número: {$account_information.numero|wash}<br />
Complemento: {$account_information.complemento|wash}<br />
Bairro: {$account_information.bairro|wash}<br />
Cidade: {$account_information.cidade|wash}<br />
Estado: {$account_information.estado|wash}<br />
CEP: {$account_information.cep|wash}<br />
</p>
</td>
</tr>
<tr>
<td valign="top">
{if is_array( $cobranca )}
<img src={'f2b.jpg'|ezimage} width="42" height="35" alt="F2b" />
	<p>
	Pague agora mesmo: <a href="{$cobranca.url}" target="_blank" onclick="window.open ('{$cobranca.url}','jan','toolbar=no,location=no,menubar=no,resizable=no,scrollbars=yes,width=650,height=600'); return false">Pagamento F2b</a><br />
	Número: {$cobranca.numero|wash}<br />
	Cliente f2b: {$cobranca.cliente|wash}<br />
	Tipo de pagamento: {$cobranca.tipo|wash}<br />
</p>
{else}
	<div class="warning">
	    <h2>Problema de comunicação com o meio de pagamento</h2>
	    <p>Clique em continuar mais uma vez, caso não obtenha sucesso, contate o administrador do site</p>
	   <br />
	</div>
{/if}
</td>
</tr>
</table>




<form method="post" action="">

<div class="buttonblock">
    <input class="defaultbutton" type="submit" name="ContinueButton" value="{"Continue"|i18n( 'design/standard/shop')}" />
</div>

</form>