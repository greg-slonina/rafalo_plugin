<?php
// to check whether accessed directly
if (!defined('ABSPATH')) {
	exit;
}

global $wp_roles;

$user_roles = get_option('eh_pricing_discount_product_price_user_role');

echo '<div id="general_role_based_price" class="wcfm_ele simple" style="padding: 3%;">';
if (is_array($user_roles) && !empty($user_roles)) {
	  echo '<h2 style="text-align: center;">' . __('Role Based Price', 'elex-catmode-rolebased-price') . '</h2><div class="wcfm-clearfix"></div>';
    ?>
    <table class="product_role_based_price widefat" id="eh_pricing_discount_product_price_adjustment_data">
        <thead>
        <th class="sort">&nbsp;</th>
        <th><?php _e('User Role', 'elex-catmode-rolebased-price'); ?></th>
        <th><?php echo sprintf(__('Price (%s)', 'elex-catmode-rolebased-price'), get_woocommerce_currency_symbol()); ?></th>
    </thead>
    <tbody>

        <?php
        $this->price_table = array();
        $i = 0;
        $product_adjustment_price;
        $product_adjustment_price = get_post_meta($product_id, 'product_role_based_price', false);
        foreach ($user_roles as $id => $value) {
            $this->price_table[$i]['id'] = $value;
            $this->price_table[$i]['name'] = $wp_roles->role_names[$value];
            if ((is_array($product_adjustment_price) && !empty($product_adjustment_price)) && is_array($product_adjustment_price[0]) && key_exists($value, $product_adjustment_price[0])) {
							$this->price_table[$i]['role_price'] = $product_adjustment_price[0][$value]['role_price'];
            }
            $i++;
        }
        foreach ($this->price_table as $key => $value) {
            ?>
            <tr>
                <td class="sort">
                    <input type="hidden" class="order" name="product_role_based_price[<?php echo $this->price_table[$key]['id'] ?>]" value="<?php echo $this->price_table[$key]['id']; ?>" />
                </td>
                <td>
                    <label name="product_role_based_price[<?php echo $this->price_table[$key]['id']; ?>][name]" style="margin-left:0px;"><?php echo isset($this->price_table[$key]['name']) ? $this->price_table[$key]['name'] : ''; ?></label>
                </td>
                <td>
                    <?php echo get_woocommerce_currency_symbol(); ?><input type="text" class="wcfm-text" name="product_role_based_price[<?php echo $this->price_table[$key]['id']; ?>][role_price]" id="product_role_based_price_<?php echo $this->price_table[$key]['id']; ?>" placeholder="N/A" value="<?php echo isset($this->price_table[$key]['role_price']) ? $this->price_table[$key]['role_price'] : ''; ?>" size="4" />
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    </table>
<?php }
else {
	  echo '<h2 style="text-align: center;">';
    _e( 'Role Based Price ', 'elex-catmode-rolebased-price' ); 
    echo '</h2><div class="wcfm-clearfix"></div>';
    ?>
<table class="product_role_based_price widefat" id="eh_pricing_discount_product_price_adjustment_data">
<th><?php _e( 'For setting up user roles eligible for individual price adjustment, go to Role Based Pricing settings -> Add roles for the field "Individual Price Adjustment".', 'elex-catmode-rolebased-price' ); ?></th>
</table>
<?php
}
echo '</div>';
?>