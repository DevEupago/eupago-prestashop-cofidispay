{*
*  2013-2022 Eupago, instituição de pagamento LDA
*
*  @author    Eupago <suporte@eupago.pt>
*  @copyright 2013-2022 Eupago, instituição de pagamento LDA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<form id="payment_form" action="{$action}" onsubmit="return validateNIF(this)">

  <div class="eupago-error-message alert alert-danger" id="message_error" style='display:none;top:0px;width:40%;height:10%;position:fixed;text-align:center' cellspacing='0px'>
      <p>Número de identificação fiscal inválido.<br/> Por favor coloque um número válido e tente novamente</p>
  </div> 
    <div class="eupago-error-message alert alert-info" id="message_wait" style='display:none;top:0px;width:40%;height:10%;position:fixed;text-align:center' cellspacing='0px'>
      <p>O seu pedido encontra-se em processamento.<br/> Por favor aguarde enquanto é redirecionado para o formulário da CofidisPay </p>
  </div> 
  <div class="additional-information" id="eupago_cofidis_element">
      <label for="cofidispay_vat_number">{l s='Número de identificação fiscal'}</label>
      <input type="text" id="cofidispay_vat_number" size="20" autocomplete="off" spellcheck="false" name="cofidispay_vat_number" class="input-text" aria-label="Número de identificação fiscal" aria-placeholder="" aria-invalid="false">
  </div>

</form>

<script>
    /**
    * Validates Portuguese NIF
    */
    function validateNIF(form) {
      var nif = document.getElementById('cofidispay_vat_number').value;

      if(nif.length === 0) {                                                                          // block empty field
        document.getElementById("message_error").style.display = 'block';
        setTimeout(location.reload.bind(location), 2500);
        $('#message_error').delay(2500).hide(300);
        return false;

      } else {

        nif = nif.trim();                                                                              // remove any extra spaces
        var nif_int = parseInt(nif);

        var digits = nif.toString().split('');                                                         // split in digits to check digit
        var nif_split = digits.map(Number);

        const nif_primeiros_digito = ["1", "2", "3", "5", "6", "7", "8", "9"];                         // validate the first digit
        var first_number_check = "not ok";

        for (var i = 0; i < 8; i++) {
          if (nif_split[0].toString().includes(nif_primeiros_digito[i])){
            var first_number_check = "ok";
          }
        };

        if (Number.isInteger(nif_int) && nif_int.toString().length == 9 && first_number_check == "ok") {  // Check if it is numeric and has length 9 and verifies the first digit
            var check_digit = 0;
            for (var i = 0; i < 8; i++) {
                check_digit += nif_split[i] * (10 - i - 1);                                         // Check digit is calculated
            }
            check_digit = 11 - (check_digit % 11);
            check_digit = check_digit >= 10 ? 0 : check_digit;                                       // If $check_digit = 10 then the it must be 0
            if (check_digit == nif_split[8]) {                                                        // The last digit is compared
                form.action="{$action}";
                $('#message_wait').show();
                return true;
            }
        }
        document.getElementById("message_error").style.display = 'block';
        setTimeout(location.reload.bind(location), 2500);
        $('#message_error').delay(2500).hide(300);
        return false;
      }
    };

</script>