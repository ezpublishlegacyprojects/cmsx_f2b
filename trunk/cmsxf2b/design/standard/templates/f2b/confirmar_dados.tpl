{*?template charset=utf-8?*}
<h1>Informações do pagamento</h1>

{if $input_error}
<div class="warning">
<ul>
	{foreach $input_errors as $error}
	 <li>{$error}</li>
	{/foreach}
</ul>
</div>
{/if}

<form method="post" action={"/f2b/confirmardados/"|ezurl}>

<div class="block">
<div class="element">
<label>
{if $is_cpf}Nome{else}Razão Social{/if}:*
</label><div class="labelbreak"></div>
<input class="box" type="text" name="Nome" size="75" value="{$nome|wash}" />
</div>

<div class="element">
	{if $is_cpf}
		<label>CPF: </label>{$cpfcnpj|extract_left( 3 )}.{$cpfcnpj|extract( 3, 3 )}.{$cpfcnpj|extract( 6, 3 )}-{$cpfcnpj|extract( 9, 2 )}
	{else}
		<label>CNPJ: </label>{$cpfcnpj|extract_left( 2 )}.{$cpfcnpj|extract( 2, 3 )}.{$cpfcnpj|extract( 5, 3 )}/{$cpfcnpj|extract( 8, 4 )}-{$cpfcnpj|extract_right( 2 )}
	{/if}
</div>
</div>
<div class="break"></div>
<div class="block">
<div class="element">
<label>
{"Email"|i18n("design/standard/shop")}:*
</label><div class="labelbreak"></div>
<input class="box" type="text" name="EMail" size="45" value="{$email|wash}" />
</div>
<div class="element">
<label>
Email adicional:
</label><div class="labelbreak"></div>
<input class="box" type="text" name="EMail2" size="45" value="{$email2|wash}" />
</div>
</div>
<br />

<div class="break"></div>

<div class="block">
<div class="element">
<label>
Logradouro:*
</label><div class="labelbreak"></div>
<input class="box" type="text" name="Logradouro" size="60" value="{$logradouro|wash}" />
</div>
    <div class="element">
    <label>
    Número:*
    </label><div class="labelbreak"></div>
    <input class="box" type="text" name="Numero" size="4" value="{$numero|wash}" />
    </div>

    <div class="element">
    <label>
    Complemento:
    </label><div class="labelbreak"></div>
    <input class="box" type="text" name="Complemento" size="20" value="{$complemento|wash}" />
    </div>
</div>
<br />
<div class="break"></div>
<div class="block">

    <div class="element">
    <label>
    Bairro:*
    </label><div class="labelbreak"></div>
    <input class="box" type="text" name="Bairro" size="30" value="{$bairro|wash}" />
    </div>
    
    <div class="element">
    <label>
    CEP:*
    </label><div class="labelbreak"></div>
    <input class="box" type="text" name="CEP" size="15" value="{$cep|wash}" />
    </div>
    <div class="element">
    <label>
    Cidade:*
    </label><div class="labelbreak"></div>
    <input class="box" type="text" name="Cidade" size="20" value="{$cidade|wash}" />
    </div>
    <div class="element">
    <label>
    Estado:*
    </label><div class="labelbreak"></div>
    
    <select name="Estado" class="box">
    
		<option value="">&#160;</option>
		
		<option value="AC" {cond( eq( $estado, 'AC'), 'selected="selected"')}>Acre</option>
		<option value="AL" {cond( eq( $estado, 'AL'), 'selected="selected"')}>Alagoas</option>
		<option value="AP" {cond( eq( $estado, 'AP'), 'selected="selected"')}>Amap&#225;</option>
		<option value="AM" {cond( eq( $estado, 'AM'), 'selected="selected"')}>Amazonas</option>
		<option value="BA" {cond( eq( $estado, 'BA'), 'selected="selected"')}>Bahia</option>
		<option value="CE" {cond( eq( $estado, 'CE'), 'selected="selected"')}>Cear&#225;</option>

		<option value="DF" {cond( eq( $estado, 'DF'), 'selected="selected"')}>Distrito Federal</option>
		<option value="ES" {cond( eq( $estado, 'ES'), 'selected="selected"')}>Esp&#237;rito Santo</option>
		<option value="GO" {cond( eq( $estado, 'GO'), 'selected="selected"')}>Goias</option>
		<option value="MA" {cond( eq( $estado, 'MA'), 'selected="selected"')}>Maranh&#227;o</option>
		<option value="MT" {cond( eq( $estado, 'MT'), 'selected="selected"')}>Mato Grosso</option>

		<option value="MS" {cond( eq( $estado, 'MS'), 'selected="selected"')}>Mato Grosso do Sul</option>
		<option value="MG" {cond( eq( $estado, 'MG'), 'selected="selected"')}>Minas Gerais</option>
		<option value="PA" {cond( eq( $estado, 'PA'), 'selected="selected"')}>Par&#225;</option>
		<option value="PB" {cond( eq( $estado, 'PB'), 'selected="selected"')}>Para&#237;ba</option>
		<option value="PR" {cond( eq( $estado, 'PR'), 'selected="selected"')}>Paran&#225;</option>
		<option value="PE" {cond( eq( $estado, 'PE'), 'selected="selected"')}>Pernambuco</option>

		<option value="PI" {cond( eq( $estado, 'PI'), 'selected="selected"')}>Piau&#237;</option>
		<option value="RJ" {cond( eq( $estado, 'RJ'), 'selected="selected"')}>Rio de Janeiro</option>
		<option value="RN" {cond( eq( $estado, 'RN'), 'selected="selected"')}>Rio Grande do Norte</option>
		<option value="RS" {cond( eq( $estado, 'RS'), 'selected="selected"')}>Rio Grande do Sul</option>
		<option value="RO" {cond( eq( $estado, 'RO'), 'selected="selected"')}>Rond&#244;nia</option>
		<option value="RR" {cond( eq( $estado, 'RR'), 'selected="selected"')}>Roraima</option>

		<option value="SC" {cond( eq( $estado, 'SC'), 'selected="selected"')}>Santa Catarina</option>
		<option value="SP" {cond( eq( $estado, 'SP'), 'selected="selected"')}>S&#227;o Paulo</option>
		<option value="SE" {cond( eq( $estado, 'SE'), 'selected="selected"')}>Sergipe</option>
		<option value="TO" {cond( eq( $estado, 'TO'), 'selected="selected"')}>Tocantins</option>

	</select>

    </div>    
  
</div>

<div class="break"></div>
<br />

<div class="block">
<div class="element">
<label>Telefone principal:*</label>
    <div class="element">
		DDD
   	 	<input class="halfbox" type="text" name="DDD" size="1" value="{$tel_ddd|wash}" />
    </div>
    <div class="element">
   	 	Número:
    	<input class="box" type="text" name="Telefone" size="7" value="{$tel_numero|wash}" />
    </div>        
</div>

<div class="element">
<label>Comercial:</label>
    <div class="element">
		DDD
   	 	<input class="halfbox" type="text" name="DDD_com" size="1" value="{$tel_ddd_com|wash}" />
    </div>
    <div class="element">
   	 	Número:
    	<input class="box" type="text" name="Telefone_com" size="7" value="{$tel_numero_com|wash}" />
    </div>        
</div>

<div class="element">
<label>Celular:</label>
    <div class="element">
		DDD
   	 	<input class="halfbox" type="text" name="DDD_cel" size="1" value="{$tel_ddd_cel|wash}" />
    </div>
    <div class="element">
   	 	Número:
    	<input class="box" type="text" name="Telefone_cel" size="7" value="{$tel_numero_cel|wash}" />
    </div>        
</div>
</div>

<div class="break"></div>
<br />

<div class="block">
<label>
{"Comment"|i18n("design/standard/shop")}:
</label><div class="labelbreak"></div>
<textarea name="Comment" cols="80" rows="5">{$comment|wash}</textarea>
</div>


<div class="buttonblock">
    <input class="button" type="submit" name="CancelButton" value="{"Cancel"|i18n('design/standard/shop')}" />
    <input class="defaultbutton" type="submit" name="StoreButton" value="{"Continue"|i18n( 'design/standard/shop')}" />
</div>

</form>

<p>
{"All fields marked with * must be filled in."|i18n("design/standard/shop")}
</p>

