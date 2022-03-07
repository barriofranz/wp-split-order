<!-- <label class="gf_splitorder-form__label gf_splitorder-form__label-for-checkbox checkbox">
<input type="checkbox" class="gf_splitorder-form__input gf_splitorder-form__input-checkbox input-checkbox" name="splitorder" id="splitorder">
	<span class="gf_splitorder-terms-and-conditions-checkbox-text">Split backorder?</span>
    <input type="hidden" name="splitorder-field" value="1">
</label> -->


<tr class="gf_splitorder">
	<th>Split Order?
		<div class="gf-tooltip">
			<span class="question-icon">?</span>
			<span class="gf-tooltiptext gf-tooltiptext-1">Splitting your order will allow us to deliver your in stock items now, and your pre-order products when they become available. If you choose not to split your order, we will deliver all of your items when your last item becomes available.</span>
		</div>
	</th>
	<td>
		<input type="checkbox" class="gf_splitorder-form__input gf_splitorder-form__input-checkbox input-checkbox" name="splitorder" id="splitorder" <?php echo ($gfSessionVal == 1) ? 'checked' : '' ?>>
	    <input type="hidden" name="splitorder-field" value="1">
		<button class="gf_splitorder-preview-btn gf_splitorder-btn" <?php echo ($gfSessionVal == 1) ? '' : 'disabled' ?>>View Split Order</button>
	</td>
</tr>
