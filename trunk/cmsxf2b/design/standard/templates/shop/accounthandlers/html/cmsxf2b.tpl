{*?template charset=utf-8?*}
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">
<p>
{'Name'|i18n('design/standard/shop')}: {$order.account_information.nome|wash}<br />
{'Email'|i18n('design/standard/shop')}: {$order.account_information.email|wash}<br />
{'Email'|i18n('design/standard/shop')} alternativo: {$order.account_information.email2|wash}<br />
{$order.account_information.id_type|wash|upcase}: {$order.account_information.id|wash}<br />
</p>

<p>
<b>Telefones:</b>
</p>
<p>
Principal: {$order.account_information.tel_ddd|wash} {$order.account_information.tel_numero|wash}<br />
Comercial: {$order.account_information.tel_ddd_com|wash} {$order.account_information.tel_numero_com|wash}<br />
Celular: {$order.account_information.tel_ddd_cel|wash} {$order.account_information.tel_numero_cel|wash}<br />
<p>

</td>
<td valign="top">

<p>
<b>{"Address"|i18n("design/standard/shop")}</b>
</p>
<p>
Endereço: {$order.account_information.logradouro|wash}<br />
Número: {$order.account_information.numero|wash}<br />
Complemento: {$order.account_information.complemento|wash}<br />
Bairro: {$order.account_information.bairro|wash}<br />
Cidade: {$order.account_information.cidade|wash}<br />
Estado: {$order.account_information.estado|wash}<br />
CEP: {$order.account_information.cep|wash}<br />
</p>
</td>
</tr>
</table>