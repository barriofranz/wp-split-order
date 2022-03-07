<div class="gf_splitorder">
    <div class="gf-splitoder-modals">
    	<div class="container" id="a">

    	</div>


    	<div class="modal" id="b">
    		<div class="content">
                <div class="woocommerce">


                    <h4>Main order</h4>
                    <div class="gf-splitorder-mainorder">
                    <table class="shop_table ">
                    	<thead>
                    		<tr>
                    			<th class="product-name">Product</th>
                    			<th class="product-total">Subtotal</th>
                    		</tr>
                    	</thead>
                    	<tbody>
                            <?php

                            foreach ($instockItems as $item){
                                ?>

                                <tr class="cart_item">
                                    <td class="product-name">
                                        <?= $item['data']->get_name() ?> <strong class="product-quantity">×&nbsp;<?= $item['quantity']; ?></strong>
                                    </td>
                                    <td class="product-total">
                                        <span class="woocommerce-Price-amount amount">
                                            <?= wc_price($item['line_subtotal']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            </tbody>
                            <tfoot>

                    		<tr class="cart-subtotal">
                    			<th>Subtotal</th>
                    			<td>
                                    <span class="woocommerce-Price-amount amount">
                                        <?= wc_price($instockItemsSubtotal) ?>
                                    </span>
                                </td>
                    		</tr>

                            <tr class="woocommerce-shipping-totals shipping">
                                <th>Shipping</th>
                                <td data-title="In Stock">
                                    <span class="woocommerce-Price-amount amount">
                                        <?= wc_price($cart_shipping_total - $backorderShipping) ?>
                                    </span>
                                </td>
                            </tr>

                    		<tr class="order-total">
                    			<th>Total</th>
                    			<td>
                                    <strong>
                                        <span class="woocommerce-Price-amount amount">
                                            <?= wc_price( ( $instockItemsSubtotal  + $cart_shipping_total - $backorderShipping ) ) ?>
                                        </span>
                                    </strong>
                                </td>
                    		</tr>



                    	</tfoot>
                    </table>
                    </div>



                    <h4>Pre-Order</h4>
                    <div class="gf-splitorder-backorder">
                    <table class="shop_table ">
                    	<thead>
                    		<tr>
                    			<th class="product-name">Product</th>
                    			<th class="product-total">Subtotal</th>
                    		</tr>
                    	</thead>
                    	<tbody>
                            <?php

                            foreach ($backorderItems as $item){
                                ?>

                                <tr class="cart_item">
                                    <td class="product-name">
                                        <?= $item['data']->get_name() ?> <strong class="product-quantity">×&nbsp;<?= $item['quantity']; ?></strong>
                                    </td>
                                    <td class="product-total">
                                        <span class="woocommerce-Price-amount amount">
                                            <?= wc_price($item['line_subtotal']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            </tbody>
                            <tfoot>

                    		<tr class="cart-subtotal">
                    			<th>Subtotal</th>
                    			<td>
                                    <span class="woocommerce-Price-amount amount">
                                        <?= wc_price($backorderItemsSubtotal) ?>
                                    </span>
                                </td>
                    		</tr>



                            <tr class="woocommerce-shipping-totals shipping">
                                <th>Shipping</th>
                                <td data-title="In Stock">
                                    <span class="woocommerce-Price-amount amount">
                                        <?= wc_price($backorderShipping) ?>
                                    </span>
                                </td>
                            </tr>


                    		<tr class="order-total">
                    			<th>Total</th>
                    			<td>
                                    <strong>
                                        <span class="woocommerce-Price-amount amount">
                                            <?= wc_price( ( $backorderItemsSubtotal + $backorderShipping ) ) ?>
                                        </span>
                                    </strong>
                                </td>
                    		</tr>



                    	</tfoot>
                    </table>
                    </div>



                </div>
    		</div>
    		<div class="footer">
        		<div class="btn-group">
        			<a class="gf_splitorder-close-btn gf_splitorder-btn cancel" >Close</a>
        		</div>
    		</div>
    	</div>
    </div>
</div>
