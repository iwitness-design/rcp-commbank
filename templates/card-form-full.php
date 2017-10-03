<?php
/**
 * Card Form - Full
 *
 * This template is for displaying credit card form details. It's shown on the registration
 * form when selecting a gateway that supports taking credit/debit card details directly and
 * requires a full billing address.
 *
 * For modifying this template, please see: http://docs.restrictcontentpro.com/article/1738-template-files
 *
 * @package     Restrict Content Pro
 * @subpackage  Templates/Card Form Full
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
?>

<fieldset class="rcp_card_fieldset">
    <p id="rcp_card_number_wrap">
        <label><?php _e( 'Card Number', 'rcp' ); ?></label>
        <input type="text" size="20" maxlength="20" name="rcp_card_number" class="rcp_card_number card-number" />
    </p>
    <p id="rcp_card_cvc_wrap">
        <label><?php _e( 'Card CVC', 'rcp' ); ?></label>
        <input type="text" size="4" maxlength="4" name="rcp_card_cvc" class="rcp_card_cvc card-cvc" />

    <!--
    <tr class="shade">
         <td align="right"><strong>sourceOfFunds.provided.card.number </strong></td>
         <td><input type="text" name="sourceOfFunds[provided][card][number]" value="" size="19" maxlength="80"/></td>
     </tr>

     <tr>
         <td align="right"><strong>sourceOfFunds.provided.card.expiry.month </strong></td>
         <td><input type="text" name="sourceOfFunds[provided][card][expiry][month]" value="" size="1" maxlength="2"/></td>
     </tr>

     <tr class="shade">
         <td align="right"><strong>sourceOfFunds.provided.card.expiry.year </strong></td>
         <td><input type="text" name="sourceOfFunds[provided][card][expiry][year]" value="" size="1" maxlength="2"/></td>
     </tr>

     <tr>
         <td align="right"><strong>sourceOfFunds.provided.card.securityCode </strong></td>
         <td><input type="text" name="sourceOfFunds[provided][card][securityCode]" value="" size="8" maxlength="4"/></td>
     </tr>
    -->
    <p id="rcp_card_exp_wrap">
        <label><?php _e( 'Expiration (MM/YY)', 'rcp' ); ?></label>
        <select name="rcp_card_exp_month" class="rcp_card_exp_month card-expiry-month">
            <?php for( $i = 1; $i <= 12; $i++ ) : ?>
                <option value="<?php echo $i; ?>"><?php echo $i . ' - ' . rcp_get_month_name( $i ); ?></option>
            <?php endfor; ?>
        </select>
        <span class="rcp_expiry_separator"> / </span>
        <select name="rcp_card_exp_year" class="rcp_card_exp_year card-expiry-year">
            <?php
            $year = date( 'y' );
            for( $i = $year; $i <= $year + 10; $i++ ) : ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
    </p>
</fieldset>