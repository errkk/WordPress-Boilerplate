<?php

class Form
{
    public $submitted = false;
    public $errors = array();
    
    private $wordlimit = 500;
    private $form_name;
    private $action = '';
    private $method = 'post';
    
    
    
    /**
     * Setup form
     * @param string $form_name
     * @param string $action
     * @param string $method Default Post 
     */
    public function __construct( $form_name, $action, $method = 'post' )
    {
	$this->form_name = $form_name;
	
	$this->action = $action;
	$this->method = $method;
	
	
	// Check submitted
	if( @$_POST[ '_submit' ] === $this->form_name ){
	    $this->submitted = true;
	}
    }
    
    /**
     * Start the form, with the form element, and the action and method
     * @param string $class (optional)
     */
    public function start_form( $class = '' )
    {
	echo '<form class="' . $class . '" action="' . $this->action . '" method="' . $this->method . '">' . "\r\n";
    }
    
    
    public function no_errors()
    {
	
	if( count( $this->errors ) === 0 ){
	    return true;
	}else{
	    return false;
	}
    }
    
    
    

    /**
     * Return the error message associated with a field if it has any
     * @param string $name
     * @return string 
     */
    public function get_field_errors( $name )
    {
	if( array_key_exists( $name, $this->errors ) ){
	    return $this->errors[$name];
	}else{
	    return false;
	}
    }
    
    
    /**
     * Get a clean echoable value from what has been posted
     * @param string $name
     * @return string/bool 
     */
    public function get_field( $name )
    {
	if( array_key_exists( $name, $_POST ) ){
	    $val = $_POST[$name];
	    if( is_array( $val ) ){
		
	    }else{
		$val = trim( $val );
		$val = esc_attr( $val );
	    }
	    return $val;
	}elseif( array_key_exists( $name, $_GET ) ){
	    $val = $_GET[$name];
	    if( is_array( $val ) ){
		
	    }else{
		$val = trim( $val );
		$val = esc_attr( $val );
	    }
	    return $val;
	}else{
	    return false;
	}
    }
    
    /**
     * Add an error to a field
     * @param string $name
     * @param string $message 
     */
    public function add_error( $name, $message )
    {
	$this->errors[$name] = $message;
    }
    
    /**
     * Check if the form has any errors
     * @return bool
     */
    public function has_errors()
    {
	if( count($this->errors) ){
	    return true;
	}else{
	    return false;
	}
    }
    
    
    
    /**
     * Render a text field
     * @param string $name
     * @param string $label
     * @param bool $mandatory 
     */
    public function input_text( $name, $label, $mandatory = false )
    {
	$error = $this->get_field_errors( $name );
	$class = 'form-row-text';
	if( $error ){
	    $class .= ' error';
	    $error_message = '<div class="message"><p>' . $error . '</p></div>';
	}else{
	    $error_message = '';
	}
	
	?>
	<div class="<?php echo $class;?>">
	    <label for="<?php echo $name;?>"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
	    <input type="text" class="text" id="<?php echo $name;?>" name="<?php echo $name;?>" value="<?php echo $this->get_field( $name );?>"/>
	    <?php echo $error_message;?>
	</div>
	<?php
    }
    
    /**
     * Render a Radio button input
     * Uses value as the ID so its unique, and name is the same for all the options
     * @param string $name
     * @param string $label
     * @param string $value
     * @param bool $mandatory 
     */
    public function radio( $name, $label, $value, $mandatory = false )
    {
	$class = 'form-row-radio';
	$checked = ( $value == $this->get_field( $name ) ) ? ' checked="checked" ' : '';
	?>
	<div class="<?php echo $class;?>">
	    <input type="radio" class="radiobutton" id="<?php echo $name.$value;?>-1" name="<?php echo $name;?>" value="<?php echo $value;?>" <?php echo $checked;?> />
	    <label for="<?php echo $name.$value;?>-1"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
	</div>
	<?php
    }
    
    /**
     * Render a textarea
     * @param string $name
     * @param string $label
     * @param bool $mandatory 
     */
    public function textarea( $name, $label, $mandatory = false, $multiple = false )
    {
	$error = $this->get_field_errors( $name );
	$class = 'form-row-text';
	if( $error ){
	    $class .= ' error';
	    $error_message = '<div class="message"><p>' . $error . '</p></div>';
	}else{
	    $error_message = '';
	}
	
	if( $multiple ){
	    $arraysign = '[]';
	}else{
	    $arraysign = '';
	}
	
	?>
	<div class="<?php echo $class;?>">
	    <label for="<?php echo $name;?>"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
	    <textarea id="<?php echo $name;?>" name="<?php echo $name . $arraysign;?>"><?php echo $this->get_field( $name );?></textarea>
	    <?php echo $error_message;?>
	</div>
	<?php
    }
    
    
    /**
     * Render a textarea
     * @param string $name
     * @param string $label
     * @param bool $mandatory 
     */
    public function cb( $name, $label, $mandatory = false )
    {
	$error = $this->get_field_errors( $name );
	$class = 'form-row-checkbox';
	if( $error ){
	    $class .= ' error';
	    $error_message = '<div class="message"><p>' . $error . '</p></div>';
	}else{
	    $error_message = '';
	}
	$checked = ( $this->get_field( $name ) == 'on' ) ? ' checked="checked" ' : '';
	?>
	<div class="<?php echo $class;?>">
	    <input <?php echo $checked;?> type="checkbox" type="class" id="<?php echo $name;?>" name="<?php echo $name;?>" />
	    <label for="<?php echo $name;?>"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
	    <?php echo $error_message;?>
	</div>
	<?php
    }
    
    /**
     * Render a Selectbox
     * @param string $name
     * @param string $label
     * @param array $data
     * @param bool $mandatory 
     */
    public function select( $name, $label, $data, $mandatory = false )
    {
	$error = $this->get_field_errors( $name );
	$class = 'form-row-select';
	if( $error ){
	    $class .= ' error';
	    $error_message = '<div class="message"><p>' . $error . '</p></div>';
	}else{
	    $error_message = '';
	}
	
//	array_unshift( $data, 'Please Select' );
	$data = ( array( 'ps' => 'Please Select' ) + $data );
	
	?>
	<div class="<?php echo $class;?>">
	    <label for="<?php echo $name;?>"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
	    <select id="<?php echo $name;?>" name="<?php echo $name;?>">
		<?php 
		foreach( $data as $key => $item ){
		    $selected = '';
		    
		    if( $this->get_field($name) && $this->get_field($name) == $key ){
			$selected = 'selected="selected"';
		    }
		    echo '<option ' . $selected . ' value="' . $key . '">' . $item .'</option>';
		}
		?>
	    </select>
	    <?php echo $error_message;?>
	</div>
	<?php
    }
    
    /**
     * Render a Hidden input
     * @param string $name
     * @param string $value
     */
    public function hidden( $name, $value = false )
    {
	$value = ( $value ) ? $value : $this->get_field( $name );
	echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    }
    
    
    public function submit_fields()
    {
	echo '<input type="hidden" name="_submit" value="' . $this->form_name . '" />';
	
    }
    
    /**
     * Render a Selectbox
     * @param string $name
     * @param string $label
     * @param array $data
     * @param bool $mandatory 
     */
    public function grouped_select( $name, $label, $data, $mandatory = false )
    {
	$error = $this->get_field_errors( $name );
	$class = 'form-row-select';
	if( $error ){
	    $class .= ' error';
	    $error_message = '<div class="message"><p>' . $error . '</p></div>';
	}else{
	    $error_message = '';
	}
	$data = ( array( 0 => 'Please Select' ) + $data );
	
	
	?>
	<div class="<?php echo $class;?>">
	    <label for="<?php echo $name;?>"><?php echo $label . (( $mandatory ) ? '*' : '');?></label>
		<?php 
		echo '<select id="' . $name . '" name="' . $name .'">';
		
		foreach( $data as $key => $item ){
		    if( is_array( $item ) ){
			// grouped items
			echo '<optgroup data-region-id="' . $key . '" label="' . $item['parent'] . '">';
			foreach( $item['subs'] as $sub ){
			    if( $this->get_field($name) && $this->get_field($name) == $sub[0] ){
				$selected = 'selected="selected"';
			    }
			    echo '<option ' . $selected . ' value="' . $sub[0] . '">' . $sub[1] .'</option>';
			    $selected = '';
			}

			echo '</optgroup>';
		    
		
		    }else{
			// ungrouped items
			$selected = '';

			if( $this->get_field($name) && $this->get_field($name) == $key ){
			    $selected = 'selected="selected"';
			}

			echo '<option ' . $selected . ' value="' . $key . '">' . $item .'</option>';
		    }
		    
		}
		
		echo '</select>';
		
		echo $error_message;
		
		?>
	</div>
	<?php
    }
    
    
    /**
     * Check if the string has more words in it that is allowed
     * @param string $string
     * @return bool 
     */
    public function check_word_limit( $string )
    {
	if( $this->wordlimit && $this->count_words( $string ) > $this->wordlimit ){
	    return true;
	}else{
	    return false;
	}
    }
    
    /**
     * Count the words
     * @param type $string
     * @return int 
     */
    private function count_words( $string )
    {
	return str_word_count( $string, 0 );
    }
    
    public function redirect( $location )
    {
	wp_safe_redirect( $location );
    }
    
    
    
    /**
     * Called by $this->validate(), run validation method against a form field
     * @param string $field
     * @param string $rule named validation method
     * @return bool 
     */
    private function validate_rule( $field, $rule )
    {
	$method = 'validate_' . $rule;
	if( method_exists( 'Form', $method ) ){
	    return $this->$method( $field );
	}else{
	    if( 'dev' === ENVIRONMENT ){
		$this->add_error($field, $method . ' not defined' ); 
	    }
	}
	return true;
    }
    
    /**
     * Check a field against a single rule or array of rules
     * @param string $field
     * @param string||array $rule 
     */
    public function validate( $field, $rule )
    {
	
	if( is_array( $rule ) ){
	    foreach( $rule as $single_rule ){
		$this->validate_rule( $field, $single_rule );
	    }
	}else{
	    $this->validate_rule( $field, $rule );
	}
	
	
    }
    
    
    
    // Validation rules
    /**
     * Check a field is long enough
     * @param string $field
     * @return bool 
     */
    private function validate_minlen( $field )
    {
	$val = $this->get_field( $field );
	if( strlen( $val ) < 6 ){
	    $this->add_error( $field, 'Too short' );
	    return false;
	}
	return true;
    }
    /**
     * Check a field filled in
     * @param string $field
     * @return bool 
     */    
    private function validate_mandatory( $field )
    {
	$val = $this->get_field( $field );
	if( !$val ){
	    $this->add_error( $field, 'Please complete this field' );
	    return false;
	}
	return true;
    }
    
    /**
     * Check for valid email address
     * @param string $field
     * @return bool 
     */
    private function validate_email( $field )
    {
	$val = $this->get_field( $field );
	if( strlen( $val ) && !filter_var( $val, FILTER_VALIDATE_EMAIL ) ){
	    $this->add_error( $field, 'Please enter a valid email address' );
	    return false;
	}
	return true;
    }
    
    /**
     * Check for occurances of words in the 'gl_naughty_words' wp_option 
     * @param string $field
     * @return bool 
     */
    private function validate_profanity( $field )
    {
	$val = $this->get_field( $field );
	$mod_keys = trim( get_option( 'gl_naughty_words' ) );
	if ( !empty( $mod_keys ) ) {
	    $words = explode( "\n", $mod_keys );

	    foreach ( (array) $words as $word ) {
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) )
		    continue;

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word = preg_quote( $word, '#' );

		$pattern = "#$word#i";
		if ( preg_match( $pattern, $val ) ) {
		    $this->add_error( $field, 'Don\'t say ' . $word . '!' );
		    return false;
		}
	    }
	}
	return true;
    }
    
    /**
     * Call validate postcode function inside the validation name structure
     * @param string $field
     * @return bool 
     */
    private function validate_postcode( $field )
    {
	$val = $this->get_field( $field );
	if( !checkPostcode( $val ) ){
	    $this->add_error( $field, 'Please enter a valid postcode' );
	    return false;
	}
	return true;
    }
    
}







/**
* Check postcode is valid
*
* @param postcode String
*   Pass a postcode string, will be checked against all postcode RegEx
* @return Bool
*   True if postcode is correct, false if not.
*/
function checkPostcode($toCheck, $returnValidPostcode=false)
{
    // Permitted letters depend upon their position in the postcode.
    $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
    $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
    $alpha3 = "[abcdefghjkstuw]";                                   // Character 3
    $alpha4 = "[abehmnprvwxy]";                                     // Character 4
    $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5

    // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA
    $pcexp[0] = '^('.$alpha1.'{1}'.$alpha2.'{0,1}[0-9]{1,2})([0-9]{1}'.$alpha5.'{2})$';

    // Expression for postcodes: ANA NAA
    $pcexp[1] =  '^('.$alpha1.'{1}[0-9]{1}'.$alpha3.'{1})([0-9]{1}'.$alpha5.'{2})$';

    // Expression for postcodes: AANA NAA
    $pcexp[2] =  '^('.$alpha1.'{1}'.$alpha2.'[0-9]{1}'.$alpha4.')([0-9]{1}'.$alpha5.'{2})$';

    // Exception for the special postcode GIR 0AA
    $pcexp[3] =  '^(gir)(0aa)$';

    // Standard BFPO numbers
    $pcexp[4] = '^(bfpo)([0-9]{1,4})$';

    // c/o BFPO numbers
    $pcexp[5] = '^(bfpo)(c\/o[0-9]{1,3})$';

    // Load up the string to check, converting into lowercase and removing spaces
    $postcode = strtolower($toCheck);
    $postcode = str_replace (' ', '', $postcode);

    // Assume we are not going to find a valid postcode
    $valid = false;

    // Check the string against the six types of postcodes
    foreach ($pcexp as $regexp) {

	if (ereg($regexp,$postcode, $matches)) {

	    // Load new postcode back into the form element
	    $toCheck = strtoupper ($matches[1] . ' ' . $matches [2]);

	    // Take account of the special BFPO c/o format
	    $toCheck = ereg_replace ('C\/O', 'c/o ', $toCheck);

	    // Remember that we have found that the code is valid and break from loop
	    $valid = true;
	    break;
	}
    }

    // Return with the reformatted valid postcode in uppercase if the postcode was valid
    if(!$returnValidPostcode)
	return $valid;
    else
	return $toCheck;
}


?>
