{*?template charset=utf-8?*}
{"Customer information"|i18n('design/standard/shop')}:

{'Name'|i18n('design/standard/shop')}: {$order.account_information.nome|wash}
{'Email'|i18n('design/standard/shop')}: {$order.account_information.email|wash}
{'Email'|i18n('design/standard/shop')} alternativo: {$order.account_information.email2|wash}
{$order.account_information.id_type|wash|upcase}: {$order.account_information.id|wash}

Telefones:

Principal: {$order.account_information.tel_ddd|wash} {$order.account_information.tel_numero|wash}
Comercial: {$order.account_information.tel_ddd_com|wash} {$order.account_information.tel_numero_com|wash}
Celular: {$order.account_information.tel_ddd_cel|wash} {$order.account_information.tel_numero_cel|wash}

{"Address"|i18n("design/standard/shop")}

Endereço: {$order.account_information.logradouro|wash}
Número: {$order.account_information.numero|wash}
Complemento: {$order.account_information.complemento|wash}
Bairro: {$order.account_information.bairro|wash}
Cidade: {$order.account_information.cidade|wash}
Estado: {$order.account_information.estado|wash}
CEP: {$order.account_information.cep|wash}