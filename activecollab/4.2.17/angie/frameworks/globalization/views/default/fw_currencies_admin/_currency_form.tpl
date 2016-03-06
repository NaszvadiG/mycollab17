{wrap field=name}
  {text_field name='currency[name]' value=$currency_data.name label="Name" required=true}
{/wrap}

{wrap field=code}
  {text_field name='currency[code]' value=$currency_data.code label="Code" required=true class="currency_code" maxlength=3}
{/wrap}

{wrap field=decimal_spaces}
  {select_number_of_decimal_spaces name='currency[decimal_spaces]' value=$currency_data.decimal_spaces label="Number of Decimal Spaces"}
{/wrap}

{wrap field=decimal_rounding}
  {select_decimal_rounding name='currency[decimal_rounding]' value=$currency_data.decimal_rounding label="Decimal Rounding"}
{/wrap}

{wrap field=code_position}
  {select_currency_code_position name='currency[code_position]' id="code_position" value=$currency_data.code_position label="Currency Code Position"}
  <p class="details">{lang}Preview: {/lang}<span class="code_position_preview"></span></p>
{/wrap}
<script type="text/javascript">
  var code_position = $('select#code_position');
  var code_input = $('input.currency_code');

  code_position.change(function(){
    preview_code_position();
  });
  code_input.keyup(function() {
    preview_code_position();
  });
  var preview_code_position = function() {
    var preview_container = $('span.code_position_preview');
    var code = code_input.val();
    if(code == '') {
      code = 'CODE';
    } //if
    var value = code_position.val();
    if(value == 'left') {
      preview_container.html(code + ' 14.50');
    } else {
      preview_container.html('14.50 ' + code);
    } //if
  } //preview_code_position
  preview_code_position();
</script>